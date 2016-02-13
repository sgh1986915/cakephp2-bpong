<script type="text/javascript">
$(document).ready(function() {

// validate signup form on keyup and submit
	$("#NewPassword").validate({
			rules: {
					"data[User][pwd]": {
						required: true,
						minlength: 5
					},
					"data[User][confirm_pwd]": {
						required: true,
						minlength: 5,
						equalTo: "#UserPwd"
					}
				},
				messages: {
					"data[User][pwd]": {
						required: "Please provide a password",
						minlength: "Your password must be at least 5 characters long"
					},
					"data[User][confirm_pwd]": {
						required: "Please provide a password",
						minlength: "Your password must be at least 5 characters long",
						equalTo: "Please enter the same password as above"
					}
				}
	});
	//EOF Validation


});

</script>


<?php echo $this->Form->create('User',array('id'=>'NewPassword','name'=>'NewPassword','url'=>'/newpassword/'.$actCode));?>
	<fieldset>
	<legend>Activation</legend>

	<?php
		echo $this->Form->input('pwd',array('type'=>'password'));
		echo $this->Form->input('confirm_pwd',array('type'=>'password'));
	?>
	<?php if (isset($Error)): ?>
	<div class="error-message">Activation code is not correct or expired!</div>
	<?php endif; ?>
	</fieldset>
<?php echo $this->Form->end('Submit');?>
