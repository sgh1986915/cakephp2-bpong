<?php
/* SVN FILE: $Id: core.php 7973 2011-12-14 09:20:30Z odikusar $ */
/**
 * This is core configuration file.
 *
 * Use it to configure core behavior of Cake.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.app.config
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision: 7973 $
 * @modifiedby		$LastChangedBy: odikusar $
 * @lastmodified	$Date: 2011-12-14 11:20:30 +0200  $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
Configure::load('sandbox', 'default');
/**
 * CakePHP Debug Level:
 *
 * Production Mode:
 * 	0: No error messages, errors, or warnings shown. Flash messages redirect.
 *
 * Development Mode:
 * 	1: Errors and warnings shown, model caches refreshed, flash messages halted.
 * 	2: As in 1, but also with full debug messages and SQL output.
 * 	3: As in 2, but also with full controller dump.
 *
 * In production mode, flash messages redirect after a time interval.
 * In development mode, you need to click the flash message to continue.
 */
 	define('LOG_ERROR', 1);
	Configure::write('debug', Configure::read('Sandbox.debug'));
/**
 * Application wide charset encoding
 */
	Configure::write('App.encoding', 'UTF-8');

	Configure::write('Session.model', 'Session');
	Configure::write('Session.save', 'database');
	Configure::write('Session.table', 'cake_sessions');
	Configure::write('Session.database', 'default');
/**
 * The name of CakePHP's session cookie.
 */
	Configure::write('Session.cookie', 'SESSION');
/**
 * Session time out time (in seconds).
 * Actual value depends on 'Security.level' setting.
 */
	Configure::write('Session.timeout', '1000');
/**
 * If set to false, sessions are not automatically started.
 */
	Configure::write('Session.start', true);
/**
 * When set to false, HTTP_USER_AGENT will not be checked
 * in the session
 */
	Configure::write('Session.checkAgent', false);
/**
 * The level of CakePHP security. The session timeout time defined
 * in 'Session.timeout' is multiplied according to the settings here.
 * Valid values:
 *
 * 'high'	Session timeout in 'Session.timeout' x 10
 * 'medium'	Session timeout in 'Session.timeout' x 100
 * 'low'	Session timeout in 'Session.timeout' x 300
 *
 * CakePHP session IDs are also regenerated between requests if
 * 'Security.level' is set to 'high'.
 */
	Configure::write('Security.level', 'low');
/**
 * A random string used in security hashing methods.
 */
	Configure::write('Security.salt', 'DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mo');
/**
 * Compress CSS output by removing comments, whitespace, repeating tags, etc.
 * This requires a/var/cache directory to be writable by the web server for caching.
 * and /vendors/csspp/csspp.php
 *
 * To use, prefix the CSS link URL with '/ccss/' instead of '/css/' or use HtmlHelper::css().
 */
	//Configure::write('Asset.filter.css', 'css.php');
/**
 * Plug in your own custom JavaScript compressor by dropping a script in your webroot to handle the
 * output, and setting the config below to the name of the script.
 *
 * To use, prefix your JavaScript link URLs with '/cjs/' instead of '/js/' or use JavaScriptHelper::link().
 */
	//Configure::write('Asset.filter.js', 'custom_javascript_output_filter.php');
/**
 * The classname and database used in CakePHP's
 * access control lists.
 */
	Configure::write('Acl.classname', 'DbAcl');
	Configure::write('Acl.database', 'default');

	date_default_timezone_set('UTC');
	set_time_limit(240);
	ini_set("max_execution_time", "240");
	ini_set('mysql.connect_timeout', '240');

	//Memcache
	if (Configure::read('Sandbox.environment') == 'dev') {
		$commonSettings = array('engine' => 'File', 'prefix' =>  'cake_', 'probability' => 100);
	} else {
		$commonSettings = array('engine' => 'Memcache', 'servers' => array('mc1.firstgen.bpong.40mm-app.internal:11211', 'mc2.firstgen.bpong.40mm-app.internal:11211'), 'prefix' =>  'cake_', 'probability' => 100);
	}
	Cache::config('default', array_merge($commonSettings, array('duration' => '+1 year')));
	Cache::config('store_categories', array_merge($commonSettings, array('duration' => '+1 hours')));
	Cache::config('full_time', array_merge($commonSettings, array('duration' => '+1 year')));
	Cache::config('tournament', array_merge($commonSettings, array('duration' => '+1 day')));
	Cache::config('markers', array_merge($commonSettings, array('duration' => '+1 hours')));
	Cache::config('new_stuff', array_merge($commonSettings, array('duration' => '+1 hours')));

	Configure::write('Error', array(
	'handler' => 'ErrorHandler::handleError',
	'level' => E_ALL & ~E_DEPRECATED,
	'trace' => true
	));
	
	Configure::write('Exception', array(
	'handler' => 'ErrorHandler::handleException',
	'renderer' => 'ExceptionRenderer',
	'log' => true
	));
	

