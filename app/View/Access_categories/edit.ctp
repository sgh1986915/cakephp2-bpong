<h2><?php echo __('Edit Category');?></h2><?php echo $this->Form->create('AccessCategory');?>
	<fieldset>
 		
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
	?>
	</fieldset>
<?php echo $this->Form->end('Submit');?>