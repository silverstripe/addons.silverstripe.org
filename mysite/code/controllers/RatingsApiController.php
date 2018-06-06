<?php

/**
 * The ratings API controller provides access to bulk ratings information for a list of addons provided
 * via the `addons` query string argument, comma delimited.
 *
 * For individual (including detailed) rating metrics, use the RatingApiController
 */
class RatingsApiController extends ApiController
{
    private static $allowed_actions = [
        'index',
    ];

    public function index(SS_HTTPRequest $request)
    {
        if (!$request->getVar('addons')) {
            return $this->formatResponse([
                'success' => false,
                'message' => 'Missing or incomplete module names',
            ]);
        }

        $addonNames = $request->getVar('addons');
        /** @var Addon[] $addons */
        $addons = Addon::get()
            ->filter(['Name' => explode(',', $request->getVar('addons'))])
            ->map('Name', 'Rating');

        return $this->formatResponse([
            'success' => true,
            'ratings' => $addons->toArray()
        ]);
    }
}
