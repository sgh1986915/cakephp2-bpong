<?php
	echo $this->element('mce_init', array('name' => 'KnowledgeAnswerAnswer'))
?>
<div class="KnowledgeAnswer form">
<?php echo $this->Form->create('KnowledgeAnswer');?>
	<fieldset>
 		<legend><?php echo __('Add new answer');?></legend>
	<?php
		echo $this->Form->hidden('question_id');
		echo $this->Form->input('answer');
	?>
	<div class="box_check">
		  <label>Is hidden</label><div class="check_"><?php echo $this->Form->checkbox('is_hidden', array ('label'=>false) );?></div>
    </div>
	</fieldset>
<?php echo $this->Form->end('Submit');?>
</div>