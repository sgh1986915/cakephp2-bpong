<?php
	//Connect timepicker library
		echo $this->Html->css ( array ('jquery.timepickr' ) );
		echo $this->Html->script ( 'timepicker' );
	//Connect datepicker library
		echo $this->Html->css ( array ('ui.datepicker2' ) );
		echo $this->Html->script('ui.datepicker2');
	//find special date field and get his ID 
	//	$special_day = array_search('Special', $worktimes);
		echo $this->element('mce_init', array('name' => 'VenueDescription'));
?>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo GOOGLE_MAP_KEY; ?>" type="text/javascript"></script>
<script type="text/javascript">
//<![CDATA[ 
	//Array of state's shortname (needed in google map location)
	states = new Array();
	states = <?php echo $all_states; ?>;
	address = "";
//]]> 
</script>
<?php	echo $this->Html->script('pages/venues/add.js'); ?>

<div class="venues">
  <?php
	    echo $this->Form->create ('Venue', array ('type' => 'file', 'id' => 'Venueadd', 'url' => '/venues/add/' . $model . '/' . $modelID) );
		echo $this->Form->hidden("Address.latitude",  array("value" => null));
		echo $this->Form->hidden("Address.longitude", array("value" => null));
?>
  <h2>Add Venue</h2>
  <fieldset>
  <?php
			echo $this->Form->input ( 'name' );?>
			<?php echo $this->Form->input ( 'venuetype_id' );
            if (!empty($accessApprove)) {
                echo $this->Formenum->input('nbpltype',array('label'=>'NBPL Type'));
                echo $this->Formenum->input('nbplstartday',array('label'=>'NBPL Start Date'));?>
                        	<?php echo $this->element('/venue/nbplday_select');?>
            <?php    
            }       
            echo $this->Formenum->input('timezone_id',array('type' => 'select', 
                'label' => 'Time zone','options' => array('0' => 'Select a Time Zone') + $timeZones));

            
            
             ?>
  <h3>Official venue image</h3>
    	    <?php echo $this->Form->input( 'Image.new', array(	  'type' 	=> 'file'
    	    										, 'class'	=> 'file'
    	    										, 'label' 	=> 'Image' ) );
    	    echo $this->Form->hidden( 'Image.new.prop', array( 'value' => 'Personal' ) );
        ?>
  <div class="box_check">
    <label class="show">Set map location manually</label>
    <div class="check_"><?php echo $this->Form->input('Map.nochange',array('type'=>'checkbox','label'=>false));?></div>
  </div>
  <div class="clear"></div>
  <div id="map_canvas"></div>
  <div class='clear'></div>
    <?php
    echo $this->Form->input ( 'Address.country_id');//, array('onchange' => 'requestAddressChange();') 
    echo $this->Form->input ( 'Address.provincestate_id');//, array('onchange' => 'requestAddressChange();') 
	echo $this->Form->input ( 'Address.address');//, array('onchange' => 'requestAddressChange();')
	echo $this->Form->input ( 'Address.city');//, array('onchange' => 'requestAddressChange();') 
	echo $this->Form->input ( 'Address.postalcode' );
	echo $this->Form->input ( 'web_address' );
	echo $this->Form->input ( 'Phone.phone' );
	echo $this->Form->input('Phone.type', array ('type'=> 'select','options' => $phonetype));
	?>
  <div class="clear"></div>
  <?php echo $this->Form->input ( 'description' );?>
  <div class="box_check">
    <label class="show">Are you owner of a Venue?</label>
    <div class="check_"><?php echo $this->Form->input('Manager.is_owner', array('type'=>'checkbox','label'=>false));?></div>
  </div>
  <div class="clear"></div>
