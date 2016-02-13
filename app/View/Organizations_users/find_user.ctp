<?php if (!empty($objects)):?>
	<table cellpadding="0" cellspacing="0" class='sub_list sub_list_full sorter' >
		<tr><th>Login</th><th>Name</th><th>Actions</th></tr>
	<?php 
	$i = 0;
	foreach ($objects as $object):
		$class = null;
	  	if ($i++ % 2 != 0) {
	    	$class = ' class="gray"';
	  	}
	?>
		<tr<?php echo $class;?>>
		<td><a href="/u/<?php echo $object['User']['lgn'];?>"><?php echo $object['User']['lgn'];?></a></td>
		<td><?php if ($object['User']['firstname']) {echo $this->Formater->userName($object['User'], 1);} ?></td>
		<td><a href="/organizations_users/invite/<?php echo $organizationID;?>/<?php echo $object['User']['id']?>">Invite</a></td>
		</tr>
		<?php endforeach;?>
	</table>
<?php else:?>
	There are no Users with such name or email.
<?php endif;?>