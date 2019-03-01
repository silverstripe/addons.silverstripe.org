<?php

use SilverStripe\Dev\BuildTask;

/**
 * Runs the add-on updater.
 *
 * @package mysite
 */
class UpdateAddonsTask extends BuildTask
{
    /**
     * {@inheritDoc}
     * @var string
     */
    protected $title = 'Update Add-ons';

    /**
     * {@inheritDoc}
     * @var string
     */
    protected $description = 'Updates add-ons from Packagist';

    /**
     * @var AddonUpdater
     */
    private $updater;

    /**
     * @param AddonUpdater $updater
     */
    public function __construct(AddonUpdater $updater)
    {
        $this->updater = $updater;
    }

    /**
     * {@inheritDoc}
     * @param SS_HTTPRequest $request
     */
    public function run($request)
    {
        $this->updater->update(
            (bool)$request->getVar('clear'),
            $request->getVar('addons') ? explode(',', $request->getVar('addons')) : null
        );
    }
}
