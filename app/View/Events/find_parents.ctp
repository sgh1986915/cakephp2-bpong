<?php if (empty($events)):?>
<div style='color:red;'>Can't find such events.<br/><br/></div>
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
		<td><?php echo $event['Event']['id']; ?></td>
		<td><?php echo $this->Html->link($event['Event']['name'], '/event/' . $event['Event']['id'] . '/' . $event['Event']['slug']); ?></td>
		<td><?php echo $this->Form->input('Event.type', array('id' => 'relation_type_' . $event['Event']['id'], 'type' => 'select', 'label'=> false, 'options' => Configure::read('Event.Relationship.Types')));?></td>
		<td><a href="#" onclick = "return addRelation(<?php echo $event['Event']['id']; ?>);">Add Relationship</td>
	
	</tr>
	<?php endforeach;?>

</table>
<?php endif;?>