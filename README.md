DeRiche
=======

A Symfony project created on September 5, 2017, 8:42 pm.

To get the project running on your machine. Do the following steps.

1. Make sure you have Composer installed on your machine. 

2. cd into where the DeRiche-Agency project lives.

3. Run composer install

3. Run bin/symfony_requirements

4. It should return back with:

  [OK]                                         
  Your system is ready to run Symfony projects

  If not add any other dependencies it may say. Then keep running bin/symfony_requirements until everything is good.
  
5.Then being in the same DeRiche folder run bin/console doctrine:schema:update --force
  Should return schema updated successfully!
  
6. Lets open up the project in PHPStorm now. Open up the folder containing all the files for the DeRiche Project.

7. Now we need to connect the project to the database. Go to view/tools window/database.
   The new window that pops up. Add the database, Click the plus icon, Data Source, SQLite.
   Check if you need to add the driver.
   Then under file point it to DeRiche/var/data.sqlite
   
8. Now we need to run the server to open the app. Go to Run/edit configurations. Add a new configuration, PHP Script.
   In file point it to DeRiche/bin/console
   In arguments put server:run
   
   If you have any errors such as the interpreter. Fix the issue beforehand.
   
   Name the server whatever you will like.
   
   Run the server now by pressing play, go to the link, if it works we are halfway there!
   
 9. Go to app/config/security.yml and comment out everything from lines 36-48
 
 10. Go to app/resources/views/admin/admin.html.twig and comment out everything from lines 36-85
 
 11. You can now rerun the server. In the web browser go to http://127.0.0.1:8000/admin. You can now create user accounts.
     Create the accounts for admin, user, writer. And Login to now start testing the site.
   
   
