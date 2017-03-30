# Smart and Readable Documentation for your PHP project

[![Build Status](https://img.shields.io/travis/ApiGen/ApiGen/master.svg?style=flat-square)](https://travis-ci.org/ApiGen/ApiGen)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/ApiGen/ApiGen.svg?style=flat-square)](https://scrutinizer-ci.com/g/ApiGen/ApiGen)
[![Downloads](https://img.shields.io/packagist/dt/apigen/apigen.svg?style=flat-square)](https://packagist.org/packages/apigen/apigen)
[![Latest stable](https://img.shields.io/packagist/v/apigen/apigen.svg?style=flat-square)](https://packagist.org/packages/apigen/apigen)


Just look at [CakePHP Framework](http://api.cakephp.org/3.0/) or [Doctrine ORM API](http://www.doctrine-project.org/api/orm/2.4/).


## Install via Composer

```bash
composer require apigen/apigen --dev
```


## Usage

Run ApiGen with source and destination options:

```bash
vendor/bin/apigen generate src --destination docs
```

Or multiple:

```bash
vendor/bin/apigen generate src tests --destination docs
```


## Configuration

To add another configuration, add `apigen.neon` to your root project.

You can setup all these options:

```yaml
visibilityLevels: [public, protected] # array
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



## Supported Annotations

(To be done...)


## Testing

```bash
composer complete-check
```


## Contributing

Rules are simple:

- **new feature needs tests**
- **all tests must pass**
- **1 feature per PR**

We would be happy to merge your feature then.
