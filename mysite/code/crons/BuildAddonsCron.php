<?php

class BuildAddonsCron implements CronTask
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
     * Run the build task BuildAddonsTask
     * @return void
     */
    public function process()
    {
        $taskClass = BuildAddonsTask::class;
        $job = new RunBuildTaskJob($taskClass);
        $jobID = Injector::inst()->get(QueuedJobService::class)->queueJob($job);
        echo 'Added ' . $taskClass . ' to job queue';
    }
}
