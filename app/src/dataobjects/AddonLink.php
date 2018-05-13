<?php

use SilverStripe\ORM\DataObject;

/**
 * A link from one add-ons to another, such as a requirement dependency.
 */
class AddonLink extends DataObject
{

    private static $db = array(
        'Name' => 'Varchar(100)',
        'Type' => 'Enum(array("require", "require-dev", "suggest", "provide", "conflict", "replace"))',
        'Constraint' => 'Varchar(100)',
        'Description' => 'Varchar(255)'
    );

    private static $has_one = array(
        'Source' => 'AddonVersion',
        'Target' => 'Addon'
    );

    public function Link()
    {
        if ($this->TargetID) {
            return $this->Target()->Link();
        }

        if ($this->Name == 'php' || strpos($this->Name, 'ext-') === 0) {
            return '';
        }

        return "https://packagist.org/packages/$this->Name";
    }
}
