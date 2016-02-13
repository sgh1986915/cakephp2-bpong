<div class="storeSlots index">
<h2>All Images Tagged With "<?php echo $tag;?>"</h2>

<table cellpadding="0" cellspacing="0">
<tr>
	<th></th>
	<th><?php echo $this->Paginator->sort('Title & Description', $modelName.'.name');?></th>
	<th><?php echo $this->Paginator->sort('Submitted By', 'User.lgn');?></th>
	<th><?php echo $this->Paginator->sort('Submitted', $modelName.'.created');?></th>
</tr>
<?php
$i = 0;
foreach ($objects as $object):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td width='80'>
		<a href="/Images/albumShow/<?php echo $object[$modelName]['id'];?>">
			<img src="<?php echo IMG_ALBUMS_URL;?>/small_<?php echo $object[$modelName]['filename'];?>"/>
		</a>
		</td>
		<td>
			<?php if ($object[$modelName]['name']):?><b><?php echo $object[$modelName]['name']; ?></b><br/><?php endif;?>
			<?php echo $object[$modelName]['description']; ?>
		</td>
		<td>
			<?php echo $this->Html->link($object[$modelName]['User']['lgn'], '/users/view/' . $object[$modelName]['User']['lgn']); ?>
		</td>
		<td>
			<?php echo $this->Time->niceShort($object[$modelName]['created']); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $this->Paginator->first(3, array(), null, array('class'=>'disabled'));?>
	<?php echo $this->Paginator->numbers();?>
	<?php echo $this->Paginator->last(3, array(), null, array('class'=>'disabled'));?>
</div>


