# Up!

[![Build Status](https://travis-ci.org/mothership-ec/up.svg?branch=develop)](https://travis-ci.org/mothership-ec/up)


**Up!** is a simple library for running <a href="http://getcomposer.org">Composer</a> commands from within your application.

**Up!** works by extending Composer's internal library and adding some simple methods which handle most of the configuration and setup for you.

**Up!** currently supports three key Composer features:

+ **Update** - Update all modules as specified in the `composer.json` file.
+ **Install** - Synchronise all modules with versions specified in the `composer.lock` file, unless no file is present then use the `composer.json` file
+ **Create project** - Create a project from a package on <a href="http://packagist.org">packagist.org</a>

**Up!** will assume that the relevant Composer configuration files are in the current working directory, unless specified via the `setBaseDir()` method

## Usage examples

```php
    <?php

    use Mothership\Up\Up;

    $up = new Up;

    // Update your project from the current working directory
    $up->update();

    // Update your project from a different directory
    $up->setBaseDir('/path/to/project')->update();

    // Synchronise your project with the `composer.lock` file
    $up->install();

    // Synchronise your project from a `composer.lock` file in a different directory
    $up->setBaseDir('/path/to/project')->install();

    // Create a new project from a Composer package
    $up->createProject('mothership-ec/mothership');

    // Create a new project from a Composer package in a different directory
    $up->setBaseDir('/path/to/project')->createProject('mothership-ec/mothership');
```

## Installation

**Up!** must be installed using Composer by adding `mothership-ec/up` to your `composer.json` file. See <a href="https://getcomposer.org/doc/01-basic-usage.md">the Composer documentation</a> for more information.

## Caveats

**Up!** currently uses <a href="http://github.com/mothership-ec/composer">a forked version of Composer</a> as Composer itself does not utilise semantic versioning
