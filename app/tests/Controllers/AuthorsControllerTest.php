<?php

use SilverStripe\Dev\SapphireTest;

class AuthorsControllerTest extends SapphireTest
{
    protected static $fixture_file = 'AuthorsControllerTest.yml';

    public function testGithubContributorsAreExcludedInAuthorsIndex()
    {
        $controller = new AuthorsController();

        $this->assertDOSContains(
            [
                ['Name' => 'Anna Green'],
                ['Name' => 'Stephen McKenna'],
                ['Name' => 'Kyra South'],
                ['Name' => 'Frank Smith'],
            ],
            $controller->Authors()
        );

        $names = $controller->Authors()->map('ID', 'Name')->toArray();

        $this->assertNotContains('GitHub contributors', $names);
        $this->assertNotContains('Github contributors', $names);
        $this->assertNotContains('Github Contributors', $names);
        $this->assertNotContains('GitHub Contributors', $names);
    }
}
