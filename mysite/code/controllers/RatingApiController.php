<?php

class RatingApiController extends Controller
{
    private static $url_handlers = [
        'GET $Vendor/$Package' => 'index',
    ];

    private static $allowed_actions = [
        'index',
    ];

    /**
     * Set the default cache lifetime in seconds. Only used outside of "dev" environments.
     *
     * @config
     * @var int
     */
    private static $cache_age = 10800;

    public function index(SS_HTTPRequest $request)
    {
        $params = $this->getURLParams();
        if (empty($params['Vendor']) || empty($params['Package'])) {
            return $this->formatResponse([
                'success' => false,
                'message' => 'Missing or incomplete module name',
            ]);
        }

        $package = sprintf('%s/%s', $params['Vendor'], $params['Package']);
        $addon = $this->getAddon($package);
        if (!$addon) {
            return $this->formatResponse([
                'success' => false,
                'message' => 'Module could not be found',
            ]);
        }

        $result = ['success' => true] + $this->getAddonMetrics($addon, $request->getVar('detailed') !== null);

        return $this->formatResponse($result);
    }

    /**
     * Given a package name, return the Addon model for it
     *
     * @param string $packageName
     * @return Addon
     */
    protected function getAddon($packageName)
    {
        return Addon::get()->filter(['Name' => $packageName])->first();
    }

    /**
     * Get the module metrics that will be returned
     *
     * @param Addon $addon
     * @param boolean $detailed
     * @return array
     */
    protected function getAddonMetrics(Addon $addon, $detailed)
    {
        if (!$addon) {
            return [];
        }

        $result = ['rating' => $addon->Rating];
        if (!$detailed) {
            return $result;
        }

        // Try to decode the (JSON) rating details
        $details = Convert::json2array($addon->RatingDetails);

        if ($details) {
            $result['metrics'] = $details;
        }

        return $result;
    }

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
