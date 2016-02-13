<?php
echo $this->Html->script('superfish.js');
echo $this->Html->css('superfish.css');
?>

<?php
$MENU['Home'][] = array('name' => '',
    'text' => '',
    'link' => '',
    'links' => array('/index'));

$MENU['Store'][] = array('name' => 'All',
    'text' => 'The BPONG Store',
    'link' => MAIN_SERVER . '/store',
    'links' => array('/StoreCategories/front_index', '/StoreCategories/front_show', '/store/terms', '/StoreSlots/front_show', '/Carts/step1', '/Carts/manage', '/Carts/login', '/StoreOrders/search', '/Checkouts/thankyou'));

if (isset($menu_categories) && count($menu_categories)) {
      foreach ($menu_categories as $menu_category) {

	$url = '/store/category/' . $menu_category['StoreCategory']['slug'];

	$MENU['Store'][] = array('name' => $menu_category['StoreCategory']['name'],
	    'text' => $menu_category['StoreCategory']['name'],
	    'link' => $url,
	    'urls' => array($url),
	    'links' => array('/StoreCategories/front_show'));
    }
}

/* $MENU['Store'][]      = array('name'=>'All',
  'text'=>'The BPONG Store',
  'link'=>'',
  'url' =>'/store/category/',
  'links'=>array());
 */
$MENU['Wsobp'][] = array('name' => 'Main',
    'text' => 'The World Series of Beer Pong',
    'link' => '',
    'links' => array('/wsobp'));

$MENU['Wsobp'][] = array('name' => 'Home',
    'text' => 'The World Series of Beer Pong',
    'link' => MAIN_SERVER . '/wsobp',
    'links' => array('/wsobp'));
$MENU['Wsobp'][] = array('name' => 'Sign Up',
    'text' => 'Sign Up',
    'link' => 'http://wsobp.eventbrite.com',
    'links' => array('http://wsobp.eventbrite.com'));

$MENU['Wsobp'][] = array('name' => 'Official Rules',
    'text' => 'Official Rules',
	'facebook_like' => 1,
    'link' => MAIN_SERVER . '/wsobp/official-rules-of-the-world-series-of-beer-pong',
    'links' => array('/wsobp/official-rules-of-the-world-series-of-beer-pong'));

$MENU['Wsobp'][] = array('name' => 'Satellite Tournaments',
    'text' => 'Satellite Tournaments',
    'link' => MAIN_SERVER . '/wsobp/world-series-of-beer-pong-satellite-tournaments',
    'links' => array('/Satellites/'));
if ($this->Session->check("Tournament"))
    $_tournament = $this->Session->read("Tournament");

if (isset($_tournament['shortname']) && !empty($_tournament['shortname'])) {
    $MENU['Wsobp'][] = array('name' => 'WSOBP Teams',
	'text' => 'WSOBP Teams',
	'link' => MAIN_SERVER . '/wsobp/beer-pong-teams',
	'links' => array('/Teams/wsobp'));
} else {
    $MENU['Wsobp'][] = array('name' => 'WSOBP Teams',
	'text' => 'WSOBP Teams',
	'link' => MAIN_SERVER . '/wsobp/beer-pong-teams',
	'links' => array('/Teams/wsobp'));
}

$MENU['Wsobp'][] = array('name' => 'Stats',
    'text' => 'Stats',
    'link' => MAIN_SERVER . '/wsobp/results',
    'links' => array('/wsobp/results'));
/*
  $MENU['Wsobp'][]      = array('name'=>'Past WSOBPs',
  'text'=>'The World Series of Beer Pong TM',
  'link'=> Router::url(array('controller' => 'pages', 'action' => 'past_wsobps')),
  'links'=>array('Users/profile'));
 */

// ORGANIZATION menu

