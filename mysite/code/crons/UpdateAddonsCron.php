
<?php

class UpdateAddonsCron implements CronTask
{

    /**
     * Run hourly, on the 30min mark
     *
     * @return string
     */
    public function getSchedule()
    {
        return "30 * * * *";
    }

    /**
     * Run the build task UpdateAddonsTask
     * @return void
     */
    public function process()
    {
        $taskClass = UpdateAddonsTask::class;
        $job = new RunBuildTaskJob($taskClass);
        $jobID = Injector::inst()->get(QueuedJobService::class)->queueJob($job);
        echo 'Added ' . $taskClass . ' to job queue';
    }
}
