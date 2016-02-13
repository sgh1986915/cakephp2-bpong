<?php 
$topMenus = array(
	'Home' => array('title' =>'Home', 'link' => MAIN_SERVER),
	'About' => array('title' =>'About Beer Pong', 'link' => MAIN_SERVER .'/about_nbpl'),
	'Play' => array('title' =>'Play Beer Pong', 'link' => MAIN_SERVER . '/events'),
	'Wsobp' => array('title' =>'World Series of Beer Pong', 'link' => MAIN_SERVER . '/wsobp'),
	'Bar' => array('title' =>'Own a Bar?', 'link' => MAIN_SERVER . '/about_nbpl_bar_program'),
	'App' => array('title' =>'Get the app', 'link' => MAIN_SERVER . '/get_the_app', 'hide_submenu' => true),
	'Stats' => array('title' =>'Stats', 'link' => MAIN_SERVER . '/players_stats'),
	'News' => array('title' =>'News', 'link' => MAIN_SERVER . '/nation/beer-pong-blog'),
	'Contact' => array('title' =>'Contact us', 'link' => MAIN_SERVER . '/contact', 'hide_submenu' => true),
	'Store' => array('title' =>'Official store', 'link' => 	BPONG_URL . '/store', 'class' => 'storemenu')
);
$MENU['Default']['config'] = array('menu_detect_var' => false, 'left_menu' => false, 'breadcrumbs' => false, 'top_info_block' => false, 'top_info_block_element' => false, 'main_col' => true, 'parent_title' => '', 'parent_link' => '', 'column' => 1, 'column_name_1' => '', 'column_name_2' => '', 'column_name_3' => '', 'detect_by_url' => false, );
//$MENU['Default'][] = array('name' => '', 'text' => '', 'link' => '', 'urls' => array('/index'));

$MENU['Home'][1][] = array('title' => '', 'text' => '', 'left_menu' => false, 'breadcrumbs' => false, 'main_col' => false, 'link' => MAIN_SERVER, 'urls' => array('/pages/home'));
$MENU['Home'][1][] = array('title' => 'My Profile', 
    'link'=>'/myprofile', 
    'urls' => array('/myprofile'));
$MENU['Home'][1][] = array('title'=>'My Signups',
    'link'=>'/signups/mySignups',
    'urls'=>array());
$MENU['Home'][1][] = array('title'=>'My Teams',
    'link'=>'/nation/beer-pong-teams/myteams',
    'urls'=>array());
$MENU['Home'][1][] = array('title'=>'Logout',
    'link'=>'/logout',
    'urls'=>array());
$MENU['Home'][2][] = array('title'=>'My Events',
    'link'=>'/events/my',
    'urls'=>array());
$MENU['Home'][2][] = array('title'=>'My Venues',
    'link'=>'/venues/my',
    'urls'=>array());   
$MENU['Home'][2][] = array('title'=>'My Organizations',
    'link'=>'/organizations/my',
    'urls'=>array()); 
// About
$MENU['About']['config'] = array('parent_title' => false, 'parent_link' => false, 'left_menu' => false, 'breadcrumbs' => false, 'main_col' => true, 'column_name_1' => 'Galleries', 'column_name_2' => 'Info');
$MENU['About'][1][] = array('title' => 'Images', 'link' => MAIN_SERVER . '/images/', 'urls' => array('/submissions/new_stuff'));
$MENU['About'][1][] = array('title' => 'Videos', 'link' => MAIN_SERVER . '/videos/', 'urls' => array('/videos/'));
$MENU['About'][1][] = array('title' => 'Players', 'link' => MAIN_SERVER . '/Users/show_all', 'urls' => array('/users/show_all'));
$MENU['About'][1][] = array('title' => 'Custom tables', 'link' => MAIN_SERVER . '/nation/beer-pong-custom-tables', 'urls' => array('/pongtables/index'));
$MENU['About'][2][] = array('title' => 'Learn the rules', 'left_menu' => true, 'link' => MAIN_SERVER . '/vault/general-beer-pong-rules', 'urls' => array('/vault/general-beer-pong-rules'));
$MENU['About'][2][] = array('title' => 'Frequently Asked Questions', 'left_menu' => true, 'link' => MAIN_SERVER . '/vault/beer-pong-faq', 'urls' => array('/vault/beer-pong-faq'));
$MENU['About'][2][] = array('title' => 'About the NBPL', 'link' => MAIN_SERVER . '/about_nbpl', 'left_menu' => true, 'urls' => array('/about_nbpl'));
$MENU['About'][2][] = array('title' => 'Handicapping', 'link'=>MAIN_SERVER.'/handicapping', 'left_menu'=>true,'urls'=>array('/handicapping'));
$MENU['About'][3][] = array('title' => 'Teams', 'link' => MAIN_SERVER . '/nation/beer-pong-teams', 'urls' => array('/teams/allteams'));
$MENU['About'][3][] = array('title' => 'Organizations', 'link' => MAIN_SERVER . '/organizations', 'left_menu' => true, 'urls' => array('/organizations/index'));

