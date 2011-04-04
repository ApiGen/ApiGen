# APIGen

We have taken the original [David Grudl's](https://github.com/dg) [APIGen](https://github.com/nette/apigen) library and continuously modify it to suit our needs.

Most changes are being sent via pull requests to the upstream. But not everything (some changes make sense only for us) and not at once (some changes depend on each other). Moreover we've decided to stop creating feature branches from upstream's master branch, because it makes merging into our master more and more painful. We will eventually create individual upstream-based branches just for pull requests.

Changes from [Jaroslav Hanslík's](https://github.com/kukulich) [fork](https://github.com/kukulich/apigen) are also incorporated - in branches prefixed "kukulich".

Being under rapid development, there are no "stable releases". Well, in fact... there are no "releases" at all :) You can always find the latest version in the master branch.

The [Jyxo PHP Library](https://github.com/jyxo) (version [with namespaces](http://jyxo.github.com/php), [without](http://jyxo.github.com/php-no-namespace)) API documentation is generated with the latest version.

Currently there are following changes from the original APIGen:

* Bugfix: Better .sh apigen script.
* Feature: Support for multiple/custom templates.
* Feature: The target directory can be optionally cleaned prior to generating.
* Feature: Packages support for both namespaced and non-namespaced code.
* Bugfix: Class list output.
* Feature: Filtering namespaces/packages in sidebar when selecting a class/interface/package/namespace.
* Feature: Class/interface tables/headings displayed only if there are some.
* Feature: Resizable sidebar.
* Feature/Bugfix: Better page titles.
* Feature: Optional progressbar while generating documentation.
* Feature: Better versioning of static files (stylesheets, …).
* Bugfix: (Kukulich) Texy should not process HTML code in docblocks.
* Bugfix: (Kukulich) Fixed highlighting of code and pre elements.
* Bugfix: (Kukulich) Fixed FQNs handling.
* Bugfix: (Kukulich) Support for line breaks in docblocks.
* Feature: (Kukulich) Better parameter type output.
* Feature: (Kukulich) Output of inherited internal classes/interfaces.