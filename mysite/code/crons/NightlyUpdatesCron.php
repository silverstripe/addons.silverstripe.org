<?php

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
     * Run the build task CacheHelpfulRobotDataTask
     * @return void
     */
    public function process()
    {
        $taskClasses = [
            [BuildAddonsTask::class, null], // rebuild all addons
            [CacheHelpfulRobotDataTask::class, null], // fetch helpful robot data
        ];

        foreach ($taskClasses as $taskInfo) {
            list($taskClass, $taskQuerystring) = $taskInfo;
            $job = new RunBuildTaskJob($taskClass, $taskQuerystring);
            $jobID = Injector::inst()->get(QueuedJobService::class)->queueJob($job);
            echo 'Added ' . $taskClass . ' to job queue (job ID ' . $jobID . ")\n";
        }
    }
}
