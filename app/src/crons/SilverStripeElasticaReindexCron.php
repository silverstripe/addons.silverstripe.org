<?php

use Heyday\Elastica\ReindexTask;
use SilverStripe\Core\Injector\Injector;
use Symbiote\QueuedJobs\Services\QueuedJobService;
use SilverStripe\CronTask\Interfaces\CronTask;

/**
 * These regular updates run as often as is practical.
 * They seem to take 2-3 hours at the moment, so we run them 4 times per day.
 */
class SilverStripeElasticaReindexCron implements CronTask
{

    /**
     * Run every 6 hours
     *
     * @return string
     */
    public function getSchedule()
    {
        return "0 */6 * * *";
    }

    /**
     * Run the build task SilverStripeElasticaReindexTask
     * @return void
     */
    public function process()
    {
        $taskClasses = [
            [UpdateSilverStripeVersionsTask::class, null],
            [UpdateAddonsTask::class, null],
            [ReindexTask::class, 'reindex=1'],
        ];

        foreach ($taskClasses as $taskInfo) {
            list($taskClass, $taskQuerystring) = $taskInfo;
            $job = new RunBuildTaskJob($taskClass, $taskQuerystring);
            $jobID = Injector::inst()->get(QueuedJobService::class)->queueJob($job);
            echo 'Added ' . $taskClass . ' to job queue (job ID ' . $jobID . ")\n";
        }
    }
}
