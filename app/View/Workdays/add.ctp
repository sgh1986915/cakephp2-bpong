<div class="workdays form">
<?php echo $this->Form->create('Workday');?>
	<fieldset>
 		<legend><?php echo __('Add Workday');?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('Venue');
	?>
	</fieldset>
<?php echo $this->Form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('List Workdays'), array('action'=>'index'));?></li>
		<li><?php echo $this->Html->link(__('List Venues'), array('controller'=> 'venues', 'action'=>'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Venue'), array('controller'=> 'venues', 'action'=>'add')); ?> </li>
	</ul>
</div>
