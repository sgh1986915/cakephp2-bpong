<?php $this->pageTitle = 'Find an NBPL League Night';?>
<?php 
$centerLat = '39.031332';
$centerLon = '-99.03125';
$zoom = 4;
if ($zip){
	$zoom = 5;
    if (!empty($geocode['lat']) && !empty($geocode['lon'])) {
   		$centerLat = $geocode['lat'];
   		$centerLon = $geocode['lon'];  		
    }
}
?>
<?php echo $this->element("venue/marker_map_nbpl_nights", array('markers' => $venues, 'zoom' => $zoom, 'centerLat' => $centerLat, 'centerLon' => $centerLon));?>
<div style='float:left;margin-bottom:5px;width:100%;'>
	<form name="nbpl_nights" action="/nbpl_nights" method="get">
	<span style='color:#202F74;'>Your Zip Code:</span>
	<input id="datepicker" class="hasDatepicker" type="text" style="width:90px;" value="<?php echo $zip;?>" name="zip">
	<input class="submit6" type="submit" value="Find">
	</form>
</div>
<div id="map" style="width: 100%; height: 500px; border:1px solid #CED1D6;float:left;margin-right:10px;"></div>
	<table class="sub_list sorter" border="0" cellspacing="0" cellpadding="0">
	  <tr class="paginationEvents">
	    <th>Name</th>
	    <th>City</th>
	    <th>State</th>	
	    <th>Zip code</th>
	    <th>Beer Pong Days</th>
	    <?php if ($zip):?><th>Distance (miles)</th><?php endif;?> 	        
	  </tr>
	<?php
		$i = 0;
		foreach($venues as $index => $venue):
			$class = null;
			if ($i++ % 2 != 0) {
				$class = ' class="alt"';
			}
		?>
	  <tr <?php echo $class;?>>
	    <td class="event-name"><a href="/venues/view/<?php echo $venue['VenueView']['slug'];?>"><?php echo $venue['VenueView']['name'];?></a></td>
	    <td class="event-name"><?php echo $venue['VenueView']['city'];?></td>
	    <td class="event-name"><?php echo $venue['VenueView']['state_name'];?></td>
	    <td class="event-name"><?php echo $venue['VenueView']['postalcode'];?></td>	
	    <td class="event-name">
            <?php if (!empty($venue['Nbplday'])):?>        
                <?php $d=0; $countDays = count($venue['Nbplday']); foreach($venue['Nbplday'] as $nbplday): $d++;?>
                <?php echo $nbplday['nbplday'];?><?php if ($countDays != $d){echo ",&nbsp;";}?>
                <?php endforeach;?>   
            <?php endif;?>	    	    
	    </td>
	   	<?php if ($zip):?><td class="event-name"><?php if (!empty($venue['0']['distance'])):?><?php echo ceil($venue['0']['distance']);?><?php endif;?></td><?php endif;?>     	        	    
	  </tr>
	 <?php endforeach;?>
</table>