if (isset($organizationsMenu)) :
	$orgLink = '<a href="/organizations" style="text-decoration:none;"><span style="color:#989898;text-decoration:none !important;">Organizations &rsaquo; </span></a>';
	$MENU['Organizations'][] = array('name' => 'Home',
	    'text' => $orgLink . $organization['Organization']['name'],
	    'link' => '/o/' . $organization['Organization']['slug'],
	    'links' => array('/Organizations/show'));

	if ((!empty($AdminMenu) && $AdminMenu ) || (!empty($orgUser['id']) && $orgUser['status'] == 'accepted' && ($orgUser['role'] == 'creator' || $orgUser['role'] == 'manager'))) :
		$MENU['Organizations'][] = array('name' => 'Edit',
		    'text' => $orgLink . $organization['Organization']['name'],
		    'link' => '/organizations/edit/' . $organization['Organization']['id'],
		    'links' => array('/Organizations/edit'));

	endif;

	if ((!empty($AdminMenu) && $AdminMenu )  || (!empty($orgUser['id']) && $orgUser['status'] == 'accepted' && ($orgUser['role'] == 'creator'))) :
		$MENU['Organizations'][] = array('name' => 'Delete',
		    'text' => $orgLink . $organization['Organization']['name'],
		    'link' => '/organizations/delete/' . $organization['Organization']['id'],
			'param' => 'onclick="return confirm(' . "'Are you sure you want to delete organization?'" . ');"',
		    'links' => array('/Organizations/delete'));

	endif;

	$MENU['Organizations'][] = array('name' => 'News',
	    'text' => $orgLink . $organization['Organization']['name'],
	    'link' => '/o_news/' . $organization['Organization']['slug'],
	    'links' => array('/OrganizationNews/org_list', '/OrganizationNews/add', '/OrganizationNews/edit'));

	$MENU['Organizations'][] = array('name' => 'Members',
	    'text' => $orgLink . $organization['Organization']['name'],
	    'link' => '/o_members/' . $organization['Organization']['slug'],
	    'links' => array('/OrganizationsUsers/org_list', '/OrganizationsUsers/add', '/OrganizationsUsers/edit', '/OrganizationsUsers/manage'));

	$MENU['Organizations'][] = array('name' => 'Events',
	    'text' => $organization['Organization']['name'],
	    'link' => '/o_events/' . $organization['Organization']['slug'],
	    'links' => array('/OrganizationsObjects/list_events', '/OrganizationsObjects/add_event'));

	$MENU['Organizations'][] = array('name' => 'Venues',
	    'text' => $orgLink . $organization['Organization']['name'],
	    'link' => '/o_venues/' . $organization['Organization']['slug'],
	    'links' => array('/OrganizationsObjects/list_venues', '/OrganizationsObjects/add_venue'));

	$MENU['Organizations'][] = array('name' => 'Albums',
	    'text' => $orgLink . $organization['Organization']['name'],
	    'link' => '/o_albums/' . $organization['Organization']['slug'],
	    'links' => array('/Organizations/albums'));


	if (!empty($organization['Organization']['about'])) {
		$MENU['Organizations'][] = array('name' => 'About',
		    'text' => $orgLink . $organization['Organization']['name'],
		    'link' => '/o_about/' . $organization['Organization']['slug'],
		    'links' => array('/Organizations/about'));
	}
	if (empty($orgUser['id']) || $orgUser['status'] == 'invited') :
	$MENU['Organizations'][] = array('name' => 'Join',
	    'text' => $orgLink . $organization['Organization']['name'],
	    'link' => '/o_join/' . $organization['Organization']['slug'],
	    'links' => array('/OrganizationsUsers/joinUser'));
	endif;

endif;

// EOF ORGANIZATION menu

$MENU['Nation'][] = array('name' => 'Main',
    'text' => 'BPONG Nation',
    'link' => '',
    'links' => array('/nation', '/team-info'));
$MENU['Nation'][] = array('name' => 'Home',
    'text' => 'Home',
    'link' => MAIN_SERVER . '/nation',
    'links' => array('/nation'));
$MENU['Nation'][] = array('name' => 'Blog',
    'text' => 'Blog',
    'link' => MAIN_SERVER . '/nation/beer-pong-blog',
    'links' => array('/Blogposts'));
$MENU['Nation'][] = array('name' => 'Forums',
    'text' => 'Forums',
    'link' => MAIN_SERVER . '/nation/beer-pong-forum',
    'links' => array('/Forumbranches', '/Forumtopics', '/Forumposts'));
/*
$MENU['Nation'][] = array('name' => 'Online Games',
    'text' => 'Online Games',
    'link' => '/nation/beer-pong-game',
    'links' => array('/Games'));
*/
$MENU['Nation'][] = array('name' => 'Custom Table Gallery',
    'text' => 'Custom Table Gallery',
    'link' => MAIN_SERVER . '/nation/beer-pong-custom-tables',
    'links' => array('/Pongtables'));

