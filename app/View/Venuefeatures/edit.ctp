<div class="venuefeatures form">
<?php echo $this->Form->create('Venuefeature');?>
	<fieldset>
 		<legend><?php echo __('Edit Venuefeature');?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('Venue');
	?>
	</fieldset>
<?php echo $this->Form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('Delete'), array('action'=>'delete', $this->Form->value('Venuefeature.id')), null, sprintf(__('Are you sure you want to delete # %s?'), $this->Form->value('Venuefeature.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Venuefeatures'), array('action'=>'index'));?></li>
		<li><?php echo $this->Html->link(__('List Venues'), array('controller'=> 'venues', 'action'=>'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Venue'), array('controller'=> 'venues', 'action'=>'add')); ?> </li>
	</ul>
</div>
