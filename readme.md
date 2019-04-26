# Woningdossier

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Ecodenl/Woningdossier/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/Ecodenl/Woningdossier/?branch=develop)
[![Code Coverage](https://scrutinizer-ci.com/g/Ecodenl/Woningdossier/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/Ecodenl/Woningdossier/?branch=develop)
[![Build Status](https://scrutinizer-ci.com/g/Ecodenl/Woningdossier/badges/build.png?b=develop)](https://scrutinizer-ci.com/g/Ecodenl/Woningdossier/build-status/develop)

To contribute to this project you must assign copyright of your contribution to Econobis B.V. 
To do this, include the following filled-in copyright assignment form in your patch: [Individual contributor assignment agreement](https://www.duurzameenergie.org/f/files/download/econobis/licensie/econobis-and-hoomdossier-individual-contributor-assignment-agreement.pdf)

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
- Docker (will follow later)
- Homestead

### The application

## PHP version
PHP 7.1 is the current targeted version as this is required by Laravel 5.5.

## Conventions
The woningdossier is based on Laravel 5.5 LTS. Our goal is to confirm as much as 
possible to the conventions by the framework (i.e. PSR-4 autoloading).

### Code conventions
We follow the [Symfony coding standards](https://symfony.com/doc/current/contributing/code/standards.html).
This is persuaded via PHP-CS-Fixer. [How to install PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer#installation)

## Pull requests
