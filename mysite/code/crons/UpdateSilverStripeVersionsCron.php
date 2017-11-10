
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
    public function process() {
        $task = Injector::inst()->get('UpdateSilverStripeVersionsTask');
        $task->run(new SS_HTTPRequest('GET', '/'));
    }
}
