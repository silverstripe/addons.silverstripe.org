<?php
/**
 * A background job which builds a single extension.
 */
class BuildExtensionJob {

	public function setUp() {
		global $databaseConfig;

		if (!DB::isActive()) {
			DB::connect($databaseConfig);
		}
	}

	public function perform() {
		$package = ExtensionPackage::get()->byID($this->args['id']);
		$builder = Injector::inst()->get('ExtensionBuilder');

		$builder->build($package);

		$package->BuildQueued = false;
		$package->write();
	}

}
