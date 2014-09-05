# ApiGen - PHP source code API generator

[![Build Status](https://travis-ci.org/apigen/apigen.svg?branch=develop)](https://travis-ci.org/apigen/apigen)
[![Downloads this Month](https://img.shields.io/packagist/dm/apigen/apigen.svg)](https://packagist.org/packages/apigen/apigen)
[![Latest stable](https://img.shields.io/packagist/v/apigen/apigen.svg)](https://packagist.org/packages/apigen/apigen)


**[WIP] In renewal process, stay tuned.**

So what's the output? Look at [Doctrine ORM API](http://www.doctrine-project.org/api/orm/2.4/), [Nette API](http://api.nette.org/) or [Kdyby API](https://api.kdyby.org).


## Features

* Detailed documentation of classes, functions and constants
* [Highlighted source code](http://api.nette.org/source-Application.UI.Form.php.html)
* Experimental support of [traits](https://api.kdyby.org/class-Nextras.Application.UI.SecuredLinksControlTrait.html).
* A page with:
    - [trees of classes, interfaces, traits and exceptions](https://api.kdyby.org/tree.html)
	- [list of deprecated elements](http://api.nette.org/deprecated.html)
	- Todo tasks
* Support for:
    - docblock templates
	- @inheritdoc
	- {@link}
* Active links in @see and @uses tags.
* Links to internal PHP classes and PHP documentation.
* [Links to the start line](http://api.nette.org/2.2.3/Nette.Application.UI.Control.html#_redrawControl) in the highlighted source code for every described element.
* [List of known subclasses and implementers](https://api.kdyby.org/class-Kdyby.Doctrine.EntityRepository.html)
* Google CSE support with suggest.
* Google Analytics support.
* Support for custom templates.


## Installation

The best way to install Apigen is via [Composer](https://getcomposer.org/):

```sh
$ composer require apigen/apigen:~2.8.1 --dev
```


## Usage

```
apigen --config <path> [options]
apigen --source <path> --destination <path> [options]
```

As you can see, you can use ApiGen either by providing individual parameters via the command line or using a config file.
Moreover you can combine the two methods and the command line parameters will have precedence over those in the config file.

Every configuration option has to be followed by its value. And it is exactly the same to write ```--config=file.conf``` and ```--config file.conf```. The only exceptions are boolean options (those with yes|no values). When using these options on the command line you do not have to provide the "yes" value explicitly. If omitted, it is assumed that you wanted to turn the option on. So using ```--debug=yes``` and ```--debug``` does exactly the same (and the opposite is ```--debug=no```).

Some options can have multiple values:

* ```--source=file1.php --source=file2.php```
* ```--source=file1.php,file2.php```


### Options

```--config|-c <file>```

Path to the config file.

```--source|-s <directory|file>``` **required**

Path to the directory or file to be processed. You can use the parameter multiple times to provide a list of directories or files. All types of PHAR archives are supported (requires the PHAR extension). To process gz/bz2 compressed archives you need the appropriate extension (see requirements).

```--destination|-d <directory>``` **required**

Documentation will be generated into this directory.

```--extensions <list>```

List of allowed file extensions, default is "php".

```--exclude <mask>```

Directories and files matching this file mask will not be parsed. You can exclude for example tests from processing this way. This parameter is case sensitive and can be used multiple times.

```--skip-doc-path <mask>```
```--skip-doc-prefix <value>```

Using this parameters you can tell ApiGen not to generate documentation for elements from certain files or with certain name prefix. Such classes will appear in class trees, but will not create a link to their documentation. These parameters are case sensitive and can be used multiple times.

```--charset <list>```

Character set of source files, default is "auto" that lets ApiGen choose from all supported character sets. However if you use only one characters set across your source files you should set it explicitly to avoid autodetection because it can be tricky (and is not completely realiable). Moreover autodetection slows down the process of generating documentation. You can also use the parameter multiple times to provide a list of all used character sets in your documentation. In that case ApiGen will choose one of provided character sets for each file.

```--main <value>```

Elements with this name prefix will be considered as the "main project" (the rest will be considered as libraries).

```--title <value>```

Title of the generated documentation.

```--base-url <value>```

Documentation base URL used in the sitemap. Only needed if you plan to make your documentation public.

```--google-cse-id <value>```

If you have a Google CSE ID, the search box will use it when you do not enter an exact class, constant or function name.

```--google-analytics <value>```

A Google Analytics tracking code. If provided, an ansynchronous tracking code will be placed into every generated page.

```--template-config <file>```

Template config file, default is the config file of ApiGen default template.

```--allowed-html <list>```

List of allowed HTML tags in documentation separated by comma. Default value is "b,i,a,ul,ol,li,p,br,var,samp,kbd,tt".

```--groups <value>```

How should elements be grouped in the menu. Possible options are "auto", "namespaces", "packages" and "none". Default value is "auto" (namespaces are used if the source code uses them, packages otherwise).

```--autocomplete <list>```

List of element types that will appear in the search input autocomplete. Possible values are "classes", "constants", "functions", "methods", "properties" and "classconstants". Default value is "classes,constants,functions".

```--access-levels <list>```

Access levels of methods and properties that should get their documentation parsed. Default value is "public,protected" (don't generate private class members).

```--internal <yes|no>```

Generate documentation for elements marked as internal (```@internal``` without description) and display parts of the documentation that are marked as internal (```@internal with description ...``` or inline ```{@internal ...}```), default is "No".

```--php <yes|no>```

Generate documentation for PHP internal classes, default is "Yes".

```--tree <yes|no>```

Generate tree view of classes, interfaces, traits and exceptions, default is "Yes".

```--deprecated <yes|no>```

Generate documentation for deprecated elements, default is "No".

```--todo <yes|no>```

Generate a list of tasks, default is "No".

```--source-code <yes|no>```

Generate highlighted source code for user defined elements, default is "Yes".

```--download <yes|no>```

Add a link to download documentation as a ZIP archive, default is "No".

```--report <file>```

Save a checkstyle report of poorly documented elements into a file.

```--wipeout <yes|no>```

Delete files generated in the previous run, default is "Yes".

```--quiet <yes|no>```

Do not print any messages to the console, default is "No".

```--progressbar <yes|no>```

Display progressbars, default is "Yes".

```--colors <yes|no>```

Use colors, default "No" on Windows, "Yes" on other systems. Windows doesn't support colors in console however you can enable it with [Ansicon](http://adoxa.110mb.com/ansicon/).

```--debug <yes|no>```

Display additional information (exception trace) in case of an error, default is "No".

```--help|-h ```

Display the list of possible options.

Only ```--source``` and ```--destination``` parameters are required. You can provide them via command line or a configuration file.


### Config files

Instead of providing individual parameters via the command line, you can prepare a config file for later use. You can use all the above listed parameters (with one exception: the ```--config``` option) only without dashes and with an uppercase letter after each dash (so ```--access-level``` becomes ```accessLevel```).

ApiGen uses the [NEON file format](http://ne-on.org) for all its config files. You can try the [online parser](http://ne-on.org) to debug your config files and see how they get parsed.

Then you can call ApiGen with a single parameter ```--config``` specifying the config file to load.

```
apigen --config <path> [options]
```

Even when using a config file, you can still provide additional parameters via the command line. Such parameters will have precedence over parameters from the config file.

Keep in mind, that any values in the config file will be **overwritten** by values from the command line. That means that providing the ```--source``` parameter values both in the config file and via the command line will not result in using all the provided values but only those from the command line.

If you provide no command line parameters at all, ApiGen will try to load a default config file called ```apigen.neon``` in the current working directory. If found it will work as if you used the ```--config``` option. Note that when using any command line option, you have to specify the config file if you have one. ApiGen will try to load one automatically only when no command line parameters are used. Option names have to be in camelCase in config files (```--template-config``` on the command line becomes ```templateConfig``` in a config file). You can see a full list of configuration options with short descriptions in the example config file [apigen.neon.example](https://github.com/apigen/apigen/blob/master/apigen.neon.example).


Note: When generating documentation of large libraries, not loading the Xdebug PHP might improve performance.
