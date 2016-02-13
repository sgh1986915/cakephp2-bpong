<script type="text/javascript">
$(document).ready(function() {

// validate signup form on keyup and submit
	$("#Activation").validate({
		rules: {
			"data[User][activation_code]": "required"
		},
		messages: {
			"data[User][activation_code]": "Please enter your activation code!"
		}
	});
	//EOF Validation


});

</script>

<div style="padding:30px 50px 30px 0px">
<?php echo $this->Form->create('User',array('id'=>'Activation','name'=>'Activation','action'=>'activation'));?>
	<fieldset>
	<legend>Activation</legend><BR>
		<div style="background-color:#fafafa; padding:20px; border:1px #ccc dotted;">Your account has been created but not activated, most likely because you did not follow the directions in the activation email.
        <br />
		<BR>Please type activation code from the activation email or use resend activation link and new activation code will be send to you.</div>
	<?php echo $this->Form->input('activation_code',array('legend'=>'Activation code:','width'=>'100'));?>
	<?php if (isset($Error)): ?>
	<div class="error-message"> Activation code is not correct!</div>
	<?php endif; ?>
	</fieldset>

<?php echo $this->Form->end('Submit');?>
	<?php if ($this->Session->check("ActivationUserID")):?>
	<div style="padding:20px 0px 3px 0; border-top:#ccc dotted 1px">
		<a href="/users/reactivate" style="text-decoration:none">
			<input type="button" class="submit" name="resend" value="Resend Activation code" style="background:transparent url(<?php echo STATIC_BPONG;?>/img/btn_resend.gif) center center no-repeat; width:175px; border:none; color:#fff;" />
		</a>
	</div>
	<?php endif;?>

</div>
