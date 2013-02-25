<?php
/**
 * Lists tags that are associated with extensions.
 */
class TagsController extends SiteController {

	public static $allowed_actions = array(
		'index'
	);

	public function index() {
		return $this->renderWith(array('Tags', 'Page'));
	}

	public function Title() {
		return 'Tags';
	}

	public function Link() {
		return Controller::join_links(Director::baseURL(), 'tags');
	}

	public function Tags() {
		$query = new SQLQuery();
		$result = new ArrayList();

		$query
			->setSelect('"ExtensionKeyword"."ID", "Name"')
			->selectField('COUNT("ExtensionKeywordID")', 'Count')
			->setFrom('ExtensionKeyword')
			->addLeftJoin('ExtensionPackage_Keywords', '"ExtensionKeywordID" = "ExtensionKeyword"."ID"')
			->setGroupBy('"ID"')
			->setOrderBy(array('"Count"' => 'DESC', '"Name"' => 'ASC'));

		foreach ($query->execute() as $row) {
			$result->push(new ArrayData($row + array(
				'Link' => Controller::join_links($this->Link(), $row['ID'])
			)));
		}

		return $result;
	}

}
