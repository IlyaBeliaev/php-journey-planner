required_plugins = %w( vagrant-hostsupdater )
required_plugins.each do |plugin|
  system "vagrant plugin install #{plugin}" unless Vagrant.has_plugin? plugin
end

Vagrant.configure(2) do |config|
  config.vm.box = "geerlingguy/ubuntu1604"
  config.vm.box_check_update = false

  config.ssh.forward_agent = true

  config.vm.define "dev-ttt" do |app|
    app.vm.network "private_network", ip: "10.0.0.100"
    app.vm.hostname = "dev-ttt"
    app.vm.synced_folder "./server", "/var/www/api-ttt.local", :create => true, :owner=> 'vagrant', :group=>'www-data', :mount_options => ['dmode=777', 'fmode=777']
    app.vm.synced_folder "./www", "/var/www/ttt.local", :create => true, :owner=> 'vagrant', :group=>'www-data', :mount_options => ['dmode=777', 'fmode=777']

    app.vm.provision :ansible do |ansible|
      ansible.playbook = "playbooks/provision.yml"
    end
  end

  config.vm.provider "virtualbox" do |vb|
    vb.memory = 1024
    vb.cpus = 1
  end

  config.hostsupdater.aliases = [
    "ttt.local",
    "api-ttt.local"
  ]

end
