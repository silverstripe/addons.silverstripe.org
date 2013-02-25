<?php
/**
 * A keyword which is attached to extensions and versions.
 */
class ExtensionKeyword extends DataObject {

	public static $db = array(
		'Name' => 'Varchar(255)'
	);

	public static $belongs_many_many = array(
		'Extensions' => 'ExtensionPackage',
		'Versions' => 'ExtensionVersion'
	);

	/**
	 * Gets a keyword object by name, creating one if it does not exist.
	 *
	 * @param string $name
	 * @return ExtensionKeyword
	 */
	public static function get_by_name($name) {
		$name = strtolower($name);
		$kw = ExtensionKeyword::get()->filter('Name', $name)->first();

		if (!$kw) {
			$kw = new ExtensionKeyword();
			$kw->Name = $name;
			$kw->write();
		}

		return $kw;
	}

}
