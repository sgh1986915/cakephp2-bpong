<table width="50%">
<tr>
	<th>Email</th>
	<th>Nick name</th>
	<th>First name</th>
	<th>Last name</th>
	<th></th>
</tr>
	<tr>
		<td><?php echo $members['User']['email']; ?></td>
		<td><?php echo $members['User']['lgn']; ?></td>
		<td><?php echo $members['User']['firstname']; ?></td>
		<td><?php echo $members['User']['lastname']; ?></td>
		<td><a href="#" onclick="assignUser('<?php echo $members['User']['id']; ?>', '<?php echo $members['User']['email']; ?>'); return false;">Assign</a></td>
	</tr>

</table>