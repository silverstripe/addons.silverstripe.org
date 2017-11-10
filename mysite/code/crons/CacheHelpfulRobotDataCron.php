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
        $task = Injector::inst()->get('CacheHelpfulRobotDataTask');
        $task->run(new SS_HTTPRequest('GET', '/'));
    }
}
