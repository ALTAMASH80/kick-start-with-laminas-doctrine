# kick-start-with-laminas-doctrine
This repository contains the basic structure for your project. It has user management with LmcUserDoctrine and LmcRbacMvc. This repository has the basic modules needed to start your own project from scratch. 

Features / Goals
----------------

* Authenticate via username, email, or both with LmcUser.
* User registration with send email feature by listening events.
  Actual email is not sent but rather a file is created with the message.
* A basic hierarchal system is there for managing roles via doctrine.
* Route Guard is implemented for un-authorized access with LmcRbacMVC un-authorized strategy.
* Permission implementation will be added soon.
* Post-creation model will be added soon.

Installation
------------

* Clone the project
* Then run composer install
* Create a database in PHPMyAdmin and set the name local.php file under the doctrine section.
* Then run command ./vendor/bin/doctrine-module migrations:execute
* Then run command mkdir -p data/{mail,logs}
* Then run composer serve



