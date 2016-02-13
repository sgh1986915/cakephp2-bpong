<link type="text/css" rel="stylesheet" href="http://freebaselibs.com/static/suggest/1.3/suggest.min.css" />
<script type="text/javascript" src="http://freebaselibs.com/static/suggest/1.3/suggest.min.js"></script>
<div class="tcenter margbot">
  <input name="data[Venue][venueUse]" type="radio" value="use" OnClick="selectVenueFunction('use');" checked="checked" >
  Use created venue
  <input name="data[Venue][venueUse]" type="radio" value="create" OnClick="selectVenueFunction('create');">
  Create new venue </div>
<!-- use created venue -->
<div id="use_venue">
    <?php echo $this->Form->input('Venue.searchname',array('label' => 'Venue Name','size' => 40,'value'=>"")); ?>
    <?php echo $this->Form->input('Venue.searchcity',array('label' => 'City','size' => 40,'value'=>"")); ?>
    <?php echo $this->Form->input('Venue.searchstate',array('label' => 'State','size' => 40,'value'=>"")); ?>
  <div class="submit"><input id="search-button" type="button" name="Find" value="Search" onclick="findVenue();" class="submit" /></div>
  <div id="ERRORVenue" style="display: none;">Can't find such venue.</div>
  <div id="LoadingVenue" style="display:none"> <?php echo $this->Html->image(STATIC_BPONG.'/img/loading.gif',array('id'=>'VenueLoading')) ?> </div>
  <div id="VenueInformation" style="display: none;">
    <!-- Please do not remove or rename this DIV. It is for AJAX -->
  </div>
  <div id="VenuesMap"></div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$('#VenueSearchname, #VenueSearchcity, #VenueSearchstate').keypress(function(event) {
	    if (event.keyCode == '13') {
		findVenue();
		return false;
	    }
	});
	
	$.ajaxSetup({ cache: false });
	$("#VenueSearchname").autocomplete("<?php echo $this->Html->url(array('controller' => 'Venues', 'action' => 'autocomplete', 'name')) ?>", {
	    width: 320,
	    max: 8,
	    highlight: false,
	    multiple: false,
	    scroll: true,
	    scrollHeight: 300
	});
	<?php /*?>
	$.ajaxSetup({ cache: false });
	$("#VenueSearchcity").autocomplete("<?php echo $this->Html->url(array('controller' => 'Venues', 'action' => 'autocomplete', 'city')) ?>", {
	    width: 320,
	    max: 8,
	    highlight: false,
	    multiple: false,
	    scroll: true,
	    scrollHeight: 300
	});
	<?php */?>
	$.ajaxSetup({ cache: false });
	$("#VenueSearchstate").autocomplete("<?php echo $this->Html->url(array('controller' => 'Venues', 'action' => 'autocomplete', 'provincestate')) ?>", {
	    width: 320,
	    max: 8,
	    highlight: false,
	    multiple: false,
	    scroll: true,
	    scrollHeight: 300
	});
	
	$("#venue_country_id").change(function(){
	  $("#country").val($('#venue_country_id option:selected').text());
	  $("#venue_state").html('<option>Loading...</option>');
	  $.ajaxSetup({cache:false});
	  $.getJSON("/provincestates/getstates",{countryID: $(this).val()}, function(options){
			$("#venue_state").html(options);
			$('#venue_state option:first').attr('selected', 'selected');
		})
	
	});
	$("#venue_state").change(function(){
		  $("#state").val($('#venue_state option:selected').text());
	});		  
});
</script>
<!-- EOF -->
<div id="create_venue"  style="display: none;">
  <!-- Create new venue -->
  <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo GOOGLE_MAP_KEY; ?>" type="text/javascript"></script>
  <?php echo $this->Html->script(array('addresschooser/googlemap.js','addresschooser/addresschooser.js'));?>
  <?php
				echo $this->Form->input ('Venue.name', array('label' => 'Name <span class="red">*</span>'));
				echo $this->Form->input ('Venue.venuetype_id');
				echo $this->Form->hidden ("Venue.id",  array('id'=>'venueId',"value" => ""));
				
			?>
  <h4>Official venue image</h4>
  <?php 	
    echo $this->Form->input ( 'Venue.Image.new', array('type' => 'file'
			 ,'class' => 'file'
			 ,'label' => 'Image' ) );
    echo $this->Form->hidden( 'Venue.Image.new.prop', array( 'value' => 'Personal' ) );
?>
  <div class="clear"></div>
  <!--  MAP  -->
  <input type='hidden' name='country' id='country'/>
  <input type='hidden' name='state' id='state'/>
  <div class="clear"></div>
  <div style="position:relative">
    <div id='map_container'>
      <div id="big_spinner" style="display:none"></div>
      <div id='map' class="venuemap"></div>
      <div id='map_tooltip'></div>
    </div>
  </div>
  <!--  EOF MAP -->
  <!-- <div id="map_canvas1" style="width: 500px; height: 500px;"></div> -->
    <?php    
				 echo $this->Form->hidden ("Venue.Address.latitude",  array('id'=>'lat',"value" => null));
				 echo $this->Form->hidden ("Venue.Address.longitude", array('id'=>'lng',"value" => null));
				 echo $this->Form->input ( 'Venue.Address.country_id', array ('label' => 'Country <span class="red">*</span>', 'type'=> 'select', 'id' => 'venue_country_id','options' => $countries));//, array('onchange' => 'requestAddressChange();') 
				 echo $this->Form->input ( 'Venue.Address.provincestate_id', array ('type'=> 'select','options' => $states, 'label' => 'State or Province', 'id' => 'venue_state'));//, array('onchange' => 'requestAddressChange();')
				 echo $this->Form->input ( 'Venue.Address.address',array('label' => 'Address <span class="red">*</span>', 'id'=>'street'));//, array('onchange' => 'requestAddressChange();')
				 echo $this->Form->input ( 'Venue.Address.city',array('label' => 'City <span class="red">*</span>', 'id'=>'city'));//, array('onchange' => 'requestAddressChange();') 
				 echo $this->Form->input ( 'Venue.Address.postalcode',array('id'=>'zip', 'label' => 'Postal code') );
				 echo $this->Form->input ( 'Venue.web_address' );
				 echo $this->Form->input ( 'Venue.Phone.phone' );
				 echo $this->Form->input ( 'Venue.Phone.type', array ('type'=> 'select','options' => $phonetype));
				 echo $this->Form->input ( 'Venue.description' );
		?>
		<input name="country" id='country' type="hidden" value="">
		<input name="state" id='state' type="hidden" value="">
  <div class="clear"></div>
  <div id="venueactivities">
    <h4>Venue activities</h4>
    <?php foreach($venueactivities as $index => $value): ?>
    <div class="input">
      <label for="VenueactivityVenueactivity<?php echo $index; ?>"><?php echo $value; ?></label>
        <?php 
					echo $this->Form->input("Venueactivity.venueactivity.$index",
	                      array(    'type'   => 'checkbox',
	                                'value'  => $value,
	                                'label'  => false,
	                                'checked'=> false,
	                                'value'  => $index,
	                                'name'   => "data[Venue][Venueactivity][]",
	                                'options'=> false)
	                  );
	                  ?>
    </div>
    <?php endforeach; ?>
  </div>
  <?php echo $this->Html->script('pages/venues/venue.js'); ?>  <div class="clear"></div> </div>


