<?php

use SilverStripe\ORM\DataObject;

/**
 * A keyword which is attached to add-ons and versions.
 */
class AddonKeyword extends DataObject
{

    private static $db = array(
        'Name' => 'Varchar(255)'
    );

    private static $belongs_many_many = array(
        'Addons' => 'Addon',
        'Versions' => 'AddonVersion'
    );

    /**
     * Gets a keyword object by name, creating one if it does not exist.
     *
     * @param string $name
     * @return AddonKeyword
     */
    public static function get_by_name($name)
    {
        $name = strtolower($name);
        $kw = AddonKeyword::get()->filter('Name', $name)->first();

        if (!$kw) {
            $kw = new AddonKeyword();
            $kw->Name = $name;
            $kw->write();
        }

        return $kw;
    }
}
