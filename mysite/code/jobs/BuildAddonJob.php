<?php
/**
 * A background job which builds a single add-on.
 */
class BuildAddonJob extends AbstractQueuedJob
{
    private $packageID;

    /**
     * @var array params
     * @throws Exception
     */
    public function __construct($params = array())
    {
        if (!empty($params['package'])) {
            $this->PackageID = $params['package'];
        }

        $this->currentStep = 0;
        $this->totalSteps = 1;
    }

    protected function getPackage()
    {
        return Addon::get()->byID($this->PackageID);
    }

    public function process()
    {
        $package = $this->getPackage();
        if (!$package->ID) {
            throw new Exception('Package not specified');
        }

        $builder = Injector::inst()->get('AddonBuilder');

        $builder->build($package);

        $package->BuildQueued = false;
        $package->write();

        // Send the package to stitch-data for data warehousing
        Injector::inst()->get('StitchDataSender')->sendAddon($package);

        $this->isComplete = true;
        $this->currentStep = 1;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'Build Addon: ' . $this->getPackage()->Name;
    }
    /**
     * Return a signature for this queued job
     *
     * @return string
     */
    public function getSignature()
    {
        return md5(get_class($this) . $this->PackageID);
    }
}
