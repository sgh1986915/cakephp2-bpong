<?php echo $this->Html->css(array(STATIC_BPONG . '/css/signup_tabs.css')) . $this->Html->script(STATIC_BPONG . '/js/signup_tabs.js');?>
<script type="text/javascript">
var signUpId = <?php echo $signupDetails['Signup']['id'];?>;
$(document).ready(function() {	
	$('#<?php echo $activeTab;?>').click();	
});
function upgrade_package_link (signupID) {
	$('#change_package_div').hide();
	$('#change_package_div').html('<br/><img src="<?php echo STATIC_BPONG?>/img/ajax-loader.gif" border="0" /><div class="clear"><br/></div>');
	$('#change_package_div').slideDown();
	$('#change_package_div').load("/signups/upgradePackage/" + signupID,{cache: false});	
	return false;
}
function change_package_link (signupID) {
	$('#change_package_div').hide();
	$('#change_package_div').html('<br/><img src="<?php echo STATIC_BPONG?>/img/ajax-loader.gif" border="0" /><div class="clear"><br/></div>');
	$('#change_package_div').slideDown();
	$('#change_package_div').load("/signups/changePackage/" + signupID,{cache: false});	
	return false;
}

</script>
<?php
	echo $this->Html->script('/js/pages/signups/signups.js', true);
?>
<?php $model = $signupDetails['Signup']['model'];?>
<!-- Signup information -->

<h2>Sign-up information</h2>
<div style='width:100%; clear:both; margin-bottom:0px;background-color:#8E8E8E;'> 
  		<div class='signup_tab signup_tab_passive' id='tab-payment' onclick = "selectTab(this);">
			<?php if ($signupDetails['Signup']['status']=='paid' ):?>
			    <img src="<?php echo STATIC_BPONG?>/img/signUpV.png" class='signup_tab_image'  alt='successful' /><br/>
			    <span class='stab_link'>Paid in Full</span>
			<?php else:?>
			    <img src="<?php echo STATIC_BPONG?>/img/signUpX.png" class='signup_tab_image' alt='pending' /><br/>
			    <span class='stab_link'><?php echo ucwords(strtolower($signupDetails['Signup']['status']))?></span>
			<?php endif;?>  		
  		</div>
 		<?php if($signupDetails['Package']['allow_team']) : ?>
  		<div class='signup_tab signup_tab_passive' id='tab-team' onclick = "selectTab(this);">
	  	 	<?php if ($teamIsCompleted):?>
				<img src="<?php echo STATIC_BPONG?>/img/signUpV.png"  class='signup_tab_image' alt='successful' /><br/>
			<?php else:?>
			
				<?php if (!empty($teamInfoForSignup['waiting_for_signup']) || !empty($teamInfoForSignup['waiting_for_accept'])):?>
				<img src="<?php echo STATIC_BPONG?>/img/signUpClock.png"  class='signup_tab_image' alt='pending' /><br/>
				<?php else:?>
				<img src="<?php echo STATIC_BPONG?>/img/signUpX.png"  class='signup_tab_image' alt='pending' /><br/>
				<?php endif;?>
			<?php endif;?>
			<span class='stab_link'>Add Team to Event</span>
  		</div>
  		<?php endif;?>	
  		<?php if ($signupDetails[$signupDetails['Signup']['model']]['is_room']>0 && $signupDetails['Package']['people_in_room'] > 0):?>	
  		<div class='signup_tab signup_tab_passive' id='tab-rooms' onclick = "selectTab(this);">
  			<?php if($roomIsCompleted):?>
				<img src="<?php echo STATIC_BPONG?>/img/signUpV.png"  class='signup_tab_image' alt='successful' /><br/>
			<?php elseif (!empty($waitingForTemmatesRoom) || !empty($roomIsPending) && !$showFindInviters):?>
				<img src="<?php echo STATIC_BPONG?>/img/signUpClock.png"  class='signup_tab_image' alt='pending' /><br/>
  			<?php else:?>
				<img src="<?php echo STATIC_BPONG?>/img/signUpX.png"  class='signup_tab_image' alt='pending' /><br/>
			<?php endif;?>
			<span class='stab_link'>Roommates & Rooms</span>
  		</div>
  		<?php endif;?>
  		<div style='float:left;margin-left:40px; margin-top:10px;'><img id='result_image' src="<?php echo STATIC_BPONG?>/img/<?php if ($signupDetails['Signup']['status']=='paid' && $roomIsCompleted && (!$signupDetails['Package']['allow_team'] || $teamIsCompleted)) { ?>signup_complete.gif<?php } else {?>signup_incomplete.gif<?php }?>"  /></div>
  		<div class='clear'></div>
