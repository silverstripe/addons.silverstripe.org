# This replaces the default reports@silverstripe.com address 
# Change this to your own email address for testing 
$reports_email_address = "ingo@silverstripe.com" 

# All vagrant hosts should have this
include vagrant

# If we don't run apt-get update, installing openjdk will fail.
stage { pre: before => Stage[main] }
class apt_get_update {
  $sentinel = "/var/lib/apt/first-puppet-run"

  exec { "initial apt-get update":
    # Use German mirrors instead of NZ
    command => "/usr/bin/perl -pi -e 's/\\.nz/\\.de/' /etc/apt/sources.list && /usr/bin/apt-get update && touch ${sentinel}",
    # command => "/usr/bin/apt-get update && touch ${sentinel}",
    onlyif  => "/usr/bin/env test \\! -f ${sentinel} || /usr/bin/env test \\! -z \"$(find /etc/apt -type f -cnewer ${sentinel})\"",
    timeout => 3600,
  }
}
class { 'apt_get_update':
  stage => pre,
}

# Required for addon README downloads
package { 'subversion':
  ensure   => 'installed',
}

include ss::lamp

# Canonical SilverStripe LAMP configuration
ss::virtualhost {
  "addons": domainname => "addons.silverstripe.org",
    aliases => [ "localhost", "127.0.0.1", "addons.silverstripe.org", "addons.sites.silverstripe.org" ],
    deployers => [ sminnee, hfried, ischommer, "qa-servers", "ajshort-au" ],

    mysql_username => "addons",
    mysql_password => "password",
    mysql_database => "addons",
    git_deploy => true;
}


# TODO Fix port forwarding issue
# $apache_port = 8080
# include nginx::install
# nginx::vhost {
#     "addons": domainname => "addons.silverstripe.org",
#     aliases => [ "addons.sites.silverstripe.org" ],
#     docroot => "/sites/addons/www",
#     errorlog => "/sites/addons/logs/nginx.error.log",
#     accesslog => "/sites/addons/logs/nginx.access.log";
# }  

sudo::conf {
  'addons':
    priority => 10,
    content => "addons         ALL=(www-data) NOPASSWD: ALL";
}

cron { UpdateExtensionsTask:
  command => "php /sites/addons/www/framework/cli-script.php dev/tasks/UpdateAddonsTask",
  user    => www-data,
  hour    => '*',
  minute  => '*/30'
}
cron { UpdateSilverStripeVersionsTask:
  command => "php /sites/addons/www/framework/cli-script.php dev/tasks/UpdateSilverStripeVersionsTask",
  user    => www-data,
  hour    => '*',
  minute  => '0'
}
cron { SilverStripe-Elastica-ReindexTask:
  command => "php /sites/addons/www/framework/cli-script.php dev/tasks/SilverStripe-Elastica-ReindexTask",
  user    => www-data,
  hour    => '*',
  minute  => '0'
}

# God worker monitor: Required for Resque
# class {'god':
#   use_rvm => false,
#   ruby_version => '1.8.7',
#   service_provider => init,
# }
# $dir = '/sites/addons/www/'
# $uid = 'www-data'
# $gid = 'www-data'
# $env = "{'QUEUE' => 'first_build,update', 'APP_INCLUDE' => 'mysite/bin/silverstripe-resque.php'}"
# $start = 'php vendor/chrisboulton/php-resque/bin/resque'
# file { "/etc/god/conf.d/addons.god":
#   mode => 0755,
#   owner => 'www-data',
#   group => 'www-data',
#   content => template("god/confd-sample.god.erb"),  
# }

# Resque: We use php-resque, but the original is handy for its web UI.
# package { 'resque':
#   ensure   => 'installed',
#   provider => 'gem',
# }

# Redis: Queue backend (we use it through php-resque)
class{'redis':
	redis_bind_address => '127.0.0.1',
}

class{'ss::elasticsearch':
  clustername => 'addons',
  heap_size => '256m'
}