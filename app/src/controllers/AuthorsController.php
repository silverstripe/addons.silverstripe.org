<?php

use SilverStripe\Control\Director;
use SilverStripe\Control\Controller;

/**
 * Handles displaying package authors.
 */
class AuthorsController extends SiteController
{

    private static $url_handlers = [
        '$AuthorID!' => 'author'
    ];

    private static $allowed_actions = [
        'index',
        'author'
    ];

    public function index()
    {
        return $this->renderWith(['Authors', 'Page']);
    }

    public function author($request)
    {
        $id = $request->param('AuthorID');
        $author = AddonAuthor::get()->byID($id);

        if (!$author) {
            $this->httpError(404);
        }

        return new AuthorController($this, $author);
    }

    public function Title()
    {
        return 'Authors';
    }

    public function Link($action = null)
    {
        return Controller::join_links(Director::baseURL(), 'authors', $action);
    }

    public function Authors()
    {
        $authors = AddonAuthor::get();
        $authors = $authors->exclude('Name', 'GitHub contributors');
        return $authors;
    }
}
