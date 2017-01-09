<?php

/**
 * The home page controller.
 */
class HomeController extends SiteController
{

    private static $popular_blacklist = array(
        'silverstripe/framework',
        'silverstripe/cms',
        'silverstripe/sqlite3',
        'silverstripe/postgresql',
        'silverstripe/reports',
        'silverstripe/siteconfig',
        'silverstripe-themes/simple'
    );

    public static $allowed_actions = array(
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

    public function Link()
    {
        return Director::baseURL();
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
        return Addon::get()->sort(DB::getConn()->random(), 'DESC')->limit($limit);
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

        $sqlQuery = new SQLQuery();
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
