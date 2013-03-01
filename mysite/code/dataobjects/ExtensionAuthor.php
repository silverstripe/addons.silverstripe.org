<?php
/**
 * An author who can be linked to several extensions.
 */
class ExtensionAuthor extends DataObject {

	public static $db = array(
		'Name' => 'Varchar(255)',
		'Email' => 'Varchar(255)',
		'Homepage' => 'Varchar(255)',
		'Role' => 'Varchar(255)'
	);

	public static $belongs_many_many = array(
		'Versions' => 'ExtensionVersion'
	);

	public static $default_sort = 'Name';

	public function GravatarUrl($size, $default = 'mm') {
		return sprintf(
			'http://www.gravatar.com/avatar/%s?s=%d&d=%s',
			md5(strtolower(trim($this->Email))),
			$size,
			$default
		);
	}

	public function Link() {
		return Controller::join_links(Director::baseURL(), 'authors', $this->ID);
	}

	public function Extensions() {
		return ExtensionPackage::get()->filter('ID', $this->Versions()->column('ExtensionID'));
	}

}
