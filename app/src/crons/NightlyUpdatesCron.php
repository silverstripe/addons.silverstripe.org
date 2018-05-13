<?php

use SilverStripe\Core\Injector\Injector;
use Symbiote\QueuedJobs\Services\QueuedJobService;
use SilverStripe\CronTask\Interfaces\CronTask;

/**
 * These nightly updates can clean up anything that might have been missed by the other tasks
 */
class NightlyUpdatesCron implements CronTask
{

    /**
     * Run at 1am every morning
     *
     * @return string
     */
    public function getSchedule()
    {
        return "0 1 * * *";
    }

    /**
     * Run the build task to update addons
     * @return void
     */
    public function process()
    {
        $taskClasses = [
            [BuildAddonsTask::class, null], // rebuild all addons
        ];

        foreach ($taskClasses as $taskInfo) {
            list($taskClass, $taskQuerystring) = $taskInfo;
            $job = new RunBuildTaskJob($taskClass, $taskQuerystring);
            $jobID = Injector::inst()->get(QueuedJobService::class)->queueJob($job);
            echo 'Added ' . $taskClass . ' to job queue (job ID ' . $jobID . ")\n";
        }
    }
}
