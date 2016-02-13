<h2>Login</h2>
<script type="text/javascript">
$(document).ready(function() {
		$('#reg').hide();
		$('#preg').hide();
        $('#cartslogin').focus(function(){if ($('#cartslogin').val()=="User Name")$('#cartslogin').val('');});
		$('#cartslogin').blur(function(){if ($('#cartslogin').val()=="")$('#cartslogin').val('User Name');});

/*Form submit*/

	$("#cartsLogin").validate({
		submitHandler: function(form) {
        	loginProcess();
        },
		rules: {
			"data[User][cartslogin]": {
			required:true
			},
			"data[User][cartspwd]": {
				required: true,
				minlength: 5

			}
		},
		messages: {
			"data[User][cartslogin]": {
			required:"Please enter your nickname!"
			},
			"data[User][cartspwd]": {
				required: "Please provide a password",
				minlength: "Your password must be at least 5 characters long"
			}
		}
	});
	//EOF Validation
	// validate signup form on keyup and submit
	$("#Registration").validate({
		submitHandler: function(form) {
			registerProcess();
        },
		rules: {
			"data[Register][lgn]": {
			required:true,
			alphanumeric:true
			},
			"data[Register][pwd]": {
				required: true,
				minLength: 5
			},
			"data[Register][confirm_pwd]": {
				required: true,
				minlength: 5,
				equalTo: "#pwd"
			},
			"data[Register][email]": {
				required: true,
				email: true
			},
			"data[Register][confirm_email]": {
				required: true,
				email: true,
				equalTo: "#reg_email"
			}
		},
		messages: {
			"data[Register][lgn]": {
			required:"Please enter your nickname!",
			alphanumeric:"Your password must have only letters, numbers or underscores"
			},
			"data[Register][pwd]": {
				required: "Please provide a password",
				minLength: "Your password must be at least 5 characters long"
			},
			"data[Register][confirm_pwd]": {
				required: "Please provide a password",
				minLength: "Your password must be at least 5 characters long",
				equalTo: "Please enter the same password as above"
			},
			"data[Register][email]": "Please enter a valid email address",
			"data[Register][confirm_email]": {
				required: "Please enter a valid email address",
				email: "Please enter a valid email address",
				equalTo: "Please enter the same email as above"
			}
		}
	});
	$("#Pseudo").validate({
		submitHandler: function(form) {
            pseudoProcess();
        },
		rules: {
			"data[Pseudo][email]": {
				required: true,
				email: true
			}
		},
		messages: {
			"data[Pseudo][email]": "Please enter a valid email address"
		}
	});
	//EOF Validation

});
// hidden error reporting when change login input
function loginChange(){
	$('#error_login').hide();
}
// When one Login type selected, others hidden
function selectLogtype(type){
	if(type=='log'){
		if($('#reg').css('display')!='none'){
			$('#reg').slideUp();
		}
		if($('#preg').css('display')!='none'){
			$('#preg').slideUp();
		}
		$('#'+type).slideDown();
	}
	if(type=='reg'){
		if($('#log').css('display')!='none'){
			$('#log').slideUp();
		}
		if($('#preg').css('display')!='none'){
			$('#preg').slideUp();
		}
		$('#'+type).slideDown();
	}
	if(type=='preg'){
		if($('#log').css('display')!='none'){
			$('#log').slideUp();
		}
		if($('#reg').css('display')!='none'){
			$('#reg').slideUp();
		}
		$('#'+type).slideDown();
	}
}
// AJAX process for login form
function loginProcess(){
	var oldnext=$('#next1').html();
	var loader='<img src="<?php echo STATIC_BPONG?>/img/ajax_loader_m.gif" border="0">';
	$('#next1').html(loader)
	var cartslogin=$('#cartslogin').val();
	var cartspwd=$('#cartspwd').val();
	$('#error_login').hide();
	$.ajaxSetup({cache:false});
	$.get("/users/login/"+cartslogin+'/'+cartspwd,{cache: false}, function(data){
	  if(data=='ok'){
 		$('#userMenu').load("/users/showUserMenu",function(){
  		$('#UserSubmenu').load("/users/showUserSubmenu",function(){menuShow(); menuHide();});});
		top.location.href ='<?php echo SECURE_SERVER; ?>/checkout';
	  }else{
	  	$('#next1').html(oldnext)
	  	$('#error_login').show();
	  }
	});
}
// AJAX process for registration form
function registerProcess(){
	var oldnext=$('#next2').html();
	var loader='<img src="<?php echo STATIC_BPONG?>/img/ajax_loader_m.gif" border="0">';
	$('#next2').html(loader)
	var email=$('#reg_email').val();
	var lgn=$('#lgn').val();
	var pwd=$('#pwd').val();
	$('#error_register').hide();
	$.ajaxSetup({cache:false});
	$.get("/users/registration_ajax/1/"+email+'/'+lgn+'/'+pwd,{cache: false}, function(data){
	  if(data=='ok'){
		top.location.href ='/checkout';
	  }else{
	  	$('#next2').html(oldnext)
	  	if(data=='erroruser'){
	  		$('#error_register').html('Such Nickname already exist.');
	  	}
	  	if(data=='erroremail'){
	  		$('#error_register').html('User with such Email already exist.');
	  	}
	  	$('#error_register').show();
	  }
	});
	return false;
}
// AJAX process for pseudo registration form
function pseudoProcess(){
	var oldnext=$('#next3').html();
	var loader='<img src="<?php echo STATIC_BPONG?>/img/ajax_loader_m.gif" border="0">';
	$('#next3').html(loader)
	var email=$('#pseudo_email').val();
	$('#error_pseudo').hide();
	$.ajaxSetup({cache:false});
	$.get("/users/registration_ajax/2/"+email,{cache: false}, function(data){
	  if(data=='ok'){
		top.location.href ='/checkout';
	  }else{
	  	alert(data);
	  	$('#next3').html(oldnext);
	  	$('#error_pseudo').html('User with such Email already exist.'+data);
	  	$('#error_pseudo').show();
	  }
	});
	return false;
}
</script>
<div class="logtobuy">
<input name="log_type" type="radio" value="log" OnClick="selectLogtype('log');"  checked> 1. Login to place your order
<?php
echo $this->Form->create('User',array('id'=>'Login','name'=>'cartsLogin','onsubmit'=>'return false;','id'=>'cartsLogin'));
?>
<div id='log'> <!-- DIV FOR AJAX -->
	<fieldset style="border:none;">
	<label class="error" id='error_login' style='display:none;'>Incorrect login or password.</label>
		<?php
		echo $this->Form->input('cartslogin',array('legend'=>'','id'=>'cartslogin','width'=>'100','label'=>false,'value'=>'User Name','onchange'=>'loginChange();'));
		echo $this->Form->input('cartspwd',array('type'=>'password','id'=>'cartspwd','label'=>false,'onchange'=>'loginChange();'));
	    ?>
	</fieldset>
	<br />
    <div class='step_previous' id='prev1' style='margin-bottom:20px !important;'><input type="button" value="Previous" class="submit" onclick="history.back();" /></div>
	<div class='step_next' id='next1' style='margin-bottom:20px !important;'><input type="submit" value="Next"></div>
