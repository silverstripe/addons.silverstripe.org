<?php

use Composer\Semver\Constraint\ConstraintInterface;
use Composer\Semver\Semver;
use SilverStripe\ORM\DataObject;

/**
 * A SilverStripe version which an add-on can be compatible with.
 */
class SilverStripeVersion extends DataObject
{
    private static $db = array(
        'Name' => 'Varchar(10)',
        'Major' => 'Int',
        'Minor' => 'Int'
    );

    private static $default_sort = array(
        'Major' => 'DESC',
        'Minor' => 'DESC'
    );

    /**
     * Returns a generic version contraint for this version
     *
     * @return string
     */
    public function __toString()
    {
        return $this->Major . '.' . $this->Minor . '.0';
    }

    /**
     * Check whether a given module's composer version constraint will apply to this version
     *
     * @param  string|ConstraintInterface $constraint
     * @return bool
     */
    public function getConstraintValidity($constraint)
    {
        if ($constraint instanceof ConstraintInterface) {
            $constraint = $constraint->getPrettyString();
        }
        return Semver::satisfies((string) $this, $constraint);
    }
}
