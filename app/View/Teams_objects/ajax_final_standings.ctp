<script type="text/javascript">
	function showTeamsGames(teamID) {
       	$("#event_games").html(showLoaderHtml());
       	$("#event_games").load("/games/ajaxShowEventGames/<?php echo $eventID;?>/" + teamID);
       	return false;
	}
</script>
<?php $this->Paginator->options(array('url' => $this->passedArgs));?>

<?php if (empty($teams)):?>
      <div style='padding-left:15px;font-size:14px;'>There are no team results.</div>
      <?php else:?>
 	<table class="sorter red_table" border="0" cellspacing="0" cellpadding="0">
	  <tr>
	  	<th class='first-red-th'>Rank</th>
	  	<th>Team</th>
	  	<th>W</th>
	  	<th>L</th>
	    <th class='last-red-th'>CD</th>	   
	  </tr>
	<?php
		$i = 0;
		foreach($teams as  $team):
			$class = null;
			if ($i++ % 2 != 0) {
				$class = ' class="gray"';
			}
		?>
	  <tr <?php echo $class;?>>
	  	<td><?php echo $team['TeamsObject']['rank'];?></td>
		<td><a href='#' onclick = "return showTeamsGames(<?php echo $team['Team']['id'];?>)"><?php echo $this->Formater->stringCut($team['TeamsObject']['name'], 30);?></a> </td>
		<td><?php echo $team['TeamsObject']['wins'];?></td>
		<td><?php echo $team['TeamsObject']['losses'];?></td>
		<td><?php if($team['TeamsObject']['cupdif']>0):?>+<?php endif;?><?php echo $team['TeamsObject']['cupdif'];?></td>
	  </tr>
	<?php endforeach;?>
	  <!-- <tr class="gray"> -->
	</table>     	
<?php endif;?>

	<div class="paginationStandings">
		<?php echo $this->element('simple_paging');?>
	</div>
