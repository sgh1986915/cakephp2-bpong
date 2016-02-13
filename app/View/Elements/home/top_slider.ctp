<?php echo $this->Html->script(array(STATIC_BPONG.'/js/bpong_slider.js'));?> 
<script type="text/javascript">
$(document).ready(function(){
	createSlider(true, 8000, 'slider1', 'navigate1');

	$('.bggt a').click(function(){ nte=1; });
	$('.bggt').click(function(){ if (nte==0) { return false; } });
	$('.pics').click(function(){
		var lnk = $(this).find('a').eq(0).attr('href');
		nte=0;
		window.location.assign(lnk);
		return false;
	});
});		
</script>
<?php /*)?>
<div class="home_welcome" style='position:absolute;z-index:3;top:132px;left:40px;'>
				<h2  class='thin_h'>Welcome to beerpong.com, <br /> <div class="small"><span>Official</span> home of The <span>N</span>ational <span>B</span>eer <span>P</span>ong <span>L</span>eague!</div></h2>
</div>
<?php */ ?>
<div id ="slider1">
<?php 
$i = 0;
$slidesCount = 0;
foreach($slides as $slide) : 
$i++;
?>
    <?php
	$cleanTitle = trim($slide['Slide']['title']);
	$cleanDescription = trim(strip_tags($slide['Slide']['description']));
	if(empty($cleanTitle) && empty($cleanDescription)) {
	    $class = 'bggt notxt';
	} else {
	    $class = 'bggt';
	}
    ?>
    <?php if (!empty($slide['Image']['0']['filename']) && (empty($LoggedMenu) || $i>1) && (!empty($LoggedMenu) || $i!=5)):
    $slidesCount++;
    
    ?>
	<div class="intro slider_cont" style='background: url("<?php echo IMG_MODELS_URL . '/' . $slide['Image']['0']['filename']; ?>") repeat scroll 0 0 transparent;<?php if ($slidesCount!=1):?>display:none;<?php endif;?>'>
				<?php if ($slide['Slide']['url']):?><a style='display:block;width:958px; height:340px;z-index:1;' href="<?php echo $slide['Slide']['url'];?>"></a><?php endif;?>
				<!-- EOF slider -->
				<?php if (!empty($slide['Slide']['description'])):?>
				<div class="introtext">
					<p><?php echo $slide['Slide']['description']; ?></p>
					<?php 
						switch ($i) {
					    case 1:
					        if (empty($LoggedMenu)): ?><a href="/registration"><img src="<?php echo IMG_NBPL_LAYOUTS_URL;?>/signup_btn.png" alt="" border="0"></a><?php endif;
					        break;
					    case 2:
					        ?><a href="/wsobp"><img src="<?php echo IMG_NBPL_LAYOUTS_URL;?>/learn_wsobp_button.png" alt="" border="0"></a><?php
					        break;
					    case 3:
					        ?><a href="<?php echo $slide['Slide']['url'];?>" target="_blank"><img src="<?php echo IMG_NBPL_LAYOUTS_URL;?>/download_android_button.png" alt="" border="0"></a><?php
					        break;
					    case 4:
					        ?><a href="<?php echo $slide['Slide']['url'];?>"><img src="<?php echo IMG_NBPL_LAYOUTS_URL;?>/shop__now_button.png" alt="" border="0"></a><?php
					        break;
					    case 5:
					        if (!empty($LoggedMenu)):?><a href="/u/<?php echo $userSession['lgn'];?>"><img src="<?php echo IMG_NBPL_LAYOUTS_URL;?>/set_affils_button.png" alt="" border="0"></a><?php  endif;
					        break;					        					        
						}
					
					?>
					<div class="clear"></div>
				</div>
				<!-- EOF introtext -->
				<?php endif;?>
	</div>
	<?php endif;?>
<?php endforeach;?>
</div>
<!-- EOF welcome -->
<?php if ($slidesCount > 1):?>	
<div class="slider" id='navigate1' style='position:absolute;z-index:100;top:320px;left:20px;'>
  <ul>
  <li  class="on"></li>
    <?php for($i = 1; $i < $slidesCount ;$i++) : ?>
	<li></li>	
    <?php endfor; ?>
  </ul> 
</div>
<?php endif;?>