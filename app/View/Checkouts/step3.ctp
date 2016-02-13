<script type="text/javascript">
$(document).ready(function() {
	<?php if ($payment_error):?>
		alert("<?php echo htmlspecialchars($payment_error);?>");
	<?php endif;?>
	$("#credit_card_payment").validate({
		rules: {
			"x_first_name":"required",
			"x_last_name":"required",
			"x_card_num":"required",
			"x_card_code":"required",
			"x_exp_date":"required"

		},
		messages: {
			"x_first_name":"Please enter your first name",
			"x_last_name":"Please enter your last name",
			"x_card_num"   :"Please enter card number",
			"x_card_code"   :"Please enter card verification number",
			"x_exp_date" : "Please enter card exp. date"
		}
	});
	//EOF Validation

});
function checkAndSendPayment() {
	var payment_method = $('#payment_method').val();
	if ($("#" + payment_method + "_payment").valid()) {
		$('#next').hide();
		$('#submit_loader').show();
		$('#Previous').attr("disabled","disabled");
		$("#" + payment_method + "_payment").submit();
	}
}
function changePaymentMethod () {
	var payment_method = $('#payment_method').val();
	$('.payment_forms').slideUp();
	$('#' + payment_method + '_form').slideDown();
}
</script>
<?php echo $this->element('checkout_header');  ?>


<div class="error" id="error-message" style='display: none;'>Error while Updating.</div>
<div class="users form">
  <fieldset>
  <div class="clear"></div>
  <h3>Credit card information</h3>
  <label>Total Amount</label>


  <b>$<?php echo sprintf("%01.2f",$amount); ?></b>
   <?php echo $this->Form->hidden('payment_method', array('id' => 'payment_method', 'value' => 'credit_card'));?>
	<div id='credit_card_form' class='payment_forms'>
	  <?php echo $this->Form->create('payment',array('id' => 'credit_card_payment','name' => 'payment','url' => $authorizeNetURL));?>
      <?php echo $authorizeNetHiddens;?>
	  <?php /*?>	
      <?php echo $this->Form->input('payment_method', array('id' => 'payment_method', 'label' => 'Payment Method', 'type' => 'select', 'options' => array('credit_card' => 'Credit Card', 'paypal' => 'Paypal'), 'selected' => 'credit_card', 'onchange' => 'changePaymentMethod();'));?>
	  <?php */ ?>


	<?php
	      		echo $this->Form->hidden('od',array('value' => $orderID, 'name' => 'data[Addition][od]'));
	      		echo $this->Form->hidden('ud',array('value' => $userID, 'name' => 'data[Addition][ud]'));

	            echo $this->Form->input('x_first_name',array('name' => 'x_first_name', 'label' => 'First Name','value'=> $bill_fname));
	    		echo $this->Form->input('x_last_name',array('name' => 'x_last_name', 'label' => 'Last Name', 'value'=> $bill_lname));



	    		echo $this->Form->input('x_card_num',array('name' => 'x_card_num', 'label'=>'Credit Card Number', 'value' => ''));
	    		echo $this->Form->input('x_card_code',array('name' => 'x_card_code', 'label'=>'CVN', 'size'=>5, 'value' => ''));
	    		echo $this->Form->input('x_exp_date',array('name' => 'x_exp_date', 'value' => '', 'label' => 'Credit Card Exp. Date', 'type' => 'text', 'id' => 'x_exp_date'));
	    		?>
	</form>
</div>
<?php /*?>
<div id='paypal_form'  class='payment_forms'>
	<?php echo $this->Form->create('paypal_payment',array('id' => 'paypal_payment','name' => 'paypal_payment', 'url' => ''));?>
	<?php
	            echo $this->Form->input('x_first_name',array('name' => 'x_first_name', 'label' => 'Paypal First Name','value'=> $bill_fname));
	    		echo $this->Form->input('x_last_name',array('name' => 'x_last_name', 'label' => 'Paypal Last Name', 'value'=> $bill_lname));
?>
</form>
<?php */ ?>
</div>
<div class="clear"></div>
  </fieldset>
<div class='step_previous'  id='prev'>
  <input type="button" value="Previous" id='Previous' class="submit" onclick="GotoStep(2,'prev');" />
</div>
<div class='step_next'  id='next' style='float:left;'>
  <input type="button" value="Pay" class="submit4" onclick='checkAndSendPayment();'/>
</div>
<div id='submit_loader' style='float:left;display:none;height:18px;text-align:center;color:#094999;'><img src="<?php echo STATIC_BPONG?>/img/ajax_loader_m_green.gif" border="0"> Please wait while.  Order is processing.</div>
<?php echo $this->element('checkout_footer');  ?>