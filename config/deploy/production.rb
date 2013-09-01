server "addons.silverstripe.org", :app, :web, :db, :primary => true
set :deploy_to, "/sites/addons"
set :user, "addons"
set :webserver_group, "addons"
set :port, 2222