$MENU['Nation'][] = array('name' => 'Teams',
    'text' => 'Teams',
    'link' => MAIN_SERVER . '/nation/beer-pong-teams',
    'links' => array('/Teams/allTeams'));
$MENU['Nation'][] = array('name' => 'Events',
    'text' => 'Events',
    'link' => MAIN_SERVER . '/events',
	'facebook_like' => 1,
    'links' => array('/Events/index', '/events'));

$MENU['Nation'][] = array('name' => 'Users',
    'text' => 'Users',
    'link' => MAIN_SERVER . '/Users/show_all',
    'links' => array('/Users/show_all'));

$MENU['Nation'][] = array('name' => 'Organizations',
    'text' => 'Organizations',
    'link' => MAIN_SERVER . '/organizations',
    'links' => array('/Organizations/index'));
$MENU['Nation'][] = array('name'=>'Rankings',
	'text'=>'Rankings',
	'link'=> MAIN_SERVER . '/rankings',
	'links'=>array('/rankings'));

/* $MENU['Nation'][]      = array('name'=>'Local Tournaments',
  'text'=>'Local Tournaments',
  'link'=>'/tournaments/searchTournaments',
  'links'=>array('/Tournaments/','/Signups'));

  $MENU['Nation'][]      = array('name'=>'BPONG Tour',
  'text'=>'BPONG Tour',
  'link'=>MAIN_SERVER.'/tour',
  'links'=>array('/tour'));
 */


$MENU['Vault'][] = array('name' => 'Home',
    'text' => 'The Vault',
    'link' => MAIN_SERVER . '/vault',
    'links' => array('/vault'));
$MENU['Vault'][] = array('name' => 'Rules',
    'text' => 'Rules',
    'link' => MAIN_SERVER . '/vault/general-beer-pong-rules',
    'links' => array('/vault/general-beer-pong-rules'),
	'facebook_like' => 1
);
$MENU['Vault'][] = array('name' => 'FAQ',
    'text' => 'FAQ',
    'link' => MAIN_SERVER . '/vault/beer-pong-faq',
    'links' => array('/vault/beer-pong-faq', '/faq'));
$MENU['Vault'][] = array('name' => 'Last Cup',
    'text' => 'Last Cup',
    'link' => MAIN_SERVER . '/vault/last-cup-road-to-the-world-series-of-beer-pong',
    'links' => array('/vault/last-cup-road-to-the-world-series-of-beer-pong'));

/*
  $MENU['Vault'][]      = array('name'=>'Tournament Brackets',
  'text'=>'Tournament Brackets',
  'link'=>MAIN_SERVER.'/vault#tournament_brackets',
  'param'=>'',
  'links'=>array());
  $MENU['Vault'][]      = array('name'=>'Desktop Wallpaper',
  'text'=>'Desktop Wallpaper',
  'link'=>MAIN_SERVER.'/vault#desktop_wallpaper',
  'param'=>'',
  'links'=>array());
 */
$MENU['Resellers'][] = array('name' => 'resellers',
    'text' => 'Resellers Program',
    'link' => '',
    'links' => array('/StoreResellers/createReseller'));


$mainMenu  = 'Home';
$subMenu   = 0;
$url=$this->request->here;
$link = "";
if ($this->name=="Pages") {
	$link = $this->request->here;
} else {
	$link = '/'.$this->name.'/'.$this->request->action;
}
	if ($this!=='/'){
		foreach ($MENU as $mainKey=>$mainValue){
				 foreach ($mainValue as $subKey=>$subValue){

				 	if(!empty($subValue['links'])&& empty($subValue['urls'])){
				 		foreach($subValue['links'] as $links){
				 			if(preg_match('/^'.preg_quote($links, '/').'/', $link)){
				 				$mainMenu = $mainKey;
				 				$subMenu  = $subKey;

				 				break;
				 			}
				 		}
				 	}elseif(!empty($subValue['urls'])){
				 		foreach($subValue['urls'] as $urls){
				 			if(preg_match('/^'.preg_quote($urls, '/').'/', $url)){
				 				$mainMenu = $mainKey;
				 				$subMenu  = $subKey;
				 				break;
				 			}
				 		}
				 	}
				 }
		}//foreach

	}

?>
<?php if (!empty($MENU[$mainMenu][$subMenu]['facebook_like'])): ?>
<div style='position: relative;float:right;margin-bottom:-100px;z-index:100;bottom:-53px;margin-right:130px;'><?php echo $this->element('facebook_like');?></div>
<?php endif;?>



