<?php
/**
 * Handles listing package vendors.
 */
class VendorsController extends SiteController
{

    public static $allowed_actions = array(
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

    public function Link()
    {
        return Controller::join_links(Director::baseURL(), 'vendors');
    }

    public function Vendors()
    {
        $query = new SQLQuery();
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
