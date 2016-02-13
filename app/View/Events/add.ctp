<script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/date.js"></script>
<!--[if IE]><script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/jquery.bgiframe.js"></script><![endif]-->
<script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/jquery.datePicker.js"></script>
<?php echo $this->Html->css('jquery.tabs'); ?><?php echo $this->Html->script(array('jquery.tabs.min.js')); ?><?php echo $this->element('mce_init', array('name' => 'EventDescription,EventAgreement,EventThankyou,VenueDescription, EventPrize, EventOther')); ?>
<script type="text/javascript">
var errors = "";
var tabs = null;
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

		tabs = $("#tabsmenu").tabs({
			onShow: function(event, ui) {
			    updateTabLayout($('#'+ui.id).index());
			    if (ui.id == "tab-2") {
			      $('#'+ui.id).css("height","100%");
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
		$("#EventAddForm").validate({
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

			"data[Venue][Address][country_id]": {
				required: function(element) {
				return $(":radio[name=data\\[Venue\\]\\[venueUse\\]]").filter(":checked").val() == "create";
			    }
			},
			"data[Venue][Address][address]": {
			    required: function(element) {
				return $(":radio[name=data\\[Venue\\]\\[venueUse\\]]").filter(":checked").val() == "create";
			    }
			},
			"data[Venue][Address][city]": {
			    required: function(element) {
				return $(":radio[name=data\\[Venue\\]\\[venueUse\\]]").filter(":checked").val() == "create";
			    }
			},
			"data[Venue][name]": {
			    required: function(element) {
				return $(":radio[name=data\\[Venue\\]\\[venueUse\\]]").filter(":checked").val() == "create";
			    }
			},
			"data[Venue][id]": {
			    required: function(element) {
				return $(":radio[name=data\\[Venue\\]\\[venueUse\\]]").filter(":checked").val() == "use";
			    }
			},
			"data[Event][max_teams]": "digits",
			"data[Event][timezone_id]": {
				min: 1
				}
		    },
		    messages: {	"data[Event][name]": {required:"Please type event name."},
		    "data[Venue][id]": {required:"Find and select venue"},
			"data[Event][timezone_id]": {min:"Please select a time zone"},
			"data[Event][start_date_]": {required:"Please choose event start date."},
			"data[Event][end_date_]": {required:"Please choose event end date."},
			"data[Venue][searchname]":   {required:"Please type venue name and choose existing venue."},
			"data[Venue][name]": {required:"Please type venue name."},
			"data[Venue][Address][country_id]": {required:"Please type venue country"},
			"data[Venue][Address][address]": {required:"Please type venue address."},
			"data[Venue][Address][city]": {required:"Please type venue city."}

		    }
		}

	    );

	    //EOF validation
	    $('.date-pick').datePicker({clickInput:true}).dpSetStartDate('01/01/2007').click(function(){$(this).attr("value",'')});

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

	    $('#submit').hide();
	    $('.prev-button').hide();
	    tabs.triggerTab(1);

	});
	//EOF ready

	function changeTab(id) {
		$("a[href='" + id + "']").click();
	    //updateTabLayout(index);
	    return false;
	}

	function updateTabLayout(index) {
	    $('.control').show();
	    var tabsCount = $('.tabs-nav li').length;
	    if (index == tabsCount) {
		$('.next-button').hide();
		$('#submit').show();
	    } else {
		$('#submit').hide();
	    }
	    if (index == 1) {
		$('.prev-button').hide();
	    }
	}

	function getPrevTabID() {
		return $('.tabs-selected').prev().children().attr("href");
	}

	function getNextTabID() {
		return $('.tabs-selected').next().children().attr("href");
	}

	function signup_notice() {
        <?php if (empty($accessApprove)):?>
		alert('Please note: you cannot take payments for your event through BPONG.COM');
		<?php endif;?>
		return true;
	}

</script>
<?php
    $defaultText = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed congue felis non lectus fermentum tempus. Aliquam aliquet justo quis nulla placerat eleifend. Phasellus a mauris justo, at malesuada ante. Morbi hendrerit risus ut eros posuere adipiscing. Nullam condimentum ante non lorem pulvinar quis mattis ipsum mollis. Aliquam ac enim at justo accumsan egestas. In hac habitasse platea dictumst. Donec at sodales arcu. Donec rhoncus magna ut urna dignissim non laoreet velit pharetra. Aliquam eu nunc mauris."
?>


    <h2>Add Event</h2>
  <div class="tournaments form"> <?php echo $this->Form->create('Event',array('enctype'=>"multipart/form-data", 'url' => '/events/add/' . $model . '/' . $modelID)); ?>
    <fieldset class="biglables">
    <div id="tabsmenu">
      <ul>
        <?php if (empty($accessApprove)):?>
        	<li><a href="#tab-1"><span>1. General</span></a></li>
        	<li><a href="#tab-2"><span>2. Venue</span></a></li>
        	<li><a href="#tab-4"><span>3. Features</span></a></li>
        <?php else:?>
        	<li><a href="#tab-1"><span>1. General</span></a></li>
        	<li><a href="#tab-2"><span>2. Venue</span></a></li>
			<li><a href="#tab-3"><span>3. Sign-up</span></a></li>
        	<li><a href="#tab-4"><span>4. Features</span></a></li>
        <?php endif;?>
      </ul>
      <div id="tab-1">
        <!-- GENERAL TAB -->
        <?php echo $this->element('event/general');?> </div>
      <!-- EOF GENERAL TAB -->
      <div id="tab-2">
        <!-- Venue tab -->
        <?php echo $this->element('event/venue', array('edit' => false)); ?> </div>
      <?php if (!empty($accessApprove)):?>
      <!-- SIGNUP TAB -->
      <div id="tab-3"><?php echo $this->element('event/signup');?></div>
      <?php endif;?>
      <!-- Stats TAB -->
      <div id="tab-4"> <?php echo $this->element('event/features', array('defaultText' => $defaultText));?> </div>
      <!-- EOF GENERAL TAB -->
    </div>
    <!-- EOF tabs -->
    </fieldset>
    <div class="clear"></div>
    <div class="heightpad"></div>
    <div class="margbot"></div>

    <style type="text/css">
	.control {
	    background:none repeat scroll 0 0 #525252 !important;
	    border:0 none !important;
	    color:#FFFFFF !important;
	    font-family:Impact,Charcoal,sans-serif !important;
	    font-size:22px;
	    margin-top:0 !important;
	    text-transform:none !important;
	    padding:2px 15px;
	    text-decoration: none !important;
	    float: left;
	    line-height: 29px;
	    margin-right: 10px;
	}

	.control:hover {
	    background: #014294 !important;
	}

	div.submit {
	    background: none;
	    clear: right;
	}
    </style>
    <!--div class="controls"-->
	<a href="#" onclick="return changeTab(getPrevTabID());" class="control prev-button">Prev</a>
	<a href="#" onclick="return changeTab(getNextTabID());" class="control next-button">Next</a>
    <!--/div-->

    <?php echo $this->Form->end(array('name' => 'Submit', 'id' => 'submit')); ?> </div>
