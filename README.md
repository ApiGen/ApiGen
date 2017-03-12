# Smart and Readable Documentation for your PHP project

[![Build Status](https://img.shields.io/travis/ApiGen/ApiGen/master.svg?style=flat-square)](https://travis-ci.org/ApiGen/ApiGen)
[![Quality Score](https://img.shields.io/scrutinizer/g/ApiGen/ApiGen.svg?style=flat-square)](https://scrutinizer-ci.com/g/ApiGen/ApiGen)
[![Coverage Status](https://coveralls.io/repos/github/ApiGen/ApiGen/badge.svg?branch=master)](https://coveralls.io/github/ApiGen/ApiGen?branch=master)
[![Downloads](https://img.shields.io/packagist/dt/apigen/apigen.svg?style=flat-square)](https://packagist.org/packages/apigen/apigen)
[![Latest stable](https://img.shields.io/packagist/v/apigen/apigen.svg?style=flat-square)](https://packagist.org/packages/apigen/apigen)


Just look at [CakePHP Framework](http://api.cakephp.org/3.0/) or [Doctrine ORM API](http://www.doctrine-project.org/api/orm/2.4/).


## Install via Composer

```bash
composer require apigen/apigen --dev
```


## Usage

Run ApiGen with source and destination options:

```sh
vendor/bin/apigen generate -s ./src -d ./docs
```

To omit cli options just create `apigen.yaml` or `apigen.neon` file in your project's root folder:

```yaml
source:
    - ./src

destination: ./docs
```

For all available options, along with descriptions and default values, just run:

```sh
vendor/bin/apigen generate --help
```

*NOTE: In config files, options are camelCased (i.e. `accessLevel` for `--access-level`).*

Refer to the [wiki](https://github.com/ApiGen/ApiGen/wiki/supported-annotations) for all supported annotations.

## Testing

```sh
vendor/bin/phpunit
```

## Get Support!

* [#apigen](http://webchat.freenode.net/?channels=#apigen) on irc.freenode.net - Come chat with us, we have cake.

* [Roadmaps](https://github.com/ApiGen/ApiGen/wiki/Roadmaps) - Want to contribute? Get involved!

## Contributing

Please refer to [CONTRIBUTING](https://github.com/apigen/apigen/blob/master/CONTRIBUTING.md) for details.
