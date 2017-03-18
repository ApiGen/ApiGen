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

```sh
vendor/bin/apigen generate --source /src --destination /docs
```

To omit cli options just create `apigen.yaml` or `apigen.neon` file in your project's root folder:

```yaml
source:
    - /src

destination: /docs
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

## Contributing

Please refer to [CONTRIBUTING](https://github.com/apigen/apigen/blob/master/CONTRIBUTING.md) for details.
