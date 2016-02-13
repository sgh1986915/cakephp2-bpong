<script type="text/javascript">
$(document).ready(function() {

// validate signup form on keyup and submit
	$("#Step4").validate({
				submitHandler: function(form) {
                jQuery(form).ajaxSubmit({
                	beforeSubmit: beforeSubmit,
                    success: showResponseStep4
                });
        }
	});
	//EOF Validation

});

function showResponseStep4(responseText)  {
	$('#Loading').hide();
	$('#BUTTONS').show();

  if (responseText!=""){
  		//$('#error-message').show('slow');
  		alert(responseText);
  } else {
  		var loader='<img src="<?php echo STATIC_BPONG?>/img/ajax_loader_m.gif" border="0">';
		$('#next').html(loader);
		window.location.href = '<?php echo SECURE_SERVER?>/signups/thankyou';

  }
}

function beforeSubmit(){
	$('#BUTTONS').hide();
	$('#Loading').show();
}

function Previos3() {
	var loader='<img src="<?php echo STATIC_BPONG?>/img/ajax_loader_m.gif" border="0">';
	$('#previos').html(loader);
	$('#SignupAjax').load("/signups/step3/<?php echo $modelName."/".$slug ?>",{cache: false});
}
</script>

<?php echo $this->Form->create('Payment',array('id'=>'Step4','name'=>'Step4','url'=>'/signups/freepayment/'.$modelName.'/'.$slug));?>

<div class="p10"><h2>Congratulations!!!</h2><BR>
This package is ABSOLUTELY FREE for you, since you have a magic code.<BR>
Click 'Proceed' to finish your signup. Good Luck!
</div>
<div style="padding:15px 0 0 20px">

        <div style="width:100px; float:left" class="step_previous" id="previos">
  			<input type="button" value="Previous" class="submit" onclick="Previos3();" />  	
  		</div>	
  		<div style="width:100px; float:left" class="step_next" id="next">
			<input type="submit" value="Proceed" class="submit"  id="Proceed" />
		</div>
  		<div id="Loading" style="display:none">
        <?php echo $this->Html->image(STATIC_BPONG.'/img/loading.gif',array('id'=>'StatusLoading')) ?> Processing...
		</div>
</div>
 </form>