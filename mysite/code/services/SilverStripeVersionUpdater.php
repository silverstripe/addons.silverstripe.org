<?php
/**
 * Updates the available SilverStripe versions.
 */
class SilverStripeVersionUpdater {

	/**
	 * @var PackagistService
	 */
	private $packagist;

	public function __construct(PackagistService $packagist) {
		$this->packagist = $packagist;
	}

	public function update() {
		$versions = $this->packagist->getPackageVersions('silverstripe/framework');

		foreach ($versions as $package) {
			if (!$package->isDev()) {
				continue;
			}

			$match = preg_match(
				'/^([0-9]+)\.([0-9]+)\.x-dev$/',
				$package->getPrettyVersion(),
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
