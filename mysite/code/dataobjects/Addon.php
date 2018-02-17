<?php

use Elastica\Document;
use Elastica\Type\Mapping;

/**
 * An add-on with one or more versions.
 */
class Addon extends DataObject
{

    public static $db = array(
        'Name'              => 'Varchar(255)',
        'Description'       => 'Text',
        'Type'              => 'Varchar(100)',
        'Readme'            => 'HTMLText',
        'Released'          => 'SS_Datetime',
        'Repository'        => 'Varchar(255)',
        'Downloads'         => 'Int',
        'DownloadsMonthly'  => 'Int',
        'Favers'            => 'Int',
        'LastUpdated'       => 'SS_Datetime',
        'LastBuilt'         => 'SS_Datetime',
        'BuildQueued'       => 'Boolean',
        // Module rating information
        'Rating'            => 'Int',
        'RatingDetails'     => 'Text',
    );

    public static $has_one = array(
        'Vendor' => 'AddonVendor',
    );

    public static $has_many = array(
        'Versions' => 'AddonVersion'
    );

    public static $many_many = array(
        'Keywords'           => 'AddonKeyword',
        'Screenshots'        => 'Image',
        'CompatibleVersions' => 'SilverStripeVersion'
    );

    public static $default_sort = 'Name';

    public static $extensions = array(
        'SilverStripe\\Elastica\\Searchable'
    );

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
            'downloads'     => array('type' => 'string'),
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
            ['Metric' => $metrics->has_readme, 'Description' => 'Readme'],
            ['Metric' => $metrics->has_license, 'Description' => 'FOSS License'],
            ['Metric' => $metrics->has_code_or_src_folder, 'Description' => 'Structured correctly'],
            ['Metric' => $metrics->has_contributing_file, 'Description' => 'Contributing file'],
            ['Metric' => $metrics->has_gitattributes_file, 'Description' => 'Git attributes file'],
            ['Metric' => $metrics->has_editorconfig_file, 'Description' => 'Editor config file'],
            ['Metric' => $metrics->good_code_coverage, 'Description' => 'Good code coverage (>40%)'],
            ['Metric' => $metrics->great_code_coverage, 'Description' => 'Great code coverage (>75%)'],
            ['Metric' => $metrics->has_documentation, 'Description' => 'Documentation'],
            ['Metric' => $metrics->travis_passing, 'Description' => 'Travis passing'],
            ['Metric' => $metrics->good_scrutinizer_score, 'Description' => 'Scrutinizer >6.5'],
            ['Metric' => $metrics->coding_standards, 'Description' => 'PSR-2 standards'],
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
