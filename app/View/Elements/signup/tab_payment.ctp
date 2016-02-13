<script type="text/javascript">
$(document).ready(function() {
	<?php if (!empty($payment_error)):?>
	alert("<?php echo htmlspecialchars($payment_error);?>");
	<?php endif;?>

	<?php if (!empty($addressID)):?>
	$('#AddressAddressesIds > option[value="<?php echo $addressID;?>"]').attr("selected","selected");	
	$('#AddressAddressesIds').change();		
	<?php endif;?>
	
	// Country click
	$("#country_id").change(function(){
		  $("#state_id").html('<option>Loading...</option>');
		  $.getJSON("/provincestates/getstates",{countryID: $(this).val()}, function(options){
				$("#state_id").html(options);
				$('#state_id option:first').attr('selected', 'selected');
			})


		});
//EOF Country  click
//validate signup form on keyup and submit
$("#Payment").validate({
	rules: {
		"country_id":"required",
		"x_address":"required",
		"x_city":"required",
		"state_id":"required",
		"x_zip":"required",
		"x_first_name":"required",
		"x_last_name":"required",
		"x_card_num":"required",
		"x_card_code":"required",
		"x_exp_date":"required",
		"x_phone":"required"
	},
	
	messages: {
		"country_id": "This field is required.",
		"state_id":"This field is required.",
		"x_first_name":"Please enter your first name",
		"x_last_name":"Please enter your last name",
		"x_card_num"   :"Please enter card number",
		"x_card_code"   :"Please enter card verification number",
		"x_exp_date" : "Please enter card exp. date",
		"x_phone" : "Please enter phone"
	}
});
//EOF Validation
});

function submitPayment() {	
	$('#x_country').val($('#country_id option:selected').text());
	$('#x_state').val($('#state_id option:selected').text());

	if ($("#Payment").valid()) {
		$('#BUTTONS').hide();
		$('#Loading').show();					
		$('#Payment').submit();																	
	}
	return false;	
}

function selectAddress(addressID)  {
	var phone = $('#x_phone').val();
	$('#billing_address input').val('Loading...');
  	$('#x_phone').val(phone);
	if(addressID==0){
		  	$('#x_address').val('');
		  	$('#x_address2').val('');
		  	$('#x_city').val('');
		  	$('#x_zip').val('');
		  	$('#country_id > option[value="0"]').attr("selected","selected");
			$('#state_id > option[value="0"]').attr("selected","selected");

	} else {
			$.ajaxSetup({cache:false});
		    $.getJSON('/Addresses/getAddressJson/User/<?php echo $signupDetails['User']['id']; ?>/'+addressID, {cache: false},
			function(address){
					// select state
					$('#state_id').html('<option>Loading...</option>');
					$.getJSON("/provincestates/getstates",{countryID: address['Address']['country_id']}, function(options){
							$('#state_id').html(options);
							$('#state_id option').attr('selected', '');
							$('#state_id > option[value="'+address['Address']['provincestate_id']+'"]').attr("selected","selected");
					})

				  	$('#country_id').val(address['Address']['country_id']);
				  	$('#x_address').val(address['Address']['address']);
				  	$('#x_address2').val(address['Address']['address2']);
				  	$('#x_city').val(address['Address']['city']);
				  	$('#x_zip').val(address['Address']['postalcode'])


			});
	}
}
//function for checking coupon
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
	 				}, function(result){
					    $("#CouponInformation").html(result['discountInformation']);
					    $("#CouponInformation").show();
						$('#CheckCouponloadbtn').hide();
						$('#CheckCouponBtn').show();
						if (result['priceInformation']!='')
							$("#topay").html(result['priceInformation']);
						if (result['isrefund']>0) {
							$("#AddressCountryId").attr('disabled', 'disabled'); 
							$("#AddressAddress").attr('disabled', 'disabled'); 
							$("#AddressCity").attr('disabled', 'disabled'); 
							$("#AddressProvincestateId").attr('disabled', 'disabled'); 
							$("#AddressPostalcode").attr('disabled', 'disabled'); 
							$("#UserFirstname").attr('disabled', 'disabled'); 
							$("#UserLastname").attr('disabled', 'disabled'); 
							$("#CardNumber").attr('disabled', 'disabled'); 
							$("#CardCvv").attr('disabled', 'disabled'); 
							$('#CreditcardInformation').hide();																					
						} else {
							$('#AddressCountryId').removeAttr('disabled'); 
							$('#AddressAddress').removeAttr('disabled');
							$('#AddressCity').removeAttr('disabled');
							$('#AddressProvincestateId').removeAttr('disabled');
							$('#AddressPostalcode').removeAttr('disabled');
							$('#UserFirstname').removeAttr('disabled');
							$('#UserLastname').removeAttr('disabled');
							$('#CardNumber').removeAttr('disabled');
							$('#CardCvv').removeAttr('disabled');
							$('#CreditcardInformation').show();
						}

				})

}
function ClearCoupon(){
	$('#PaymentPromocode').val("");
	CheckCoupon();
}
</script>
<?php if ($signupDetails['Signup']['status']!='paid' ):?>
<h3 class="new">Complete Payment</h3>
<?php else:?>
<h3 class="new">Payment Information</h3>

