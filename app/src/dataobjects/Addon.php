<?php

use Elastica\Document;
use Elastica\Type\Mapping;
use Heyday\Elastica\Searchable;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\FieldType\DBBoolean;
use SilverStripe\Assets\Image;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Control\Director;
use SilverStripe\Control\Controller;
use SilverStripe\View\ArrayData;
use SilverStripe\ORM\DataObject;

/**
 * An add-on with one or more versions.
 */
class Addon extends DataObject
{

    private static $db = array(
        'Name'              => 'Varchar(255)',
        'Description'       => 'Text',
        'Type'              => 'Varchar(100)',
        'Readme'            => 'HTMLText',
        'Released'          => 'Datetime',
        'Repository'        => 'Varchar(255)',
        'Downloads'         => 'Int',
        'DownloadsMonthly'  => 'Int',
        'Favers'            => 'Int',
        'LastUpdated'       => 'Datetime',
        'LastBuilt'         => 'Datetime',
        'BuildQueued'       => 'Boolean',
        'Abandoned'         => 'Text',
        // Module rating information
        'Rating'            => 'Int',
        'RatingDetails'     => 'Text',
        // Commercially supported by SilverStripe Ltd.
        'Supported'         => 'Boolean',
    );

    private static $has_one = array(
        'Vendor' => 'AddonVendor',
    );

    private static $has_many = array(
        'Versions' => 'AddonVersion'
    );

    private static $many_many = array(
        'Keywords'           => AddonKeyword::class,
        'Screenshots'        => Image::class,
        'CompatibleVersions' => SilverStripeVersion::class,
    );

    private static $default_sort = 'Name';

    private static $extensions = [
        Searchable::class,
    ];

    /**
     * Gets the addon's versions sorted from newest to oldest.
     *
     * @return ArrayList
     */
    public function SortedVersions()
    {
        $versions = $this->Versions()->toArray();

        usort($versions, function ($a, $b) {
            return version_compare($b->Version, $a->Version);
        });

        return new ArrayList($versions);
    }

    public function MasterVersion()
    {
        return $this->Versions()->filter('PrettyVersion', array('dev-master', 'trunk'))->First();
    }

    public function Authors()
    {
        return $this->Versions()->relation('Authors');
    }

    public function VendorName()
    {
        return substr($this->Name, 0, strpos($this->Name, '/'));
    }

    public function VendorLink()
    {
        return Controller::join_links(
            Director::baseURL(),
            'add-ons',
            $this->VendorName()
        );
    }

    public function PackageName()
    {
        return substr($this->Name, strpos($this->Name, '/') + 1);
    }

    public function Link()
    {
        return Controller::join_links(
            Director::baseURL(),
            'add-ons',
            $this->Name
        );
    }

    public function DescriptionText()
    {
        return $this->Description;
    }

    public function RSSTitle()
    {
        return sprintf('New module release: %s', $this->Name);
    }

    public function PackagistUrl()
    {
        return "https://packagist.org/packages/$this->Name";
    }

    public function getElasticaMapping()
    {
        return new Mapping(null, array(
            'name'          => array('type' => 'string'),
            'description'   => array('type' => 'string'),
            'type'          => array('type' => 'string'),
            'compatibility' => array('type' => 'string'),
            'vendor'        => array('type' => 'string'),
            'tags'          => array('type' => 'string'),
            'released'      => array('type' => 'date'),
            'downloads'     => array('type' => 'long'),
            'readme'        => array('type' => 'string')
        ));
    }

    public function getElasticaDocument()
    {
        return new Document($this->ID, array(
            'name'          => $this->Name,
            'description'   => $this->Description,
            'type'          => $this->Type,
            'compatibility' => $this->CompatibleVersions()->column('Name'),
            'vendor'        => $this->VendorName(),
            'tags'          => $this->Keywords()->column('Name'),
            'released'      => $this->obj('Released')->Format('c'),
            'downloads'     => (int)$this->Downloads,
            'readme'        => strip_tags($this->Readme),
            'SS_Published'  => true,
        ));
    }

