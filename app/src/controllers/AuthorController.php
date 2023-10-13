<?php

use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;

/**
 * Displays an individual author and lists their add-ons.
 */
class AuthorController extends SiteController
{

    private static $url_handlers = [
        '$AuthorID!' => 'author'
    ];

    private static $allowed_actions = array(
        'author'
    );

    public function index()
    {
        $this->httpError(404);
    }

    public function author(HTTPRequest $request)
    {
        $id = $request->param('AuthorID');
        $author = AddonAuthor::get()->byID($id);

        if (!$author) {
            $this->httpError(404);
        }

        return $this->renderWith(array('Author', 'Page'), $this->getTemplateVars($author));
    }

    private function getTemplateVars(AddonAuthor $author)
    {
        return [
            'Author' => $author,
            'Title' => $author->Name,
        ];
    }

    public function Link($action = null)
    {
        return Controller::join_links(Director::baseURL(), 'author', $action);
    }
}
