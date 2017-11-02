<?php

/**
 * Class AjaxController
 * handle the ajax call to select the sorting and return a json response
 */
class AjaxController extends Controller
{

    private static $allowed_actions = array(
        'index'
    );

    /**
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function index(SS_HTTPRequest $request)
    {
        $sortMethod = $request->postVar('type') . 'PopularAddons';
        $addons = HomeController::$sortMethod(5);
        $body = $this->renderWith('PopularAddons', array('PopularAddons' => $addons));
        $response = new SS_HTTPResponse();
        $response->addHeader('Content-Type', 'application/json');
        $response->setBody(Convert::array2json(array('success' => true, 'body' => $body->getValue())));
        return $response;
    }
}
