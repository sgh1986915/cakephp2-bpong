<?php if (empty($rankings)):?>
	<div class='no_results'>No results found</div>
<?php else: ?>
<?php echo $this->Paginator->options(array('url' => $this->passedArgs)); ?>
<table class="toptable" cellspacing="0" cellpadding="0">
    <thead>
    <tr>
    	<th>Team</th>
    	<th>Rating</th>
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
	<td><a href="/nation/beer-pong-teams/team-info/<?php echo $ranking['Team']['slug'];?>/<?php echo $ranking['Team']['id'];?>"><?php echo $ranking['Team']['name'];?></a></td>
	<td><?php echo sprintf("%01.2f", $ranking['Team']['rating']);?></td>
	<td><?php echo $ranking['Team']['total_wins'];?></td>
	<td><?php echo $ranking['Team']['total_losses'];?></td>
	<td><?php echo $ranking['Team']['total_cupdif'];?></td>
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