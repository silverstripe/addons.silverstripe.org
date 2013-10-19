<?php
/**
 * A version of an add-on package.
 */
class AddonVersion extends DataObject {

	public static $db = array(
		'Name' => 'Varchar(255)',
		'Description' => 'Text',
		'Type' => 'Varchar(100)',
		'Released' => 'SS_Datetime',
		'Extra' => 'MultiValueField',
		'Homepage' => 'Varchar(255)',
		'Version' => 'Varchar(100)',
		'PrettyVersion' => 'Varchar(100)',
		'Development' => 'Boolean',
		'License' => 'MultiValueField',
		'SourceType' => 'Varchar(100)',
		'SourceUrl' => 'Varchar(255)',
		'SourceReference' => 'Varchar(40)',
		'DistType' => 'Varchar(100)',
		'DistUrl' => 'Varchar(255)',
		'DistReference' => 'Varchar(100)',
		'DistChecksum' => 'Varchar(40)',
		'Dist' => 'MultiValueField',
		'Support' => 'MultiValueField'
	);

	public static $has_one = array(
		'Addon' => 'Addon'
	);

	public static $has_many = array(
		'Links' => 'AddonLink'
	);

	public static $many_many = array(
		'Authors' => 'AddonAuthor',
		'Keywords' => 'AddonKeyword',
		'CompatibleVersions' => 'SilverStripeVersion'
	);

	public static $default_sort = array(
		'ID' => 'DESC'
	);

	public function DisplayVersion() {
		return $this->PrettyVersion;
	}

	public function DisplayRequireVersion() {
		return str_replace('.x-dev', '.*@dev', $this->DisplayVersion());
	}

	/**
	 * Fallback to SourceUrl with normalized github links.
	 */
	public function DisplayHomepage() {
		if($this->Homepage) {
			return $this->Homepage;
		} else {
			return str_replace(
				array('git://github.com', 'git@github.com'),
				'https://github.com',
				$this->SourceUrl
			);
		}
	}

	public function getRequires() {
		return $this->Links()->filter('Type', 'require');
	}

	public function getRequiresDev() {
		return $this->Links()->filter('Type', 'require-dev');
	}

	public function getSuggests() {
		return $this->Links()->filter('Type', 'suggest');
	}

	public function getProvides() {
		return $this->Links()->filter('Type', 'provide');
	}

	public function getConflicts() {
		return $this->Links()->filter('Type', 'conflict');
	}

	public function getReplaces() {
		return $this->Links()->filter('Type', 'replace');
	}

	public function InstallLink() {
		return Controller::join_links($this->Addon()->Link(), 'install', $this->ID);
	}

	public function onBeforeDelete() {
		parent::onBeforeDelete();

		// Remove our relations but leave the related objects for objects
		// that may be used by other objects.
		foreach($this->Links() as $link) {
			$link->delete();
		}
		$this->Authors()->removeAll();
		$this->Keywords()->removeAll();
		$this->CompatibleVersions()->removeAll();
	}

}
