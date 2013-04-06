<?php

use Composer\Package\AliasPackage;
use Composer\Package\CompletePackage;
use Composer\Package\LinkConstraint\VersionConstraint;
use Guzzle\Http\Exception\ClientErrorResponseException;
use SilverStripe\Elastica\ElasticaService;

/**
 * Updates all add-ons from Packagist.
 */
class AddonUpdater {

	/**
	 * @var PackagistService
	 */
	private $packagist;

	/**
	 * @var SilverStripe\Elastica\ElasticaService
	 */
	private $elastica;

	/**
	 * @var ResqueService
	 */
	private $resque;

	/**
	 * @var SilverStripeVersion[]
	 */
	private $silverstripes = array();

	public function __construct(PackagistService $packagist, ElasticaService $elastica, ResqueService $resque) {
		$this->packagist = $packagist;
		$this->elastica = $elastica;
		$this->resque = $resque;
	}

	/**
	 * Updates all add-ons.
	 */
	public function update() {
		foreach (SilverStripeVersion::get() as $version) {
			$this->silverstripes[$version->ID] = $version->getConstraint();
		}

		$this->elastica->startBulkIndex();

		foreach ($this->packagist->getGroupedPackages() as $name => $versions) {
			$addon = Addon::get()->filter('Name', $name)->first();

			if (!$addon) {
				$addon = new Addon();
				$addon->Name = $name;
			}

			usort($versions, function ($a, $b) {
				return version_compare($a->getVersion(), $b->getVersion());
			});

			$this->updateAddon($addon, array_filter($versions, function ($version) {
				return !($version instanceof AliasPackage);
			}));
		}

		$this->elastica->endBulkIndex();
	}

	private function updateAddon(Addon $addon, array $versions) {
		if (!$addon->VendorID) {
			$vendor = AddonVendor::get()->filter('Name', $addon->VendorName())->first();

			if (!$vendor) {
				$vendor = new AddonVendor();
				$vendor->Name = $addon->VendorName();
				$vendor->write();
			}

			$addon->VendorID = $vendor->ID;
		}

		try {
			$details = $this->packagist->getPackageDetails($addon->Name);
			$details = $details['package'];

			$addon->Type = str_replace('silverstripe-', '', $details['type']);
			$addon->Description = $details['description'];
			$addon->Released = $details['time'];
			$addon->Repository = $details['repository'];

			if (isset($details['downloads']['total']) && is_int($details['downloads']['total'])) {
				$addon->Downloads = $details['downloads']['total'];
			}
		} catch (ClientErrorResponseException $e) {}

		foreach ($versions as $version) {
			$this->updateVersion($addon, $version);
		}

		// If there is no build, then queue one up if the add-on requires
		// one.
		if (!$addon->BuildQueued) {
			if (!$addon->BuiltAt) {
				$this->resque->queue('first_build', 'BuildAddonJob', array('id' => $addon->ID));
				$addon->BuildQueued = true;
			} else {
				$built = (int) $addon->obj('BuiltAt')->format('U');

				foreach ($versions as $version) {
					if ($version->getReleaseDate()->getTimestamp() > $built) {
						$this->resque->queue('update', 'BuildAddonJob', array('id' => $addon->ID));
						$addon->BuildQueued = true;

						break;
					}
				}
			}
		}

		$addon->LastUpdated = time();
		$addon->write();
	}

	private function updateVersion(Addon $addon, CompletePackage $package) {
		$version = null;

		if ($addon->isInDB()) {
			$version = $addon->Versions()->filter('Version', $package->getVersion())->first();
		}

		if (!$version) {
			$version = new AddonVersion();
		}

		$version->Name = $package->getName();
		$version->Type = str_replace('silverstripe-', '', $package->getType());
		$version->Description = $package->getDescription();
		$version->Released = $package->getReleaseDate()->getTimestamp();

		$keywords = $package->getKeywords();

		if ($keywords) {
			foreach ($keywords as $keyword) {
				$keyword = AddonKeyword::get_by_name($keyword);

				$addon->Keywords()->add($keyword);
				$version->Keywords()->add($keyword);
			}
		}

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

		$addon->Versions()->add($version);

		$this->updateLinks($version, $package);
		$this->updateCompatibility($addon, $version, $package);
		$this->updateAuthors($version, $package);
	}

	private function updateLinks(AddonVersion $version, CompletePackage $package) {
		$getLink = function ($name, $type) use ($version) {
			$link = null;

			if ($version->isInDB()) {
				$link = $version->Links()->filter('Name', $name)->filter('Type', $type)->first();
			}

			if (!$link) {
				$link = new AddonLink();
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
				$addon = Addon::get()->filter('Name', $name)->first();

				$local = $getLink($name, $type);
				$local->Constraint = $link->getPrettyConstraint();

				if ($addon) {
					$local->TargetID = $addon->ID;
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

	private function updateCompatibility(Addon $addon, AddonVersion $version, CompletePackage $package) {
		$require = null;

		foreach ($package->getRequires() as $name => $link) {
			if ($name == 'silverstripe/framework') {
				$require = $link;
				break;
			}

			if ($name == 'silverstripe/cms') {
				$require = $link;
			}
		}

		if (!$require) {
			return;
		}

		foreach ($this->silverstripes as $id => $link) {
			if ($require->getConstraint()->matches($link)) {
				$addon->CompatibleVersions()->add($id);
				$version->CompatibleVersions()->add($id);
			}
		}
	}

	private function updateAuthors(AddonVersion $version, CompletePackage $package) {
		if ($package->getAuthors()) foreach ($package->getAuthors() as $details) {
			$author = null;

			if (empty($details['name']) && empty($details['email'])) {
				continue;
			}

			if (!empty($details['email'])) {
				$author = AddonAuthor::get()->filter('Email', $details['email'])->first();
			}

			if (!$author && !empty($details['homepage'])) {
				$author = AddonAuthor::get()
					->filter('Name', $details['name'])
					->filter('Homepage', $details['homepage'])
					->first();
			}

			if (!$author && !empty($details['name'])) {
				$author = AddonAuthor::get()
					->filter('Name', $details['name'])
					->filter('Versions.Addon.Name', $package->getName())
					->first();
			}

			if (!$author) {
				$author = new AddonAuthor();
			}

			if(isset($details['name'])) $author->Name = $details['name'];
			if(isset($details['email'])) $author->Email = $details['email'];
			if(isset($details['homepage'])) $author->Homepage = $details['homepage'];
			if(isset($details['role'])) $author->Role = $details['role'];

			$version->Authors()->add($author->write());
		}
	}

}
