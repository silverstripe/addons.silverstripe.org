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
 * Lists and searches extensions.
 */
class ExtensionsController extends SiteController {

	public static $url_handlers = array(
		'$Vendor!/$Name!' => 'extension'
	);

	public static $allowed_actions = array(
		'index',
		'extension'
	);

	public static $dependencies = array(
		'ElasticaService' => '%$SilverStripe\Elastica\ElasticaService'
	);

	/**
	 * @var \SilverStripe\Elastica\ElasticaService
	 */
	private $elastica;

	public function index() {
		return $this->renderWith(array('Extensions', 'Page'));
	}

	public function setElasticaService(ElasticaService $elastica) {
		$this->elastica = $elastica;
	}

	public function extension($request) {
		$vendor = $request->param('Vendor');
		$name = $request->param('Name');
		$ext = ExtensionPackage::get()->filter('Name', "$vendor/$name")->first();

		if (!$ext) {
			$this->httpError(404);
		}

		return new ExtensionController($this, $ext);
	}

	public function Title() {
		return 'Extensions';
	}

	public function Link() {
		return Controller::join_links(Director::baseURL(), 'extensions');
	}

	public function Extensions() {
		$list = ExtensionPackage::get();

		$search = $this->request->getVar('search');
		$type = $this->request->getVar('type');
		$compat = $this->request->getVar('compatibility');
		$tags = $this->request->getVar('tags');
		$sort = $this->request->getVar('sort');

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
					$list = ExtensionPackage::get()->byIDs($ids);
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
		$list->setPageLength(15);

		return $list;
	}

	public function ExtensionsSearchForm() {
		$form = new Form(
			$this,
			'ExtensionsSearchForm',
			new FieldList(array(
				TextField::create('search', 'Search')
					->setValue($this->request->getVar('search'))
					->addExtraClass('input-block-level'),
				DropdownField::create('sort', 'Sort By')
					->setSource(array(
						'name' => 'Name',
						'downloads' => 'Most downloaded',
						'newest' => 'Newest'
					))
					->setEmptyString('Best match')
					->setValue($this->request->getVar('sort'))
					->addExtraClass('input-block-level'),
				DropdownField::create('type', 'Search For')
					->setSource(array(
						'module' => 'Modules',
						'theme' => 'Themes'
					))
					->setEmptyString('Modules and themes')
					->setValue($this->request->getVar('type'))
					->addExtraClass('input-block-level'),
				CheckboxSetField::create('compatibility', 'Compatible With')
					->setSource(SilverStripeVersion::get()->map('Name', 'Name'))
					->setValue($this->request->getVar('compatibility'))
					->setTemplate('ExtensionsSearchFormCompatibility')
			)),
			new FieldList()
		);

		return $form
			->setFormMethod('GET')
			->setFormAction($this->Link());
	}

}
