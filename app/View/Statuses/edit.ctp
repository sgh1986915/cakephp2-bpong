<div class="statuses form">
<?php echo $this->Form->create('Status');?>
	<fieldset>
 		<legend><?php echo __('Edit Status');?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('group_id');
		echo $this->Form->input('name');
	?>
	</fieldset>
<?php echo $this->Form->end('Submit');?>
</div>