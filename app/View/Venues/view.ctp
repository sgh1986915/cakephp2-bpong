<?php
	if (!empty($venue['Address']) && (!$venue['Address']['latitude'] || !$venue['Address']['longitude'])) {
		$latLon = $this->Address->getLatLon($venue['Address'], 1);
		$venue['Address']['latitude'] = $latLon['lat'];
		$venue['Address']['longitude'] = $latLon['lon'];		
	}

	$lat = $venue['Address']['latitude'];
  	$lon = $venue['Address']['longitude'];
  	if ( empty($lat) && empty ($lon) ) {
  		$informWindow = "Google Map Coordinates are not set";
  	} else {
  		$informWindow = "<div><b>{$venue['Venue']['name']}</b><div align=\"right\">{$venue['Address']['address']}<br />{$venue['Address']['city']}</div></div>";
  	}
  	if ( empty( $lat ) ) {
  		$lat = "37.4419";
  	}
  	if ( empty( $lon ) ) {
  		$lon = "-122.1419";
  	}
?>



<style>
.info-content {
	font-size: 14px;
	line-height: 20px;
}
.venue_invo_div {border: 1px solid #C0C0C0;float:left;width:250px;text-align:left;}
.venue_invo_div a {text-decoration:none;}
.venue_invo_div_header{font-family: Folio, sans-serif; width:100%; color:white; background-color:black;text-align:center;font-size:23px;padding-top:6px; padding-bottom:6px;}
.venue_invo_div_f_table td {padding-top:5px;}
.venue_invo_event td {font-weight:normal;padding-top:7px;}
.venue_invo_event td a {font-weight:normal;text-decoration:none;}
</style>

<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
$(document).ready(function() {
	theSameHeightDivs('venue_invo_div');
});

function theSameHeightDivs (className) {
	var maxHeight = 1;
	var thisHeight = 1;
	$('.' + className).each(function(index) {
		thisHeight = parseInt($(this).css('height'));
		if (thisHeight > maxHeight) {
			maxHeight = thisHeight;
		}

	});
	$('.' + className).css('height', maxHeight);	
}

//<![CDATA[ 
	latitude = <?php echo $lat; ?>;
	longitude = <?php echo $lon; ?>;
	infoContent = "<div class='info-content'><?php echo addslashes( $informWindow ); ?></div>";
//]]> 
</script>
<?php	echo $this->Html->script('pages/venues/view.js'); ?>
    <h2 class='hr'>Venue <span class="h_subtext">&rsaquo; <?php echo $venue['Venue']['name']; ?></span> <?php if($Updated) : ?>
	    <?php echo $this->Html->link('Edit', array('controller' => 'Venues', 'action' => 'edit', $venue['Venue']['id'])); ?>
	<?php endif; ?></h2>

<div style='float:left;width:661px;'> 
<div id="map_canvas" style='margin-top:15px !important;width:660px;margin-bottom:10px !important;'> </div>

<h2 class="hr big_h" >Top 10 Players</h2>
<?php if ($venue['Venue']['nbpltype'] == 'None'):?>
	This is not an NBPL Venue.<br/><br/>
<?php elseif(empty($venueUsers)):?>
	There are no players.<br/><br/>
<?php else:?>
	<table class="toptable rating_table" cellspacing="0" cellpadding="0">
	    <thead>
	    <tr>
	    	<th>Rank</th>
	    	<th>Player</th>
	    	<th>Points</th>
	    	<th>Rating</th>
	    	<th>Wins</th>
	    	<th>Losses</th>
	    	<th>CD</th>
	    </tr>
	    </thead>
	    <tbody>
		<?php
		$i = 0;
		foreach ($venueUsers as $venueUser):
	    	$class = '';
	    	if ($i++ % 2 != 0) {
	    		$class = ' class="alt"';
	    	}
		?>
		<tr<?php echo $class;?>>
		<td><?php echo $i;?></td>
		<td><a href="/u/<?php echo $venueUser['User']['lgn']?>"><?php echo $venueUser['User']['lgn'];?></a></td>
		<td><?php echo $venueUser['VenuesUser']['nbplpoints_ytd'];?></td>
		<td><?php echo sprintf("%01.2f", $venueUser['User']['rating']);?></td>
		<td><?php echo $venueUser['VenuesUser']['wins_ytd'];?></td>
		<td><?php echo $venueUser['VenuesUser']['losses_ytd'];?></td>
		<td><?php echo $venueUser['VenuesUser']['cupdif_ytd'];?></td>
		</tr>
		<?php endforeach;?>
		</tbody>
	</table>
<?php endif;?>
<br/>
<h2 class="hr big_h" >Top 10 Teams</h2>
<?php if ($venue['Venue']['nbpltype'] == 'None'):?>
	This is not an NBPL Venue.<br/><br/>
<?php elseif(empty($venueUsers)):?>
	There are no teams.<br/><br/>
<?php else:?>
	<table class="toptable rating_table" cellspacing="0" cellpadding="0">
	    <thead>
	    <tr>
	    	<th>Rank</th>
	    	<th>Team</th>
	    	<th>Points</th>
	    	<th>Rating</th>
	    	<th>Wins</th>
	    	<th>Losses</th>
	    	<th>CD</th>
	    </tr>
	    </thead>
	    <tbody>
		<?php
		$i = 0;
		foreach ($venueTeams as $venueTeam):
	    	$class = '';
	    	if ($i++ % 2 != 0) {
	    		$class = ' class="alt"';
	    	}
		?>
		<tr<?php echo $class;?>>
		<td><?php echo $i;?></td>
		<td><a href="/nation/beer-pong-teams/team-info/<?php echo $venueTeam['Team']['slug']?>/<?php echo $venueTeam['Team']['id']?>"><?php echo $venueTeam['Team']['name']?></a></td>
		<td><?php echo $venueTeam['VenuesTeam']['nbplpoints_ytd'];?></td>
		<td><?php echo sprintf("%01.2f", $venueTeam['Team']['rating']);?></td>
		<td><?php echo $venueTeam['VenuesTeam']['wins_ytd'];?></td>
		<td><?php echo $venueTeam['VenuesTeam']['losses_ytd'];?></td>
		<td><?php echo $venueTeam['VenuesTeam']['cupdif_ytd'];?></td>
		</tr>
		<?php endforeach;?>
		</tbody>
	</table>
<?php endif;?>
</div>
<div style='float:right;margin-top:15px !important;width:251px;'>
	<div class='venue_invo_div'>
		<div class='venue_invo_div_header'><span class='b'>VENUE</span> DETAILS</div>
		<div style='float:left; width:100%; margin-bottom:10px;background-color:#F6A629; border-top:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;color:black; fonr-size:16px;text-align:center;font-weight:bold;padding-top:4px; padding-bottom:4px;'>
			<?php echo $venue['Venue']['name']; ?>
		</div>
		<table class='venue_invo_div_f_table' >
			<tr><td><span class='b'>Type of venue</span></td><td><?php echo $venue['Venuetype']['name']; ?></td></tr>
			<?php $venue['Venue']['description'] = trim($venue['Venue']['description']); if (!empty($venue['Venue']['description'])):?>
			<tr><td><span class='b'>Description</span></td><td><?php echo $venue['Venue']['description']; ?></td></tr>
			<?php endif;?>
			<?php if (!empty($venue['Address']['address'])):?>
			<tr><td><span class='b'>Address</span></td><td><?php echo $venue['Address']['address']; ?></td></tr>
			<?php endif;?>
			<?php if (!empty($venue['Address']['city'])):?>
			<tr><td><span class='b'>City</span></td><td><?php echo $venue['Address']['city']; ?></td></tr>
			<?php endif;?>
			<?php if (!empty($venue['Address']['Provincestate']['name'])):?>
			<tr><td><span class='b'>State</span></td><td><?php echo $venue['Address']['Provincestate']['name']; ?></td></tr>
			<?php endif;?>
			<?php if (!empty($venue['Address']['Country']['name'])):?>				
			<tr><td><span class='b'>Country</span></td><td><?php echo $venue['Address']['Country']['name']; ?></td></tr>
			<?php endif;?>
			<?php if (!empty($venue['Venue']['web_address']) &&  $venue['Venue']['web_address'] != "http://" && ( strpos( $venue['Venue']['web_address'], "http://" ) === 0 )):?>
			<tr><td><span class='b'>Web Address</span></td><td><a href="<?php echo $venue['Venue']['web_address'];?>">link</a></td></tr>
			<?php endif;?>
			<?php if (!empty($venue['Phone'])):?>		
			<tr><td><span class='b'>Telephone</span></td><td><?php foreach ($venue['Phone'] as $phones): ?><?php echo $phones['phone'];?><?php endforeach; ?></td></tr>	
			<?php endif;?>	
			<?php if (!empty($venue['Nbplday'])):?>		
			<tr><td><span class='b'>NBPL League Night(s)</span></td><td>
	                <?php 
	                $d=0; 
	                $countDays = count($venue['Nbplday']);
	                foreach($venue['Nbplday'] as $nbplday):
	                $d++;
	                ?>
	                <?php echo $nbplday['nbplday'];?> - <?php echo date('g:i a', strtotime($nbplday['nbplstarttime']));?><br/>
	                <?php endforeach;?>
			</td></tr>
			<?php endif;?>					
		</table>
	</div>
	
	<div class='venue_invo_div' style='margin-top:15px;margin-bottom:15px;'>
		<div class='venue_invo_div_header'><span class='b'>UPCOMING</span> EVENTS</div>	
		
		<div style='margin-bottom:10px;float:left; width:100%; background-color:#D13A2F; border-top:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;color:white; fonr-size:16px;text-align:left;font-weight:bold;padding:0px;'>
			<div style='float:left;padding-left:5px;padding-top:4px; padding-bottom:4px;margin:0px;'>Name</div><div style='float:right;border-left:1px solid #C0C0C0;padding-right:20px;padding-left:20px;padding-top:4px; padding-bottom:4px;margin:0px;'>Date</div>
		</div>
		<?php if (empty($upcomingEvents)):?>
			&nbsp;&nbsp;&nbsp;There are no upcoming events
		<?php else:?>
		<table cellpadding=0 cellspacing=0 class='venue_invo_event'>
			<?php 
			  $i = 0;
			  foreach ($upcomingEvents as $upcomingEvent):
			  $class = null;
	 			if ($i++ % 2 != 0) {
	    			$class = ' class="gray"';
	 			 }
			
			?>
				<tr<?php echo $class;?>><td><a href="/event/<?php echo $upcomingEvent['Event']['id'];?>/<?php echo $upcomingEvent['Event']['slug'];?>"><?php echo $upcomingEvent['Event']['name'];?></a></td><td><?php echo date('m/d/Y', strtotime($upcomingEvent['Event']['start_date']));?></td></tr>	
			<?php endforeach;?>
		</table>
		<div style='float:right;padding:10px;'><a href="/venues/all_events/<?php echo $venue['Venue']['id'];?>">All Events ></a></div>
		<?php endif;?>
	</div>
	<div class='venue_invo_div'>
		<div class='venue_invo_div_header'><span class='b'>RECENT</span> EVENTS</div>
		<div style='margin-bottom:8px;text-align:center;margin-bottom:10px;float:left; width:100%; background-color:#1658A2; border-top:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;color:white; fonr-size:16px;text-align:left;font-weight:bold;padding:0px;'>
			<div style='float:left;padding-left:5px;padding-top:4px; padding-bottom:4px;margin:0px;'>Name</div><div style='float:right;border-left:1px solid #C0C0C0;padding-right:20px;padding-left:25px;padding-top:4px; padding-bottom:4px;margin:0px;'>Date</div>		
		</div>
		<?php if (empty($recentEvents)):?>
			&nbsp;&nbsp;&nbsp;There are no recent events
		<?php else:?>
		<table cellpadding=0 cellspacing=0 class='venue_invo_event'>
			<?php 
			  $i = 0;
			  foreach ($recentEvents as $recentEvent):
			  $class = null;
	 			if ($i++ % 2 != 0) {
	    			$class = ' class="gray"';
	 			 }
			
			?>
				<tr<?php echo $class;?>><td><a href="/event/<?php echo $recentEvent['Event']['id'];?>/<?php echo $recentEvent['Event']['slug'];?>"><?php echo $recentEvent['Event']['name'];?></a></td><td><?php echo date('m/d/Y', strtotime($recentEvent['Event']['start_date']));?></td></tr>	
			<?php endforeach;?>
		</table>
		<div style='float:right;padding:10px;'><a href="/venues/recent_events/<?php echo $venue['Venue']['id'];?>">All Past Events ></a></div>
		<?php endif;?>
	</div>

</div>
<div class="clear"></div>