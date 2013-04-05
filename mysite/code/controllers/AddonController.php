<?php
/**
 * Displays information about an add-on and its versions.
 */
class AddonController extends SiteController {

	public static $allowed_actions = array(
		'index'
	);

	protected $parent;
	protected $addon;

	public function __construct(Controller $parent, Addon $addon) {
		$this->parent = $parent;
		$this->addon = $addon;

		parent::__construct();
	}

	public function index() {
		return $this->renderWith(array('Addon', 'Page'));
	}

	public function Title() {
		return $this->addon->Name;
	}

	public function Link() {
		return $this->addon->Link();
	}

	public function Addon() {
		return $this->addon;
	}

}
