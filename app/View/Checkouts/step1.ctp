<script type="text/javascript">
// Update cart
function cart_update(cart_tcost,cart_items){
 	$("#cart_tcost").html(cart_tcost);
 	$("#cart_items").html(cart_items);
};
//Update tottals (footer) of the cart
function totals_update(cart_total,weight_total,order_total,ship_total){
	$("#weight_total").html(weight_total);
	$("#order_total").html(order_total);
	$("#ship_total").html(ship_total);
	$("#cart_total").html(cart_total);
};
// Go to Chaeckout Step # step
function GotoStep(step,direct){
	if(direct=='next'){
		$('#next').html(showNextLoading());
	}
	if(direct=='prev'){
		$('#prev').html(showPrevLoading());
	}
	if(direct!='prev'&&direct!='next'){
		$('#'+direct).html(showPrevLoading());
	}
	if (step == 1) {
		window.location.href = '<?php echo SECURE_SERVER;?>/checkout';
	} else {	
		$.ajaxSetup({cache:false});
		$('#CheckoutAjax').load('/checkouts/step'+step,{cache: false},function(){			
			tb_init('a.thickbox, area.thickbox, input.thickbox');
			}
		
		);
	}
};
</script>

<div id="CheckoutAjax">
<?php if ($gotoStep) :?>
	<script type="text/javascript">
	$(document).ready(function() {
		GotoStep(<?php echo $gotoStep;?>,'next');
	});	
	</script>
	
	<div style='text-align:center;margin-top:100px;margin-bottom:100px;'><img src="/img/ajax-loader.gif" alt="" border="0"></div>
<?php else: ?>

  <!-- !!!!!! Main AJAX block !!!!!! -->
