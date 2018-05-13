<?php

use SilverStripe\Control\Controller;

/**
 * Displays information about an add-on and its versions.
 */
class AddonController extends SiteController
{

    private static $allowed_actions = array(
        'index'
    );

    protected $parent;
    protected $addon;

    public function __construct(Controller $parent, Addon $addon)
    {
        $this->parent = $parent;
        $this->addon = $addon;

        parent::__construct();
    }

    public function index()
    {
        return $this->renderWith(array('Addon', 'Page'));
    }

    public function Title()
    {
        return $this->addon->Name;
    }

    public function Link($action = null)
    {
        return $this->addon->Link($action);
    }

    public function Addon()
    {
        return $this->addon;
    }
}
