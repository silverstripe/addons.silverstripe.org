<?php

use Heyday\Elastica\Searchable;
use SilverStripe\Dev\FunctionalTest;

/**
 * @mixin PHPUnit_Framework_TestCase
 */
class SupportedAddonsApiControllerTest extends FunctionalTest
{
    protected static $fixture_file = 'SupportedAddonsApiControllerTest.yml';

    protected static $illegal_extensions = [
        Addon::class => [
            Searchable::class,
        ],
    ];

    public function testOnlySupportedAddonsAreReturned()
    {
        $response = $this->get('api/supported-addons');

        $this->assertJson($response->getBody());

        $result = json_decode($response->getBody());
        $this->assertTrue($result->success);
        $this->assertContains('foo/supported-bar', $result->addons);
        $this->assertNotContains('foo/unsupported-bar', $result->addons);
    }
}
