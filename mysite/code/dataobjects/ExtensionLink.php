<?php
/**
 * A link from one extension to another, such as a requirement dependency.
 */
class ExtensionLink extends DataObject {

	public static $db = array(
		'Name' => 'Varchar(100)',
		'Type' => 'Enum(array("require", "require-dev", "suggest", "provide", "conflict", "replace"))',
		'Constraint' => 'Varchar(100)',
		'Description' => 'Varchar(255)'
	);

	public static $has_one = array(
		'Source' => 'ExtensionVersion',
		'Target' => 'ExtensionPackage'
	);

}
