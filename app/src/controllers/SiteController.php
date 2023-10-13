<?php

use SilverStripe\View\Requirements;
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
        Requirements::javascript("//cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js");
        Requirements::javascript("themes/addons/bootstrap/js/bootstrap.min.js");
        Requirements::javascript("themes/addons/javascript/addons.js");
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
                    '?type=theme'
                ),
                'title' => 'Themes',
            ),
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
