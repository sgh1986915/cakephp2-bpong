<script type="text/javascript">
$(document).ready(function() {
	$("#albumForm").validate({
		submitHandler: function(form) {
        jQuery(form).ajaxSubmit({
        	beforeSubmit: beforeSubmit_Album,
            success: afterSubmit_Album
        });
    },
    rules: {
    	"data[Album][name]":"required"
    },
    messages: {
    	"data[Album][name]":"This field is required."
    }
    });	//EOF Validation
});
function afterSubmit_Album(responseText)  {
	$('#create_album').html(responseText);
	$('#albumType').attr('disabled', 'disabled')
	$('#album_loader').hide();
	$('#content_upload').show();
	
}

function beforeSubmit_Album(){
	$('#album_loader').html(contentLoading());
}
</script>
<div class="submit_package"> 
<span class='submit_nums'>3)</span>  <b>Create new album</b>
<div style='margin-left:15px;margin-top:10px;'>
<?php echo $this->Form->create('Album',array('id'=>'albumForm','url'=>'/albums/save_from_submissions/' . $type));?>
        <div class="input text">
        	<label class='image_label'>* Album Name</label>
        	<?php echo $this->Form->input('name', array('div' => false, 'type' =>'text', 'label' => false, 'div' => false, 'style' => 'width:500px;'));?> 
        </div>
          <div class="input text">
        	<label class='image_label'>Description</label>
        	<?php echo $this->Form->input('description', array('div' => false, 'type' =>'textarea', 'label' => false, 'style' => 'width:500px;height:60px;'));?> 
        </div>
	<div class="input text">
    	<label class='image_label'>Tags</label>
    	<div style='float:left;'><?php echo $this->Form->input('tags', array('id' => 'video_tags', 'div' => false, 'label' => false, 'div' => false, 'style' => 'width:500px;'));?>
    	<br/><span class='tags_notice'>separate multiple tags with commas</span></div>  	
    </div>       
<div class="clear"></div>
<div class="input text">
    	<label class='image_label'>&nbsp;</label><input type="submit" value="Submit" class='red_button'>
</div>
</form>
</div>
</div>
</div>
<div id='album_loader'></div>
<br/><br/>