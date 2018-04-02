interview
=========

A Symfony project created on April 2, 2018, 11:57 am.
# symfony(v3.3)-interview

Hi there,
below are things I have done to create Symfony3 project.

Created project by typing in terminal:
**symfony new interview 3.3**

Removed any database if existed:
**php bin/console doctrine:database:drop --force**

Created new database:
**php bin/console doctrine:database:create**

Created getters and setters for entity Offer:
**php bin/console doctrine:generate:entities AppBundle/Entity/Offer**

Updated database structure:
**php bin/console doctrine:schema:update --force**

To import data I used below terminal command:
php bin/console app:fetch-offers advertiser_id
e.g. **php bin/console app:fetch-offers 1**


**NOTE:** I have not used docker, if required by future projects I will definitely
use it. I have not been normalizing coding style, don't know your preferences,
I am aware about the need of following standards.

Summary:
I have created 2 files, **Offer.php** and **FetchOffersCommand.php**. It is my first
time for working with Symfony, I used official documentation and helped with
StackOverflow most common issues articles.

To **config.yml** I have added:
charset: utf8mb4
server_version: '5.5'
mapping_types:
    enum: string

**Offer.php** basically creates entity mapped to database with Doctrine.

**FetchOffersCommand.php** is a code for terminal command. 
I used curl to get data, script is checking if entry exists already in database,
if not then entry is inserted to database. 

Database fetched results are available at **database_entries_screenshot.jpeg** file.


