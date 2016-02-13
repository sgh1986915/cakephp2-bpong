<?php echo $this->Html->css(array(STATIC_BPONG.'/css/jquery.alerts.css'))
                  .$this->Html->script(array(JS_NBPL.'/jquery.ui.draggable.js',JS_NBPL.'/jquery.alerts.js'))
 ?>
<!-- required plugins -->
<script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/date.js"></script>
<!--[if IE]><script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/jquery.bgiframe.js"></script><![endif]-->
<script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/jquery.datePicker.js"></script>
<?php echo $this->Html->css('jquery.tabs'); ?><?php echo $this->Html->script(array('jquery.tabs.min.js')); ?>
<?php echo $this->element('mce_init', array('name' => 'EventDescription,EventAgreement,EventThankyou, EventPrize, EventOther')); ?>
<script type="text/javascript">
var errors = "";
	$(document).ready(function() {
		<?php
		//Show error messages
		 if (!empty($this->validationErrors['Event'])) {
		 	$msg = "";
		 	foreach ($this->validationErrors['Event'] as $key=>$err) {
		 		$msg.= "-".$err."<BR>";
		 	}
		 	echo "jAlert('{$msg}','Errors');";
		 }
		?>

		$("#tabsmenu").tabs({
		    onShow: function(event, ui) {
			if (ui.id == "tab-2") {
			    $(ui.id).css("height","100%");
			}
		    }
		});



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
		$("#EventEditForm").validate({
			invalidHandler: function(form, validator) {
			      var errs = validator.errorList;
			      var errorMessages = "";
			      $.each(errs, function(index, value) {
			    	  errorMessages = errorMessages +" - "+value.message+"<br>";
			      });
			     if (errorMessages != ""){
			    	 jAlert(errorMessages,"Errors");
			     }
		    },
			rules: {
				"data[Event][name]": "required",
				"data[Event][start_date_]": "required",
				"data[Event][end_date_]": "required",
				"data[Event][max_teams]": "digits",
				"data[Event][timezone_id]": {
					min: 1
					}
			},
			messages: {	"data[Event][name]": {required:"Please type event name."},
						"data[Event][timezone_id]": {min:"Please select a time zone"},
						"data[Event][start_date_]": {required:"Please choose event start date."},
						"data[Event][end_date_]": {required:"Please choose event end date."}
		}

		});
		//EOF validation


		$('#EventSignupRequired').click(function(){
			if ($('#SignDate').attr('class') == 'upper') {
				$('#SignDate').slideDown();
				$('#SignDate').removeClass('upper');
				$('#SignDate').addClass('bottom');
			} else {
				$('#SignDate').slideUp();
				$('#SignDate').removeClass('bottom');
				$('#SignDate').addClass('upper');
			}
		});

		$('.date-pick').datePicker({clickInput:true})
		   .dpSetStartDate('01/01/2007')
		   .click(function(){$(this).attr("value",'')});
	});
	//EOF ready
</script>
<h2>Edit Event</h2>
<div class="tournaments form">
  <fieldset style="padding:0">
  <div id="tabsmenu">
	    <ul>
	      <li><a href="#tab-1"><span>General</span></a></li>
	      <li><a href="#tab-6"><span>Venue</span></a></li>
	      <?php if (!empty($accessApprove)):?>
	      <li><a href="#tab-2"><span>Sign-up</span></a></li>
	      <?php endif;?>
	      <li><a href="#tab-4"><span>Features</span></a></li>
	      <li><a href="#tab-5"><span>Managers</span></a></li>
	      <?php if (!empty($accessApprove)):?>
	      <li><a href="#tab-3"><span>Packages</span></a></li>
	      <?php endif;?>
	      <?php if (!empty($relationshipAccess)):?>
	      <li><a href="#tab-7"><span>Relationship</span></a></li>
	      <?php endif;?>
	    </ul>

	    <div id="tab-1"><?php echo $this->element('event/general', array('edit' => true));?> </div>
	    <?php if (!empty($accessApprove)):?>
	    <div id="tab-2"> <?php echo $this->element('event/signup', array('edit' => true));?> </div>
	    <?php endif;?>
	    <?php if (!empty($accessApprove)):?>
	    <div id="tab-3"><div id="PackagesInformation" class="details"><?php echo $this->requestAction('/packages/view/Event/' . $this->request->data['Event']['id'])?></div></div>
	    <?php endif;?>
	    <div id="tab-4"> <?php echo $this->element('event/features', array('edit' => true));?> </div>
	    <div id="tab-5"> <?php echo $this->element('managers', array('edit' => true));?> </div>
	    <div id="tab-6"> <?php echo $this->element('event/venue', array('edit' => true));?> </div>
	    <?php if (!empty($relationshipAccess)):?>
	    <div id="tab-7"> <?php echo $this->element('event/relationship', array('edit' => true));?> </div>
	    <?php endif;?>
  </div>
  <!-- EOF tabs -->
  </fieldset>
  </form>
</div>
