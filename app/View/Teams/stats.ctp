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
	  $.post("/games/get_event_opponents",{'event_id': event_id, 'team_id' : <?php echo $this->request->data['Team']['id'];?>}, function(options){
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
	  $.post("/games/get_opponent_events",{'opponent_id': opponent_id, 'team_id' : <?php echo $this->request->data['Team']['id'];?>}, function(options){
			$("#select_event_id").html(options);
			$('#select_event_id > option[value="' + event_id + '"]').attr('selected', 'selected');
			$('#filter_form').submit();
	  });
}
</script>
<?php $this->pageTitle = 'Team Profile: ' . $this->request->data['Team']['name']; ?>

<div style='position: relative;margin-bottom:-30px;bottom:-2px; float:right;'><?php echo $this->element('facebook_like');?></div>
<div class="left" style="text-align:left;width:270px;">
	<h2 class='hr' style='font-size:18px;'><?php echo $this->request->data['Team']['name']?></h2>
	<br/>
	<div class='clear' style='width:100%;'><?php echo $this->element('/teammates', array('teammates' => $teammates));?></div>
	<div class="stats-item">
		<div  class="left team_stats">Wins</div>
		<div class="right"><?php echo intval($this->request->data['Team']['total_wins']);?></div>
	</div>
	<div class="stats-item">
		<div  class="left team_stats">Losses:</div>
		<div class="right"><?php echo intval($this->request->data['Team']['total_losses']);?></div>
	</div>
	<div class="stats-item">
		<div  class="left team_stats">Average Cup Differential:</div>
		<div class="right"><?php if ($averageCupdif>0):?>+<?php endif;?><?php echo $averageCupdif;?></div>
	</div>
	<div class="stats-item">
		<div  class="left team_stats">Win %:</div>
		<div class="right"><?php echo $averageWin;?></div>
	</div>	
	
	<div class="stats-item">
		<div  class="left team_stats">Games played:</div>
		<div class="right"><?php echo intval($this->request->data['Team']['total_wins']+$this->request->data['Team']['total_losses']);?></div>
	</div>	
	<div class="stats-item">
		<div  class="left team_stats">Date Created:</div>
		<div class="right"><?php echo date('m/d/Y', strtotime($this->request->data['Team']['created']));?></div>
	</div>	
</div>
<div class="left" style="text-align:left;margin-left:20px;width:600px;">
	<h2 class='hr' style='font-size:18px;'>Recent Game Results</h2>
	<?php if (!empty($chartInfo)):?>
		<?php echo $this->element('/charts/team_stats_chart', array('chartInfo' => $chartInfo));?>
	<?php else:?>
		<br/>There are no game results for this team.
	<?php endif;?>
	
</div>
<div class='clear'><br/></div>
<div class='searchcalendar' style='float:right;position:relative;bottom:-20px;font-size:12px;padding-right:5px;width:730px;'>
	<?php echo $this->Form->create('GamesSearch', array('id' => 'filter_form', 'url' => '/teams/stats/' . $this->request->data['Team']['slug'] . '/' . $this->request->data['Team']['id'] . '/games_search:1')); ?>
		<span class="form-title">Date Range:</span>
		<?php echo $this->Form->input('date_from', array('onchange' => "$('#filter_form').submit();", 'class' => 'datepicker', 'label' => false, 'div' => false, 'style' => 'width:55px;')); ?>
		&nbsp;&nbsp;<span class="form-title">to</span>&nbsp;
		<?php echo $this->Form->input('date_to', array('onchange' => "$('#filter_form').submit();",'class' => 'datepicker', 'label' => false, 'div' => false, 'style' => 'width:55px;')); ?>
		&nbsp;&nbsp;&nbsp;&nbsp;
		<?php echo $this->Form->input('event_id', array('id' => 'select_event_id', 'onchange' => 'changeEvent();', 'label' => false, 'div' => false, 'style' => 'width:180px;', 'style' => 'color:#D61C20;width:170px;','type' => 'select', 'options' => array('0' => 'Select Tournament') + $gameEvents)); ?>
		&nbsp;&nbsp;&nbsp;&nbsp;
		<?php echo $this->Form->input('opponent_id', array('id' => 'select_opponent_id', 'onchange' => 'changeOpponent();', 'label' => false, 'div' => false, 'style' => 'width:180px;', 'style' => 'color:#D61C20;width:170px;','type' => 'select', 'options' => array('0' => 'Select Opponent') + $gameOpponents)); ?>
	 
	<?php echo $this->Form->end() ?>
</div>
<h2 class='hr'>Games</h2>
<?php $this->Paginator->options(array('url' => $this->passedArgs)); ?>
<?php if (empty($games)):?>
	<div style='font-size:120%;text-align:center;width:100%;padding:20px;'>There are no Games</div>
<?php else:?>
	<table class="sorter red_table" border="0" cellspacing="0" cellpadding="0">
	  <tr>
	  	<th class='first-red-th'>Opponent</th>
	  	<th>Result</th>
	  	<th><?php echo $this->Paginator->sort('CD', 'cupdif', array('sorter' => true));?></th>
	  	<th><?php echo $this->Paginator->sort('OTs', 'numots', array('sorter' => true));?></th>
	  	<th><?php echo $this->Paginator->sort('Date', 'Game.created', array('sorter' => true));?></th>
	  	<th><?php echo $this->Paginator->sort('Tournament', 'Event.name', array('sorter' => true));?></th>
	  	<th><?php echo $this->Paginator->sort('Bracket', 'brackettype_id', array('sorter' => true));?></th>
	    <th><?php echo $this->Paginator->sort('Round #', 'round', array('sorter' => true));?></th>
	    <th class='last-red-th'><?php echo $this->Paginator->sort('Table #', 'table', array('sorter' => true));?></th>	   
	  </tr>
	<?php
		$i = 0;
		foreach($games as $index => $game):
			$class = null;
			if ($i++ % 2 != 0) {
				$class = ' class="gray"';
			}
			$vin = 0;
			if ($game['Game']['winningteam_id'] == $this->request->data['Team']['id']) {
				$vin = 1;	
			}
			if ($game['Team1']['id'] == $this->request->data['Team']['id']) {
				$opponent = $game['Team2'];
			}else {
				$opponent = $game['Team1'];				
			}
		?>
	  <tr <?php echo $class;?>>
	  	<td><?php echo $this->Html->link($opponent['name'], '/nation/beer-pong-teams/team-info/' . $opponent['slug'] . '/' . $opponent['id']); ?></span></td>   	
	  	<td class = 'center'><?php if ($vin):?><span style='color:rgb(4,157,0);'>W</span><?php else:?><span style='color:rgb(168,58,58);'>L</span><?php endif;?></td>
	  	<td class = 'center'><?php if ($vin) {echo '+';} else { echo '-';}?><?php echo $game['Game']['cupdif'];?></td>
	  	<td class = 'center'><?php echo $game['Game']['numots'];?></td>
		<td><?php echo date('m/d/Y', strtotime($game['Game']['created']));?></td>
	    <td><?php echo $this->Html->link($game['Event']['name'], '/event/' . $game['Event']['id'] . '/' . $game['Event']['slug']); ?></span></td>	  
	  	<td><?php echo $game['Brackettype']['type'];?></td>
	    <td class = 'center'><?php echo $game['Game']['round'];?></td>
	    <td class = 'center'><?php echo $game['Game']['table'];?></td>	    
	    
	  </tr>
	<?php endforeach;?>
	  <!-- <tr class="gray"> -->
	</table>
<?php endif;?>

	<?php echo $this->element('simple_paging');?>


