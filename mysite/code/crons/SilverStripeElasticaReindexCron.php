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
    public function process() {
        $task = Injector::inst()->get('SilverStripeElasticaReindexTask');
        $task->run(new SS_HTTPRequest('GET', '/'));
    }
}
