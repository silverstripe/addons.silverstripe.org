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

	const SCREENSHOTS_DIR = 'screenshots';

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
			$this->buildScreenshots($extension, $package, $path);
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

	private function buildScreenshots(ExtensionPackage $extension, PackageInterface $package, $path) {
		$extra = $package->getExtra();
		$screenshots = array();
		$target = self::SCREENSHOTS_DIR . '/' . $extension->Name;

		if (isset($extra['screenshots'])) {
			$screenshots = (array) $extra['screenshots'];
		} elseif (isset($extra['screenshot'])) {
			$screenshots = (array) $extra['screenshot'];
		}

		// Delete existing screenshots.
		foreach ($extension->Screenshots() as $screenshot) {
			$screenshot->delete();
		}

		$extension->Screenshots()->removeAll();

		foreach ($screenshots as $screenshot) {
			if (!is_string($screenshot)) {
				continue;
			}

			$scheme = parse_url($screenshot, PHP_URL_SCHEME);

			// Handle absolute image URLs.
			if ($scheme == 'http' || $scheme == 'https') {
				$temp = TEMP_FOLDER . '/' . md5($screenshot);

				if (!copy($screenshot, $temp)) {
					continue;
				}

				$data = array(
					'name' => basename($screenshot),
					'size' => filesize($temp),
					'tmp_name' => $temp,
					'error' => 0
				);
			}
			// Handle images that are included in the repository.
			else {
				$source = $path . '/' . ltrim($screenshot, '/');

				// Prevent directory traversal.
				if ($source != realpath($source)) {
					continue;
				}

				if (!file_exists($source)) {
					continue;
				}

				$data = array(
					'name' => basename($source),
					'size' => filesize($source),
					'tmp_name' => $source,
					'error' => 0
				);
			}

			$upload = new Upload();
			$upload->setValidator(new ExtensionBuilderScreenshotValidator());
			$upload->load($data, $target);

			$extension->Screenshots()->add($upload->getFile());
		}
	}

}
