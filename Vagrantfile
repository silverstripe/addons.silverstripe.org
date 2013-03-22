Vagrant.configure("1") do |config|
  config.vm.forward_port 80, 3000
end

Vagrant.configure("2") do |config|
  config.vm.box = "squeeze"
  config.vm.synced_folder ".", "/sites/addons/www/"
  config.vm.provision :puppet do |puppet|
    puppet.manifests_path = ""
    puppet.manifest_file = "vagrant.pp"
    puppet.module_path = "puppet/modules"
    #puppet.options = "--verbose --debug"
  end
end