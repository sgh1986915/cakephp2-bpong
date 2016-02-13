<?php echo $this->Html->script('jquery.nyroModal', false); ?>

<?php echo $this->Html->script('add_elements', false); ?>
<?php echo $this->Html->script('jquery.nivo.slider', false); ?>
<?php echo $this->Html->script('add_nivo', false); ?>
<?php echo $this->Html->css('nivo-slider', 'stylesheet', array('media'=>'all' ), false);?>
<?php echo $this->Html->css('nyroModal', 'stylesheet', array('media'=>'all' ), false);?>
<?php echo $this->Html->css('wsobp_new', 'stylesheet', array('media'=>'all' ), false);?>
<?php $this->pageTitle = "The World Series of Beer Pong (WSOBP) | Professional Beer Pong Tournaments for Big Money | BPONG.COM"; ?>


<div class="wsobp_top">
	<div id="wsobp_slider">
	    <img src="<?php echo IMG_WSOBP_7_URL; ?>/gal_top_img_0.jpg" alt="" />
	    <img src="<?php echo IMG_WSOBP_7_URL; ?>/gal_top_img_1.jpg" alt="" />
	    <img src="<?php echo IMG_WSOBP_7_URL; ?>/gal_top_img_2.jpg" alt="" />
	    <img src="<?php echo IMG_WSOBP_7_URL; ?>/gal_top_img_3.jpg" alt="" />
	    <img src="<?php echo IMG_WSOBP_7_URL; ?>/gal_top_img_4.jpg" alt="" />
	</div>
	<?php echo $this->element("wsobp_menu", array('page' => 'gallery'));?>
	<div class="clear"></div>
</div>


<ul id="gallery_tabs">
     <li><a href="#gal_6" class="gal_tab_selected" >WSOBP VI</a></li>
     <li><a href="#gal_5" class="gal_tab_disabled" >WSOBP V</a></li>
     <li><a href="#gal_4" class="gal_tab_disabled" >WSOBP IV</a></li>
     <li><a href="#gal_3" class="gal_tab_disabled" >WSOBP III</a></li>
     <li><a href="#gal_2" class="gal_tab_disabled" >WSOBP II</a></li>
     <li><a href="#gal_1" class="gal_tab_disabled" >WSOBP I</a></li>
</ul>

<div class="redline"></div>

<div class="galleries_wrapper">

		<!-- images block -->
	<div id="gal_1" class="wsobp_new_gallery gal_hidden">
		<ul class="wsobp_gallery_ul">
		<?php for($i=1; $i<=27; $i++): ?>
			<li class="wsobp_gallery_li">
				<a href="<?php echo IMG_WSOBP_1_URL;?>/gallery_image_<?php echo $i; ?>.jpg" class="nyroModal" rel="gal_1">
					<img src="<?php echo IMG_WSOBP_1_URL;?>/gallery_thumb_<?php echo $i; ?>.jpg" alt="" />
				</a>
			</li>
		<?php endfor;?>
		</ul>
		<div class="clear"></div>
	</div>
	<!-- /images block -->

	<!-- images block -->
	<div id="gal_2" class="wsobp_new_gallery gal_hidden">
		<ul class="wsobp_gallery_ul">
		<?php for($i=1; $i<=25; $i++): ?>
			<li class="wsobp_gallery_li">
				<a href="<?php echo IMG_WSOBP_2_URL;?>/gallery_image_<?php echo $i; ?>.jpg" class="nyroModal" rel="gal_2">
					<img src="<?php echo IMG_WSOBP_2_URL;?>/gallery_thumb_<?php echo $i; ?>.jpg" alt="" />
				</a>
			</li>
		<?php endfor;?>
		</ul>
		<div class="clear"></div>
	</div>
	<!-- /images block -->

	<!-- images block -->
	<div id="gal_3" class="wsobp_new_gallery gal_hidden">
		<ul class="wsobp_gallery_ul">
		<?php for($i=1; $i<=45; $i++): ?>
			<li class="wsobp_gallery_li">
				<a href="<?php echo IMG_WSOBP_3_URL;?>/gallery_image_<?php echo $i; ?>.jpg" class="nyroModal" rel="gal_3">
					<img src="<?php echo IMG_WSOBP_3_URL;?>/gallery_thumb_<?php echo $i; ?>.jpg" alt="" />
				</a>
			</li>
		<?php endfor;?>
		</ul>
		<div class="clear"></div>
	</div>
	<!-- /images block -->

	<!-- images block -->
	<div id="gal_4" class="wsobp_new_gallery gal_hidden">
		<ul class="wsobp_gallery_ul">
		<?php for($i=1; $i<=41; $i++): ?>
			<li class="wsobp_gallery_li">
				<a href="<?php echo IMG_WSOBP_4_URL;?>/gallery_image_<?php echo $i; ?>.jpg" class="nyroModal" rel="gal_4">
					<img src="<?php echo IMG_WSOBP_4_URL;?>/gallery_thumb_<?php echo $i; ?>.jpg" alt="" />
				</a>
			</li>
		<?php endfor;?>
		</ul>
		<div class="clear"></div>
	</div>
	<!-- /images block -->

	<!-- images block -->
	<div id="gal_5" class="wsobp_new_gallery gal_hidden">
		<ul class="wsobp_gallery_ul">
		<?php for($i=1; $i<=36; $i++): ?>
			<li class="wsobp_gallery_li">
				<a href="<?php echo IMG_WSOBP_5_URL;?>/gallery_image_<?php echo $i; ?>.jpg" class="nyroModal" rel="gal_5">
					<img src="<?php echo IMG_WSOBP_5_URL;?>/gallery_thumb_<?php echo $i; ?>.jpg" alt="" />
				</a>
			</li>
		<?php endfor;?>
		</ul>
		<div class="clear"></div>
	</div>
	<!-- /images block -->

	<!-- images block -->
	<div id="gal_6" class="wsobp_new_gallery gal_showing">
		<ul class="wsobp_gallery_ul">
		<?php for($i=1; $i<=43; $i++): ?>
			<li class="wsobp_gallery_li">
				<a href="<?php echo IMG_WSOBP_6_URL;?>/gallery_image_<?php echo $i; ?>.jpg" class="nyroModal" rel="gal_6">
					<img src="<?php echo IMG_WSOBP_6_URL;?>/gallery_thumb_<?php echo $i; ?>.jpg" alt="" />
				</a>
			</li>
		<?php endfor;?>
		</ul>
		<div class="clear"></div>
	</div>
	<!-- /images block -->

	<div class="clear"></div>
</div>