</div>
<div class='signup_tab_info'>
	    <div id='tab-payment-content' class='signup_tab_div'><?php echo $this->element('/signup/tab_payment');?></div>
	    <div id='tab-rooms-content' class='signup_tab_div'><?php echo $this->element('/signup/tab_rooms');?></div>
	    <div id='tab-team-content' class='signup_tab_div'><?php echo $this->element('/signup/tab_team', array('new_created_team_id' => $new_created_team_id));?></div>
	    <div class='clear'></div>
</div>
<br/>


<div>
  <div class="clear"></div>
  <div style="border: 1px dotted rgb(204, 204, 204); padding: 15px; background-color: rgb(255, 247, 217); font-size: 13px; line-height: normal; margin-top:25px"> "<?php echo !empty(empty($signupDetails[$model]['shortname']) , $signupDetails[$model]['name'], $signupDetails[$model]['shortname'] );?>" sign-up is a <?php echo ife(empty($signupDetails[$model]['is_room']),3,4 )?>-step process. You must complete all <?php echo ife(empty($signupDetails[$model]['is_room']),3,4 )?> steps to be entered into the tournament. <strong>If you see any red X's above, your  "<?php echo ife(empty($signupDetails[$model]['shortname']) ) ? $signupDetails[$model]['name'] : $signupDetails[$model]['shortname'] ;?>" sign up is not fully complete and you will not be seeded in the tournament.</strong> Click the red X's to proceed to the appropriate section to complete your sign up. Once all steps are green checks, you are good to go! </div>
  <div class="clear"></div>
</div>


<br/>
<div class="infosignups">
  <dl class="signup_info">
    <dt>Signup to:</dt>
    <dd><a href="/<?php echo strtolower($model);?>/<?php echo $signupDetails[$model]['id']; ?>/<?php echo $signupDetails[$model]['slug']; ?>"><?php echo $signupDetails[$model]['name']; ?></a></dd>
    <dt>Signup date:</dt>
    <dd><?php echo $this->Time->niceShort($signupDetails['Signup']['signup_date']); ?></dd>
    <?php if ($isFreeSignup):?>
	    <dt>Status:</dt>
	    <dd>Free</dd>   
    <?php else:?>
	    <dt>Status:</dt>
	    <dd><?php echo ucwords(strtolower($signupDetails['Signup']['status'])); ?> <?php if ($signupDetails['Signup']['for_team']):?>(for entire team)<?php endif;?></dd>
	    <dt>Paid:</dt>
	    <dd>$<?php echo sprintf("%.2f",$signupDetails['Signup']['paid']); ?></dd>
	    <dt>Discount:</dt>
	    <dd>$<?php echo sprintf("%.2f",$signupDetails['Signup']['discount']); ?></dd>
	    <dt>Total:</dt>
	    <dd>$<?php echo sprintf("%.2f",$signupDetails['Signup']['total']); ?></dd>
    <?php endif;?>
    <dt>Creator:</dt>
    <dd><a href="/u/<?php echo $signupDetails['User']['lgn']; ?>"><?php echo $signupDetails['User']['lgn']; ?></a></dd>
  </dl>
