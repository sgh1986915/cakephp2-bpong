<?php if (!empty($objects)):?>
	<table cellpadding="0" cellspacing="0" class='sub_list sub_list_full sorter' >
		<tr><th>Name</th><th>Start Date</th><th>End Date</th><th>Actions</th></tr>
	<?php 
	$i = 0;
	foreach ($objects as $object):
		$class = null;
	  	if ($i++ % 2 != 0) {
	    	$class = ' class="gray"';
	  	}
	?>
		<tr<?php echo $class;?>>
		<td><a href="/event/<?php echo $object['Event']['id'];?>/<?php echo $object['Event']['slug'];?>"><?php echo $object['Event']['name'];?></a></td>
		<td align="center"><?php echo empty($object['Event']['start_date'])?"--":$this->Time->niceShort($object['Event']['start_date']); ?></td>
		<td align="center"><?php if (empty($object['Event']['end_date'])) {echo "--";} else {echo (substr($object['Event']['end_date'],0,10) == '0000-00-00')?"Not Defined":$this->Time->niceShort($object['Event']['end_date']);} ?></td>  		
		<td><a href="/organizations_objects/add_event/<?php echo $organizationID;?>/<?php echo $object['Event']['id']?>">Add</a></td>
		
		</tr>
		<?php endforeach;?>
	</table>
<?php else:?>
	There are no Events with such name.
<?php endif;?>