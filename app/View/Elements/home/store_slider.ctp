<?php 
	$slider = array();
	$slider[] = array('image' => IMG_SLIDES_URL . '/shop.jpg', 'url' => 'http://www.bpong.com/store/'); 
?>

<script type="text/javascript">
$(document).ready(function(){
	createSlider(true, 7000, 'slider3', 'navigate3');
});		
</script>
<?php $slidesCount = 2;?>
<div class="col b30">
	<h3  class='thin_h'>Great deals on BPONG Gear</h3>
	<div id='slider3'>
		<?php 
		$i = 0;
		foreach ($slider as $slide):
		$i++;
		?>
		<div class='slider_cont' <?php if ($i!=1):?>style='display:none;'<?php endif;?>><?php if (!empty($slide['url'])):?><a href="<?php echo $slide['url'];?>"><?php endif;?><img src="<?php echo $slide['image'];?>" class="img" alt="" /><?php if (!empty($slide['url'])):?></a><?php endif;?></div>
		<?php endforeach;?>
	</div>
<?php if (count($slider) > 1):?>	
<div class="slider" id='navigate3'>
  <ul>
  <li  class="on"></li>
    <?php for($i = 1; $i < count($slider) ;$i++) : ?>
	<li></li>
    <?php endfor; ?>
  </ul> 
</div>	
<?php endif;?>
	
	<!-- EOF slider -->
</div>
<!-- EOF b30 -->