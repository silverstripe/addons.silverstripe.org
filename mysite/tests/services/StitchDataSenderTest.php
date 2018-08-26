<?php


use SilverStripe\Elastica\Searchable;

/**
 * Tests for StitchDataSender
 *
 * @mixin PHPUnit_Framework_TestCase
 */
class StitchDataSenderTest extends SapphireTest
{

    protected static $fixture_file = 'mysite/tests/services/testAddon.yml';

    protected $illegalExtensions = [
        'Addon' => [
            Searchable::class,
        ]
    ];

    public function testAddonToJson()
    {
        $s = new StitchDataSender();
        /** @var Addon $addon */
        $addon = $this->objFromFixture('Addon', 'addon_a');
        $result = $s->addonToJson($addon);


        $this->assertEquals('sminnee/test-package', $result['Name']);
        $this->assertInstanceof('Datetime', $result['Released']);
        $this->assertEquals([ 'Name' => 'good_code_coverage', 'Value' => 0 ], $result['RatingDetails'][0]);
        $this->assertEquals(2, sizeof($result['Versions']));
        $this->assertEquals('1.4.2.0', $result['Versions'][0]['Version']);
        $this->assertEquals(
            [[ 'Version' => '3.6', 'Major' => 3, 'Minor' => 6 ]],
            $result['Versions'][0]['CompatibleVersions']
        );
        $this->assertEquals(
            [[
                'Name' => 'Uncle Cheese',
                'Email' => 'unclecheese@leftandmain.com',
                'Homepage' => 'http://leftandmain.com',
            ]],
            $result['Versions'][0]['Authors']
        );
    }
}