<script type="text/javascript">
$(document).ready(function() {

	$('.store_geotrust').html($('#geotrust_content').html());		

	if($("#ship_country").val()==0){
		$('#verify_link').hide();
	}
	/// If shipping and billing already have been selected:
	<?php if($billing_id){ ?>
		<?php if(!$hidden_user){ ?>
			$('#select_bill').val(<?php echo $billing_id; ?>);
		<?php } ?>
		selectAddress('bill',<?php echo $billing_id;?>);
	<?php }	?>

	<?php if($shipping_id&&!$use_bill){ ?>
		<?php if(!$hidden_user){ ?>
			$('#select_ship').val(<?php echo $shipping_id; ?>);
		<?php } ?>
		selectAddress('ship',<?php echo $shipping_id;?>);
	<?php }	?>
// validate signup form on keyup and submit
/*Form submit*/
jQuery.validator.addMethod("box", function(value, element) {
	if(!$('#use_bill').attr('checked')&&(element.name=='data[Bill][address]'||element.name=='data[Bill][address2]'||element.name=='data[Bill][address3]')){
		return true;
	}
	var str=value;
	if(str.search(/PO Box/i)!=-1||str.search(/P\.O\. Box/i)!=-1||str.search(/P\.O Box/i)!=-1||str.search(/PO\. Box/i)!=-1||str.search(/P\.O\./i)!=-1||str.search(/P\/O/i)!=-1){
		return false;
	} else {
		return true;
	}
}, "We can't ship to PO Boxes.");
jQuery.validator.addMethod("fpo", function(value, element) {
	var str=value;
	if(str.search(/^APO$/)!=-1||str.search(/^A\.P\.O\.$/)!=-1||str.search(/^FPO$/)!=-1||str.search(/^F\.P\.O\.$/)!=-1){
		return false;
	} else {
		return true;
	}
}, "We don't ship to military addresses now.");
jQuery.validator.addMethod("loading", function(value, element) {
	if(value=='Loading...'){
		return false;
	}else{
		return true;
	}
}, 'Please wait a loading.');

	$.ajaxSetup({cache:false});
	$("#adresses").validate({
		submitHandler: function(form) {
				$('#next').hide();
				$('#submit_loader').show();
                jQuery(form).ajaxSubmit({
                    beforeSubmit: beforeSubmit,
                    success: afterSubmit
                });
        },
		rules: {
			"data[Bill][country_id]":
			{  required: true,
			   min: 1
			},
			"data[Bill][provincestate_id]":
			{  required: true,
				min: 1			
			},
			"data[Ship][provincestate_id]":
			{  required: true,
				min: 1			
			},
			"data[Bill][city]": {
			required:true
			},
			"data[Bill][postalcode]": "required",
			"data[Ship][country_id]":
			{  required: true,
			   min: 1
			},
			"data[Prop][ship_fname]":"required",
			"data[Prop][ship_lname]":"required",		
			"data[Prop][bill_fname]":"required",
			"data[Prop][bill_lname]":"required",
			"data[Prop][bill_phone]":"required",
			"data[Prop][ship_phone]":"required",
			"data[Ship][address]":
			{  required:true,
			   box: true,
			   loading: true
			},
			"data[Bill][address]":
			{  
			   required:true,
			   box: true,
			   loading: true
			},
			"data[Ship][address2]":
			{  box: true
			},
			"data[Ship][address3]":
			{  box: true
			},
			"data[Ship][city]": {
			required:true
			},
			"data[Ship][postalcode]": "required"
		},
		messages: {
			"data[Bill][provincestate_id]": "Please select your State!",
			"data[Ship][provincestate_id]": "Please select your State!",						
			"data[Bill][country_id]": "Please select your Country!",
			"data[Bill][postalcode]": "This field can not be empty!",
			"data[Ship][country_id]": "Please select your Country!",
			"data[Ship][postalcode]": "This field can not be empty!",
			"data[Prop][bill_phone]": "This field can not be empty!",
			"data[Prop][ship_phone]": "This field can not be empty!",
			"data[Prop][ship_fname]":"This field can not be empty!",
			"data[Prop][ship_lname]":"This field can not be empty!",		
			"data[Prop][bill_fname]":"This field can not be empty!",
			"data[Prop][bill_lname]":"This field can not be empty!"

		}
	});
	//EOF Validation

  // Biling Country click
		$("#bill_country").change(function(){
				var use_bill=0;
				if ($('#use_bill').attr('checked')) {
					use_bill=1;
				}
		  		if($("#bill_country").val()==1 && use_bill==1){ // FOR USA
		  			$('#verify_link').slideDown();
		  		}else{
		  				$('#verify_link').hide();
		  		}

			  $("#bill_state").html('<option>Loading...</option>');
			  $.ajaxSetup({cache:false});
			  $.getJSON("/provincestates/getstates",{countryID: $(this).val()}, function(options){
					$("#bill_state").html(options);
					$('#bill_state option:first').attr('selected', 'selected');
				})

			});
  //EOF Country  click
  // Shipping Country click
		$("#ship_country").change(function(){
		  		if($("#ship_country").val()==1){ // FOR USA
		  			$('#verify_link').slideDown();
		  		}else{
		  				$('#verify_link').hide();
		  		}

			  $("#ship_state").html('<option>Loading...</option>');
			  $.ajaxSetup({cache:false});
			  $.getJSON("/provincestates/getstates",{countryID: $(this).val()}, function(options){

					$("#ship_state").html(options);
					$('#ship_state option:first').attr('selected', 'selected');
				})

			});
//EOF Country  click

		<?php if($use_bill){ ?>
		$('#use_bill').attr('checked','checked');
		usebillClick();
		<?php }?>

});
function beforeSubmit(){

	$('#Previous').attr("disabled","disabled");
}
//After Submit
function afterSubmit(responseText)  {
	if(responseText=='ok'){
		/// USPS validation is ok
		var country_id=$('#ship_country').val();
		if ($('#use_bill').attr('checked')) {
			country_id=$('#bill_country').val();
		}
		$.get("/checkouts/checkCountryAjax/"+country_id, function(data){
			if(data=='ok'){
				GotoStep(2,'nothing');
			}else{
				$('#submit_loader').hide()
				$('#next').show()
				alert('\nProducts:\n'+data+"\ncouldn't be shipped to your country!");
			}
		});

	}
	$('#Previous').attr("disabled",false);
}


