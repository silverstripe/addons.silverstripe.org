<?php

class CacheHelpfulRobotDataTask extends BuildTask
{
    /**
     * @var string
     */
    protected $title = 'Cache Helpful Robot Data';

    /**
     * @var string
     */
    protected $description = 'Downloads and stores Helpful Robot module data';

    /**
     * @param SS_HTTPRequest $request
     */
    public function run($request)
    {
        set_error_handler(function($code, $message) {
            throw new ErrorException($message, $code);
        });

        $addons = Addon::get();

        foreach ($addons as $addon) {
            $this->log('fetching ' . $addon->Name);

            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'user_agent' => 'addons.silverstripe.org',
                    'follow_location' => true,
                    'timeout' => 5,
                ],
            ]);

            try {
                $url = 'https://helpfulrobot.io/' . $addon->Name;
                $contents = file_get_contents($url, null, $context);

                $json = json_decode($contents, true);

                $addon->HelpfulRobotData = $contents;
                $addon->HelpfulRobotScore = $json['inspections'][0]['score'];
                $addon->write();
            } catch (ErrorException $exception) {
                $this->log(' - ' . $addon->Name . ' data missing');

                try {
                    file_get_contents($addon->Repository, null, $context);
                } catch (ErrorException $exception) {
                    $this->log(' - ' . $addon->Name . ' deleted');
                    $addon->delete();
                }
            }
        }
    }

    /**
     * @param string $message
     */
    private function log($message)
    {
        if (Director::is_cli()) {
            print $message . PHP_EOL;
        } else {
            print $message . "<br>";
        }
    }
}
