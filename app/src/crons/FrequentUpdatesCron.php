<?php

use Heyday\Elastica\ReindexTask;
use SilverStripe\Core\Injector\Injector;
use Symbiote\QueuedJobs\Jobs\RunBuildTaskJob;
use Symbiote\QueuedJobs\Services\QueuedJobService;
use SilverStripe\CronTask\Interfaces\CronTask;

/**
 * These regular updates run as often as is practical.
 * They seem to take 2-3 hours at the moment.
 * The Packagist data is cached for 12 hours, so there's not much point running it more often,
 * see https://packagist.org/apidoc.
 */
class FrequentUpdatesCron implements CronTask
{

    public function getSchedule()
    {
        return "0 */12 * * *";
    }

    public function process()
    {
        $taskClasses = [
            [UpdateSilverStripeVersionsTask::class, null],
            [UpdateAddonsTask::class, null], // triggers BuildAddonsJob for modules requiring a rebuild
            [ReindexTask::class, 'recreate=1'],
        ];

        foreach ($taskClasses as $taskInfo) {
            list($taskClass, $taskQuerystring) = $taskInfo;
            $job = new RunBuildTaskJob($taskClass, $taskQuerystring);
            $jobID = Injector::inst()->get(QueuedJobService::class)->queueJob($job);
            echo 'Added ' . $taskClass . ' to job queue (job ID ' . $jobID . ")\n";
        }
    }
}
