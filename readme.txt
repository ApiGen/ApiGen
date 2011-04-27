Welcome to ApiGen
=================

ApiGen is the tool for creating professional API documentation from PHP
source code. It is similar to discontinued phpDocumentor. ApiGen has
support for PHP 5.3 namespaces, linking between documentation, creation
of highlighted source code with cross referencing to PHP general
documentation and is very fast.

ApiGen uses an Nette Framework templating system to produce useful and easy
to read HTML documentation. You can also create your own templates to match
the look and feel of your project.

ApiGen can be used from the command line:

	apigen [options]

Options:
	-s <path>  Name of a source directory to parse. Required.
	-d <path>  Folder where to save the generated documentation. Required.
	-c <path>  Output config file.
	-l <path>  Directory with additional libraries.
	-t ...     Title of generated documentation.

Example:

	apigen -s MyProject -d API-reference -t "My Project Documentation"


Requirements
------------

ApiGen requires PHP 5.3.0 or later.


-----

homepage: http://apigen.org
repository: http://github.com/nette/apigen
