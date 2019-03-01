<?php

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Convert;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config;
use SilverStripe\Control\Controller;

class RatingApiController extends ApiController
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

    public function index(HTTPRequest $request)
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
}
