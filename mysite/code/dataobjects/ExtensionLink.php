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

	public function Link() {
		if ($this->TargetID) {
			return $this->Target()->Link();
		}

		if ($this->Name == 'php' || strpos($this->Name, 'ext-') === 0) {
			return '';
		}

		return "https://packagist.org/packages/$this->Name";
	}

}
