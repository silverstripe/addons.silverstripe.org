<?php

use SilverStripe\Control\RSS\RSSFeed;
use SilverStripe\View\Requirements;
use SilverStripe\View\SSViewer;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Control\Controller;
use SilverStripe\View\ArrayData;

/**
 * The base site controller.
 */
class SiteController extends Controller
{

    public function init()
    {
        RSSFeed::linkToFeed("add-ons/rss", "New modules on addons.silverstripe.org");

        Requirements::javascript("//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js");
        Requirements::javascript("themes/addons/bootstrap/js/bootstrap.min.js");
        Requirements::javascript("themes/addons/javascript/addons.js");
        Requirements::javascript("//www.google.com/jsapi");
        Requirements::javascript("themes/addons/javascript/chart.js");
        parent::init();
    }

    public function Menu()
    {
        $menu = new ArrayList();

        $menuEntries = array(
            //array('controller' => 'HomeController'),
            array('controller' => 'AddonsController'),
            array(
                'link' => Controller::join_links(
                    singleton('AddonsController')->Link(),
                    '?type=theme&view=expanded'
                ),
                'title' => 'Themes',
            ),
            array('controller' => 'VendorsController'),
            array('controller' => 'AuthorsController'),
            array('controller' => 'TagsController'),
            array('controller' => 'SubmitAddonController'),
        );

        foreach ($menuEntries as $menuEntry) {
            if (isset($menuEntry['controller'])) {
                $inst = singleton($menuEntry['controller']);
                $active = false;

                foreach (self::$controller_stack as $candidate) {
                    $active = (
                        $candidate instanceof $menuEntry['controller']
                        && $this->request->getVar('type') != 'theme'
                    );
                }

                $menu->push(new ArrayData(array(
                    'Title' => $inst->Title(),
                    'Link' => $inst->Link(),
                    'Active' => $active,
                    'MenuItemType' => $inst->MenuItemType()
                )));
            } else {
                $active = ($this->request->getVar('type') == 'theme');
                $menu->push(new ArrayData(array(
                    'Title' => $menuEntry['title'],
                    'Link' => $menuEntry['link'],
                    'Active' => $active,
                    'MenuItemType' => 'link',
                )));
            }
        }

        return $menu;
    }

    /**
     * @return String 'link' or 'button'
     */
    public function MenuItemType()
    {
        return 'link';
    }

    public function GATrackingCode()
    {
        return defined('GA_TRACKING_CODE') ? GA_TRACKING_CODE : null;
    }

    public function LinkWithSearch($extraParamStr = '')
    {
        $params = array_diff_key($this->request->getVars(), array('url' => null));
        parse_str($extraParamStr, $extraParams);
        $params = array_merge($params, (array)$extraParams);

        return Controller::join_links($this->Link(), '?' . http_build_query($params));
    }
}
