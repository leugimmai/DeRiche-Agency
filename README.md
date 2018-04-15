DeRiche
=======

A Symfony project for the DeRiche Agency.

To get the project running on your machine in a local environment. Do the following steps.

1. Make sure to have Composer installed on your machine. It is a PHP package installer. 

2. cd into where the DeRiche-Agency project is located.

3. Run composer install to download all the dependencies needed.

  ```composer install```

3. Run php bin/symfony_requirements. To make sure everything is properley installed.

  ```php bin/symfony_requirements```

4. It should return back with:

  [OK]                                         
  Your system is ready to run Symfony projects

  If not add any other dependencies it may say. Then keep running php bin/symfony_requirements until everything is good.
  
5. Then being in the same DeRiche folder run the command below. Should return schema updated successfully.

  ```php bin/console doctrine:schema:update --force```

6. Run the command below in your terminal to start the local server. You can then go to the link given to visit the project.

```php bin/console server:run```