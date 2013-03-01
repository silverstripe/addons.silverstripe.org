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
		Injector::inst()->get('ExtensionBuilder')->build(ExtensionPackage::get()->byID($this->args['id']));
	}

}
