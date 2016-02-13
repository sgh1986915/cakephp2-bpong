<?php echo $this->Html->script('jquery.ui.min.js'); ?>
<?php echo $this->Html->css('ui.datepicker2.css'); ?>
<?php $this->Paginator->options(array('url' => $this->passedArgs));?>
<script type='text/javascript'>
$(document).ready(function() {
	$( ".datepicker" ).datepicker({ dateFormat: 'm/d/y' });
});

function changeEvent() {
	  var opponent_id = $('#select_opponent_id').val();
	  var event_id = $('#select_event_id').val();
	  $("#select_opponent_id").html('<option>Loading...</option>');
	  $.ajaxSetup({cache:false});
	  $.post("/games/get_event_opponents",{'event_id': event_id, 'user_id' : <?php echo $user['User']['id'];?>}, function(options){
			$("#select_opponent_id").html(options);
			$('#select_opponent_id > option[value="' + opponent_id + '"]').attr('selected', 'selected');
			$('#filter_form').submit();
	  });
}
function changeOpponent () {
	  var opponent_id = $('#select_opponent_id').val();
	  var event_id = $('#select_event_id').val();
	  $("#select_event_id").html('<option>Loading...</option>');
	  $.ajaxSetup({cache:false});
	  $.post("/games/get_opponent_events",{'opponent_id': opponent_id, 'user_id' : <?php echo $user['User']['id'];?>}, function(options){
			$("#select_event_id").html(options);
			$('#select_event_id > option[value="' + event_id + '"]').attr('selected', 'selected');
			$('#filter_form').submit();
	  });
}

</script>

<?php $this->pageTitle = 'Player Stats:' . $this->Formater->userName($user['User'], 1); ?>
<div class="left" style="text-align:left;width:270px;">
	<h2 class='hr' style='font-size:18px;'>Player Stats: <?php echo $this->Formater->userName($user['User'], 1);?></h2>
	<br/>
	<div class='clear' style='width:100%;'><?php echo $this->element('/teammates', array('teammates' => array('0' => $user)));?></div>
	<div class="stats-item">
		<div  class="left team_stats">Wins:</div>
		<div class="right"><?php echo intval($user['stats']['wins']);?></div>
	</div>
	<div class="stats-item">
		<div  class="left team_stats">Losses:</div>
		<div class="right"><?php echo intval($user['stats']['losses']);?></div>
	</div>
	<div class="stats-item">
		<div  class="left team_stats">Average Cup Differential:</div>
		<div class="right">
		<?php if ($user['stats']['average_cupdif'] > 0) echo "+";
		echo $user['stats']['average_cupdif'];?></div>
	</div>
	<div class="stats-item">
		<div  class="left team_stats">Win/Loss %:</div>
		<div class="right"><?php echo 100 * $user['stats']['average_wins'];?></div>
	</div>	
	
	<div class="stats-item">
		<div  class="left team_stats">Games played:</div>
		<div class="right"><?php echo intval($user['stats']['wins']+$user['stats']['losses']);?></div>
	</div>		
    <div class="stats-item">
        <div class="left team_stats">BPONG Rating:</div>
        <div class="right"><?php printf("%1.0F", $user['User']['rating']); ?></div>
    </div>
    <div class="stats-item">
        <div class="left team_stats">Global Ranki:</div>
        <div class="right"><?php echo $userRanking['Ranking']['rank'].' out of '.$userRanking['Rankinghistory']['numusers'];?></div>
    </div>
            
    
</div>


<div class="left" style="text-align:left;margin-left:20px;width:610px;">
	<h2 class='hr' style='font-size:18px;'>Recent Game Results</h2>
	<?php if (!empty($userChart)):?>
		<?php echo $this->element('/charts/user_profile_chart_big', array('userChart' => $userChart));?>	
	<?php else:?>
		<br/>There is no stats for this user.
	<?php endif;?>	
</div>

