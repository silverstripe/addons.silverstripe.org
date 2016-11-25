<?php

use Composer\Semver\Constraint\MultiConstraint;
use Composer\Semver\Constraint\Constraint;

/**
 * A SilverStripe version which an add-on can be compatible with.
 */
class SilverStripeVersion extends DataObject {

	public static $db = array(
		'Name' => 'Varchar(10)',
		'Major' => 'Int',
		'Minor' => 'Int'
	);

	public static $default_sort = array(
		'Major' => 'DESC',
		'Minor' => 'DESC'
	);

	/**
	 * @return Composer\Package\LinkConstraint\LinkConstraintInterface
	 */
	public function getConstraint() {
		$next = $this->Major . '.' . ($this->Minor + 1);

		return new MultiConstraint(array(
			new Constraint('>=', "$this->Major.$this->Minor.*"),
			new Constraint('<', "$next.*")
		));
	}

}
