# APIGen

We have taken the original [David Grudl's](https://github.com/dg) [APIGen](https://github.com/nette/apigen) library and continuously modify it to suit our needs.

All changes can be found in separate branches. Each branch is based on the original library master and all changes are being sent via pull requests to the upstream.

Changes from [Jaroslav Hanslík's](https://github.com/kukulich) [fork](https://github.com/kukulich/apigen) are also incorporated - in branches prefixed "kukulich".

Being under rapid development, there are no "stable releases". Well, in fact... there are no "releases" at all :) You can always find the latest version in the master branch.

Currently there are following changes from the original APIGen:

* Bugfix: Better .sh apigen script b82e1a9b3ac1e716e89d8627556e0f256ce73e29
* Feature: Support for multiple/custom templates c43509e3d23ba7ea10f10fd2e434a526534e05d3
* Feature: The target directory can be optionally cleaned prior to generating cd6c208f49150b1133e5903d5045f8312b08c9d0
* Feature: Packages support for both namespaced and non-namespaced code (e3826c3a60939bb79759cfd8d6ec69aa102a30c5).
* Bugix: Class list output afc157adf4c065063477346e697058c078c0efdb
* Feature: Filtering namespaces/packages in sidebar when selecting a class/interface/package/namespace 4ff02076a1fc5c969cfde1a06e9d36f7740d43ce
* Feature: Class/interface tables/headings displayed only if there are some 05298a6c1822af9e2666b8be06ef2416b7368308
* Feature: Resizable sidebar add8fac451d5bdc3bbfd301f94fde67861eb7e7e
* Feature/Bugfix: Better page titles 5322e0babc1f8b0fe7b0401930040153df6a0549
* Feature: Progressbar while generating documentation af96c173b0585b56aa7bfa4f87e9f256f778061b
* Bugfix: (Kukulich) Texy should not process HTML code in docblocks 69b4e6836e6a60e6d96868c617e6ab11fbddfd61
* Bugfix: (Kukulich) Fixed highlighting of <code> and <pre> elements 84a24635afd79f61174847d89a8fec797452c1d0
* Bugfix: (Kukulich) Fixed FQNs handling d3e9db41cfdcffb779b5bb9b89ed5d43e113eaac
* Bugfix: (Kukulich) Support for line breaks in docblocks 75a18186c84da93d653473db4761ad736e11db31
* Feature: (Kukulich) Better parameter type output 34a2eefb97efa8d26355fcffb3d3450ad806182d
* Feature: (Kukulich) Output of inherited internal classes/interfaces a01e75fb2fb721a9232322b19e042c3585de934c