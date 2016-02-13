<h4>Invite user to share room:</h4>
<div>
	<form id="matesform" action="" method="post">
		<fieldset style="background:none">
			<?php echo $this->Form->input("User.email", array( 'label' => 'Email')); ?>
			<?php echo $this->Form->input("User.lgn", array( 'label' => 'Nickname')); ?>
			<?php echo $this->Form->input("User.lastname", array( 'label' => 'Lastname')); ?>
			<input type="submit" class="sbmt_ie" id="findMate" value="Find"/> <img id='find_loader' style='display:none;' src="<?php echo STATIC_BPONG; ?>/img/ajax_loader_m.gif" border="0">

</fieldset>
	</form>
	<span id="ErrEmail" style="display: none;">Check your email</span>
</div>
<div id="RoommatesToInvite">
</div>



