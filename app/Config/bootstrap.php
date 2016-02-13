<?php
/**
 * Short description for file.
 *
 * Long description for file
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
 * @since			CakePHP(tm) v 0.10.8.2117
 * @version			$Revision: 7966 $
 * @modifiedby		$LastChangedBy: odikusar $
 * @lastmodified	$Date: 2011-12-13 16:04:57 +0200 (Вт, 13 дек 2011) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 *
 * This file is loaded automatically by the app/webroot/index.php file after the core bootstrap.php is loaded
 * This is an application wide file to load any function that is not used within a class define.
 * You can also use this to include or require any files in your application.
 *
 */
// Load Composer autoload.
require ROOT . DS . 'Vendor/autoload.php';

// Remove and re-prepend CakePHP's autoloader as Composer thinks it is the
// most important.
// See: http://goo.gl/kKVJO7
spl_autoload_unregister(array('App', 'load'));
spl_autoload_register(array('App', 'load'), true, true);
/**
 * The settings below can be used to set additional paths to models, views and controllers.
 * This is related to Ticket #470 (https://trac.cakephp.org/ticket/470)
 *
 * $modelPaths = array('full path to models', 'second full path to models', 'etc...');
 * $viewPaths = array('this path to views', 'second full path to views', 'etc...');
 * $controllerPaths = array('this path to controllers', 'second full path to controllers', 'etc...');
 *
 */
	/**
	 * ID of the visitors
	 */

	define('VISITOR_USER', 1);
	define('VISITOR_GROUP', 1);
	define('REGISTRY_STATUS_ID', 5);//status ID for new users
	define('ACTIVE_STATUS_ID', 2); // status ID after user Activation
	// For VISITOR
	Configure::write('VisitorSession',
			array(
				'id' => VISITOR_USER,
				'firstname' => '',
				'middlename' => '',
				'lastname' => '',
				'lgn' => '',
				'email' => '',
				'last_logged' => '',
				'pre_last_logged' => '',
				'avatar' => '',
				'promocodes' => '',
				'timezone' => ''
			)
	);

	define('DEFAULT_LANG_ID', 1);
	define('ACTIVATION_KODE_EXPIRED', 72);//in hours

	define('SITE_NAME', 'firstgen.bpong.com');
	//define('DEFAULT_LANG', 'Global::defaultLanguage');
	define('ADMIN_EMAIL', 'odikusar@shakuro.com');

	define('PAGING_RANGE', '10,20,50');
	define('DEV_MODE', true);

	define('MCLOGIN', '190eb246db4747677d4f1e473a3b91f0-us1');
	define('MCPASSWORD', NULL);
	define('LISTID', 1);

	//Constants for ranking system..
	define('ELO_DIVISOR',2000);
    define('INITIAL_PLAYER_RATING',5000);
    define('PLAYER_TO_TEAM_RATING_MULT',.95);
    define('INITIAL_TEAM_RATING',5000);
    define('DEFAULT_AFFIL_RATING',5000);

    define('GAME_WEIGHT_MOBILE',5);
    define('GAME_WEIGHT_WEEKLIES',10);

	if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') {
		define('PROTOCOL', "http://");
		define('HTTPS_CONNECT', 0);
	} else {
		define('PROTOCOL', "https://");
		define('HTTPS_CONNECT', 1);
	}
	define('MANAGER_EMAIL', 'brandon.arnson@beerpong.com');
	define('TOURNAMENTS_EMAIL', 'tournaments@bpong.com');


	define('WSOBP_SIGNUP_LINK',"http://wsobp.eventbrite.com");

	// FEDEX constans
	define('FEDEX_METER', '1173978');
	define('FEDEX_ACCOUNT', '345242180');
	define('FEDEX_TEST_METER', '1173978');
	define('FEDEX_TEST_ACCOUNT', '345242180');
	        // USPS constans
	define ('USPS_USERNAME', '898BPONG2587');
	define ('USPS_PASSWORD', '260YG94YV726');

	// Google constans
	define('GOOGLE_API_KEY','ABQIAAAA8qJypDyl6wo_JmQGKB5suRTjh8dFNsjxW-BxNXVeRoKYL2a7NhRXiiZJPcOehNdH7MRV92taHFP3iQ');
	// Warehouses constans
	define('FCI_API_URL', 'http://promailv5.fulfillmentconcepts.com/pmdev/Import/XMLImp.asp?SysName=BPONG1');
	define('FCI_TEST_API_URL', 'http://promailv5-dev.fulfillmentconcepts.com/pmdev/Import/XMLImp.asp');
	define('FCI_WAREHOUSE_TAG', 'fci');
	define('FCI_WAREHOUSE_CODE', 'KY');

	define('FCI_TEST_FTP_ADDRESS', 'pizza.shakuro.net');
	define('FCI_TEST_FTP_LOGIN', 'pizza');
	define('FCI_TEST_FTP_PASSWORD', 'pizzapacks');

	define('FCI_FTP_ADDRESS', 'promailv5.fulfillmentconcepts.com');
	define('FCI_FTP_LOGIN', 'bpong');
	define('FCI_FTP_PASSWORD', 'User1219');

	define('WEBGISTIX_CUSTOMER_ID', '259');
	define('WEBGISTIX_PASSWORD', 'Webgistix');
	define('WEBGISTIX_USERNAME', 'Webgistix');

	//define('WEBGISTIX_WAREHOUSE_TAG', 'webgistix');	 !!!!! change
	//define('WEBGISTIX_WAREHOUSE_CODE', 'NV');

	define('WEBGISTIX_NV_WAREHOUSE_TAG', 'webgistix_nv');
	define('WEBGISTIX_NV_WAREHOUSE_CODE', 'NV');
	define('WEBGISTIX_NV_LOCATION_CODE', 'Vegas');

	define('WEBGISTIX_ATL_WAREHOUSE_TAG', 'webgistix_atl');
	define('WEBGISTIX_ATL_WAREHOUSE_CODE', 'ATL');
	define('WEBGISTIX_ATL_LOCATION_CODE', 'Atlanta');


	define('MOULTON_WAREHOUSE_TAG', 'moulton');
	define('MOULTON_WAREHOUSE_CODE', 'MLM');

	define('IPG_WAREHOUSE_TAG', 'ipg');
	define('IPG_WAREHOUSE_CODE', 'IPG');

	define('SIGN_ART_WAREHOUSE_TAG', 'sign_art');
	define('SIGN_ART_WAREHOUSE_CODE', 'SAE');

	//// STAET SALES TAXES array[srate_id][percent];
	$taxes['31']='10';
	Configure::write('SALES_TAXES', $taxes);
	//// EOF STAET SALES TAXES
	// STORE RESELLERS Constant Arrays Tag => Name
	Configure::write('Store.Reseller.Statuses',
		array('active' => 'Active',
			  'deactivated' => 'Deactivated',
			  'pending_terms' => 'Pending Agreement to Terms',
			  'pending_review' => 'Pending Initial Review'
		)
	);
	Configure::write('Store.Reseller.Types',
		array('individual' => 'Individual',
			  'partnership' => 'Partnership',
			  'company' => 'Limited Liability Company',
			  'corporation' => 'Corporation'
		)
	);
	// Resellers Discount Group ID
	Configure::write('Store.Reseller.GroupID', 2);
	// EOF STORE RESELLERS

	// EVENTS Types
	Configure::write('Event.Types',
		array('default' => 'Default',
			  'wsobp' => 'WSOBP',
			  'tournament' => 'Tournament',
              'nbplweekly'=>'NBPL Weekly',
              'nbplsatellite'=>'NBPL Satellite Tournament'
		)
	);                       
	Configure::write('Event.Relationship.Types',
		array('satellite' => 'Satellite',
			  'sub_event' => 'Sub-Event',
			  'tour_stop' => 'Tour-Stop'
		)
	);
	Configure::write(
		'Weekdays',
		array(
			'Sunday' => 'Sunday',
			'Monday' => 'Monday',
			'Tuesday' => 'Tuesday',
			'Wednesday' => 'Wednesday',
			'Thursday' => 'Thursday',
			'Friday' => 'Friday',
			'Saturday' => 'Saturday'
		)
	);

	// EOF EVENTS Types


	// DB SOURCE SETTINGS
	define('MASTER_TIME', 20);
	// EOF DB SOURCE SETTINGS
	   //session_start();

	// FACEBOOK KEYS
