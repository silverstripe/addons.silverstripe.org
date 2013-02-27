<?php
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

	public function index() {
		return $this->renderWith(array('Extensions', 'Page'));
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

		$list = new PaginatedList($list, $this->request);
		$list->setPageLength(20);

		return $list;
	}

}
