<!-- required plugins -->
<script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/date.js"></script>
<!--[if IE]><script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/jquery.bgiframe.js"></script><![endif]-->

<!-- jquery.datePicker.js -->
<script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/jquery.datePicker.js"></script>
<script type="text/javascript">
$(document).ready(function() {

// validate signup form on keyup and submit
/*Form submit*/

	$("#PackagedetailEditForm").validate({
		submitHandler: function(form) {
                jQuery(form).ajaxSubmit({
                    beforeSubmit: beforeSubmit,
                    success: showResponse
                });
        },
		rules: {
			"data[Packagedetail][price]": {
					required: true,
					number: true
			},
			"data[Packagedetail][price]": {
					required: true,
					number: true
			}

		},
		messages: {
			"data[Packagedetail][deposit]":  {
				number:	"Please enter a valid number.  !"
			},
			"data[Packagedetail][deposit]":  {
				number:	"Please enter a valid number.  !"
			}
		}
	});
	//EOF Validation
	
	//Start data picker initiation
	//added by Edward
	$('.date-pick').datePicker()
				   .dpSetStartDate('01/01/2007')
				   .click(function(){$(this).attr("value",'')});	

});

//After Submit
function beforeSubmit () {
	  $("#form_loader").show();
	  tb_remove();		
}
function showResponse(responseText)  {
  if (responseText=="Error"){
  		$('#error-message').show('slow');
  	  	$("#form_loader").hide();	
  } else {
		$('#PackagesInformation').load("/packages/view/<?php echo $modelName.'/'.$modelID?>",{cache: false},function(){$('#form_loader').hide();});
  }
}

</script>

<div class="phone form">
<h1 class="login"><img src="<?php echo STATIC_BPONG?>/img/logclose.png" id="Close" class="right" style="cursor:pointer; padding:4px 0px 0px 0px;"  onclick="self.parent.tb_remove();" />Edit  package detail</h1>
<div style="background-color:#FFFFFF">
<?php echo $this->Form->create('Packagedetail',array('name'=>'PackagedetailEditForm','id'=>'PackagedetailEditForm','url'=>'/packagedetails/edit/'.$modelName.'/'.$modelID.'/'.$packageID.'/'.$detailID));?>
	<fieldset>
	<div class="error" id="error-message" <?php echo isset($Error)?"":"style='display: none;'" ?>>Error while adding.</div>
	<?php
			echo $this->Form->input('Packagedetail.start_date', array('type'=> 'text', 'size' => 10 ,'class' => 'date-pick dp-applied', 'style' => 'width:100px;', 'readonly' => true));
            echo $this->Form->input('Packagedetail.end_date', array('type'=> 'text', 'size' => 10 ,'class' => 'date-pick dp-applied', 'style' => 'width:100px;',  'readonly' => true));
            echo $this->Form->input('Packagedetail.price', array('style' => 'width:100px;', 'label' => 'Price per person'));
            echo $this->Form->input('Packagedetail.price_team', array('style' => 'width:100px;', 'label' => 'Price per team'));  
            echo $this->Form->input('Packagedetail.deposit', array('style' => 'width:100px;'));
	?>
	</fieldset>
<?php echo $this->Form->end('Submit');?>
<div class="tb_bottom_l"><div class="tb_bottom_r"></div></div>
</div>
</div>