</div>
<!--EOF Signup information -->
<!-- Model Information -->
<div class="signupsinfor gray_hr">
  <?php if (!empty($signupDetails[$model])):?>
  <?php $modelInformation = $signupDetails[$model];?>
  <h3><?php echo $model; ?> information</h3>
  <strong>Name:</strong> <?php echo $modelInformation['name']; ?><BR>
  <strong>Start:</strong> <?php echo $this->Time->niceShort($modelInformation['start_date']); ?><BR>
  <strong>End:</strong> <?php echo $this->Time->niceShort($modelInformation['end_date']); ?><BR>
  <?php endif;?>
  <!-- EOF model information-->
  <hr />
  <!-- Package information -->
  <?php if (!empty($signupDetails['Packagedetails']) && !empty($signupDetails['Package'])):?>
  	<h3>Package information</h3>
  	<strong> Name:</strong> <?php echo $signupDetails['Package']['name']; ?><BR>
	  <?php if (!empty($signupDetails['Package']['people_in_room'])):?>
	  	<strong>People in room:</strong> <?php echo $signupDetails['Package']['people_in_room']; ?><BR>
	  <?php endif;?>
	    <?php if (!$isFreeSignup && ($signupDetails['Signup']['status']=='paid' || $signupDetails['Signup']['status']=='partly paid') && ($canChangePackage || $canUpgradePackage)):?>
			<br/>  
	    	<?php if ($canUpgradePackage):?>
	    			<a href="#" onclick='return upgrade_package_link(<?php echo $signupDetails['Signup']['id'];?>);'><img src="<?php echo STATIC_BPONG;?>/img/upgrade_package.gif" alt="Upgrade package" title="Upgrade package"/></a>
			    	<a href="#" onclick='return upgrade_package_link(<?php echo $signupDetails['Signup']['id'];?>);'>Upgrade package</a>
					&nbsp;&nbsp;
			  <?php endif;?>
			  <?php if ($canChangePackage):?>
			  	    <a href="#" onclick='return change_package_link(<?php echo $signupDetails['Signup']['id'];?>);'><img src="<?php echo STATIC_BPONG;?>/img/change_package.gif" alt="Change package" title="Change package"/></a>
			    	<a href="#" onclick='return change_package_link(<?php echo $signupDetails['Signup']['id'];?>);'>Change package</a>
			  		<?php endif;?>
  		<?php endif;?>
  <?php endif;?>
  <div id='change_package_div' style='width:100%;'></div>
  <div class='clear'><br/></div>
  <hr/>
  <h3>Accepted users</h3>
  <!-- EOF package information -->
  <?php foreach ($signupUsers as $signupUser):?>
  	<a href="/u/<?php echo $signupUser['User']['lgn'];?>"><?php echo $signupUser['User']['lgn'];?></a><br/>
  <?php endforeach;?>
</div>
<div class="clear"></div>
<div class="line_space">&nbsp;</div>
<h2>How Do I..</h2>
<h3 class="new">Complete payment</h3>
<p>
  <?php if (!empty($signupDetails[$model]['signup_required'])):?>
  <em>Please note that all balances must be paid by <?php echo $this->Time->niceDate($signupDetails[$model]['finish_signup_date']);?>.</em>
  <?php endif;?>
