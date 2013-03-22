server "fozzie.rackspace.silverstripe.com", :app, :web, :db, :primary => true
set :deploy_to, "/sites/addons-staging"
set :user, "addons-staging"
set :webserver_group, "addons-staging"
set :port, 2222