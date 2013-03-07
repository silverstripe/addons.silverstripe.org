SilverStripe Extensions
=======================

This is the [SilverStripe](http://silverstripe.org) extensions listing site
project. It aggregates SilverStripe packages from [Packagist](http://packagist.org).

* [SilverStripe Extensions](http://extensions.silverstripe.org)
* [GitHub Project](https://github.com/silverstripe/extensions.silverstripe.org)
* [Issue Tracker](https://github.com/silverstripe/extensions.silverstripe.org/issues)

Installation
============

 * `composer install`
 * Install and run dependencies (see below)
 * Configure elastic search in `mysite/_config/injector.yml`
 * Run all tasks to populate database (see below)

Dependencies
============

Elastic Search
--------------

[Elastic search](www.elasticsearch.org) is used to provide extension package indexing and searching. If a
local installation of elastic search is used, the following configuration can be used
in `mysite/_config/injector.yml`:

    Injector:
      SilverStripe\Elastica\ElasticaService:
        constructor:
          - %$Elastica\Client
          - extensions

You should run the elastic search reindex task to create the mappings after installation.

Resque
------

A [PHP implementation of resque](https://github.com/chrisboulton/php-resque) 
is used to provide background building of extension
details. As such an installation of [redis](http://redis.io/) must be present. If you wish to use a
remote server, you can configure the `ResqueService` constructor parameters to
specify the backend using the injector system (see `mysite/_config/injector.yml`).

To actually run the background tasks you need to spawn worker processes. On a
production server this should be set up using a process monitor such as god. A
new worker process can be spawned using the following command in the webroot:

    QUEUE=first_build,update APP_INCLUDE=mysite/bin/silverstripe-resque.php php vendor/chrisboulton/php-resque/bin/resque

Tasks
============

 * `sake dev/tasks/UpdateExtensionsTask`: Runs the extension updater.
 * `sake dev/tasks/UpdateSilverStripeVersionsTask`: Updates the available SilverStripe versions.
 * `sake dev/tasks/SilverStripe-Elastica-ReindexTask`: Defines and refreshes the elastic search index.