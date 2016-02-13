<div class="groups form">
<?php echo $this->Form->create('Group');?>
	<fieldset>
 	   <legend>Group name</legend>
	       <?php echo $this->Form->input('Group.name'); ?>
	   <legend>Default status  name</legend>
	       <?php echo $this->Form->input('Status.name'); ?>
	   <legend>Copy Group/status acces</legend>
	       <?php echo $this->Form->input('Status.statuses'); ?>    
	</fieldset>
<?php echo $this->Form->end('Submit');?>
</div>
