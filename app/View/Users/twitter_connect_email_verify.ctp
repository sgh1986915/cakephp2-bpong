<h3>Please specify your email to finish your registration</h3>
<div class="whitebg">
<script type="text/javascript">

$(document).ready(function() {

$('#UserUseremail').focus(function(){if ($('#UserUseremail').val()=="Email")$('#UserUseremail').val('');});
$('#UserUseremail').blur(function(){if ($('#UserUseremail').val()=="")$('#UserUseremail').val('Email');});

// validate signup form on keyup and submit
/*Form submit*/
	$("#specEmail").validate({
		rules: {
			"data[User][useremail]": {required: true,
								  email: true
								  }
		},
		messages: {
			"data[User][useremail]": "Please enter a valid email address"
		}
	});
	//EOF Validation


});

</script>
<?php echo $this->Form->create('User',array('id'=>'specEmail','name'=>'specEmail','url'=>'/users/twitter_connect_email_verify/' . $userID .'/' . $twitterID));?>

<fieldset style="border:none;" class="loginpad fleft" id="LostPasswords">
<div class="error" id="error-message" <?php echo isset($error)?"":"style='display: none;'" ?>><?php echo $error;?></div>
<div style='width:350px;'>
<?php
		echo $this->Form->input('useremail',array('legend'=>'','width'=>'100','label'=>false,'value'=>'Email', 'div' => false));
	?>
</div>	
<div class="clear"></div>
<div id="Forgotsubmitbtn" class="submit">
  <input value="Submit" type="submit">
</div>
<div id="Forgotloadbtn" style="text-align:center; display:none;"> <img src="<?php echo STATIC_BPONG;?>/img/ajax_loader_m.gif" border="0"> </div>
</fieldset>
</form>
</div>