// old BPONG.COM	define('FACEBOOK_API_KEY', 'fac72172a569ca8f8142a4087cc47a8a');
// old BPONG.COM	define('FACEBOOK_SECRET_KEY', '31981db8ad87decb62d4d3ba8499d9db');
	define('FACEBOOK_API_KEY','342359459110899');
	define('FACEBOOK_SECRET_KEY','263cc300e2ebce8c2afaebb7b154e17a');
// EOF FACEBOKK KEYS

	// TWITTER KEYS
	define('TWITTER_CONSUMER_KEY', 'aX7416k0aU46VCqNTzmMg');
	define('TWITTER_SECRET_KEY', 'p88LtW6FfMMrlK6h2D3wEWU2wyMNbpwm7X3gJsvuihI');
	// EOF TWITTER KEYS
	Configure::write('User.ProhibitedNames', array('bpong', 'beerpong'));

	define('TMP_DIR', ROOT . DS . 'app' . DS. 'tmp' . DS);

	// RACKSPACE CLOUD HOSTING SETTINGS
	define('RACKSPACE_CLOUDFILE_USERNAME', 'fortymm2');
	define('RACKSPACE_CLOUDFILE_APIKEY', '02f202135186a5cadae723162e2a11b8');

	function setServ($serv) {
		if (HTTPS_CONNECT) {
			return 'ssl';
		} else {
			return $serv;
		}
	}
	
	// RACKSPACE CONTAINERS
	define('IMG_ALBUMS_URL', PROTOCOL . 'c801195.' . setServ('r95') . '.cf2.rackcdn.com');
	define('IMG_AVATARS_URL', PROTOCOL . 'c800631.' . setServ('r31') . '.cf2.rackcdn.com');
	define('IMG_MODELS_URL', PROTOCOL . 'c806876.' . setServ('r76') . '.cf2.rackcdn.com');
	define('IMG_SLIDES_URL', PROTOCOL . 'c806998.' . setServ('r98') . '.cf2.rackcdn.com');
    define('IMG_QRCODES_URL', PROTOCOL . 'c965952.' . setServ('r52') . '.cf2.rackcdn.com');
	define('IMG_NBPL_LAYOUTS_URL', PROTOCOL . 'c973595.' . setServ('r95') . '.cf2.rackcdn.com');
    define('IMG_BPONG_LAYOUTS_URL', PROTOCOL . 'c973596.' . setServ('r96') . '.cf2.rackcdn.com');

    define('IMG_WSOBP_1_URL', PROTOCOL . 'c973602.' . setServ('r2') . '.cf2.rackcdn.com');
    define('IMG_WSOBP_2_URL', PROTOCOL . 'c973603.' . setServ('r3') . '.cf2.rackcdn.com');
    define('IMG_WSOBP_3_URL', PROTOCOL . 'c973604.' . setServ('r4') . '.cf2.rackcdn.com');
    define('IMG_WSOBP_4_URL', PROTOCOL . 'c973605.' . setServ('r5') . '.cf2.rackcdn.com');
    define('IMG_WSOBP_5_URL', PROTOCOL . 'c973606.' . setServ('r6') . '.cf2.rackcdn.com');
    define('IMG_WSOBP_6_URL', PROTOCOL . 'c973607.' . setServ('r7') . '.cf2.rackcdn.com');
    define('IMG_WSOBP_7_URL', PROTOCOL . 'c973608.' . setServ('r8') . '.cf2.rackcdn.com');

    define('IMG_SATELLITES_URL', PROTOCOL . 'c973663.' . setServ('r63') . '.cf2.rackcdn.com');

    define('IMG_QRCODES', PROTOCOL . 'c965952.' . setServ('r52') . '.cf2.rackcdn.com');
    define('CSS_NBPL', PROTOCOL . 'ae919c44f27215954700-409cc735879c818751871fc6272cf952.' . setServ('r66') . '.cf1.rackcdn.com' . '/css_nbpl' );
    define('JS_NBPL', PROTOCOL . 'ae919c44f27215954700-409cc735879c818751871fc6272cf952.' . setServ('r66') . '.cf1.rackcdn.com' . '/js_nbpl');

    // EOF RACKSPACE CONTAINERS
    define('CURRENT_YEAR',2012);



    define('AES_ENCRYPT_KEY',"85428061B163CE1F");
    define('AES_ENCRYPT_IV',"1A5F1350C3B4D6A4");

	define('BPONG_URL',"http://www.bpong.com");

    define('ANDROID_APPLICATION_LINK','https://market.android.com/details?id=com.bpong.scorekeeper');
	define('USE_MOBILE_ENCRYPTION',0);

	if(!isset($_SERVER['HTTP_REFERER'])) {
		$_SERVER['HTTP_REFERER'] = '/';
	}
	
	
	
	
	
	Configure::write('Dispatcher.filters', array(
	'AssetDispatcher',
	'CacheDispatcher'
	));
	
	// Add logging configuration.
	CakeLog::config('debug', array(
	'engine' => 'FileLog',
	'types' => array('notice', 'info', 'debug'),
	'file' => 'debug',
	));
	CakeLog::config('error', array(
	'engine' => 'FileLog',
	'types' => array('warning', 'error', 'critical', 'alert', 'emergency'),
	'file' => 'error',
	));

	CakePlugin::load('DebugKit');
	CakePlugin::load('Cpamf');
