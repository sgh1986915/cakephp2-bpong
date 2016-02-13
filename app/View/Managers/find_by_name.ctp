<table width="50%">
<tr>
	<th>Email</th>
	<th>Nick name</th>
	<th>First name</th>
	<th>Last name</th>
	<th></th>
</tr>
	<tr>
		<td><?php echo $managers['User']['email']; ?></td>
		<td><?php echo $managers['User']['lgn']; ?></td>
		<td><?php echo $managers['User']['firstname']; ?></td>
		<td><?php echo $managers['User']['lastname']; ?></td>
		<td><a href="/managers/assignManager/<?php echo $assignmodel; ?>/<?php echo urlencode($managers['User']['email']); ?>/<?php echo $modelID; ?>">Assign</a></td>
	</tr>

</table>