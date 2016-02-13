<?php
/* SVN FILE: $Id: routes.php 7918 2011-12-10 03:51:55Z _skinny $ */
/**
 * Short description for file.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
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
 * @version			$Revision: 7918 $
 * @modifiedby		$LastChangedBy: _skinny $
 * @lastmodified	$Date: 2011-12-10 05:51:55 +0200 (Сб, 10 дек 2011) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/views/pages/home.thtml)...
 */

	Router::connect('/', array('controller' => 'pages', 'action' => 'home'));

	$bpongRedirects = array('/storeOrders/*', '/store/*', '/StoreSlots/*', '/checkout/*', 
        '/resellers/*');

	foreach ($bpongRedirects as $bpongRedirect) {
		Router::connect($bpongRedirect, array('controller' => 'pages', 'action' => 'bpong_redirect'));
	}	
	

	Router::connect('/captcha/*', array('controller' => 'users', 'action' => 'captcha'));
    Router::connect('/reports', array('controller' => 'pages', 'action' => 'reports'));
	Router::connect('/uploader/*', array('controller' => 'albums', 'action' => 'uploader'));
	Router::connect('/submit/*', array('controller' => 'submissions', 'action' => 'index'));
    Router::connect('/cs/', array('controller' => 'pages', 'action' => 'customer_services'));
	Router::connect('/nation', array('controller' => 'pages', 'action' => 'nation'));
	Router::connect('/mailing-list', array('controller' => 'pages', 'action' => 'display', 'mailing_list'));
	Router::connect('/activated', array('controller' => 'pages', 'action' => 'display','activated'));
	Router::connect('/denied', array('controller' => 'pages', 'action' => 'display','denied'));

    Router::connect('/software',array('controller'=>'pages','action'=>'display','software'));

	Router::connect('/wsobp',  array('controller' => 'pages', 'action' => 'wsobp_new'));
	Router::connect('/wsobp/pricing/*',  array('controller' => 'pages', 'action' => 'wsobp_pricing'));
	Router::connect('/wsobp/gallery/*',  array('controller' => 'pages', 'action' => 'wsobp_gallery'));
	Router::connect('/viewbrackets/*',  array('controller' => 'pages', 'action' => 'viewbrackets'));
	Router::connect('/viewprojector/*',array('controller'=>'pages','action'=>'viewprojector'));
	Router::connect('/bidwinners',array('controller'=>'pages','action'=>'display','bidwinners'));

	// Organizations
	Router::connect('/o/*', array('controller' => 'organizations', 'action' => 'show'));
	Router::connect('/o_news/*', array('controller' => 'organization_news', 'action' => 'org_list'));
	Router::connect('/o_members/*', array('controller' => 'organizations_users', 'action' => 'org_list'));
	Router::connect('/o_join/*', array('controller' => 'organizations_users', 'action' => 'joinUser'));
	Router::connect('/o_about/*', array('controller' => 'organizations', 'action' => 'about'));
	Router::connect('/o_albums/*', array('controller' => 'organizations', 'action' => 'albums'));
	Router::connect('/o_events/*', array('controller' => 'organizations_objects', 'action' => 'list_events'));
	Router::connect('/o_venues/*', array('controller' => 'organizations_objects', 'action' => 'list_venues'));


	Router::connect('/wsobp/spencers',  array('controller' => 'pages', 'action' => 'display', 'spencers'));

	Router::connect('/nation', array('controller' => 'pages', 'action' => 'display','nation'));

	Router::connect('/vault', array('controller' => 'pages', 'action' => 'display','vault'));
	Router::connect('/privacy', array('controller' => 'pages', 'action' => 'display','privacy'));
	Router::connect('/contact/*', array('controller' => 'pages', 'action' => 'contactnew'));
	Router::connect('/sitemap', array('controller' => 'pages', 'action' => 'display','sitemap'));
	Router::connect('/pressroom', array('controller' => 'pages', 'action' => 'display','pressroom'));
	Router::connect('/drinkresponsibly', array('controller' => 'pages', 'action' => 'display','drinkresponsibly'));
	Router::connect('/terms', array('controller' => 'pages', 'action' => 'display','terms'));
	Router::connect('/affiliate', array('controller' => 'pages', 'action' => 'display','affiliate'));

	Router::connect('/signups/upgradePackage/*', array('controller' => 'signups', 'action' => 'changePackage','isUpgrade'=>1));

    Router::connect('/vault/last-cup-road-to-the-world-series-of-beer-pong',  array('controller' => 'pages', 'action' => 'last_cup'));
	Router::connect('/vault/beer-pong-faq', array('controller' => 'pages', 'action' => 'display','faq'));
	Router::connect('/vault/general-beer-pong-rules', array('controller' => 'pages', 'action' => 'display','rules'));
	Router::connect('/wsobp/official-rules-of-the-world-series-of-beer-pong', array('controller' => 'pages', 'action' => 'display','wsobp_rules'));
	Router::connect('/code_of_conduct', array('controller' => 'pages', 'action' => 'display','code_of_conduct'));
    Router::connect('/wsobp/results/*', array('controller' => 'events', 'action' => 'wsobp_results'));


	//Router::connect('/wsobp/world-series-of-beer-pong-satellite-tournaments', array('controller' => 'pages', 'action' => 'satellite_tournament_'));
	//Router::connect('/wsobp/world-series-of-beer-pong-satellite-tournaments', array('controller' => 'satellites', 'action' => 'index'));
	Router::connect('/(?i)ReturnToMesquite',array('controller'=>'events','action'=>'view','694'));
	Router::connect('/wsobp/stats/world-series-of-beer-pong-IV', array('controller' => 'pages', 'action' => 'display','stats'));
	//Working with teams
	Router::connect('/tour', array('controller' => 'pages', 'action' => 'tour'));
    Router::connect('/nation/beer-pong-teams/team-info/*', array('controller' => 'teams', 'action' => 'view'));
	Router::connect('/nation/beer-pong-teams/edit-team/*', array('controller' => 'teams','action' => 'edit'));
	Router::connect('/nation/beer-pong-teams/new-team/*', array('controller' => 'teams','action' => 'add'));
	Router::connect('/nation/beer-pong-teams/delete-team/*', array('controller' => 'teams','action' => 'delete'));
	Router::connect('/nation/beer-pong-teams/team-assigments/*', array('controller' => 'teams','action' => 'assigments'));
	Router::connect('/nation/beer-pong-teams/assign-team/*', array('controller' => 'teams','action' => 'AssignTournEvent'));
	Router::connect('/nation/beer-pong-teams/team-events/*', array('controller' => 'teams','action' => 'eventteams'));
    Router::connect('/nation/beer-pong-teams/team-tournaments/*', array('controller' => 'teams','action' => 'tournamentteams'));
    Router::connect('/wsobp/beer-pong-teams/*', array('controller' => 'teams','action' => 'wsobp'));
    Router::connect('/nation/beer-pong-teams/myteams', array('controller' => 'teams','action' => 'myteams'));
    Router::connect('/nation/beer-pong-teams/show-all-teams', array('controller' => 'teams','action' => 'index'));
    Router::connect('/nation/beer-pong-teams/*', array('controller' => 'teams','action' => 'allTeams'));


	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
	Router::connect('/tag/*', array('controller' => 'tags', 'action' => 'show'));
	// End

	Router::connect('/profile/*', array('controller' => 'users', 'action' => 'profile'));
	Router::connect('/u/*', array('controller' => 'users', 'action' => 'profile'));
	Router::connect('/myprofile/*',array('controller'=>'users','action'=>'myprofile'));
    Router::connect('/users/view/*', array('controller' => 'users', 'action' => 'profile'));

    Router::connect('/t/*',array('controller'=>'teams','action'=>'view'));

    Router::connect('/tests', array('controller' => 'tests', 'action' => 'index'));
	Router::connect('/login', array('controller' => 'users', 'action' => 'loginForm'));
	Router::connect('/activation/*', array('controller' => 'users', 'action' => 'activation'));
	Router::connect('/logout', array('controller' => 'users', 'action' => 'logout'));
	Router::connect('/registration', array('controller' => 'users', 'action' => 'registration'));

	/*Signups*/
	Router::connect('/signups/Tournament/the-world-series-of-beer-pong-vii', array('controller' => 'pages', 'action' => 'home'));
	Router::connect('/signups/Tournament/*', array('controller' => 'signups', 'action' => 'step1','Tournament'));
	Router::connect('/signups/Event/*', array('controller' => 'signups', 'action' => 'step1','Event'));

	Router::connect('/newpassword/*', array('controller' => 'users', 'action' => 'newpassword'));

