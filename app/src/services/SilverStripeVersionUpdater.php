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

    private const FINAL_SUPPORTED_LAST_MAJOR = '4.13.x-dev';

    public function __construct(PackagistService $packagist)
    {
        $this->packagist = $packagist;
    }

    public function update()
    {
        SilverStripeVersion::get()->removeAll();
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

            if (!$isDev || version_compare($version, self::FINAL_SUPPORTED_LAST_MAJOR) < 0) {
                continue;
            }

            $match = preg_match(
                '/^([4-9]+)\.([0-9]+)\.x-dev$/',
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
}
