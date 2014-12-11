# ApiGen - PHP source code API generator

[![Build Status](https://travis-ci.org/apigen/apigen.svg?branch=master)](https://travis-ci.org/apigen/apigen)
[![Downloads this Month](https://img.shields.io/packagist/dm/apigen/apigen.svg)](https://packagist.org/packages/apigen/apigen)
[![Latest stable](https://img.shields.io/packagist/v/apigen/apigen.svg)](https://packagist.org/packages/apigen/apigen)


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

### As a PHAR (recommended)

1. Download `apigen.phar` via installer:

	```sh
	$ curl -sS http://apigen.org/installer | php
	```

2. Create `apigen.neon` file in your project. This is basic example only with all required items. The file uses [Neon](http://ne-on.org) syntax.

	```yaml
	source:
	    - src

	destination: api
	```

3. Run ApiGen:

	```sh
	php apigen.phar generate
	```

For global installation, see [documentation](doc/installation.md).


### Using Composer globally

Alternatively, you can install ApiGen via composer global .

```sh
composer global require apigen/apigen
```

Run:

```sh
~/.composer/vendor/bin/apigen generate
```

If you add `~/.composer/vendor/bin` to your `PATH`, you can run `bin/apigen generate` instead.


### Using Composer as dependency of your project

Install package:

```sh
composer require apigen/apigen --dev
```

Run:

```sh
php vendor/bin/apigen generate
```


## Options

```yaml
# list of scanned file extensions (e.g. php5, phpt...)
extensions:
	- php # default

# directories and files matching this file mask will not be parsed
exclude:
	- tests/
	- vendor/
	- *Factory.php

# this files will be included in class tree, but will not create a link to their documentation
skipDocPath:
    - *<mask>``` # mask

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
baseUrl: http://api.apigen.org

# custom search engine id, will be used by search box
googleCseId: 011549293477758430224

# Google Analytics tracking code
googleAnalytics: UA-35236-5

# choose ApiGen own template theme
templateTheme: default # default [other options: bootstrap]

# want to use individual templates, higher priority than option templateTheme
templateConfig: path/to/individual/template-folder/config.neon

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

# generate highlighted source code for elements
sourceCode: true # default [false]

# generate tree view of classes, interfaces, traits and exceptions
tree: true # default [false]

# generate documentation for deprecated elements
deprecated: false # default [false]

# generate list of tasks with @todo annotation
todo: false # default [true]

# add link to ZIP archive of documentation
download: false # default [true]
```


## Detailed documentation

- [list of all supported annotations](doc/supported-annotations.md)
- [online apps built with ApiGen](doc/built-with-apigen.md)


## Performance

When generating documentation of large libraries, **not loading the Xdebug PHP extension**  will improve performance.
