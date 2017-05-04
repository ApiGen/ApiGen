# Smart and Readable Documentation for your PHP project

[![Build Status](https://img.shields.io/travis/ApiGen/ApiGen/master.svg?style=flat-square)](https://travis-ci.org/ApiGen/ApiGen)
[![Windows Build Status](https://ci.appveyor.com/api/projects/status/p8y6685thhh7mgw0/branch/master?svg=true)](https://ci.appveyor.com/project/ek9/apigen/branch/master)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/ApiGen/ApiGen.svg?style=flat-square)](https://scrutinizer-ci.com/g/ApiGen/ApiGen)
[![Downloads](https://img.shields.io/packagist/dt/apigen/apigen.svg?style=flat-square)](https://packagist.org/packages/apigen/apigen)
[![Latest stable](https://img.shields.io/packagist/v/apigen/apigen.svg?style=flat-square)](https://packagist.org/packages/apigen/apigen)

Just look at [CakePHP Framework](http://api.cakephp.org/3.0/) or [Doctrine ORM API](http://www.doctrine-project.org/api/orm/2.4/).


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
source: [src]           # directory(-ies) to scan PHP files from
destination: docs       # destination directory to generate API docs in
accessLevels: [public, protected] # array
annotationGroups: [todo, deprecated] # array
title: "ApiGen Docs"
baseUrl: http://apigen.org/api
exclude: tests
extensions: [php] # array
overwrite: true # bool
templateConfig: path-to-template-config.neon # string

# templates parameters
googleAnalytics: 123
```

Note: The key names should be the camelCased verions of the the flags listed in the CLI when running `$ apigen generate -h`. For example, `annotation-groups` becomes `annotationGroups`.

## DocBlock Annotations

This section provides a list of [PHP DocBlock
annotations](https://www.phpdoc.org/docs/latest/guides/docblocks.html) (tags)
that are supported by ApiGen:

- `@deprecated` - indicated that the associated element is deprecated and can be removed in the future version.
- `@internal` - denotes that the associated elements is internal to this application or library and hides it by default.
- `@link` - indicates a relation between the associated element and a page of a website.
- `@param` - documents a single argument of a function or method.
- `@return` - documents the return value of functions or methods.
- `@see` - indicates a reference from the associated element to a website or other elements.
- `@uses` - indicates a reference to (and from) a single associated element.


## Themes

In order to enable a custom theme, you have to either provide `--theme-config`
CLI option when runing `apigen generate` or add `themeConfig` configuration
option in your ApiGen configuration file:

```yaml
themeConfig: path/to/theme/config.neon # path to theme's config file
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
