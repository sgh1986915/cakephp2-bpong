<?php 
	$allTypes = Configure::read('Event.Relationship.Types');
?>
<h3>Relationships list</h3>
<?php if (empty($events)):?>
<div style='color:red;'>No parents<br/><br/></div>
<?php else:?>
<table width="50%">
<tr>
	<th>ID</th>
	<th>Name</th>
	<th>Relationship Type</th>
	<th></th>
</tr>
	<?php foreach ($events as $event): ?>
	<tr>
		<td><?php echo $event['Parent']['id']; ?></td>
		<td><a href="/events/view/<?php echo $event['Parent']['slug']; ?>"><?php echo $event['Parent']['name']; ?></a></td>
		<td><?php echo $allTypes[$event['EventsEvent']['relationship_type']]; ?></td>
		<td><a href="#" onclick = "return deleteRelation(<?php echo $event['EventsEvent']['id']; ?>);">Delete Relationship</td>
	
	</tr>
	<?php endforeach;?>

</table>
<?php endif;?>