<?php

use Elastica\Document;
use Elastica\Type\Mapping;

/**
 * An add-on with one or more versions.
 */
class Addon extends DataObject {

	public static $db = array(
		'Name' => 'Varchar(255)',
		'Description' => 'Text',
		'Type' => 'Varchar(100)',
		'Readme' => 'HTMLText',
		'Released' => 'SS_Datetime',
		'Repository' => 'Varchar(255)',
		'Downloads' => 'Int',
		'DownloadsMonthly' => 'Int',
		'Favers' => 'Int',
		'LastUpdated' => 'SS_Datetime',
		'LastBuilt' => 'SS_Datetime',
		'BuildQueued' => 'Boolean'
	);

	public static $has_one = array(
		'Vendor' => 'AddonVendor'
	);

	public static $has_many = array(
		'Versions' => 'AddonVersion'
	);

	public static $many_many = array(
		'Keywords' => 'AddonKeyword',
		'Screenshots' => 'Image',
		'CompatibleVersions' => 'SilverStripeVersion'
	);

	public static $default_sort = 'Name';

	public static $extensions = array(
		'SilverStripe\\Elastica\\Searchable'
	);

	/**
	 * Gets the addon's versions sorted from newest to oldest.
	 *
	 * @return ArrayList
	 */
	public function SortedVersions() {
		$versions = $this->Versions()->toArray();

		usort($versions, function($a, $b) {
			return version_compare($b->Version, $a->Version);
		});

		return new ArrayList($versions);
	}

	public function MasterVersion() {
		return $this->Versions()->filter('PrettyVersion', array('dev-master', 'trunk'))->First();
	}

	public function Authors() {
		return $this->Versions()->relation('Authors');
	}

	public function VendorName() {
		return substr($this->Name, 0, strpos($this->Name, '/'));
	}

	public function VendorLink() {
		return Controller::join_links(
			Director::baseURL(), 'add-ons', $this->VendorName()
		);
	}

	public function PackageName() {
		return substr($this->Name, strpos($this->Name, '/') + 1);
	}

	public function Link() {
		return Controller::join_links(
			Director::baseURL(), 'add-ons', $this->Name
		);
	}

	public function PackagistUrl() {
		return "https://packagist.org/packages/$this->Name";
	}

	public function getElasticaMapping() {
		return new Mapping(null, array(
			'name' => array('type' => 'string'),
			'description' => array('type' => 'string'),
			'type' => array('type' => 'string'),
			'compatibility' => array('type' => 'string', 'index_name' => 'compatible'),
			'vendor' => array('type' => 'string'),
			'tags' => array('type' => 'string', 'index_name' => 'tag'),
			'released' => array('type' => 'date'),
			'downloads' => array('type' => 'long'),
			'readme' => array('type' => 'string')
		));
	}

	public function getElasticaDocument() {
		return new Document($this->ID, array(
			'name' => $this->Name,
			'description' => $this->Description,
			'type' => $this->Type,
			'compatibility' => $this->CompatibleVersions()->column('Name'),
			'vendor' => $this->VendorName(),
			'tags' => $this->Keywords()->column('Name'),
			'released' => $this->obj('Released')->Format('c'),
			'downloads' => $this->Downloads,
			'readme' => strip_tags($this->Readme),
			'_boost' => sqrt($this->Downloads)
		));
	}

	public function onBeforeDelete() {
		parent::onBeforeDelete();
		
		// Partially cascade delete. Leave author and keywords in place,
		// since they might be related to other addons.
		foreach($this->Screenshots() as $image) {
			$image->delete();
		}
		$this->Screenshots()->removeAll();

		foreach($this->Versions() as $version) {
			$version->delete();
		}

		$this->Keywords()->removeAll();
		$this->CompatibleVersions()->removeAll();
	}

	public function getDateCreated() {
		return date('Y-m-d', strtotime($this->Created));
	}

	public function IsHostedOnGitHub() {
		return strpos($this->Repository, 'github');
	}

	public function GitHubZipDownloadLink() {
		$downloadLink = '';
	
		if($this->IsHostedOnGitHub()) {

			if(strpos($this->Repository, '.git')) {
				$repositoryLink = str_replace('git@github.com:', '', $this->Repository);
				$repositoryLink = str_replace('.git', '', $repositoryLink);
				
				$repositoryLink = 'https://github.com/' . $repositoryLink ;
				$downloadLink = $repositoryLink . '/archive/master.zip';
			} else {
				$downloadLink = $this->Repository . '/archive/master.zip';
			}
		}

		return $downloadLink;
	}
}
