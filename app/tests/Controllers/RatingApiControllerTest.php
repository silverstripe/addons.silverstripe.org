<?php

use SilverStripe\Dev\FunctionalTest;

/**
 * @mixin PHPUnit_Framework_TestCase
 */
class RatingApiControllerTest extends FunctionalTest
{
    protected static $fixture_file = 'RatingApiControllerTest.yml';

    public function testErrorWhenMissingParams()
    {
        $response = $this->get('api/rating');

        $this->assertJson($response->getBody());

        $result = json_decode($response->getBody());
        $this->assertFalse($result->success);
        $this->assertContains('Missing', $result->message);
    }

    public function testErrorWhenModuleNotFound()
    {
        $response = $this->get('api/rating/bar/baz');

        $this->assertJson($response->getBody());

        $result = json_decode($response->getBody());
        $this->assertFalse($result->success);
        $this->assertContains('not be found', $result->message);
    }

    public function testResponseIncludesRating()
    {
        $response = $this->get('api/rating/foo/bar');

        $this->assertJson($response->getBody());

        $result = json_decode($response->getBody());
        $this->assertTrue($result->success);
        $this->assertSame(53, $result->rating);
    }

    public function testDetailedResponse()
    {
        $response = $this->get('api/rating/foo/bar?detailed');

        $this->assertJson($response->getBody());

        $result = json_decode($response->getBody());
        $this->assertTrue($result->success);
        $this->assertSame(3, $result->metrics->coverage);
    }
}
