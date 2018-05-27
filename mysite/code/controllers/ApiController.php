<?php
abstract class ApiController extends Controller
{
    /**
     * Given a result payload, format as a JSON response and return
     *
     * @param array $data
     * @return SS_HTTPResponse
     */
    protected function formatResponse(array $data)
    {
        $response = new SS_HTTPResponse();
        $response
            ->addHeader('Content-Type', 'application/json')
            ->setBody(Convert::raw2json($data, JSON_PRETTY_PRINT));

        // Don't cache anything in dev mode
        if (Director::get_environment_type() !== 'dev') {
            // Only cache failure messages for one minute, otherwise use the configured cache age
            $cacheAge = empty($data['success']) ? 60 : Config::inst()->get(__CLASS__, 'cache_age');
            $response->addHeader('Cache-Control', 'max-age=' . $cacheAge);
        }

        return $response;
    }
}