// Play
$MENU['Play']['config'] = array('parent_title' => false, 'parent_link' => false, 'left_menu' => true, 'breadcrumbs' => false, 'main_col' => true, 'column_name_1' => 'Events');
$MENU['Play'][1][] = array('title' => 'All events', 'link' => MAIN_SERVER . '/events', 'menu_detect_var' => 'events_all', 'top_info_block_element' => 'events_list_top_info');
$MENU['Play'][1][] = array('title' => 'Tournaments', 'link' => MAIN_SERVER . '/events/index/tournament', 'menu_detect_var' => 'events_tournament', 'top_info_block_element' => 'events_list_top_info');
$MENU['Play'][1][] = array('title' => 'Satellite Tournaments', 'link' => MAIN_SERVER . '/wsobp/world-series-of-beer-pong-satellite-tournaments', 'menu_detect_var' => 'events_tournament', 'menu_detect_var' => 'events_satellite', 'top_info_block_element' => 'events_list_top_info');
$MENU['Play'][2][] = array('title' => 'Want to run a tournament<br/> at your bar or venue?', 'link' => MAIN_SERVER . '/about_nbpl_bar_program', 'urls' => array('/contact/tournaments'));
$MENU['Play'][2][] = array('title' => 'Find an NBPL League Night', 'link' => MAIN_SERVER . '/nbpl_nights', 'urls' => array('/venues/nbpl_nights'));

// Wsobp
$MENU['Wsobp']['config'] = array('parent_title' => false, 'parent_link' => false, 'left_menu' => true, 'breadcrumbs' => false, 'main_col' => true, 'column_name_1' => 'All about the event');
$MENU['Wsobp'][1][] = array('title' => 'LIVE final bracket', 'link' => MAIN_SERVER . '/viewbrackets/644', 'urls' => array());
$MENU['Wsobp'][1][] = array('title' => 'WSOBP 2011 Teams', 'left_menu' => false, 'link' => MAIN_SERVER . '/wsobp/beer-pong-teams', 'urls' => array('/teams/wsobp'));
$MENU['Wsobp'][1][] = array('title' => 'Sign up', 'left_menu' => false, 'link' => MAIN_SERVER . '/wsobp', 'urls' => array('/wsobp', '/signups'));
$MENU['Wsobp'][1][] = array('title' => 'The Official Rules', 'link' => MAIN_SERVER . '/wsobp/official-rules-of-the-world-series-of-beer-pong', 'urls' => array('/wsobp/official-rules-of-the-world-series-of-beer-pong'));

$MENU['Wsobp'][2][] = array('title' => 'Gallery', 'left_menu' => false, 'link' => MAIN_SERVER . '/wsobp/gallery', 'urls' => array('/wsobp/gallery'));
$MENU['Wsobp'][2][] = array('title' => 'Pricing', 'left_menu' => false, 'link' => MAIN_SERVER . '/wsobp/pricing', 'urls' => array('/wsobp/pricing'));
$MENU['Wsobp'][2][] = array('title' => 'Satellite tournaments', 'link' => MAIN_SERVER . '/wsobp/world-series-of-beer-pong-satellite-tournaments', 'urls' => array());
$MENU['Wsobp'][2][] = array('title' => 'WSOBP Archive', 'link' => MAIN_SERVER . '/wsobp/gallery', 'urls' => array());

