<?php

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Convert;
use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config;


/**
 * Abstract controller for actions that provide an API endpoint.
 */
abstract class ApiController extends Controller
{
    /**
     * Set the default cache lifetime in seconds. Only used outside of "dev" environments.
     *
     * @config
     * @var int
     */
    private static $cache_age = 10800;

    /**
     * Given a result payload, format as a JSON response and return
     *
     * @param array $data
     * @return SS_HTTPResponse
     */
    protected function formatResponse(array $data)
    {
        $response = new HTTPResponse();
        $response
            ->addHeader('Content-Type', 'application/json')
            ->setBody(Convert::raw2json($data, JSON_PRETTY_PRINT));

        // Don't cache anything in dev mode
        if (Director::get_environment_type() !== 'dev') {
            // Only cache failure messages for one minute, otherwise use the configured cache age
            $cacheAge = empty($data['success']) ? 60 : Config::inst()->get(static::class, 'cache_age');
            HTTPCacheControl::singleton()
                ->enableCache()
                ->setMaxAge($cacheAge);
        }

        return $response;
    }

    /**
     * Overrides this method only to prepend capturing any provided framework version header
     *
     * @inheritDoc
     */
    protected function handleAction($request, $action)
    {
        $frameworkVersionHeader = $request->getHeader('Silverstripe-Framework-Version');

        if ($frameworkVersionHeader) {
            ApiCallerVersions::create([
                'Endpoint' => $request->getURL(),
                'Version' => $frameworkVersionHeader,
            ])->write();
        }

        return parent::handleAction($request, $action);
    }
}
