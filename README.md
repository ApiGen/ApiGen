# Welcome to TR ApiGen #

TR ApiGen is a fork of the original [tool](https://github.com/nette/apigen) created by [Andrewsville](https://github.com/Andrewsville) and [Kukulich](https://github.com/kukulich). We have taken the original ApiGen, fixed some bugs, altered some things and added many useful features.

# Differences from the original tool #

The biggest difference is that we use our own [TokenReflection library](https://github.com/Andrewsville/PHP-Token-Reflection) to describe the source code. Using TokenReflection is:

* fast (TR Apigen is faster than the original version, actually),
* safe (documented source code does not get included and thus parsed),
* simple (you do not need to include or autoload all libraries you use in your source code).

Besides, we have made following changes in our ApiGen.

### Changed behavior ###

* There are different command line parameters (we will look at them later).
* TR Apigen supports multiple/custom templates.
* There are two fancy progressbars. One is displayed while parsing source codes, the other one while generating the actual documentation. So you do not have to stare at the command prompt not having an idea how long it will take to finish.
* We can handle exceptions. So if something goes wrong, you will know what happened (and you can let us know ;)
* TR ApiGen uses much less memory.

### Documentation changes ###

* Better page titles.
* A page with trees of classes, interfaces and exceptions.
* A page with a list of deprecated elements.
* A page with Todo tasks.
* Detailed documentation for constants and properties with all annotations.
* Links to PHP manual pages for constants and properties of PHP internal classes.
* Links to the start line in the highlighted source code for constants and properties.
* List of packages.
* List of indirect known subclasses and implementers.
* Inherited methods, constants and properties are in alphabetical order.
* Class methods, constants and properties are in truly alphabetical order.
* Better visualization of method parameters.
* Unified order of annotations.
* Support of the {@link} tag.
* Clickable links for @see and @uses tags.
* No URL shortening.
* Better visualization of nested namespaces.
* No wrapping of class names.
* Resizable left column.
* Better static files (stylesheets, javascript) versioning.
* Full sitemap with links to package and namespace pages.
* Class and interface tables/headings are displayed only if required.
* New version of jQuery.
* New version of FSHL.
* Google Analytics support.
* List of undocumented elements.
* Documentation for functions and constants in namespace or global space.

### Bugfixes ###
* No fatal errors when a class is missing (this is a benefit of using the TokenReflection library).
* Better FQN resolving in documentation.
* Better values output of constants and properties (you see the actual definition - "\\n", not just a space).
* Fixed email addresses in annotations (the original ApiGen cannot handle an email address next to the author name).
* Displaying the "public" keyword for public properties.
* Texy! downgraded to the stable 2.1 version (the 3.0-dev version causes PCRE internal errors sometimes).
* Support for Mac and DOS line endings.

## Usage ##

```
	apigen --config <path> [options]
	apigen --source <path> --destination <path> [options]
```

As you can see, you can use TR ApiGen either by providing individual parameters via the command line or using a config file. Moreover you can combine the two methods and the command line parameters will have precedence over those in the config file.

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

Using this parameters you can tell TR ApiGen not to generate documentation for classes from certain files or with certain name prefix. Such classes will appear in class trees, but will not create a link to their documentation. These parameters can be used multiple times.

```--title <value>```

Title of the generated documentation.

```--base-url <value>```

Documentation base URL used in the sitemap. Only needed if you plan to make your documentation public.

```--google-cse <value>```

If you have a Google CSE ID, there will be a search field on the top of all pages leading to your customized search.

```--google-analytics <value>```

A Google Analytics tracking code. If provided, an ansynchronous tracking code will be placed into every generated page.

```--template <value>```

Template name, default is "default" :)

```--template-dir <directory>```

Template directory, default is the ApiGen template directory.

```--allowed-html <list>```

List of allowed HTML tags in documentation separated by comma. Default value is "b,i,a,ul,ol,li,p,br,var,samp,kbd,tt".

```--access-levels <list>```

Access levels of methods and properties that should get their documentation parsed. Default value is "public,protected" (don't generate private class members).

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

TR ApiGen requires PHP 5.3.1 or later. We do not officially support PHP 5.3.0 because of a [reflection bug](http://bugs.php.net/bug.php?id=48757) that affects the TokenReflection library.

When generating documentation of large libraries (Zend Framework for example) we recommend not to have the Xdebug PHP extension loaded (it does not need to be used, it significantly slows down the generating process even when only loaded).

## Usage examples ##

* Jyxo PHP Libraries, both [namespaced](http://jyxo.github.com/php/) and [non-namespaced](http://jyxo.github.com/php-no-namespace/),
* [TokenReflection library](http://andrewsville.github.com/PHP-Token-Reflection/).