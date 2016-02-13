<script type="text/javascript">
	$(document).ready(function(){
		$('#albumType').sSelect();
		$('#videoContent').sSelect();
		<?php if (!$album_id):?>
			$('#content_upload').hide(); 
		<?php endif;?>		
	});
</script>
<?php 
    if (!isset($this->request->data['albumType'])) {
        $this->request->data['albumType'] = '0';
    }
    if (!isset($album_id)) {
        $album_id = '0';
    }
?>

<script type="text/javascript">
	var selectedAlbumID = <?php echo $album_id;?>;
	function selectAlbumType() {
		$.ajaxSetup({cache:false});
		$('#create_album').html(contentLoading());
		$('#album_list').html('');
		$('#content_upload').hide();
		switch($('#albumType').val()){
		case "itself":
			$('.upload_block').html('4)');
			$('#video_type_block').html('3)');			
			$('#create_album').html('');
			$('#content_upload').show();
			imageLimit = 1;
		    break;
		case "new":
			$('#video_type_block').html('4)');
			$('.upload_block').html('5)');
			$('#create_album').load('/Albums/create_from_submissions/video/', function() {});
		    break;
		case "existing":
			$('#video_type_block').html('3)');		
			$('.upload_block').html('4)');
			$('#album_list').load('/Submissions/album_list/video/', function() {
				$('#create_album').html('');
			});
			break;
		case "0":
			$('#create_album').html('');			
			$('#video_type_block').html('3)');			
			$('#.upload_block').html('4)');
			break;
		}
	}
	function selectAlbumName() {
		selectedAlbumID = $('#albumID').val();
		if (selectedAlbumID == 0) {
			$('#content_upload').hide();
		} else {
			$('#content_upload').show();
		};
	}
	function selectVideoContent() {
		$('#embed_code_upload').hide();
		$('#video_file_upload').hide();
		if ($('#videoContent').val() == 'code') {
			$('#embed_code_upload').show();
		} else {
			$('#video_file_upload').show();
		};
	}
	function substr_count (haystack, needle, offset, length) {
	    // *     example 1: substr_count('Kevin van Zonneveld', 'e');
	    var pos = 0, cnt = 0;
	     haystack += '';
	    needle += '';
	    if (isNaN(offset)) {offset = 0;}
	    if (isNaN(length)) {length = 0;}
	    offset--;
	    while ((offset = haystack.indexOf(needle, offset+1)) != -1){
	        if (length > 0 && (offset+needle.length) > length){
	            return false;
	        } else{            cnt++;
	        }
	    }

	    return cnt;}
</script>
<div class="submit_package">
<span class='video_type'><strong class="red">2)</strong></span> <b>Do you want to create a new album for this video, add it to an existing album, or submit it by itself?</b>
<?php echo $this->Form->input('albumType', array('id' => 'albumType', 'label' => false, 'div' => false, 'type' => 'select', 'class' => 'custom-select', 'onchange' => 'selectAlbumType();', 'options' =>
    array('0' => 'Select One', 'itself' => 'By Itself', 'new' => 'New Album', 'existing' => 'Existing Album'), 'selected' => '0', 'selected' => $this->request->data['albumType'])); ?>
    <div style='margin-left:15px;margin-top:10px;'>
	<?php /*?><span class='submit_nums'>Note:</span> If you select "By Itself", you will only be able to upload one video at a time. <?php */ ?>
        <div id = 'album_list'>
              <?php if ($album_id && $this->request->data['albumType'] != 'itself'):?>
              <?php echo $this->element('/submissions/album_list'); ?>
              <?php endif;?>    
        </div>
    </div>
</div>
<div id='create_album'></div>
<div id='content_upload'>
    <div id='video_content_type' style='padding-top:10px; padding-bottom:10px;clear:both;' class="submit_package">
		<span class='video_type'><strong class="red" id='video_type_block'>3)</strong></span> <b>Are you embedding an already-existing online video or uploading your own video file?</b>
    <?php echo $this->Form->input('videoContent', array('id' => 'videoContent', 'label' => false, 'div' => false, 'type' => 'select', 'onchange' => 'selectVideoContent();', 'options' =>
    array('0' => 'Select One', 'code' => 'Already-existing online video', 'file' => 'Uploading own video file'), 'selected' => '0')); ?>		
    </div>
    <div id='embed_code_upload' style='display:none;padding-top:10px; padding-bottom:0px;clear:both;'  class="submit_package">
    	<span class='submit_nums upload_block'>4)</span> <b>Enter Video Info</b>
    	<?php echo $this->element('/submissions/youtube_code_form'); ?>  
    	<br class='clear'/>
    </div>
    <div id='video_file_upload' style='display:none;padding-top:10px; padding-bottom:0px;clear:both;'  class="submit_package">
    	<span class='submit_nums upload_block'>4)</span> <b>Upload Video</b>
		    <?php echo $this->element('/submissions/youtube_file_form'); ?>    		    	
    	<br class='clear'/>
    </div>
</div>