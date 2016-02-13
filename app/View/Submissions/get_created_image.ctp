<script type="text/javascript">
$(document).ready(function() {
    $("#<?php echo 'image_tags' . $imageID;?>").autocomplete("/tags/autocomplete/Image", {
        		width: 320,
        		max: 4,
        		highlight: false,
        		multiple: true,
        		multipleSeparator: ", ",
        		scroll: true,
        		scrollHeight: 300
    });
    images["<?php echo $imageID;?>"] = <?php echo $imageID;?>;
    window.scrollTo(0,$("#all_images<?php echo $imageID;?>Name").offset().top);
    $("#all_images<?php echo $imageID;?>Name").focus();

});
</script>

<div class="submit_package">
  <div id='image_info_text_<?php echo $imageID;?>'></div>
  <div style='width:100%; margin-top:10px; text-align:center;'> <img src="<?php echo IMG_ALBUMS_URL;?>/big_<?php echo $img['Image']['filename'];?>"/> </div>
  <div class="input text">
    <label class='image_label'>* Image Title</label>
    <?php echo $this->Form->input('all_images.' . $imageID . '.name', array('label' => false, 'style' => 'width:500px;', 'div' => false)); ?> </div>
  <div class="clear"></div>
  <div class="input text">
    <label class='image_label'>Description</label>
    <?php echo $this->Form->input('all_images.' . $imageID . '.description', array('label' => false, 'type' => 'textarea', 'style' => 'width:500px;height:60px;', 'div' => false)); ?>
  </div>
  <div class="input text">
    <label class='image_label'>Tags</label>
    	<div style='float:left;'>
        <?php echo $this->Form->input('all_images.' . $imageID . '.tags', array('id' => 'image_tags' . $imageID, 'label' => false, 'style' => 'width:500px;', 'div' => false)); ?>
    	<br/><span class='tags_notice'>separate multiple tags with commas</span>
    </div>
    </div>
   <div class="clear"></div>
 <div class="input text">
    <label class='image_label'>
    <input name="data[Images][cover_id]" type="radio" value="<?php echo $imageID;?>">
    </label>
    <span class="red">Make this the album cover</span> </div>
</div>
