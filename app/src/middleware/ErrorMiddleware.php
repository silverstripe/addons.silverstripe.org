<?php

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Control\Middleware\HTTPMiddleware;

/**
 * Renders custom error pages when an error response is returned.
 */
class ErrorMiddleware implements HTTPMiddleware
{

    public function process(HTTPRequest $request, callable $delegate): HTTPResponse
    {
        /** @var HTTPResponse $response */
        $response = $delegate($request);

        if ($response->getStatusCode() == 404) {
            $controller = SiteController::create();
            $controller = $controller->customise(['Title' => 'Page Not Found']);
            $body = $controller->renderWith(['ErrorPage_404', 'ErrorPage', 'Page']);

            $response->addHeader('Content-Type', 'text/html');
            $response->setBody($body);
        }

        return $response;
    }
}
