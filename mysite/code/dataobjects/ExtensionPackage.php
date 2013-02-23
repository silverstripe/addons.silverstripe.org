<?php
/**
 * A root extension package with one or more versions.
 */
class ExtensionPackage extends DataObject {

	public static $db = array(
		'Name' => 'Varchar(255)',
		'Description' => 'Text',
		'Type' => 'Varchar(100)',
		'Repository' => 'Varchar(255)',
		'Keywords' => 'MultiValueField',
		'Downloads' => 'Int',
		'LastUpdated' => 'SS_Datetime'
	);

	public static $has_many = array(
		'Versions' => 'ExtensionVersion'
	);

}