$MENU['Wsobp'][3][] = array('title' => 'Frequently Asked Questions', 'link' => MAIN_SERVER . '/vault/beer-pong-faq', 'urls' => array('/vault/beer-pong-faq'));
// Bar
$MENU['Bar']['config'] = array('parent_title' => false, 'parent_link' => false, 'left_menu' => true, 'breadcrumbs' => false, 'main_col' => false, 'column_name_1' => 'Bring the fun to your place', 'top_info_block' => '<img src="' . IMG_NBPL_LAYOUTS_URL . '/bar-program-hero.jpg" width="960px" border="0">');
$MENU['Bar'][1][] = array('title' => 'About the NBPL Bar Program', 'link' => MAIN_SERVER . '/about_nbpl_bar_program', 'urls' => array('/about_nbpl_bar_program'));
$MENU['Bar'][1][] = array('title' => 'Bar Program Packages', 'link' => MAIN_SERVER . '/bar_program_packages', 'urls' => array('/bar_program_packages'));
$MENU['Bar'][1][] = array('title' => 'Apply to be an NBPL League Bar', 'link' => MAIN_SERVER . '/apply_bar', 'urls' => array('/apply_bar'));
$MENU['Bar'][2][] = array('title' => 'Bar Program FAQs', 'link' => MAIN_SERVER . '/bar_program_faq', 'urls' => array('/bar_program_faq'));
$MENU['Bar'][2][] = array('title' => 'Contact NBPL Bar Support', 'link' => MAIN_SERVER . '/contact_bar_support', 'urls' => array('/contact_bar_support'));
$MENU['Bar'][2][] = array('title' => 'Recommend a Bar', 'link' => MAIN_SERVER . '/recommend_bar', 'urls' => array('/recommend_bar'));

// Stats
$MENU['Stats']['config'] = array('parent_title' => 'Stats', 'parent_link' => MAIN_SERVER . '/players_stats', 'left_menu' => true, 'breadcrumbs' => true, 'main_col' => true, 'top_info_block_element' => 'nbpl_stats_search');
$MENU['Stats'][1][] = array('title' => 'Players', 'link' => MAIN_SERVER . '/players_stats', 'urls' => array('/rankings/players_stats'));
$MENU['Stats'][1][] = array('title' => 'Teams', 'link' => MAIN_SERVER . '/teams_stats', 'urls' => array('/rankings/teams_stats'));
$MENU['Stats'][1][] = array('title' => 'School Affils', 'link' => MAIN_SERVER . '/schools_stats', 'urls' => array('/rankings/schools_stats'));
$MENU['Stats'][2][] = array('title' => 'Greek Affils', 'link' => MAIN_SERVER . '/greeks_stats', 'urls' => array('/rankings/greeks_stats'));
//$MENU['Stats'][2][] = array('title' => 'Organizations', 'link' => MAIN_SERVER . '/organizations_stats', 'urls' => array('/rankings/organizations_stats'));
$MENU['Stats'][2][] = array('title' => 'Cities', 'link' => MAIN_SERVER . '/cities_stats', 'urls' => array('/rankings/cities_stats'));
//$MENU['Stats'][2][] = array('title' => 'States', 'link' => MAIN_SERVER . '/states_stats', 'urls' => array('/rankings/states_stats'));
//$MENU['Stats'][2][] = array('title' => 'Countries', 'link' => MAIN_SERVER . '/countries_stats', 'urls' => array('/rankings/countries_stats'));
$MENU['Stats'][3][] = array('title' => 'Enter your stats with the <br/> Official NBPL Mobile App', 'link' => MAIN_SERVER . '/get_the_app', 'urls' => array());

if (!empty($affil) && !empty($title)) {
	$MENU['Affils'][1][] = array('title' => $title, 'link' => '', 'parent_title' => 'Greek Affils', 'parent_link' => MAIN_SERVER . '/greeks_stats', 'left_menu' => false, 'breadcrumbs' => true, 'urls' => array('/greeks/show'));
	$MENU['Affils'][1][] = array('title' => $title, 'link' => '', 'parent_title' => 'School Affils', 'parent_link' => MAIN_SERVER . '/schools_stats', 'left_menu' => false, 'breadcrumbs' => true, 'urls' => array('/schools/show'));
	$MENU['Affils'][1][] = array('title' => $title, 'link' => '', 'parent_title' => 'Cities Affils', 'parent_link' => MAIN_SERVER . '/cities_stats', 'left_menu' => false, 'breadcrumbs' => true, 'urls' => array('/cities/show'));
	
}

// News
$MENU['News']['config'] = array('parent_title' => 'News', 'parent_link' => false, 'left_menu' => false, 'breadcrumbs' => false, 'main_col' => true);
$MENU['News'][1][] = array('title' => 'Blog', 'link' => MAIN_SERVER . '/nation/beer-pong-blog', 'urls' => array('/blogposts/index'));
$MENU['News'][1][] = array('title' => 'Forums', 'link' => MAIN_SERVER . '/nation/beer-pong-forum', 'urls' => array('/forumbranches', '/forumtopics', '/forumposts'));

