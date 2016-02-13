<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php if (!HTTPS_CONNECT):?>
	<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script><script type="text/javascript">stLight.options({publisher:'614b0a90-1f48-4016-81ea-3ea6c36b1925'});</script>
<?php endif;?>
<?php echo $this->Html->css(array(STATIC_BPONG.'/css/thickbox.css', 
								  STATIC_BPONG.'/css/layout_live.css',
                                  STATIC_BPONG.'/css/cake.generic.css',
                                  CSS_NBPL . '/autocomplete.css',
                                  CSS_NBPL . '/datePicker.css',
                                  CSS_NBPL . '/jquery.alerts.css'
                                  ));
      echo $this->Html->script(array(
      							  JS_NBPL . '/jquery-1.5.2.min.js',
                  				  JS_NBPL . '/thickbox.js',
                  				  JS_NBPL . '/jquery.cookie.js',
                  				  JS_NBPL . '/jquery.validate.min.js',
                  				  JS_NBPL . '/jquery.form.js',
                  				  JS_NBPL . '/jquery.autocomplete.pack.js',
                  				  JS_NBPL . '/jquery.alerts.js',
                  				  JS_NBPL . '/stickyfloat.min.js',
                  				  STATIC_BPONG.'/js/highcharts/highcharts.js',
                  				  STATIC_BPONG . '/js/bpong.js'                 				               				  
                  				  ));
?>
<style type="text/css" media="screen, projection">
	<!--
 		@import url("<?php echo STATIC_BPONG?>/css/old_layout_for_nbpl.css");		 
		@import url("<?php echo STATIC_BPONG?>/css/nbpl_layout.css");
	-->
</style>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="expires" content="-1" />
<?php if (isset($meta_description)): ?>
<meta name="description" content="<?php echo $meta_description; ?>" />
<?php else : ?>
<?php endif; ?>
<?php if (isset($rels_images)): ?>
<?php foreach($rels_images as $rels_image): ?>
<link rel="image_src" href="<?php echo $rels_image;?>" />
<meta property="og:image" content="<?php echo $rels_image;?>" />
<?php endforeach; ?>
<?php endif; ?>
<title><?php if (empty($this->pageTitle)) {echo $title_for_layout;} else { echo $this->pageTitle;} ?></title>

<?php echo $this->element('nbpl_default_javascripts');?>

<?php echo $scripts_for_layout; ?>
</head>
<body>
<div class="logoshadow"></div>
<div id="wrapper">
	<div id="nbpl_header">
		<h1 class="logo"><a href="<?php echo MAIN_SERVER;?>">The national Beer Pong League</a></h1>
		<div class="rtbox">
			<div class="topmenu"><?php echo $this->element('nbpl_login_block');?></div>
			<!-- EOF topmenu -->
			<div class="sicons">
				<span>Connect with us&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<ul>
					<li></li>
					<li><a href="http://www.facebook.com/BPONG" class="f"></a></li>
					<li><a href="http://twitter.com/BPONG" class="t"></a></li>
					<li><a href="http://www.myspace.com/wsobp" class="m"></a></li>
					<!--<li><a href="#" class="r"></a></li>-->
				</ul>
			</div>
			<!-- EOF sicons -->
			
			<div class="searchbox">
			
			
				<form action="<?php echo MAIN_SERVER;?>/searchings" id="cse-search-box">
				    <input type="hidden" name="cx" value="000811573964062089656:cectdwhd4i4" />
				    <input type="hidden" name="cof" value="FORID:11" />
				    <input type="hidden" name="ie" value="UTF-8" />
				    <input type="text" name="q" size="20" id="GoogleSearch" value="Search beerpong.com"/>
				    <input type="image" src="<?php echo IMG_NBPL_LAYOUTS_URL;?>/search_btn.gif" class="imagebtn" onclick="$('#cse-search-box').submit();"/>
				</form>
			</div>
			<!-- EOF searchbox -->
		</div>
		<!-- EOF rtbox -->
		<div class="clear"></div>
	<?php echo $this->element('nbpl_mainmenu_content', array('content_for_layout' => $content_for_layout));?>
	
 <div id="footer">
	<?php echo $this->element('nbpl_footer');?>	
 </div>

 </div>
 <!-- EOF wrapper -->
</body>
<?php if (Configure::read('Sandbox.environment') == 'dev'):?>
<script type="text/javascript">
         var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
         document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
         </script>
<script type="text/javascript">
         var pageTracker = _gat._getTracker("UA-601359-3");
         pageTracker._initData();
         pageTracker._trackPageview();
 		</script>
<?php endif;?>
</html>
<?php echo $this->element('sql_dump'); 
?>
