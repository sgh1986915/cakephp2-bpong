<script type="text/javascript">
$(document).ready(function() {
	<?php if ($payment_error):?>
	alert("<?php echo htmlspecialchars($payment_error);?>");
	<?php endif;?>
	
	<?php if (!empty($addressID)):?>
	$('#AddressAddressesIds > option[value="<?php echo $addressID;?>"]').attr("selected","selected");
	$('#AddressAddressesIds').change();		
	<?php endif;?>
	
  // amount click
		$("#PaymentAmountCustom").click(function(){
					$("#PaymentAmountvalue").show();
			});
			$("#PaymentAmountDeposit").click(function(){
					$("#PaymentAmountvalue").hide();
			});
			$("#PaymentAmountPrice").click(function(){
					$("#PaymentAmountvalue").hide();
			});
//EOF amount click


// Country click
		$("#country_id").change(function(){
			  $("#state_id").html('<option>Loading...</option>');
			  $.getJSON("/provincestates/getstates",{countryID: $(this).val()}, function(options){
					$("#state_id").html(options);
					$('#state_id option:first').attr('selected', 'selected');
				})


			});
//EOF Country  click
// validate signup form on keyup and submit
	$("#Step4").validate({
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
			"x_phone":"required",
			"data[Payment][amountvalue]": {
					number: true,
					range: [<?php echo $step3['Package']['deposit']?>, <?php echo $step3['Package']['price']?>],
      				required: function(element) {
        							return $("input[name='data[Payment][amount]']:checked").val()=="custom";
      			                    }
             }
		},
		
		messages: {
			"country_id": "This field is required.",
			"state_id":"This field is required.",
			"x_first_name":"Please enter your first name",
			"x_last_name":"Please enter your last name",
			"x_card_num"   :"Please enter card number",
			"x_card_code"   :"Please enter card verification number",
			"x_exp_date" : "Please enter card exp. date",
			"x_phone" : "Please enter phone",		
			"data[Payment][amountvalue]":  {
				number:	"Please enter a valid number",
				range:     "Amount should be between <?php echo $step3['Package']['deposit']?> and <?php echo $step3['Package']['price']?>"
			}
		}
	});
	//EOF Validation
});

function submitPayment() {	
	$('#x_country').val($('#country_id option:selected').text());
	$('#x_state').val($('#state_id option:selected').text());

	if ($("#Step4").valid()) {

		var amt = $("input[name='data[Payment][amount]']:checked").val();
		if (amt == 'custom') {		
			amt = $("#amountvalue").val();		
		}

		if (!amt) {
			alert('Please select price');	
			return false;
		}
			
		$('.signup_buttons').hide();
		$('#Loading').show();
		$.ajaxSetup({cache:false});
		$.getJSON("<?php echo SECURE_SERVER?>/signups/ajaxCreateFpHash", {'amt': amt},
				function(json) {
					if (json && json.x_fp_hash && json.x_amount > 0) {
						$('#x_fp_hash').val(json.x_fp_hash);
						$('#x_fp_timestamp').val(json.x_fp_timestamp);
						$('#x_fp_sequence').val(json.x_fp_sequence);
						$('#x_amount').val(json.x_amount);
							
						$.ajaxSetup({cache:false});				
						$.getJSON("<?php echo SECURE_SERVER?>/signups/ajaxSaveSignup", {'amt': amt},
								function(json2) {
									if (json2 && json2.signup_id && json2.signup_id > 0) {
										$('#x_po_numz').val(json2.x_po_numz);
										$('#x_invoice_num').val(json2.x_invoice_num);
										$('#x_description').val(json2.x_description);
										$('#sd').val(json2.signup_id);						
										$('#Step4').submit();																	
									} else {
										$('.signup_buttons').show();
										$('#Loading').hide();
										alert('Payment error! Try again.');
									}				     
						});															
						
					} else {
						$('.signup_buttons').show();
						$('#Loading').hide();
						alert('Payment error! Try again.');
					}				     
		});
	}
	return false;	
}


