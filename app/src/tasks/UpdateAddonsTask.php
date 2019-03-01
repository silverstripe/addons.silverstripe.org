<?php

use SilverStripe\Control\HTTPRequest;
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
     * @param HTTPRequest $request
     */
    public function run($request)
    {
        $addons = $request->getVar('addons');
        $this->updater->update(
            (bool)$request->getVar('clear'),
            $addons ? explode(',', $addons) : null
        );
    }
}
