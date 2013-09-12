SiteHelper
==========

SiteHelper is a collection of reusable code to aid in the building of PHP
websites.  Included are some useful classes to simplify various behaviour.
While there are many other such libraries avaiable, I am developing my own
from the ground up partly as a learning experience.

License
=======
This code is licensed under the MIT license.  A copy of this license can
be found in the file named license.txt

Usage
=====

The bootstrap files are located in the root directory of the project.  PHP
pages can load the bootstrap.php file.  It will automatically register the
class loader for any items under the mrbavii\sitehelper namespace.  The
bootstrap.js file can be included as well from HTML files, generated content,
etc.  It will provide some JavaScript functions under the mrbavii.sitehelper
name.

Stuff
=====
In addition some simple "stuff" is also provided under the mrbavii\sitestuff
namespace and under the javascript name mrbavii.sitestuff name.

