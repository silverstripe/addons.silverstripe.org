<?php

use Elastica\Query;
use Elastica\Query\Match;
use SilverStripe\Elastica\ElasticaService;
use SilverStripe\Elastica\ResultList;

/**
 * Lists and searches add-ons.
 */
class AddonsController extends SiteController
{

    public static $url_handlers = array(
        'rss'             => 'rss',
        '$Vendor!/$Name!' => 'addon',
        '$Vendor!'        => 'vendor',
    );

    public static $allowed_actions = array(
        'index',
        'addon',
        'vendor',
        'rss',
    );

    public static $dependencies = array(
        'ElasticaService' => '%$ElasticaService'
    );

    /**
     * @var \SilverStripe\Elastica\ElasticaService
     */
    private $elastica;

    public function index()
    {
        return $this->renderWith(array('Addons', 'Page'));
    }

    public function setElasticaService(ElasticaService $elastica)
    {
        $this->elastica = $elastica;
    }

    public function addon($request)
    {
        $vendor = $request->param('Vendor');
        $name = $request->param('Name');
        $addon = Addon::get()->filter('Name', "$vendor/$name")->first();

        if (!$addon) {
            $this->httpError(404);
        }

        return new AddonController($this, $addon);
    }

    public function vendor($request)
    {
        $name = $request->param('Vendor');
        $vendor = AddonVendor::get()->filter('Name', $name)->first();

        if (!$vendor) {
            $this->httpError(404);
        }

        return new VendorController($this, $vendor);
    }

    public function Title()
    {
        return 'Add-ons';
    }

    public function Link($slug = null)
    {
        if ($slug) {
            return Controller::join_links(Director::baseURL(), 'add-ons', $slug);
        } else {
            return Controller::join_links(Director::baseURL(), 'add-ons');
        }
    }

    public function ListView()
    {
        $view = $this->request->getVar('view');
        if ($view) {
            return $view;
        } else {
            return 'list';
        }
    }

    public function Addons()
    {
        $list = Addon::get();

        $search = $this->request->getVar('search');
        $type = $this->request->getVar('type');
        $compat = $this->request->getVar('compatibility');
        $tags = $this->request->getVar('tags');
        $sort = $this->request->getVar('sort');

        if (!in_array($sort, array('name', 'downloads', 'newest', 'relative'))) {
            $sort = null;
        }

        // Proxy out a search to elastic if any parameters are set.
        if ($search || $type || $compat || $tags) {
            $bool = new Query\BoolQuery();

            $query = new Query();
            $query->setQuery($bool);
            $query->setSize(count($list));

            if ($search) {
                $match = new Match();
                $match->setField('_all', $search);

                $bool->addMust($match);
            }

            if ($type) {
                $bool->addMust(new Query\Term(array('type' => $type)));
            }

            if ($compat) {
                $bool->addMust(new Query\Terms('compatibility', (array)$compat));
            }

            if ($tags) {
                $bool->addMust(new Query\Terms('tag', (array)$tags));
            }

            $list = new ResultList($this->elastica->getIndex(), $query);

            if ($sort) {
                $ids = $list->column('ID');

                if ($ids) {
                    $list = Addon::get()->byIDs($ids);
                } else {
                    $list = new ArrayList();
                }
            } else {
                $list = $list->toArrayList();
            }
        } else {
            if (!$sort) {
                $sort = 'relative';
            }
        }

        switch ($sort) {
            case 'name':
                $list = $list->sort('Name');
                break;
            case 'newest':
                $list = $list->sort('Released', 'DESC');
                break;
            case 'downloads':
                $list = $list->sort('Downloads', 'DESC');
                break;
            case 'relative':
                if (!$list instanceof ArrayList) {
                    /** @var ArrayList|Addon[] $unsorted */
                    $unsorted = ArrayList::create($list->toArray());
                } else {
                    $unsorted = $list;
                }
                foreach ($unsorted as $item) {
                    $item->Score = $item->relativePopularityFormatted() . ' per day';
                }
                $list = $unsorted->sort('relativePopularity DESC');
                break;
            default:
                $list = $list->sort('Downloads', 'DESC');
        }

        $list = new PaginatedList($list, $this->request);
        $list->setPageLength(16);

        return $list;
    }

    public function AddonsSearchForm()
    {
        $form = new Form(
            $this,
            'AddonsSearchForm',
            new FieldList(array(
                TextField::create('search', 'Search for')
                    ->setValue($this->request->getVar('search'))
                    ->addExtraClass('input-block-level'),
                DropdownField::create('sort', 'Sort by')
                    ->setSource(array(
                        'name'      => 'Name',
                        'downloads' => 'Most downloaded',
                        'relative'  => 'Average downloads per day',
                        'newest'    => 'Newest'
                    ))
                    ->setEmptyString('Best match')
                    ->setValue($this->request->getVar('sort'))
                    ->addExtraClass('input-block-level'),
                DropdownField::create('type', 'Add-on type')
                    ->setSource(array(
                        'module' => 'Modules',
                        'theme'  => 'Themes'
                    ))
                    ->setEmptyString('Modules and themes')
                    ->setValue($this->request->getVar('type'))
                    ->addExtraClass('input-block-level'),
                CheckboxSetField::create('compatibility', 'Compatible SilverStripe versions')
                    ->setSource(SilverStripeVersion::get()->map('Name', 'Name'))
                    ->setValue($this->request->getVar('compatibility'))
                    ->setTemplate('AddonsSearchCheckboxSetField')
            )),
            new FieldList()
        );

        return $form
            ->setFormMethod('GET')
            ->setFormAction($this->Link());
    }

    public function rss($request, $limit = 10)
    {
        $addons = Addon::get()
            ->sort('Released', 'DESC')
            ->limit($limit);

        $rss = new RSSFeed(
            $addons,
            $this->Link(),
            "Newest addons on addons.silverstripe.org",
            null,
            'RSSTitle'
        );

        return $rss->outputToBrowser();
    }

}
