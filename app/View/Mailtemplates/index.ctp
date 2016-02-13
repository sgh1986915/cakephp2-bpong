<div class="mailtemplates index">
<h2><?php echo __('Mailtemplates');?></h2>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('code');?></th>
	<th><?php echo $this->Paginator->sort('name');?></th>
	<th><?php echo $this->Paginator->sort('subject');?></th>
	<th class="actions"><?php echo 'Actions';?></th>
</tr>
<?php
$i = 0;
foreach ($mailtemplates as $mailtemplate):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $mailtemplate['Mailtemplate']['code']; ?>
		</td>
		<td>
			<?php echo $mailtemplate['Mailtemplate']['name']; ?>
		</td>
		<td>
			<?php echo $mailtemplate['Mailtemplate']['subject']; ?>
		</td>
		<td class="actions">
			<?php //echo $this->Html->link('View', array('action'=>'view', $mailtemplate['Mailtemplate']['id'])); ?>
			<?php echo $this->Html->link('Edit', array('action'=>'edit', $mailtemplate['Mailtemplate']['id'])); ?>
			<?php echo $this->Html->link('Delete', array('action'=>'delete', $mailtemplate['Mailtemplate']['id']), null, sprintf('Are you sure you want to delete # %s?', $mailtemplate['Mailtemplate']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $this->Paginator->first(3, array(), null, array('class'=>'disabled'));?>
	<?php echo $this->Paginator->numbers();?>
	<?php echo $this->Paginator->last(3, array(), null, array('class'=>'disabled'));?>
	<?php echo $this->element('pagination'); ?>
</div>
<!--
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link('New Mailtemplate', array('action'=>'add')); ?></li>
	</ul>
</div>
-->