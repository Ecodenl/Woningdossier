#If your Vagrant version is lower than 1.5, you can still use this provisioning
#by commenting or removing the line below and providing the config.vm.box_url parameter,
#if it's not already defined in this Vagrantfile. Keep in mind that you won't be able
#to use the Vagrant Cloud and other newer Vagrant features.
Vagrant.require_version ">= 1.5"

# Check to determine whether we're on a windows or linux/os-x host,
# later on we use this to launch ansible in the supported way
# source: https://stackoverflow.com/questions/2108727/which-in-ruby-checking-if-program-exists-in-path-from-ruby
def which(cmd)
    exts = ENV['PATHEXT'] ? ENV['PATHEXT'].split(';') : ['']
    ENV['PATH'].split(File::PATH_SEPARATOR).each do |path|
        exts.each { |ext|
            exe = File.join(path, "#{cmd}#{ext}")
            return exe if File.executable? exe
        }
    end
    return nil
end

Vagrant.configure(2) do |config|

    config.vm.provider :virtualbox do |v|
        v.name = "woondossier.vm"
        v.customize [
            "modifyvm", :id,
            "--name", "woondossier.vm",
            "--memory", 1024,
            "--natdnshostresolver1", "on",
            "--cpus", 1,
        ]
    end

    config.vm.box = "ubuntu/xenial64"
    config.vm.box_url = "https://app.vagrantup.com/ubuntu/boxes/xenial64/versions/20180215.0.0/providers/virtualbox.box"

    config.vm.network :private_network, ip: "192.168.10.99"
    config.ssh.forward_agent = true

    # Ansible doesn't run on Windows, so we install & run Ansible on the
    # target VM if it's Ubuntu > 14.04 as this installs Ansible >= 2.2 (and
    # MySQL 5.7) which fixes the mysql_user 'password' setting for
    # redefining root passwords

    #if which('ansible-playbook')
    #    config.vm.provision "ansible" do |ansible|
    #        ansible.playbook = "ansible/playbook.yml"
    #        ansible.inventory_path = "ansible/inventories/dev"
    #        ansible.limit = 'all'
    #    end
    #else
    #    config.vm.provision :shell, path: "ansible/windows.sh", args: ["woondossier.vm"]
    #end

    config.vm.provision :shell, path: "ansible/windows.sh", args: ["woondossier.vm"]

    config.vm.synced_folder "./", "/vagrant", type: "nfs", mount_options: ["actimeo=1"]
end
