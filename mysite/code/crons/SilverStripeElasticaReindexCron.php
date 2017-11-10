<?php

class SilverStripeElasticaReindexCron implements CronTask
{

    /**
     * Run hourly, on the hour
     *
     * @return string
     */
    public function getSchedule()
    {
        return "0 * * * *";
    }

    /**
     * Run the build task SilverStripeElasticaReindexTask
     * @return void
     */
    public function process()
    {
        $taskClass = 'SilverStripeElasticaReindexTask';
        $job = new RunBuildTaskJob($taskClass);
        $jobID = Injector::inst()->get(QueuedJobService::class)->queueJob($job);
        echo 'Added ' . $taskClass . ' to job queue';
    }
}
