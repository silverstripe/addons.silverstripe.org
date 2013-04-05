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

	public function PopularAddons($limit = 10) {
		return Addon::get()->sort('Downloads', 'DESC')->limit($limit);
	}

	public function NewestAddons($limit = 10) {
		return Addon::get()->sort('Released', 'DESC')->limit($limit);
	}

	public function RandomAddons($limit = 10) {
		return Addon::get()->sort(DB::getConn()->random(), 'DESC')->limit($limit);
	}

	public function NewestVersions($limit = 10) {
		return AddonVersion::get()
			->filter('Development', false)
			->sort('Released', 'DESC')
			->limit($limit);
	}

}
