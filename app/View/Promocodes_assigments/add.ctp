<script type="text/javascript">

$(document).ready(function() {

// validate signup form on keyup and submit
/*Form submit*/
	$('#models').hide();

	$("#PromocodeAssigmentAddForm").validate({
		submitHandler: function(form) {
                jQuery(form).ajaxSubmit({
                    success: showResponse
                });
        }
	});
//EOF Validation



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

});
//////////EOF ready//////

/////fillselect/////////
function generateModels(){
	  $("#PromocodeAssigmentExactModelId").html('<option>Loading...</option>');
	  $.getJSON("/promocodes_assigments/modelsAutocomplete",{model: $("#PromocodeAssigmentModel").val()}, function(options){
			$("#PromocodeAssigmentExactModelId").html(options);
			$('#PromocodeAssigmentExactModelId option:first').attr('selected', 'selected');
		})

}
//
//After Submit
function showResponse(responseText)  {
  if ($.trim(responseText)!=""){
  		$('#error-message').html(responseText);
  		$('#error-message').show('slow');
  } else {
  		$('#PromocodeAssigments').load("/promocodes_assigments/view/<?php echo $promocodeID?>",{cache: false},function(){tb_init('a.thickbox, area.thickbox, input.thickbox');});
  		tb_remove();
  }
}
</script>

<h1 class="login">
  <div class="left">New assigment</div>
  <div class="right"> <img src="<?php echo STATIC_BPONG?>/img/logclose.png" id="Close" onclick="self.parent.tb_remove();" /></div>
</h1>
<div class="whitebg nopad"> <?php echo $this->Form->create('PromocodeAssigment',array('name'=>'PromocodeAssigmentAddForm','id'=>'PromocodeAssigmentAddForm','url'=>'/promocodes_assigments/add/'.$promocodeID));?>
  <div class="error" id="error-message" <?php echo isset($Error)?"":"style='display: none;'" ?>>Error while adding.</div>
  <fieldset>
  <?php echo $this->Form->input('model');?>
  </fieldset>
  <div id="models"> <?php echo $this->Form->radio('model_id',$model_ids,array('label'=>false,'legend'=>false));?>
    <div id="models_id" style="display:none;" > <?php echo $this->Form->input('exact_model_id',array('type' => 'select','label'=>false,'options' => $exact_model_ids));?> </div>
  </div>
  <?php echo $this->Form->end('Submit');?>
  <div class="clear"></div>
</div>
<div class="clear"></div>
<img src="<?php echo STATIC_BPONG;?>/img/whitebot.png" border="0" style='float:left'>