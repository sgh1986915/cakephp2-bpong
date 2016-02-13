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
var nbpldays_num = <?php echo intval(count($nbpldays));?>;

function addNewDay() {
	$("#nbplday_loader").show();
	nbpldays_num = nbpldays_num+1;
	$.get('/nbpldays/showNew/' + nbpldays_num, function(data) {
		$("#new_days").append(data);
		$("#nbplday_loader").hide();
		});
	return false;
}

//<![CDATA[
states = new Array();
states = <?php echo $all_states; ?>;
address = "";

user_id =  <?php if (isset($this->request->data['User'][0]) && !empty($this->request->data['User'][0])) {echo $this->request->data['User'][0]['id']; }else {echo VISITOR_USER;}?>;
model_id = <?php echo $this->request->data['Venue']['id']; ?>;
<?php
	  	$lat = $this->request->data['Address']['latitude'];
	  	$lon = $this->request->data['Address']['longitude'];
	  	if ( empty( $lat ) ) {
	  		$lat = "37.4419";
	  	}
	  	if ( empty( $lon ) ) {
	  		$lon = "-122.1419";
	  	}
	  	echo "latitude  = " . $lat . ";";
	  	echo "longitude = " . $lon . ";";
?>
//]]>
</script>
<?php echo $this->Html->script('pages/venues/edit.js');  ?>

<div class="venues"> <?php echo $this->Formenum->create('Venue',array('type' => 'file', 'id' => 'Venueedit', 'url' => $this->Html->url()));
		echo $this->Formenum->hidden("id");
		echo $this->Formenum->hidden("Address.latitude");
		echo $this->Formenum->hidden("Address.longitude");
?>
  <h2><?php echo 'Edit Venue';?></h2>
  <fieldset>
  <?php
		echo $this->Formenum->input('name');
		echo $this->Formenum->input('venuetype_id');
//        if (!empty($accessAprove))
        if (!empty($accessApprove)) {
        	?>

        	<?php
            echo $this->Formenum->input('nbpltype',array('label'=>'NBPL Type'));
		    echo $this->Formenum->input('nbplstartday',array('label'=>'NBPL Start Date'));?>
                	<?php echo $this->element('/venue/nbplday_select');?>
		    <?php
        }
        echo $this->Formenum->input('timezone_id',array('type' => 'select', 
                'label' => 'Time zone','options' => array('0' => 'Select a Time Zone') + $timeZones));
        ?>
  <h3>Official venue image</h3>
  <?php if(!empty($offimage)){ ?>
  <?php echo $this->Html->image(IMG_MODELS_URL.'/thumbs_' . $offimage[0]['Image']['filename'], array( 'border' => '0' )); ?>
  <?php
				echo $this->Formenum->input( 'Image.' . $offimage[0]['Image']['id'], array(	  'type' 	=> 'file'
																			, 'class'	=> 'file'
																			, 'label'	=> 'Image') );
	    		echo $this->Formenum->hidden( 'Image.' . $offimage[0]['Image']['id'] . '.prop', array( 'value' => 'Personal'));
	        } else {
				echo $this->Formenum->input( 'Image.new', array(	  'type' 	=> 'file'
														, 'class'	=> 'file'
														, 'label'	=> 'Image') );
				echo $this->Formenum->hidden( 'Image.new.prop', array( 'value' => 'Personal' ) );
	        }
		?>
  <?php if(!empty($images) ) { ?>
  <h3>Other venue images</h3>
  <?php foreach( $images as $img ) { ?>
  <?php echo $this->Html->image(IMG_MODELS_URL . '/thumbs_' . $img['Image']['filename'], array( 'border' => '0' )); ?>
  <?php
	    		   echo $this->Formenum->input( 'Image.' . $img['Image']['id'], array( 	  'type' 	=>	'file'
	    		   																, 'class'	=>	'file'
	    		   																, 'label'	=>	'Image' ));
	    		   echo $this->Formenum->hidden( 'Image.' . $img['Image']['id'] . '.prop', array( 'value' => 'All' ) );
	            }
	        }
		?>
  <div class="input">
    <label class="show" style="margin-bottom:7px"> Set map location manually </label>
    <div class="check_"> <?php echo $this->Formenum->input( "Map.nochange", array( "type" => "checkbox", "label" => false, "checked" => false));?> </div>
  </div>
  <?php echo $this->Formenum->input('web_address');?>
  <div class="clear"></div>
  <div id="map_canvas"></div>
  <div class="clear"></div>
  <div>
    <?php
		echo $this->Formenum->input('Address.address');
		echo $this->Formenum->input('Address.city');
		echo $this->Formenum->input('Address.provincestate_id', array('selected' => $this->request->data['Address']['provincestate_id'], 'options' => $provincestates));
		echo $this->Formenum->input('Address.postalcode');
		echo $this->Formenum->input('Address.country_id');