// Select saved addresses
function selectAddress(type,addr_id)  {
	var use_bill=0;
	if ($('#use_bill').attr('checked')) {
		use_bill=1;
	}
	if(!addr_id){
	addr_id=$('#select_'+type).val();
	}
	if(addr_id>0){
		$('#'+type+' input').val('Loading...');
		$.ajaxSetup({cache:false});
		$.getJSON('/Addresses/getAddressJson/User/<?php echo $user_id; ?>/'+addr_id, {cache: false},
		  function(address){
		  	// select state
			$('#'+type+'_state').html('<option>Loading...</option>');
			$.getJSON("/provincestates/getstates",{countryID: address['Address']['country_id']}, function(options){
			$('#'+type+'_state').html(options);
					$('#'+type+'_state option').attr('selected', '');
					$('#'+type+'_state > option[value="'+address['Address']['provincestate_id']+'"]').attr("selected","selected");
			}) 

		  	$('#'+type+'_country').val('');
		  	$('#'+type+'_address').val('');
		  	$('#'+type+'_address2').val('');
		  	$('#'+type+'_address3').val('');
		  	$('#'+type+'_city').val('');
		  	$('#'+type+'_postalcode').val('')
		  	$('#'+type+'_country').val(address['Address']['country_id']);
		  	$('#'+type+'_address').val(address['Address']['address']);
		  	$('#'+type+'_address2').val(address['Address']['address2']);
		  	$('#'+type+'_address3').val(address['Address']['address3']);
		  	$('#'+type+'_city').val(address['Address']['city']);
		  	$('#'+type+'_postalcode').val(address['Address']['postalcode']);
			$('#'+type+'_country > option[value="'+address['Address']['country_id']+'"]').attr("selected","selected");
			
			if(type=='ship'&&$("#ship_country").val()==1){
				$('#verify_link').slideDown();
			}
			
			if(use_bill==1&&$("#bill_country").val()==1){
				$('#verify_link').slideDown();
			}

		  }
		);
	}else{
			if(type=='ship'||use_bill==1){
				$('#verify_link').hide();
			}
		  	$('#'+type+'_country').val('');
		  	$('#'+type+'_address').val('');
		  	$('#'+type+'_address2').val('');
		  	$('#'+type+'_address3').val('');
		  	$('#'+type+'_city').val('');
		  	$('#'+type+'_state').val('');
		  	$('#'+type+'_postalcode').val('');
	}

}
function usebillClick(){
	var disabled=false;
	if ($('#use_bill').attr('checked')) {
		disabled='disabled';
		$('#select_ship').val('0');
		$('#ship_country').val('0');
		$('#ship_address').val('');
		$('#ship_address2').val('');
		$('#ship_address3').val('');
		$('#ship_city').val('');
		$('#ship_state').val('0');
		$('#ship_postalcode').val('');
		$('#ship_fname').val('');
		$('#ship_lname').val('');
		$('#ship_phone').val('');
		if($("#bill_country").val()==1){
		  			$('#verify_link').slideDown();
		}
		$("#ship_country").rules("remove");
		$("#ship_address").rules("remove");
		$("#ship_city").rules("remove");
		$("#ship_state").rules("remove");
		$("#ship_postalcode").rules("remove");
		$("#ship_fname").rules("remove");
		$("#ship_lname").rules("remove");
		$("#ship_phone").rules("remove");
	} else {
		$("#ship_country").rules("add", {  required: true,min: 1});
		$("#ship_address").rules("add", "required");
		$("#ship_city").rules("add", "required");
		$("#ship_state").rules("add",  {  required: true,min: 1});
		$("#ship_postalcode").rules("add", "required");
		$("#ship_fname").rules("add", "required");
		$("#ship_lname").rules("add", "required");
		$("#ship_phone").rules("add", "required");	 
	}
	
	$('#select_ship').attr('disabled',disabled);
	$('#ship_country').attr('disabled',disabled);
	$('#ship_address').attr('disabled',disabled);
	$('#ship_address2').attr('disabled',disabled);
	$('#ship_address3').attr('disabled',disabled);
	$('#ship_city').attr('disabled',disabled);
	$('#ship_state').attr('disabled',disabled);
	$('#ship_postalcode').attr('disabled',disabled);
	$('#ship_fname').attr('disabled',disabled);
	$('#ship_lname').attr('disabled',disabled);
	$('#ship_phone').attr('disabled',disabled);

}
</script>
  <?php echo $this->element('checkout_header');  ?> <?php echo $this->Form->create('Addresses',array('name'=>'AddressForm','id'=>'adresses','url'=>'/checkouts/saveAddress'));?>
  <div id='billing'>
    <h2>Billing Address</h2>
    <?php if(!empty($bill_adreses)&&!$hidden_user): ?>
    <?php echo $this->Form->input('selectBill',array('id'=>'select_bill','type' => 'select','style'=>'width:190px;','onChange'=>'selectAddress("bill",0);','label'=>'<b>Select one from:</b>','div'=>false,'options' => $bill_adreses));?> <span id='bill_loader' style='display:none;'><img src="/img/loading.gif"></span> <br/>
    <b>Or write new:</b>
    <?php endif; ?>
    <div id='bill'>
      <div class="error" id="error-message" <?php echo isset($Error)?"":"style='display: none;'" ?>>Error while adding.</div>
      <?php

	            echo $this->Form->input('Bill.country_id',array('id'=>'bill_country','type' => 'select','style'=>'width:190px;','label'=>'Country <span style="color:red">*</span>','options' => $countries));
	            echo $this->Form->input('Bill.address',array('id'=>'bill_address','label'=>'Address 1 <span style="color:red">*</span>','class'=>'cart_address'));
	            echo $this->Form->input('Bill.address2',array('id'=>'bill_address2','label'=>'Address 2','class'=>'cart_address'));
	            echo $this->Form->input('Bill.address3',array('id'=>'bill_address3','label'=>'Address 3','class'=>'cart_address'));
	            echo $this->Form->input('Bill.city',array('id'=>'bill_city','label'=>'City <span style="color:red">*</span>','class'=>'cart_address'));
	            echo $this->Form->input('Bill.provincestate_id',array('id'=>'bill_state','type' => 'select','label'=>'State <span style="color:red">*</span>','style'=>'width:208px;','options' => $states));
				echo $this->Form->input('Bill.postalcode',array('id'=>'bill_postalcode','label'=>'Postal Code <span style="color:red">*</span>','class'=>'cart_address'));
