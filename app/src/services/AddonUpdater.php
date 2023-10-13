<?php

use Composer\Package\Version\VersionParser;
use Packagist\Api\Result\Package;
use Packagist\Api\Result\Package\Version;
use Symbiote\QueuedJobs\Services\QueuedJobService;
use SilverStripe\ORM\DataList;
use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;

/**
 * Updates all add-ons from Packagist.
 * Creates partially populated add-ons for performance reasons.
 * Relies on {@link AddonBuilder} to finish the job.
 */
class AddonUpdater
{

    /**
     * @var PackagistService
     */
    private $packagist;

    /**
     * @var SilverStripeVersion[]
     */
    private $silverstripes;

    public function __construct(PackagistService $packagist)
    {
        $this->packagist = $packagist;
        $this->setSilverStripeVersions(SilverStripeVersion::get());
    }

    /**
     * Updates all add-ons.
     *
     * @param boolean Clear existing addons before updating them.
     * Will also clear their search index, and cascade the delete for associated data.
     * @param array Limit to specific addons, using their name incl. vendor prefix.
     */
    public function update($clear = false, $limitAddons = null)
    {
        if ($clear && !$limitAddons) {
            Addon::get()->removeAll();
            AddonAuthor::get()->removeAll();
            AddonKeyword::get()->removeAll();
            AddonLink::get()->removeAll();
            AddonVendor::get()->removeAll();
            AddonVersion::get()->removeAll();
        }

        // Uses a generator to save memory
        $packages = $this->packagist->getPackages($limitAddons ? $limitAddons : []);

        foreach ($packages as $package) {
            $versions = $package->getVersions();
            usort($versions, function ($a, $b) {
                return version_compare($a->getVersionNormalized(), $b->getVersionNormalized());
            });

            // Get name from the highest version,
            // since the (performant) composer metadata retrieval
            // doesn't contain a toplevel "name" key
            $highestVersion = $versions[count($versions)-1];
            $name = $highestVersion->getName();

            // Unclear when this happens, but we've observed a few NULL records per month
            if (!$name) {
                continue;
            }
            if ($limitAddons && !in_array($name, $limitAddons)) {
                continue;
            }

            $addon = Addon::get()->filter('Name', $name)->first();

            if ($addon && $highestVersion->isAbandoned()) {
                echo sprintf("Deleting abandoned addon: %s", $name) . PHP_EOL;
                $addon->delete();
                continue;
            }

            if (!$addon && $highestVersion->isAbandoned()) {
                echo sprintf("Skipping abandoned addon: %s", $name) . PHP_EOL;
                continue;
            }

            if (!$addon) {
                echo sprintf("Creating addon: %s", $name) . PHP_EOL;

                $addon = new Addon();
                $addon->Name = $name;
                $addon->write();
            }

            $this->updateAddon($addon, $package, $versions);

            // Enforce garbage collection
            unset($addon);
            unset($package);
            unset($versions);
        }
    }

    private function updateAddon(Addon $addon, Package $package, array $versions)
    {
        echo "Updating addon {$addon->Name}:\n";

        if (!$addon->VendorID) {
            $vendor = AddonVendor::get()->filter('Name', $addon->VendorName())->first();

            if (!$vendor) {
                $vendor = new AddonVendor();
                $vendor->Name = $addon->VendorName();
                $vendor->write();
            }

            echo " - Set vendor name to {$vendor->Name}\n";

            $addon->VendorID = $vendor->ID;
        }

        foreach ($versions as $version) {
            $this->updateVersion($addon, $version);
        }

        if (!$addon->Versions()->exists()) {
            $addon->delete();
            echo " - Addon has no relevant versions, deleting it\n";
            return;
        }

        $built = (int) $addon->obj('LastBuilt')->getTimestamp();
        $hasBuild = (bool)$addon->LastBuilt;
        $hasBuildQueued = (bool)$addon->BuildQueued;
        $hasNewerVersions = (bool)array_filter($versions, function ($version) use ($built) {
            return strtotime($version->getTime()) > $built;
        });

        // TODO Technically we only need to build if the DefaultVersion has changed
        if ((!$hasBuildQueued && !$hasBuild) || (!$hasBuildQueued && $hasNewerVersions)) {
            $buildJob = new BuildAddonJob(['package' => $addon->ID]);
            singleton(QueuedJobService::class)->queueJob($buildJob);
            $addon->BuildQueued = true;
            echo " - Queued {$addon->Name} for build\n";
        } else {
            echo " - Will not queue a rebuild\n";
        }

        $addon->LastUpdated = time();
        $addon->write();
    }

