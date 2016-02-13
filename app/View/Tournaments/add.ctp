<!-- required plugins -->
<script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/date.js"></script>
<!--[if IE]><script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/jquery.bgiframe.js"></script><![endif]-->

<!-- jquery.datePicker.js -->
<script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/jquery.datePicker.js"></script>
<div class="toleft">
<?php echo $this->element('mce_init', array('name' => 'TournamentDescription,TournamentAgreement,TournamentThankyou')); ?>

<?php echo $this->Html->css('jquery.tabs'); ?>
<?php echo $this->Html->script(array('jquery.tabs.min.js')); ?>

<script type="text/javascript">
$(document).ready(function() {
	$('#tabsmenu').tabs();
	
	//Add new methode - Max people per team should be greater then min people per team
	jQuery.validator.addMethod("GreaterThen", function(value, element,params) {
				
				if (value != "" && $(params).val() != "") {
					if (parseInt(value) < parseInt($(params).val())) {					
						return false;
					} else {
						return true;
					}
					
				} else {
					return true;
				}

	}, "Max people per team should be greater than Min people.");

	
// validate signup form on keyup and submit
	$("#TournamentAddForm").validate({
		rules: {
			"data[Tournament][name]": "required",
			"data[Tournament][shortname]": "required",
			"data[Tournament][min_people_team]": {
													range: [1, 20]
			},
			"data[Tournament][max_people_team]": {
													range: [1, 20],
													GreaterThen :"#TournamentMinPeopleTeam"
			}
		},
		messages: {
			"data[Tournament][name]": "Please enter name",
			"data[Tournament][shortname]": "Please enter short  name"
		}
	});
	//EOF Validation

	//Start data picker initiation
	//added by Edward
	$('.date-pick').datePicker()
				   .dpSetStartDate('01-01-2007')
				   .click(function(){$(this).attr("value",'')});


});
//EOF ready
</script>

<div class="tournaments form">
<?php echo $this->Form->create('Tournament');?>
	<fieldset>
 		<legend>Add Tournament</legend>
<div id="tabsmenu">
    <ul>
        <li><a href="#fragment-1"><span>General</span></a></li>
        <li><a href="#fragment-2"><span>Signup</span></a></li>
    </ul> 		

<div id="fragment-1">
		 <?php
		    echo $this->Form->input('name', array('size' => 70));
		    echo $this->Form->input('shortname');
	    ?>
		<div class="text_descript">	<?php echo $this->Form->input('description');?></div>
		<?php 
		    echo $this->Form->input('start_date',array('type'=> 'text', 'size' => 10 ,'class' => 'date-pick dp-applied', 'readonly' => true));
		    echo $this->Form->input('end_date',array('type'=> 'text', 'size' => 10, 'class' => 'date-pick dp-applied', 'readonly' => true,  'error' => 'End date must be later then start date'));
		    echo $this->Form->input('url', array('size' => 40));
		?>
</div> 
<div id="fragment-2">
		<div class="text_descript"><?php echo $this->Form->input('agreement');?></div>
		<div class="text_descript"><?php echo $this->Form->input('thankyou');?></div>

        <div style="width:150px; height:20px">
        	<label>Signup required</label>
        	<div style="float:right; width:18px; margin:-9px 0"><?php echo $this->Form->input('signup_required',array('label'=>false));?></div>
        </div>
        <div style="width:150px; height:25px">
        	<label>Shown on front</label>
        	<div style="float:right; width:18px; margin:-9px 0"><?php echo $this->Form->input('shown_on_front',array('label'=>false));?></div>
        </div>        
	    <div class="box_check">
		  <label>Multi team</label>
		  <div class="check_"><?php echo $this->Form->checkbox('multi_team', array ('label'=>false) );?></div>
        </div>
        
		 <?php echo $this->Form->input('min_people_team', array('size' => 10,'label'=>'Min people per team'));?>
		 <?php echo $this->Form->input('max_people_team', array('size' => 10,'label'=>'Max people per team'));?>
		 <?php echo $this->Form->input('finish_signup_date',array('type'=> 'text', 'size' => 10, 'class' => 'date-pick dp-applied', 'readonly' => true));?>
		
		<div style="width:150px">
			<label style="float:left">Choosing rooms</label><div style="width:17px; float:right; padding:0px; margin:-8px 0"><?php echo $this->Form->input('is_room', array ('type'=>'checkbox','label'=>false) );?></div>
    	</div>
		
</div>	
	</fieldset>
<?php echo $this->Form->end('Submit');?>
</div>
</div>