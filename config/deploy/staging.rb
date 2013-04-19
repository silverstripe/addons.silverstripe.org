server "addons.silverstripe.org", :app, :web, :db, :primary => true
set :deploy_to, "/sites/extensions-staging"
set :user, "extensions-staging"
set :webserver_group, "extensions-staging"
set :port, 2222