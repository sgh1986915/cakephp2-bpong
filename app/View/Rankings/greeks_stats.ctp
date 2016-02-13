<?php $this->set('title_for_layout', 'The Beer Pong Greeks Stats');?>
<?if (empty($rankings)):?>
	<div class='no_results'>No results found</div>
<?php else: ?>
<?php echo $this->Paginator->options(array('url' => $this->passedArgs)); ?>
<table class='sub_list sorter' cellpadding="0" cellspacing="0">
    <tr>
    	<th><?php echo $this->Paginator->sort('Points', 'points', array('sorter' => true));?></th>
    	<th>Greek</th>
    	<th>Wins</th>
    	<th>Losses</th>
    	<th>CD</th>
    </tr>
	<?php
	$i = 0;
	foreach ($rankings as $ranking):
    	$class = '';
    	if ($i++ % 2 != 0) {
    		$class = ' class="alt"';
    	}

	?>
	<tr<?php echo $class;?>>
	<td><?php echo $ranking[$modelName]['points'];?></td>
	<td><a href="/greek/<?php echo $ranking[$modelName]['id'];?>"><?php echo $ranking[$modelName]['name'];?></a></td>
	<td><?php echo $ranking[$modelName]['total_wins'];?></td>
	<td><?php echo $ranking[$modelName]['total_losses'];?></td>
	<td><?php echo $ranking[$modelName]['total_cupdif'];?></td>
	</tr>
	<?php endforeach;?>

</table>

<?php echo $this->element('paging');?>
<?php endif;?>