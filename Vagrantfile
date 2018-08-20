# Based on defaults for SilverStripe Ltd projects.
# See https://silverstripe.atlassian.net/wiki/spaces/DEV/pages/401506576.

Vagrant.configure(2) do |config|
  # Webroot defaults.
  # Don't change this to public/, it will be auto-detected in the box
  WEBROOT_HOST = "."
  WEBROOT_GUEST = "/var/www/mysite/www"

  # Change this IP to avoid clashes with other running virtual machines
  # on your own host machine virtual network
  config.vm.network "private_network", ip: "192.168.33.127"

  # Change to a unique host name.
  # Sets automatically when using the vagrant-hostsupdater plugin.
  # Use a *.vagrant top level domain to get built-in SSL certificates
  config.vm.hostname = "ssaddons.vagrant"

  # Handy for subsites
  #config.hostsupdater.aliases = ["mysubsite1.vagrant", "mysubsite2.vagrant"]

  # Choose an SSP or CWP base box
  config.vm.box = "silverstripeltd/dev-ssp"
  # config.vm.box = "silverstripeltd/dev-cwp"

  # Update memory settings for Virtualbox
  # See https://www.vagrantup.com/docs/virtualbox/configuration.html#vboxmanage-customizations
  # Needs additional config for other providers, see https://www.vagrantup.com/docs/providers/
  config.vm.provider "virtualbox" do |v, override|
    v.memory = 1024
    v.cpus = 2
  end

  # Configure webroot and mount options
  # See https://github.com/gael-ian/vagrant-bindfs
  if Vagrant.has_plugin?("vagrant-bindfs") then
    # Useful for OSX (for optimal performance)
    config.vm.synced_folder WEBROOT_HOST, "/vagrant-nfs", type: "nfs"
    config.bindfs.bind_folder "/vagrant-nfs", WEBROOT_GUEST,
    force_user:   'vagrant',
    force_group:  'vagrant',
    perms:        'u=rwX:g=rD:o=rD',
    o:            'nonempty'
  else
    # For Windows and Linux
    config.vm.synced_folder WEBROOT_HOST, WEBROOT_GUEST
  end

  # Reduce disk space by cloning from master VM
  # See https://www.vagrantup.com/docs/virtualbox/configuration.html#linked-clones
  config.vm.provider 'virtualbox' do |v|
    v.linked_clone = true
  end

  # Optional apt and composer cache (shared beween boxes)
  # See https://github.com/fgrehm/vagrant-cachier
  if Vagrant.has_plugin?("vagrant-cachier")
    config.cache.scope = :box
    config.cache.enable :apt
    config.cache.enable :composer
  end

  # Prevent "stdin: not a tty" errors
  config.ssh.shell = "bash -c 'BASH_ENV=/etc/profile exec bash'"

  # Forward SSH agent, important for private git checkouts
  config.ssh.forward_agent = true

  # Set default directory to webroot
  config.vm.provision "shell",
    inline: "echo 'cd #{WEBROOT_GUEST}' >> /home/vagrant/.bashrc",
    name: "default dir"

  # Custom provisioning for ssaddons
  config.vm.provision "shell",
    inline: "exec /var/www/mysite/www/.vagrant-provision.sh",
    name: "ssaddons provisioning"
  end
