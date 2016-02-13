<!-- required plugins -->
<script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/date.js"></script>
<!--[if IE]><script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/jquery.bgiframe.js"></script><![endif]-->
<!-- jquery.datePicker.js -->
<script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/jquery.datePicker.js"></script>
  <h2>Add new promocode</h2>

  <script type="text/javascript">
function assignUser(id, email)  {
    $("#PromocodeAssignUserId").val(id);
    $("#assign_user_email").html(email);
}	

$(document).ready(function() {
    //MANAGER VALIDATION
	$("#Members").validate({
		submitHandler: function(form) {
                jQuery(form).ajaxSubmit({
                    beforeSubmit: Hidemember,
                    success: showResponse
                });
        },
		rules: {
				"data[Members][email]": {
				  required: true,
				  email: true
			    }
		},
		messages: {
			"data[Members][email]": "Please enter a valid email address"
		}
	});
	function Hidemember(){
		 $("#SubmitButton").hide('slow');
	      $("#ERROR").hide();
	      $("#MemberInformation").hide(function(){ $('#Loading').show();});
	}

	function showResponse(responseText)  {
			 
	   		 $('#Loading').hide('slow'); 
	 		 $("#SubmitButton").show('slow');
	 		 
		  if (responseText==""){  		
		  		$('#ERROR').show('slow');
		  } else {
		  		$('#MemberInformation').html(responseText);
		  		$('#MemberInformation').show('slow');
		  }  
	}	
	
	//EOF MANAGER Validation		
	
	$("#PromocodeAssigmentModel").change(function(){
			if ($(this).val()=="All"){
				$('#models').hide('slow');
			} else {
				$('#models').show();
				generateModels();
			}
	});

	$("#PromocodeAssigmentModelId-1").click(function(){
			$('#models_id').hide('slow');
	});
	$("#PromocodeAssigmentModelId0").click(function(){
			$('#models_id').show('slow');

	});

	 $('.date-pick').datePicker({clickInput:true}).dpSetStartDate('01/01/2007').click(function(){$(this).attr("value",'')});

// validate signup form on keyup and submit
	$("#PromocodeAddForm").validate({
		rules: {
			"data[Promocode][type]": "required",
			"data[Promocode][code]": "required",
			"data[Promocode][number_of_uses]": "required"
		},
		messages: {
		}
	});
	//EOF Validation


});
//EOF ready

function generateCode(){
	$('#Generate').hide();
	$('#generateloadbtn').show();
	$.ajaxSetup({cache:false});
	$.getJSON('/promocodes/generateCode', {cache: false},
			  function(answer){
				  $('#generateloadbtn').hide();
				  $('#Generate').show();
				  $('#PromocodeCode').val(answer);
			  }
	);
}

/////fillselect/////////
function generateModels(){
	  $("#PromocodeAssigmentExactModelId").html('<option>Loading...</option>');
	  $.getJSON("/promocodes_assigments/modelsAutocomplete",{model: $("#PromocodeAssigmentModel").val()}, function(options){
			$("#PromocodeAssigmentExactModelId").html(options);
			$('#PromocodeAssigmentExactModelId option:first').attr('selected', 'selected');
		})

}


</script>
  <?php echo $this->Form->create('Promocode');?>
  <fieldset>
  <?php
		echo $this->Form->input('type');?>
  <?php
		echo $this->Form->input('code');?>
  <input type="button" name="name" id="Generate" value="Generate new code" onclick="generateCode();" class="submit_gray" />
  <div id="generateloadbtn" style="display:none;"> <img src="<?php echo STATIC_BPONG?>/img/ajax_loader_m.gif" border="0"> </div>
  <?php
		echo $this->Form->input('value', array('value' => 0));
		echo $this->Form->input('number_of_uses', array('value' => 1));
		
		echo $this->Form->input('expiration_date', array('type'=> 'text', 'class' => 'date-pick'));
		echo $this->Form->input('threshold', array('value' => 0));
	?>
  <div class="input text">
    <label for="PromocodeNumberOfUses">Assigned User</label>
    <?php echo $this->Form->hidden('assign_user_id');?> <span id='assign_user_email'></span> </div>
  <h3>Assign promocode to</h3>
  <?php echo $this->Form->input('PromocodeAssigment.model',array('style'=>"float:left;"));?>
  <div id="models" style="display:none;"> <?php echo $this->Form->radio('PromocodeAssigment.model_id',$model_ids,array('label'=>false,'legend'=>false));?>
    <div id="models_id" style="display:none;"> <?php echo $this->Form->input('PromocodeAssigment.exact_model_id',array('type' => 'select','label'=>false,'options' => $exact_model_ids));?> </div>
  </div>
  <?php echo $this->Form->input('description',array('cols'=>620)); ?>
  </fieldset>
<div class="clear"></div>
  <?php echo $this->Form->end('Submit');?> <?php echo $this->Form->create('Members',array('id'=>'Members','name'=>'Members','url'=>'/promocodes/findByEmail','class'=>''));?> <br/>
 <div class="heightpad"></div>
 <h4>Find and add user to Promocode</h4>
  <label>Email</label>
  <input id="MemberEmail" type="text" value="" name="data[User][email]"/>
<div class="clear"></div>
<div class="submit">  <input type="submit" value="Find" /></div>
 <div class="heightpad"></div>
  <div id="Loading" style="display:none;text-align:center;"> <?php echo $this->Html->image(STATIC_BPONG.'/img/loading.gif',array('id'=>'StatusLoading')) ?> </div>
  <div id="ERROR" style="display: none;">Can't find such user.</div>
  <div id="MemberInformation" style="display: none;"></div>
  <?php echo $this->Form->end();?> 
