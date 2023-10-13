<?php

use Composer\Package\Package;
use Composer\Package\PackageInterface;
use SilverStripe\Assets\Filesystem;

/**
 * Downloads an add-on and builds more details information about it.
 */
class AddonBuilder
{
    private $packagist;

    public function __construct(PackagistService $packagist)
    {
        $this->packagist = $packagist;
    }

    public function build(Addon $addon)
    {
        putenv("GIT_SSH_COMMAND=\"ssh -o StrictHostKeyChecking=no\"");

        $package = $this->packagist->getPackageDetails($addon->Name);
        $packageVersions = $package->getVersions();
        $time = time();

        if (!$packageVersions) {
            echo "No versions found on Packagist for " . $addon->Name . "; deleting orphan record.\n";
            $addon->delete();
            return;
        }

        // Get the latest local and packagist version pair.
        $defaultVersion = $addon->DefaultVersion();
        if (!$defaultVersion) {
            echo "No versions found for " . $addon->Name . "; deleting orphan record.\n";
            $addon->delete();
            return;
        }

        // Update general metadata
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

        // Loops through versions, but only builds for the latest version
        foreach ($packageVersions as $packageVersion) {
            // Packagist API responses are inconsistent, so we need to check both.
            // p/<package>json normalises "dev-master" as "9999999-dev"
            // packages/<package>json normalises "dev-master" as "dev-master"
            $matchesNormalisedVersion = $packageVersion->getVersionNormalized() === $defaultVersion->Version;
            $matchesVersion = $packageVersion->getVersion() === $defaultVersion->PrettyVersion;
            if (!$matchesNormalisedVersion && !$matchesVersion) {
                continue;
            }

            // Convert PackagistAPI result into class compatible with Composer logic
            $package = new Package(
                $addon->Name,
                $packageVersion->getVersionNormalized(),
                $packageVersion->getVersion()
            );

            if ($extra = $packageVersion->getExtra()) {
                $package->setExtra((array) $extra);
            }
            if ($source = $packageVersion->getSource()) {
                $package->setSourceUrl($source->getUrl());
                $package->setSourceType($source->getType());
                $package->setSourceReference($source->getReference());
            }
            if ($dist = $packageVersion->getDist()) {
                $package->setDistUrl($dist->getUrl());
                $package->setDistType($dist->getType());
                $package->setDistReference($dist->getReference());
            }
        }

        $addon->LastBuilt = $time;
        $addon->write();
    }

    /**
     * Determine if the repository is from GitHub, and if so then return the "context" (vendor/module) from the path
     *
     * @param  Addon $addon
     * @return string|false
     */
    public function getGitHubContext(Addon $addon)
    {
        $repository = $addon->Repository;
        if (stripos($repository, '://github.com/') === false) {
            return false;
        }

        preg_match('/^http(?:s?):\/\/github\.com\/(?<module>.*)(\.git)?$/U', $repository, $matches);

        if (isset($matches['module'])) {
            return $matches['module'];
        }

        return false;
    }

    /**
     * Determine whether an addon is hosted on GitHub
     *
     * @param  Addon $addon
     * @return bool
     */
    public function hasGitHubRepository(Addon $addon)
    {
        return (strpos($addon->Repository, 'github.com') !== false);
    }
}
