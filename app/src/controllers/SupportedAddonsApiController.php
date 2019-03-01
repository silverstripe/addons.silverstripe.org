<?php

class SupportedAddonsApiController extends ApiController
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
    private static $cache_age = 604800;

    public function index()
    {
        $supportedAddons = Addon::get()->filter('Supported', true)->column('Name');

        $result = ['success' => true, 'addons' => $supportedAddons];

        return $this->formatResponse($result);
    }
}
