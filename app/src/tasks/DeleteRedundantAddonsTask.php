<?php

use Elastica\Exception\NotFoundException;
use SilverStripe\Dev\BuildTask;

/**
 * Deletes packages removed from Packagist.
 *
 * @package mysite
 */
class DeleteRedundantAddonsTask extends BuildTask
{
    /**
     * {@inheritDoc}
     * @var string
     */
    protected $title = 'Delete Redundant Add-ons';

    /**
     * {@inheritDoc}
     * @var string
     */
    protected $description = 'Deletes packages removed from Packagist';

    /**
     * {@inheritDoc}
     * @param SS_HTTPRequest $request
     */
    public function run($request)
    {
        $dateOneWeekAgo  = date('Y-m-d', strtotime('-1 week'));

        $addons = Addon::get()->filter('LastUpdated:LessThan', $dateOneWeekAgo);

        foreach ($addons as $addon) {
            /** @var Addon $addon */
            try {
                $addon->Keywords()->removeAll();
                $addon->Screenshots()->removeAll();
                $addon->CompatibleVersions()->removeAll();

                foreach ($addon->Versions() as $version) {
                    /** @var AddonVersion $version */
                    $version->Authors()->removeAll();
                    $version->Keywords()->removeAll();
                    $version->CompatibleVersions()->removeAll();
                    $version->delete();
                }

                $addon->delete();
            } catch (NotFoundException $e) {
                // no-op
            }
        }
    }
}
