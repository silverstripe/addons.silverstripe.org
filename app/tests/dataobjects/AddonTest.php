<?php

use Heyday\Elastica\Searchable;
use SilverStripe\Dev\SapphireTest;

class AddonTest extends SapphireTest
{
    protected $usesDatabase = true;

    protected static $illegal_extensions = [
        Addon::class => [
            Searchable::class,
        ],
    ];

    /**
     * @dataProvider provideVersions
     */
    public function testDefaultVersion($expected, $versions)
    {
        $addon = new Addon();
        $addon->write();

        foreach ($versions as $version) {
            $versionObj = new AddonVersion([
                'Version' => $version
            ]);
            $versionObj->write();
            $addon->Versions()->add($versionObj);
        }

        $this->assertEquals($expected, $addon->DefaultVersion()->Version);
    }

    public function provideVersions()
    {
        return [
            'Master only' => [
                '9999999-dev',
                [
                    '9999999-dev'
                ]
            ],
            'Release branches only' => [
                '4.7.9999999.9999999-dev',
                [
                    '4.7.9999999.9999999-dev',
                    '4.6.0',
                    '4.6.9999999.9999999-dev',
                ]
            ],
            'Releases only' => [
                '4.6.0',
                [
                    '4.6.0',
                    '3.7.0',
                ]
            ],
            'With dev branches' => [
                '9999999-dev',
                [
                    '4.7.9999999.9999999-dev',
                    '4.6.9999999.9999999-dev',
                    '9999999-dev',
                    '4.6.0.0-RC1',
                    'dev-mypull'
                ]
            ]
        ];
    }
}
