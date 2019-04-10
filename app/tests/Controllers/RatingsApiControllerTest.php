<?php

use Heyday\Elastica\Searchable;
use SilverStripe\Dev\FunctionalTest;

/**
 * @mixin PHPUnit_Framework_TestCase
 */
class RatingsApiControllerTest extends FunctionalTest
{
    protected static $fixture_file = 'RatingApiControllerTest.yml';

    protected static $illegal_extensions = [
        Addon::class => [
            Searchable::class,
        ],
    ];

    public function testGetRatingsForMultipleModules()
    {
        $response = $this->get('api/ratings?addons=foo/bar,far/baz');

        $this->assertJson($response->getBody());

        $result = json_decode($response->getBody());
        $this->assertTrue($result->success);
        $this->assertNotEmpty($result->ratings);
        $this->assertEquals(53, $result->ratings->{'foo/bar'});
        $this->assertEquals(83, $result->ratings->{'far/baz'});
    }

    public function testMissingAddonsAreOmittedFromResults()
    {
        $response = $this->get('api/ratings?addons=silverstripe/imaginarymodule');

        $this->assertJson($response->getBody());

        $result = json_decode($response->getBody());
        $this->assertTrue($result->success);
        $this->assertEmpty($result->ratings);
    }
}
