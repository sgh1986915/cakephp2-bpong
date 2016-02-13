<?php
	echo $this->element('mce_init', array('name' => 'MailtemplateBody'))
?>

<div class="mailtemplates form">
<?php echo $this->Form->create('Mailtemplate');?>
	<fieldset>
 		<legend><?php echo 'Edit Mailtemplate';?></legend>
	<?php
		
		echo $this->Form->input('name');
		echo $this->Form->input('from');
	?>
	<div class="input text">
						<label>Keywords:</label>
						<div class="left"><?php echo $this->request->data['Mailtemplate']['keywords'];?></div>
	</div>	
	<?php echo $this->Form->input('subject',array('style'=>'width:600px'));
		echo $this->Form->input('body');
		echo $this->Form->input('bcc',array('style'=>'width:600px'));?>
	<div class="input"><label for="MailtemplateComments">Comments</label>
	<?
		echo $this->Form->textarea('comments',array('legend'=>'comments'));
	?>
	</div>
	</fieldset>
<?php echo $this->Form->end('Submit');?>
</div>