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
        $task = Injector::inst()->get('BuildAddonsTask');
        $task->run(new SS_HTTPRequest('GET', '/'));
    }
}
