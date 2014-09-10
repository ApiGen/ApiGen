# ApiGen - PHP source code API generator

[![Build Status](https://travis-ci.org/apigen/apigen.svg?branch=develop)](https://travis-ci.org/apigen/apigen)
[![Downloads this Month](https://img.shields.io/packagist/dm/apigen/apigen.svg)](https://packagist.org/packages/apigen/apigen)
[![Latest stable](https://img.shields.io/packagist/v/apigen/apigen.svg)](https://packagist.org/packages/apigen/apigen)


**[WIP] In renewal process, stay tuned.**

So what's the output? Look at [Doctrine ORM API](http://www.doctrine-project.org/api/orm/2.4/), [Nette API](http://api.nette.org/) or [Kdyby API](https://api.kdyby.org).


## Features

* Detailed documentation of classes, functions and constants
* [Highlighted source code](http://api.nette.org/source-Application.UI.Form.php.html)
* Experimental support of [traits](https://api.kdyby.org/class-Nextras.Application.UI.SecuredLinksControlTrait.html).
* A page with:
    - [trees of classes, interfaces, traits and exceptions](https://api.kdyby.org/tree.html)
	- [list of deprecated elements](http://api.nette.org/deprecated.html)
	- Todo tasks
* Support for:
    - docblock templates
	- @inheritdoc
	- {@link}
* Active links in @see and @uses tags.
* Links to internal PHP classes and PHP documentation.
* [Links to the start line](http://api.nette.org/2.2.3/Nette.Application.UI.Control.html#_redrawControl) in the highlighted source code for every described element.
* [List of known subclasses and implementers](https://api.kdyby.org/class-Kdyby.Doctrine.EntityRepository.html)
* Google CSE support with suggest.
* Google Analytics support.
* Support for custom templates.


## Installation

The best way to install Apigen is via [Composer](https://getcomposer.org/):

```sh
$ composer require apigen/apigen:~2.8
```

## Usage

First, we create config file, e.g. `apigen.neon` and set required parameters.

If you haven't heard about .neon yet, [go check it](http://ne-on.org). It's similar to .yaml, just nicer.

### Minimal configuration

```yaml
source:
    - src # directory API is generated for
    - tests/ApiGen/Generator.php # or file
destination: api # target dir for documentation
```

Then run ApiGen passing your config:

```sh
php apigen --config apigen.neon
```

That's it!


### Optional configuration

```sh
# list of allowed extensions
extensions:
	- php # default

# directories and files matching this file mask will not be parsed
exclude:
	- */tests/*
	- */vendor/*

# this files will be included in class tree, but will not create a link to their documentation
# either files
skipDocPath:
    - * <mask>``` # mask

# or with certain name prefix
skipDocPrefix:
    - Nette

# markup that manages comments converting to html
markup: markdown # default [texy]

# character set of source files; if you use only one across your files, we recommend you name it
charset:
	# default
    - auto # will choose from all supported (starting with UTF-8), slow and not 100% reliable
    # e.g.
    - UTF-8
    - Windows-1252

# elements with this name prefix will be considered as the "main project" (the rest will be considered as libraries)
main: ApiGen

# title of generated documentation
title: ApiGen API

# base url used for sitemap (useful for public doc)
baseUrl: http://nette.org

# custom search engine id, will be used by search box
googleCseId: 011549293477758430224

# Google Analytics tracking code
googleAnalytics: UA-35236-5

# path to template config file
templateConfig: .. # default @todo

# the way elements are grouped in menu
groups: auto # default [other options: namespace, packages, none], auto will detect namespace first, than packages

# element supported by autocomplete in search input
autocomplete:
	# default
	- classes
	- constants
	- functions
	# other
	- methods
	- properties
	- classconstants

# access levels of included method and properties
accessLevels:
	# default
	- public
	- protected
	# other
	- private

# include elements marked as @internal/{@internal}
internal: false # default [true]

# generate documentation for PHP internal classes
php: true # default [false]

# generate tree view of classes, interfaces, traits and exceptions
tree: true # default [false]

# generate documentation for deprecated elements
deprecated: false # default [false]

# generate list of tasks with @todo annotation
todo: false # default [true]

# add link to ZIP archive of documentation
download: false # default [true]

# delete files generated in the previous run.
wipeout: true # default [false]

# display additional information (exception trace) in case of an error
debug: false # default [true]
```


### Performance

When generating documentation of large libraries, **not loading the Xdebug PHP extension**  will improve performance.
