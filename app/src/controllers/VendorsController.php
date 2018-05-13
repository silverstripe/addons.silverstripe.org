<?php

use SilverStripe\Control\Director;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverStripe\ORM\Queries\SQLSelect;

/**
 * Handles listing package vendors.
 */
class VendorsController extends SiteController
{

    private static $allowed_actions = array(
        'index'
    );

    public function index()
    {
        return $this->renderWith(array('Vendors', 'Page'));
    }

    public function Title()
    {
        return 'Vendors';
    }

    public function Link($action = null)
    {
        return Controller::join_links(Director::baseURL(), 'vendors', $action);
    }

    public function Vendors()
    {
        $query = new SQLSelect();
        $result = new ArrayList();

        $query
            ->setSelect('"AddonVendor"."Name"')
            ->selectField('COUNT("Addon"."ID")'. 'Count')
            ->setFrom('"AddonVendor"')
            ->addLeftJoin('Addon', '"Addon"."VendorID" = "AddonVendor"."ID"')
            ->setGroupBy('"AddonVendor"."ID"')
            ->setOrderBy(array('"Count"' => 'DESC', '"Name"' => 'ASC'));

        foreach ($query->execute() as $row) {
            $link = Controller::join_links(
                Director::baseURL(),
                'add-ons',
                $row['Name']
            );

            $result->push(new ArrayData($row + array('Link' => $link)));
        }

        return $result;
    }
}
