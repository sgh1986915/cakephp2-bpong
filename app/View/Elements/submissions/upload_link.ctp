<script type="text/javascript">
	function submitContentForm() {
		var someEmpty = 0;
		if (!$('#link_title').val()) {
			someEmpty = 1;
			alert('Please specify Title');
		}	
		if (!$('#link_url').val()) {
			someEmpty = 1;
			alert('Please specify Url');
		}	
		if (someEmpty == 1) {

		} else {
			$('#submitContentForm').submit();
		}			

	}
</script>
<script type="text/javascript">
$(document).ready(function() {
	$("#submitContentForm").validate({
    rules: {
    	"data[Link][title]":"required",
    	"data[Link][url]":"required"    	
    },
    messages: {
    	"data[Link][title]":"This field is required.",
    	"data[Link][url]":"This field is required."    	
    }
    });	//EOF Validation
});
</script>
<div id='content_upload' class="submit_package"> <span class='submit_nums' id='upload_block'>2)</span> <b>Enter Link Info</b> <?php echo $this->Form->create('Link',array('id'=>'submitContentForm', 'url'=>'/links/submissionsAdd/'));?>
 <div class="input text">
  	<label class='image_label'>* Title</label>
      <?php echo $this->Form->input('title', array('id' => 'link_title', 'div' => false, 'type' =>'text', 'label' => false, 'style' => 'width:500px;'));?>
	</div>  
	<div class="input text">
  	<label class='image_label'>* URL</label>
      <?php echo $this->Form->input('url', array('id' => 'link_url', 'div' => false, 'type' =>'text', 'label' => false, 'style' => 'width:500px;'));?>
  </div>
  <div class="input text">
  	<label class='image_label'>Description</label>
      <?php echo $this->Form->input('description', array('div' => false, 'type' =>'textarea', 'label' => false, 'style' => 'width:500px;'));?>
	</div>
	<div class="input text">
    	<label class='image_label'>Tags</label>
    	<div style='float:left;'><?php echo $this->Form->input('tags', array('id' => 'video_tags', 'div' => false, 'label' => false, 'div' => false, 'style' => 'width:500px;'));?>
    	<br/><span class='tags_notice'>separate multiple tags with commas</span></div>  	
    </div>
<div class="clear"></div>
<div class="input text">
    	<label class='image_label'>&nbsp;</label><input type="submit" value="Complete" class='red_button'>
</div>
  </form>
  <div class="clear"></div>
</div>
