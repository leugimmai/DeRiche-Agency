<h1>
    Server Setup
</h1>

<h3>Choose a service</h3>

You can use a virtual machine server, Amazon Web Services, or Digital Ocean.Amazon Web Services and Digital Ocean do have free tiers to host.

This server set up will be done using Ubuntu Server as the platform delivering the DeRiche Auditing Tool

Once you have set up or created an account to have an Ubuntu Server. You will need to SSH into it to add the files and set up the server. You can use PUTTY if you are on Windows and with Mac Digital Ocean and AWS will provide to you the config files to add into terminal.

This will focus on installing and setting up using a Digital Ocean Droplet with Ubuntu Server 16.04.3.



<h3>Steps:</h3>

1. Let us check if there are any needed updates needed for the Ubuntu Server

    ```apt-get update```

2. Then let us install the updates. Enter 'Y' when asked to continue

    ```apt-get upgrade```

3. Uninstall Apache just incase it was installed

    ```apt-get remove apache2```

4. Let us now install Composer, Git, PHP, Nginx. Enter 'Y' when asked to continue.

    ```apt-get install composer git php7.0-fpm nginx php7.0-xml php7.0-sqlite3 php7.0-intl php7.0-mbstring php7.0-gd zip unzip```
    
5. We will have to edit what serves up the web page. Run this command to get into the file to edit.  

```nano /etc/nginx/sites-available/default```

6. Delete everything that is in the file and replace with the server code with everything over from the file nginx.txt that is in the repository.

7. Write out, save the file, and then you exit the nano editor. You should be able to the IP Address created by Digital Ocean to go check. You should get File Not Found.

8. We now need to cd, change directory into the web folder.

```cd /var/www/ ```

9. We will now clone from GitHub into this folder the entire DeRiche Project.

```git clone https://github.com/leugimmai/DeRiche-Agency```

10. If you type ls in the command line you should get back html and DeRiche-Agency

11. Move all the files from DeRiche into var/www

```mv DeRiche-Agency/* .```

12. You should see all the files in var/www if you type up ls in the command line. Next delete unecessary files and folder.

```rm -r DeRiche-Agency/ html/```

13. You should now run composer install while inside the folder /var/www/. When you get prompted to enter paramenters just hit enter to get through them all.

```composer install```

14. Typing bin/symfony_requirements should give you a confirmation saying Your system is ready to run Symfony projects.

```bin/symfony_requirements```

15. Inside of /var/www/ run this command to give proper permissions.

```chown -R www-data:www-data .```

16. Run this command to restart the server.

```service nginx restart```

17. Update the database while inside folder /var/www/

```bin/console doctrine:schema:update --force```

18. Run this command again to fix permissions.

```chown -R www-data:www-data .```
```chwon -R www-data:www-data www/*```

19. You should now be able to go to the ipaddr/admin to create users for the site.

22. The version we have so far does not have the security files put in. Once you've made your admin username and password download and replace app/resources/views/admin/admin.html.twig and app/config/security.yml. You can download it off one of our github branches.
```git clone https://github.com/Prussel4/Test.git -b Files ```







