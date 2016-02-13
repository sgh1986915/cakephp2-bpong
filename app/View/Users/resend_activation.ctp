<script type="text/javascript">
$(document).ready(function() {

// validate signup form on keyup and submit
	$("#Activation").validate({
		rules: {
			"data[User][email]": "required"
		},
		messages: {
			"data[User][email]": "Please enter your email"
		}
	});
	//EOF Validation


});

</script>

<div style="padding:30px 50px 30px 0px">
<?php echo $this->Form->create('User',array('id'=>'Activation','name'=>'Activation','action'=>'resend_activation'));?>
	<fieldset>
	<legend>Resend the Activation Email</legend><BR>
<div style="background-color:#fafafa; padding:20px; border:1px #ccc dotted;">
Simply enter your registered email address in the box below, and click the "Submit" button. This must be the same email address you gave us when you became a member.
</div>
	<?php echo $this->Form->input('email',array('legend' => 'Email:','width'=>'100'));?>
	</fieldset>

<?php echo $this->Form->end('Submit');?>

</div>
