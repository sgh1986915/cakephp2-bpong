<script type="text/javascript">
function submitVideoFileForm() {
	if (!$('#video_title').val()) {
		alert('Please specify title of your video');
		$('#video_title').focus()
		return false;
	}
	if (!$('#file').val()) {
		alert('Please specify video file');
		$('#file').focus()
		return false;
	}
	$('#file_upload_button').hide();
	$('#files_loader').show();
	
	$.post("/videos/ajaxGetYoutubeUrl/", { "title": $('#video_title').val(), "description" : $('#video_description').val(), "album_id" : selectedAlbumID, "tags": $('#video_tags').val()},
			   function(res){
		   			if (res.token && res.url) {
    		   			$('#videoFileForm').attr('action', res.url);
    		   			$('#video_token').val(res.token);
    		   			$('#videoFileForm').submit();
		   			} else {
						alert('Sorry - Some error on YouTybe server has occurred');
				   	}
	
	}, "json");
}
</script>

<?php echo $this->Form->create('Video',array('id'=>'videoFileForm','url'=>'/videos/youTubeUploaderBack/0', 'type' => 'file',));?>
<?php echo $this->Form->hidden('album_id', array('div' => false, 'id' => 'selectedAlbumID2', 'value' => '0')); ?>
<br/>
  	<div class="input text">
    	<label class='image_label'>* Title</label>
    	<?php echo $this->Form->input('title', array('id' => 'video_title', 'div' => false, 'type' =>'text', 'label' => false, 'div' => false, 'style' => 'width:500px;'));?>
    </div>	
  	<div class="input text">
    	<label class='image_label'>Description</label>
    	<?php echo $this->Form->input('description', array('id' => 'video_description', 'div' => false, 'type' =>'textarea', 'label' => false, 'div' => false, 'style' => 'width:500px; height:50px;'));?>
    </div>
  	<div class="input text">
    	<label class='image_label'>Tags</label>
    	<div style='float:left;'>
        	<?php echo $this->Form->input('tags', array('id' => 'video_tags', 'div' => false, 'label' => false, 'div' => false, 'style' => 'width:500px;'));?>
        	<br/><span class='tags_notice'>separate multiple tags with commas</span>
    	</div>  	
    </div>
  <div class="input text">
    	<label class='image_label'>* Video File</label>
    	<?php echo $this->Form->input('file', array('id' => 'file', 'div' => false, 'type' =>'file', 'name' => 'file', 'label' => false, 'div' => false));?>
    </div>
    	<?php echo $this->Form->hidden('token', array('id' => 'video_token', 'name' => 'token', 'value' => '123'));?>

    <br class='clear'/>           
    <div class='clear'></div>
    <div class="input text">
        	<label class='image_label'>&nbsp;</label><input type="button" value="Continue" class='red_button' onclick='submitVideoFileForm();'>
    </div>
</form>





<div id='files_loader' style='float:left;margin-left:0px;display:none;'>
    Upload of large files can take a while, please wait.<br/>
    <img border="0" src="/img/loader_verify.gif" style='margin-top:10px;'>
</div>
    <br class='clear'/>      <br class='clear'/>  