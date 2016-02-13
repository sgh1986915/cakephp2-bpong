<h1 class="login"><img src="<?php echo STATIC_BPONG;?>/img/logclose.png" id="Close" class="right" onclick="self.parent.tb_remove();" />Lost password</h1>
<div class="whitebg">
<script type="text/javascript">

$(document).ready(function() {

$('#UserUseremail').focus(function(){if ($('#UserUseremail').val()=="Email")$('#UserUseremail').val('');});
$('#UserUseremail').blur(function(){if ($('#UserUseremail').val()=="")$('#UserUseremail').val('Email');});

// validate signup form on keyup and submit
/*Form submit*/
	$("#lostPassword").validate({
		submitHandler: function(form) {
                jQuery(form).ajaxSubmit({
                	beforeSubmit: beforeSubmit,
                    success: showResponse
                });
        },
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

function showResponse(responseText)  {
 	$('#Forgotloadbtn').hide();
	$('#Forgotsubmitbtn').show();

  if (responseText=="Error"){
  		$('#error-message').show('slow');
  } else {
  		//Success
  		$('#LostPasswords').hide();
		$('#LostPasswordsOK').show();

  }
}

function registration(){
	window.location ="/registration";
	tb_remove();
}

function beforeSubmit(){
	//$('#error-message').hide('slow');
	$('#Forgotsubmitbtn').hide();
	$('#Forgotloadbtn').show();

}

</script>
<?php echo $this->Form->create('User',array('id'=>'lostPassword','name'=>'lostPassword','action'=>'/lostPassword'));?>

<fieldset style="border:none;" class="loginpad fleft" id="LostPasswords">
<div class="error" id="error-message" <?php echo isset($Error)?"":"style='display: none;'" ?>>There are no user with such email</div>
<?php
		echo $this->Form->input('useremail',array('legend'=>'','width'=>'100','label'=>false,'value'=>'Email'));
	?>
<div class="clear"></div>
<div id="Forgotsubmitbtn" class="submit">
  <input value="Submit" type="submit">
</div>
<div id="Forgotloadbtn" style="text-align:center; display:none;"> <img src="<?php echo STATIC_BPONG;?>/img/ajax_loader_m.gif" border="0"> </div>
</fieldset>
</form>
<fieldset style="display: none;" class="loginpad" id="LostPasswordsOK">
<div class="dottedborder">Email has been sent to you</div>
</fieldset>
<div class="clear"></div>
<div class="fs21 tcenter"> <a href="javascript: registration();">New User</a> &nbsp;| &nbsp;<a href="/users/login/?&inlineId=login&amp;height=300&amp;width=400&amp;modal=true;" class="thickbox" id="login">Login</a> 
<br/><br/><a href="/users/resend_activation/">Resend the Activation Email</a>
</div>
</div><img alt="" src="<?php echo STATIC_BPONG;?>/img/whitebot.png" border="0" style="vertical-align: top" />