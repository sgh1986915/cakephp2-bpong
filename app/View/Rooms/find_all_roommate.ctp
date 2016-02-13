<h4>Who I want to invite to my room</h4>
<?php
	if ( isset( $users ) && !empty ( $users ) ):
?>
<table>
	<tr>
		<th>Lgn</th>
		<th>email</th>
		<th>Full name</th>
		<th>&nbsp </th>
	</tr>
	<?php foreach($users as $user) :?>
		<tr>
			<td><?php echo $user['User']['lgn']; ?></td>
			<td><?php echo $user['User']['email']; ?></td>
			<td><?php echo $user['User']['firstname'] . " " . $user['User']['lastname']; ?></td>
			<td>
				<?php if ( empty( $user['User']['checked_status'] ) ):?>
					<input 	type="button"
							class="sbmt_ie"
							value="Invite"
							name="invitefriend"
							id="invite<?php echo $user['User']['id'];?>"
							onclick="inviteFriend(<?php echo $user['User']['id'];?>);"/>
					<img src="<?php echo STATIC_BPONG?>/img/loading.gif" alt="loading..." id="loading<?php echo $user['User']['id'];?>" style="display:none;"/>
				<?php
					else:
						echo $user['User']['checked_status'];
				 	endif;
				?>
			</td>
		</tr>
	<?php endforeach;?>
</table>
<script language="JavaScript1.2" type="text/javascript">
	function inviteFriend( userid ) {
		$("#invite" + userid).hide();
		$("#loading" + userid).show();
		$.post(	  "/rooms/inviteMate"
				, {    signUpId: signUpId
					 , user_id: userid }
				, function(data) {
					if ( data != 0 && data != ""  ) {
						$("#RoommatesToInvite").html( data );
					} else {
						location.reload(true);
						window.location.href= "/signups/signupDetails/<?php echo $signupId;?>/tab-rooms/"
					}
					}
			);
	}
</script>
<?php
	else:
?>
<p class="you_have_no2"><?php echo $errorMessage; ?></p>
<?php
	endif;
?>
