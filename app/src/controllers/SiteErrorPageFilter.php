<?php

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;

/**
 * Renders custom error pages when an error response is returned.
 */
class SiteErrorPageFilter
{

    /**
     * @ignore
     */
    public function preRequest()
    {
    }

    /**
     * @param HTTPRequest $request
     * @param HTTPResponse $response
     */
    public function postRequest($request, $response)
    {
        if ($response && $response->getStatusCode() == 404) {
            $controller = new SiteController();
            $controller = $controller->customise(array('Title' => 'Page Not Found'));
            $body = $controller->renderWith(array('ErrorPage_404', 'ErrorPage', 'Page'));

            $response->addHeader('Content-Type', 'text/html');
            $response->setBody($body);
        }
    }
}
