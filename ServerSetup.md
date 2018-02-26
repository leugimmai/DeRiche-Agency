<h1>
    Server Setup
</h1>

Choosing a service for your server: 
    You can use a virtual machine server, Amazon Web Services, or Digital Ocean.
    Amazon Web Services and Digital Ocean do have free tiers to host.

This server set up will be done using Ubuntu Server as the platform delivering the DeRiche Auditing Tool

Once you have set up or created an account to have an Ubuntu Server. You will need to SSH into it to add the files and
set up the server. You can use PUTTY if you are on Windows and with Mac Digital Ocean and AWS will provide to you the config files to add into terminal.

Steps:

1. Let us check if there are any needed updates needed for the Ubuntu Server

```apt-get update```

2. Then let us install the updates

```apt-get upgrade```

3. Let us now install Composer, Git, PHP, Nginx

```apt-get install composer git php7.0-fpm nginx```

4. Uninstall Apache just incase it was installed

```apt-get remove apache2```

