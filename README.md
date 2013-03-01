SilverStripe Extensions
=======================

This is the [SilverStripe](http://silverstripe.org) extensions listing site
project. It aggregates SilverStripe packages from [Packagist](http://packagist.org).

* [SilverStripe Extensions](http://extensions.silverstripe.org)
* [GitHub Project](https://github.com/silverstripe/extensions.silverstripe.org)
* [Issue Tracker](https://github.com/silverstripe/extensions.silverstripe.org/issues)

Dependencies
============

Elastic Search
--------------

Elastic search is used to provide extension package indexing and searching. If a
local installation of elastic search is used, the following configuration can
be used:

    Injector:
      SilverStripe\Elastica\ElasticaService:
        constructor:
          - %$Elastica\Client
          - extensions

You should run the elastic search reindex task to create the mappings after
installation.

Resque
------

A PHP implementation of resque is used to provide background building of extension
details. As such an installation of redis must be present. If you wish to use a
remote server, you can configure the `ResqueService` constructor parameters to
specify the backend using the injector system.

To actually run the background tasks you need to spawn worker processes. On a
production server this should be set up using a process monitor such as god. A
new worker process can be spawned using:

    QUEUE=first_build,update APP_INCLUDE=mysite/bin/silverstripe-resque.php php vendor/chrisboulton/php-resque/bin/resque
