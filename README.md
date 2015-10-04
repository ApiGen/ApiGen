# Smart and Readable Documentation for your PHP project

[![Build Status](https://img.shields.io/travis/ApiGen/ApiGen/master.svg?style=flat-square)](https://travis-ci.org/ApiGen/ApiGen)
[![Quality Score](https://img.shields.io/scrutinizer/g/ApiGen/ApiGen.svg?style=flat-square)](https://scrutinizer-ci.com/g/ApiGen/ApiGen)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/ApiGen/ApiGen.svg?style=flat-square)](https://scrutinizer-ci.com/g/ApiGen/ApiGen)
[![Downloads](https://img.shields.io/packagist/dt/apigen/apigen.svg?style=flat-square)](https://packagist.org/packages/apigen/apigen)
[![Latest stable](https://img.shields.io/packagist/v/apigen/apigen.svg?style=flat-square)](https://packagist.org/packages/apigen/apigen)


Just look at [CakePHP Framework](http://api.cakephp.org/3.0/) or [Doctrine ORM API](http://www.doctrine-project.org/api/orm/2.4/).


## Requirements

- PHP 5.5


## Install

### 1. Using Composer (preferred method)

In your project's root folder:

```
composer require --dev apigen/apigen
```

Or if you want it globally:

```
composer global require --dev apigen/apigen
```

### 2. As a PHAR

In your project's root folder:

```
curl -L -O https://github.com/ApiGen/ApiGen.github.io/raw/master/apigen.phar
```

(or just download it [here](https://github.com/ApiGen/ApiGen.github.io/raw/master/apigen.phar)).

For global installation, just move the downloaded `apigen.phar` to your path.

## Usage

*NOTE: The above examples assume you have ApiGen installed in your path. You might need to change the
`apigen` command to `vendor/bin/apigen` if installed locally through Composer or `php apigen.phar`
if using the PHAR version.*

Run ApiGen with source and destination options:

```sh
apigen generate -s ./src -d ./docs
```

To omit cli options just create `apigen.yaml` or `apigen.neon` file in your project's root folder:

```yaml
source:
    - ./src

destination: ./docs
```

For all available options, along with descriptions and default values, just run:

```sh
apigen generate --help
```

*NOTE: In config files, options are camelCased (i.e. `accessLevel` for `--access-level`).*

Refer to the [wiki](https://github.com/ApiGen/ApiGen/wiki/supported-annotations) for all supported annotations.

## Testing

```sh
$ phpunit
```

## Get Support!

* [#apigen](http://webchat.freenode.net/?channels=#apigen) on irc.freenode.net - Come chat with us, we have cake.

* [GitHub Issues](https://github.com/ApiGen/ApiGen/issues) - Got issues? Please tell us!

* [Roadmaps](https://github.com/ApiGen/ApiGen/wiki/Roadmaps) - Want to contribute? Get involved!

## Contributing

Please refer to [CONTRIBUTING](https://github.com/apigen/apigen/blob/master/CONTRIBUTING.md) for details.
