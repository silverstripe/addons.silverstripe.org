<?php

use Composer\Factory;
use Composer\IO\NullIO;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

/**
 * Interacts with Packagist to retrieve package listings and details.
 */
class PackagistService
{

    /**
     * @var Composer\Composer
     */
    private $composer;

    /**
     * @var Composer\Repository\RepositoryInterface
     */
    private $repository;

    public function __construct()
    {
        chdir(BASE_PATH);
        $this->composer = Factory::create(new NullIO());

        $this->client = new Packagist\Api\Client(
            new Client([
                'headers' => [
                    'user-agent' => 'addons.silverstripe.org'
                ]
            ])
        );
    }

    /**
     * @return Composer\Composer
     */
    public function getComposer()
    {
        return $this->composer;
    }

    /**
     * Gets all SilverStripe packages. Only contains versions, not other metadata, for performance reasons.
     * See https://packagist.org/apidoc for rationale.
     *
     * @param string[] $limitAddons If specified, can be a list of addons to restrict the return to
     * @return \Generator Returning Packagist\Api\Result\Package[]
     */
    public function getPackages(array $limitAddons = [])
    {
        // Look up by package type
        $addonTypes = [
            'silverstripe-module',
            'silverstripe-vendormodule',
            'silverstripe-theme'
        ];

        // Gracefully handles API errors and rate limiting.
        if ($limitAddons) {
            foreach ($limitAddons as $name) {
                // output to give feedback when running
                echo sprintf("PackagistService: Retrieved %s", $name) . PHP_EOL;
                $package = $this->getComposerPackage($name);
                if (!$package) {
                    continue;
                }
                yield $package;
            }
        } else {
            foreach ($addonTypes as $type) {
                $repositoriesNames = $this->client->all(['type' => $type]);
                foreach ($repositoriesNames as $name) {
                    // output to give feedback when running
                    echo sprintf("PackagistService: Retrieved %s", $name) . PHP_EOL;
                    $package = $this->getComposerPackage($name);
                    if (!$package) {
                        continue;
                    }
                    yield $package;
                }
            }
        }
    }

    protected function getComposerPackage($name)
    {
        $packages = null;

        try {
            $packages = $this->client->getComposer($name);
        } catch (BadResponseException $e) {
            // Abandoned packages cause 404s, and we occasionally get rate limited.
            // Neither of those should cause a hard abort.
            echo sprintf('PackagistService: Failed to retrieve (%s)', $e->getMessage()) . PHP_EOL;
            return null;
        }

        if (!isset($packages[$name])) {
            echo sprintf('PackagistService: Not in packages array!', $name) . PHP_EOL;
            Debug::dump($packages);
            return null;
        }

        return $packages[$name];
    }

    /**
     * Gets all SilverStripe packages, grouped by package name.
     *
     * @return array
     */
    public function getGroupedPackages()
    {
        $grouped = array();

        foreach ($this->getPackages() as $package) {
            $name = $package->getName();

            if (array_key_exists($name, $grouped)) {
                $grouped[$name][] = $package;
            } else {
                $grouped[$name] = array($package);
            }
        }

        return $grouped;
    }

    /**
     * Gets detailed information for a package.
     *
     * @param string $name
     * @return array
     */
    public function getPackageDetails($name)
    {
        return $this->client->get($name);
    }

    /**
     * Gets all versions of a package by name.
     *
     * @param $name
     * @return \Composer\Package\PackageInterface[]
     */
    public function getPackageVersions($name)
    {
        $versions = array();
        $package = $this->client->get($name);

        foreach ($package->getVersions() as $repo) {
            $versions[] = $repo;
        }

        return $versions;
    }
}