// App
$MENU['App']['config'] = array('parent_title' => 'News', 'parent_link' => false, 'left_menu' => false, 'breadcrumbs' => false, 'main_col' => false, 'top_info_block' => false);
$MENU['App'][1][] = array('title' => 'App', 'link' => MAIN_SERVER . '/get_the_app', 'urls' => array('/get_the_app'));


// Store
$MENU['Store']['config'] = array('column_name_1' => 'Official NBPL gear to tighten up your game');
$MENU['Store'][1][] = array('title' => 'All products', 'link' => BPONG_URL . '/store');
$MENU['Store'][1][] = array('title' => 'Balls & Cups', 'link' => BPONG_URL . '/store/category/beer-pong-balls-cups');
$MENU['Store'][1][] = array('title' => 'Clothing', 'link' => BPONG_URL . '/store/category/clothing');
$MENU['Store'][1][] = array('title' => 'Accessories', 'link' => BPONG_URL . '/store/category/accessories');
$MENU['Store'][2][] = array('title' => 'Tables', 'link' => BPONG_URL . '/store/category/beer-pong-tables');
$MENU['Store'][2][] = array('title' => 'Hats', 'link' => BPONG_URL . '/store/category/hats');
$MENU['Store'][2][] = array('title' => 'Posters', 'link' => BPONG_URL . '/store/category/posters');
$MENU['Store'][2][] = array('title' => 'Clearance items', 'link' => BPONG_URL . '/store/category/clearance-items');

$MENU['Contact'][1][] = array('title' => 'Contact', 'link' => MAIN_SERVER . '/contact', 'urls' => array('/contact'));


$MENU['OthersPages'][1][] = array('title' => 'Slider', 'parent_title' => 'Slides', 'parent_link' => MAIN_SERVER . '/slides', 'left_menu' => false, 'breadcrumbs' => true, 'main_col' => true, 'top_info_block_element' => 'slider_edit_image', 'urls' => array('/slides/edit'));


// ORGANIZATIONS
if (isset($organizationsMenu)) :
	$orgLink = '<a href="/organizations" style="text-decoration:none;"><span style="color:#989898;text-decoration:none !important;">Organizations &rsaquo; </span></a>';
	$MENU['Organizations']['config'] = array('left_menu' => true, 'main_col' => true, 'top_info_block_element' => 'nbpl_organizations_header');
	//$MENU['Organizations'][1][] = array('title' => 'All Organizations', 'link' => '/organizations', 'urls' => array());
	$MENU['Organizations'][1][] = array('title' => 'Home', 'link' => '/o/' . $organization['Organization']['slug'], 'urls' => array('/organizations/show'));
	$MENU['Organizations'][1][] = array('title' => 'News', 'link' => '/o_news/' . $organization['Organization']['slug'], 'urls' => array('/organization_news/org_list', '/organization_news/add', '/organization_news/edit'));
	$MENU['Organizations'][1][] = array('title' => 'Members', 'link' => '/o_members/' . $organization['Organization']['slug'], 'urls' => array('/organizations_users/org_list', '/organizations_users/add', '/organizations_users/edit', '/organizations_users/manage'));
	$MENU['Organizations'][1][] = array('title' => 'Events', 'link' => '/o_events/' . $organization['Organization']['slug'], 'urls' => array('/organizations_objects/list_events', '/organizations_objects/add_event'));
	$MENU['Organizations'][1][] = array('title' => 'Venues', 'link' => '/o_venues/' . $organization['Organization']['slug'], 'urls' => array('/organizations_objects/list_venues', '/organizations_objects/add_venue'));
	$MENU['Organizations'][1][] = array('title' => 'Albums', 'link' => '/o_albums/' . $organization['Organization']['slug'], 'urls' => array('/organizations/albums'));
	
	if (!empty($organization['Organization']['about'])) {
		$MENU['Organizations'][1][] = array('title' => 'About', 'link' => '/o_about/' . $organization['Organization']['slug'], 'urls' => array('/organizations/about'));
	}
	if (empty($orgUser['id']) || $orgUser['status'] == 'invited') :
		$MENU['Organizations'][1][] = array('title' => 'Join', 'link' => '/o_join/' . $organization['Organization']['slug'], 'urls' => array('/organizations_users/joinUser'));	
	endif;	
	
	
	
	
endif;
// ORGANIZATIONS



// EOF NBPLS
?>

<?php echo $this->element('nbpl_mainmenu_code', array('MENU' => $MENU, 'topMenus' => $topMenus, 'content_for_layout' => $content_for_layout));?>