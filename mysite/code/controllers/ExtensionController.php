<?php
/**
 * Displays information about an extension and its versions.
 */
class ExtensionController extends SiteController {

	public static $allowed_actions = array(
		'index'
	);

	protected $parent;
	protected $extension;

	public function __construct(Controller $parent, ExtensionPackage $extension) {
		$this->parent = $parent;
		$this->extension = $extension;

		parent::__construct();
	}

	public function index() {
		return $this->renderWith(array('Extension', 'Page'));
	}

	public function Title() {
		return $this->extension->Name;
	}

	public function Link() {
		return $this->extension->Link();
	}

	public function Extension() {
		return $this->extension;
	}

}
