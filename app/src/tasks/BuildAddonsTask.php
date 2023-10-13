<?php

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Dev\BuildTask;

/**
 * Updates addons. Should usually be handled by a redis queue which interacts directly with
 * {@link BuildAddonJob}, but this task can help with debugging.
 *
 * @package mysite
 */
class BuildAddonsTask extends BuildTask
{
    /**
     * {@inheritDoc}
     * @var string
     */
    protected $title = 'Build Add-ons';

    /**
     * {@inheritDoc}
     * @var string
     */
    protected $description = 'Updates metadata for addons we found with UpdateAddonsTask';

    /**
     * @var AddonBuilder
     */
    protected $builder;

    /**
     * @param AddonBuilder $builder
     */
    public function __construct(AddonBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * {@inheritDoc}
     * @param HTTPRequest $request
     */
    public function run($request)
    {
        $addons = Addon::get();
        if ($request->getVar('addons')) {
            $addons = $addons->filter('Name', explode(',', $request->getVar('addons')));
        } else {
            // Don't bother updating abandoned packages
            $addons = $addons->exclude('Abandoned', '1');
        }

        if (!$addons->Count()) {
            $this->log('No addons found');
            return;
        }

        foreach ($addons as $addon) {
            if (!$addon->Name) {
                $this->log(sprintf('Addon #%d as no name, skipping', $addon->ID));
                continue;
            }

            /** @var Addon $addon */
            $this->log(sprintf('Building "%s" (#%d)', $addon->Name, $addon->ID));
            try {
                $this->builder->build($addon);
            } catch (RuntimeException $e) {
                $this->log('Error: ' . $e->getMessage());
            }

            if ($addon->ID) {
                $addon->BuildQueued = false;
                $addon->write();
            }
        }
    }

    /**
     * @param string $msg
     */
    protected function log($msg)
    {
        echo $msg . PHP_EOL;
    }
}
