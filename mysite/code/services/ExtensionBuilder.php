<?php

use Composer\Package\Loader\ArrayLoader;
use Composer\Package\Loader\JsonLoader;
use Composer\Package\PackageInterface;
use dflydev\markdown\MarkdownParser;

/**
 * Downloads an extension and builds more details information about it.
 */
class ExtensionBuilder {

	const EXTENSIONS_DIR = 'extensions';

	private $packagist;

	public function __construct(PackagistService $packagist) {
		$this->packagist = $packagist;
	}

	public function build(ExtensionPackage $extension) {
		$composer = $this->packagist->getComposer();
		$downloader = $composer->getDownloadManager();
		$packages = $this->packagist->getPackageVersions($extension->Name);
		$time = time();

		if (!$packages) {
			throw new Exception('Could not find corresponding Packagist versions');
		}

		// Get the latest local and packagist version pair.
		$version = $extension->Versions()->filter('Development', true)->first();

		foreach ($packages as $package) {
			if ($package->getVersion() != $version->Version) {
				continue;
			}

			$path = implode('/', array(
				TEMP_FOLDER, self::EXTENSIONS_DIR, $extension->Name
			));

			$this->download($package, $path);
			$this->buildReadme($extension, $path);
		}

		$extension->LastBuilt = $time;
		$extension->write();
	}

	protected function download(PackageInterface $package, $path) {
		$this->packagist
			->getComposer()
			->getDownloadManager()
			->download($package, $path);
	}

	private function buildReadme(ExtensionPackage $extension, $path) {
		$candidates = array(
			'README.md',
			'README.markdown',
			'README.mdown',
			'docs/en/index.md'
		);

		foreach ($candidates as $candidate) {
			$lower = strtolower($candidate);
			$paths = array("$path/$candidate", "$path/$lower");

			foreach ($paths as $path) {
				if (!file_exists($path)) {
					return;
				}

				$parser = new MarkdownParser();
				$readme = $parser->transformMarkdown(file_get_contents($path));

				$purifier = new HTMLPurifier();
				$readme = $purifier->purify($readme);

				$extension->Readme = $readme;
				return;
			}
		}
	}

}
