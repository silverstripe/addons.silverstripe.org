<?php

use Composer\Package\AliasPackage;
use Composer\Package\CompletePackage;
use Composer\Package\LinkConstraint\VersionConstraint;
use Guzzle\Http\Exception\ClientErrorResponseException;
use SilverStripe\Elastica\ElasticaService;
use Packagist\Api\Result\Package;
use Composer\Package\Version\VersionParser;

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
	 *
	 * @param Boolean Clear existing addons before updating them.
	 * Will also clear their search index, and cascade the delete for associated data.
	 */
	public function update($clear = false) {
		if($clear) {
			Addon::get()->removeAll();
			AddonAuthor::get()->removeAll();
			AddonKeyword::get()->removeAll();
			AddonLink::get()->removeAll();
			AddonVendor::get()->removeAll();
			AddonVersion::get()->removeAll();
		}

		foreach (SilverStripeVersion::get() as $version) {
			$this->silverstripes[$version->ID] = $version->getConstraint();
		}

		$this->elastica->startBulkIndex();

		foreach ($this->packagist->getPackages() as $package) {
			$name = $package->getName();
			$versions = $package->getVersions();

			$addon = Addon::get()->filter('Name', $name)->first();

			if (!$addon) {
				$addon = new Addon();
				$addon->Name = $name;
				$addon->write();
			}

			usort($versions, function ($a, $b) {
				return version_compare($a->getVersionNormalized(), $b->getVersionNormalized());
			});

			$this->updateAddon($addon, $package, $versions);
		}

		$this->elastica->endBulkIndex();
	}

	private function updateAddon(Addon $addon, Package $package, array $versions) {
		if (!$addon->VendorID) {
			$vendor = AddonVendor::get()->filter('Name', $addon->VendorName())->first();

			if (!$vendor) {
				$vendor = new AddonVendor();
				$vendor->Name = $addon->VendorName();
				$vendor->write();
			}

			$addon->VendorID = $vendor->ID;
		}

		$addon->Type = str_replace('silverstripe-', '', $package->getType());
		$addon->Description = $package->getDescription();
		$addon->Released = strtotime($package->getTime());
		$addon->Repository = $package->getRepository();
		$addon->Downloads = $package->getDownloads()->getTotal();

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
					if (strtotime($version->getTime()) > $built) {
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

	private function updateVersion(Addon $addon, Version $package) {
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
		$version->Released = strtotime($package->getTime());
		$keywords = $package->getKeywords();

		if ($keywords) {
			foreach ($keywords as $keyword) {
				$keyword = AddonKeyword::get_by_name($keyword);

				$addon->Keywords()->add($keyword);
				$version->Keywords()->add($keyword);
			}
		}

		$version->Version = $package->getVersionNormalized();
		$version->PrettyVersion = $package->getVersion();

		$stability = VersionParser::parseStability($package->getVersion());
		$isDev = $stability === 'dev';
		$version->Development = $isDev;

		$version->SourceType = $package->getSource()->getType();
		$version->SourceUrl = $package->getSource()->getUrl();
		$version->SourceReference = $package->getSource()->getReference();
		
		if($package->getDist()) {
			$version->DistType = $package->getDist()->getType();
			$version->DistUrl = $package->getDist()->getUrl();
			$version->DistReference = $package->getDist()->getReference();
			$version->DistChecksum = $package->getDist()->getShasum();
		}

		$version->Extra = $package->getExtra();
		$version->Homepage = $package->getHomepage();
		$version->License = $package->getLicense();
		// $version->Support = $package->getSupport();

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
			'require' => 'getRequire',
			'require-dev' => 'getRequireDev',
			'provide' => 'getProvide',
			'conflict' => 'getConflict',
			'replace' => 'getReplace'
		);

		foreach ($types as $type => $method) {
			if ($linked = $package->$method()) foreach ($linked as $link => $constraint) {
				$name = $link;
				$addon = Addon::get()->filter('Name', $name)->first();

				$local = $getLink($name, $type);
				$local->Constraint = $constraint;

				if ($addon) {
					$local->TargetID = $addon->ID;
				}

				$version->Links()->add($local);
			}
		}

		//to-do api have no method to get this.
		/*$suggested = $package->getSuggests();

		if ($suggested) foreach ($suggested as $package => $description) {
			$link = $getLink($package, 'suggest');
			$link->Description = $description;

			$version->Links()->add($link);
		}*/
	}

	private function updateCompatibility(Addon $addon, AddonVersion $version, CompletePackage $package) {
		$require = null;

		if($package->getRequire()) foreach ($package->getRequire() as $name => $link) {
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
			if ($require == $link) {
				$addon->CompatibleVersions()->add($id);
				$version->CompatibleVersions()->add($id);
			}
		}
	}

	private function updateAuthors(AddonVersion $version, CompletePackage $package) {
		if ($package->getAuthors()) foreach ($package->getAuthors() as $details) {
			$author = null;

			if (!$details->getName() && !$details->getEmail()) {
				continue;
			}

			if ($details->getEmail()) {
				$author = AddonAuthor::get()->filter('Email', $details->getEmail())->first();
			}

			if (!$author && $details->getHomepage()) {
				$author = AddonAuthor::get()
					->filter('Name', $details->getName())
					->filter('Homepage', $details->getHomepage())
					->first();
			}

			if (!$author && $details->getName()) {
				$author = AddonAuthor::get()
					->filter('Name', $details->getName())
					->filter('Versions.Addon.Name', $package->getName())
					->first();
			}

			if (!$author) {
				$author = new AddonAuthor();
			}

			if($details->getName()) $author->Name = $details->getName();
			if($details->getEmail()) $author->Email = $details->getEmail();
			if($details->getHomepage()) $author->Homepage = $details->getHomepage();
			
			//to-do not supported by API
			//if(isset($details['role'])) $author->Role = $details['role'];

			$version->Authors()->add($author->write());
		}
	}

}
