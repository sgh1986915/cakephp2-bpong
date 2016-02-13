<?php 
	$slider = array();
	$slider[] = array('big_image' => IMG_SLIDES_URL . '/ns1.jpg', 'little_image' => IMG_NBPL_LAYOUTS_URL . '/thumb1_s.jpg', 'text' => "Nick Syrigos (left) and Dan Range (right) showcase their $50,000 winner's check from WSOBP VI with Bruce Buffer"); 
	$slider[] = array('big_image' => IMG_SLIDES_URL . '/ns2.jpg', 'little_image' => IMG_NBPL_LAYOUTS_URL . '/thumb2_s.jpg', 'text' => "Over 507 teams from 9 different countries converged on the Las Vegas strip to compete in WSOBP VI"); 
	$slider[] = array('big_image' => IMG_SLIDES_URL . '/ns3.jpg', 'little_image' => IMG_NBPL_LAYOUTS_URL . '/thumb3_s.jpg', 'text' => "Lucky the Leprechaun takes a shot on stage at WSOBP VI"); 
	$slider[] = array('big_image' => IMG_SLIDES_URL . '/ns4.jpg', 'little_image' => IMG_NBPL_LAYOUTS_URL . '/thumb4_s.jpg', 'text' => "Bruce Buffer, veteran voice of MMA, heats up the crowd for the final $50,000 game at WSOBP VI."); 
	$slider[] = array('big_image' => IMG_SLIDES_URL . '/ns5.jpg', 'little_image' => IMG_NBPL_LAYOUTS_URL . '/thumb5_s.jpg', 'text' => "Team Standing Ovation (left), and Team Unstoppable Since Inception (right), face off in the final $50,000 game."); 
	$slider[] = array('big_image' => IMG_SLIDES_URL . '/ns6.jpg', 'little_image' => IMG_NBPL_LAYOUTS_URL . '/thumb6_s.jpg', 'text' => "Spectators from almost every US State pack the stands to watch the 3rd day elimination rounds."); 
?>
<script type="text/javascript">
$(document).ready(function(){
	createSlider(true, 7000, 'slider2', 'navigate2');
});		
</script>
<div class="row" style="background: none;float:left;position: relative;">
	<h3 class='thin_h'>Beer Pong in Pictures</h3>
	<div id='slider2'>
		<?php 
		$i = 0;
		foreach ($slider as $slide):
		$i++;
		?>
		<div class='slider_cont' <?php if ($i!=1):?>style='display:none;'<?php endif;?>>
			<?php if (!empty($slide['url'])):?><a href="<?php echo $slide['url'];?>"><?php endif;?><img src="<?php echo $slide['big_image'];?>" alt="" width="270px"/><?php if (!empty($slide['url'])):?></a><?php endif;?>
			<div class="gallerydescr"><?php echo $slide['text'];?></div>
		</div>	
		<?php endforeach;?>
		</div>					
<div id='navigate2'>
	<ul class="gallerylist">
		<?php 
		$i = 0;
		foreach ($slider as $slide):
		$i++;
		?>
		<li <?php if ($i==1):?>class="on"<?php endif;?>><img src="<?php echo $slide['little_image'];?>" alt="" /></li>
		<?php endforeach;?>		
	</ul>
</div>
		<div class="clear"></div>
		<a href="/images/">View all images</a>&nbsp;|&nbsp;<a href="/submit">Upoload an image</a>
</div>