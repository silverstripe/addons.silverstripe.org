
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
    public function process() {
        $task = Injector::inst()->get('UpdateAddonsTask');
        $task->run(new SS_HTTPRequest('GET', '/'));
    }
}
