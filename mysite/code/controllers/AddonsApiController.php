<?php

class AddonsApiController extends ApiController
{
    private static $allowed_actions = [
        'index',
    ];

    /**
     * Set the default cache lifetime in seconds. Only used outside of "dev" environments.
     *
     * Set to 1 week
     *
     * @config
     * @var int
     */
    private static $cache_age = 86400;

    public function index(SS_HTTPRequest $request)
    {

        $after = (int)$request->getVar('after');
        $limit = 50;

        // Sort by ID. This will keep pagination consisent even if new records are added
        // Pagination is based on records greater than a given ID to ensure this is true
        // if records are deleted
        $addons = Addon::get()->sort('ID');
        $pageAddons = $addons->filter('ID:greaterthan', $after)->limit($limit)->toArray();

        if ($pageAddons) {

            $converter = Injector::inst()->get('AddonToArray');
            $apiAddons = array_map(
                function ($addon) use ($converter) {
                    return $converter->convert($addon);
                },
                $pageAddons
            );

            $maxID = max(array_map(
                function ($addon) {
                    return $addon->ID;
                },
                $pageAddons
            ));

            $result = [
                'success' => true,
                'addons' => $apiAddons
            ];

            if ($addons->filter('ID:greaterthan', $maxID)->count() > 0) {
                $result['next'] = Director::absoluteURL(HTTP::setGetVar('after', $maxID));
            }

        } else {
            $result = [
                'success' => false,
                'message' => 'No addons found',
            ];
        }



        return $this->formatResponse($result);
    }
}
