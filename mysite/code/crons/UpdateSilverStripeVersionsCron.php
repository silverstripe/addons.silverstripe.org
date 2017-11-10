
<?php

class UpdateSilverStripeVersionsCron implements CronTask
{

    /**
     * Run hourly, on the 20min mark
     *
     * @return string
     */
    public function getSchedule()
    {
        return "20 * * * *";
    }

    /**
     * Run the build task UpdateSilverStripeVersionsTask
     * @return void
     */
    public function process()
    {
        $taskClass = 'UpdateSilverStripeVersionsTask';
        $job = new RunBuildTaskJob($taskClass);
        $jobID = Injector::inst()->get(QueuedJobService::class)->queueJob($job);
        echo 'Added ' . $taskClass . ' to job queue';
    }
}