?>
    </div>
    <?php
				echo $this->Form->input('Prop.bill_fname',array('id'=>'bill_fname','label'=>'First Name <span style="color:red">*</span>','class'=>'cart_address','value'=>$bill_fname));
				echo $this->Form->input('Prop.bill_lname',array('id'=>'bill_lname','label'=>'Last Name <span style="color:red">*</span>','class'=>'cart_address','value'=>$bill_lname));
				echo $this->Form->input('Prop.bill_phone',array('id'=>'bill_phone','label'=>'Phone <span style="color:red">*</span>','class'=>'cart_address','value'=>$bill_phone));
				?>
    <!--  </form> -->
    <div id='usebill'>
      <input name="data[use_bill]" id="use_bill" <?php echo $use_bill; ?> onclick="usebillClick();" type="checkbox" value="1">
      Use the Billing address as the Shipping address</div>
    <div id='verify_link'><a class="thickbox" title="Verify Address"  href="/checkouts/verify_address/?modal=true;&height=500&width=400">Verify Your Shipping Address</a></div>
  </div>
  <div class="fright" id='shipping'>
    <h2>Shipping Address</h2>
    <?php if(!empty($ship_adreses)&&!$hidden_user): ?>
    <?php echo $this->Form->input('selectShip',array('id'=>'select_ship','type' => 'select','style'=>'width:190px;','onChange'=>'selectAddress("ship",0);','label'=>'<b>Select one from:</b>','div'=>false,'options' => $ship_adreses));?> <span id='ship_loader' style='display:none;'><img src="/img/loading.gif"></span> <br/>
    <b>Or write new:</b>
    <?php endif; ?>
    <div id='ship'>
      <?php /*echo $this->Form->create('Shipping',array('name'=>'AddressAddForm','id'=>'AddressAddForm')); */?>
      <div class="error" id="error-message" <?php echo isset($Error)?"":"style='display: none;'" ?>>Error while adding.</div>
      <?php

	            echo $this->Form->input('Ship.country_id',array('id'=>'ship_country','type' => 'select','style'=>'width:190px;','label'=>'Country <span style="color:red">*</span>','options' => $shipCountries));
	            echo $this->Form->input('Ship.address',array('id'=>'ship_address','label'=>'Address 1 <span style="color:red">*</span>','class'=>'cart_address'));
	            echo $this->Form->input('Ship.address2',array('id'=>'ship_address2','label'=>'Address 2','class'=>'cart_address'));
	            echo $this->Form->input('Ship.address3',array('id'=>'ship_address3','label'=>'Address 3','class'=>'cart_address'));
	            echo $this->Form->input('Ship.city',array('id'=>'ship_city','label'=>'City <span style="color:red">*</span>','class'=>'cart_address'));
	            echo $this->Form->input('Ship.provincestate_id',array('id'=>'ship_state','type' => 'select','label'=>'State <span style="color:red">*</span>','style'=>'width:208px;','options' => $states));
				echo $this->Form->input('Ship.postalcode',array('id'=>'ship_postalcode','label'=>'Postal Code <span style="color:red">*</span>','class'=>'cart_address'));
				?>
    </div>
    <?php
				echo $this->Form->input('Prop.ship_fname',array('id'=>'ship_fname','label'=>'First Name <span style="color:red">*</span>','class'=>'cart_address','value'=>$ship_fname));
				echo $this->Form->input('Prop.ship_lname',array('id'=>'ship_lname','label'=>'Last Name <span style="color:red">*</span>','class'=>'cart_address','value'=>$ship_lname));
				echo $this->Form->input('Prop.ship_phone',array('id'=>'ship_phone','label'=>'Phone <span style="color:red">*</span>','class'=>'cart_address','value'=>$ship_phone));
?>
  </div>
  <div class="clear"></div>
  <div class='step_previous'  id='prev'>
    <input type="button" value="Previous" id='Previous' class="submit" onclick="window.location.href = '<?php echo MAIN_SERVER?>/shopping_cart'" />
  </div>
  <div class='step_next'  id='next' style='float:left;'>
    <input type="submit" value="Next" class="submit4" />
  </div>
  <div id='submit_loader' style='float:left;display:none;height:18px;width:78px;text-align:center;'><img src="/img/ajax_loader_m_green.gif" border="0"></div>
  </form>
  <?php echo $this->element('checkout_footer');  ?>
<?php endif; ?>
</div>

<div style='position:relative;z-index:150;top:-320px;float:right;margin-right:100px;'><?php echo $this->element('geotrust');  ?></div>
