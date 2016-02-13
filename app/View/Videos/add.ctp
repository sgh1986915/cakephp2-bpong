<script type="text/javascript">
	function submitContentForm() {
		var videoCode = $('#video_code').val();
		if (videoCode && substr_count(videoCode, 'youtube')) {
			$('#submitContentForm').submit();
		} else {
			alert('Please specify correct YouTube Embed Code');
			return false;
			
		}
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

<div id='content_upload' style='padding-top:10px; padding-bottom:0px;clear:both;'  class="submit_package">
<b>Enter YouTube Video Info</b>
	<?php echo $this->Form->create('Video',array('id'=>'submitContentForm','url'=>'/videos/add/'));?>
	<?php echo $this->Form->hidden('album_id', array('div' => false, 'id' => 'selectedAlbumID', 'value' => $albumID)); ?>
	<br/>
        <div style='float:left;'>
        	<label class='image_label' style='width:50px;'>* Embed Code</label>
        	<?php echo $this->Form->input('code', array('id' => 'video_code', 'div' => false, 'type' =>'textarea', 'label' => false, 'div' => false, 'style' => 'width:320px;height:60px;'));?>
        </div>
          <div style='float:left;'>
        	<label class='image_label' style='width:80px;'>Description</label>
        	<?php echo $this->Form->input('description', array('div' => false, 'type' =>'textarea', 'label' => false, 'style' => 'width:300px;height:50px;'));?>
        </div>
      <div class='clear'>&nbsp;</div>
    <div style='text-align:center;'><input type="button" value="Complete" class='red_button' onclick='submitContentForm();'></div>
	</form>
	<br class='clear'/>
</div>