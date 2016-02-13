<div class="venuetypes index">
<h2><?php echo __('Venuetypes');?></h2>
<p>
<?php
echo $this->Paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%')
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('id');?></th>
	<th><?php echo $this->Paginator->sort('name');?></th>
	<th class="actions"><?php echo __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($venuetypes as $venuetype):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $venuetype['Venuetype']['id']; ?>
		</td>
		<td>
			<?php echo $venuetype['Venuetype']['name']; ?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action'=>'view', $venuetype['Venuetype']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action'=>'edit', $venuetype['Venuetype']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action'=>'delete', $venuetype['Venuetype']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $venuetype['Venuetype']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Venuetype'), array('action'=>'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Venues'), array('controller'=> 'venues', 'action'=>'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Venue'), array('controller'=> 'venues', 'action'=>'add')); ?> </li>
	</ul>
</div>
