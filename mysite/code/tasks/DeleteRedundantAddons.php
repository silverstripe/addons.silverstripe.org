<?php
/**
 * Deletes packages removed from Packagist.
 */
class DeleteRedundantAddons extends BuildTask {

	protected $title = 'Delete Redundant Add-ons ';

	protected $description = 'Deletes packages removed from Packagist';

	private $updater;

	public function run($request) {
		$dateOneWeekAgo  = date('Y-m-d', strtotime('-1 week'));

		$addons = Addon::get()->filter('LastUpdated:LessThan' ,$dateOneWeekAgo);

		foreach ($addons as $addon) {

			$addon->Keywords()->removeAll();
			$addon->Screenshots()->removeAll();
			$addon->CompatibleVersions()->removeAll();

			foreach ($addon->Versions() as $version) {
				$version->Authors()->removeAll();
				$version->Keywords()->removeAll();
				$version->CompatibleVersions()->removeAll();
				$version->delete();
			}

			$addon->delete();
		}
	}

}
