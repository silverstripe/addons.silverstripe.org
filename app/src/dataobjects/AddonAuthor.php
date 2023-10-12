<?php

use SilverStripe\Control\Director;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ManyManyList;

/**
 * An author who can be linked to several add-ons.
 */
class AddonAuthor extends DataObject
{
    private static $db = array(
        'Name' => 'Varchar(255)',
        'Email' => 'Varchar(255)',
        'Homepage' => 'Varchar(255)',
        'Role' => 'Varchar(255)'
    );

    private static $belongs_many_many = array(
        'Versions' => 'AddonVersion'
    );

    private static $default_sort = 'Name';

    public function GravatarUrl($size, $default = 'mm')
    {
        return sprintf(
            'https://www.gravatar.com/avatar/%s?s=%d&d=%s',
            md5(strtolower(trim($this->Email ?? ''))),
            $size,
            $default
        );
    }

    public function Link()
    {
        return Controller::join_links(Director::baseURL(), 'authors', $this->ID);
    }

    public function Addons()
    {
        /** @var ManyManyList $versions */
        $versions = $this->Versions();
        if (!$versions || !$versions->count()) {
            return ArrayList::create();
        }
        return Addon::get()->filter('ID', $versions->column('AddonID'));
    }
}
