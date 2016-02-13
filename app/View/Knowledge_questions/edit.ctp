<?php
	echo $this->element('mce_init', array('name' => 'KnowledgeQuestionQuestion'))
?>
<div class="knowledgeQuestions form">
<?php echo $this->Form->create('KnowledgeQuestion');?>
	<fieldset>
 		<legend><?php echo __('Edit question');?></legend>
	<?php
	    echo $this->Form->hidden('id');
	    echo $this->Form->hidden('topic_id');
		echo $this->Form->input('ord',array('style'=>'width:20px;'));
		echo $this->Form->input('question');
	?>
	<div class="box_check">
		  <label>Is hidden</label><div class="check_"><?php echo $this->Form->checkbox('is_hidden', array ('label'=>false) );?></div>
    </div>
	</fieldset>
<?php echo $this->Form->end('Submit');?>
</div>