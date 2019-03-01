<?php

use SilverStripe\ORM\DataObject;

/**
 * Tracks any SilverStripe framework versions that are communicated in the headers of API calls (those handled by
 * controllers that extend ApiController) made to addons.silverstripe.org
 */
class ApiCallerVersions extends DataObject
{
    private static $db = [
        'Endpoint' => 'Varchar(255)',
        'Version' => 'Varchar(100)',
    ];

    private static $indexes = [
        'Version' => true,
    ];
}
