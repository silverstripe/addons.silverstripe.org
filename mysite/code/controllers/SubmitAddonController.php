<?php
/**
 * Instructions on how to submit a module.
 * Doesn't actually handle the submission itself,
 * that's left to packagist.org.
 */
class SubmitAddonController extends SiteController {

	public static $allowed_actions = array(
		'index',
	);

	public function index() {
		return $this->renderWith(array('SubmitAddon', 'Page'));
	}

	public function Title() {
		return 'Submit';
	}

	public function Link() {
		return Controller::join_links(Director::baseURL(), 'submit');
	}

	public function MenuItemType() {
		return 'button';
	}

}
