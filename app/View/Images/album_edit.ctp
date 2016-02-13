<script type="text/javascript">
$(document).ready(function() {
	$("#image_form").validate({
		rules: {
			"data[Image][name]": "required"
		},
		messages: {
			"data[Image][name]": "This field can not be empty!"
		}
	});
	//EOF Validation

});
</script>
<?php $this->pageTitle = 'Edit Image'; ?>
<h2>Edit Image</h2>
<div style='text-align:center'>
	<img src="<?php echo IMG_ALBUMS_URL;?>/big_<?php echo $this->request->data['Image']['filename'];?>"/>
</div>
<br class='clear'/>
<?php echo $this->Form->create('Image',array('id' => 'image_form', 'enctype'=>"multipart/form-data",'url'=>'/Images/albumEdit/' . $this->request->data['Image']['id']));?>
<?php
		echo $this->Form->input('name', array('label' => 'Name', 'style' => 'width:400px;'));
?>
<div class="input text">
    <?php echo $this->Form->input('description', array('label' => 'Description', 'div' => false, 'style' => 'width:400px;height:50px;')); ?>
</div>
<!--  SET Author TAGS --><?php echo $this->element('/tags/set_authors', array("modelName" => "Image", 'authorID' => $this->request->data['Image']['user_id'], 'tags' => $this->request->data['Tag']));?>
<!--  MANAGE User TAGS --><?php echo $this->element('/tags/manage_users', array("modelName" => "Image", 'authorID' => $this->request->data['Image']['user_id'], 'tags' => $this->request->data['Tag']));?>

<div class='clear'></div>
<div class="input text">
<label>Use as album cover</label>
<?php echo $this->Form->input('is_cover', array('label' => false, 'type' => 'checkbox', 'checked' => $isCover, 'value' => 1, 'div' => false)); ?>
</div>
<div class='clear'></div>
<br class='clear'/>
<?php echo $this->Form->end('Submit');?>
