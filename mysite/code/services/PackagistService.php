<?php

use Composer\Factory;
use Composer\IO\NullIO;
use Composer\Package\Loader\ArrayLoader;
use Composer\Repository\ComposerRepository;
use Guzzle\Http\Client;

/**
 * Interacts with Packagist to retrieve package listings and details.
 */
class PackagistService {

	const PACKAGIST_URL = 'https://packagist.org';

	/**
	 * @var Composer\Composer
	 */
	private $composer;

	/**
	 * @var Composer\Repository\RepositoryInterface
	 */
	private $repository;

	public function __construct() {
		$this->composer = Factory::create(new NullIO());
		$this->client = new Client(self::PACKAGIST_URL);
	}

	/**
	 * @return Composer\Composer
	 */
	public function getComposer() {
		return $this->composer;
	}

	/**
	 * Gets all SilverStripe packages.
	 *
	 * @return \Composer\Package\PackageInterface[]
	 */
	public function getPackages() {
		$packages = array();
		$loader = new ArrayLoader();

		foreach ($this->composer->getRepositoryManager()->getRepositories() as $repo) {
			foreach ($repo->getMinimalPackages() as $info) {
				if (strpos($info['raw']['type'], 'silverstripe-') === 0) {
					$packages[] = $loader->load($info['raw']);
				}
			}
		}

		return $packages;
	}

	/**
	 * Gets all SilverStripe packages, grouped by package name.
	 *
	 * @return array
	 */
	public function getGroupedPackages() {
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
	public function getPackageDetails($name) {
		return $this->client->get("/packages/$name.json")->send()->json();
	}

	/**
	 * Gets all versions of a package by name.
	 *
	 * @param $name
	 * @return \Composer\Package\PackageInterface[]
	 */
	public function getPackageVersions($name) {
		$packages = array();
		$loader = new ArrayLoader();

		foreach ($this->composer->getRepositoryManager()->getRepositories() as $repo) {
			foreach ($repo->getMinimalPackages() as $info) {
				if ($info['name'] == $name) {
					$packages[] = $loader->load($info['raw']);
				}
			}
		}

		return $packages;
	}

}
