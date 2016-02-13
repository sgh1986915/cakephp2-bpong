<?php if (!empty($images)):?>
<?php echo $this->Html->script('jquery-ui-1.8.13.custom.min.js'); ?>
<style>
	#sortable { list-style-type: none; margin: 0; padding: 0;clear:both;}
	#sortable li { margin: 3px; padding: 1px; float: left; width: 70px;background-color: #F1F1F1; height: 80px; font-size: 4em; text-align: center; border: 1px solid #CCCCCC;display:block;position:relative;}
</style>
<script>
$(function() {
	$("#sortable").sortable({
		update:  function(event, ui) {
			$.ajaxSetup({ cache: false });
			$.post("/images/ajaxChangeOrder/", {images_order:  $('#sortable').sortable('toArray') + " "},
					function(data){
					}
			);
		}
	});


});
</script>
<br/>
<div style='margin-bottom:20px;'>
	<ul id="sortable">
	<?php foreach ($images as $img):?>
		<li id='img_<?php echo $img['Image']['id'];?>'>
			<input name="data[<?php echo $modelName?>][main_image_id]" type="radio" value="<?php echo $img['Image']['id'];?>" <?php if ($mainImageID == $img['Image']['id']):?>checked<?php endif;?> >
			<br/>
			<a href="<?php echo IMG_ALBUMS_URL;?>/slot_big_<?php echo $img['Image']['filename'];?>">
				<img src="<?php echo IMG_ALBUMS_URL;?>/slot_thumb_<?php echo $img['Image']['filename'];?>" alt="" border="0" style='margin-top:4px;'/>
			</a>
		</li>
	<?php endforeach;?>
	</ul>
	<br class='clear'/>
</div>
<?php endif;?>