# Woningdossier

![develop](https://github.com/Ecodenl/Woningdossier/actions/workflows/build.yml/badge.svg)
![develop](https://github.com/Ecodenl/Woningdossier/actions/workflows/tests.yml/badge.svg)
![develop](https://github.com/Ecodenl/Woningdossier/actions/workflows/static-analysis.yml/badge.svg)



To contribute to this project you must assign copyright of your contribution to Econobis B.V. 
To do this, include the following filled-in copyright assignment form in your patch: [Individual contributor assignment agreement](https://alfresco.econobis.nl/share/page/site/projectgroep-econobis/document-details?nodeRef=workspace://SpacesStore/beaaae10-302a-4f8e-aceb-1040360ba4b7)

Econobis B.V. is 100% daughter of Coöperatieve Vereniging Energie Coöperaties op Orde UA. More information can be viewed on [www.econobis.nl](https://www.econobis.nl/).

## Why all this legal stuff ? 
We want clean renewable energy. We want a digital energy market which is democratic controlled, fair and accessible for all people at all times. 
We are cooperatives, because we believe that members, in our case normal people, are the owners of everything we do. 
That is why we choose for Open Source software. It is also democratic, fair and accessible for everybody. 
But we want to make sure that it remains Open Source. That is why we need the legal stuff.
  

## Tech: Setup

### The server
There are multiple ways to get the development environment up and running. How 
you run it is a matter of taste.

Options:
- [Vagrant + ansible](docs/setup/vagrant-ansible.md)
- [Docker](docs/setup/docker.md)

## The application

### PHP version
PHP 7.4 is the current targeted version as this is the lowest actively supported PHP version.

### Conventions
The Hoomdossier is based on Laravel 6.0 LTS. Our goal is to confirm as much as 
possible to the conventions by the framework (i.e. PSR-4 autoloading).

### Code conventions
We follow the [Symfony coding standards](https://symfony.com/doc/current/contributing/code/standards.html).
This is persuaded via PHP-CS-Fixer. [How to install PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer#installation)

### Translations
We try to use as much of the translation files as possible, these will be stored in the database with the following commands.

    1: php artisan translations:import
    2: php artisan languageline:to-question-structure --groups-to-convert=cooperation/tool/general-data/building-characteristics

[Laravel translations import](https://github.com/WeDesignIt/laravel-translations-import)
