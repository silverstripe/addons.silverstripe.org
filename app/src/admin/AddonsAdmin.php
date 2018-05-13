<?php

use SilverStripe\Admin\ModelAdmin;

/**
 * A basic interface for managing add-ons.
 */
class AddonsAdmin extends ModelAdmin
{

    private static $title = 'Add-ons';

    private static $url_segment = 'add-ons';

    private static $managed_models = array(
        'Addon',
        'AddonVendor',
        'AddonAuthor'
    );

    private static $model_importers = array();
}
