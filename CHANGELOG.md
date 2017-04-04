# Change Log

All notable changes to [apigen][0] project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [5.0.x-dev] - UNRELEASED

### Added

- TBA

### Changed

- TBA

### Fixed

- TBA

### Removed

- TBA

### Updated

- TBA

## [4.2.0-RC1] - 2017-04-03

### Added

- Added support for `*.dist` config files [#603].
- Added NEON file support.
- Added ability to skip confirmation question to overwrite non-empty
  destination directory when building docs [#604].
- Added `.editorconfig` to the project.
- Enabled PHP7 tests in Travis-CI.
- Enabled automated CI tests on Windows via Appveyor [#831]
- Added `--debug` CLI option, which prints detailed parser errors.
- Added `--overwrite` CLI option [#679].
- Added support for `static` type [#704].
- Added documentation generation for global constants and functions [#2].
- Added support for array-nottation typehint for `@propety` and `@method` [#699].
- Added bitcoin link support via `@link bitcoin:address` [#731].

### Changed

- Minimum PHP requirement was increased from `5.4` to `5.5`.
- Project is now `PSR-2` compatible.
- Changed PHP Token Reflection library from `andrewsville/php-token-reflection`
  to `popsul/php-token-reflection`.
- UTF-8 is now a standard/default charset. [ApiGen] will  expect UTF-8 encoded
  files by default (see [#64] for info).
- Project structure has been decoupled, some parts of internal code have been
  `ApiGen\Parser` (`apigen/parser` package).
- ApiGen now uses different temporary directories for different users. This
  should prevent problems when different users are running apigen simultaneously.
- Only relevant classes are generated in sidebar and source code pages [#771].

### Fixed

- Fixed an issue with temporary files not being removed upon exit (in cases
  where failure happens) [#520]
- Fixed an issue with `generate` command throwing an error [#631]
- Fixed tests (and hopefully compatibility) on Windows OS [#804]
- Fixed deprecation checks when generating docs
- Fixed issues with exception handling in low-level parser
- Fixed generation problems when generating docs for classes using same Traits.
- Fixed an error on generating docs for non-existent traits.
- Fixed an issue with handling paths on different OS. The paths should now be
  normalized and work on Windows [#668].
- `TreeGenerator` now properly generates a tree, instead of a list [#569].
- Fixed API documentation download link generation. The generated `.zip`
  filename will now include a name of the slugified project name [#702].
- Fixed an issue where ApiGen sometimes would incorrectly resolve return
  typehints for functions [#740].
- Fixed an issue when docblocks marked with `@internal` would be documented
  [#734].
- Fixed support of `$this` as return type hint [#750].
- Fixed support for `themes` allowing you to use any theme available in `vendor`.

### Removed

- `--charset` CLI option has been dropped (expecting `UTF-8` now by default).
- `--skip-doc-path` CLI option has ben dropped (use `--exclude` instead).
- Removed various deprecated generators (Robots, Sitemap) which weren't used.
- Dropped PHAR support in `composer`.
- Cleanup codebase to get rid of unused namespaces, methods or properties.

### Updated

- Updated `nette` dependency from `2.2` to `2.3`.
- Updated `symfony` components dependencies to support both `2.x` (`2.7` or
  `2.8`) and `3.x` versions [#835]
- Enabled autocomplete for methods and properties.

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

### Updated

- https://github.com/ApiGen/ApiGen/blob/master/CONTRIBUTING.md contributing info added
- https://github.com/ApiGen/ApiGen/blob/master/UPGRADE-4.0.md upgrade from 2.8 to 4.0 info added

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
- New [Release process](wiki/Release-Process) was established. Releasing minor
  version **every 2 months**.

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

[5.0.x-dev]: https://github.com/apigen/apigen/compare/4.2...master
[4.2.0-RC1]: https://github.com/apigen/apigen/compare/v4.1.2...v4.2.0-RC1
[4.1.2]: https://github.com/apigen/apigen/compare/v4.1.1...v4.1.2
[4.1.1]: https://github.com/apigen/apigen/compare/v4.1.0...v4.1.1
[4.1.0]: https://github.com/apigen/apigen/compare/v4.0.1...v4.1.0
[4.0.1]: https://github.com/apigen/apigen/compare/v4.0.0...v4.0.1
[4.0.0]: https://github.com/apigen/apigen/compare/v2.8.1...v4.0.0
[0]: https://github.com/apigen/apigen
