<hr />
<!-- Room Information -->
<h3>Room:</h3>
<?php //print_r($signupDetails); ?>
<?php if ( $signupDetails['Signup']['status'] == 'paid' ):?>
	<?php if ( !empty( $signupRoom ) ):?>
		<?php if ( $signupRoom['SignupRoom']['status'] != 'Deleted' ):?>
<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/del_room.gif" alt="Delete room" title="Delete room"/>', array('controller' => 'rooms', 'action' => 'delete', $signupDetails['Signup']['id'] ), array('escape' => false));?>
			<hr />
			<strong>People in room:</strong> <?php echo $signupRoom['SignupRoom']['people_in_room']; ?><br />
			<strong>Status of a Room:</strong> <?php echo $signupRoom['SignupRoom']['status']; ?><br />
			<hr />
			<strong>Answers:</strong>
			<?php
				foreach( $answers as $index => $answer) {
					echo $answer['Options']['optiontext'];
					if ( $index != count($answers)-1 ) {
						echo ", ";
					}
				}
			?>
		<hr />

		<!-- Roommate's Information -->
		<h3>Roommate(s):</h3>
        <h4>Invite user to share room:</h4>
		<div>
        <fieldset>
			<?php echo $this->Form->input("User.email", array('div' => false)); ?>
			<input type="button" class="sbmt_ie" id="findMate" value="Find" onclick="findMate();"/>
		</fieldset>
			<span id="ErrEmail" style="display: none;">Check your email</span>
        </div>
		<div id="RoommatesToInvite">
		</div>
		<h4>Users you have invited:</h4>
		<div id="InvitedRoommates">
			<?php
				if ( isset( $mates ) && !empty ( $mates ) ):
			?>
			<table>
				<tr>
					<th>Lgn</th>
					<th>email</th>
					<th>First</th>
					<th>Last</th>
					<th>Status</th>
				</tr>
			<?php
				foreach ($mates as $mate):
			?>
				<tr>
					<td><?php echo $mate['User']['lgn']; ?></td>
					<td><?php echo $mate['User']['email']; ?></td>
					<td><?php echo $mate['User']['firstname']; ?></td>
					<td><?php echo $mate['User']['lastname']; ?></td>
					<td><?php echo $mate['SignupRoommate']['status']; ?></td>
				</tr>
			<?php
				endforeach;
			?>
			</table>
			<?php
				endif;
			?>
		</div>
		<h4>Users who have invited you:</h4>
		<div id="RoommatesWhoInviteMe">
			<?php
				if ( isset( $roommateRequestedMe ) && !empty ( $roommateRequestedMe ) ):
			?>
			<table>
				<tr>
					<th>Lgn</th>
					<th>email</th>
					<th>First</th>
					<th>Last</th>
					<th>&nbsp;</th>
				</tr>
			<?php
				foreach ( $roommateRequestedMe as $mate ):
			?>
				<tr>
					<td><?php echo $mate['Creator']['lgn']; ?></td>
					<td><?php echo $mate['Creator']['email']; ?></td>
					<td><?php echo $mate['Creator']['firstname']; ?></td>
					<td><?php echo $mate['Creator']['lastname']; ?></td>
					<td>
<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/accept.gif" alt="Accept Roommate" title="Accept Roommate"/>', array('controller'=>'rooms', 'action'=> 'acceptRoommate', $signupDetails['Signup']['id'], $mate['SignupRoommate']['id'] ), array('escape'=>false)); ?>

<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/decll_rommate.gif" alt="Decline Roommate" title="Decline Roommate"/>',array('controller'=>'rooms', 'action'=> 'declineRoommate', $signupDetails['Signup']['id'], $mate['SignupRoommate']['id'] ), array('escape'=>false)); ?>
					</td>
				</tr>
			<?php
				endforeach;
			?>
			</table>
			<?php
				else:
			?>
			<p class="you_have_no2">You have not to be invited yet</p>
			<?php
				endif;
			?>

		</div>
		<!-- End of Roommates Information -->
		<?php else:
		//Room is deleted because user accept somebodies inviting
		?>
			<?php if (isset($room_neighbors) && !empty($room_neighbors)) { ?>
				<br />Creator:
				<?php foreach ($room_neighbors as $index => $value) {
						if ($value['SignupRoommate']['status'] == 'Creator' ) {
				?>
							Login - <?php echo $value['User']['lgn']; ?>
							 ,email - <?php echo $value['User']['email']; ?>
					<?php }?>
				<?php }?>
				<br />Mates:
				<?php foreach ($room_neighbors as $index => $value) {
						if ($value['SignupRoommate']['status'] != 'Creator' ) {
				?>
							Login - <?php echo $value['User']['lgn']; ?>
							 ,email - <?php echo $value['User']['email']; ?>
					<?php }?>
				<?php }?>
			<?php } ?>
		<?php endif; ?>

	<?php else: ?>
		<p class="you_have_no2">You have not create a room</p>
		<br />
<span class="addbtn"><?php
			echo $this->Html->link('Create room', array(  'controller' => 'rooms'
													, 'action' => 'createRoom'
													, $signupDetails['Signup']['id'] )
											, array ('class'=>'addbtn3') );
		?></span>
        <br />
		<h4>Users who have invited you:</h4>
		<div id="RoommatesWhoInviteMe">
			<?php
				if ( isset( $roommateRequestedMe ) && !empty ( $roommateRequestedMe ) ):
			?>
			<table>
				<tr>
					<th>Lgn</th>
					<th>email</th>
					<th>First</th>
					<th>Last</th>
					<th>&nbsp;</th>
				</tr>
			<?php
				foreach ( $roommateRequestedMe as $mate ):
			?>
				<tr>
					<td><?php echo $mate['Creator']['lgn']; ?></td>
					<td><?php echo $mate['Creator']['email']; ?></td>
					<td><?php echo $mate['Creator']['firstname']; ?></td>
					<td><?php echo $mate['Creator']['lastname']; ?></td>
					<td><?php echo $this->Html->link('Accept', array(	  'controller' 	=> 'rooms'
																, 'action' 		=> 'acceptRoommate'
																, $signupDetails['Signup']['id']
																, $mate['SignupRoommate']['id'] )); ?>
						<?php echo $this->Html->link('Decline',array(	  'controller' 	=> 'rooms'
																, 'action' 		=> 'declineRoommate'
																, $signupDetails['Signup']['id']
																, $mate['SignupRoommate']['id'] )); ?>
					</td>
				</tr>
			<?php
				endforeach;
			?>
			</table>
			<?php
				else:
			?>
			<p class="you_have_no2">You have not to be invited yet</p>
			<?php
				endif;
			?>

		</div>
	<!-- End of Room Information -->
	<?php endif; ?>

<?php else: ?>
<p class="you_have_no2">Please pay a full price to create room</p>
<?php endif; ?>