<?php $this->pageTitle = 'Edit Album'; ?>
<script type="text/javascript">
$(document).ready(function() {
	$("#album_form").validate({
		rules: {
			"data[Album][name]": "required"
		},
		messages: {
			"data[Album][name]": "This field can not be empty!"
		}
	});
	//EOF Validation

});
</script>
<h2>Edit Album</h2>
<?php echo $this->Form->create('Album',array('id' => 'album_form', 'enctype'=>"multipart/form-data",'url'=>'/Albums/edit/' . $id));?>
<?php	
		echo $this->Form->input('name', array('label' => 'Name', 'style' => 'width:400px;'));
		echo $this->Form->input('description', array('label' => 'Description', 'div' => false, 'style' => 'width:400px;height:50px;'));
		echo $this->Form->hidden('referer', array('value'=> $referer));
?>
 		<!--  SET Authors TAGS --><?php echo $this->element('/tags/set_authors', array("modelName" => "Album", 'authorID' => $this->request->data['Album']['user_id'], 'tags' => $this->request->data['Tag']));?>
 		<!--  MANAGE Users TAGS --><?php echo $this->element('/tags/manage_users', array("modelName" => "Album", 'authorID' => $this->request->data['Album']['user_id'], 'tags' => $this->request->data['Tag']));?>

<?php echo $this->Form->end('Submit');?>