    private function updateVersion(Addon $addon, Version $package)
    {
        $version = null;

        if ($addon->isInDB()) {
            $version = $addon->Versions()->filter('Version', $package->getVersionNormalized())->first();
        }

        if (!$version) {
            $version = new AddonVersion();
        }

        $version->Name = $package->getName();
        $version->Type = preg_replace('/^silverstripe-(vendor)?/', '', $package->getType());
        $version->Description = $package->getDescription();
        $version->Released = strtotime($package->getTime());
        $keywords = $package->getKeywords();

        if ($keywords) {
            foreach ($keywords as $keyword) {
                $keyword = AddonKeyword::get_by_name($keyword);

                $addon->Keywords()->add($keyword);
                $version->Keywords()->add($keyword);
            }
        }

        $version->Version = $package->getVersionNormalized();
        $version->PrettyVersion = $package->getVersion();

        $stability = VersionParser::parseStability($package->getVersion());
        $isDev = $stability === 'dev';
        $version->Development = $isDev;

        $version->SourceType = $package->getSource()->getType();
        $version->SourceUrl = $package->getSource()->getUrl();
        $version->SourceReference = $package->getSource()->getReference();

        if ($package->getDist()) {
            $version->DistType = $package->getDist()->getType();
            $version->DistUrl = $package->getDist()->getUrl();
            $version->DistReference = $package->getDist()->getReference();
            $version->DistChecksum = $package->getDist()->getShasum();
        }

        $version->Extra = $package->getExtra();
        $version->Homepage = $package->getHomepage();
        $licenses = $package->getLicenses();
        $version->License = $licenses ? $licenses[0] : null;
        // $version->Support = $package->getSupport();

        echo " - Processed version {$version->Version}\n";

        $addon->Versions()->add($version);

        $this->updateCompatibility($addon, $version, $package);

        if (!$version->CompatibleVersions()->exists()) {
            $version->delete();
            echo " - Version has no compatibility, deleting it\n";
            return;
        }

        $this->updateLinks($version, $package);
        $this->updateAuthors($version, $package);
    }

    private function updateLinks(AddonVersion $version, Version $package)
    {
        $getLink = function ($name, $type) use ($version) {
            $link = null;

            if ($version->isInDB()) {
                $link = $version->Links()->filter('Name', $name)->filter('Type', $type)->first();
            }

            if (!$link) {
                $link = new AddonLink();
                $link->Name = $name;
                $link->Type = $type;
            }

            return $link;
        };

        $types = array(
            'require' => 'getRequire',
            'require-dev' => 'getRequireDev',
            'provide' => 'getProvide',
            'conflict' => 'getConflict',
            'replace' => 'getReplace'
        );

        foreach ($types as $type => $method) {
            if ($linked = $package->$method()) {
                foreach ($linked as $link => $constraint) {
                    $name = $link;
                    $addon = Addon::get()->filter('Name', $name)->first();

                    $local = $getLink($name, $type);
                    $local->Constraint = $constraint;

                    if ($addon) {
                        $local->TargetID = $addon->ID;
                    }

                    $version->Links()->add($local);
                }
            }
        }
    }

    private function updateCompatibility(Addon $addon, AddonVersion $version, Version $package)
    {
        $require = null;

        if ($package->getRequire()) {
            foreach ($package->getRequire() as $name => $link) {
                if ((string)$link == 'self.version') {
                    continue;
                }

                $versionLocked = [
                    'silverstripe/recipe-core',
                    'silverstripe/recipe-cms',
                    'silverstripe/framework',
                    'silverstripe/cms',
                ];

                if (in_array($name, $versionLocked)) {
                    $require = $link;
                    // If they're depending on silverstripe/cms,
                    // let one of the others override the constraint if present.
                    if ($name !== 'silverstripe/cms') {
                        break;
                    }
                }
            }
        }

        if (!$require) {
            return;
        }

        //  >= interpreted as ^, see https://github.com/silverstripe/addons.silverstripe.org/issues/160
        $require = preg_replace('/^>=/', '^', $require);

        $addon->CompatibleVersions()->removeAll();
        $version->CompatibleVersions()->removeAll();

        foreach ($this->getSilverStripeVersions() as $silverStripeVersion) {
            /** @var SilverStripeVersion $silverStripeVersion */
            try {
                if ($silverStripeVersion->getConstraintValidity($require)) {
                    $addon->CompatibleVersions()->add($silverStripeVersion);
                    $version->CompatibleVersions()->add($silverStripeVersion);
                }
            } catch (Exception $e) {
                // An exception here shouldn't prevent further updates.
                Injector::inst()->get(LoggerInterface::class)
                    ->warning($addon->Name . "\t" . $addon->ID . "\t" . $e->getMessage());
            }
        }
    }

    private function updateAuthors(AddonVersion $version, Version $package)
    {
        if ($package->getAuthors()) {
            foreach ($package->getAuthors() as $details) {
                $author = null;

                if (!$details->getName() && !$details->getEmail()) {
                    continue;
                }

                if ($details->getEmail()) {
                    $author = AddonAuthor::get()->filter('Email', $details->getEmail())->first();
                }

                if (!$author && $details->getHomepage()) {
                    $author = AddonAuthor::get()
                    ->filter('Name', $details->getName())
                    ->filter('Homepage', $details->getHomepage())
                    ->first();
                }

                if (!$author && $details->getName()) {
                    $author = AddonAuthor::get()
                    ->filter('Name', $details->getName())
                    ->filter('Versions.Addon.Name', $package->getName())
                    ->first();
                }

                if (!$author) {
                    $author = new AddonAuthor();
                }

                if ($details->getName()) {
                    $author->Name = $details->getName();
                }
                if ($details->getEmail()) {
                    $author->Email = $details->getEmail();
                }
                if ($details->getHomepage()) {
                    $author->Homepage = $details->getHomepage();
                }

                        //to-do not supported by API
                        //if(isset($details['role'])) $author->Role = $details['role'];

                $version->Authors()->add($author->write());
            }
        }
    }

    /**
     * Get the list of SilverStripe versions
     *
     * @return DataList
     */
    public function getSilverStripeVersions()
    {
        return $this->silverstripes;
    }

    /**
     * Set the list of SilverStripeVersions
     *
     * @param  DataList $versions
     * @return $this
     */
    public function setSilverStripeVersions(DataList $versions)
    {
        $this->silverstripes = $versions;
        return $this;
    }
}