?>
  </div>
  <?php echo $this->Formenum->input('description');?>
  <div class="clear"></div>
<!--  
  <div id="venueactivities">
    <h4>Venue activities</h4>
    <?php echo $this->Formenum->error('Venue.Venueactivity','Please check something');?>
    <?php
		//Get checked values
		$checked_array = Set::extract($this->request->data ['Venueactivity'], '{n}.id');
		foreach($venueactivities as $index => $value):
	?>
    <div class="input">
      <label for="VenueactivityId<?php echo $index; ?>"><?php echo $value; ?></label>
        <?php
						echo $this->Formenum->input("Venueactivity.id.$index",
		                      array('type'=>'checkbox',
		                                'value'=>$value,
		                               'label'=>false,
		                                'checked'=>(in_array( $index, $checked_array) ? 'checked' : false),
		                                'value'=>$index,
		                                'name'=>"data[Venueactivity][]",
		                                'options'=>false )
		                  );
			?>
    </div>
    <?php endforeach; ?>
  </div>
  <div id="venuefeatures">
    <h4>Venue features</h4>
    <?php echo $this->Formenum->error('Venue.Venuefeature','Please check something'); ?>
    <?php
		//Get checked values
		$checked_array = Set::extract($this->request->data ['Venuefeature'], '{n}.id');
		foreach($venuefeatures as $index => $value):
	?>
    <div class="input">
      <label class="show" for="VenuefeatureId<?php echo $index; ?>"><?php echo $value; ?></label>
      <div class="check_">
        <?php
				echo $this->Formenum->input("Venuefeature.id.$index",
                      array('type'=>'checkbox',
                                'value'=>$value,
                               'label'=>false,
                                'checked'=>(in_array( $index, $checked_array) ? 'checked' : false),
                                'value'=>$index,
                                'name'=>"data[Venuefeature][]",
                                'options'=>false)
                  );
			?>
      </div>
    </div>
    <?php endforeach;?>
  </div>
  <div id="worktimeblock">
    <h4>Venue worktime</h4>
    <div id="visualcb"> <?php echo $this->Formenum->error('Venue.Worktime','Please check something');	?>
      <table>
        <?php
			//Get checked values from data array for worktime checkboxes
			$checked_array = Set::extract($this->request->data ['Worktime'], '{n}.id');

			$worktempdata =array();
			$timearray = array();
			foreach ($this->request->data['Worktime'] as $index => $value) {
				if( isset($value['VenuesWorktime']) && is_array( $value['VenuesWorktime'] ) ) {
					$timearray[(int)$value['id']]['from_time'] = $value['VenuesWorktime']['from_time'];
					$timearray[$value['id']]['end_time'] = $value['VenuesWorktime']['end_time'];
					if (isset($value['VenuesWorktime']['special_date']))
						$timearray[$value['id']]['special_date'] = $value['VenuesWorktime']['special_date'];
				}
			}
			//After edit data array is chenged and this code is executed
			if (empty($timearray) && isset( $this->request->data['Worktime']['Worktime'] )) {
				foreach ($this->request->data['Worktime']['Worktime'] as $index => $value) {
					$timearray[$value['workday_id']]['from_time'] = $value['from_time'];
					$timearray[$value['workday_id']]['end_time'] = $value['end_time'];
					if (isset($value['special_date']))
						$timearray[$value['workday_id']]['special_date'] = $value['special_date'];
				}
			}

			foreach($worktimes as $index => $value):
		?>
        <tr>
          <td class="ie_btm">
              <label for="WorktimeId<?php echo $index; ?>"><?php echo $value; ?></label>
                <div class="checkbox input"><?php
					echo $this->Formenum->input("Worktime.id.$index",
	                      array('type'=>'checkbox',
	                                'value'		=>	$value,
									'div'		=>	false,
	                                'label'		=>	false,
	                                'checked'	=>	(in_array( $index, $checked_array) ? 'checked' : false),
	                                'value'		=>	$index,
	                                'name'		=>	"data[Worktime][]",
	                                'options'	=>	false )
	                  );
				?></div>
            </td>
          <td class="worktimef"><div id="worktimefield<?php echo $index; ?>" style="display: none;">
              <?php
				if (strtolower ( $value ) == 'special') {
					echo $this->Formenum->input('Worktime.'.$special_day.'.special_date', array(
													  'before' 		=> 'Date&nbsp;&nbsp;'
													, 'label'		=> false
													, 'div' 		=> false
													, 'class' 		=> 'date-pick'
													, 'readonly' 	=> true
													, 'name' 		=> "data[Worktime][Worktime][$index][special_date]"
													, 'disabled' 	=> true
													, 'style'		=> 'font-weight: bold;width: 75px;'
													, 'value'		=> ( isset( $timearray[$index]['special_date'] )? $this->Time->format( 'd/m/Y', $timearray[$index]['special_date'] ): '')
													));
				?>
              <br />
              <div class="clear"></div>
              <div>
                <?php
			  }	echo $this->Formenum->hidden("workday" . $index, array(
								  'name' => "data[Worktime][Worktime][$index][workday_id]"
								, 'value' => $index
								, 'disabled' => true
				));
				echo $this->Formenum->input('Worktime.'.$index.'.from_time', array(
																	  'before' 	=> ' From '
																	, 'label'	=> false
																	, 'div' 	=> false
																	, 'style' 	=> 'width:50px;'
																	, 'name' => "data[Worktime][Worktime][$index][from_time]"
																	, 'disabled' => true
																	, 'value'		=> (isset($timearray[$index]['from_time']))?$timearray[$index]['from_time'] : ''
				));

				echo $this->Formenum->input('Worktime.'.$index.'.end_time', array(
															  'before' 	=> ' To '
															, 'label'	=> false
															, 'div' 	=> false
															, 'style' 	=> 'width:50px;'
															, 'name' => "data[Worktime][Worktime][$index][end_time]"
															, 'disabled' => true
															, 'value'		=> (isset($timearray[$index]['end_time']))?$timearray[$index]['end_time']:''
															));
			?>
              </div>
            </div></td>
        </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
                -->
  </fieldset>
  <div class="clear"></div>
  <div class="submit">
    <input type="submit" value="Submit" />
  </div>
  <div class="heightpad"></div>
  <?php echo $this->Formenum->end(); ?> </div>
  <?php echo $this->element('managers'); ?>
  <!-- Show Phone information -->
