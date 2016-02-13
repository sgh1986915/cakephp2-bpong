<?php if (!isset($this->request->data['Question']['Option'])) $this->request->data['Question']['Option'] = array();?>
<script type="text/javascript">
var packagesTeamsAbility = [];
<?php foreach ($packages as $package) { ?>
	packagesTeamsAbility[<?php echo $package['id'];?>] = <?php if ($package['price_team'] > 0 && $package['price_team'] > $package['price']) {echo '1';} else {echo '0';}?>;	
<?php } ?>
$(document).ready(function() {

$("select").change(function() {
  $("#Step3").valid();
});

// validate signup form on keyup and submit
	$("#Step3").validate({
				submitHandler: function(form) {

				if (checkQuestions()){
	                jQuery(form).ajaxSubmit({
	                    beforeSubmit: beforeSubmit,
	                    success: showResponseStep3
	                });
				}
        },
		rules: {
			"data[Package][id]": {required: true}
		},
		messages: {
			"data[Package][id]":{
				required: "Please choose  a package"
			}
		}
	});
	//EOF Validation


});

function beforeSubmit(){
	var loader = showNextLoading();
	$('#next').html(loader);
}
function checkPaymentType() {
	if ($('#for_team').val() == 'not_specified') {
    	var packageID = $("input[name='data[Package][id]']:checked").val();
        var currentPromocode = $('#PackagePromocode').val(); //if theres a promocode of any kind, individual signup...
        if (packagesTeamsAbility[packageID] >0 && currentPromocode.length == 0) { 
			tb_show('Test','/packages/selectType/' + packageID +'?&amp;inlineId=EditPackage&amp;height=500&amp;width=400&amp;modal=true;');
		} else {
			$('#for_team').val(0);
			$('#Step3').submit();
		}
	} else {
        $('#Step3').submit();
    }
	return false;	
}
function changePackage() {
	$('#for_team').val('not_specified');
}
function showResponseStep3(responseText)  {
  if (responseText!=""){
  		$('#error-message').show('slow');
  		alert(responseText);
  } else {
		$.ajaxSetup({cache:false});
  		$('#SignupAjax').load("/signups/step4/<?php echo $modelName."/".$slug ?>",{cache: false});
  }
}

function Previos2() {
	var loader = showPrevLoading();
    $('#previos').html(loader);
    $.ajaxSetup({cache:false});
	$('#SignupAjax').load("/signups/step2/<?php echo $modelName."/".$slug ?>",{cache: false});
}

// validation for Questions
function checkQuestions (){
	result = true;
	<?php if (!empty($questions)):?>
		<?php foreach ($questions as $question): ?>
			  <?php if ($question['Question']['is_required']==1):?>
			  			<?php if ($question['Question']['type']=="Select"):?>
								if ($('.Question<?php echo $question['Question']['id']?>').val()==""){
									jQuery('.Question<?php echo $question['Question']['id']?>').addClass("error");
									jQuery('<?php echo "#QuestionError".$question['Question']['id']?>').show();
									result = false;
								} else {
									jQuery('.Question<?php echo $question['Question']['id']?>').removeClass("error");
									jQuery('<?php echo "#QuestionError".$question['Question']['id']?>').hide();
								}
						<?php else:?>
								if($('.Question<?php echo $question['Question']['id']?>:checked').length==0){
									jQuery('<?php echo "#QuestionError".$question['Question']['id']?>').show();
									result = false;
								}else{
									jQuery('<?php echo "#QuestionError".$question['Question']['id']?>').hide();
								}
			  			<?php endif;?>
			  <?php endif;?>
		<?php endforeach;?>
	<?php endif;?>

	return result;

}

//function for checking coupon
function CheckCoupon(){

	$('#CheckCouponBtn').hide();
	$('#CheckCouponloadbtn').show();
	$("#CouponInformation").hide()

	 $.getJSON("/promocodes/checkCoupon",{
	 					coupon:   $('#PackagePromocode').val(),
						model:    "<?php echo $modelName;?>",
						model_id: <?php echo $id;?>
	 				}, function(result){
					    $("#CouponInformation").html(result);
					    $("#CouponInformation").show();
						$('#CheckCouponloadbtn').hide();
						$('#CheckCouponBtn').show();

				})

}

function promoEnter(myfield,e) {
    var keycode;
    if (window.event) keycode = window.event.keyCode;
    else if (e) keycode = e.which;
    else return true;
    
    if (keycode == 13) {
    	CheckCoupon();
       return false;
       }
    else {
       return true;
    }   
}
</script>
<div id="EditPackage"></div>

<?php echo $this->Form->create('Package',array('id'=>'Step3','name'=>'Step3','url'=>'/signups/step3/'.$modelName.'/'.$slug));?>
<div class="users form" style="border:#ccc 1px dotted; padding:20px">
<h3 style="padding-left:20px">Choose the package</h3>
	  <fieldset>
  	  <?php echo $this->Form->radio('id', Set::combine($packages, '{n}.id', '{n}.info'),array('onchange' => 'changePackage();', 'separator' =>'<br /><br />', 'legend'=>false,'label'=>false,'value'=>$this->request->data['Package']['id']));?>
      <?php echo $this->Form->input('Signup.for_team', array('type'=>'hidden', 'default' => 'not_specified', 'id' => 'for_team'));?>
      </fieldset>
<br/>
<h3 style=" padding-left:20px">Promocode or Discount coupon</h3>
	  <fieldset>
	  <div id="CouponInformation" style="display:none;"><!-- Don't remove this it's for AJAX'--></div>
  	  <?php echo $this->Form->input('promocode',array('label'=>false,'style'=>'float:left;', "onKeyPress"=>"return promoEnter(this,event)"));?>
		<div id="CheckCouponBtn" style="width:250px; float:left">
			<input type="button" value="Check coupon" class="submit2" onclick="CheckCoupon();" />
   		</div>
   		<div id="CheckCouponloadbtn" style="display:none;">
     		<img src="<?php echo STATIC_BPONG?>/img/ajax_loader_m.gif" border="0">
     	</div>
      </fieldset>


<?php if (!empty($questions)):?>
	 <h3 style=" padding-left:20px">Answer the questions</h3>
	<fieldset>
	<?php echo $this->element('questions');?>
	</fieldset>
<?php endif;?>
<div class='clear'> </div>
</div>

<div style="padding:15px 0 0 20px">
	<div id="previos" class='step_previous' style="width:100px; float:left">
		<input type="button" value="Previous" class="submit" onclick="Previos2();" />
   </div>
	<div id="next" class='step_next' style="width:100px; float:left">
		<input type="button" value="Next" class="submit" style='margin-top:0px;' onclick='return checkPaymentType();' />
	</div>
</div>
</form>
