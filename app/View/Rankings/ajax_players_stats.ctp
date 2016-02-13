<?php if (empty($rankings)):?>
	<div class='no_results'>No results found</div>
<?php else: ?>
<?php echo $this->Paginator->options(array('url' => $this->passedArgs)); ?>
<table class="toptable" cellspacing="0" cellpadding="0">
    <thead>
    <tr>
    	<th>Rank</th>
    	<th>Player</th>
    	<th>Rating</th>
    	<th>Wins</th>
    	<th>Losses</th>
    	<th>CD</th>
    	<th>Location</th>
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
	<td><?php echo $ranking['Ranking']['rank'];?></td>
	<td><a href="/u/<?php echo $ranking[$modelName]['lgn']?>"><?php echo $ranking[$modelName]['lgn'];?></a></td>
	<td><?php echo sprintf("%01.2f", $ranking['Ranking']['rating']);?></td>
	<td><?php echo $ranking[$modelName]['total_wins'];?></td>
	<td><?php echo $ranking[$modelName]['total_losses'];?></td>
	<td><?php echo $ranking[$modelName]['total_cupdif'];?></td>
	<td>
	<?php if (!empty($ranking[$modelName]['Address'][0]['Country']['id']) && !empty($ranking[$modelName]['Address'][0]['city'])):?>
	<?php if (!empty($ranking[$modelName]['Address'][0]['city'])):?><?php echo ucwords(strtolower($ranking[$modelName]['Address'][0]['city']));?>, <?php echo $ranking[$modelName]['Address'][0]['Country']['iso2'];?>
	<?php else:?><?php endif;?>
	<?php else:?>-<?php endif;?>
	</td>
	</tr>
	<?php endforeach;?>
	</tbody>
</table>

<?php if ($this->Paginator->numbers(array('model' => 'Ranking'))):?>
	<div class="paginationRanking">
		<?php echo $this->element('simple_paging');?>
	</div>
	<br class='clear'/>
<?php endif;?>

<?php endif;?>