<ol style="margin-left:25px">
  <li>Log into your BPONG account using the 'sign in' button at the top right corner of BPONG.COM.</li>
  <li>Point your mouse to 'My BPONG' (also in the top right corner), and choose 'Signups'.</li>
  <li>You will see your "<?php echo !empty(empty($signupDetails[$model]['shortname']) ) ? $signupDetails[$model]['name'] : $signupDetails[$model]['shortname'] );?>" signup listed. Click the blue circle with the letter 'I' in the middle (view signup details. You will then be able to pay the remaining balance of your signup.</li>
</ol>
</p>
<div class="line_space">&nbsp;</div>
<h3 class="new">Use a promo code</h3>
<p>This is for people that have won Satellite tournaments and were issued a promocde. There are two possibilities:<br />
  <strong>1. If you signed up for "<?php echo !empty(empty($signupDetails[$model]['shortname']) , $signupDetails[$model]['name'], $signupDetails[$model]['shortname'] );?>" before winning your Satellite Tournament, you can get a refund of your payment<?php echo ife(empty($cheepestPackage['packagedetails']['price']) ) ? ''  : '  (up to $'.$cheepestPackage['packagedetails']['price'].')';?>.</strong>
<ol style="margin-left:25px">
  <li>Log into your BPONG account using the 'sign in' button at the top right corner of BPONG.COM.</li>
  <li>Point your mouse to 'My BPONG' (also in the top right corner), and choose 'Signups'.</li>
  <li>You will see your  "<?php echo !empty(empty($signupDetails[$model]['shortname']) ) ? $signupDetails[$model]['name'] : $signupDetails[$model]['shortname'] ;?>" signup listed. Select it.</li>
  <li>Click the red circle with the green checkmark on it.  You will then be able to enter your promocode.</li>
  <li>Once you have entered your promocode, we will manually credit your account for the value of the base  "<?php echo !empty(empty($signupDetails[$model]['shortname']) , $signupDetails[$model]['name'], $signupDetails[$model]['shortname'] );?>" package <?php echo ife(empty($cheepestPackage['packagedetails']['price']) ) ? ''  : ' (generally $'.$cheepestPackage['packagedetails']['price'].')';?> and refund your credit card.</li>
</ol>
<strong>2. You have not signed up yet</strong>
<ol style="margin-left:25px">
  <li>Go to <a href="/wsobp">http://www.bpong.com/wsobp</a> and complete the signup process.</li>
  <li>When it asks you for a promocode, enter the code you were given. This will give you a <?php echo !empty(empty($cheepestPackage['packagedetails']['price']) ) ? ''  : '$'.$cheepestPackage['packagedetails']['price']);?> discount for the tournament (which will make the base package free.</li>
</ol>
</p>
<div class="line_space">&nbsp;</div>
<h3 class="new">Create a Team/Assign Team to "<?php echo !empty(empty($signupDetails[$model]['shortname']) ) ? $signupDetails[$model]['name'] : $signupDetails[$model]['shortname'] ;?>"</h3>
<p>Once you and your partner have both signed up, you will need to link up as a team. One player will need to create a team, and then 'invite' the other player. <br />
  To create a team: <br />
<ol style="margin-left:25px">
  <li> Log into your BPONG account using the 'sign in' button at the top right corner of BPONG.COM.</li>
  <li>Point your mouse to 'My BPONG' (also in the top right corner), and choose 'Teams'.</li>
  <li>Select 'New Team'.</li>
  <li>You can upload a team image, choose a team name, and enter a description for your team.</li>
  <li>Make sure that 'people in team equals 2.</li>
  <li>When you click 'submit', it will give you the option of inviting another member to the team. You can search by username or by email. Once you invite a player, they will receive an email instructing them on how to accept.</li>
</ol>
Once  the team has been created, it can be assigned to "<?php echo !empty(empty($signupDetails[$model]['shortname']) , $signupDetails[$model]['name'], $signupDetails[$model]['shortname'] );?>". For this to happen, the team must have two confirmed players (i.e. the second player must have already accepted the invitation), and both players must have already signed up for the  "<?php echo ife(empty($signupDetails[$model]['shortname']) ) ? $signupDetails[$model]['name'] : $signupDetails[$model]['shortname'] ;?>". <br />
To assign your team to "<?php echo !empty(empty($signupDetails[$model]['shortname']) ) ? $signupDetails[$model]['name'] : $signupDetails[$model]['shortname'] );?>" (only one player has to do this:
<ol style="margin-left:25px">
  <li>Log into your BPONG account using the 'sign in' button at the top right corner of BPONG.COM.</li>
  <li>Click on 'Teams' under 'My BPONG'</li>
  <li>Click on the 'Team Assignments' icon next to the team name (it's the BPONG cup with a green 'plus' sign).</li>
  <li>Assign the team to "<?php echo !empty(empty($signupDetails[$model]['shortname']) ) ? $signupDetails[$model]['name'] : $signupDetails[$model]['shortname'] ;?>"</li>
</ol>
</p>
<?php if ($signupDetails[$model]['is_room']>0  &&  !empty($signupDetails['Package']) && $signupDetails['Package']['is_hidden'] == 0):?>
<div class="line_space">&nbsp;</div>
<h3 class="new">Create a Room/ Select Rooming preferences </h3>
<p> Once you and your partner have signed up (or once you have signed up if you are getting your own room), you will need to create a 'room'.
<ol style="margin-left:25px">
  <li>Log into your BPONG account using the 'sign in' button at the top right corner of BPONG.COM.</li>
  <li>Point your mouse to 'MY BPONG' and choose 'Signups'.</li>
  <li>You will see your  "<?php echo !empty(empty($signupDetails[$model]['shortname']) ) ? $signupDetails[$model]['name'] : $signupDetails[$model]['shortname'] ;?>" signup listed.</li>
  <li>Click the blue circle with the letter 'I' in the middle ('view signup details').</li>
  <li>Click on the blue button that says 'Create room'.</li>
  <li>You will then have the opportunity to invite your teammate to the room (if you went with double occupancy), and you'll also be able to choose smoking/non-smoking and 1 king bed vs 2 queens.</li>
  <li>If you invite your teammate to the room, he/she will receive an email from BPONG.COM with instructions on how to accept the invitation.</li>
</ol>
</p>
<?php endif;?>
