# SilverStripe Addons

[![Build Status](https://travis-ci.org/silverstripe/addons.silverstripe.org.svg?branch=master)](https://travis-ci.org/silverstripe/addons.silverstripe.org)

This is the [SilverStripe](http://silverstripe.org) add-ons listing site project. It aggregates SilverStripe packages 
from [Packagist](http://packagist.org).

* [SilverStripe Add-ons](http://addons.silverstripe.org)
* [GitHub Project](https://github.com/silverstripe/addons.silverstripe.org)
* [Issue Tracker](https://github.com/silverstripe/addons.silverstripe.org/issues)

## Getting started

1. `git clone https://github.com/silverstripe/addons.silverstripe.org.git`
2. `cd` into the directory
3. `composer install`
4. Install elasticsearch `brew install elasticsearch` and configure if required
5. Install redis `brew install redis`
6. Start `elasticsearch` and `redis-server`
7. Start a Resque worker by running `./framework/sake dev/resque/run queue=first_build,update`
8. Run all tasks to populate database (see below, first run may take some time to populate)

## Dependencies

### ElasticSearch

[ElasticSearch](http://www.elasticsearch.org) is used to provide add-on package indexing and searching.

The configuration is already set up in `mysite/_config/injector.yml` and will use a different index depending on 
whether the site is on the production server (live) or on UAT or local development environment (test or dev).

 - Install with `brew install elasticsearch`
 - Start the server with `elasticsearch` in your terminal

You should run the elastic search reindex task to create the mappings after installation.

### Redis

Redis is an in-memory data structure store which is used by the Resque module when processing and updating new modules 
from Packagist.org.

* Install with `brew install redis`
* Start the redis instance with `redis-server`

Once running you can run the `UpdateAddonsTask` to pull all SilverStripe modules from Packagist into the addons site.

### Resque

A [PHP implementation of resque](https://github.com/chrisboulton/php-resque) is used to provide background building 
of add-ons details. As such an installation of [redis](http://redis.io/) must be present. If you wish to use a 
remote server, you can configure the `ResqueService` constructor parameters to specify the backend using the 
injector in `mysite/_config/injector.yml`.

To run the background tasks you need to spawn worker processes. On a production server this should be set up using a 
process monitor such as [god](http://godrb.com/). A new worker process can be spawned using the following command in 
the webroot:

```
./framework/sake dev/resque/run queue=first_build,update
```

## Tasks

Run each of the following tasks in order the first time you set up the site to ensure you have a full database 
of modules to work with.

1. `framework/sake dev/tasks/UpdateSilverStripeVersionsTask`: Updates the available SilverStripe versions.
2. `framework/sake dev/tasks/UpdateAddonsTask`: Runs the add-on updater.
3. `framework/sake dev/tasks/DeleteRedundantAddonsTask`: Deletes addons which haven't been updated
   from packagist in a specified amount of time, which implies they're no longer available there.
4. `framework/sake dev/tasks/BuildAddonsTask`: Manually build addons, downloading screenshots
   and a README for display through the website. There's no need to set up a cron job
   for this task if you're using the resque queue.
5. `framework/sake dev/tasks/SilverStripe-Elastica-ReindexTask`: Defines and refreshes the elastic search index.
6. `framework/sake dev/tasks/CacheHelpfulRobotDataTask`: Caches Helpful Robot scores and data, so they can
   be displayed on listing and detail pages, for each addon. This also removes modules that cannot be loaded
   by requests to their repository URLs.

## Front-end build tooling

The site uses [LESS](http://lesscss.org) for compiling CSS.

One way to compile it is through [Grunt](http://gruntjs.org), which requires you to install 
[NodeJS](http://nodejs.org) first.

```
npm install -g grunt grunt-cli
npm install --save-dev
```

To compile, run:

```
grunt less
```

To watch for file changes, run:

```
grunt watch
```
