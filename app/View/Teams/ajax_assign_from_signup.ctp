<script type="text/javascript">
$(document).ready(function() {
	$(function() {
		makeRadioTabs('my_radio', 'old_tab', 'radio_div');
	});
	
<?php if (!$neededTeammatesCnt):?>
		window.location.href = '<?php echo MAIN_SERVER;?>/teams/AssignTournEvent/<?php echo $model;?>/<?php echo $modelID;?>/<?php echo $teamID;?>/<?php echo $signupID;?>/<?php echo $teamNameToUse?>';
	<?php endif;?>
});
function findTeammates(){
     $('#Loading').show();
     $('#UserInformation').hide();
     //var teamid = 'asdfasd';
    
    $.post('/teammates/findNewTeammates/1',
        {'data[Teammate][email]':$('#TeammateEmail').val(),
          'data[Teammate][lgn]':$('#TeammateLgn').val(),
          'data[Teammate][last_name]':$('#TeammateLastName').val()
          ,'data[Team][id]':'<?php echo $teamID; ?>'
        },function(responseText) {
            $('#Loading').hide('slow');
            
            $('#UserInformation').html(responseText);
            $('#UserInformation').show('slow');
        });
    return false;
}
function HideTeammate(){
	if ( $("#SubmitButton").css('display') != "none" )
		 $("#SubmitButton").hide();
	if ( $("#ERROR").css('display')!= "none" )
      	 $("#ERROR").hide('slow');
	  $("#SubmitButton").hide();
      $("#UserInformation").hide(function(){ $('#Loading').show();});
}

function showTeammateResponse(responseText)  {
   		 $('#Loading').hide();
	  	 $("#SubmitButton").show();

	  if (responseText==""){
	  		$('#ERROR').show('slow');
	  } else {
	  		$('#UserInformation').html(responseText);
	  		$('#UserInformation').show();
	  }
}
function createNewUser() {
	 var filter = /^.+@.+\..{2,3}$/
	var email = $('#new_email').val();
	if(filter.test(email) == false) {	   
		alert('Invalid Email Address');
		return false;
	}
	var firstname = $('#new_firstname').val();
	var lastname = $('#new_lastname').val();
	$('#radio_div_new').html(showLoaderHtml());
	$.post("/users/registration_ajax/2/"+email+'/0/0/0', {'firstname': firstname, 'lastname': lastname}, function(data){
		ajaxAssignTeammate(<?php echo $teamID; ?>, data);
	});	
}



//EOF ready
</script>
<?php if ($neededTeammatesCnt):?>
<h3 class="new">Please invite <?php echo $neededTeammatesCnt;?> <?php if ($neededTeammatesCnt > 1):?>teammates<?php else:?>teammate<?php endif;?> to your team and continue sign-up  </h3>

<label class='radio_label'><input id ="old_tab" class="my_radio" name="data[Addition][add_type]" type="radio" value="old"/> <span>My teammate already has an account at BeerPong.com</span></label> &nbsp;&nbsp;&nbsp;
<label class='radio_label'><input id ="new_tab" class="my_radio" name="data[Addition][add_type]" type="radio" value="new"> <span>Create an account for my teammate</span></label>

<div class='radio_div' id = 'radio_div_old' style='padding-top:10px;'>
		<?php echo $this->Form->create('Teammate',array('id'=>'Teammate','name'=>'Teammate','default'=>false,'onsubmit'=>'findTeammates();'));?>
	 		<?php echo $this->Form->hidden('team_id',array('value' => $teamID));?>
	 		<div class="left" style="width:600px"><?php echo $this->Form->input('email',array('size' => 40,'label'=>'Email'));?></div>
			<div class="left" style="width:600px"><?php echo $this->Form->input('lgn',array('size' => 40,'label'=>'Username'));?></div>
	        <div class="left" style="width:600px"><?php echo $this->Form->input('last_name',array('size' => 40,'label'=>'Last Name'));?></div> 
	        <div class="clear"></div>
	            <div id="ERROR" style="display: none;">Cannot find such user</div>
			<div id="UserInformation" style="display: none;"><!-- This is for AJAX --></div>
	<div class='clear'></div><br/>
	<div id="Loading" style="display:none;float:left;clear:both;"><img src="/img/ajax_loader_m.gif" /> Loading...</div>
	<div class='clear'></div><br/>
	<?php echo $this->Form->end('Find', array('class'=>'submit_gray'));?>
</div>
<div class='radio_div' id='radio_div_new' style='padding-top:10px;'>
	 		<?php echo $this->Form->hidden('team_id',array('value' => $teamID));?>
	        <div class="left" style="width:600px"><?php echo $this->Form->input('firstname',array('id' => 'new_firstname', 'size' => 40,'label'=>'First Name'));?></div> 	 		
	 		<div class="left" style="width:600px"><?php echo $this->Form->input('lastname',array('id' => 'new_lastname', 'size' => 40,'label'=>'Last Name'));?></div> 
	 		<div class="left" style="width:600px"><?php echo $this->Form->input('email',array('id' => 'new_email', 'size' => 40,'label'=>'Email'));?></div>

	        <div class="clear"></div>
	<div class='clear'></div><br/>
<div class="submit"><input type="button" onclick="createNewUser();" value="Submit"></div>


</div>
<br/><br/>
</div>
<?php else:?>
<img src="/img/ajax_loader_m.gif" /> Loading...
<?php endif;?>