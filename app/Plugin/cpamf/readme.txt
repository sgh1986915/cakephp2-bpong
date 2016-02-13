================================================================================
CpAmf - Amf plugin for cakePHP 1.2
ver. 0.12
$Id: readme.txt 103 2009-07-08 12:41:21Z daniel.verner $
================================================================================

Authors: Daniel Verner, Arnold Remete
		CarrotPlant Ltd.
		2009
		
Email:  verner@carrotplant.com
Web:    carrotplant.com

=============
Requirements
=============

1. CakePHP 1.2 - http://cakephp.org/
2. AMFPHP 1.9 (included in package) - http://www.amfphp.org/
3. AMF extension(optional) - http://www.teslacore.it/wiki/index.php?title=AMFEXT

========
Preface
========

The goal of this project was to use cakePHP controllers, as flash remoting services.
The basic idea was to use AMFPHP 1.9, because we used it before, and it was a good 
choice in earlier projects. As nobody wants to "reinvent the wheel", I googled for
a solution to integrate the AMFPHP into the cakePHP framework.

After a short research I found following solutions:
1. cakeAMFPHP plugin - http://cakeforge.org/projects/cakeamfphp/
2. cakeAMF plugin - https://trac.cakefoundation.org/amf/

I tried both solutions, but cakeAMFPHP does not use the latest AMFPHP, and 
with cakeAMF there was a problem using flex RemoteObjects.

The idea for my implementation came from cakeAMFPHP. 

Special thanks to the developers of:
1. cakeAMFPHP plugin - http://cakeforge.org/projects/cakeamfphp/
2. cakeAMF plugin - https://trac.cakefoundation.org/amf/
3. AMFPHP - http://www.amfphp.org/
4. cakePHP - http://cakephp.org/

Special thanks to Wade Arnold for his solution to map flex arraycollection 
to php array: 
http://wadearnold.com/blog/?p=18

Any comments and suggestions are welcome.

=============
Introduction
=============

This plugin is based on the latest AMFPHP package, and works with cakePHP 1.2.
The amf plugin uses all features of the AMFPHP package: works with or without 
the amf php extension (if amfext is installed and enabled, it will be automatically use by this plugin).
Amf plugin allows you to use cakePHP controllers as "services", using all cakePHP
controller features (models, behaviors etc. ), also works with flex RemotingObject,
and can be used with MATE framework (flex).


=============
Installation
=============

Just copy the plugin into your cake application's "plugins" directory. 
To check the gateway installation, simply open the following url:
yourdomain.com/cpamf/gateway.

IMPORTANT: There is no trailing .php after the gateway url. By default (at the first run)
the service browser offers yourdomain.com/cpamf/gateway.php instead of /gateway,
so take care of the gateway url!

You should see a message like this:
amfphp and this gateway are installed correctly.
You may now connect to this gateway from Flash.

If you have the amf extension you shuld see this in message:
AMF C Extension is loaded and enabled.

=====================
Value Object mapping
=====================

Amfphp has a useful feature: the VO mapping. Cpamf plugin uses this feature in a bit
specialized way. We create a model in cake php, and a Class in flex which 
corresponds to our model.
The metedata tag to achieve this mapping is:
[RemoteClass(alias="User")]

In our model we create an afterFilter method, and use the cakePHP built in Set::Map() 
function to convert the associative array to an object (or array of objects). We use
generic class here (php's dummy class 'stdClass'), set the _explicitType property
of all objects, and unset the _name_ property (which is set by Set::Map() method),
because we don't need this property in our flex class. This approach allows
us to change the model, and the corresponding flex class without the need for 
changing the vo classes (we don't even need to create them).

When we get data (object) from flex we don't use mapping, on php side we use associative
arrays.

The amfphp vo directory is set to default value (vendors/amfphp/services/vo). You can change this
value if you want to use objects on php side. (vendors/amfphp/globals.php).

We use one special vo class: ArrayCollection.php, it allows us to map an array of
objects (php side) to an ArrayCollection of objects (flex side). Thanks to 
Wade Arnold for this solution. 

==========================
Using the service browser
==========================

AMFPHP comes with a handy utility called service browser,
which is useful for testing the services (or in this case controllers).
Although you can test your controllers with CakePHP itself, but if you want
to test them using flash remoting you can use the service browser.
The browser is accessible at the following url:
yourdomain.com/cpamf/browser.

NOTE: The service browser is accessible only when the 'Cpamf.serviceBrowserEnabled' is set to 1.
in the cpamf_app_controller.

CakePHP controllers have many inherited methods from the AppController, and usually we
don't need to see nor access them through the service browser. If the method prefix is
set in the vendors/amfphp/globals.php (define( "METHOD_PREFIX", "prefix" );), then
the service browser will only display methods beginning with the specified prefix.

====================
Debug mode disabled
====================

The CakePHP debug mode is always set to 0 in the cpamf plugin's AppController.
This is a necessary step to prevent the CakePHP's SQL Log to appear in the output.
Any unwanted strings in the output would result in a "Channel Disconnected" error on the
Flex client side.

===============
Authentication
===============

In the previous version of the cpamf plugin there was a problem using the plugin
with the enabled Auth component. In this release this bug is fixed. When the
Auth component is enabled, the cpamf plugin allows three actions in cpamf_controller:
index, gateway, and browser. These actions are allowed to all users. All
other controller actions (used through the cpamf plugin) are controlled by the main application.

You can find more information about the usage of the Auth component here:
http://mark-story.com/posts/view/auth-and-acl-an-end-to-end-tutorial-pt-1

============
No Warranty
============

The Software is provided "AS IS" and without warranty of any kind.

========
Licence
========

The MIT License
http://www.opensource.org/licenses/mit-license.php 