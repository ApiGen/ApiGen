# Change Log

All notable changes to [apigen][0] project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

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

[4.2.x-dev]: https://github.com/apigen/apigen/compare/v4.2.1...4.2
[4.1.2]: https://github.com/apigen/apigen/compare/v4.1.1...v4.1.2
[4.1.1]: https://github.com/apigen/apigen/compare/v4.1.0...v4.1.1
[4.1.0]: https://github.com/apigen/apigen/compare/v4.0.1...v4.1.0
[4.0.1]: https://github.com/apigen/apigen/compare/v4.0.0...v4.0.1
[4.0.0]: https://github.com/apigen/apigen/compare/v2.8.1...v4.0.0
[0]: https://github.com/apigen/apigen
