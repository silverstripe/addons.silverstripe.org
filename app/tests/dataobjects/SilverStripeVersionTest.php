<?php

use SilverStripe\Dev\SapphireTest;

/**
 * Tests for the SilverStripeVersion model
 *
 * @package mysite
 */
class SilverStripeVersionTest extends SapphireTest
{
    /**
     * @var SilverStripeVersion
     */
    protected $version;

    /**
     * Create a test version
     *
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->version = new SilverStripeVersion(
            [
                'Major' => 4,
                'Minor' => 1
            ]
        );
    }

    /**
     * Ensure that a simple string is returned for the version, which can be used for comparing composer
     * constraints against for validity
     */
    public function testGetConstraint()
    {
        $this->assertSame('4.1.0', (string) $this->version);
    }

    /**
     * Test various composer constraints against the version to see if this SilverStripe version would apply to
     * the module
     *
     * @dataProvider constraintProvider
     * @param string $constraint
     * @param bool   $expected
     */
    public function testGetConstraintValidity($constraint, $expected)
    {
        $this->assertSame($expected, $this->version->getConstraintValidity($constraint));
    }

    /**
     * @return array[]
     */
    public function constraintProvider()
    {
        return [
            ['1.0.0', false],
            ['~3.2', false],
            ['^3.2', false],
            ['>=3.2', true], // warning!!
            ['>=3.2,<4', false],
            ['4.0.0', false],
            ['~4.0.0', false],
            ['^4.0', true],
            ['>=4.0.0', true],
            ['~4.2.3', false],
            ['<5.0.0', true],
            ['^5.0.0', false],
            ['^3.5|^4.0', true],
            ['^3.5|^5.0', false]
        ];
    }
}
