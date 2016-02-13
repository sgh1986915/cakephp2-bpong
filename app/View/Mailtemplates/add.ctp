<?php
	echo $this->element('mce_init', array('name' => 'MailtemplateBody'))
?>
<div class="mailtemplates form">
<?php echo $this->Form->create('Mailtemplate');?>
	<fieldset>
 		<legend><?php echo 'Add Mailtemplate';?></legend>
	<?php
		echo $this->Form->input('language_id');
		echo $this->Form->input('code');
		echo $this->Form->input('name');
		echo $this->Form->input('from');
		echo $this->Form->input('subject');
		echo $this->Form->input('body');
		echo $this->Form->input('bcc');
	?>
	<div class="input"><label for="MailtemplateComments">Comments</label>
	<?
		echo $this->Form->textarea('comments',array('legend'=>'comments'));
	?>
	</div>
	</fieldset>
<?php echo $this->Form->end('Submit');?>
</div>