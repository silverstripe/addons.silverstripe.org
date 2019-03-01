<?php

use Composer\Package\Version\VersionParser;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Heyday\Elastica\ElasticaService;
use Packagist\Api\Result\Package;
use Packagist\Api\Result\Package\Version;
use SilverStripe\Control\Director;
use Symbiote\QueuedJobs\Services\QueuedJobService;
use SilverStripe\Dev\Debug;
use SilverStripe\Control\Email\Email;
use SilverStripe\ORM\DataList;
use Psr\SimpleCache\CacheInterface;
use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;

/**
 * Updates all add-ons from Packagist.
 */
class AddonUpdater
{

    /**
     * @var PackagistService
     */
    private $packagist;

    /**
     * @var Heyday\Elastica\ElasticaService
     */
    private $elastica;

    /**
     * @var SilverStripeVersion[]
     */
    private $silverstripes;

    public function __construct(
        PackagistService $packagist,
        ElasticaService $elastica
    ) {
        $this->packagist = $packagist;
        $this->elastica = $elastica;

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

        // This call to packagist can be expensive. Requests are served from a cache if usePackagistCache() returns true
        /** @var CacheInterface $cache */
        $cache = Injector::inst()->get(CacheInterface::class . '.addons');

        if ($this->usePackagistCache() && $packages = $cache->get('AddonUpdater-packagist')) {
            $packages = unserialize($packages);
        } else {
            $packages = $this->packagist->getPackages($limitAddons);
            $cache->set('AddonUpdater-packagist', serialize($packages));
        }

        // TODO: AWS elasticsearch doesn't have this setting enabled
        // https://www.elastic.co/guide/en/elasticsearch/reference/5.2/url-access-control.html
        // and bulk index operations by elastica currently require it
        // Switching to https://github.com/heyday/silverstripe-elastica and SS4 might help

        // $this->elastica->startBulkIndex();

        foreach ($packages as $package) {
            /** @var Packagist\Api\Result\Package $package */
            $name = $package->getName();
            $versions = $package->getVersions();

            if ($limitAddons && !in_array($name, $limitAddons)) {
                continue;
            }

            $addon = Addon::get()->filter('Name', $name)->first();

            if (!$addon) {
                $addon = new Addon();
                $addon->Name = $name;
                $addon->write();
            }

            usort($versions, function ($a, $b) {
                return version_compare($a->getVersionNormalized(), $b->getVersionNormalized());
            });

            $this->updateAddon($addon, $package, $versions);
        }

        // $this->elastica->endBulkIndex();
    }



    /**
     * Check whether or not we should contact packagist or use a cached version. This allows to speed up the task
     * during development.
     *
     * @return bool
     */
    protected function usePackagistCache()
    {
        return Director::isDev();
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

        $addon->Type = preg_replace('/^silverstripe-(vendor)?/', '', $package->getType());
        $addon->Abandoned = $package->isAbandoned();
        $addon->Description = $package->getDescription();
        $addon->Released = strtotime($package->getTime());
        $addon->Repository = $package->getRepository();
        if ($downloads = $package->getDownloads()) {
            $addon->Downloads = $downloads->getTotal();
            $addon->DownloadsMonthly = $downloads->getMonthly();
        }
        $addon->Favers = $package->getFavers();

        foreach ($versions as $version) {
            $this->updateVersion($addon, $version);
        }

        // If there is no build, then queue one up if the add-on requires
        // one.
        if (!$addon->BuildQueued) {
            echo " - Will queue a rebuild\n";
            if (!$addon->BuiltAt) {
                $buildJob = new BuildAddonJob(['package' => $addon->ID]);
                singleton(QueuedJobService::class)->queueJob($buildJob);
                echo " - Queued {$addon->Name} for build\n";
                $addon->BuildQueued = true;
            } else {
                $built = (int) $addon->obj('BuiltAt')->format('U');

                foreach ($versions as $version) {
                    if (strtotime($version->getTime()) > $built) {
                        $buildJob = new BuildAddonJob(['package' => $addon->ID]);
                        singleton(QueuedJobService::class)->queueJob($buildJob);
                        echo " - Queued {$addon->Name} version {$version->Name} for build\n";
                        $addon->BuildQueued = true;

                        break;
                    }
                }
            }
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
        $version->License = $package->getLicense();
        // $version->Support = $package->getSupport();

        echo " - Processed version {$version->Version}\n";

        $addon->Versions()->add($version);

        $this->updateLinks($version, $package);
        $this->updateCompatibility($addon, $version, $package);
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

        //to-do api have no method to get this.
        /*$suggested = $package->getSuggests();

        if ($suggested) foreach ($suggested as $package => $description) {
            $link = $getLink($package, 'suggest');
            $link->Description = $description;

            $version->Links()->add($link);
        }*/
    }

    private function updateCompatibility(Addon $addon, AddonVersion $version, Version $package)
    {
        $require = null;

        if ($package->getRequire()) {
            foreach ($package->getRequire() as $name => $link) {
                if ((string)$link == 'self.version') {
                    continue;
                }

                if ($name == 'silverstripe/framework') {
                    $require = $link;
                    break;
                }

                if ($name == 'silverstripe/cms') {
                    $require = $link;
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
