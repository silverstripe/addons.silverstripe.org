<?php
/**
 * A version of an extension package.
 */
class ExtensionVersion extends DataObject {

	public static $db = array(
		'Name' => 'Varchar(255)',
		'Description' => 'Text',
		'Type' => 'Varchar(100)',
		'Released' => 'SS_Datetime',
		'Extra' => 'MultiValueField',
		'Homepage' => 'Varchar(255)',
		'Version' => 'Varchar(100)',
		'PrettyVersion' => 'Varchar(100)',
		'Alias' => 'Varchar(100)',
		'PrettyAlias' => 'Varchar(100)',
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
		'Extension' => 'ExtensionPackage'
	);

	public static $has_many = array(
		'Links' => 'ExtensionLink'
	);

	public static $many_many = array(
		'Authors' => 'ExtensionAuthor',
		'Keywords' => 'ExtensionKeyword',
		'CompatibleVersions' => 'SilverStripeVersion'
	);

	public static $default_sort = array(
		'ID' => 'DESC'
	);

	public function DisplayVersion() {
		return $this->PrettyAlias ?: $this->PrettyVersion;
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
		return Controller::join_links($this->Extension()->Link(), 'install', $this->ID);
	}

}
