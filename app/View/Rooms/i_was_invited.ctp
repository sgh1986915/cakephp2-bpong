			<?php
				if ( isset( $roommateRequestedMe ) && !empty ( $roommateRequestedMe ) ):
			?>
		<h4>Users who have invited you:</h4>
        <div id="RoommatesWhoInviteMe">
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
					<td><?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/accept.gif" alt="Accept Roommate" title="Accept Roommate"/>', array('controller'=>'rooms', 'action'=> 'acceptRoommate', $signupID, $mate['SignupRoommate']['id'] ), array('escape'=>false)); ?>

<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/decll_rommate.gif" alt="Decline Roommate" title="Decline Roommate"/>',array('controller'=>'rooms', 'action'=> 'declineRoommate', $signupID, $mate['SignupRoommate']['id'] ), array('escape'=>false)); ?>

					</td>
				</tr>
			<?php
				endforeach;
			?>
			</table>
		</div>
<?php
	endif;
?>