<?php endif;?>
<div>
<!-- Signup information -->
<div style="width:160px; float:left;margin-top:7px;" class="bord">
<dl class="signup_info">
<dt>Signup date:</dt> <dd><?php echo $this->Time->niceShort($signupDetails['Signup']['signup_date']); ?></dd>
    <?php if ($isFreeSignup):?>
	    <dt>Status:</dt>
	    <dd>Free</dd>   
    <?php else:?>
		<dt>Status:</dt>	 <dd><?php echo ucwords(strtolower($signupDetails['Signup']['status'])); ?> <?php if ($signupDetails['Signup']['for_team']):?>(for entire team)<?php endif;?></dd>
		<dt>Paid:</dt>		 <dd>$<?php echo sprintf("%.2f",$signupDetails['Signup']['paid']); ?></dd>
		<dt>Discount:</dt>	 <dd>$<?php echo sprintf("%.2f",$signupDetails['Signup']['discount']); ?></dd>
		<dt>Total:</dt>		 <dd>$<?php echo sprintf("%.2f",$signupDetails['Signup']['total']); ?></dd>
    <?php endif;?>
</dl>
</div>
<!--EOF Signup information -->
<div style="width:522px; padding:-10px 20px 30px 20px; margin-left:20px; float:left" class="gray_hr">
  <!-- payment information -->
  <?php if(!empty($payments)):?>
  <h3>Payment history</h3>
  <table class="gray_bg" style="font-size:11px">
    <tr>
      <th style="width:90px">Payment date</th>
      <th>Amount</th>
      <th>Status</th>
      <th style="width:120px">Reason</th>
      <th>Description</th>
      <th>User</th>
    </tr>
    <?php foreach ($payments as $payment): ?>
    <tr>
      <td><?php echo $this->Time->niceShort($payment['Payment']['payment_date']) ?></td>
      <td><?php echo $payment['Payment']['amount']>=0?'$':'-$'; echo sprintf("%.2f",abs($payment['Payment']['amount'])); ?></td>
      <td><?php echo $payment['Payment']['status'] ?></td>
      <td><?php echo $payment['Payment']['reason'] ?></td>
      <td><?php echo $payment['Payment']['description']; ?></td>
      <td><a href="/u/<?php echo $payment['User']['lgn']; ?>"><?php echo $payment['User']['lgn']; ?></a></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php endif;?>
  <!-- EOF payment Information -->
<?php if ($signupDetails['Signup']['status'] != 'paid'):?>
<!-- To pay information -->
<h3>To pay</h3>
	<div id="topay">$<?php echo $signupDetails['Signup']['2pay'];?></div>

<!-- EOF to pay -->
<div class='clear'></div>
</div>
<div class='clear'></div>
</div>

	<?php echo $this->Form->create('Payment',array('id'=>'Payment','name'=>'Payment', 'url' => $authorizeNetURL));?>
	<?php /*?>
	<!-- coupon information -->
	<h4>Discount coupon</h4>
		  <fieldset>
		  <div id="CouponInformation" stylae="display:none;"> <!-- Don't remove this it's for AJAX'--></div>
	  	  <?php echo $this->Form->input('promocode',array('label'=>false,'style'=>'float:left;'));?>
			<div id="CheckCouponBtn">
				<input type="button" value="Check coupon" class="submit2" onclick="CheckCoupon();" />
				<input type="button" value="Clear coupon" class="submit2" onclick="ClearCoupon();" />
	   		</div>
	   		<div id="CheckCouponloadbtn" style="display:none;">
	     		<img src="<?php echo STATIC_BPONG?>/img/ajax_loader_m.gif" border="0">
	     	</div>
	      </fieldset>
	<!-- EOF coupon information-->
<div class="clear"></div>
<?php */ ?>
<div class="clear"></div>

