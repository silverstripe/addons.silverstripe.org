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
    command => "/usr/bin/apt-get update && touch ${sentinel}",
    onlyif  => "/usr/bin/env test \\! -f ${sentinel} || /usr/bin/env test \\! -z \"$(find /etc/apt -type f -cnewer ${sentinel})\"",
    timeout => 3600,
  }
}
class { 'apt_get_update':
  stage => pre,
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

include nginx::install

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

class{'redis':
	redis_bind_address => '127.0.0.1',
}

class{'ss::elasticsearch':
  clustername => 'addons',
  heap_size => '256m'
}