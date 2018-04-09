PHP Storm Setup 
===============

To set up the environment to run using Php Storm by Intelli J. Run the following steps.

1. Lets open up the project in PHPStorm now. Open up the folder containing all the files for the DeRiche Project.

2. Now we need to connect the project to the database. Go to view/tools window/database.
   The new window that pops up. Add the database, Click the plus icon, Data Source, SQLite.
   Check if you need to add the driver.
   Then under file point it to DeRiche/var/data.sqlite
   
3. Now we need to run the server to open the app. Go to Run/edit configurations. Add a new configuration, PHP Script.
   In file point it to DeRiche/bin/console
   In arguments put server:run
   
   If you have any errors such as the interpreter. Fix the issue beforehand.
   
   Name the server whatever you will like.
   
   Run the server now by pressing play, go to the link.

4. You can now go to your web browser and go to http://127.0.0.1:8000/admin to create user accounts.
     Create the accounts for admin, user, writer. And login to now start testing the site.