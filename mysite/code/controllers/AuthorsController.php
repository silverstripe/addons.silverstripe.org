<?php
/**
 * Handles displaying package authors.
 */
class AuthorsController extends SiteController
{

    public static $url_handlers = [
        '$AuthorID!' => 'author'
    ];

    public static $allowed_actions = [
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

    public function Link()
    {
        return Controller::join_links(Director::baseURL(), 'authors');
    }

    public function Authors()
    {
        $authors = AddonAuthor::get();
        $authors = $authors->exclude('Name', 'GitHub contributors');
        return $authors;
    }
}
