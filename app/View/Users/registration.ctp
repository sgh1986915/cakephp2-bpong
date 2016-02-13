<script type="text/javascript">
$(document).ready(function() {

jQuery.validator.addMethod("alphanumeric", function(value, element) {
	return this.optional(element) || /^[A-Za-z0-9-_]+$/i.test(value);
}, "Letters, numbers or underscores only please");

  // Country click
		$("#AddressCountryId").change(function(){

			  $("#AddressProvincestateId").html('<option>Loading...</option>');
			  $.getJSON("/provincestates/getstates",{countryID: $(this).val()}, function(options){
				  //alert(options);
					$("#AddressProvincestateId").html(options);
					$('#AddressProvincestateId option:first').attr('selected', 'selected');
				})
			});
//EOF Country  click



// validate signup form on keyup and submit
	$("#Registration").validate({
		rules: {
			"data[User][lgn]":{
				required:true,
				maxlength:20,
				minlength: 5,
				alphanumeric:true
			},
			"data[Captcha][text]": "required",
			"data[User][pwd]": {
				required: true,
				minlength: 5
			},
			"data[User][confirm_pwd]": {
				required: true,
				minlength: 5,
				equalTo: "#UserPwd"
			},
			"data[User][email]": {
				required: true,
				email: true
			},
			"data[User][confirm_email]": {
				required: true,
				email: true,
				equalTo: "#UserEmail"
			}
		},
		messages: {
			"data[User][lgn]":  {required:"Please enter your nickname"},
			"data[User][pwd]": {
				required: "Please provide a password",
				minlength: "Your password must be at least 5 characters long"
			},
			"data[User][confirm_pwd]": {
				required: "Please provide a password",
				minlength: "Your password must be at least 5 characters long",
				equalTo: "Please enter the same password as above"
			},
			"data[User][email]": "Please enter a valid email address",
			"data[User][confirm_email]": {
				required: "Please enter a valid email address",
				email: "Please enter a valid email address",
				equalTo: "Please enter the same email as above"
			}
		}
	});
	//EOF Validation


});
//EOF ready
function ShowHide(i){

   if($("#circle"+i).attr("class")=="DOWN"){

      $("#additional"+i).slideDown();
      $("#circle"+i).attr({ src: "<?php echo STATIC_BPONG?>/img/circle-top.png"});
	  $("#circle"+i).removeClass("DOWN")
	  $("#circle"+i).addClass("UP");

   }else{

      $("#additional"+i).slideUp();
      $("#circle"+i).attr({ src: "<?php echo STATIC_BPONG?>/img/circle-down.png"});
	  $("#circle"+i).removeClass("UP")
	  $("#circle"+i).addClass("DOWN");

   }

}


</script>

<div class="registration form">
  <h2>Registration</h2>
  <?php echo $this->Form->create('User',array('enctype' => "multipart/form-data",'id'=>'Registration','name'=>'Registration','action'=>'registration'));?>
  <fieldset>
  <?php
            echo $this->Form->input('email', array('label' => 'Email <span class="red">*</span>'));
            echo $this->Form->input('confirm_email', array('label' => 'Confirm Email <span class="red">*</span>'));
            echo $this->Form->input('lgn',array('label'=>'Nickname <span class="red">*</span>', 'class'=>'w15'));
            echo $this->Form->input('pwd',array('type'=>'password', 'class'=>'w15', 'label'=>'Password <span class="red">*</span>'));
            echo $this->Form->input('confirm_pwd',array('type'=>'password', 'class'=>'w15', 'label'=>'Confirm Password <span class="red">*</span>'));?>
  <div style="width:150px; display:inline;">
    <label class="show">Receive updates from BPONG.COM</label>
      <?php
            echo $this->Form->input('subscribed',array('type'=>'checkbox','label'=>false));
            echo $this->Form->hidden('User.old_subscribed');
            ?>
    <div style='padding-left:155px;margin-top:40px;margin-bottom:20px;'>
   <img src="/captcha/<?php echo rand(1, 10000);?>" alt="captcha" border="0" />
    
    <br/>
    <?php echo $this->Form->input('Captcha.text', array('div' => false, 'label' => false, 'type' => 'text', 'value' => ''));?><br/>
    <span class="red">*</span> Please type the letters shown above
  	</div>
    <div class="clear"></div>
  </div>
  <div class="optional">
    <h4>Optional information <img class="UP" id="circle1" src="<?php echo STATIC_BPONG?>/img/circle-top.png" alt="UP" style="cursor:pointer;" onclick="ShowHide('1')"/> </h4>
    <div id="additional1"> <?php echo $this->Form->input('avatar',array('type'=>'file', 'class'=>'file'));?> <span style='font-size:90%;'>Image types allowed: jpg, gif, png. Maximum  image size: 500KB</span><br/>
      <?php
            echo $this->Form->input('firstname');
            echo $this->Form->input('middlename');
            echo $this->Form->input('lastname');
            echo $this->Form->input('gender');
            echo $this->Form->input('birthdate', array('minYear' => 1930,'empty'=>"choose",'selected'=>0));
            echo $this->Form->input('Phone.phone');
            echo $this->Form->input('Address.country_id',array('type' => 'select','label'=>'Country','options' => $countries));
            echo $this->Form->input('Address.address');
            echo $this->Form->input('Address.address2');
            echo $this->Form->input('Address.address3');
            echo $this->Form->input('Address.city',array('label'=>'City'));
            echo $this->Form->input('Address.provincestate_id',array('type' => 'select','label'=>'State','options' => $states));
            echo $this->Form->input('Address.postalcode');
            echo $this->Form->input('User.timezone_id',array('type' => 'select','label'=>'Time zone','options' => $timeZones));
			?>
      <div class="input">
        <label class="widelabel">Show optional information </label>
        <div class="checkbox">
          <?
            echo $this->Form->input('User.show_details',array('type'=>'checkbox','label'=>false));
            ?>
        </div>
      </div>
    </div>
   
  </div>
  </fieldset>
  <br/>
  <div class="clear"></div><br/>
  <?php echo $this->Form->end(array('value' => 'Submit','class'=>'submit'));?>
  <div class="clear"></div>
</div>
