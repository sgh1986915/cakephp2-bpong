<h1 class="login">
  <div class="left">Edit phone information</div>
  <div class="right"> <img src="<?php echo STATIC_BPONG?>/img/logclose.png" id="Close" onclick="self.parent.tb_remove();" /></div>
</h1>
<script type="text/javascript">
$(document).ready(function() {

// validate signup form on keyup and submit
/*Form submit*/

	$("#PhoneEditForm").validate({
		submitHandler: function(form) {
                jQuery(form).ajaxSubmit({
                    success: showResponse
                });
        },
		rules: {
			"data[Phone][type]": "required",
			"data[Phone][phone]": "required"
		},
		messages: {
			"data[Phone][type]": "This field can not be empty!",
			"data[Phone][phone]": "This field can not be empty!"
		}
	});
	//EOF Validation


  // Country click			 
		$("#PhoneCountryId").change(function(){
			  $("#PhoneProvincestateId").html('<option>Loading...</option>');
			  $.getJSON("/provincestates/getstates",{countryID: $(this).val()}, function(options){
					$("#PhoneProvincestateId").html(options);
					$('#PhoneProvincestateId option:first').attr('selected', 'selected');
				})
			  
			  	
			});										
//EOF Country  click
	
});

//After Submit
function showResponse(responseText)  { 
  if (responseText=="Error"){  		
  		$('#error-message').show('slow');
  } else {
  		$('#phoneInformation').load("/phones/view/<?php echo $modelName.'/'.$modelID.'/'.$ownerID ?>",{cache: false},function(){tb_init('a.thickbox, area.thickbox, input.thickbox');});
  		tb_remove();
  }
}

</script>
<div class="whitebg nopad"> <?php echo $this->Form->create('Phone',array('name'=>'PhoneEditForm','id'=>'PhoneEditForm','url'=>'/phones/edit/'.$modelName.'/'.$modelID.'/'.$ownerID.'/'.$phoneID));?>
<fieldset class="narrow">
  <div class="error" id="error-message" <?php echo isset($Error)?"":"style='display: none;'" ?>>Error while editing.</div>
  <?php
            echo $this->Form->input('Phone.type');
            echo $this->Form->input('Phone.phone');
	?>
  </fieldset>
  <?php echo $this->Form->end('Submit');?>
  <div class="clear"></div>
</div>
<div class="clear"></div>
<img src="<?php echo STATIC_BPONG;?>/img/whitebot.png" border="0" style='float:left'>