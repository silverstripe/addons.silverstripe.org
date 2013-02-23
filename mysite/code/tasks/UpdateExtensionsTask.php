<?php
/**
 * Runs the extension updater.
 */
class UpdateExtensionsTask extends BuildTask {

	protected $title = 'Update Extensions';

	protected $description = 'Updates extensions from Packagist';

	private $updater;

	public function __construct(ExtensionUpdater $updater) {
		$this->updater = $updater;
	}

	public function run($request) {
		$this->updater->update();
	}

}
