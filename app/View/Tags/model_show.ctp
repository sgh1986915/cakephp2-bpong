<?php $this->Paginator->options(array('url' => $this->passedArgs)); ?>
<h2>Tags of the category "<?php echo $model;?>"</h2>
<?php echo $this->Html->link('Add new Tag', array('action'=>'add', $model)); ?>
<br class='clear'/><br class='clear'/>
<table cellpadding="0" cellspacing="0">
<tr>
	<th>Tag Name</th>
	<th>Creator</th>
	<th>Action</th>
</tr>
<?php
$i = 0;
foreach ($tags as $tag):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $tag['Tag']['tag'];?>
		</td>
		<td>
			<a href="/users/view/<?php echo $tag['User']['lgn'] ?>"><?php echo $tag['User']['lgn'] ?></a>
		</td>
		<td class="actions">
			<?php echo $this->Html->link('Edit', array('action'=>'edit', $tag['Tag']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action'=>'delete', $tag['Tag']['id']), null, sprintf(__('Are you sure you want to delete "%s"?'), $tag['Tag']['tag'])); ?>
			
		</td>
	</tr>
	
<?php endforeach; ?>
</table>
<br class='clear'/>
<div class="paging">
	<?php echo $this->Paginator->first(3, array(), null, array('class'=>'disabled'));?>
	<?php echo $this->Paginator->numbers();?>
	<?php echo $this->Paginator->last(3, array(), null, array('class'=>'disabled'));?>
</div>


