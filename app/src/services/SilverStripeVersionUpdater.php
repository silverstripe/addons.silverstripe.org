<?php
/**
 * Updates the available SilverStripe versions.
 */

use Composer\Package\Version\VersionParser;

class SilverStripeVersionUpdater
{

    /**
     * @var PackagistService
     */
    private $packagist;

    /**
     * @config
     * @var array
     */
    protected $versionBlacklist = array('2.5.x-dev');

    public function __construct(PackagistService $packagist)
    {
        $this->packagist = $packagist;
    }

    public function update()
    {
        $versions = $this->packagist->getPackageVersions('silverstripe/framework');

        foreach ($versions as $package) {
            $version = $package->getVersion();

            // Replace version by branch alias if applicable
            $extra = $package->getExtra();
            if (isset($extra['branch-alias'][$version])) {
                $version = $extra['branch-alias'][$version];
            }
            $stability = VersionParser::parseStability($version);

            $isDev = $stability === 'dev';
            
            if (!$isDev || in_array($version, $this->versionBlacklist)) {
                continue;
            }

            $match = preg_match(
                '/^([0-9]+)\.([0-9]+)\.x-dev$/',
                $version,
                $matches
            );

            if (!$match) {
                continue;
            }

            $major = $matches[1];
            $minor = $matches[2];

            $record = SilverStripeVersion::get()
                ->filter('Major', $major)
                ->filter('Minor', $minor)
                ->first();

            if (!$record) {
                $record = new SilverStripeVersion();
                $record->Name = "$major.$minor";
                $record->Major = $major;
                $record->Minor = $minor;
                $record->write();
            }
        }
    }

    public function setVersionBlacklist($list)
    {
        $this->versionBlacklist = $list;
    }

    public function getVersionBlacklist()
    {
        return $this->versionBlacklist;
    }
}
