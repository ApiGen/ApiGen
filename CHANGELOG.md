# Change Log

@todo - move to releases after RC1 release, links do not work here and this duplicates it

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## [5.0.0-RC1] - UNRELEASED

### Added

- Added `Annotation` package [#863]
- Added `Element` package
- Added `ModularConfiguration` package
- Added `Reflection` package
- Added `StringRouting` package [#858]
- Added `.editorconfig` to the project
- Added support for `static` type [#704]
- Added bitcoin link support via `@link bitcoin:address` [#731]
- Added `theme` option to load theme from directory  
- **Added [phpDocumentor/TypeResolver](https://github.com/phpDocumentor/TypeResolver) for type resolving**
- **Added [phpDocumentor/ReflectionDocBlock](https://github.com/phpDocumentor/ReflectionDocBlock) for annotation parsing**
- Added [Symplify/EasyCodingStandard](https://github.com/Symplify/EasyCodingStandard) to make coding standard powerful
- Added [phpstan](https://github.com/phpstan/phpstan) for static analysis checks

### Changed

- **Changed PHP Token Reflection library from `andrewsville/php-token-reflection` to `roave/better-reflection` [#827]**
- **Minimum PHP requirement was increased from `5.4` to `7.1`**
- Change DI library from `Nette\DI` to [Symfony\DependencyInjection](http://symfony.com/doc/current/components/dependency_injection) due to [Symfony 3.3 new features](http://symfony.com/blog/the-new-symfony-3-3-service-configuration-changes-explained) and **PSR-11** [#880]
- Project is now `PSR-2` compatible
- UTF-8 is now a standard/default charset. [ApiGen] will expect UTF-8 encoded files by default [#64]
- Only relevant classes are generated in sidebar and source code pages [#771]
- Enabled autocomplete for methods and properties
- `ThemeDefault` and `ThemeBootstrap` merged to one local theme, with support of Bootstrap 3 
  and mobile-friendly design [#814, #862]
- `@internal` annotation cannot be ignored now [#823]
- Long and short description merged to one, since there was thin and not clear line between them [#826]
- `Class<Generator,Reflection>` split to `Class`, `Trait` and `Interface` with own type-specific methods [#818, #827]
- `SourceCodeGenerator` dropped and moved to particular reflection generators (ClassGenerator...) [#845]
- Indirect and direct users/implementers/inheritors merged to one group [#855]
- Tree and Namespace filters simplified [#858]
- Standardize use of annotations [#862]
- Left sidebar removed, it duplicated main content and had complicated tree structures [#860]
- Don't fully qualify local members [#749]

### Fixed

- Fixed an issue with temporary files not being removed upon exit (in cases where failure happens) [#520]
- Fixed an issue with `generate` command throwing an error [#631]
- Fixed tests (and hopefully compatibility) on Windows OS [#804]
- Fixed deprecation checks when generating docs
- Fixed issues with exception handling in low-level parser
- Fixed an error on generating docs for non-existent traits
- Fixed an issue with handling paths on different OS. The paths should now be normalized and work on Windows [#668]
- Fixed an issue where ApiGen sometimes would incorrectly resolve return typehints for functions [#740]
- Fixed an issue when docblocks marked with `@internal` would be documented [#734]
- Fixed support of `$this` as return type hint [#750]
- Fixed missing methods in class [#848]
- Fixed duplicated function source code [#717]  

### PHP 5.5, 5.6 and 7 and parsing related Fixes

- Fixed composed trait methods [#620]
- Fixed default constant value and `__DIR__` constant parsing [#774]
- Fixed broken function definition [#751]
- Fixed `::class` parsing [#680]

### Removed

- `--main` option dropped [#826]
- Magic elements dropped [#813]
- `--charset` CLI option has been dropped (expecting `UTF-8` now by default)
- `--skip-doc-path` CLI option has ben dropped
- `--template-config` and `--template-theme` dropped [#827]
- `--exclude` and `--extensions` options dropped (use `FinderInterface` implementation instead) [#853] 
- Removed various deprecated generators (Robots, Sitemap) which weren't used
- `Tree` generator dropped [#809]
- Dropped Zip generator, internet is quite fast nowadays.
- Dropped PHAR support.
- `Exception` element dropped [#827]
- Dropped element js toggling in page, was bugged and causing page jumps.
- Dropped support for global constant documentation, as BetterReflection doesn't support it out of the box and it is 
  pretty old.

## [4.1.2] - 2015-11-29

- Minor fixes.

## [4.1.1] - 2015-04-09

### Fixed

- Fix issue with ThemeConfigPathResolver for vendor [#590]

## [4.1.0] - 2015-04-04

### Added

- [#573] `--config` now supports YAML format, thanks to @trong
- [#536] `--source` option now accepts single files
- [#539] `--annotation-groups` options added, to generate pages with single annotation (e.g. for todo: `--annotation-groups=todo`)
  - before:
    - `--todo --deprecated` (only these two)
  - now:
    - `--annotation-groups todo,deprecated`
    - `--annotation-groups event,api`
- https://github.com/ApiGen/ApiGen/blob/master/CONTRIBUTING.md contributing info added

### Changed

- [#550] wiping out destination is now protected by asking
- [#523] when page from `--annotation-groups` has no items, info message is displayed
- [#504] themes decoupled to standalone packages
- [https://github.com/ApiGen/ThemeBootstrap/pull/3] Bootstrap theme was updated to Twitter Bootstrap 3, thanks to @olvlvl
- [#507] use Box for PHAR compiling

### Fixed

- [#545] missloading of class list in layout panel
- [#526] Exceptions were displayed instead of interfaces, thanks to @jrnickell
- [#530] `--source-code` options should be `--no-source-code`, thanks to @yoosefi
- [#538] spaces from `apigen.services.neon` removed, thanks to @ramsey
- [#575] function link fixed


## [4.0.1] - 2015-03-09

### Fixed

- Fixed issued when parsing configuration array when using `phar` #564

## [4.0.0] - 2015-01-03

Version `3.0` was skipped, because master branch had `3.0-dev` alias with code
base similar to 2.8. Since then there were many BC breaks, thus major version was bumped to `4.0`.

### Added

- [Zenify\CodingStandard](https://github.com/Zenify/CodingStandard) was
  introduced to keep codebase clean. It's based on
  [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)
- Codebase is now unit tested with with [PHPUnit](https://github.com/sebastianbergmann/phpunit) (test coverage of ~80%).
- Continuous integration testing enabled ([Travis CI](http://travis-ci.org)).

### Changed

- Minimum PHP version was raised from `5.3` to `5.4`.
- Docblock markup was changed from Texy to [Markdown Markup](https://github.com/michelf/php-markdown)
- [Symfony\Console](https://github.com/symfony/Console) replaced custom CLI
  solution, thus composer-like approach is used.
  In particular, you need to call specific command name in first argument.

  Before:

  `apigen -s source -d destination`

  After:

  `apigen generate -s source -d destination`
- New command `self-update` added, to upgrade `.phar` file.

  Before:

  *manual update*

  After:

  `apigen self-update`
- Bool options for arguments passed via CLI are off when absent, on when
  present.

  Before:

  `... --tree yes # tree => true`

  `... --tree no # tree => false`

   After:

   `... --tree # tree => true`

   `... # tree => false`

- Options with values for arguments passed via CLI now accept multiple formats:

  Before:

  `... --access-levels public --access-levels protected`

  After:

  `... --access-levels public,protected`

  or

  `... --access-levels="public,protected"`

  or

  `... --access-levels public --access-levels protected`

- Some CLI options were dropped. To see what the available ones are, just run `apigen generate --help`.

  - `--skip-doc-prefix` was dropped, use `--skip-doc-path` instead
  - `--allowed-html` was dropped
  - `--autocomplete` was dropped; autocomplete now works for classes, constants and functions by default
  - `--report`; use [Php_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) for any custom checkstyle
  - `--wipeout`; now wipes out everytime
  - `--progressbar`; now always present
  - `--colors`; now always colors
  - `--update-check`; update manually by `apigen self-update` (new version is released every ~ 2 months)

- Some CLI options were renamed and reversed.

  - `--source-code` was off by default, now it on by default; to turn it off, add `--no-source-code`

### Removed

- PEAR support was dropped. **Use PHAR file instead**. Latest stable version
  can be always found at [apigen.org](http://apigen.org)

[5.0.x-dev]: https://github.com/apigen/apigen/compare/4.1.2...master
[4.1.2]: https://github.com/apigen/apigen/compare/v4.1.1...v4.1.2
[4.1.1]: https://github.com/apigen/apigen/compare/v4.1.0...v4.1.1
[4.1.0]: https://github.com/apigen/apigen/compare/v4.0.1...v4.1.0
[4.0.1]: https://github.com/apigen/apigen/compare/v4.0.0...v4.0.1
[4.0.0]: https://github.com/apigen/apigen/compare/v2.8.1...v4.0.0
[0]: https://github.com/apigen/apigen
