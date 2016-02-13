<div class="venuetypes form">
<?php echo $this->Form->create('Venuetype');?>
	<fieldset>
 		<legend><?php echo __('Edit Venuetype');?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
	?>
	</fieldset>
<?php echo $this->Form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('Delete'), array('action'=>'delete', $this->Form->value('Venuetype.id')), null, sprintf(__('Are you sure you want to delete # %s?'), $this->Form->value('Venuetype.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Venuetypes'), array('action'=>'index'));?></li>
		<li><?php echo $this->Html->link(__('List Venues'), array('controller'=> 'venues', 'action'=>'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Venue'), array('controller'=> 'venues', 'action'=>'add')); ?> </li>
	</ul>
</div>
