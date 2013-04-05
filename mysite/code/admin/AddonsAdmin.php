<?php
/**
 * A basic interface for managing add-ons.
 */
class AddonsAdmin extends ModelAdmin {

	public static $title = 'Add-ons';

	public static $url_segment = 'add-ons';

	public static $managed_models = array(
		'Addon',
		'AddonVendor',
		'AddonAuthor'
	);

	public static $model_importers = array();

}