<h4 >Credit card information</h4>
    	<?php
    		echo $authorizeNetHiddens;
    		
    		echo $this->Form->input('x_first_name', array('id' => 'x_first_name', 'name' => 'x_first_name', 'label' => 'First Name', 'value' => $signupDetails['User']['firstname']));
    		echo $this->Form->input('x_last_name', array('id' => 'x_last_name', 'name' => 'x_last_name', 'label' => 'Last Name', 'value' => $signupDetails['User']['lastname']));
    		
    		echo $this->Form->hidden('sd',array('value' => $signupId, 'id' => 'sd', 'name' => 'data[Addition][sd]'));
	      	echo $this->Form->hidden('ud',array('value' => $userID, 'id' => 'ud', 'name' => 'data[Addition][ud]'));
	      	echo $this->Form->hidden('dd',array('value' => 0, 'id' => 'dd', 'name' => 'data[Addition][dd]'));
	      	echo $this->Form->hidden('payment_process_num',array('value' => 2, 'id' => 'dd', 'name' => 'data[Addition][payment_process_num]'));	      	
    	
    		echo $this->Form->input('x_card_num',array('label'=>'Credit Card Number', 'name' => 'x_card_num'));
    		echo $this->Form->input('x_card_code',array('label'=>'CVN','size'=>5, 'name' => 'x_card_code'));
    		echo $this->Form->input('x_exp_date',array('name' => 'x_exp_date', 'value' => '', 'label' => 'Credit Card Exp. Date', 'type' => 'text', 'id' => 'x_exp_date'));
    		?>

<div class="clear"></div>

<div id='billing_address'>
 <h4 >Billing address</h4>
        <?php
        	echo $this->Form->input('Address.addressesIds',array('type' => 'select','label'=>'Select address','options' => $addressesIds,'onChange'=>'selectAddress(this.value);'));
            echo $this->Form->hidden('x_country', array('name' => 'x_country', 'id' => 'x_country', 'value' => ''));
            echo $this->Form->hidden('x_state', array('name' => 'x_state', 'id' => 'x_state', 'value' => ''));
                   
        
            echo $this->Form->input('country_id',array('name' => 'data[Addition][country_id]', 'id' => 'country_id', 'type' => 'select', 'label'=>'Country', 'options' => $countries, 'style'=>'width:220px;'));
            echo $this->Form->input('x_address', array('name' => 'x_address', 'id' => 'x_address', 'label' => 'Addess'));
            echo $this->Form->input('x_address2', array('name' => 'data[Addition][address2]', 'id' => 'x_address2', 'label' => 'Addess2'));
            echo $this->Form->input('x_city',array('label'=>'City', 'name' => 'x_city', 'id' => 'x_city'));
            echo $this->Form->input('state_id',array('id' => 'state_id', 'name' => 'data[Addition][state_id]', 'type' => 'select', 'label'=>'State', 'options' => $states, 'style'=>'width:220px;'));
			echo $this->Form->input('x_zip', array('label'=>'Postal code', 'name' => 'x_zip', 'id' => 'x_zip'));
			echo $this->Form->input('x_phone',array('label'=>'Phone', 'name' => 'x_phone', 'id' => 'x_phone', 'value' => $phone));		   	
		?>
    	
<div class="clear"></div>
</div>

<div style="">
	<div id="BUTTONS" class="step_next signup_buttons" style="width:100px; float:left;margin:20px 0px !important;">
	<input id="Proceed" class="submit" type="submit" value="Proceed" onclick="return submitPayment();">
	</div>
  	<div id="Loading" style="display:none">
        <?php echo $this->Html->image(STATIC_BPONG.'/img/ajax_loader_m_green.gif',array('id'=>'StatusLoading')); ?> Processing...
	</div>
</div>
</form>
<?php else :?>
</div></div>
<?php endif;?>