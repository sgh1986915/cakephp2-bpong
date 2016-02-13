<!-- CREATE ROOM BLOCK -->
<?php if ($showCreateRoomBlock):?>
	<div style='float:left; margin-right:20px;width:30%;'>
		<?php if (empty($rooms) && $signupDetails['Signup']['for_team'] && !$isteamAssigned):?>
			<p class="you_have_no2"><br/>You have chosen to pay for you and your teammate. Please add your team before creating a room.<br/><br/></p>
		<?php else:?>
		<?php if (!empty($questions)):?>
		<h3 class='new bottom_border'>Room Preferences</h3>
		<?php endif;?>
		<?php echo $this->element('/signup/create_room_block');?>
	<?php endif;?>	
	</div>
<?php endif;?>
<!-- EOF CREATE ROOM BLOCK -->

<div style='float:left'>
	<!-- ROOM INFO BLOCK -->
	<?php if (!empty($rooms)):?>
		<h3 class='new bottom_border'>Room Information</h3>
		<?php
		$roomIndex = 0;	 
		foreach ($rooms as $room):
			$roomIndex++;	
		?>	<span style='display:none'><?php echo $room['id'];?></span>
			<?php if (count($rooms) > 1):?><h3>Room <?php echo $roomIndex;?></h3><?php endif;?>	
			<strong>People in room:</strong> <?php echo $room['people_in_room']; ?><br />
			<strong>Status of a Room:</strong> <?php echo $room['status']; ?><br />
			<?php if (!empty($room['answers'])):?>
			<strong>Answers:</strong>
			<?php
					foreach( $room['answers'] as $index => $answer) {	
						echo $answer['Options']['optiontext'];
						if ( $index != count($room['answers']) -1 ) {
							echo ", ";
						}
					}
				endif;
			?>
			<?php 
			if(!empty($room['roommates'])): ?>
			<br/>
			<strong>Roommates:</strong>
				<?php 
				$matesIndex = 0;
				foreach ($room['roommates'] as $index => $roommate): 
				$matesIndex++;
				?><b><?php echo $matesIndex;?>.</b> 
					<?php echo $roommate['roommate']['status'];?> - 		
					<a href="/u/<?php echo $roommate['user']['lgn']; ?>"><?php echo $roommate['user']['lgn']; ?></a> (<?php echo $roommate['user']['email']; ?>)
				<?php endforeach; ?>
			<?php endif; ?>
			<?php if (count($room['roommates']) <2 && !$signupDetails['Signup']['for_team']):?>
				<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/del_room.gif" alt="Delete room" title="Delete room"/>', array('controller' => 'rooms', 'action' => 'delete', $room['id'], $signupId ), array('escape'=>false));?>	
			<?php endif;?>
			<?php endforeach;?>
	<?php endif;?>
	<!-- EOF ROOM INFO BLOCK -->
	<?php if ($waitingForTemmatesRoom):?>
		<p class="you_have_no2"><br/>Waiting for your teammate to finish his signup.<br/><br/></p>
	<?php endif;?>
	<?php if ($showFindInviters):?>
	<?php echo $this->element('/signup/find_inviters');?>
	<?php endif;?>
	
	 <?php echo $this->requestAction("/rooms/iWasInvited", array('signupID' => $signupDetails['Signup']['id'], 'return' => true ));
     echo $this->requestAction("/rooms/usersWereInvited", array('signupID' => $signupDetails['Signup']['id'], 'return' => true ));?>
</div>