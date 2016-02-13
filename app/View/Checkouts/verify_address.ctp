<script type="text/javascript">
verifying();
// verify by USPS API
function verifying(){
	var type='ship';
	if ($('#use_bill').attr('checked')) {
		type='bill';
	}	
	var address=$('#'+type+'_address').val();
	var address2=$('#'+type+'_address2').val();
	var address3=$('#'+type+'_address3').val();
	var city=$('#'+type+'_city').val();
	var state=$('#'+type+'_state').val();
	var postalcode=$('#'+type+'_postalcode').val();

	$.ajaxSetup({cache:false});
	$.getJSON('/checkouts/uspsVerifyAjax', {cache: false, address:address,address2:address2,address3:address3,city:city,state:state,postalcode:postalcode},
	  function(answer){

		if(answer['answer']=='ok'){
			$('#my_address').html(address);
			$('#my_address2').html(address2);
			$('#my_address3').html(address3);
			$('#my_city').html(city);
			if(state>0){
				$('#my_state').html($('#'+type+'_state > option[value="'+state+'"]').text());
			}
			$('#my_postalcode').html(postalcode);

			$('#usps_address').html(answer['address']);
			$('#usps_address2').html(answer['address2']);
			$('#usps_address3').html(answer['address3']);
			$('#usps_city').html(answer['city']);
			$('#usps_state').html(answer['state']);
			$('#usps_postalcode').html(answer['postalcode']);

			$('#verify_loader').hide();
			$('#addresses').slideDown();
		}else{
		$('#usps_label').html('USPS validation ERROR: '+answer['text']);
		$('#verify_loader').hide();

		$('#verify_error').slideDown();

		}




	  }
	);
};
// select one of two adresses
function newAddress(usps){
	var type='ship';
	if ($('#use_bill').attr('checked')) {
		type='bill';
	}	
	if(usps==1){
		$('#'+type+'_address').val($('#usps_address').html());
		$('#'+type+'_address2').val($('#usps_address2').html());
		$('#'+type+'_address3').val($('#usps_address3').html());
		$('#'+type+'_city').val($('#usps_city').html());
		$('#'+type+'_postalcode').val($('#usps_postalcode').html());
	}
	tb_remove();
	$('#usps_error').hide();
};
</script>

<div class="add_to"><h1><img src="<?php echo STATIC_BPONG?>/img/logclose.jpg" id="Close" class="right" style="cursor:pointer; padding:4px 0px 0px 0px;"  onclick="self.parent.tb_remove();" />Verify Address by USPS</h1>
	<fieldset style="border:none;">
<div id='verify_loader' style='width:100%;text-align:center;padding-top: 35%; '>
	<b>USPS Verifying ...</b><br/>
	<img src="<?php echo STATIC_BPONG?>/img/loader_verify.gif" border="0">
</div>
<div id='verify_error'  style='width:100%;text-align:center;padding-top: 35%;display:none;'>
	<label class="error" id='usps_label' generated="true" style='text-align:center;font'></label>
</div>
<div id='addresses' style='display:none;' >
<h3 style='color:#0C2E84;'>Your Entered Address:</h3>
	<table border=0 class='usps'>
		<tr><td width='100'><b>Address 1</b></td><td><span id='my_address'></span></td></tr>
		<tr><td><b>Address 2</b></td><td><span id='my_address2'></span></td></tr>
		<tr><td><b>Address 3</b></td><td><span id='my_address3'></span></td></tr>
		<tr><td><b>City</b></td><td><span id='my_city'></span></td></tr>
		<tr><td><b>State</b></td><td><span id='my_state'></span></td></tr>
		<tr><td><b>Postal Code</b></td><td><span id='my_postalcode'></span></td></tr>
	</table>
	<a href="javascript:newAddress(0);"><b>Use Your Address</b></a>
<h3 style='color:#0C2E84;'>USPS Verified Address:</h3>
	<table border=0 class='usps'>
		<tr><td width='100'><b>Address 1</b></td><td><span id='usps_address'></span></td></tr>
		<tr><td><b>Address 2</b></td><td><span id='usps_address2'></span></td></tr>
		<tr><td><b>Address 3</b></td><td><span id='usps_address3'></span></td></tr>
		<tr><td><b>City</b></td><td><span id='usps_city'></span></td></tr>
		<tr><td><b>State</b></td><td><span id='usps_state'></span></td></tr>
		<tr><td><b>Postal Code</b></td><td><span id='usps_postalcode'></span></td></tr>
	</table>
	<a href="javascript:newAddress(1);"><b>Use USPS Address</b></a>
</div>

    </fieldset>
</div>
<img src="<?php echo STATIC_BPONG?>/img/store/bottom_addtocart.gif" alt="btn" />

