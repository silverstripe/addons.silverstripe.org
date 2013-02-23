<?php
/**
 * A version of an extension package.
 */
class ExtensionVersion extends DataObject {

	public static $db = array(
		'Name' => 'Varchar(255)',
		'Description' => 'Text',
		'Type' => 'Varchar(100)',
		'Keywords' => 'MultiValueField',
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
		'Authors' => 'ExtensionAuthor'
	);

}
