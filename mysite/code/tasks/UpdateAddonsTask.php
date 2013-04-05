<?php
/**
 * Runs the add-on updater.
 */
class UpdateAddonsTask extends BuildTask {

	protected $title = 'Update Add-ons';

	protected $description = 'Updates add-ons from Packagist';

	private $updater;

	public function __construct(AddonUpdater $updater) {
		$this->updater = $updater;
	}

	public function run($request) {
		$this->updater->update();
	}

}
