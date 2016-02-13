<script type="text/javascript">
//<![CDATA[
    $("#VenuesMap").height(250);        
    var venues_map;  
 	function myclick(i) {
        GEvent.trigger(gmarkers[i], "click");
 	}   
	   
    if (GBrowserIsCompatible()) {
      // arrays to hold copies of the markers and html used by the side_bar
      // because the function closure trick doesnt work there
      var gmarkers = [];
      function resizeMap( markers ) {
    	    // Create new bounds object
    	    var bounds = new GLatLngBounds();
    	    // Loop through the points, extending the bounds as necessary
    	    for (var i=0; i<markers.length; i++) {
    			      bounds.extend(markers[i].getPoint());
    	    }
    	    // Find the centre of the new bounds
    	    var lat = bounds.getSouthWest().lat() + ((bounds.getNorthEast().lat()- bounds.getSouthWest().lat()) / 2);
    	    var lon = bounds.getSouthWest().lng() + ((bounds.getNorthEast().lng()- bounds.getSouthWest().lng()) / 2);
    	    // Get the bounds zoom level

    	    var zoom = Math.min(6,venues_map.getBoundsZoomLevel(bounds));
    	    // Change the map to the new bounds values
    	    venues_map.setCenter(new GLatLng(lat, lon), zoom);
      }	
      // A function to create the marker and set up the event window
      function createMarker(point,html) {
        var marker = new GMarker(point);
        GEvent.addListener(marker, "click", function() {
          marker.openInfoWindowHtml(html);
        });
        // save the info we need to use later for the side_bar
        gmarkers.push(marker);
        // add a line to the side_bar html
         return marker;
      }
      // This function picks up the click and opens the corresponding info window
    
      // create the map
      venues_map = new GMap2(document.getElementById("VenuesMap"));
      venues_map.addControl(new GLargeMapControl());
      venues_map.addControl(new GMapTypeControl());
      venues_map.setCenter(new GLatLng( 43.907787,-79.359741), 8);
      // add the points 
      <?php foreach($venues as $venue):?>   
         <?php if (!empty($venue['Address']['latitude']) && !empty($venue['Address']['longitude'])):?>
	      var point = new GLatLng(<?php echo $venue['Address']['latitude']; ?>,<?php echo $venue['Address']['longitude']; ?>);
	      var marker = createMarker(point,"<?php echo $venue['Venue']['name']; ?>");
	      venues_map.addOverlay(marker);
	     <?php endif;?> 
      <?php endforeach;?>
      
      resizeMap(gmarkers);
      venues_map.checkResize();                 
      // put the assembled side_bar_html contents into the side_bar div
      //document.getElementById("side_bar").innerHTML = side_bar_html;      
    }
    else {
      alert("Sorry, the Google Maps API is not compatible with this browser");
    }
    //]]>
    </script>
    
<table width="50%">
<tr>
	<th>Name</th>
	<th>Phone</th>
	<th>Type</th>
	<th>Address</th>
	<th>City</th>
	<th>State</th>
	<th>Show&nbsp;on&nbsp;map</th>
	<th>Select&nbsp;Venue</th>
</tr>
<?php $i=0;?>
<?php foreach($venues as $venue):?>
    <tr>
	<td><?php echo $venue['Venue']['name']; ?></td>
	<td><?php echo $venue['Venue']['phone']; ?></td>
	<td><?php echo $venue['Venuetype']['name']; ?></td>
	<td><?php echo $venue['Address']['address']; ?></td>
	<td><?php echo $venue['Address']['city']; ?></td>
	<td><?php echo $venue['Address']['state_name']; ?></td>
	<?php if (!empty($venue['Address']['latitude']) && !empty($venue['Address']['longitude'])):?>
		<td><a href="#" onclick="javascript:myclick('<?php echo $i;?>')">Show&nbsp;on&nbsp;map</a></td>
		<?php $i++;?>
	<?php endif;?>
	<?php if (isset($assignmodel) && isset($modelID)):?>
	    <td>
		<a onclick="return confirm('Are you sure you want to reassign venue?');" href="/venues/assignVenue/<?php echo $assignmodel; ?>/<?php echo $modelID; ?>/<?php echo urlencode($venue['Venue']['id']); ?>/">Assign</a>
	    </td>
	<?php else:?>
	    <td>
		<a class="selectVenue" href="javascript:void(0)">Select&nbsp;Venue</a>
		<span class="selectedVenue">selected</span>
		<input type="hidden" class="venueIdValue" value="<?php echo $venue['Venue']['id'];?>"/>
	    </td>
	<?php endif;?>
    </tr>
<?php endforeach;?>
</table>
<script>
    $("a.selectVenue").click(function(){
	var venueId = $(this).nextAll(".venueIdValue").first().val();
	$('#venueId').val(venueId);
	$("a.selectVenue").show();
	$("span.selectedVenue").hide()

	$(this).hide();
	$(this).nextAll(".selectedVenue").show();
    });
</script>
<style>
    .selectedVenue {
	display: none;
    }
</style>