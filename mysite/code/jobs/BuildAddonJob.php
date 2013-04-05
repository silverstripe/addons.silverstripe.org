<?php
/**
 * A background job which builds a single add-on.
 */
class BuildAddonJob {

	public function setUp() {
		global $databaseConfig;

		if (!DB::isActive()) {
			DB::connect($databaseConfig);
		}
	}

	public function perform() {
		$package = Addon::get()->byID($this->args['id']);
		$builder = Injector::inst()->get('AddonBuilder');

		$builder->build($package);

		$package->BuildQueued = false;
		$package->write();
	}

}
