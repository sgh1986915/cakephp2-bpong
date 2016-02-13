<?php $this->set('title_for_layout', 'The Beer Pong Teams Stats');?>
<?if (empty($rankings)) {?>
	<div class='no_results'>No results found</div>
<?php } else { ?>
<?php echo $this->Paginator->options(array('url' => $this->passedArgs)); ?>
<table class='sub_list sorter' cellpadding="0" cellspacing="0">
    <tr>

    	<th>Team</th>
    	<th><?php echo $this->Paginator->sort('Rating', 'rating', array('sorter' => true));?> <img src="<?php echo IMG_NBPL_LAYOUTS_URL;?>/question.png" class="tooltip" alt="?" />
	    	<div class="tooltip_info">
	    		<?php echo $this->element('/rankings/tooltip_ranking');?>
			</div>
    	</th>
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

	<td><a href="/nation/beer-pong-teams/team-info/<?php echo $ranking['Team']['slug'];?>/<?php echo $ranking['Team']['id'];?>"><?php echo $ranking['Team']['name'];?></a></td>
	<td><?php echo sprintf("%01.2f", $ranking['Team']['rating']);?></td>
	<td><?php echo $ranking['Team']['total_wins'];?></td>
	<td><?php echo $ranking['Team']['total_losses'];?></td>
	<td><?php echo $ranking['Team']['total_cupdif'];?></td>
	</tr>
	<?php endforeach;?>

</table>

<?php echo $this->element('paging');?>
<?php } ?>