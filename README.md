# Smart and Readable Documentation for PHP projects

ApiGen is easy to use and modern API doc generator **supporting all PHP 8.1 features**.


## Features

- phpDoc
  - [all types supported by PHPStan](https://phpstan.org/writing-php-code/phpdoc-types)
  - [generic class declarations](https://phpstan.org/blog/generics-in-php-using-phpdocs)
- PHP 8.1
  - [enums](https://wiki.php.net/rfc/enumerations)
  - [pure intersection types](https://wiki.php.net/rfc/pure-intersection-types)
  - [never type](https://wiki.php.net/rfc/noreturn_type)
  - [final class constants](https://wiki.php.net/rfc/final_class_const)
  - [new in initializers](https://wiki.php.net/rfc/new_in_initializers)
  - [readonly properties](https://wiki.php.net/rfc/readonly_properties_v2)
- PHP 8.0
  - [constructor property promotion](https://wiki.php.net/rfc/constructor_promotion)
  - [union types](https://wiki.php.net/rfc/union_types_v2)
  - [mixed type](https://wiki.php.net/rfc/mixed_type_v2)
  - [static return type](https://wiki.php.net/rfc/static_return_type)
- PHP 7.4
  - [typed properties](https://wiki.php.net/rfc/typed_properties_v2)


## Built on Shoulders of Giants

- [nikic/php-parser](https://github.com/nikic/PHP-Parser)
- [phpstan/phpdoc-parser](https://github.com/phpstan/phpdoc-parser)
- [latte/latte](https://github.com/nette/latte)
- [league/commonmark](https://github.com/thephpleague/commonmark)


## Install

This will install ApiGen to `tools/apigen` directory.

```bash
composer create-project apigen/apigen tools/apigen
```


## Usage

Generate API docs by passing source directories and destination option:

```bash
tools/apigen/bin/apigen src --output docs
```


## Configuration

ApiGen can be configured with `apigen.neon` configuration file.

```neon
parameters:
  # string[], passed as arguments in CLI, e.g. ['src']
  paths: []

  # string[], --include in CLI, included files mask, e.g. ['*.php']
  include: ['*.php']

  # string[], --exclude in CLI, excluded files mask, e.g. ['tests/**']
  exclude: []

  # bool, should protected members be excluded?
  excludeProtected: false

  # bool, should private members be excluded?
  excludePrivate: true

  # string[], list of tags used for excluding class-likes and members
  excludeTagged: ['internal']

  # string, --output in CLI
  outputDir: '%workingDir%/api'

  # string | null, --theme in CLI
  themeDir: null

  # string, --title in CLI
  title: 'API Documentation'

  # string, --base-url in CLI
  baseUrl: ''

  # int, --workers in CLI, number of processes that will be forked for parallel rendering
  workerCount: 8

  # string, --memory-limit in CLI
  memoryLimit: '512M'
```


## Performance

To achieve the best performance you need

* `pcntl` extension (required for parallel rendering) and
* `opcache` extension with enabled JIT
