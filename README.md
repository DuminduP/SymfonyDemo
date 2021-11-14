Symfony Demo Project
========================

This is a Demo Project using
 * Symfony 3.4 
 * Doctrine with SQLite
 * Twig
 * PHP7.4+
 
The goal is to develop an address book in which you can add, edit and delete entries. Also, have an overview of all contacts.

The address book should contain the following data:
 - Firstname
 - Lastname
 - Street and number
 - Zip
 - City
 - Country
 - Phonenumber
 - Birthday
 - Email address
 - Picture (optional)

 # Installation

 1. Clone or download the repository
 `https://github.com/DuminduP/SymfonyDemo.git`
 2. Run composer install
 `composer install`
 3. It will ask for your input, such as SQLite database file path, photo upload directory. It will work file with defaults. Just press enter. Put any string on the secret.
 4. Run web server.
 `php bin/console server:run`
 5. All good :) visit [http://127.0.0.1:8000/](http://127.0.0.1:8000/)
 


