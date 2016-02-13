<div class="eventfeatures index">
<h2>Eventfeatures</h2>

<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('name');?></th>
	<th><?php echo $this->Paginator->sort('private');?></th>
	<th class="actions">Actions</th>
</tr>
<?php
$i = 0;
foreach ($eventfeatures as $eventfeature):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $eventfeature['Eventfeature']['name']; ?>
		</td>
		<td>
			<?php echo empty($eventfeature['Eventfeature']['private'])?"No":"Yes"; ?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/edit.gif" alt="Edit" />', array('action'=>'edit', $eventfeature['Eventfeature']['id']), array('escape'=>false)); ?>
			<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/delete_.gif" alt="Delete" />', array('action'=>'delete', $eventfeature['Eventfeature']['id']), array('escape'=>false), sprintf('Are you sure you want to delete # %s?', $eventfeature['Eventfeature']['id'])); ?>
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
<div class="actions">
	<ul>
		<li><span class="addbtn"><?php echo $this->Html->link('New Eventfeature', array('action'=>'add'), array('class'=>'addbtn')); ?></span></li>
	</ul>
</div>
