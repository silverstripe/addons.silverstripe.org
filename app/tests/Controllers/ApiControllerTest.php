<?php

use SilverStripe\Dev\FunctionalTest;

/**
 * @mixin PHPUnit_Framework_TestCase
 */
class ApiControllerTest extends FunctionalTest
{
    protected $usesDatabase = true;

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
