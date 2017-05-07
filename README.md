# Smart and Readable Documentation for your PHP project

[![Build Status](https://img.shields.io/travis/ApiGen/ApiGen/master.svg?style=flat-square)](https://travis-ci.org/ApiGen/ApiGen)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/ApiGen/ApiGen.svg?style=flat-square)](https://scrutinizer-ci.com/g/ApiGen/ApiGen)
[![Downloads](https://img.shields.io/packagist/dt/apigen/apigen.svg?style=flat-square)](https://packagist.org/packages/apigen/apigen/stats)
[![Latest stable](https://img.shields.io/packagist/v/apigen/apigen.svg?style=flat-square)](https://packagist.org/packages/apigen/apigen)

Just look at [Nette API](http://api.nette.org/), [CakePHP API](http://api.cakephp.org/3.4/) or [Doctrine API](http://www.doctrine-project.org/api/orm/2.5/).


## Install

```bash
composer require --dev apigen/apigen
```

## Usage

Generate API docs by passing single source and destination options:

```bash
vendor/bin/apigen generate src --destination docs
```

Or generate API docs for multiple directories:

```bash
vendor/bin/apigen generate src tests --destination docs
```

## Configuration

Below is a minimal example configuration. Save it as a `apigen.neon` file in
the root of your project:

```yaml
parameters:
    source: [src]           # 1+ files or directories to scan PHP classes and functions in
    destination: docs       # directory, where API docs will be generated to
    visibilityLevels: [public, protected] # array
    annotationGroups: [todo, deprecated] # array
    title: "ApiGen Docs" # string
    baseUrl: http://apigen.org/api # string
    overwrite: false # bool
    googleAnalytics: "" # string
```

## DocBlock Annotations

This section provides a list of [PHP DocBlock
annotations](https://www.phpdoc.org/docs/latest/guides/docblocks.html) (tags)
that are supported by ApiGen:

- `@deprected` - indicated that the associated element is deprecated and can be removed in the future version.
- `@internal` - denotes that the associated elements is internal to this application or library and hides it by default.
- `@link` - indicates a relation between the associated element and a page of a website.
- `@param` - documents a single argument of a function or method.
- `@return` - documents the return value of functions or methods.
- `@see` - indicates a reference from the associated element to a website or other elements.
- `@uses` - indicates a reference to (and from) a single associated element.


## Themes

In order to enable a custom theme, you have to provide `themeDirectory` configuration
option in your ApiGen configuration file:

```yaml
parameters:
    themeDirectory: path/to/theme # path to theme's config file
```

## Contributing

Rules are simple:

- **new feature needs tests**
- **all tests must pass**
    ```bash
    composer complete-check
    ```
- **1 feature per PR**

We would be happy to merge your feature then.
