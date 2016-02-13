<?php echo $this->Form->create('AccessCategory');?>
 		<h2><?php echo __('Add Category');?></h2>
	<fieldset>
	<?php		
		echo $this->Form->input('name');
	?>
	</fieldset>
<?php echo $this->Form->end('Submit');?>
