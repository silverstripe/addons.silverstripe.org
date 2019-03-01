<?php

use SilverStripe\Control\Director;
use SilverStripe\Control\Controller;

/**
 * Instructions on how to submit a module.
 * Doesn't actually handle the submission itself,
 * that's left to packagist.org.
 */
class SubmitAddonController extends SiteController
{

    private static $allowed_actions = array(
        'index',
    );

    public function index()
    {
        return $this->renderWith(array('SubmitAddon', 'Page'));
    }

    public function Title()
    {
        return 'Submit';
    }

    public function Link($action = null)
    {
        return Controller::join_links(Director::baseURL(), 'submit', $action);
    }

    public function MenuItemType()
    {
        return 'button';
    }
}