<div class='clear'><br/></div>
<h2>Game Detail</h2>
<div style='font-size:12px;padding-bottom:5px;color:#D61C20;width:100%;margin-bottom:5px;border-bottom: 3px solid #D61C20;'>
	<?php echo $this->Form->create('GamesSearch', array('id' => 'filter_form', 'url' => '/users/stats/' . $user['User']['id'] . '/games_user_search:1')); ?>
		<span class="form-title">Date Range:</span>
		<?php echo $this->Form->input('date_from', array('onchange' => "$('#filter_form').submit();", 'class' => 'datepicker', 'label' => false, 'div' => false, 'style' => 'width:55px;')); ?>
		&nbsp;<span class="form-title">to</span>&nbsp;
		<?php echo $this->Form->input('date_to', array('onchange' => "$('#filter_form').submit();", 'class' => 'datepicker', 'label' => false, 'div' => false, 'style' => 'width:55px;')); ?>
		&nbsp;
		<?php echo $this->Form->input('game_type', array('onchange' => "$('#filter_form').submit();", 'label' => false, 'div' => false, 'style' => 'color:#D61C20;width:170px;','type' => 'select', 'options' => array('0' => 'Select Game Type', 'single' => 'Singles', 'team' => 'Team'))); ?>
		&nbsp;				
		<?php echo $this->Form->input('event_id', array('id' => 'select_event_id', 'onchange' => 'changeEvent();', 'label' => false, 'div' => false, 'style' => 'color:#D61C20;width:170px;','type' => 'select', 'options' => array('0' => 'Select Tournament') + $gameEvents)); ?>
		&nbsp;
		<?php echo $this->Form->input('opponent_id', array('id' => 'select_opponent_id', 'onchange' => 'changeOpponent();', 'label' => false, 'div' => false, 'style' => 'color:#D61C20;width:170px;','type' => 'select', 'options' => array('0' => 'Select Opponent') + $gameOpponents)); ?>	
	<?php echo $this->Form->end() ?>
</div>

<?php $this->Paginator->options(array('url' => $this->passedArgs)); ?>
<?php if (empty($games)):?>
	<div style='font-size:120%;text-align:center;width:100%;padding:20px;'>There are no Games</div>
<?php else:?>
	<table class="sorter red_table" border="0" cellspacing="0" cellpadding="0">
	  <tr>
	  	<th class='first-red-th'>Team</th>
	  	<th>Opponent</th>
	  	<th>Result</th>
	  	<th><?php echo $this->Paginator->sort('CD', 'cupdif', array('sorter' => true));?></th>
	  	<th><?php echo $this->Paginator->sort('OTs', 'numots', array('sorter' => true));?></th>
	  	<th><?php echo $this->Paginator->sort('Date', 'Game.created', array('sorter' => true));?></th>
	  	<th><?php echo $this->Paginator->sort('Tournament', 'Event.name', array('sorter' => true));?></th>
	  	<th class='last-red-th'><?php echo $this->Paginator->sort('Bracket', 'brackettype_id', array('sorter' => true));?></th> 
	  </tr>
	<?php
		$i = 0;
		foreach($games as $index => $game):
			$class = null;
			if ($i++ % 2 != 0) {
				$class = ' class="gray"';
			}
			$vin = 0;
			if (isset($userTeams[$game['Game']['winningteam_id']])) {
				$vin = 1;	
			}
			if (isset($userTeams[$game['Team1']['id']])) {
				$myTeam = $game['Team1'];
				$opponent = $game['Team2'];
			}else {
				$myTeam = $game['Team2'];
				$opponent = $game['Team1'];				
			}
		?>
	  <tr <?php echo $class;?>>
	  	<td><?php echo $this->Html->link($myTeam['name'], '/nation/beer-pong-teams/team-info/' . $myTeam['slug'] . '/' . $myTeam['id']); ?></td>	    	  
	  	<td><?php echo $this->Html->link($opponent['name'], '/nation/beer-pong-teams/team-info/' . $opponent['slug'] . '/' . $opponent['id']); ?></td>	    	
	  	<td style='text-align:center;'><?php if ($vin):?><span style='color:rgb(4,157,0);'>W</span><?php else:?><span style='color:rgb(168,58,58);'>L</span><?php endif;?></td>
	  	<td style='text-align:center;'><?php if ($vin) {echo '+';} else { echo '-';}?><?php echo $game['Game']['cupdif'];?></td>
	  	<td style='text-align:center;'><?php echo $game['Game']['numots'];?></td>
		<td><?php echo date('m/d/Y', strtotime($game['Game']['created']));?></td>
	    <td><?php echo $this->Html->link($game['Event']['name'], '/event/' . $game['Event']['id'] . '/' . $game['Event']['slug']); ?></td>	      
	  	<td><?php echo $game['Brackettype']['type'];?></td>   	    
	  </tr>
	<?php endforeach;?>
	  <!-- <tr class="gray"> -->
	</table>
<?php endif;?>
<?php echo $this->element('simple_paging');?>



