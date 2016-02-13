<div class="settings index">
<h2><?php echo __('Settings');?></h2>
<p>
<?php
echo $this->Paginator->counter(array(
'format' => 'Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%'
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('id');?></th>
	<th><?php echo $this->Paginator->sort('name');?></th>
	<th><?php echo $this->Paginator->sort('value');?></th>
	<th><?php echo $this->Paginator->sort('type');?></th>
	<th><?php echo $this->Paginator->sort('description');?></th>
	<th class="actions"><?php echo 'Actions';?></th>
</tr>
<?php
$i = 0;
foreach ($settings as $setting):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $setting['Setting']['id']; ?>
		</td>
		<td>
			<?php echo $setting['Setting']['name']; ?>
		</td>
		<td>
			<?php echo $setting['Setting']['value']; ?>
		</td>
		<td>
			<?php echo $setting['Setting']['type']; ?>
		</td>
		<td>
			<?php echo $setting['Setting']['description']; ?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link('Edit', array('action'=>'edit', $setting['Setting']['id'])); ?>
			<?php echo $this->Html->link('Delete', array('action'=>'delete', $setting['Setting']['id']), null, sprintf('Are you sure you want to delete # %s?', $setting['Setting']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $this->Paginator->prev('<< '.'previous', array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $this->Paginator->numbers();?>
	<?php echo $this->Paginator->next('next'.' >>', array(), null, array('class'=>'disabled'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link('New Setting', array('action'=>'add')); ?></li>
	</ul>
</div>
