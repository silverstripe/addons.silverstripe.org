<?php
/**
 * Tests for the AddonBuilder
 */
class AddonBuilderTest extends SapphireTest
{
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
        $builder = new AddonBuilder(new PackagistService);
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
}
