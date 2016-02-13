<?php $this->set('title_for_layout', 'The Beer Pong Greeks Stats');?>
<?php if (empty($rankings)):?>
	<div class='no_results'>No results found</div>
<?php else: ?>
<?php echo $this->Paginator->options(array('url' => $this->passedArgs)); ?>
<table class="toptable" cellspacing="0" cellpadding="0">
    <thead>
    <tr>
    	<th>Points</th>
    	<th>Greek</th>
    	<th>Wins</th>
    	<th>Losses</th>
    	<th>CD</th>
    </tr>
        </thead>
    <tbody>
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
	<td><?php echo $ranking[$modelName]['name'];?></td>
	<td><?php echo $ranking[$modelName]['total_wins'];?></td>
	<td><?php echo $ranking[$modelName]['total_losses'];?></td>
	<td><?php echo $ranking[$modelName]['total_cupdif'];?></td>
	</tr>
	<?php endforeach;?>
    	</tbody>
</table>

<?php if ($this->Paginator->numbers(array('model' => $modelName))):?>
	<div class="paginationRanking">
		<?php echo $this->element('simple_paging');?>
	</div>
	<br class='clear'/>
<?php endif;?>
<?php endif;?>