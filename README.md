SilverStripe Add-ons
====================

This is the [SilverStripe](http://silverstripe.org) add-ons listing site
project. It aggregates SilverStripe packages from [Packagist](http://packagist.org).

* [SilverStripe Add-ons](http://addons.silverstripe.org)
* [GitHub Project](https://github.com/silverstripe/addons.silverstripe.org)
* [Issue Tracker](https://github.com/silverstripe/addons.silverstripe.org/issues)

Basic Installation
============

 * `cd public && composer install --no-dev`
 * Install and run dependencies (see below)
 * Configure elastic search in `mysite/_config/injector.yml`
 * Run all tasks to populate database (see below)

Full Installation with VM
============

While you can set up your dev environment as usual,
its likely differing from the production configuration.
In order to stay as close as possible to the 

**IMPORTANT**: These instructions currently assume that you
have access to resources internal to SilverStripe Ltd.
We're working on opening those up.

First off, download and install some dependencies:

 * [Puppet](https://puppetlabs.com/puppet/puppet-open-source/), as well as 
 * [VirtualBox](https://www.virtualbox.org/)
 * [Vagrant](http://downloads.vagrantup.com/)

Then run through the following shell script:

  # Get the base Vagrant box (downloads ~500MB)
  vagrant box add squeeze http://tools.silverstripe.com/vagrant/squeeze.box
  sudo chown `whoami` /Users/`whoami`/.vagrant.d/insecure_private_key

  # Install SilverStripe's Puppet Manifests
  git clone git@gitorious.silverstripe.com:infrastructure/puppet.git puppet
  (cd puppet && ./checkout)

  # Create VM (this will take a few minutes to create a new VM)
  vagrant up

Now you should be able to browse the website under `http://localhost:3000`,
and access the VM on SSH via `vagrant ssh`. To shut down the VM again, use `vagrant halt`.

Dependencies
============

Elastic Search
--------------

[Elastic search](www.elasticsearch.org) is used to provide add-on package indexing and searching. If a
local installation of elastic search is used, the following configuration can be used
in `mysite/_config/injector.yml`:

    Injector:
      SilverStripe\Elastica\ElasticaService:
        constructor:
          - %$Elastica\Client
          - addons

You should run the elastic search reindex task to create the mappings after installation.

Resque
------

A [PHP implementation of resque](https://github.com/chrisboulton/php-resque) 
is used to provide background building of add-ons
details. As such an installation of [redis](http://redis.io/) must be present. If you wish to use a
remote server, you can configure the `ResqueService` constructor parameters to
specify the backend using the injector system (see `mysite/_config/injector.yml`).

To actually run the background tasks you need to spawn worker processes. On a
production server this should be set up using a process monitor such as god. A
new worker process can be spawned using the following command in the webroot:

    QUEUE=first_build,update APP_INCLUDE=mysite/bin/silverstripe-resque.php php vendor/chrisboulton/php-resque/bin/resque

Tasks
============

 * `sake dev/tasks/UpdateAddonsTask`: Runs the add-on updater.
 * `sake dev/tasks/UpdateSilverStripeVersionsTask`: Updates the available SilverStripe versions.
 * `sake dev/tasks/SilverStripe-Elastica-ReindexTask`: Defines and refreshes the elastic search index.

LESS Compilation
===========

The site uses [LESS](http://lesscss.org) for compiling CSS.
One way to compile it is through [Grunt](http://gruntjs.org),
which requires you to install [NodeJS](http://nodejs.org) first.

  npm install -g grunt grunt-cli
  npm install --save-dev

To compile, run:

  grunt less

To watch for file changes, run:

  grunt watch

Deployment
============

Deployment is handled through [Capistrano](https://github.com/capistrano/capistrano).

Installation:

	gem install capistrano capistrano-ext railsless-deploy

Usage:

	cap staging deploy:setup
	cap staging deploy:update
	cap production deploy:setup
	cap production deploy:update