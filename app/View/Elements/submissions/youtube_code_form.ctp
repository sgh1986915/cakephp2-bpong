<script type="text/javascript">
function submitVideoCodeForm() {
	var videoCode = $('#video_code').val();
	$('#selectedAlbumID1').val(selectedAlbumID);
	if (videoCode) {
		$('#videoCodeForm').submit();
	} else {
		alert('Please specify correct Embed Code');
		return false;
	}

}
</script>

<?php echo $this->Form->create('Video',array('id'=>'videoCodeForm','url'=>'/videos/submissionsAdd/'));?>
<?php echo $this->Form->hidden('album_id', array('div' => false, 'id' => 'selectedAlbumID1', 'value' => '0')); ?>
<br/>
  	<div class="input text">
    	<label class='image_label'>* Embed Code</label>
    	<?php echo $this->Form->input('code', array('id' => 'video_code', 'div' => false, 'type' =>'textarea', 'label' => false, 'div' => false, 'style' => 'width:500px; height:50px;'));?>
    </div>
  	<div class="input text">
    	<label class='image_label'>Description</label>
    	<?php echo $this->Form->input('description', array('div' => false, 'type' =>'textarea', 'label' => false, 'style' => 'width:500px; height:50px;'));?>
    </div>
  	<div class="input text">
    	<label class='image_label'>Tags</label>
		<div style='float:left;'>
        	<?php echo $this->Form->input('tags', array('id' => 'video_tags', 'div' => false, 'label' => false, 'div' => false, 'style' => 'width:500px;'));?>
        	<br/><span class='tags_notice'>separate multiple tags with commas</span>
    	</div>  	
    </div>        
    <?php echo $this->Form->hidden('token', array('name' => 'token', 'id' => 'token'));?>
    <div class='clear'></div>
    <div class="input text">
        	<label class='image_label'>&nbsp;</label><input type="button" value="Continue" class='red_button' onclick='submitVideoCodeForm();'>
    </div>
</form>