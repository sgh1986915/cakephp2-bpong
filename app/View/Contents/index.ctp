<div class="contents index">
<h2>Contents</h2>
<p>
<?php
echo $this->Paginator->counter(array(
'format' =>'Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%'
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('token');?></th>
	<th><?php echo $this->Paginator->sort('language_id');?></th>
	<th><?php echo $this->Paginator->sort('title');?></th>
	<th><?php echo $this->Paginator->sort('content');?></th>
	<th class="actions">Actions</th>
</tr>
<?php
$i = 0;
foreach ($contents as $content):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
	
		<td>
			<?php echo $content['Content']['token']; ?>
		</td>
		<td>
			<?php echo $content['Language']['code']; ?>
		</td>
		<td>
			<?php echo $content['Content']['title']; ?>
		</td>
		<td>
			<?php echo substr(strip_tags($content['Content']['content']),0,150); ?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link('Edit', array('action'=>'edit', $content['Content']['id'])); ?>
			<?php echo $this->Html->link('Delete', array('action'=>'delete', $content['Content']['id']), null, sprintf('Are you sure you want to delete # %s?', $content['Content']['id'])); ?>
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