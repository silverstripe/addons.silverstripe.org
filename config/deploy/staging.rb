server "addons.silverstripe.org", :app, :web, :db, :primary => true
set :deploy_to, "/sites/addons-staging"
set :user, "addons-staging"
set :webserver_group, "addons-staging"
set :port, 2222