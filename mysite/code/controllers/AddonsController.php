<?php

use Elastica\Filter\BoolAnd;
use Elastica\Filter\Term;
use Elastica\Filter\Terms;
use Elastica\Query;
use Elastica\Query\Match;
use Elastica\Search;
use SilverStripe\Elastica\ElasticaService;
use SilverStripe\Elastica\ResultList;

/**
 * Lists and searches add-ons.
 */
class AddonsController extends SiteController {

	public static $url_handlers = array(
		'rss' => 'rss',
		'$Vendor!/$Name!' => 'addon',
		'$Vendor!' => 'vendor',
	);

	public static $allowed_actions = array(
		'index',
		'addon',
		'vendor',
		'rss',
	);

	public static $dependencies = array(
		'ElasticaService' => '%$SilverStripe\Elastica\ElasticaService'
	);

	/**
	 * @var \SilverStripe\Elastica\ElasticaService
	 */
	private $elastica;

	public function index() {
		return $this->renderWith(array('Addons', 'Page'));
	}

	public function setElasticaService(ElasticaService $elastica) {
		$this->elastica = $elastica;
	}

	public function addon($request) {
		$vendor = $request->param('Vendor');
		$name = $request->param('Name');
		$addon = Addon::get()->filter('Name', "$vendor/$name")->first();

		if (!$addon) {
			$this->httpError(404);
		}

		return new AddonController($this, $addon);
	}

	public function vendor($request) {
		$name = $request->param('Vendor');
		$vendor = AddonVendor::get()->filter('Name', $name)->first();

		if (!$vendor) {
			$this->httpError(404);
		}

		return new VendorController($this, $vendor);
	}

	public function Title() {
		return 'Add-ons';
	}

	public function Link() {
		return Controller::join_links(Director::baseURL(), 'add-ons');
	}

	public function ListView() {
		$view = $this->request->getVar('view');
		if($view) {
			return $view;
		} else {
			return 'list';
		}
	}

	public function Addons() {
		$list = Addon::get();

		$search = $this->request->getVar('search');
		$type = $this->request->getVar('type');
		$compat = $this->request->getVar('compatibility');
		$tags = $this->request->getVar('tags');
		$sort = $this->request->getVar('sort');
		$view = $this->request->getVar('view');

		if (!$view) {
			$view = 'list';
		}

		if (!in_array($sort, array('name', 'downloads', 'newest'))) {
			$sort = null;
		}

		// Proxy out a search to elastic if any parameters are set.
		if ($search || $type || $compat || $tags) {
			$filter = new BoolAnd();

			$query = new Query();
			$query->setSize(count($list));

			if ($search) {
				$match = new Match();
				$match->setField('_all', $search);

				$query->setQuery($match);
			}

			if ($type) {
				$filter->addFilter(new Term(array('type' => $type)));
			}

			if ($compat) {
				$filter->addFilter(new Terms('compatible', (array) $compat));
			}

			if ($tags) {
				$filter->addFilter(new Terms('tag', (array) $tags));
			}

			if ($type || $compat || $tags) {
				$query->setFilter($filter);
			}

			$list = new ResultList($this->elastica->getIndex(), $query);

			if ($sort) {
				$ids = $list->column('ID');

				if ($ids) {
					$list = Addon::get()->byIDs($ids);
				} else {
					$list = new ArrayList();
				}
			}
		} else {
			if (!$sort) $sort = 'downloads';
		}

		switch ($sort) {
			case 'name': $list = $list->sort('Name'); break;
			case 'newest': $list = $list->sort('Released', 'DESC'); break;
			case 'downloads': $list = $list->sort('Downloads', 'DESC'); break;
		}

		$list = new PaginatedList($list, $this->request);
		$list->setPageLength(16);

		return $list;
	}

	public function AddonsSearchForm() {
		$form = new Form(
			$this,
			'AddonsSearchForm',
			new FieldList(array(
				TextField::create('search', 'Search for')
					->setValue($this->request->getVar('search'))
					->addExtraClass('input-block-level'),
				DropdownField::create('sort', 'Sort by')
					->setSource(array(
						'name' => 'Name',
						'downloads' => 'Most downloaded',
						'newest' => 'Newest'
					))
					->setEmptyString('Best match')
					->setValue($this->request->getVar('sort'))
					->addExtraClass('input-block-level'),
				DropdownField::create('type', 'Add-on type')
					->setSource(array(
						'module' => 'Modules',
						'theme' => 'Themes'
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

	public function rss($request, $limit = 10) {
		$addons = Addon::get()
			->sort('Released', 'DESC')
			->limit($limit);

		$rss = new RSSFeed($addons, $this->Link(), "All addons");
    	return $rss->outputToBrowser();
	}

}