/* Forum urls */
	//Router::connect('/nation/beer-pong-forum', array('controller' => 'forumbranches', 'action' => 'index'));


	Router::connect('/nation/beer-pong-forum/add/*', array('controller' => 'forumbranches', 'action' => 'add'));
	Router::connect('/nation/beer-pong-forum/edit/*', array('controller' => 'forumbranches', 'action' => 'edit'));
	Router::connect('/nation/beer-pong-forum/delete/*', array('controller' => 'forumbranches', 'action' => 'delete'));

	Router::connect('/nation/beer-pong-forum/addtopic/*', array('controller' => 'forumtopics', 'action' => 'add'));
	Router::connect('/nation/beer-pong-forum/edittopic/*', array('controller' => 'forumtopics', 'action' => 'edit'));
	Router::connect('/nation/beer-pong-forum/deletetopic/*', array('controller' => 'forumtopics', 'action' => 'delete'));

	Router::connect('/nation/beer-pong-forum/topics/*', array('controller' => 'forumposts', 'action' => 'index'));
	Router::connect('/nation/beer-pong-forum/addpost/*', array('controller' => 'forumposts', 'action' => 'add'));
	Router::connect('/nation/beer-pong-forum/editpost/*', array('controller' => 'forumposts', 'action' => 'edit'));
	Router::connect('/nation/beer-pong-forum/deletepost/*', array('controller' => 'forumposts', 'action' => 'delete'));

	Router::connect('/nation/beer-pong-forum/*', array('controller' => 'forumbranches', 'action' => 'index'));
    /* EOF of forum url */

	Router::connect('/nation/beer-pong-game*', array('controller' => 'onlinegames', 'action' => 'index'));
	Router::connect('game', array('controller' => 'onlinegames', 'action' => 'index'));
	Router::connect('/game/', array('controller' => 'onlinegames', 'action' => 'index'));

	/*CUSTOM TABLES*/
	Router::connect('/nation/beer-pong-custom-tables/edit/*', array('controller' => 'pongtables', 'action' => 'edit'));
	Router::connect('/nation/beer-pong-custom-tables/add/*', array('controller' => 'pongtables', 'action' => 'add'));
	Router::connect('/nation/beer-pong-custom-tables/delete/*', array('controller' => 'pongtables', 'action' => 'delete'));
	Router::connect('/nation/beer-pong-custom-tables/*', array('controller' => 'pongtables', 'action' => 'index'));

	Router::connect('/event/*', array('controller' => 'events', 'action' => 'view'));
	//Router::connect('/tournaments/view/*', array('controller' => 'events', 'action' => 'view'));
	
	Router::connect('/nbpl_nights/*', array('controller' => 'venues', 'action' => 'nbpl_nights'));	

  //Blog
  Router::connect('/nation/beer-pong-blog', array('controller' => 'blogposts', 'action' => 'index'));
  Router::connect('/nation/beer-pong-blog/add', array('controller' => 'blogposts', 'action' => 'add'));
  Router::connect('/nation/beer-pong-blog/delete/*', array('controller' => 'blogposts', 'action' => 'delete'));
  Router::connect('/nation/beer-pong-blog/view/*', array('controller' => 'blogposts', 'action' => 'view'));
  Router::connect('/nation/beer-pong-blog/edit/*', array('controller' => 'blogposts', 'action' => 'edit'));
  Router::connect('/nation/beer-pong-blog/*', array('controller' => 'blogposts', 'action' => 'index'));

  //Events
  Router::connect('/wsobpspectator', array('controller' => 'events', 'action' => 'view', 'world-series-of-beer-pong-vi-spectator-pass'));
  Router::connect('/wsobp/world-series-of-beer-pong-satellite-tournaments/*', array('controller' => 'events', 'action' => 'index', 'satellite'));
  Router::connect('/video/*', array('controller' => 'videos', 'action' => 'show'));

  //New stuff
  Router::connect('/all_submissions/', array('controller' => 'submissions', 'action' => 'new_stuff', 'All'));
  Router::connect('/videos/', array('controller' => 'submissions', 'action' => 'new_stuff', 'Video'));
  Router::connect('/images/', array('controller' => 'submissions', 'action' => 'new_stuff', 'Image'));
  Router::connect('/links/', array('controller' => 'submissions', 'action' => 'new_stuff', 'Link'));
  Router::connect('/videos/index/*', array('controller' => 'submissions', 'action' => 'new_stuff', 'Video'));
  Router::connect('/images/index/*', array('controller' => 'submissions', 'action' => 'new_stuff', 'Image'));
  Router::connect('/links/index/*', array('controller' => 'submissions', 'action' => 'new_stuff', 'Link'));
  Router::connect('/all_submissions/index/*', array('controller' => 'submissions', 'action' => 'new_stuff', 'All'));
  //Extensions

  //College discounts
  Router::connect('/unlv', array('controller' => 'collegediscounts', 'action' => 'landingpage','unlv'));
  Router::connect('/UNLV', array('controller' => 'collegediscounts', 'action' => 'landingpage','unlv'));
  Router::connect('/Tempe12', array('controller' => 'collegediscounts', 'action' => 'landingpage','asu'));
  Router::connect('/TEMPE12', array('controller' => 'collegediscounts', 'action' => 'landingpage','asu'));
  Router::connect('/tempe12', array('controller' => 'collegediscounts', 'action' => 'landingpage','asu'));
  Router::connect('/ASU',array('controller'=>'collegediscounts','action'=>'landingpage','asu','facebook'));
  Router::connect('/Tuscon12', array('controller' => 'collegediscounts', 'action' => 'landingpage','arizona'));
  Router::connect('/TUSCON12', array('controller' => 'collegediscounts', 'action' => 'landingpage','arizona'));
  Router::connect('/tuscon12', array('controller' => 'collegediscounts', 'action' => 'landingpage','arizona'));
  Router::connect('/Tucson12', array('controller' => 'collegediscounts', 'action' => 'landingpage','arizona'));
  Router::connect('/TUCSON12', array('controller' => 'collegediscounts', 'action' => 'landingpage','arizona'));
  Router::connect('/tucson12', array('controller' => 'collegediscounts', 'action' => 'landingpage','arizona'));

  // Bars
  Router::connect('/about_nbpl_bar_program/*', array('controller' => 'pages', 'action' => 'display', 'about_nbpl_bar_program')); 
  Router::connect('/bar_program_packages/*', array('controller' => 'pages', 'action' => 'display', 'bar_program_packages'));
  Router::connect('/apply_bar/*', array('controller' => 'pages', 'action' => 'display', 'apply_bar'));
  Router::connect('/bar_program_faq/*', array('controller' => 'pages', 'action' => 'display', 'bar_program_faq')); 
  Router::connect('/handicapping',array('controller'=>'pages','action'=>'display','handicapping'));
  Router::connect('/contact_bar_support/*', array('controller' => 'pages', 'action' => 'display', 'contact_bar_support'));
  Router::connect('/recommend_bar/*', array('controller' => 'pages', 'action' => 'display', 'recommend_bar'));   
  
  Router::connect('/about_nbpl_points/*',array('controller'=>'pages','action'=>'display','about_nbpl_points'));
  Router::connect('/about_nbpl/*', array('controller' => 'pages', 'action' => 'display', 'about_nbpl'));  
  Router::connect('/redeem_points/*',array('controller'=>'pages','action'=>'display','redeem_points'));
  Router::connect('/season_and_structure/*', array('controller' => 'pages', 'action' => 'display', 'season_and_structure'));    
  Router::connect('/final_bracket/*', array('controller' => 'pages', 'action' => 'display', 'final_bracket'));
  
  Router::connect('/get_the_app/*', array('controller' => 'pages', 'action' => 'get_the_app'));  
  
  // Stats pages
  Router::connect('/players_stats/*', array('controller' => 'rankings', 'action' => 'players_stats')); 
  Router::connect('/teams_stats/*', array('controller' => 'rankings', 'action' => 'teams_stats')); 
  Router::connect('/schools_stats/*', array('controller' => 'rankings', 'action' => 'schools_stats'));
  Router::connect('/greeks_stats/*', array('controller' => 'rankings', 'action' => 'greeks_stats')); 
  Router::connect('/organizations_stats/*', array('controller' => 'rankings', 'action' => 'organizations_stats')); 
  Router::connect('/cities_stats/*', array('controller' => 'rankings', 'action' => 'cities_stats'));
  Router::connect('/states_stats/*', array('controller' => 'rankings', 'action' => 'states_stats'));
  Router::connect('/countries_stats/*', array('controller' => 'rankings', 'action' => 'countries_stats'));

  Router::connect('/school/*', array('controller' => 'schools', 'action' => 'show'));
  Router::connect('/city/*', array('controller' => 'cities', 'action' => 'show'));
  Router::connect('/greek/*', array('controller' => 'greeks', 'action' => 'show'));
      
  //Rankings/index goes to explanation
  Router::connect('/rankings',array('controller'=>'rankings','action'=>'explanation'));
  //Software Web Demo
  Router::connect('/softwaredemo',array('controller'=>'pages','action'=>'softwareWebDemo'));
  
  Router::parseExtensions('rss');
  Router::connect('/cpamf/:action/*', array('plugin'=>'cpamf', 'controller' => 'cpamf'));

  //Redirection for mobile ads
  Router::connect('/mobileappredirect/*',array('controller'=>'Pages','action'=>'mobileredirect'));
  //App download page
  Router::connect('/app/*',array('controller'=>'pages','action'=>'redirect_android_market'));
  
  Router::connect('/livestream',array('controller'=>'pages','action'=>'livestream'));

  require CAKE . 'Config' . DS . 'routes.php';
