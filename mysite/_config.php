<?php

global $project;
$project = 'mysite';

global $database;
if (!defined('SS_DATABASE_NAME')) {
    $database = 'addons';
}

require_once 'conf/ConfigureFromEnv.php';

// Note: This is SilverStripe 3.0 compatible code, so we cannot use CONSTANTS in the yaml config
//       so this mean that we unfortunately need to set up ElasticSearch (ES) configuration in
//       runtime.
//
// The following constants are defined in SSP for connection to AWS ES Service:
//     - AWS_REGION_NAME
//     - ELASTICSEARCH_HOST
//     - ELASTICSEARCH_PORT
//     - ELASTICSEARCH_INDEX
if (defined('ELASTICSEARCH_HOST') && defined('ELASTICSEARCH_PORT')) {
    $config = [
        'host' => ELASTICSEARCH_HOST,
        'port' => ELASTICSEARCH_PORT,
        'timeout' => 5
    ];

    if (defined('AWS_REGION_NAME')) {
        $config['transport'] = 'AwsAuthV4';
        $config['aws_region'] = AWS_REGION_NAME;
    }

    $esClient = new Elastica\Client($config);
    Injector::inst()->registerService($esClient, 'ElasticClient');

    if (defined('ELASTICSEARCH_INDEX')) {
        $esIndex = ELASTICSEARCH_INDEX;
    } else {
        $esIndex = 'addons';
    }

    $esService = new SilverStripe\Elastica\ElasticaService($esClient, $esIndex);
    Injector::inst()->registerService($esService, 'ElasticaService');
}
