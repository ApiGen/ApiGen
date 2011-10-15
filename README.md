# Welcome to ApiGen #

ApiGen is the tool for creating professional API documentation from PHP source code, similar to discontinued phpDocumentor/phpDoc.

ApiGen has support for PHP 5.3 namespaces, packages, linking between documentation, cross referencing to PHP standard classes and general documentation, creation of highlighted source code and experimental support for PHP 5.4 **traits**.

## Support/Bug Reports ##

For all support questions please use our [mailing list](https://groups.google.com/group/apigen). For bug reports and issues the [issue tracker](https://github.com/apigen/apigen/issues) is available. Changes between versions are described in the [change log](https://github.com/apigen/apigen/blob/master/CHANGELOG.md).

## Features ##

* Our own [TokenReflection library](https://github.com/Andrewsville/PHP-Token-Reflection) is used to describe the source code. It is **safe** (documented source code does not get included and thus parsed) and **simple** (you do not need to include or autoload all libraries you use in your source code).
* Detailed documentation of classes, functions and constants.
* Highlighted source code.
* Support of namespaces and packages with subpackages.
* Experimental support of traits.
* A page with trees of classes, interfaces, traits and exceptions.
* A page with a list of deprecated elements.
* A page with Todo tasks.
* List of undocumented elements.
* Support for docblock templates.
* Support for @inheritdoc.
* Support for {@link}.
* Active links in @see and @uses tags.
* Documentation of used internal PHP classes.
* Links to the start line in the highlighted source code for every described element.
* List of direct and indirect known subclasses, implementers and users for every class/interface/trait.
* Google CSE support with suggest.
* Google Analytics support.
* Support for multiple/custom templates.
* Sitemap and opensearch support.
* Support for different line endings.
* Lots of configuration options (see below).

## Installation ##

The preferred installation way is using the PEAR package. PEAR is a distribution system for PHP packages. It is bundled with PHP since the 4.3 version and it is easy to use.

Unlike the GitHub version that contains everything you need to use it, the PEAR package contains only ApiGen itself. Its dependencies (Nette, Texy, FSHL and TokenReflection) have to be installed separately. But do not panic, the PEAR installer will take care of it (almost).

In order to install any PEAR package, you have to add the appropriate repository URL. The good news is that you have to do that only once. Using these commands you add all required repositories.

```
	pear channel-discover pear.apigen.org
	pear channel-discover pear.nette.org
	pear channel-discover pear.texy.info
	pear channel-discover pear.kukulich.cz
	pear channel-discover pear.andrewsville.cz
```

Theoretically you should only use one command

```
	pear install apigen/ApiGen
```

to install ApiGen, then. However things are not so easy. This would work if all required libraries were in stable versions. But they aren't. Nette and TokenReflection are beta versions. Assuming you have your PEAR installer configured that it will not install non-stable packages (that is the default configuration), you have to explicitly enter each non-stable package you want to use. So you have to  run these commands

```
	pear install nette/Nette-beta
	pear install andrewsville/TokenReflection-beta
```

and finally

```
	pear install apigen/ApiGen
```

When all required libraries appear in stable versions, only the last command will be required and all dependencies will be downloaded by the PEAR installer automatically.

If you have have installed a version older than 2.1 using PEAR you will have to uninstall it first using these commands.

```
	pear uninstall kukulich/ApiGen
	pear uninstall kukulich/TokenReflection
```

This is required because we have moved from a temporary PEAR repository to a definitive one. So this change will be required only if you have installed ApiGen and TokenReflection library from the ```pear.kukulich.cz``` repository.

## Usage ##

```
	apigen --config <path> [options]
	apigen --source <path> --destination <path> [options]
```

As you can see, you can use ApiGen either by providing individual parameters via the command line or using a config file. Moreover you can combine the two methods and the command line parameters will have precedence over those in the config file.

Every configuration option has to be followed by its value. And it is exactly the same to write ```--config=file.conf``` and ```--config file.conf```. The only exceptions are boolean options (those with yes|no values). When using these options on the command line you do not have to provide the "yes" value explicitly. If ommited, it is assumed that you wanted to turn the option on. So using ```--debug=yes``` and ```--debug``` does exactly the same (and the opposite is ```--debug=no```).

Some options can have multiple values. To do so, you can either use them multiple times or separate their values by a comma. It means that ```--source=file1.php --source=file2.php``` and ```--source=file1.php,file2.php``` is exactly the same.

### Options ###

```--config|-c <file>```

Path to the config file.

```--source|-s <directory|file>``` **required**

Path to the directory or file to be processed. You can use the parameter multiple times to provide a list of directories or files.

```--destination|-d <directory>``` **required**

Documentation will be generated into this directory.

```--exclude <mask>```

Directories and files matching this file mask will not be parsed. You can exclude for example tests from processing this way. This parameter can be used multiple times.

```--skip-doc-path <mask>```
```--skip-doc-prefix <value>```

Using this parameters you can tell ApiGen not to generate documentation for classes from certain files or with certain name prefix. Such classes will appear in class trees, but will not create a link to their documentation. These parameters can be used multiple times.

```--main <value>```

Classes with this name prefix will be considered as the "main project" (the rest will be considered as libraries).

```--title <value>```

Title of the generated documentation.

```--base-url <value>```

Documentation base URL used in the sitemap. Only needed if you plan to make your documentation public.

```--google-cse-id <value>```

If you have a Google CSE ID, the search box will use it when you do not enter an exact class, constant or function name.

```--google-cse-label <value>```

This will be the default label when using Google CSE.

```--google-analytics <value>```

A Google Analytics tracking code. If provided, an ansynchronous tracking code will be placed into every generated page.

```--template-config <file>```

Template config file, default is the config file of ApiGen default template.

```--allowed-html <list>```

List of allowed HTML tags in documentation separated by comma. Default value is "b,i,a,ul,ol,li,p,br,var,samp,kbd,tt".

```--access-levels <list>```

Access levels of methods and properties that should get their documentation parsed. Default value is "public,protected" (don't generate private class members).

```--internal <yes|no>```

Generate documentation for elements marked as internal (```@internal``` without description) and display parts of the documentation that are marked as internal (```@internal with description ...``` or inline ```{@internal ...}```), default is "No".

```--php <yes|no>```

Generate documentation for PHP internal classes, default is "Yes".

```--tree <yes|no>```

Generate tree view of classes, interfaces and exceptions, default is "Yes".

```--deprecated <yes|no>```

Generate documentation for deprecated classes and class members, default is "No".

```--todo <yes|no>```

Generate a list of tasks, default is "No".

```--source-code <yes|no>```

Generate highlighted source code for user defined classes, default is "Yes".

```--undocumented <file>```

Save a list of undocumented classes, methods, properties and constants into a file.

```--wipeout <yes|no>```

Delete files generated in the previous run, default is "Yes".

```--quiet <yes|no>```

Do not print any messages to the console, default is "No".

```--progressbar <yes|no>```

Display progressbars, default is "Yes".

```--colors <yes|no>```

Use colors, default "No" on Windows, "Yes" on other systems. Windows doesn't support colors in console however you can enable it with [Ansicon](http://adoxa.110mb.com/ansicon/).

```--update-check <yes|no>```

Check for a new version of ApiGen, default is "Yes".

```--debug <yes|no>```

Display additional information (exception trace) in case of an error, default is "No".

```--help|-h```

Display the list of possible options.

Only ```--source``` and ```--destination``` parameters are required. You can provide them via command line or a configuration file.

### Config files ###

Instead of providing individual parameters via the command line, you can prepare a config file for later use. You can use all the above listed parameters (with one exception: the ```--config``` option) only without dashes and with an uppercase letter after each dash (so ```--access-level``` becomes ```accessLevel```).

And then you can call apigen with a single parameter ```--config``` specifying the config file to load.

```
	apigen --config <path> [options]
```

Even when using a config file, you can still provide additional parameters via the command line. Such parameters will have precedence over parameters from the config file.

Keep in mind, that any values in the config file will be **overwritten** by values from the command line. That means that providing the ```--source``` parameter values both in the config file and via the command line will not result in using all the provided values but only those from the command line.

### Example ###
We are generating documentation for the Nella Framework. We want Nette and Doctrine to be parsed as well because we want their classes to appear in class trees, lists of parent classes and their members in lists of inherited properties, methods and constants. However we do not want to generate their full documentation along with highlighted source codes. And we do not want to process any "test" directories, because there might be classes that do not belong to the project actually.

```
	apigen --source ~/nella/Nella --source ~/doctrine2/lib/Doctrine --source ~/doctrine2/lib/vendor --source ~/nette/Nette --skip-doc-path ~/doctrine2 --skip-doc-prefix Nette --exclude "*/tests/*" --destination ~/docs/ --title "Nella Framework"
```

## Requirements ##

ApiGen requires PHP 5.3 or later. Four libraries it uses ([Nette](https://github.com/nette/nette), [Texy](https://github.com/dg/texy), [TokenReflection](https://github.com/Andrewsville/PHP-Token-Reflection) and [FSHL](https://github.com/kukulich/fshl)) require three additional PHP extensions: tokenizer, iconv and mbstring.

When generating documentation of large libraries (Zend Framework for example) we recommend not to have the Xdebug PHP extension loaded (it does not need to be used, it significantly slows down the generating process even when only loaded).

## Authors ##

* [Jaroslav Hanslík](https://github.com/kukulich)
* [Ondřej Nešpor](https://github.com/Andrewsville)
* [David Grudl](https://github.com/dg)

## Usage examples ##

* Jyxo PHP Libraries, both [namespaced](http://jyxo.github.com/php/) and [non-namespaced](http://jyxo.github.com/php-no-namespace/),
* [TokenReflection library](http://andrewsville.github.com/PHP-Token-Reflection/),
* [FSHL library](http://fshl.kukulich.cz/api/),
* [Nella Framework](http://api.nella-project.org/framework/).

Besides from these publicly visible examples there are companies that use ApiGen to generate their inhouse documentation: [Medio Interactive](http://www.medio.cz/), [Wikidi](http://wikidi.com/).