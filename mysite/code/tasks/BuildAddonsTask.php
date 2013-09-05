<?php
/**
 * Updates addons. Should usually be handled by a redis queue
 * which interacts directly with {@link BuildAddonJob},
 * but this task can help with debugging.
 */
class BuildAddonsTask extends BuildTask {

	protected $title = 'Build Add-ons';

	protected $description = 'Downloads README and screenshots';

	protected $builder;

	function __construct(AddonBuilder $builder) {
		$this->builder = $builder;
	}

	public function run($request) {
		$addons = Addon::get();
		if($request->getVar('addons')) {
			$addons = $addons->filter('Name', explode(',', $request->getVar('addons')));
		}

		foreach($addons as $addon) {
			$this->log(sprintf('Building "%s"', $addon->Name));
			$this->builder->build($addon);
			$addon->BuildQueued = false;
			$addon->write();
		}
	}

	protected function log($msg) {
		echo $msg . "\n";
	}

}
