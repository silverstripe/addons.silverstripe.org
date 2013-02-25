<?php

use Composer\Package\AliasPackage;
use Composer\Package\CompletePackage;
use Guzzle\Http\Exception\ClientErrorResponseException;

/**
 * Updates all extensions from Packagist.
 */
class ExtensionUpdater {

	/**
	 * @var PackagistService
	 */
	private $packagist;

	public function __construct(PackagistService $packagist) {
		$this->packagist = $packagist;
	}

	/**
	 * Updates all extensions.
	 */
	public function update() {
		foreach ($this->packagist->getGroupedPackages() as $name => $versions) {
			$ext = ExtensionPackage::get()->filter('Name', $name)->first();

			if (!$ext) {
				$ext = new ExtensionPackage();
				$ext->Name = $name;
				$ext->write();
			}

			usort($versions, function ($a, $b) {
				return version_compare($a->getVersion(), $b->getVersion());
			});

			$this->updateExtension($ext, array_filter($versions, function ($version) {
				return !($version instanceof AliasPackage);
			}));
		}
	}

	private function updateExtension(ExtensionPackage $ext, array $versions) {
		DB::getConn()->transactionStart();

		if (!$ext->VendorID) {
			$vendor = ExtensionVendor::get()->filter('Name', $ext->getVendorName())->first();

			if (!$vendor) {
				$vendor = new ExtensionVendor();
				$vendor->Name = $ext->getVendorName();
				$vendor->write();
			}

			$ext->VendorID = $vendor->ID;
		}

		try {
			$details = $this->packagist->getPackageDetails($ext->Name);
			$details = $details['package'];

			$ext->Type = $details['type'];
			$ext->Description = $details['description'];
			$ext->Repository = $details['repository'];

			if (isset($details['downloads']['total']) && is_int($details['downloads']['total'])) {
				$ext->Downloads = $details['downloads']['total'];
			}
		} catch (ClientErrorResponseException $e) {}

		foreach ($versions as $version) {
			$this->updateVersion($ext, $version);
		}

		$ext->LastUpdated = time();
		$ext->write();

		DB::getConn()->transactionEnd();
	}

	private function updateVersion(ExtensionPackage $ext, CompletePackage $package) {
		$version = $ext->Versions()->filter('Version', $package->getVersion())->first();

		if (!$version) {
			$version = new ExtensionVersion();
		}

		$version->Name = $package->getName();
		$version->Type = $package->getType();
		$version->Description = $package->getDescription();

		$keywords = $package->getKeywords() ?: array();
		$existing = $ext->Keywords->getValue() ?: array();

		foreach ($keywords as $keyword) {
			if (!in_array($keyword, $existing)) {
				$existing[] = $keyword;
			}
		}

		$version->Keywords->setValue($keywords);
		$ext->Keywords->setValue($existing);

		$version->Version = $package->getVersion();
		$version->PrettyVersion = $package->getPrettyVersion();
		$version->Alias = $package->getAlias();
		$version->PrettyAlias = $package->getPrettyAlias();
		$version->Development = $package->isDev();

		$version->SourceType = $package->getSourceType();
		$version->SourceUrl = $package->getSourceUrl();
		$version->SourceReference = $package->getSourceReference();

		$version->DistType = $package->getDistType();
		$version->DistUrl = $package->getDistUrl();
		$version->DistReference = $package->getDistReference();
		$version->DistChecksum = $package->getDistSha1Checksum();

		$version->Extra = $package->getExtra();
		$version->Homepage = $package->getHomepage();
		$version->License = $package->getLicense();
		$version->Support = $package->getSupport();

		$ext->Versions()->add($version);

		$this->updateLinks($version, $package);
		$this->updateAuthors($version, $package);
	}

	private function updateLinks(ExtensionVersion $version, CompletePackage $package) {
		$getLink = function ($name, $type) use ($version) {
			$link = $version->Links()->filter('Name', $name)->filter('Type', $type)->first();

			if (!$link) {
				$link = new ExtensionLink();
				$link->Name = $name;
				$link->Type = $type;
			}

			return $link;
		};

		$types = array(
			'require' => 'getRequires',
			'require-dev' => 'getDevRequires',
			'provide' => 'getProvides',
			'conflict' => 'getConflicts',
			'replace' => 'getReplaces'
		);

		foreach ($types as $type => $method) {
			if ($linked = $package->$method()) foreach ($linked as $link) {
				/** @var $link \Composer\Package\Link */
				$name = $link->getTarget();
				$ext = ExtensionPackage::get()->filter('Name', $name)->first();

				$local = $getLink($name, $type);
				$local->Constraint = $link->getPrettyConstraint();

				if ($ext) {
					$local->TargetID = $ext->ID;
				}

				$version->Links()->add($local);
			}
		}

		$suggested = $suggested = $package->getSuggests();

		if ($suggested) foreach ($suggested as $package => $description) {
			$link = $getLink($package, 'suggest');
			$link->Description = $description;

			$version->Links()->add($link);
		}
	}

	private function updateAuthors(ExtensionVersion $version, CompletePackage $package) {
		if ($package->getAuthors()) foreach ($package->getAuthors() as $details) {
			$author = null;

			if (empty($details['name']) && empty($details['email'])) {
				continue;
			}

			if (!empty($details['email'])) {
				$author = ExtensionAuthor::get()->filter('Email', $details['email'])->first();
			}

			if (!$author && !empty($details['homepage'])) {
				$author = ExtensionAuthor::get()
					->filter('Name', $details['name'])
					->filter('Homepage', $details['homepage'])
					->first();
			}

			if (!$author && !empty($details['name'])) {
				$author = ExtensionAuthor::get()
					->filter('Name', $details['name'])
					->filter('Versions.Extension.Name', $package->getName())
					->first();
			}

			if (!$author) {
				$author = new ExtensionAuthor();
			}

			if(isset($details['name'])) $author->Name = $details['name'];
			if(isset($details['email'])) $author->Email = $details['email'];
			if(isset($details['homepage'])) $author->Homepage = $details['homepage'];
			if(isset($details['role'])) $author->Role = $details['role'];

			$version->Authors()->add($author->write());
		}
	}

}
