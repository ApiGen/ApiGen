# APIGen

We have taken the original [David Grudl's](https://github.com/dg) [APIGen](https://github.com/nette/apigen) library and continuously modify it to suit our needs.

All changes can be found in separate branches. Each branch is based on the original library master and all changes are being sent via pull requests to the upstream.

Changes from [Jaroslav Hanslík's](https://github.com/kukulich) [fork](https://github.com/kukulich/apigen) are also incorporated - in branches prefixed "kukulich".

Being under rapid development, there are no "stable releases". Well, in fact... there are no "releases" at all :) You can always find the latest version in the master branch.

Currently there are following changes from the original APIGen:

* Bugfix: Better .sh apigen script.
* Feature: Support for multiple/custom templates.
* Feature: The target directory can be optionally cleaned prior to generating.
* Feature: Packages support for both namespaced and non-namespaced code.
* Bugix: Class list output.
* Feature: Filtering namespaces/packages in sidebar when selecting a class/interface/package/namespace.
* Feature: Class/interface tables/headings displayed only if there are some.
* Feature: Resizable sidebar.
* Feature/Bugfix: Better page titles.
* Feature: Progressbar while generating documentation.
* Bugfix: (Kukulich) Texy should not process HTML code in docblocks.
* Bugfix: (Kukulich) Fixed highlighting of code and pre elements.
* Bugfix: (Kukulich) Fixed FQNs handling.
* Bugfix: (Kukulich) Support for line breaks in docblocks.
* Feature: (Kukulich) Better parameter type output.
* Feature: (Kukulich) Output of inherited internal classes/interfaces.