<?php if(isset($this_store)): /* Only For  Store Checkout */ ?>
      <ul id="nav" class="sf-menu" style="background:none">
        	<li class="ie_pad" style="background-image:none">
            	<img src="<?php echo STATIC_BPONG?>/img/secure.png"/>
            </li>
        </ul>
<?php else:   /*All pages expecting Store Checkout */ ?>
      <ul id="nav" class="sf-menu">
        	<li id="a">
            	<a href="<?php echo MAIN_SERVER;?>"><img src="<?php echo STATIC_BPONG?>/img/home_off.png<?php  /*echo $mainMenu=="Home"?"home_on.png":"home.png";*/  ?>" alt="HOME" title="To the home page" /></a>
            </li>
       <?php if ($this->request->here !== "/wsobp/spencers"):?>
            <li id="b">
            	<a href="<?php echo MAIN_SERVER;?>/store"><img src="<?php echo STATIC_BPONG?>/img/<?php echo $mainMenu=="Store"?"store_on.png":"store_off.png"; ?>" alt="STORE" title="Store" /></a>
		<ul class="submenu">
		   <?php foreach ($MENU['Store'] as $key=>$value): ?>
			<?php if (!empty ($value['link'])): ?>
    			    <?php
				if ($value['name'] == 'Beer Pong Tables') {
				    $value['name'] = 'Tables';
				}
				if ($value['name'] == 'Beer Pong Balls & Cups') {
				    $value['name'] = 'Balls & Cups';
				}
			    ?>
			    <li <?php echo !empty($subMenu==$key) ? " class='on' " : " " ?> >
				<?php echo $this->Html->link($value['name'], $value['link']) ?>
			    </li>
			<?php endif; ?>
		    <?php endforeach; ?>
                </ul>
            </li>
       <?php endif;?>
            <li id="c">
            	<a href="<?php echo MAIN_SERVER;?>/wsobp"><img src="<?php echo STATIC_BPONG?>/img/<?php echo $mainMenu=="Wsobp"?"wsobp_on.png":"wsobp_off.png"; ?>" alt="THE WSOBP" title="World Series of Beer Pong" /></a>
		<ul class="submenu">
		   <?php foreach ($MENU['Wsobp'] as $key=>$value): ?>
			<?php if (!empty ($value['link'])): ?>
			    <?php if ($value['name'] == 'Satellite Tournaments') : ?>
				<?php $value['name'] = "Satellite&trade; Tournaments"; ?>
				<li <?php echo !empty($subMenu==$key) ? " class='on' " : " " ?> >
				    <a href="<?php echo $value['link'] ?>"><?php echo $value['name'] ?></a>
				</li>
			    <?php else : ?>
				<li <?php echo !empty($subMenu==$key) ? " class='on' " : " " ?> >
				    <?php echo $this->Html->link($value['name'], $value['link'], false, false) ?>
				</li>
			    <?php endif; ?>
			<?php endif; ?>
		    <?php endforeach; ?>
                </ul>
            </li>
            <li id="d">
            	<a href="<?php echo MAIN_SERVER;?>/nation"><img src="<?php echo STATIC_BPONG?>/img/<?php echo $mainMenu=="Nation"?"community_on.png":"community_off.png"; ?>" alt="NATION" title="Nation" /></a>
		<ul class="submenu">
		   <?php foreach ($MENU['Nation'] as $key=>$value): ?>
			<?php if (!empty ($value['link'])): ?>
			    <li <?php echo !empty($subMenu==$key) ? " class='on' " : " " ?> >
				<?php echo $this->Html->link($value['name'], $value['link']) ?>
			    </li>
			<?php endif; ?>
		    <?php endforeach; ?>
                </ul>
            </li>
            <li id="e">
            	<a href="<?php echo MAIN_SERVER;?>/vault"><img src="<?php echo STATIC_BPONG?>/img/<?php echo $mainMenu=="Vault"?"resources_on.png":"resources_off.png"; ?>" alt="Vault" title="Vault" /></a>
		<ul class="submenu">
		   <?php foreach ($MENU['Vault'] as $key=>$value): ?>
			<?php if (!empty ($value['link'])): ?>
			    <li <?php echo !empty($subMenu==$key) ? " class='on' " : " " ?> >
				<?php echo $this->Html->link($value['name'], $value['link']) ?>
			    </li>
			<?php endif; ?>
		    <?php endforeach; ?>
                </ul>
            </li>
            <li class="searchbox">
            <!-- SiteSearch Google -->
				<form action="<?php echo MAIN_SERVER;?>/searchings" id="cse-search-box">
				  <div>
				    <input type="hidden" name="cx" value="000811573964062089656:cectdwhd4i4" />
				    <input type="hidden" name="cof" value="FORID:11" />
				    <input type="hidden" name="ie" value="UTF-8" />
				    <input type="text" name="q" size="20" id="GoogleSearch" value="Search BPONG.COM"/>
				    <input type="submit" name="sa" value="Search" class="sbmt_ie" style="width:55px; background:url('<?php echo STATIC_BPONG?>/img/btn_bg.png') repeat-x; height:auto; padding:2px; text-transform:uppercase; cursor:pointer; margin-top:0px !important;" />
				  </div>
				</form>

            	<!-- SiteSearch Google -->
            </li>
        </ul>
        <!--  Submenu -->

         <?php if (!empty($MENU[$mainMenu][$subMenu]['name'])): ?>
            <?php if($mainMenu=='Store' && !isset($slot_id)):?><img src="<?php echo STATIC_BPONG?>/img/store_top.gif" alt="Bpong.com Store" style='border-bottom: 3px solid #D61C20;border-top: 3px solid #D61C20;'><?php endif;?>
            <div class="relcart">

            <div class="cart_main">
			<?php if($mainMenu=='Store'){ echo $this->element('cart'); } ?></div>
            <h1 class="withline"><?php echo $MENU[$mainMenu][$subMenu]['text']?></h1>

			<?php if (empty($_SERVER['HTTPS'])) :?>
            <div class="sharethis"><?php echo $this->element('share_this');?></div>
            <?php endif;?>
