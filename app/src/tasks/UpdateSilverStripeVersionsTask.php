<?php

use SilverStripe\Dev\BuildTask;

/**
 * Updates the available SilverStripe versions.
 *
 * @package mysite
 */
class UpdateSilverStripeVersionsTask extends BuildTask
{
    /**
     * {@inheritDoc}
     * @var string
     */
    protected $title = 'Update SilverStripe Versions';

    /**
     * {@inheritDoc}
     * @var string
     */
    protected $description = 'Updates the available SilverStripe versions';

    /**
     * @var SilverStripeVersionUpdater
     */
    private $updater;

    /**
     * @param SilverStripeVersionUpdater $updater
     */
    public function __construct(SilverStripeVersionUpdater $updater)
    {
        $this->updater = $updater;
    }

    /**
     * {@inheritDoc}
     * @param SS_HTTPRequest $request
     */
    public function run($request)
    {
        $this->updater->update();
    }
}
