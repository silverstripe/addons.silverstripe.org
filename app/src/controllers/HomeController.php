<?php

use SilverStripe\Control\Director;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\Limitable;
use SilverStripe\View\ArrayData;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\Queries\SQLSelect;

/**
 * The home page controller.
 */
class HomeController extends SiteController
{
    /**
     * These modules will be excluded from the "popular addons" widgets on the home page. Included by default
     * are all of the SilverStripe core modules which are installed by default, or in CI all builds (stats skewed).
     *
     * @config
     * @var array
     */
    private static $popular_blacklist = array(
        'silverstripe/admin',
        'silverstripe/assets',
        'silverstripe/asset-admin',
        'silverstripe/campaign-admin',
        'silverstripe/cms',
        'silverstripe/errorpage',
        'silverstripe/framework',
        'silverstripe/graphql',
        'silverstripe/postgresql',
        'silverstripe/reports',
        'silverstripe/siteconfig',
        'silverstripe/sqlite3',
        'silverstripe/versioned',
        'silverstripe/versioned-admin',
        'silverstripe-themes/simple',
    );

    private static $allowed_actions = array(
        'index'
    );

    public function index()
    {
        return $this->renderWith(array('Home', 'Page'));
    }

    public function Title()
    {
        return 'Home';
    }

    public function Link($action = null)
    {
        return Controller::join_links(Director::baseURL(), $action);
    }

    /**
     * @param int $limit
     * @return DataList|Limitable default to Relative addons
     */
    public function PopularAddons($limit = 10)
    {
        return self::RelativePopularAddons($limit);
    }

    /**
     * @param int $limit
     * @return DataList|Limitable List sorted by absolute downloads
     */
    public static function AbsolutePopularAddons($limit = 10)
    {
        return Addon::get()
            ->sort('Downloads', 'DESC')
            ->exclude('Name', self::config()->popular_blacklist)
            ->limit($limit);
    }

    public static function RelativePopularAddons($limit = 10)
    {
        $addons = Addon::get()
            ->exclude(array('Name' => self::config()->popular_blacklist));
        $list = ArrayList::create($addons->toArray());
        /** @var ArrayList $addons */
        $addons = $list->sort('relativePopularity DESC');
        $addons = $addons->limit($limit);
        foreach ($addons as $addon) {
            $addon->Score = $addon->relativePopularityFormatted() . ' per day';
        }

        return $addons;
    }

    public function NewestAddons($limit = 10)
    {
        return Addon::get()->sort('Released', 'DESC')->limit($limit);
    }

    public function RandomAddons($limit = 10)
    {
        return Addon::get()->sort(DB::get_conn()->random(), 'DESC')->limit($limit);
    }

    public function NewestVersions($limit = 10)
    {
        return AddonVersion::get()
            ->filter('Development', false)
            ->sort('Released', 'DESC')
            ->limit($limit);
    }
}
