<?php if (!empty($objects)):?>
	<table cellpadding="0" cellspacing="0" class='sub_list sub_list_full sorter' >
		<tr><th>Name</th><th>City</th><th>State</th><th>Actions</th></tr>
	<?php 
	$i = 0;
	foreach ($objects as $object):
		$class = null;
	  	if ($i++ % 2 != 0) {
	    	$class = ' class="gray"';
	  	}
	?>
		<tr<?php echo $class;?>>
		<td><a href="/venues/view/<?php echo $object['Venue']['slug'];?>"><?php echo $object['Venue']['name'];?></a></td>
		<td><?php echo $object['Address']['city'];?></td>
		<td><?php echo $object['Address']['Provincestate']['name'];?></td>
		<td><a href="/organizations_objects/add_venue/<?php echo $organizationID;?>/<?php echo $object['Venue']['id']?>">Add</a></td>
		
		</tr>
		<?php endforeach;?>
	</table>
<?php else:?>
	There are no Venues with such name.
<?php endif;?>