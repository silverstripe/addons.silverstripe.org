server "addons.silverstripe.org", :app, :web, :db, :primary => true
set :deploy_to, "/sites/extensions"
set :user, "extensions"
set :webserver_group, "extensions"
set :port, 2222