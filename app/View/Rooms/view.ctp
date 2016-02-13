<br />
		<h3>Answers:</h3>
			<?php
				foreach( $answers as $index => $answer) {
					echo $answer['Options']['optiontext'];
					if ( $index != count($answers)-1 ) {
						echo ", ";
					}
				}
			?>
		<!-- Roommate's Information -->
		<h3>Room history:</h3>
		<div id="InvitedRoommates">
			<table>
				<tr>
					<th>Lgn</th>
					<th>email</th>
					<th>First</th>
					<th>Last</th>
					<th>Status</th>
				</tr>
			<?php foreach ($rooms as $room):?>
				<tr>
					<td><?php echo $room['User']['lgn']; ?></td>
					<td><?php echo $room['User']['email']; ?></td>
					<td><?php echo $room['User']['firstname']; ?></td>
					<td><?php echo $room['User']['lastname']; ?></td>
					<td><?php echo $room['SignupRoommate']['status']; ?></td>
				</tr>
			<?php endforeach; ?>
			</table>
		</div>