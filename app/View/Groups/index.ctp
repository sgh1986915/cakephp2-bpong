<div class="groups index">
<h2><?php echo __('Groups');?></h2>
<p>
<?php
echo $this->Paginator->counter(array(
'format' => 'Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%'
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('name');?></th>
	<th><?php echo $this->Paginator->sort('defstats_id');?></th>
	<th class="actions">Actions</th>
</tr>
<?php
$i = 0;
foreach ($groups as $group):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $group['Group']['name']; ?>
		</td>
		<td>
			<?php echo $group['Status']['name']; ?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Edit'), array('action'=>'edit', $group['Group']['id'])); ?>
			<?php echo (VISITOR_GROUP!=$group['Group']['id'])?$this->Html->link(__('Delete'), array('action'=>'delete', $group['Group']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $group['Group']['id'])):""; ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $this->Paginator->prev('<< '.__('previous'), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $this->Paginator->numbers();?>
	<?php echo $this->Paginator->next(__('next').' >>', array(), null, array('class'=>'disabled'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link('New Group', array('action'=>'add')); ?></li>
	</ul>
</div>
