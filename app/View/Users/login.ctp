<h1 class="login"><img src="<?php echo STATIC_BPONG;?>/img/logclose.png" id="Close" class="right"  onclick="self.parent.tb_remove();" />Sign in</h1>
<div class="whitebg">
  <script type="text/javascript">
tb_pathToImage = "";
$(document).ready(function() {

        $('#UserUserlogin').focus(function(){if ($('#UserUserlogin').val()=="User Name or Email")$('#UserUserlogin').val('');});
		$('#UserUserlogin').blur(function(){if ($('#UserUserlogin').val()=="")$('#UserUserlogin').val('User Name or Email');});

  	    //$('#UserUserpwd').focus(function(){if ($('#UserUserpwd').val()=="Password")$('#UserUserpwd').val('');});
		//$('#UserUserpwd').blur(function(){if ($('#UserUserpwd').val()=="")$('#UserUserpwd').val('Password');});


// validate signup form on keyup and submit
/*Form submit*/

	$("#Login").validate({
		submitHandler: function(form) {
                jQuery(form).ajaxSubmit({
                	cache: false,
                	beforeSubmit: beforeSubmit,
                    success: showResponse
                });
        },
		rules: {
			"data[User][userlogin]": "required",
			"data[User][userpwd]": {
				required: true,
				minlength: 5
			}
		},
		messages: {
			"data[User][userlogin]": "This field can not be empty!",
			"data[User][userpwd]": {
				required: "Please provide a password",
				minlength: "Your password must be at least 5 characters long"
			}
		}
	});
	//EOF Validation


});

function showResponse(responseText)  {

  if (responseText=="Error"){
  		$('#error-message').show('slow');
  		$('#Loginloadbtn').hide();
  		$('#Loginsubmitbtn').show();
  } else if (responseText=="NotActive"){
			tb_remove();
			window.location.href ="/activation";
  }else {
		<?php 
		//window.location.href = 'echo $this->Html->url(array('controller' => 'users', 'action' => 'backRedirect'));';
		?>

		window.location.reload();
		/*
		tb_remove();
  		$('#userMenu').load("/users/showUserMenu",{cache: false},function(){
  						$('#UserSubmenu').load("/users/showUserSubmenu",{cache: false},function(){menuShow(); menuHide();});
  		});
		*/

  }
}

function beforeSubmit(){
	//$('#error-message').hide('slow');
	$('#Loginsubmitbtn').hide();
	$('#Loginloadbtn').show();
}

function registration(){
	window.location ="/registration";
	tb_remove(); 
}

</script>
  <?php echo $this->Form->create('User',array('id'=>'Login','name'=>'Login','url'=>'/users/login'));?>
  <fieldset>
  <div class="error" id="error-message" <?php echo isset($Error)?"":"style='display: none;'" ?>>Incorrect login or password</div>
  <?php
		echo $this->Form->input('userlogin',array('legend'=>'','width'=>'100','label'=>false,'value'=>'User Name or Email'));	?>
  <?php		echo $this->Form->input('userpwd',array('type'=>'password','label'=>false));	?>
 <div class="checkbox">
    <input name="data[User][rememberMe]" type="checkbox" value="1" /></div>
 <label>Remember me</label>
 <div class="clear"></div>
    <div id="Loginsubmitbtn" class="submit">
    <input value="Submit" class="submit" type="submit"/>
  </div>
  <div id="Loginloadbtn" style="display:none;"> <img src="<?php echo STATIC_BPONG;?>/img/ajax_loader_m.gif" border="0"> </div>
  </fieldset>
  </form>
  <div class="clear"></div>
  <div class="fs21 tcenter"><a href="javascript: registration();">New User</a> &nbsp;| &nbsp;<a href="/users/lostPassword/?&inlineId=lostPassword&amp;height=300&amp;width=400&amp;loading=false&amp;modal=true;" class="thickbox" id="lostPassword" >Lost Password</a> 
  <br/><br/><a href="/users/resend_activation/">Resend the Activation Email</a>
  </div>
  <div class="tcenter" style='margin-top:20px;'>
  <a href="<?php echo MAIN_SERVER;?>/users/fb_connect" ><img alt='' style='vertical-align:middle;' src="<?php echo STATIC_BPONG?>/img/fb-icon-small.jpg"/></a>&nbsp;&nbsp;<a href="<?php echo MAIN_SERVER;?>/users/fb_connect" >Login with Facebook</a>
 &nbsp;| &nbsp; 
  <a href="<?php echo MAIN_SERVER;?>/users/twitter_connect" ><img alt='' style='vertical-align:middle;' src="<?php echo STATIC_BPONG?>/img/twitter.png"/></a>&nbsp;&nbsp;<a href="<?php echo MAIN_SERVER;?>/users/twitter_connect" >Login with Twitter</a>
  </div>
</div>
<img style="float:left;" src="<?php echo STATIC_BPONG;?>/img/whitebot.png" border="0">
<br/>
