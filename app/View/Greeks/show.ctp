<?php $this->pageTitle = $modelName . ' - ' . $affil[$modelName]['name']; ?>
<script type="text/javascript">
	$(document).ready(function() {
		customPreLoadPiece("/users_affils/ajax_affils_users/<?php echo $modelName;?>/<?php echo $id;?>","#list_1", 'paginationGames', 'loader_1');
		customPreLoadPiece("/games/ajaxShowAffilGames/<?php echo $modelName;?>/<?php echo $id;?>","#list_2", 'paginationGames', 'loader_2');
	});
</script>
<div class="left" style="text-align:left;width:270px;">
	<h2 class='hr' style='font-size:18px;'><?php echo $affil[$modelName]['name']?></h2>
	<?php /*?>
	<div style='float:left;padding:5px 10px 10px 10px;font-size:16px;color:#202F74;'>
	Rank<br/>
	#12<sup>th</sup>
	<br/>
	</div>
	<?php */ ?>
	<?php if ($affil[$modelName]['total_wins'] > 0 || $affil[$modelName]['total_losses']):?>
	<div style='float:left;padding-left:40px;'>
		<?php echo $this->element('/charts/affil_pie_chart', array('chartIndex' => 1, 'winnings' => $affil[$modelName]['total_wins'], 'losses' => $affil[$modelName]['total_losses'], 'chartLink' => ''));?>	
	</div>
	<?php endif;?>
	<br/>
	<div class="stats-item">
		<div  class="left team_stats">Type</div>
		<div class="right"><?php echo $affil[$modelName]['type'];?></div>
	</div>
	<div class="stats-item">
		<div  class="left team_stats">Wins</div>
		<div class="right"><?php echo intval($affil[$modelName]['total_wins']);?></div>
	</div>
	<div class="stats-item">
		<div  class="left team_stats">Losses:</div>
		<div class="right"><?php echo intval($affil[$modelName]['total_losses']);?></div>
	</div>
	<div class="stats-item">
		<div  class="left team_stats">Cup differential:</div>
		<div class="right"><?php echo intval($affil[$modelName]['total_cupdif']);?></div>
	</div>
	<div class="stats-item">
		<div  class="left team_stats">Players affiliated:</div>
		<div class="right"><?php echo intval($affil[$modelName]['userscount']);?></div>
	</div>
</div>

<div class="left" style="text-align:left;margin-left:20px;width:620px;">
	<h2 class='hr' style='font-size:18px;'>Recent Game Results</h2>
	<?php if (!empty($chartInfo)):?>
		<?php echo $this->element('/charts/team_stats_chart', array('chartInfo' => $chartInfo));?>
	<?php else:?>
		<br/>There are no results.
	<?php endif;?>
</div>
<div class='clear' style='width:100%;'><br/></div>
			<div style='float:left; width:37%;'>
				<h2 class='hr'>Top Players</h2>
				<div id='list_1'>
					<?php echo $this->requestAction('/users_affils/ajax_affils_users/' . $modelName . '/' . $id); ?>
				</div>
				<div class='loader_1' style='height:10px;' class='clear'></div>
			</div>
			<div style='float:right; width:60%;'>
				<h2 class='hr'>Recent Games
				<div style='font-size:13px;float:right;color:#2BA500;font-weight:bold;padding-right:30px;bottom:-2px;position:relative;'>* Winning teams in green</div>
				</h2>
				<div id='list_2'>
					<?php echo $this->requestAction('/games/ajaxShowAffilGames/' . $modelName . '/' . $id); ?>
				</div>
				<div class='loader_2' style='height:10px;' class='clear'></div>
			</div>