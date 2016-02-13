<?php echo $this->Html->css(array(STATIC_BPONG . '/css/signup_tabs.css')) . $this->Html->script(STATIC_BPONG . '/js/signup_tabs.js');?>
<script type="text/javascript">
$(document).ready(function() {	
	$('#<?php echo $activeTab;?>').click();	
    <?php if ($signupUser['agreement_accepted']):?>
    $('#ReadandAgree').attr('checked', 'checked');
    $('#Understand').attr('checked', 'checked');

    $('#ReadandAgree').attr('disabled', 'disabled');
    $('#Understand').attr('disabled', 'disabled');   
    <?php endif;?>
});
function SubmitAgree () {
	if (!$('#ReadandAgree').is(":checked")) {
		 alert("You have to read and agree to the terms.");
	} else if (!$('#Understand').is(":checked"))  {
		 alert("You have to agree that you understand and agree that all monies paid are not refundable for any reason.");
	} else{
		$('#agreementForm').submit();	   		 
	}
	return false;
}
</script>
<?php
	echo $this->Html->script('/js/pages/signups/signups.js', true);
?>
<!-- Signup information -->

<h2>Finish Sign-up</h2>
<div style='width:100%; clear:both; margin-bottom:0px;background-color:#8E8E8E;'> 
    	<div class='signup_tab signup_tab_passive' id='tab-agreement' onclick = "selectTab(this);">
    		<?php if (!$signupUser['agreement_accepted']):?>
    		<img src="<?php echo STATIC_BPONG?>/img/signUpX.png" class='signup_tab_image' alt='pending' />
    		<?php else:?>
    		<img src="<?php echo STATIC_BPONG?>/img/signUpV.png"  class='signup_tab_image' alt='successful' />
    		<?php endif;?>
	    	<br/>
			<span class='stab_link'>Agreement</span>   	
    	</div>
    	<div class='signup_tab signup_tab_passive' id='tab-address' onclick = "selectTab(this);">
    		<?php if (!$isAddressCompleted):?>
    		<img src="<?php echo STATIC_BPONG?>/img/signUpX.png" class='signup_tab_image' alt='pending' />
    		<?php else:?>
    		<img src="<?php echo STATIC_BPONG?>/img/signUpV.png"  class='signup_tab_image' alt='successful' />
    		<?php endif;?>
	    	<br/>
			<span class='stab_link'>Address</span>    	
    	</div>
    	<?php if ($showRoomsBlock):?>
    	<div class='signup_tab signup_tab_passive' id='tab-rooms' onclick = "selectTab(this);">
    		<?php if (!$isRoomsCompleted):?>
    		<img src="<?php echo STATIC_BPONG?>/img/signUpX.png" class='signup_tab_image' alt='pending' />
    		<?php else:?>
    		<img src="<?php echo STATIC_BPONG?>/img/signUpV.png"  class='signup_tab_image' alt='successful' />
    		<?php endif;?>
	    	<br/>
			<span class='stab_link'>Room</span>    	
    	</div>	
    	<?php endif;?>
  		<div style='float:left;margin-left:40px; margin-top:10px;'><img id='result_image' src="<?php echo STATIC_BPONG?>/img/<?php if (!$isRegistrationCompleted) { ?>signup_incomplete.gif<?php } else {?>signup_complete.gif<?php }?>"  /></div>
  		<div class='clear'></div>
</div>
<div class='signup_tab_info'>
	    <div id='tab-agreement-content' class='signup_tab_div'>
	     <?php echo $this->Form->create('Agreement',array('id'=>'agreementForm','name'=>'Step4','url'=>'/signups/accept_agreement/'.$signupId));?>    
          <div id="aggreement" style=" height: 500px; overflow: auto; padding:20px 50px 20px 20px; border:1px dotted #ccc">
          	<?php echo $this->element('/signup/agreement');?> 
          </div>
          </form>
         <?php if (!$signupUser['agreement_accepted']):?>
	         <div style="width:100px; float:left" class="step_next" id="next">
	           		<input type="button" onclick="SubmitAgree()" class="submit" value="I agree">
	         </div> 
         <?php endif;?>   
	    </div>
	    <div id='tab-address-content' class='signup_tab_div'><?php echo $this->element('/signup/teammate_address_info');?></div>
	    <?php if ($showRoomsBlock):?>
	    <div id='tab-rooms-content' class='signup_tab_div'><?php echo $this->element('/signup/tab_rooms');?></div>
	    <?php endif;?>	  
	    <div class='clear'></div>
</div>