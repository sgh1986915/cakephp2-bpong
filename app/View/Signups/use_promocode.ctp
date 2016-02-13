<script type="text/javascript">
$(document).ready(function() {
// validate signup form on keyup and submit
	$("#Payment").validate({
		rules: {
			"data[Payment][promocode]":"required"

		}
	});
	//EOF Validation


});

function CheckCoupon(){
	$('#CheckCouponBtn').hide();
	$('#CheckCouponloadbtn').show();
	$("#CouponInformation").hide()
	$.ajaxSetup({cache:false});
	 $.getJSON("/signups/calculatePricebyDiscount",{
	 					coupon:   $('#PaymentPromocode').val(),
						model:    "<?php echo $signupDetails['Signup']['model'];?>",
						model_id: <?php echo $signupDetails['Signup']['model_id'];?>,
						signup_id:<?php echo $signupDetails['Signup']['id'];?>,
						cache: false
	 				},function(result){
					    $("#CouponInformation").html(result['discountInformation']);
					    $("#CouponInformation").show();
						$('#CheckCouponloadbtn').hide();
						$('#CheckCouponBtn').show();

				})

}
</script>

<!-- Signup information -->
<h2>Use promocode</h2>
<div class="left35 p10">
<h2>Signup information</h2>
<div style="width:160px; float:left" class="bord">
<dl class="signup_info">
<dt>Signup to:</dt>   <dd><?php echo $signupDetails['Signup']['model']; ?></dd>
<dt>Signup date:</dt>   <dd><?php echo $this->Time->niceShort($signupDetails['Signup']['signup_date']); ?></dd>
<dt>Status:</dt>   <dd><?php echo $signupDetails['Signup']['status']; ?></dd>
<dt>Paid:</dt>   <dd>$<?php echo sprintf("%.2f",$signupDetails['Signup']['paid']); ?></dd>
<dt>Discount:</dt>   <dd>$<?php echo sprintf("%.2f",$signupDetails['Signup']['discount']); ?></dd>
<dt>Total:</dt>   <dd>$<?php echo sprintf("%.2f",$signupDetails['Signup']['total']); ?></dd>
</dl>
</div>
<!--EOF Signup information -->
<!-- Model Information -->
<div style="width:522px; padding:-10px 20px 30px 20px; margin:0 0 30px 20px; float:left" class="gray_hr">
<?php if (!empty($signupDetails[$signupDetails['Signup']['model']])):?>
	<?php $modelInformation = $signupDetails[$signupDetails['Signup']['model']];?>
	<h3><?php echo $signupDetails['Signup']['model']; ?> information</h3>
	<strong>Name:</strong>  <?php echo $modelInformation['name']; ?><BR>
	<strong>Start:</strong> <?php echo $this->Time->niceShort($modelInformation['start_date']); ?><BR>
	<strong>End:</strong>   <?php echo $this->Time->niceShort($modelInformation['end_date']); ?><BR>
<?php endif;?>
<!-- EOF model information-->
<hr />
<!-- Package information -->
<?php if (!empty($signupDetails['Packagedetails']) && !empty($signupDetails['Package'])):?>
	<h3>Package information</h3>
	<strong>Name:</strong> <?php echo $signupDetails['Package']['name']; ?><BR>
	<?php if (!empty($signupDetails['Package']['people_in_room'])):?>
	<strong>People in room:</strong> <?php echo $signupDetails['Package']['people_in_room']; ?><BR>
	<?php endif;?>
<?php endif;?>
<!-- EOF package information -->
<hr />
<?php echo $this->Form->create('Payment',array('id'=>'Payment','name'=>'Payment','url'=>'/signups/usePromocode/'.$signupDetails['Signup']['id']));?>
<!-- coupon information -->
<h3>Discount coupon</h3>
	  <fieldset>
	  <div id="CouponInformation" stylae="display:none;"> <!-- Don't remove this it's for AJAX'--></div>
  	  <?php echo $this->Form->input('promocode',array('label'=>false,'style'=>'float:left;'));?>
		<div id="CheckCouponBtn" style="width:250px; float:left">
			<input type="button" value="Check coupon" class="submit2" onclick="CheckCoupon();" />
   		</div>
   		<div id="CheckCouponloadbtn" style="display:none;">
     		<img src="<?php echo STATIC_BPONG?>/img/ajax_loader_m.gif" border="0">
     	</div>
      </fieldset>
<!-- EOF coupon information-->

</div>
<div class="clear" style=" height:2px; background-color:#fafafa;"></div>
<div style="padding:15px 0 0 20px">
        <div id="BUTTONS">
  			<input type="submit" value="Proceed" class="submit"  id="Proceed" />
  		</div>
 </div>
</form>

</div>