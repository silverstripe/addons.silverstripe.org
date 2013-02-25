<?php

use Composer\Package\LinkConstraint\MultiConstraint;
use Composer\Package\LinkConstraint\VersionConstraint;

/**
 * A SilverStripe version which an extension can be compatible with.
 */
class SilverStripeVersion extends DataObject {

	public static $db = array(
		'Name' => 'Varchar(10)',
		'Major' => 'Int',
		'Minor' => 'Int'
	);

	/**
	 * @return Composer\Package\LinkConstraint\LinkConstraintInterface
	 */
	public function getConstraint() {
		$next = $this->Major . '.' . ($this->Minor + 1);

		return new MultiConstraint(array(
			new VersionConstraint('>=', "$this->Major.$this->Minor.0.0"),
			new VersionConstraint('<', "$next.0.0")
		));
	}

}
