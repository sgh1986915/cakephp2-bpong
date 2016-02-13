<script type="text/javascript">
$(document).ready(function() {
	tb_init('a.thickbox, area.thickbox, input.thickbox');
// Age validation rule
	var current   = new Date("<?php echo  $this->Time->niceDateJS($modelInfo['start_date']);?>");
	msPerDay = 24 * 60 * 60 * 1000;
jQuery.validator.addMethod("age", function(value, element) {

	if ( $("#UserBirthdateMonth").val()!="" &&  $("#UserBirthdateYear").val()!="" &&  $("#UserBirthdateDate").val()!=""){

		var birthdate = new Date($("#UserBirthdateYear").val(),$("#UserBirthdateMonth").val()-1,$("#UserBirthdateDay").val());
		daysLeft = Math.round((current.getTime() - birthdate.getTime())/msPerDay);
	    age= Math.floor(daysLeft/365);

		if(age < <?php echo $maxAge?>){
			return false;
		} else {
			return true;
		}

	} else {
		return true;
	}

}, "Your age must be greater then <?php echo $maxAge?> years old.");
//EOF age validation rule


<?php /*
$("select").change(function() {
  $("#Step2").valid();
});
<?php */ ?>
// validate signup form on keyup and submit
	$("#addressInfo").validate({
		rules: {
			"data[User][birthdate][month]":"required",
			"data[User][birthdate][day]":{
      				required: function(element) {
        							return $("#UserBirthdateMonth").val()!="";
      			                    }
             },
			"data[User][birthdate][year]":{
      				required: function(element) {
        							return $("#UserBirthdateDay").val()!="";
      			                    },
      			     age:true
             },
			"data[User][gender]":{
					required:true
			},
			<?php if (!$homeCount):?>
				"data[Address][country_id]":{min:1},
				"data[Address][address]":"required",
				"data[Address][city]":"required",
				"data[Address][provincestate_id]":{min:1},
				"data[Address][postalcode]":"required",
			<?php endif; ?>			
			"data[Phone][cnt]":"required",
			"data[User][firstname]":"required",
			"data[User][lastname]":"required",
			"data[User][gender]":"required"
		},
		messages: {
			"data[User][firstname]":"Please enter your first name",
			"data[User][lastname]":"Please enter your last name",
			<?php if (!$homeCount):?>
				"data[Address][country_id]":{min:"This field is required."},
				"data[Address][provincestate_id]":{min:"This field is required."},
			<?php endif; ?>
			"data[User][gender]":"Please enter your gender",
			"data[User][birthdate][month]":"Please enter your month of birth",
			"data[User][birthdate][day]":"Please enter your day of birth",
			"data[User][birthdate][year]":{
					required:"Please enter your year of birth"
			},
			"data[Phone][cnt]":{
				required:"Please enter your phone"
			}
		}
	});
	//EOF Validation
  <?php if (!$homeCount):?>
  // Country click
		$("#AddressCountryId").change(function(){
			  $("#AddressProvincestateId").html('<option>Loading...</option>');
			  $.getJSON("/provincestates/getstates",{countryID: $(this).val()}, function(options){
					$("#AddressProvincestateId").html(options);
					$('#AddressProvincestateId option:first').attr('selected', 'selected');
				})
			});
	//EOF Country  click
   <?php endif; ?>
	
});

function  DeletePhone(phoneID){
	//AJAX call for deleting Phone
				$.post("/phones/delete/<?php echo $this->request->data['User']['id']?>/"+phoneID
	               ,{
	               		phoneID: phoneID
	                }
	               ,function(response){
	               		if (response==""){
	                		$("#phone_"+phoneID).hide("slow");
	                	} else {
	                		alert(response);
	                	}
	                });

}
</script>

<?php echo $this->Form->create('User',array('id'=>'addressInfo','name'=>'addressInfo','url'=>'/signups/update_address/' . $signupId));?>

<div class="error" id="error-message" style='display: none;'>Error while Updating.</div>
    <div class="users form" style="border:#ccc 1px dotted; margin-top:20px;padding-bottom:15px;">
        <fieldset>
        	<div class="input text">
        		<label for="UserEmail">Email</label>
        		<?php echo $this->request->data['User']['email'];?>
        		<a href="/users/settings/<?php echo $userSession['id'];?>" style="margin-left: 30px;">Change email address</a>
        	</div>
    	<?php
    		echo $this->Form->input('firstname');
    		echo $this->Form->input('middlename');
    		echo $this->Form->input('lastname');
    		echo $this->Form->input('gender');
    		echo $this->Form->input('birthdate', array('minYear' => 1930,'maxYear' => 2005,'empty'=>"choose"));
    	?>
    	    <div style="width:150px; display:inline;">
        		<label class="show">Subscribe to the mail list </label>
    	<div style="width:16px; float:left">
			<?php
            echo $this->Form->input('subscribed',array('type'=>'checkbox','label'=>false));
            echo $this->Form->hidden('User.old_subscribed');
            ?>
    	</div>
    	</fieldset>
    	<!-- Show address information -->
    	<div class='clear'><br/></div>
   <div class="left35"><h4>Home address information</h4></div>
   		<?php if ($homeCount):?>
   		<div class="left35">
   		   <div id="addressInformation" class="details">
			 <!-- Please don't remove this DIV it's for AJAX -->
			<?php echo $this->requestAction('/addresses/view/User/' . $this->request->data['User']['id'] . '/' . $this->request->data['User']['id']. '/Home');?>	
		   </div>
		   	</div>			   	
		<?php else:?>
		<fieldset>
		<?php 
        	echo $this->Form->hidden('Address.label', array('value' => 'Home'));
        	echo $this->Form->input('Address.country_id',array('type' => 'select','label'=>'Country','options' => $countries));
            echo $this->Form->input('Address.address');
            echo $this->Form->input('Address.address2');
            echo $this->Form->input('Address.address3');
            echo $this->Form->input('Address.city',array('label'=>'City'));
            echo $this->Form->input('Address.provincestate_id',array('type' => 'select','label'=>'State','options' => $states));
		echo $this->Form->input('Address.postalcode');
		?>   	
		</fieldset>
    	<?php endif;?>
    <!-- EOF Address information -->
    <!-- PHONES -->
    <div class="left35"><h4 >Phones</h4>
       <div id="phoneInformation" class="details">
    		 <!-- Please don't remove this DIV it's for AJAX -->
    		 <?php echo $this->requestAction('/phones/view/User/' . $this->request->data['User']['id'] . '/' . $this->request->data['User']['id']);?>
        </div>
        <div class='clear'><br/></div>
	</div>
	<!-- EOF PHONES -->
	<div class='clear'><br/></div>
    </div>
  	 <div style="padding:15px 0 0 20px">
        <div id="next" class='step_next' style="width:100px; float:left"><input type="submit"  value="Submit" class="submit"  style='margin-top:0px;'/></div>
    </div>
</form>