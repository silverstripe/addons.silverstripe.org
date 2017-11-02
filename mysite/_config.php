<?php

global $project;
$project = 'mysite';

global $database;
if (!defined('SS_DATABASE_NAME')) {
    $database = 'addons';
}

require_once 'conf/ConfigureFromEnv.php';
