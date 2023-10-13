<?php

use SilverStripe\Control\Director;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;

/**
 * Lists and searches add-ons.
 */
class AddonsController extends SiteController
{

    private static $url_handlers = array(
        '$Vendor!/$Name!' => 'addon',
        '$Vendor!'        => 'vendor',
    );

    private static $allowed_actions = array(
        'index',
        'addon',
        'vendor',
    );

    public function index()
    {
        return $this->renderWith(array('Addons', 'Page'));
    }

    public function addon($request)
    {
        $vendor = $request->param('Vendor');
        $name = $request->param('Name');
        $addon = Addon::get()->filter('Name', "$vendor/$name")->first();

        if (!$addon) {
            $this->httpError(404);
        }

        return new AddonController($this, $addon);
    }

    public function vendor($request)
    {
        $name = $request->param('Vendor');
        $vendor = AddonVendor::get()->filter('Name', $name)->first();

        if (!$vendor) {
            $this->httpError(404);
        }

        return new VendorController($this, $vendor);
    }

    public function Title()
    {
        return 'Add-ons';
    }

    public function Link($slug = null)
    {
        if ($slug) {
            return Controller::join_links(Director::baseURL(), 'add-ons', $slug);
        } else {
            return Controller::join_links(Director::baseURL(), 'add-ons');
        }
    }

    public function ListView()
    {
        $view = $this->request->getVar('view');
        if ($view) {
            return $view;
        } else {
            return 'list';
        }
    }

    public function Addons()
    {
        $search = $this->request->getVar('search');
        $type = $this->request->getVar('type');
        $compat = $this->request->getVar('compatibility');
        $tags = $this->request->getVar('tags');
        $sort = $this->request->getVar('sort');

        if (!in_array($sort, array('name', 'downloads', 'newest'))) {
            $sort = null;
        }

        switch ($sort) {
            case 'name':
                $sort = 'Name';
                break;
            case 'newest':
                $sort = ['Released' => 'DESC'];
                break;
            case 'downloads':
                $sort = ['Downloads' => 'DESC'];
                break;
            default:
                $sort = ['Downloads' => 'DESC'];
        }

        if ($search || $tags) {
            $singleton = Addon::singleton();
            $context = $singleton->getDefaultSearchContext();
            $query = [];
            if ($search) {
                $query[$singleton->getGeneralSearchFieldName()] = $search;
            }
            if ($compat) {

            }
            if ($tags) {
                $query['Keywords.Name'] = $tags;
            }
            $list = $context->getQuery($query, $sort);

        } else {
            $list = Addon::get()->sort($sort);
        }

        if ($type) {
            $list = $list->filter('Type', $type);
        }

        if ($compat) {
            $list = $list->filter('Versions.CompatibleVersions.Name', $compat);
        }

        $list = new PaginatedList($list, $this->request);
        $list->setPageLength(16);

        return $list;
    }

    public function AddonsSearchForm()
    {
        $form = new Form(
            $this,
            'AddonsSearchForm',
            new FieldList(array(
                TextField::create('search', 'Search for')
                    ->setValue($this->request->getVar('search'))
                    ->addExtraClass('input-block-level'),
                DropdownField::create('sort', 'Sort by')
                    ->setSource(array(
                        'name'      => 'Name',
                        'downloads' => 'Most downloaded',
                        'relative'  => 'Average downloads per day',
                        'newest'    => 'Newest'
                    ))
                    ->setValue($this->request->getVar('sort'))
                    ->addExtraClass('input-block-level'),
                DropdownField::create('type', 'Add-on type')
                    ->setSource(array(
                        'module' => 'Modules',
                        'theme'  => 'Themes'
                    ))
                    ->setEmptyString('Modules and themes')
                    ->setValue($this->request->getVar('type'))
                    ->addExtraClass('input-block-level'),
                CheckboxSetField::create('compatibility', 'Compatible Silverstripe CMS versions')
                    ->setSource(SilverStripeVersion::get()->map('Name', 'Name'))
                    ->setValue($this->request->getVar('compatibility'))
                    ->setTemplate('AddonsSearchCheckboxSetField')
            )),
            new FieldList()
        );

        return $form
            ->setFormMethod('GET')
            ->setFormAction($this->Link());
    }
}
