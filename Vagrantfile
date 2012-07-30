Vagrant::Config.run do |config|
    config.vm.box = "precise32"

    config.vm.provision :shell, :path => ".provision/provision.sh"

    config.vm.forward_port 80, 8888
end
