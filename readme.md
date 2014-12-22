# ApiGen - PHP source code API generator

[![Build Status](https://img.shields.io/travis/apigen/apigen.svg?style=flat-square)](https://travis-ci.org/apigen/apigen)
[![Quality Score](https://img.shields.io/scrutinizer/g/ApiGen/ApiGen.svg?style=flat-square)](https://scrutinizer-ci.com/g/ApiGen/ApiGen)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/ApiGen/ApiGen.svg?style=flat-square)](https://scrutinizer-ci.com/g/ApiGen/ApiGen)
[![Downloads this Month](https://img.shields.io/packagist/dm/apigen/apigen.svg?style=flat-square)](https://packagist.org/packages/apigen/apigen)
[![Latest stable](https://img.shields.io/packagist/v/apigen/apigen.svg?style=flat-square)](https://packagist.org/packages/apigen/apigen)


ApiGen generates nice looking and user-friendly documentation.

Just look at [Doctrine ORM API](http://www.doctrine-project.org/api/orm/2.4/) or [Nette API](http://api.nette.org/).


## Features

- Detailed documentation of classes, functions and constants
- [Highlighted source code](http://api.nette.org/source-Application.UI.Form.php.html)
- Support of [traits](https://api.kdyby.org/class-Nextras.Application.UI.SecuredLinksControlTrait.html)
- A page with:
    - [trees of classes, interfaces, traits and exceptions](https://api.kdyby.org/tree.html)
	- [list of deprecated elements](http://api.nette.org/deprecated.html)
	- Todo tasks
- Support for docblock templates flavored with Markdown
- [Links to the start line](http://api.nette.org/2.2.3/Nette.Application.UI.Control.html#_redrawControl) in the highlighted source code for every described element
- [List of known subclasses and implementers](https://api.kdyby.org/class-Kdyby.Doctrine.EntityRepository.html)
- Support for custom templates


## Installation

### 1. As a PHAR (recommended)

1. Download [ApiGen RC5](http://apigen.org/apigen.phar)

2. Run ApiGen with source and destination options:

```sh
php apigen.phar generate -s src -d ../my-project-api
```
	
To omit cli options just create `apigen.neon` file in your project using [Neon](http://ne-on.org) syntax.

```yaml
source:
    - src

destination: ../my-project-api
```

For global installation, see [documentation](doc/installation.md).


### 2. Using Composer as dependency of your project

```sh
composer require apigen/apigen --dev
```

Then run with options as above:

```sh
php vendor/bin/apigen generate -s src -d ../my/project-api
```


## Options

```yaml
# list of scanned file extensions (e.g. php5, phpt...)
extensions: [php]

# directories and files matching this file mask will not be parsed
exclude:
	- tests/
	- vendor/
	- *Factory.php

# similar to above, but this files will be included in class tree
skipDocPath:
    - *Component\Console

# character set of source files; if you use only one across your files, we recommend you name it
charset: [UTF-8]

# elements with this name prefix will be considered as the "main project" (the rest will be considered as libraries)
main: ApiGen

# title of generated documentation
title: ApiGen API

# base url used for sitemap (useful for public doc)
baseUrl: http://api.apigen.org

# custom search engine id, will be used by search box
googleCseId: 011549293477758430224

# Google Analytics tracking code
googleAnalytics: UA-35236-5

# choose ApiGen template theme
templateTheme: default # or: bootstrap

# want to use individual templates, higher priority than option templateTheme
templateConfig: my/template/config.neon

# the way elements are grouped in menu
groups: auto # also: namespace, packages, none; auto will detect namespace first, than packages

# access levels of included method and properties
accessLevels: [public, protected] # also [private]

# include elements marked as @internal/{@internal}
internal: false

# generate documentation for PHP internal classes
php: true

# generate highlighted source code for elements
sourceCode: true

# generate tree view of classes, interfaces, traits and exceptions
tree: true

# generate documentation for deprecated elements
deprecated: false

# generate list of tasks with @todo annotation
todo: false

# add link to ZIP archive of documentation
download: false
```


## Detailed documentation

- [list of all supported annotations](doc/supported-annotations.md)
- [online apps built with ApiGen](doc/built-with-apigen.md)


## Performance

When generating documentation of large libraries, **not loading the Xdebug PHP extension**  will improve performance.
