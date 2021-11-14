Symfony Demo Project
========================

This is a Demo Project using
 * PHP7.4+
 * Symfony 3.4 
 * Doctrine with SQLite
 * Twig
 * Bootstrap 5
 * JQuery 3.6
 * PHPUnit 8.5
 
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

# Requirements

PHP 7.4 or higher;

PDO-SQLite PHP extension enabled;
``` 
sudo apt install php-sqlite3
```


 # Installation

 1. Clone or download the repository
```python
 git clone git@github.com:DuminduP/SymfonyDemo.git
```
 2. Run composer install
 ``` 
 composer install
 ```
 
 3. It will ask for your input, such as SQLite database file path, photo upload directory. It will work file with defaults. Just press enter. 
 4. Run unit test 
 ```
 phpunit
 ```
 5. Run web server.
 ```
 php bin/console server:run
 ```
 6. All good :) visit [http://127.0.0.1:8000/](http://127.0.0.1:8000/)
 


