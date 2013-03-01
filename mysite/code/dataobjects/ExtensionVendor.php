<?php
/**
 * An extension vendor, derived from the vendor part of a package name,
 */
class ExtensionVendor extends DataObject {

	public static $db = array(
		'Name' => 'Varchar(255)'
	);

	public static $has_many = array(
		'Extensions' => 'ExtensionPackage'
	);

	public function Authors() {
		return $this->Extensions()->relation('Versions')->relation('Authors');
	}

}
