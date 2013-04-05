<?php
/**
 * Displays an individual author and lists their add-ons.
 */
class AuthorController extends SiteController {

	public static $allowed_actions = array(
		'index'
	);

	protected $parent;
	protected $author;

	public function __construct(Controller $parent, AddonAuthor $author) {
		$this->parent = $parent;
		$this->author = $author;

		parent::__construct();
	}

	public function index() {
		return $this->renderWith(array('Author', 'Page'));
	}

	public function Title() {
		return $this->author->Name;
	}

	public function Link() {
		return Controller::join_links($this->parent->Link(), $this->author->ID);
	}

	public function Author() {
		return $this->author;
	}

}