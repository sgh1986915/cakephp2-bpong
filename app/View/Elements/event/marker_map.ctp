<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type='text/javascript'>
$(document).ready(function() {
	// initializes a google map with events
	initializeMap();
});



<?php $markersCount = count($markers); ?>
    var locations = [
	<?php
	$i = 0;
	foreach ($markers AS $marker) :
	$i++;
	if (!empty($marker['Venue']['address']) && !empty($marker['Venue']['id']) && (!$marker['Venue']['latitude'] || !$marker['Venue']['longitude'])) {
		$latLon = $this->Address->getLatLon($marker['Venue']['Address'], 1);
		$marker['Venue']['latitude'] = $latLon['lat'];
		$marker['Venue']['longitude'] = $latLon['lon'];
	}

	?>
	    <?php if (!empty($marker['Venue']['id']) && !empty($marker['Venue']['longitude'])) : ?>
		[
		    '<div class="balloon-content">\n\
		    <strong><a href="/event/<?php echo $marker['EventView']['id'];?>/<?php echo addslashes($marker['EventView']['slug']);?>"><?php echo addslashes($marker['EventView']['name']);?>
		    </a></strong>'+
		    '<br/>'+
			'<span class="element-header">Start date: </span>'+
			'<span><?php echo $this->Time->niceShort($marker['EventView']['start_date']); ?></span>'+
		    ''
		    <?php if (strstr($marker['EventView']['end_date'],'0000-00-00') === false): ?>
			+'<br/>'+
			    '<span class="element-header">End date: </span>'+
			    '<span><?php echo $this->Time->niceShort($marker['EventView']['end_date']); ?></span>'+
			''
		    <?php endif; ?>
		    +'<br/>'+
			'<span class="element-header">Venue address: </span>'+
			'<span><?php echo ucwords(strtolower(addslashes($marker['Venue']['address']))); ?></span>'+
			', <span><?php echo ucwords(strtolower($marker['Venue']['city'])); ?></span>'
			<?php if (isset($marker['Venue']['shortname'])): ?>
			    +' <span><?php echo ucwords(strtolower(addslashes($marker['Venue']['shortname']))); ?></span>'
			<?php endif; ?>
		    +'</div>',
		    <?php echo $marker['Venue']['latitude']; ?>,
		    <?php echo $marker['Venue']['longitude']; ?>
		]
	<?php if ($i != $markersCount):?>
		,
	<?php endif; ?>
	    <?php endif; ?>
	<?php endforeach; ?>
    ];


    function initializeMap() {
		var latlng = new google.maps.LatLng(39.031332, -105.03125);
		var infowindow = new google.maps.InfoWindow({zIndex:100});
		var myOptions = {
			zoom: <?php echo $zoom;?>,
			center: latlng,
			mapTypeControlOptions: {
				mapTypeIds: [google.maps.MapTypeId.ROADMAP, google.maps.MapTypeId.SATELLITE]
			},
			navigationControl: true,
			navigationControlOptions: {
				style: google.maps.NavigationControlStyle.LARGE
			},
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		var map = new google.maps.Map(document.getElementById("map"), myOptions);

	    for (var i = 0; i < locations.length; i++) {
			var location = locations[i];
			var point = new google.maps.LatLng( location[1], location[2] );
			createMarker(map, point, location[0], infowindow);
	    }

    }

    function createMarker(map, point, infoContent, infowindow) {
		var image = new google.maps.MarkerImage("<?php echo IMG_MODELS_URL;?>/bpong-marker.png",
			// This marker is 20 pixels wide by 32 pixels tall.
			new google.maps.Size(33, 35),
			// The origin for this image is 0,0.
			new google.maps.Point(0,0),
			// The anchor for this image is the base of the flagpole at 0,32.
			new google.maps.Point(15, 13)
		);

		// Set up our GMarkerOptions object
		var marker = new google.maps.Marker({
			position: point,
			map: map,
			icon: image
		});
		google.maps.event.addListener(marker, "click", function() {
			infowindow.setContent(infoContent);
			infowindow.open(map,marker);
		});
    }
</script>