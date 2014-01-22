<?php
/**
 * The base site controller.
 */
class SiteController extends Controller {

	public function Menu() {
		$menu = new ArrayList();

		$menuEntries = array(
			array('controller' => 'HomeController'),
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
			if(isset($menuEntry['controller'])) {
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
	public function MenuItemType() {
		return 'link';
	}

	public function GATrackingCode() {
		return defined('GA_TRACKING_CODE') ? GA_TRACKING_CODE : null;
	}

}
