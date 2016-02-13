<?php $this->Paginator->options(array('url' => $this->passedArgs));?>
<?php $this->pageTitle = 'Games of the Event "' . $event['Event']['name'] . '"'; ?>
<a class="backlink" href="/event/<?php echo $event['Event']['id'];?>/<?php echo $event['Event']['slug'];?>"><< Back To Event View</a>
<h2 class='hr'>Games of the Event "<?php echo $event['Event']['name'];?>"</h2>
<?php if (empty($games)):?>
      <div style='padding-left:15px;font-size:14px;'>There are no games</div>
      <?php else:?>
 	<table class="sorter red_table" border="0" cellspacing="0" cellpadding="0">
	  <tr>
	  	<th style='text-align:center;' class='first-red-th'>Game number</th>
	  	<th>Bracket</th>
	  	<th style='text-align:center;'>Teams</th>
	  	<th>CD</th>
	    <th class='last-red-th'>OTs</th>	   
	  </tr>
	<?php
		$i = 0;
		foreach($games as  $game):
			$class = null;
			if ($i++ % 2 != 0) {
				$class = ' class="gray"';
			}
			$looser = $vinner = array();
			if ($game['Game']['winningteam_id'] == $game['Team1']['id']) {
				$vinner = $game['Team1'];
				$looser	= $game['Team2'];
			} else {
				$vinner = $game['Team2'];
				$looser	= $game['Team1'];				
			}
		?>
	  <tr <?php echo $class;?>>
	    <td style='text-align:center;'><?php echo $game['Game']['gamenumber'];?></td>
		<td><?php echo $game['Brackettype']['type'];?></td>
		<td style='text-align:center;'>
			<a href='/nation/beer-pong-teams/team-info/<?php echo $vinner['slug'] ?>/<?php echo $vinner['id'] ?>/<?php echo $game['Game']['event_id'];?>' class='green_link'><?php 
                foreach ($vinner['TeamsObject'] as $teamObject)
                    if ($teamObject['status'] <> 'Deleted') 
                        echo $this->Formater->stringCut($teamObject['name'], 30);?></a> 
			<br/><span style='font-weight:normal'>vs</span><br/>
			<a href='/nation/beer-pong-teams/team-info/<?php echo $looser['slug'] ?>/<?php echo $looser['id'] ?>/<?php echo $game['Game']['event_id'];?>'><?php 
                foreach ($looser['TeamsObject'] as $teamObject)
                    if ($teamObject['status'] <> 'Deleted')      
                echo $this->Formater->stringCut($teamObject['name'], 30);?></a> 
		</td>
		<td><?php if ($game['Game']['cupdif']):?>+<?php endif;?><?php echo $game['Game']['cupdif'];?></td>
		<td><?php echo $game['Game']['numots'];?></td>
	  </tr>
	<?php endforeach;?>
	  <!-- <tr class="gray"> -->
	</table>     	
<?php endif;?>

	<div class="paginationGames">
		<?php echo $this->element('simple_paging');?>
	</div>
