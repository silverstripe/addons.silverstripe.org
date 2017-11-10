<?php

class CacheHelpfulRobotDataCron implements CronTask
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
        $taskClass = 'CacheHelpfulRobotDataTask';
        $job = new RunBuildTaskJob($taskClass);
        $jobID = Injector::inst()->get(QueuedJobService::class)->queueJob($job);
        echo 'Added ' . $taskClass . ' to job queue';
    }
}
