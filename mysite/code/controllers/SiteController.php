<?php
/**
 * The base site controller.
 */
class SiteController extends Controller {

	public function Menu() {
		$menu = new ArrayList();

		$controllers = array(
			'HomeController',
			'AddonsController',
			'VendorsController',
			'AuthorsController',
			'TagsController',
			'SubmitAddonController',
		);

		foreach ($controllers as $controller) {
			$inst = singleton($controller);
			$active = false;

			foreach (self::$controller_stack as $candidate) {
				if ($candidate instanceof $controller) {
					$active = true;
				}
			}

			$menu->push(new ArrayData(array(
				'Title' => $inst->Title(),
				'Link' => $inst->Link(),
				'Active' => $active,
				'MenuItemType' => $inst->MenuItemType()
			)));
		}

		return $menu;
	}

	/**
	 * @return String 'link' or 'button'
	 */
	public function MenuItemType() {
		return 'link';
	}

}