function Previos3() {
	var loader = showPrevLoading();
	$('#previos').html(loader);
	$.ajaxSetup({cache:false});
	$('#SignupAjax').load("/signups/step3/<?php echo $modelName."/".$slug ?>",{cache: false});
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
		    $.getJSON('/Addresses/getAddressJson/User/<?php echo $this->request->data['User']['id']; ?>/'+addressID, {cache: false},
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
</script>

<?php echo $this->Form->create('Payment',array('id'=>'Step4','name'=>'Step4', 'url' => $authorizeNetURL));?>
<div class="error" id="error-message" style='display: none;'>Error while Updating.</div>
    <div class="users form" style="border:#ccc 1px dotted; margin-top:20px">
        <fieldset>
<h4 >Billing information</h4>
	<div class="radio_line">
		<?php echo $this->Form->radio('amount', $amounts, array ('separator' =>'<br /><br />', 'legend'=>false, 'label'=>false, 'value' => sprintf("%01.2f", $step3['Package']['price'])));?>
    </div>
<div style="text-align:left; padding-left:20px; float:left; width:250px; height:auto; display:none" id="PaymentAmountvalue" >
 <input name="data[Payment][amountvalue]" id='amountvalue' value="" type="text" />
</div>

<div style="clear:both">
<?php  if (!empty($discount) || isset($discountError)):?>
<!-- Discount information-->
	<h4 >Discount information </h4>
			Discount coupon: <?php echo $step3['Package']['promocode'];?>;<BR>
			<?php if (isset($discountError)):?>
				<?php echo $discountError;?>
			<?php else:?>
    			<?php echo $discount;?>
    		<?php endif;?>
<!--EOF Discount information-->
<?php endif;?>
</div>
<div class="clear"></div>
<?php echo $authorizeNetHiddens;?>
<h4 >Credit card information</h4>
    	<?php
    	  	
    	    
    	
    	
	    	echo $this->Form->hidden('x_amount', array('name' => 'x_amount', 'id' => 'x_amount', 'value' => '')); 
	    	echo $this->Form->hidden('x_fp_hash', array('name' => 'x_fp_hash', 'id' => 'x_fp_hash', 'value' => ''));
	    	echo $this->Form->hidden('x_fp_sequence', array('name' => 'x_fp_sequence', 'id' => 'x_fp_sequence', 'value' => ''));
	    	echo $this->Form->hidden('x_fp_timestamp', array('name' => 'x_fp_timestamp', 'id' => 'x_fp_timestamp', 'value' => ''));	
	    	    		    		      	
	    	echo $this->Form->hidden('sd',array('value' => 0, 'id' => 'sd', 'name' => 'data[Addition][sd]'));
	      	echo $this->Form->hidden('ud',array('value' => $userID, 'id' => 'ud', 'name' => 'data[Addition][ud]'));
	      	echo $this->Form->hidden('dd',array('value' => $discountID, 'id' => 'dd', 'name' => 'data[Addition][dd]'));	      	
	      	echo $this->Form->hidden('payment_process_num',array('value' => 1, 'id' => 'dd', 'name' => 'data[Addition][payment_process_num]'));
	      	
	      	echo $this->Form->hidden('x_po_numz',array('value' => '', 'name' => 'x_po_numz', 'id' => 'x_po_numz'));
	      	echo $this->Form->hidden('x_invoice_num',array('value' => '', 'name' => 'x_invoice_num', 'id' => 'x_invoice_num'));
	      	echo $this->Form->hidden('x_description',array('value' => '', 'name' => 'x_description', 'id' => 'x_description'));
	      		      		      		      	

	    	
            echo $this->Form->input('x_first_name', array('id' => 'x_first_name', 'name' => 'x_first_name', 'label' => 'First Name', 'value' => $this->request->data['User']['firstname']));
    		echo $this->Form->input('x_last_name', array('id' => 'x_last_name', 'name' => 'x_last_name', 'label' => 'Last Name', 'value' => $this->request->data['User']['lastname']));
  		
    	    //echo $this->Form->input('Card.type',array('label'=>'Type','type' => 'select','options' => $cardtypes));
    		echo $this->Form->input('x_card_num',array('label'=>'Credit Card Number', 'name' => 'x_card_num'));
    		echo $this->Form->input('x_card_code',array('label'=>'CVN','size'=>5, 'name' => 'x_card_code'));
    		echo $this->Form->input('x_exp_date',array('name' => 'x_exp_date', 'value' => '', 'label' => 'Expiration Date (mmyy)', 'type' => 'text', 'id' => 'x_exp_date'));
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
    	
    	
</div>    	
    	</fieldset>


    </div>   
<div style="padding:15px 0 0 20px">
	<div id="previos" class='step_previous signup_buttons' style="width:100px; float:left">
		<input type="button" value="Previous" class="submit" onclick="Previos3();" />
    </div>
	<div id="next" class='step_next signup_buttons' style="width:100px; float:left">
		<input type="submit" value="Proceed" class="submit" onclick="return submitPayment();" />
	</div>
	<div class='clear'> </div>
  	<div id="Loading" style="display:none;color:#0A4B9C;margin-top:20px;margin-left:20px;">
        <?php echo $this->Html->image(STATIC_BPONG.'/img/ajax_loader_m_green.gif',array('id'=>'StatusLoading')) ?> Processing...
	</div>	
</div>   
    
      </form>
