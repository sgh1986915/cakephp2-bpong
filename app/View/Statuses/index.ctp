<div class="statuses index">
<h2>Statuses</h2>
<p>
<?php
echo $this->Paginator->counter(array(
'format' => 'Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%'
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('group_id');?></th>
	<th><?php echo $this->Paginator->sort('Status','name');?></th>
	<th class="actions">Default</th>
	<th class="actions">Actions</th>
</tr>
<?php
$i = 0;
foreach ($statuses as $status):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $this->Html->link($status['Group']['name'], array('controller'=> 'groups', 'action'=>'edit', $status['Group']['id'])); ?>
		</td>
		<td>
			<?php echo $status['Status']['name']; ?>
		</td>
		<td>
			<?php echo ($status['Status']['id'] === $status['Group']['defstats_id'])?"Yes":"" ?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Edit'), array('action'=>'edit', $status['Status']['id'])); ?>
			<?php if ($status['Status']['id'] !== $status['Group']['defstats_id']): echo $this->Html->link(__('Delete'), array('action'=>'delete', $status['Status']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $status['Status']['id'])); endif;?>
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
		<li><?php echo $this->Html->link('New Status', array('action'=>'add')); ?></li>
	</ul>
</div>
