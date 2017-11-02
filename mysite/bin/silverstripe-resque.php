#!/usr/bin/env php
<?php
/**
 * Initialises the SilverStripe environment for Resque.
 */

if (php_sapi_name() != 'cli') {
    die(1);
}

$base = dirname(dirname(__DIR__));
require_once $base . '/framework/core/Core.php';