</div>
</form>
<div class="clear"></div>
<div class='clear'><br/></div>
<div class="left" style='clear:both;width:100%;'>
  <a href="<?php echo MAIN_SERVER;?>/users/fb_connect/store_login" ><img alt='' style='vertical-align:middle;' src="<?php echo STATIC_BPONG?>/img/fb-icon-small.jpg"/></a>&nbsp;&nbsp;<a href="<?php echo MAIN_SERVER;?>/users/fb_connect/store_login" >Login with Facebook</a>
 &nbsp;| &nbsp; 
  <a href="<?php echo MAIN_SERVER;?>/users/twitter_connect/store_login" ><img alt='' style='vertical-align:middle;' src="<?php echo STATIC_BPONG?>/img/twitter.png"/></a>&nbsp;&nbsp;<a href="<?php echo MAIN_SERVER;?>/users/twitter_connect/store_login" >Login with Twitter</a>
</div>
<br/>

</div>

<div class="logtobuy">
<input name="log_type" type="radio" value="reg" OnClick="selectLogtype('reg');">2. Register to place your order
     	   	   <?php echo $this->Form->create('Register',array('id'=>'Registration','name'=>'Registration','onsubmit'=>'return false;'));?>
<div id='reg'> <!-- DIV FOR AJAX -->
	        <fieldset>
			<label class="error" id='error_register' style='display:none;'></label>

     	   		<?php
            echo $this->Form->input('email',array('id'=>'reg_email'));
            echo $this->Form->input('confirm_email');
            echo $this->Form->input('lgn',array('label'=>'Nickname', 'class'=>'w15','id'=>'lgn'));
            echo $this->Form->input('pwd',array('type'=>'password', 'class'=>'w15','id'=>'pwd'));
            echo $this->Form->input('confirm_pwd',array('type'=>'password', 'class'=>'w15'));
            ?>
            </fieldset>
			<div class='step_previous'  id='prev2'><input type="button" value="Previous" class="submit" onclick="history.back();" /></div>
			<div class='step_next' id='next2'>
				<input type="submit" value="Next">
			</div>
		</div>
		</form>
	            <div class="clear"></div>
</div>




<div class="logtobuy">
<input name="log_type" type="radio" value="preg" OnClick="selectLogtype('preg');">3. Place your order without registration
     	   <?php echo $this->Form->create('Pseudo',array('id'=>'Pseudo','name'=>'Pseudo','onsubmit'=>'return false;'));?>
<div id='preg'> <!-- DIV FOR AJAX -->
           <fieldset>
           <br> Please enter your email address
			<label class="error" id='error_pseudo' style='display:none;'></label>
        	<?php
            echo $this->Form->input('email',array('id'=>'pseudo_email'));?>
            </fieldset>
			<div class='step_previous'  id='prev3'><input type="button" value="Previous" class="submit" onclick="history.back();" /></div>
			<div class='step_next' id='next3'>
				<input type="submit" value="Next">
			</div>
		</div>
		</form>
            <div class="clear"></div>
</div>
<?php
	//echo $this->element('checkout_footer');
?>
<script type="text/javascript">var journeycode='255fc24b-2b48-4ef5-aedf-6710494792d8';var captureConfigUrl='cdsusa.veinteractive.com/CaptureConfigService.asmx/CaptureConfig';</script> 
<script type="text/javascript">try { var vconfigHost = (("https:" == document.location.protocol) ? "https://" : "http://"); document.write(unescape("%3Cscript src='" + vconfigHost + "configusa.veinteractive.com/vecapture.js' type='text/javascript'%3E%3C/script%3E")); } catch(err) {} </script>

