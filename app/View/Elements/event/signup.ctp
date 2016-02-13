<?php if (!empty($edit)):?>
	<?php echo $this->Form->create('Event',array('enctype'=>"multipart/form-data",'id'=>'EventEditForm','name'=>'EventEditForm','action'=>'edit'));?><?php echo $this->Form->hidden('id');	?>
<?php endif;?> 
<?php 
    if (isset($this->request->data['Event']['finish_signup_time'])) {
		$finishTimeSelected = $this->request->data['Event']['finish_signup_time'];
	} else {
		$finishTimeSelected = false;
	}
?>

<script type="text/javascript">
$(document).ready(function() {	
        $('#EventSignupRequired').click(function(){
        	if ($('#SignDate').attr('class') == 'upper') {
        		$('#SignDate').slideDown();
        		$('#SignDate').removeClass('upper');
        		$('#SignDate').addClass('bottom');
        		$('#PackagesForm').slideDown();
        		$('#PackagesForm').removeClass('upper');
        		$('#PackagesForm').addClass('bottom');
        	} else {
        		$('#SignDate').slideUp();
        		$('#SignDate').removeClass('bottom');
        		$('#SignDate').addClass('upper');
        		$('#PackagesForm').slideUp();
        		$('#PackagesForm').removeClass('bottom');
        		$('#PackagesForm').addClass('upper');
        	}
        });

        <?php if (empty($this->request->data['Event']['signup_required'])):?>
        	$('#hide_signup').hide();
        <?php endif;?>
        <?php if (empty($this->request->data['Event']['accept_paypal'])):?>
    	$('.paypal_info').hide();
    	<?php endif;?>
});
function signup_notice() {
    <?php if (empty($accessApprove)):?> 
	alert('Please note: you cannot take payments for your event through BPONG.COM');
	<?php endif;?>
	if ($('#EventSignupRequired').attr('checked')) {
		$('#hide_signup').slideDown();
	} else {
		$('#hide_signup').slideUp();
	}	

			
	return true;
}
function paypal_change() {
	if ($('#EventAcceptPaypal').attr('checked')) {
		$('.paypal_info').slideDown();
	} else {
		$('.paypal_info').slideUp();
	}	

			
	return true;
}
</script>
<div style='font-size: 15px;padding:20px;color: #333333;'>
BPONG.COM offers tournament organizers the ability to accept signups directly through our site. If you choose this option, your event will have it’s own Signup page on BPONG.COM. This will allow people to assign teams to your event in advance, which will makes using our Tournament Software even easier. In addition, our system will allow you to collect registration fees directly from players via PayPal. Would you like to take signups?
</div>
<table border="0" cellspacing="0" cellpadding="0" class="boxes">
  <tr>
    <td class="labeltd"><label for="EventSignupRequired">Take signups through BPONG</label></td>
    <td><?php echo $this->Form->input('Event.signup_required', array('label'=>false, 'onclick' => 'signup_notice()'));?></td>
  </tr>
</table>
<br/>
<div id='hide_signup'>
	<?php if (isset($AdminMenu) && !empty($AdminMenu)): ?>
	<div class="text_descript"><?php echo $this->Form->input('Event.agreement');?></div>
	<?php endif;?>
	<div class="text_descript"><?php echo $this->Form->input('Event.thankyou');?></div>
	<table border="0" cellspacing="0" cellpadding="0" class="boxes">
	  <tr>
	    <td class="labeltd"><label for="EventMultiTeam">Multi team</label></td>
	    <td><?php echo $this->Form->checkbox('Event.multi_team', array ('label'=>false) );?></td>
	  </tr>
	  <tr>
	    <td class="labeltd"><label for="EventMaxTeams">Maximum Number of Teams</label></td>
	    <td><?php echo $this->Form->input('Event.max_teams', array('label'=>false));?> </td>
	  </tr>
	  <?php /*?>
	  <tr>
	    <td class="labeltd"><label for="EventAllowSatellite">Allow satellite tournaments</label></td>
	    <td><?php echo $this->Form->checkbox('Event.allow_satellite', array('label'=>false));?></td>
	  </tr>
	  <?php */?>
	  <?php if (isset($AdminMenu) && !empty($AdminMenu)): ?>
	  <tr>
	    <td class="labeltd"><label for="EventShownOnFront">Shown on front</label></td>
	    <td><?php if (isset($this->request->data['Event']['old_shown_on_front'])):?>
	      <?php echo $this->Form->hidden('Event.old_shown_on_front');?>
	      <?php endif;?>
	      <?php echo $this->Form->input('Event.shown_on_front',array('label'=>false, 'type' => 'checkbox'));?></td>
	  </tr>
	  <?php endif;?>
	  <tr>
	    <td class="labeltd"><label for="EventMaxPeopleTeam">Number of people per team</label></td>
	    <td><?php echo $this->Form->input('Event.people_team', array('label'=>false, 'type' => 'select', 'default' => 2, 'options' => array('1' => 1,'2' => 2,'3' => 3,'4' => 4,'5' => 5,'6' => 6,'7' => 7,'8' => 8,'9' => 9,'10' => 10)));?></td>
	  </tr>
	  <tr>
	    <td><label for="EventMaxPeopleTeam">Sign-ups Cutoff Date</label></td>  
	    <td><?php	echo $this->Form->input('Event.finish_signup_date_',array('type'=> 'text', 'size' => 10, 'class' => 'date-pick dp-applied', 'readonly' => true, 'label' => false));?></td>
	  </tr>
	  <tr>
	    <td><label for="EventMaxPeopleTeam">Sign-ups Cutoff Time</label></td>  
	    <td>
	        <?php echo $this->Form->input('Event.finish_signup_time',array('type'=>'time', 'selected' => $finishTimeSelected, 'interval' => 15, 'label' => false));?>
	     </td>
	  </tr>
	  <tr>
	    <td class="labeltd"><label for="EventIsRoom">Are there hotel rooms<br/> associated with this event?</label></td>
	    <td><?php echo $this->Form->input('Event.is_room', array ('type'=>'checkbox','label'=>false) );?></td>
	  </tr>
	  <tr>
	    <td class="labeltd"><label for="EventIsRoom"><span style='color:red;'>*</span> Accept payments for<br/> this event through PayPal</label></td>
	    <td><?php echo $this->Form->input('Event.accept_paypal', array ('type'=>'checkbox','label'=>false, 'onclick' => 'paypal_change()') );?></td>
	  </tr>	  
	  <tr class='paypal_info'>
	    <td class="labeltd"><label for="EventIsRoom">PayPal Email Address</label></td>
	    <td><?php echo $this->Form->input('Event.paypal_email', array ('type'=>'text','label'=>false, 'default' => $userSession['email']) );?></td>
	  </tr>	 
	  <tr>
	    <td class="labeltd"></td>
	    <td style='font-style: italic;'><span style='color:red;'>*</span> If you do not choose this option, participants will be allowed to
	    sign up for your events without paying. You should indicate clearly
	    in your event description how people are supposed to pay fro your event.
	    </td>
	  </tr>	 	  	  
	</table>
</div>

<?php if (!empty($edit)):?>
  <div class="heightpad"></div>
  <?php echo $this->Form->end('Submit');?>
<?php endif;?> 
<br class='clear'/>
<span style='font-style: italic;'>* Once you've submitted your event, you will opportunity to set pricing</span>
