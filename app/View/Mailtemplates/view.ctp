<div class="mailtemplates view">
<h2><?php  echo 'Mailtemplate';?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo 'Id'; ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mailtemplate['Mailtemplate']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo 'Language Id'; ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mailtemplate['Mailtemplate']['language_id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo 'Code'; ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mailtemplate['Mailtemplate']['code']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo 'Name'; ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mailtemplate['Mailtemplate']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo 'Subject'; ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mailtemplate['Mailtemplate']['subject']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo 'Body'; ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mailtemplate['Mailtemplate']['body']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link('Edit Mailtemplate', array('action'=>'edit', $mailtemplate['Mailtemplate']['id'])); ?> </li>
		<li><?php echo $this->Html->link('Delete Mailtemplate', array('action'=>'delete', $mailtemplate['Mailtemplate']['id']), null, sprintf('Are you sure you want to delete # %s?', $mailtemplate['Mailtemplate']['id'])); ?> </li>
		<li><?php echo $this->Html->link('List Mailtemplates', array('action'=>'index')); ?> </li>
		<li><?php echo $this->Html->link('New Mailtemplate', array('action'=>'add')); ?> </li>
	</ul>
</div>
