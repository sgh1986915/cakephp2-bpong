<script type="text/javascript">
tb_pathToImage = "";
$(document).ready(function() {
// validate signup form on keyup and submit
/*Form submit*/

	$("#Login2").validate({
		rules: {
			"data[User][userlogin]": "required",
			"data[User][userpwd]": {
				required: true,
				minlength: 3
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

</script>

<div class="loginboxmain"> <?php echo $this->Form->create('User',array('id'=>'Login2','name'=>'Login2','url'=>'/login'));?>
  <fieldset class="loginpad">
  <div class="error" id="error-message2" <?php echo isset($Error)?"":"style='display: none;'" ?>>Incorrect login or password</div>
  <?php
	    echo $this->Form->hidden('URL');?>
  <label>User Name or Email:</label>
  <?php	echo $this->Form->input('userlogin',array('legend'=>'','label'=>false, 'id'=> 'userlogin2'));?>
  <br />
  <label>Password:</label>
  <?php	echo $this->Form->input('userpwd',array('type'=>'password','label'=>false, 'id'=> 'userpassword2'));?>
  <br />
  <div style='width:100%;text-align:center;'>
    <input name="data[User][rememberMe]" type="checkbox" value="1" />
    <span >Remember me</span></div>
    <br />
  <div style='width:100%;padding:5px; text-align:center;' class='submit'><?php echo $this->Form->end(array('value' => 'GO','class'=>'submit', 'div' => false, 'style' => 'display:inline;'));?></div>
  </fieldset>
  <div class="clear"></div>
  <div class="tcenter"><a href="<?php echo MAIN_SERVER;?>/users/lostPassword/?&inlineId=lostPassword2&amp;height=300&amp;width=400&amp;loading=true&amp;modal=true;" class="thickbox" id="lostPassword2" >Lost Password</a> &nbsp;| &nbsp;<a href="<?php echo MAIN_SERVER;?>/registration">New User</a>
    <br/><a href="/users/resend_activation/">Resend the Activation Email</a>
  </div>
  <div class="tcenter" style='margin-top:20px;'>
  <a href="<?php echo MAIN_SERVER;?>/users/fb_connect" ><img alt='' style='vertical-align:middle;' src="<?php echo STATIC_BPONG?>/img/fb-icon-small.jpg"/></a>&nbsp;&nbsp;<a href="<?php echo MAIN_SERVER;?>/users/fb_connect" >Login with Facebook</a>
 &nbsp;| &nbsp;
  <a href="<?php echo MAIN_SERVER;?>/users/twitter_connect" ><img alt='' style='vertical-align:middle;' src="<?php echo STATIC_BPONG?>/img/twitter.png"/></a>&nbsp;&nbsp;<a href="<?php echo MAIN_SERVER;?>/users/twitter_connect" >Login with Twitter</a>
  </div>
</div>
