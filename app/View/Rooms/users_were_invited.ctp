			<?php
				if ( isset( $myHistory ) && !empty ( $myHistory ) ):
			?>
		<h4>Users you have invited:</h4>
		<div id="InvitedRoommates">
			<table>
				<tr>
					<th>Lgn</th>
					<th>email</th>
					<th>First</th>
					<th>Last</th>
					<th>Status</th>
				</tr>
			<?php
				foreach ($myHistory as $mate):
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
		</div>
			<?php
				endif;
			?>
