<?php
/**
 * A root extension package with one or more versions.
 */
class ExtensionPackage extends DataObject {

	public static $db = array(
		'Name' => 'Varchar(255)',
		'Description' => 'Text',
		'Type' => 'Varchar(100)',
		'Released' => 'SS_Datetime',
		'Repository' => 'Varchar(255)',
		'Downloads' => 'Int',
		'LastUpdated' => 'SS_Datetime'
	);

	public static $has_one = array(
		'Vendor' => 'ExtensionVendor'
	);

	public static $has_many = array(
		'Versions' => 'ExtensionVersion'
	);

	public static $many_many = array(
		'Keywords' => 'ExtensionKeyword',
		'CompatibleVersions' => 'SilverStripeVersion'
	);

	/**
	 * @return string
	 */
	public function getVendorName() {
		return substr($this->Name, 0, strpos($this->Name, '/'));
	}

}
