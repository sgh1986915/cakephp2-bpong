<div class="col b63">
	<h3  class='thin_h'>Find an Venue or Tournament Near You & Get Involved</h3>
	<?php echo $this->element("/home/marker_map", array('markers' => $markers, 'zoom' => 4));?>
	<div id="map" style="width: 445px; height: 305px;border:1px solid #CED1D6;float:left;margin-right:10px;"></div>  
	<div class="ltext13">
		<p>
            <form class="cbox">
                <div class="row">
                    Key:
                </div>
				<div class="row">
                    <img style="vertical-align: middle" src="<?php echo IMG_NBPL_LAYOUTS_URL;?>/marker_venue.png"/>
					NBPL Venues
				</div>
				<div class="row">
                    <img style="vertical-align: middle" src="<?php echo IMG_NBPL_LAYOUTS_URL;?>/marker_satellite.jpg"/>
					Satellite Tournaments
				</div>							
			</form>
            <br/><br/>
            <b>About events</b><br />
            BeerPong.com contains the most comprehensive database of amateur and professional competitive beer pong tournaments throughout the world.

		</p>
		<a href="/events" class="bluebtn">SHOW ALL EVENTS</a>
	</div>
	<!-- EOF ltext -->
	<div class="clear"></div>
	<div class="row big">
		<a href="/about_nbpl_bar_program">Run a tournament at your bar</a>&nbsp;|&nbsp;<a href="/recommend_bar">Recommend a bar</a>
		<a href="/events/add" class="right">List your tournament here</a>
	</div>
</div>