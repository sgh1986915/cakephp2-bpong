<script type="text/javascript">
$(document).ready(function() {
// validate signup form on keyup and submit
	$("#EventfeatureAddForm").validate({
		rules: {
			"data[Eventfeature][name]": "required"
		},
		messages: {
			"data[Eventfeature][name]": "Please enter name"
		}
	});
	//EOF Validation
	
	
});
//EOF ready
</script>
<div class="eventfeatures form p10">
<?php echo $this->Form->create('Eventfeature');?>
	<fieldset>
 		<legend>Add Eventfeature</legend>
	<div class="p10 left35">
	<?php echo $this->Form->input('name');	?>
	<label>Private</label><?php echo $this->Form->input('private', array('label'=>false)); ?>
    </div>
	</fieldset>
<?php echo $this->Form->end('Submit');?>
</div>
