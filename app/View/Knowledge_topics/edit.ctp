<div class="knowledgeTopics form">
<?php echo $this->Form->create('KnowledgeTopic');?>
	<fieldset>
 		<legend><?php echo __('Edit KnowledgeTopic');?></legend>
	<?php
	    echo $this->Form->hidden('id');
		echo $this->Form->input('name');
	?>
	<div class="box_check">
		  <label>Is hidden</label><div class="check_"><?php echo $this->Form->checkbox('is_hidden', array ('label'=>false) );?></div>
    </div>
	
	</fieldset>
<?php echo $this->Form->end('Submit');?>
</div>