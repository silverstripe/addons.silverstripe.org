<?php
/**
 * A basic interface for managing extensions.
 */
class ExtensionsAdmin extends ModelAdmin {

	public static $title = 'Extensions';

	public static $url_segment = 'extensions';

	public static $managed_models = array(
		'ExtensionPackage',
		'ExtensionAuthor'
	);

	public static $model_importers = array();

}
