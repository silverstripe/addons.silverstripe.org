<?php
/**
 * Updates the available SilverStripe versions.
 */
class UpdateSilverStripeVersionsTask extends BuildTask {

	protected $title = 'Update SilverStripe Versions';

	protected $description = 'Updates the available SilverStripe versions';

	/**
	 * @var SilverStripeVersionUpdater
	 */
	private $updater;

	public function __construct(SilverStripeVersionUpdater $updater) {
		$this->updater = $updater;
	}

	public function run($request) {
		$this->updater->update();
	}

}
