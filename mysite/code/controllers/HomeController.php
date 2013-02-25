<?php
/**
 * The home page controller.
 */
class HomeController extends SiteController {

	public static $allowed_actions = array(
		'index'
	);

	public function index() {
		return $this->renderWith(array('Home', 'Page'));
	}

	public function Title() {
		return 'Home';
	}

	public function Link() {
		return Director::baseURL();
	}

	public function PopularExtensions($limit = 10) {
		return ExtensionPackage::get()->sort('Downloads', 'DESC')->limit($limit);
	}

	public function NewestExtensions($limit = 10) {
		return ExtensionPackage::get()->sort('Released', 'DESC')->limit($limit);
	}

	public function RandomExtensions($limit = 10) {
		return ExtensionPackage::get()->sort(DB::getConn()->random(), 'DESC')->limit($limit);
	}

	public function NewestReleases($limit = 10) {
		return ExtensionVersion::get()
			->filter('Development', false)
			->sort('Released', 'DESC')
			->limit($limit);
	}

}