<!--  
  <div id="venueactivities">
    <h4>Venue activities</h4>
    <?php
	echo $this->Form->error ( 'Venue.Venueactivity', 'Please check something' );
	?>
    <?php foreach($venueactivities as $index => $value): ?>
    <div  class="input">
      <label for="VenueactivityVenueactivity<?php echo $index; ?>"><?php echo $value; ?></label>
      <div class="check_">
        <?php 
				echo $this->Form->input("Venueactivity.venueactivity.$index",
                      array('type'=>'checkbox',
                                'value'=>$value,
                               'label'=>false,
                                'checked'=>false,
                                'value'=>$index,
                                'name'=>"data[Venueactivity][Venueactivity][]",
                                'options'=>false)
                  );
                  ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <div id="venuefeatures">
    <h4>Venue features</h4>
    <?php
	echo $this->Form->error ( 'Venue.Venuefeature', 'Please check something' );
	?>
    <?php foreach($venuefeatures as $index => $value): ?>
    <div  class="input">
      <label class="show" for="VenuefeatureVenuefeature<?php echo $index; ?>"><?php echo $value; ?></label>
      <div class="check_">
        <?php 
				echo $this->Form->input("Venuefeature.venuefeature.$index",
                      array('type'=>'checkbox',
                                'value'=>$value,
                               'label'=>false,
                                'checked'=>false,
                                'value'=>$index,
                                'name'=>"data[Venuefeature][Venuefeature][]",
                                'options'=>false)
                  );
			?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <div id="worktimeblock">
    <h4>Venue worktime</h4>
    <div id="visualcb">
      <?php
		echo $this->Form->error ( 'Venue.Worktime', 'Please check something' );
	?>
      <table class="vertal">
        <?php foreach($worktimes as $index => $value): ?>
        <tr>
          <td class="ie_btm"><div class="box_check">
              <label for="WorktimeWorktime<?php echo $index; ?>"><?php echo $value; ?></label>
              <div class="check_">
                <?php 
					echo $this->Form->input("Worktime.worktime.$index",
	                      array('type'=>'checkbox',
	                                'value'		=>	$value,
	                                'label'		=>	false,
	                                'checked'	=>	false,
	                                'value'		=>	$index,
	                                'name'		=>	"data[Worktime][Worktime][]",
	                                'options'	=>	false )
	                  );
				?>
              </div>
            </div></td>
          <td class="worktimef"><div id="worktimefield<?php echo $index; ?>" style="display: none;">
              <?php 
				if (strtolower ( $value ) == 'special') {
					echo $this->Form->input ( 'Worktime.' . $index . '.special_date', array (
																						  'before'		=> 'Date&nbsp;&nbsp;'
																						, 'label' 		=> false
																						, 'div' 		=> false
																						, 'readonly' 	=> true
																						, 'style' 		=> 'width:70px;'
																						, 'name' 		=> "data[Worktime][Worktime][$index][special_date]"
																						, 'class' 		=> 'date-pick'
																						, 'disabled'	=> true ) );
	?>
              <br />
                <?php 
				}
				echo $this->Form->hidden ( "workday" . $index, array ('name' => "data[Worktime][Worktime][$index][workday_id]", 'value' => $index, 'disabled' => true ) );
				echo $this->Form->input ( 'Worktime.' . $index . '.from_time', array ('before' => 'From&nbsp;', 'label' => false, 'div' => false, 'style' => 'width:50px;', 'name' => "data[Worktime][Worktime][$index][from_time]", 'disabled' => true ) );
				echo $this->Form->input ( 'Worktime.' . $index . '.end_time', array ('before' => '&nbsp;To&nbsp;', 'label' => false, 'div' => false, 'style' => 'width:50px;', 'name' => "data[Worktime][Worktime][$index][end_time]", 'disabled' => true ) );
			?>
            </div></td>
        </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
                -->
  <script>
//FOR EDIT VENUE
/*specDay = <?php echo $special_day; ?>;

$("#worktimeblock input:checkbox").each(function(){
		$(this).click(function () {
			get_id = this.id.split('WorktimeWorktime');
			i = get_id[1] ;
			$("#worktimefield" + i).toggle();

			if (!$(this).is(':checked')) {
				// Disable
	 			if (i == specDay ) {
	 				$("#Worktime" + i + "SpecialDate").attr("disabled", "disabled");
	 			}
	 			$("#VenueWorkday" + i).attr("disabled", "disabled");
	 			$("#Worktime" + i + "FromTime").attr("disabled", "disabled");
	 			$("#Worktime" + i + "EndTime").attr("disabled", "disabled");
			} else {
				// Enable
	 			if (i == specDay ) {
	 				$("#Worktime" + i + "SpecialDate").removeAttr("disabled");
	 			}
				$("#VenueWorkday" + i).removeAttr("disabled");
				$("#Worktime" + i + "FromTime").removeAttr("disabled");
				$("#Worktime" + i + "EndTime").removeAttr("disabled");
				$("#Worktime" + i + "FromTime").timePicker('',4);
				$("#Worktime" + i + "EndTime").timePicker('',4);
													  
			}
		});
});   */
</script>
  </fieldset>
  <div class="heightpad"></div>

    <div class="submit">
      <input type="submit" value="Submit" />
    </div>
 <div class="heightpad"></div>
 
  <?php echo $this->Form->end(); ?> 
</div>
<?php if( $List ) {?>
<div class="right"> <span class="ie6_btn"><?php echo $this->Html->link('List Venues', array('action'=>'index'), array('class'=>'addbtn4')); ?></span> </div>
<?php } ?>
