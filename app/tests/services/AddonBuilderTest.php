<?php

use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\SapphireTest;

/**
 * Tests for the AddonBuilder
 *
 * @package mysite
 */
class AddonBuilderTest extends SapphireTest
{
    /**
     * @var AddonBuilder
     */
    protected $builder;

    /**
     * Get the test subject
     */
    public function setUp(): void
    {
        parent::setUp();

        // Partially mocked as we don't care about the PackagistService at this point
        $this->builder = $this->getMockBuilder('AddonBuilder')
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Test that a GitHub repository can be identified, and have its context returned if it matches
     *
     * @param string $input
     * @param string|false $expected
     * @dataProvider repositoryContextProvider
     */
    public function testGetGitHubContext($input, $expected)
    {
        $addon = new Addon(array('Repository' => $input));
        $builder = Injector::inst()->create('AddonBuilder');
        $result = $builder->getGitHubContext($addon);
        $this->assertSame($expected, $result);
    }

    public function repositoryContextProvider()
    {
        return array(
            array('https://github.com/silverstripe/addons.org.git', 'silverstripe/addons.org'),
            array('http://github.com/silverstripe/addons.org.git', 'silverstripe/addons.org'),
            array('https://github.com/silverstripe/sspak.git', 'silverstripe/sspak'),
            array('http://github.com/silverstripe/sspak.git', 'silverstripe/sspak')
        );
    }

    /**
     * Test that the GitHub-ness of an addon's repository can be correctly established
     *
     * @param string $repository
     * @param bool   $expected
     * @covers ::hasGitHubRepository
     * @dataProvider hasGitHubProvider
     */
    public function testHasGitHubRepository($repository, $expected)
    {
        $addon = Addon::create();
        $addon->Repository = $repository;

        $result = $this->builder->hasGitHubRepository($addon);
        $this->assertSame($expected, $result);
    }

    /**
     * @return array
     */
    public function hasGitHubProvider()
    {
        return array(
            array(
                'https://github.com/silverstripe/silverstripe-framework',
                true
            ),
            array(
                'https://bitbucket.org/some/otherrepo',
                false
            )
        );
    }

    /**
     * Test that we can determine the differece between a relative-ish URI and one that isn't, so we know
     * when to insert the GitHub repository URL into the mix.
     *
     * @param string $uri
     * @param bool   $expected
     * @covers ::isRelativeUri
     * @dataProvider uriProvider()
     */
    public function testIsRelativeUri($uri, $expected)
    {
        $this->assertSame($expected, $this->builder->isRelativeUri($uri));
    }

    /**
     * @return array
     */
    public function uriProvider()
    {
        return array(
            array('/add-ons/silverstripe/sapphire#-preview', false),
            array('#installation-with-composer', false),
            array('_resources/example.png?raw=true', true),
            array('add-ons/silverstripe/sapphire#usage', true),
            array('//add-ons/silverstripe/sapphire#usage', false),
            array('https://silverstripe.mit-license.org/', false),
            array('http://silverstripe.mit-license.org/', false)
        );
    }
}
