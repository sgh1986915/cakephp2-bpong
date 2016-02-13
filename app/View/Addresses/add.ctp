<?php if ($label):
  			$URL =  $modelName.'/'.$modelID.'/'.$ownerID.'/'.$label ;
  		 else:
  		    $URL =  $modelName.'/'.$modelID.'/'.$ownerID ;
  		 endif; ?>
<script type="text/javascript">
$(document).ready(function() {

// validate signup form on keyup and submit
/*Form submit*/

	$("#AddressAddForm").validate({
		submitHandler: function(form) {
                jQuery(form).ajaxSubmit({
                    success: showResponse
                });
        },
		rules: {
			"data[Address][country_id]":{min:1},
			"data[Address][address]":"required",
			"data[Address][city]":"required",
			"data[Address][provincestate_id]":{min:1},
			"data[Address][postalcode]":"required"
		},
		messages: {
			"data[Address][country_id]":{min:"This field is required."},
			"data[Address][provincestate_id]":{min:"This field is required."}
		}
	});
	//EOF Validation


  // Country click
		$("#AddressCountryId").change(function(){
			  $("#AddressProvincestateId").html('<option>Loading...</option>');
			  $.getJSON("/provincestates/getstates",{countryID: $(this).val()}, function(options){
					$("#AddressProvincestateId").html(options);
					$('#AddressProvincestateId option:first').attr('selected', 'selected');
				})


			});
//EOF Country  click

});

//After Submit
function showResponse(responseText)  {
  if (responseText=="Error"){
  		$('#error-message').show('slow');
  } else {
  			$('#addressInformation').load("/addresses/view/<?php echo $URL; ?>",{cache: false},function(){tb_init('a.thickbox, area.thickbox, input.thickbox');});
  		tb_remove();
  }
}

</script>

<h1 class="login">
  <div class="left">Add new address</div>
  <div class="right"> <img src="<?php echo STATIC_BPONG?>/img/logclose.png" id="Close" onclick="self.parent.tb_remove();" /></div>
</h1>
<div class="whitebg nopad"> <?php echo $this->Form->create('Address',array('name'=>'AddressAddForm','id'=>'AddressAddForm','url'=>'/addresses/add/'.$URL));?>
  <fieldset class="narrow">
  <div class="error" id="error-message" <?php echo isset($Error)?"":"style='display: none;'" ?>>Error while adding.</div>
  <?php
	        if (!$label)
                echo $this->Form->input('Address.label',array('type' => 'select','label'=>'Label','options' => $labels));
             else
                 echo $this->Form->hidden('Address.label');

            echo $this->Form->input('Address.country_id',array('type' => 'select','label'=>'Country','options' => $countries));
            echo $this->Form->input('Address.address');
            echo $this->Form->input('Address.address2');
            echo $this->Form->input('Address.address3');
            echo $this->Form->input('Address.city',array('label'=>'City'));
            echo $this->Form->input('Address.provincestate_id',array('type' => 'select','label'=>'State','options' => $states));
			echo $this->Form->input('Address.postalcode');
	?>
  </fieldset>
  <?php echo $this->Form->end('Submit');?>
  <div class="clear"></div>
</div>
<div class="clear"></div>
<img src="<?php echo STATIC_BPONG;?>/img/whitebot.png" border="0" style='float:left'>