# Setting up with vagrant + ansible

Prerequisites:

- vagrant
- virtualbox
- virtualbox guest additions

## Set up the box
To create the virtual machine, start your terminal application and go to the 
project directory. From there execute the command

`vagrant up`

## Add IP to your hosts file

Note that root / administrator rights are needed to update your hosts file.

### Linux and Mac OS
Edit your `/etc/hosts` file and add the following entry:

    192.168.10.99   woondossier.vm

### Windows
Edit your hosts file at `C:\Windows\System32\drivers\etc\hosts` and add the 
following entry:

    192.168.10.99   woondossier.vm
    

## Accessing the virtual machine
Via your terminal, go to  your project directory and execute the command

`vagrant ssh`

Your code folder is mounted on `/vagrant/`, so once you're in the VM, use the
command `cd /vagrant/` to get to your code directory. To exit the VM, just use 
the `exit` command.

## Shutting down the virtual machine
Note that the VM *will* take resources from your machine to run. Therefore it's 
a good habit to shutdown or suspend the machine when you're done working on the 
project. You can do this by going to your code directory (on your local machine, 
not the VM) and enter the command

`vagrant suspend`

Next time you're going to work on the project, you can use `vagrant up` again 
to pick up where you left off.