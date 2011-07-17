# Welcome to ApiGen #

ApiGen is a fork of the original [tool](https://github.com/nette/apigen) created by [Andrewsville](https://github.com/Andrewsville) and [Kukulich](https://github.com/kukulich).

When the original ApiGen was introduced, its [author](https://github.com/dg) stated that: *"This time with no technical support. There are definitely things that ApiGen does not support and that are absolutely crucial for you. And without them there is no use in trying ApiGen because other similar tools have them. If this is the case, feel free to add them."*

And we did :) That is how our **ApiGen** was born. We have taken the original tool, fixed some bugs, altered some things and added many useful features.

# Differences from the original tool #

The biggest difference is that we use our own [TokenReflection library](https://github.com/Andrewsville/PHP-Token-Reflection) to describe the source code. Using TokenReflection is:

* **safe** (documented source code does not get included and thus parsed),
* **simple** (you do not need to include or autoload all libraries you use in your source code).

Besides, we have made following changes in our ApiGen.

### Changes ###

* Much more confuguration options (see below).
* Much lower memory usage.
* Documentation for functions and constants in namespaces or global space.
* A page with trees of classes, interfaces and exceptions.
* A page with a list of deprecated elements.
* A page with Todo tasks.
* List of undocumented elements.
* Support for docblock templates.
* Support for @inheritdoc.
* Support for {@link}.
* Clickable links for @see and @uses.
* Detailed documentation for constants and properties with all annotations.
* Links to PHP manual pages for constants and properties of PHP internal classes.
* Links to the start line in the highlighted source code for constants and properties.
* List of packages and subpackages.
* List of indirect known subclasses and implementers.
* Improved search and suggest - lets you search in class, functions and constant names without the need of Google CSE.
* Support for multiple/custom templates.
* Fancy progressbars (one while parsing source codes, one while generating documentation).
* Exceptions handling.
* Inherited methods, constants and properties are in alphabetical order.
* Class methods, constants and properties are in truly alphabetical order.
* Better visualization of method parameters.
* Unified order of annotations.
* No URL shortening.
* Better visualization of nested namespaces.
* No wrapping of class names.
* Resizable left column.
* Better static files (stylesheets, javascript) versioning.
* Better page titles.
* A lot of visual enhancements.
* Google Analytics support.
* Full sitemap with links to package and namespace pages.
* New version of jQuery.
* New version of FSHL.

### Bugfixes ###
* No fatal errors when a class is missing (this is a benefit of using the TokenReflection library).
* Better FQN resolving in documentation.
* Better values output of constants and properties (you see the actual definition - "\\n", not just a space).
* Fixed email addresses in annotations (the original ApiGen cannot handle an email address next to the author name).
* Fixed parsing packages and subpackages with description.
* Displaying the "public" keyword for public properties.
* Texy! downgraded to the stable 2.1 version (the 3.0-dev version causes PCRE internal errors sometimes).
* Support for Mac and DOS line endings.

## Installation ##

The preferred installation way is using the PEAR package. PEAR is a distribution system for PHP packages. It is bundled with PHP since the 4.3 version and it is easy to use.

Unlike the GitHub version that contains everything you need to use it, the PEAR package contains only ApiGen itself. Its dependencies (Nette, Texy, FSHL and TokenReflection) have to be installed separately. But do not panic, the PEAR installer will take care of it (almost).

In order to install any PEAR package, you have to add the appropriate repository URL. The good news is that you have to do that only once. Using these two commands you add our own and Nette repository.

```
	pear channel-discover pear.kukulich.cz
	pear channel-discover pear.nette.org
```

Theoretically you should only use one command

```
	pear install kukulich/ApiGen
```

to install ApiGen, then. However things are not so easy. This would work if all required libraries were in stable versions. But they aren't. Nette, TokenReflection and FSHL are beta versions. Assuming you have your PEAR installer configured that it will not install non-stable packages (that is the default configuration), you have to explicitly enter each non-stable package you want to use. So you have to  run these commands

```
	pear install kukulich/FSHL-2.0.0RC
	pear install kukulich/TokenReflection-beta
	pear install nette/Nette-beta
```

and finally

```
	pear install kukulich/ApiGen
```

When all required libraries appear in stable versions, only the last command will be required and all dependencies will be downloaded by the PEAR installer automatically.

## Usage ##

```
	apigen --config <path> [options]
	apigen --source <path> --destination <path> [options]
```

As you can see, you can use ApiGen either by providing individual parameters via the command line or using a config file. Moreover you can combine the two methods and the command line parameters will have precedence over those in the config file.

Every configuration option has to be followed by its value. And it is exactly the same to write ```--config=file.conf``` and ```--config file.conf```. The only exceptions are boolean options (those with yes|no values). When using these options on the command line you do not have to provide the "yes" value explicitly. If ommited, it is assumed that you wanted to turn the option on. So using ```--debug=yes``` and ```--debug``` does exactly the same (and the opposite is ```--debug=no```).

Some options can have multiple values. To do so, you can either use them multiple times or separate their values by a comma. It means that ```--source=file1.php --source=file2.php``` and ```--source=file1.php,file2.php``` is exactly the same.

### Parameter list ###

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

```--debug <yes|no>```

Display additional information (exception trace) in case of an error, default is "No".

```--help|-h ```

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

### Original ApiGen ###
* [David Grudl](https://github.com/dg)

### New ApiGen ###
* [Jaroslav Hanslík](https://github.com/kukulich)
* [Ondřej Nešpor](https://github.com/Andrewsville)

## Usage examples ##

* Jyxo PHP Libraries, both [namespaced](http://jyxo.github.com/php/) and [non-namespaced](http://jyxo.github.com/php-no-namespace/),
* [TokenReflection library](http://andrewsville.github.com/PHP-Token-Reflection/),
* [FSHL library](http://kukulich.github.com/fshl/),
* [Nella Framework](http://api.nella-project.org/framework/).

Besides from these publicly visible examples there are companies that use ApiGen to generate their inhouse documentation: [Medio Interactive](http://www.medio.cz/), [Wikidi](http://wikidi.com/).