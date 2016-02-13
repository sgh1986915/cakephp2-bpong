<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type='text/javascript'>
$(document).ready(function() {
    // initializes a google map with events
    initializeMap();
});



<?php $markersCount = count($markers);?>
    var venueLocations = [
    <?php
    $i = 0;
    foreach ($markers AS $marker) :
    $i++;
    if (!empty($marker['VenueView']['address']) && !empty($marker['VenueView']['id']) && (!$marker['VenueView']['latitude'] || !$marker['VenueView']['longitude'])) {
        $latLon = $this->Address->getLatLon($marker['VenueView'], 0);
        $marker['VenueView']['latitude'] = $latLon['lat'];
        $marker['VenueView']['longitude'] = $latLon['lon'];
    }

    ?>
        <?php if (!empty($marker['VenueView']['id']) && !empty($marker['VenueView']['longitude'])) : ?>
        [
            '<div class="balloon-content">\n\
            <strong><a href="/venues/view/<?php echo $marker['VenueView']['slug'];?>/"><?php echo addslashes($marker['VenueView']['name']);?></a></strong>'
            +'<br/>'+
            '<span class="element-header">Address: </span>'+
            '<span><?php echo ucwords(strtolower(addslashes($marker['VenueView']['address']))); ?></span>'+
            ', <span><?php echo ucwords(strtolower($marker['VenueView']['city'])); ?></span>'
            <?php if (isset($marker['VenueView']['shortname'])): ?>
                +' <span><?php echo ucwords(strtolower(addslashes($marker['VenueView']['shortname']))); ?></span>'
            <?php endif; ?>
            +'<br/>'+
            <?php if ($marker['VenueView']['phone']):?>    
            '<span class="element-header">Telephone: </span>'+
            '<span><?php echo $marker['VenueView']['phone'];?></span>'+
            <?php endif;?>
            ''
            <?php if (!empty($marker['Nbplday'])):?>        
            +
                '<span class="element-header">Beer Pong Day: </span>'+
                <?php 
                $d=0; 
                $countDays = count($marker['Nbplday']);
                foreach($marker['Nbplday'] as $nbplday):
                $d++;
                ?>
                '<span><?php echo $nbplday['nbplday'];?><?php if ($countDays != $d){echo ",&nbsp;";}?>'+
                <?php endforeach;?>
            ''    
            <?php endif;?>
            +'<br/>'+    
            '<strong><a href="/venues/view/<?php echo $marker['VenueView']['slug'];?>/" style="color:#D61C20;">Register Now >></a></strong>'            
            +'</div>',
            <?php echo $marker['VenueView']['latitude']; ?>,
            <?php echo $marker['VenueView']['longitude']; ?>
        ]
    <?php if ($i != $markersCount):?>
        ,
    <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>
    ];    
    
    function initializeMap() {
        var latlng = new google.maps.LatLng(<?php echo $centerLat;?>, <?php echo $centerLon;?>);
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

        for (var i = 0; i < venueLocations.length; i++) {
            var location = venueLocations[i];
            var point = new google.maps.LatLng( location[1], location[2] );
            createVenueMarker(map, point, location[0], infowindow);
        }
        
    }

    function createVenueMarker(map, point, infoContent, infowindow) {
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