<h4>Phones</h4>
<div id="phoneInformation" style="display:none;" class="details">
  <!-- Please don't remove this DIV it's for AJAX -->
</div>
<div class="clear"></div>
<!-- EOF Phone information -->
<?php if( $List ) { ?>
<div class="actions">
<span class="ie6_btn"><?php echo $this->Html->link('List Venues', array('action'=>'index'), array('class'=>'addbtn4')); ?></span></div>
<?php } ?>
<script type="text/javascript">
//FOR EDIT VENUE
specDay = <?php echo $special_day; ?>;
$("#worktimeblock input[@type=checkbox][@checked]").each(
  function() {
		get_id = this.id.split('WorktimeId');
		i = get_id[1];
		$("#worktimefield" + i).toggle();
		if (i == specDay ) {
			$("#Worktime" + i + "SpecialDate").removeAttr("disabled");
		}
		$("#VenueWorkday" + i).removeAttr("disabled");
		$("#Worktime" + i + "FromTime").removeAttr("disabled");
		$("#Worktime" + i + "EndTime").removeAttr("disabled");
		$("#Worktime" + i + "FromTime").timePicker($("#Worktime" + i + "FromTime").val(),4);
		$("#Worktime" + i + "EndTime").timePicker($("#Worktime" + i + "EndTime").val(),4);

  }
)
/*{
													  startTime:new Date(0, 0, 0, 0, 0, 1),
													  endTime:new Date(0, 0, 0, 23, 59, 59),
													  show24Hours:true,
													  separator:':',
													  step: 15}
 */
$("#worktimeblock input:checkbox").each(function(){
		$(this).click(function () {
			get_id = this.id.split('WorktimeId');
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
				$("#Worktime" + i + "FromTime").timePicker('09:00',4);
				$("#Worktime" + i + "EndTime").timePicker('18:00',4);
			}
		});
});
/*
{
													  startTime:new Date(0, 0, 0, 0, 0, 1),
													  endTime:new Date(0, 0, 0, 23, 59, 59),
													  show24Hours:true,
													  separator:':',
													  step: 15}
 */
//EOF
</script>
