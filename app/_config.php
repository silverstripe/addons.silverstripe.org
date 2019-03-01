<?php

use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Environment;
use Heyday\Elastica\ElasticaService;
use Psr\Log\LoggerInterface;

global $project;
$project = 'app';

global $database;
if (!Environment::getEnv('SS_DATABASE_NAME')) {
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
if (Environment::getEnv('ELASTICSEARCH_HOST') && Environment::getEnv('ELASTICSEARCH_PORT')) {
    $config = [
        'host' => Environment::getEnv('ELASTICSEARCH_HOST'),
        'port' => Environment::getEnv('ELASTICSEARCH_PORT'),
        'timeout' => 5
    ];

    if (Environment::getEnv('AWS_REGION_NAME')) {
        $config['transport'] = 'AwsAuthV4';
        $config['aws_region'] = Environment::getEnv('AWS_REGION_NAME');
    }

    $esClient = new Elastica\Client($config);
    Injector::inst()->registerService($esClient, 'ElasticClient');

    if (Environment::getEnv('ELASTICSEARCH_INDEX')) {
        $esIndex = Environment::getEnv('ELASTICSEARCH_INDEX');
    } else {
        $esIndex = 'addons';
    }

    $esService = new ElasticaService($esClient, $esIndex, Injector::inst()->get(LoggerInterface::class));
    Injector::inst()->registerService($esService, ElasticaService::class);
}
