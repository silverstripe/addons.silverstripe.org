<?php

use SilverStripe\Control\Controller;

/**
 * Displays an individual author and lists their add-ons.
 */
class AuthorController extends SiteController
{

    private static $allowed_actions = array(
        'index'
    );

    protected $parent;
    protected $author;

    public function __construct(Controller $parent, AddonAuthor $author)
    {
        $this->parent = $parent;
        $this->author = $author;

        parent::__construct();
    }

    public function index()
    {
        return $this->renderWith(array('Author', 'Page'));
    }

    public function Title()
    {
        return $this->author->Name;
    }

    public function Link($action = null)
    {
        return Controller::join_links($this->parent->Link($action), $this->author->ID);
    }

    public function Author()
    {
        return $this->author;
    }
}
