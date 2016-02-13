<h3 class='new'>Room information</h3>

<?php if (empty($rooms)):?>
	<p class="you_have_no2"><br/>You have not created a room.<br/><br/></p>
	<br/>
	<span class="addbtn">
			<?php
				echo $this->Html->link('Create room', array(	  'controller' => 'rooms'
														, 'action' => 'createRoom'
														, $signupDetails['Signup']['id'], 'teammate' )
												, array ('class'=>'addbtn3') );
			?>
	</span>
<?php else:?>
<!-- ROOM INFO BLOCK -->
<?php if (!empty($rooms)):?>
	
	<?php
	$roomIndex = 0;	 
	foreach ($rooms as $room):
		$roomIndex++;	
	?>
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
		<strong>Mates:</strong>
			<?php 
			$matesIndex = 0;
			foreach ($room['roommates'] as $index => $roommate): 
			$matesIndex++;
			?><b><?php echo $matesIndex;?>.</b> 
				  Login - <?php echo $roommate['user']['lgn']; ?>, email - <?php echo $roommate['user']['email']; ?>
				<?php if ($roommate['roommate']['status'] == 'Creator'):?>
				(Creator)
				<?php endif;?>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php if (count($room['roommates']) <2 && !$signupDetails['Signup']['for_team']):?>
			<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/del_room.gif" alt="Delete room" title="Delete room"/>', array('controller' => 'rooms', 'action' => 'delete', $room['id'], $signupId ), array('escape'=>false));?>	
		<?php endif;?>
		<?php endforeach;?>
<?php endif;?>
<!-- EOF ROOM INFO BLOCK -->
<?php endif;?>