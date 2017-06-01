# Smart and Readable Documentation for your PHP project

[![Build Status](https://img.shields.io/travis/ApiGen/ApiGen/master.svg?style=flat-square)](https://travis-ci.org/ApiGen/ApiGen)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/ApiGen/ApiGen.svg?style=flat-square)](https://scrutinizer-ci.com/g/ApiGen/ApiGen)
[![Downloads](https://img.shields.io/packagist/dt/apigen/apigen.svg?style=flat-square)](https://packagist.org/packages/apigen/apigen/stats)
[![Latest stable](https://img.shields.io/packagist/v/apigen/apigen.svg?style=flat-square)](https://packagist.org/packages/apigen/apigen)

Just look at [Nette API](http://api.nette.org/), [CakePHP API](http://api.cakephp.org/3.4/) or [Doctrine API](http://www.doctrine-project.org/api/orm/2.5/).


## Built on Shoulders of Giants

- PHP 7.1
- [nikic/PHP-Parser](https://github.com/nikic/PHP-Parser)
- [Roave/BetterReflection](https://github.com/Roave/BetterReflection)
- [phpDocumentor/TypeResolver](https://github.com/phpDocumentor/TypeResolver)


## Install

```bash
composer require apigen/apigen --dev
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
    source: [src]           # 1+ directories to scan PHP classes and functions in
    destination: docs       # directory, where API docs will be generated to
    visibilityLevels: [public, protected] # array
    annotationGroups: [todo, deprecated] # array
    title: "ApiGen Docs" # string
    baseUrl: http://apigen.org/api # string
    overwrite: false # bool
```

## DocBlock Annotations

These annotations are supported by ApiGen:

- `@link` - website url
- `@see`, `@uses`, `@covers` - reference to some element (Class, Function, Property, Method...)
- `@deprecated` - element about to be removed, with replacement

- `@param` - an argument of a method or function 
- `@return` - the return value of method or function 
- `@internal` - denotes that the associated elements is internal to this application or library and hides it by default

- all urls are clickable (@todo)


### In Examples

#### `@see`, `@covers`, `@uses`

**In Code**

```php
/**
 * @see SomeClass
 * @see SomeClass::$propety
 * @see SomeClass::someFunction()
 */
```

**Generated**

[SomeClass](class-SomeClass.html)
[SomeClass::$property](class-SomeClass.html#$someProperty)
[SomeClass::someFunction()](class-SomeClass.html#_someFunction)

---

#### `@link`

**In Code**

```php
/**
 * This is already mentioned on Wiki.
 * @link https://en.wikipedia.org/wiki/United_we_stand,_divided_we_fall  
 */
```

**Generated**

```html
This is already mentioned on Wiki.
(https://en.wikipedia.org/wiki/United_we_stand,_divided_we_fall)[https://en.wikipedia.org/wiki/United_we_stand,_divided_we_fall] 
```

### `@deprecated`

**In Code**

```php
/**
 * @deprecated use Nette\Utils\ObjectMixin::setExtensionMethod() instead
 */
```

**Generated**

```html
@todo
```



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