    public function onBeforeDelete()
    {
        parent::onBeforeDelete();

        // Partially cascade delete. Leave author and keywords in place,
        // since they might be related to other addons.
        foreach ($this->Screenshots() as $image) {
            $image->delete();
        }
        $this->Screenshots()->removeAll();

        foreach ($this->Versions() as $version) {
            $version->delete();
        }

        $this->Keywords()->removeAll();
        $this->CompatibleVersions()->removeAll();
    }

    public function getDateCreated()
    {
        return date('Y-m-d', strtotime($this->Created));
    }

    /**
     * Returns unserialised result data from the ratings check suite
     *
     * {@see \SilverStripe\ModuleRatings\CheckSuite}
     *
     * @return ArrayData
     */
    public function RatingData()
    {
        if ($this->RatingDetails) {
            $data = (array)json_decode($this->RatingDetails, true);
            return ArrayData::create($data);
        }
    }

    /**
     * Returns a list of whether rating metrics have passed for this addon, and a description of the metric
     *
     * @return ArrayList
     */
    public function RatingDescriptions()
    {
        $metrics = $this->RatingData();

        return ArrayList::create([
            [
                'Metric' => $metrics->has_readme,
                'Description' => 'Readme',
                'Title' => 'Module has a readme file',
            ],
            [
                'Metric' => $metrics->has_license,
                'Description' => 'FOSS License',
                'Title' => 'Module has a "free open source software" license',
            ],
            [
                'Metric' => $metrics->has_code_or_src_folder,
                'Description' => 'Structured correctly',
                'Title' => 'PHP code is in a folder called "code" or "src"',
            ],
            [
                'Metric' => $metrics->has_contributing_file,
                'Description' => 'Contributing file',
                'Title' => 'A guide for open source contributors exists',
            ],
            [
                'Metric' => $metrics->has_gitattributes_file,
                'Description' => 'Git attributes file',
                'Title' => 'A .gitattributes file exists to ignore files from distributable packages',
            ],
            [
                'Metric' => $metrics->has_editorconfig_file,
                'Description' => 'Editor config file',
                'Title' => 'An EditorConfig ruleset file exists for IDE formatting',
            ],
            [
                'Metric' => $metrics->good_code_coverage,
                'Description' => 'Good code coverage (>40%)',
                'Title' => '40% or more of code is covered by automated tests (in codecov.io or ScrutinizerCI)',
            ],
            [
                'Metric' => $metrics->great_code_coverage,
                'Description' => 'Great code coverage (>75%)',
                'Title' => '75% or more of code is covered by automated tests (in codecov.io or ScrutinizerCI)',
            ],
            [
                'Metric' => $metrics->has_documentation,
                'Description' => 'Documentation',
                'Title' => 'A "docs" folder exists containing documentation',
            ],
            [
                'Metric' => $metrics->ci_passing,
                'Description' => 'CI builds passing',
                'Title' => 'Automated tests via TravisCI or CircleCI are passing',
            ],
            [
                'Metric' => $metrics->good_scrutinizer_score,
                'Description' => 'Scrutinizer >6.5',
                'Title' => 'ScrutinizerCI score for this module is at least 6.5/10',
            ],
            [
                'Metric' => $metrics->coding_standards,
                'Description' => 'PSR-2 standards',
                'Title' => 'PHP code conforms to SilverStripe\'s implementation of PSR-2 formatting rules'
            ],
        ]);
    }



    /**
     *
     * @return bool|DateInterval
     */
    public function addonAge()
    {
        $date = new DateTime();
        $released = new DateTime($this->Released);

        return $date->diff($released);
    }

    /**
     * Calculate the total amount of downloads per day
     * Based on the total amount of downloads divided by the age of the addon
     *
     * @return float
     */
    public function getRelativePopularity()
    {
        return (int)$this->Downloads / max((int)$this->addonAge()->days, 1);
    }

    /**
     * Format the relative popularity to a nicely readable number
     *
     * @return string
     */
    public function relativePopularityFormatted()
    {
        return number_format($this->getRelativePopularity(), 2);
    }
}
