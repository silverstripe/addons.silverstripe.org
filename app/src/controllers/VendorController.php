<?php

use SilverStripe\Control\Controller;
use SilverStripe\ORM\PaginatedList;

/**
 * Displays the packages and associated authors for a vendor.
 */
class VendorController extends SiteController
{

    private static $allowed_actions = array(
        'index'
    );

    protected $parent;
    protected $vendor;

    public function __construct(Controller $parent, AddonVendor $vendor)
    {
        $this->parent = $parent;
        $this->vendor = $vendor;

        parent::__construct();
    }

    public function index()
    {
        return $this->renderWith(array('Vendor', 'Page'));
    }

    public function Title()
    {
        return $this->vendor->Name;
    }

    public function Vendor()
    {
        return $this->vendor;
    }

    public function Addons()
    {
        $list = new PaginatedList($this->vendor->Addons(), $this->request);
        $list->setPageLength(15);

        return $list;
    }
}
