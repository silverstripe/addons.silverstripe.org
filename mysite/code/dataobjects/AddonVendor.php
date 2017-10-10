<?php
/**
 * An add-one vendor, derived from the vendor part of a package name,
 */
class AddonVendor extends DataObject
{

    public static $db = array(
        'Name' => 'Varchar(255)'
    );

    public static $has_many = array(
        'Addons' => 'Addon'
    );

    public function Authors()
    {
        return $this->Addons()->relation('Versions')->relation('Authors');
    }
}