</div>

            <ul class="subnav">
             <?php
	     $Menucnt = count($MENU[$mainMenu] );
                          $i = 0;
             ?>
	           <?php foreach ($MENU[$mainMenu] as $key=>$value): ?>
	           		<?php $i++; ?>
	           		 <?php if (!empty ($value['link'])): ?>
	           		 				<?php if ( $i ==  $Menucnt ): ?>
	            						<li <?php echo $subMenu==$key?" class='on last' ":" class='last' " ?> ><a <?php echo !empty($value['param'])?" ".$value['param']." ":"" ?> href="<?php echo $value['link'] ?>"><?php echo $value['name'] ?></a></li>
	            					<?php else: ?>
	            					    <li <?php echo $subMenu==$key?" class='on' ":" " ?> ><a <?php echo !empty($value['param'])?" ".$value['param']." ":"" ?> href="<?php echo $value['link'] ?>"><?php echo $value['name'] ?></a></li>
	            					<?php endif; ?>
	            	<?php endif; ?>
	            <?endforeach; ?>
            </ul>
            <?php endif; ?>
<?php endif; ?>
        <!--  EOF SUBMENU -->
        <!-- *********** EOF nav *********** -->
<script type="text/javascript">
var activeMenuItem;
$(document).ready(function() {
    $('ul#nav > li').each(function() {
       $(this).hover(
          function() {
	      $(this).stop(true, true);
	      var image = $('a > img', this);
              src = image.attr('src');
              if(src.search(/_on./)==-1) {
                 image.attr('src', src.replace(/_off/,'_on_'));
              } else {
                 activeMenuItem = image.attr('src');
              }
          },
          function() {
	      var image = $('a > img', this);
              if(image.attr('src') != activeMenuItem) {
                src = image.attr('src');
		//image.animate({}, 800, function() {
		    image.attr('src', src.replace(/_on_/,'_off'));
		//});
              }
          }
       )
    });

    $('ul#nav').superfish();
});
</script>
<div style="display:none;">
<img src="<?php echo STATIC_BPONG?>/img/home_on_.png" title="To the home page" /><img src="<?php echo STATIC_BPONG?>/img/store_on_.png" title="Store"  /><img src="<?php echo STATIC_BPONG?>/img/wsobp_on_.png" title="World Series of Beer Pong"   /><img src="<?php echo STATIC_BPONG?>/img/community_on_.png" title="Nation"   /><img src="<?php echo STATIC_BPONG?>/img/resources_on_.png" title="Vault"   />
</div>
<?php
	Configure::write('Main.Menu', $MENU);
?>
