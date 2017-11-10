<?php
/**
 * A background job which builds a single add-on.
 */
class BuildAddonJob extends AbstractQueuedJob implements QueuedJob
{
    private $packageID;

    /**
     * @var array params
     * @throws Exception
     */
    public function __construct($params = array()) {
        if (!empty($params['package'])) {
            $this->setObject(Addon::get()->byID($params['package']));
        }
    }

    public function setUp()
    {
        global $databaseConfig;

        if (!DB::isActive()) {
            DB::connect($databaseConfig);
        }
    }

    public function perform()
    {
        $package = $this->getObject();
        if (!$package->ID) throw new Exception('Package not specified');

        $builder = Injector::inst()->get('AddonBuilder');

        $builder->build($package);

        $package->BuildQueued = false;
        $package->write();

        $this->isComplete = true;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'Build Extended Addon Information';
    }
    /**
     * Return a signature for this queued job
     *
     * @return string
     */
    public function getSignature() {
        return md5(get_class($this) . serialize($this->jobData) . $this->packageID);
    }

    /**
     * @return stdClass
     */
    public function getJobData() {
        $data = new stdClass();
        $data->totalSteps = $this->totalSteps;
        $data->currentStep = $this->currentStep;
        $data->isComplete = $this->isComplete;
        $data->jobData = $this->jobData;
        $data->messages = $this->messages;
        $data->packageID = $this->packageID;

        return $data;
    }

    /**
     * Do some processing yourself!
     */
    public function process()
    {
        $this->setUp();
        $this->perform();
    }
}
