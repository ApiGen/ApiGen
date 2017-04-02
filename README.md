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

Install using composer as a development dependency in your project:

```
composer require --dev apigen/apigen
```

Or if you want it globally:

```
composer global require --dev apigen/apigen
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

## Documentation

### Configuration

This section provides information on all available configuration options that
can be included in configuration files (`apigen.yml` or `apigen.neon`).

#### Minimal Configuration

A minimal configuration file:

```neon
# apigen.neon.dist
# This is minimal configuration for ApiGen.
source: [src]           # directory(-ies) to scan PHP files from
destination: docs       # destination directory to generate API docs in
```

**Note!** The configuration files match CLI options for [generate](#generate)
command. The only difference is that when defining these options in
configuration file, you have to use `camelCased` format (i.e.
`--annotation-groups` CLI options becomes `annotationGroups` configuration
parameter).

#### Reference Configuration

A reference configuration file with all of the available and support
configuration options, together with their default values.

```neon
# apigen.neon.dist
# This is reference configuration for ApiGen. It contains all of the available
# and supported configuration options, together with their default values.
# source options
source: [src]                       # Source directory(-ies) to build API docs for
extensions: [php] # array           # A list of file extension to include when
                                    # scanning source dir
accessLevels: [public, protected]   # Access levels of methods and properties
                                    # to include
annotationGroups: [todo, deprecated]# Annotation Groups to include
internal: false                     # Set to `true` to include @internal in API
                                    # docs.
#main: 'SomePrefix'                 # Elements with this name prefix will be
                                    # first in the tree.
php: false                          # Set to `true` to generate docs for PHP
                                    # internal classes.
noSourceCode: false                 # Set to `true` to NOT generate highlighted
                                    # source code for elements.

# destination / generated docs options
destination: doc                    # Destination directory for API docs
exclude: tests                      # A blob pattern to exclude from API docs
                                    # generation.
overwrite: true # bool              # Overwrite destination directory by
                                    # default
title: "ApiGen Docs"                # Title of generated API docs.
baseUrl: http://apigen.org/api      # Base URL for generated API docs.
templateConfig: path/to/config.neon # path to template configuration

# templates parameters
googleAnalytics: 123
googleCseId: 456
download: true                      # show a link to download API docs ZIP
                                    # archive in the API docs

# debug
debug: false                        # set to true to enable debug
```

### CLI Commands

This section provides information on all available CLI commands and their
options.

To get a list of available `apigen` commands:

    $ apigen list

Main commands:

- [generate](#generate) - generates API documentation.
- `help` - display help for a specific command.
- `list` - lists available commands.

#### Generate

`generate` command is the main command which starts generation of API
documentation. The command relies on reading data from [configuration
files](#Configuration). If these are not available, then it will expect command
line arguments passed to it.

A list of options accepted by `generate` command:

| Option                | Description                                         |
| --------------------- | --------------------------------------------------- |
| `--source` (`-s`)     | Source directory(-ies) to generate API docs for. **Multiple values are allowed**.
| `--destination` (`-d`)| Destination directory for API docs.
| `--access-levels`     | Access levels of methods and properties to be included in API docs [options: `public`, `protected`, `private`]. Default: `["public","protected"]`.
| `--annotation-groups` | Generate page with elements with specific annotation.
| `--config`            | Custom path to apigen configuration file. Default: `./apigen.neon`
| `--google-cse-id`     | Custom Google Search Engine ID (for search box).
| `--base-url`          | Base URL (used for Sitemap / search box).
| `--google-analytics`  | Google Analytics tracking code to include in generated API docs.
| `--debug`             | Turn on debug mode (prints verbose information from low-level parser). Useful when debugging / during development.
| `--download`          | Pass this option to include a link to a generated ZIP archive in the API docs.
| `--extensions`        | A list of scanned file extensions. **Multiple values are allowed**. Default: `["php"]`.
| `--exclude`           | Diretories or files matching provided mask will be excluded (e.g. `*/tests/*`). **Multiple values are allowed**.
| `--groups`            | Define element grouping in the menu [options: `namespaces`, `packages`]. Default: `namespaces`.
| `--main`              | Elements with provided main name prefix will be first in the tree.
| `--internal`          | Include elements marked as `@internal`.
| `--php`               | Generate documentation for PHP internal classes.
| `--no-source-code`    | Do not generate highlighted source code for elements.
| `--template-theme`    | ApiGen template theme name. [default: "default"].
| `--template-config`   | Specify your own template config (this setting will override `--template-theme`).
| `--title`             | Custom title of API docs.
| `--tree`              | Generate a tree view of classes, interfaces, traits and exceptions.
| `--deprecated`        | deprecated, only present for BC
| `--todo`              | deprecated, only present for BC
| `--charset`           | deprecated, only present for BC
| `--skip-doc-path`     | deprecated, only present for BC
| `--overwrite` (`-o`)  | Force overwriting of destination directory.
| `--help` (`-h`)       | Display help message for all or specific commands.
| `--quiet` (`-q`)      | Do not output any messages.
| `--version` (`-V`)    | Display version of apigen.
| --------------------- |
## Contributing

Please refer to [CONTRIBUTING](CONTRIBUTING.md) for details.

### Tests

To run tests:

```sh
$ vendor/bin/phpunit
```

## Get Support!

* [#apigen](http://webchat.freenode.net/?channels=#apigen) on irc.freenode.net - Come chat with us, we have cake.
* [GitHub Issues](https://github.com/ApiGen/ApiGen/issues) - Got issues? Please tell us!
* [Roadmaps](https://github.com/ApiGen/ApiGen/wiki/Roadmaps) - Want to contribute? Get involved!

