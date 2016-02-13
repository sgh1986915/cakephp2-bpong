<?php if (!empty($teammates)):?>
<?php ?>
<table width="50%">
<tr>
	<th>Nick name</th>
	<th>First name</th>
	<th>Last name</th>
	<th>Action</th>
</tr>
<?php foreach ($teammates as $teammate):?>
	<tr>
		<td><?php echo $teammate['User']['lgn']; ?></td>
		<td><?php echo $teammate['User']['show_details'] == 1?$teammate['User']['firstname']:" -- "?></td>
		<td><?php echo $teammate['User']['show_details'] == 1?$teammate['User']['lastname']:" -- "?></td>
		<td>
		<?php if (!empty($byAjax)):?>
			<a href="#" onclick="return ajaxAssignTeammate(<?php echo $teamID; ?>, '<?php echo urlencode($teammate['User']['lgn']); ?>');">Invite</a>
		<?php else:?>
			<a href="/teammates/assign/<?php echo $teamID; ?>/<?php echo urlencode($teammate['User']['lgn']); ?>/0">Invite</a>
		<?php endif;?>
		</td>
	</tr>
<?php endforeach;?>
</table>
<?php else:?>
	Can not find user or user is already on the team.
<?php endif;?>