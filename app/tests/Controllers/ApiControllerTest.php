<?php

use Heyday\Elastica\Searchable;
use SilverStripe\Dev\FunctionalTest;

/**
 * @mixin PHPUnit_Framework_TestCase
 */
class ApiControllerTest extends FunctionalTest
{
    protected $usesDatabase = true;

    protected static $illegal_extensions = [
        Addon::class => [
            Searchable::class,
        ],
    ];

    public function testFrameworkVersionSentInHeadersIsCollected()
    {
        $this->get('api/supported-addons', null, [
            'Silverstripe-Framework-Version' => '1.2.3',
        ]);

        $metrics = ApiCallerVersions::get();

        $this->assertCount(1, $metrics);
        $this->assertSame('1.2.3', $metrics->first()->Version);
    }
}
