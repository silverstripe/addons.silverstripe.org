<?php

use SilverStripe\Control\Director;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DB;
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
     * @return DataList|SS_Limitable default to Relative addons
     */
    public function PopularAddons($limit = 10)
    {
        return self::RelativePopularAddons($limit);
    }

    /**
     * @param int $limit
     * @return DataList|SS_Limitable List sorted by absolute downloads
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

    public function ChartData()
    {
        $chartData = array();
        $list = ArrayList::create(array());

        $sqlQuery = new SQLSelect();
        $sqlQuery->setFrom('Addon');
        $sqlQuery->setSelect('DATE(Created) as Created');
        $sqlQuery->selectField('COUNT(*)', 'CountInOneDay');
        $sqlQuery->addWhere('"Created" >= DATE_SUB(NOW(), INTERVAL 30 DAY)');
        $sqlQuery->addGroupBy('DATE(Created)');

        $result = $sqlQuery->execute();

        if (count($result)) {
            foreach ($result as $row) {
                $date = date('j M Y', strtotime($row['Created']));
                if (!isset($chartData[$date])) {
                    $chartData[$date] = $row['CountInOneDay'];
                }
            }
        }

        if (count($chartData)) {
            foreach ($chartData as $x => $y) {
                $list->push(ArrayData::create(array(
                    'XValue' => $x,
                    'YValue' => $y
                )));
            }
        }

        return $list;
    }
}
