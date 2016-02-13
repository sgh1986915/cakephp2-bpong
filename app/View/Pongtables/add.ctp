<link href="<?php echo STATIC_BPONG?>/js/bbcode/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src='<?php echo STATIC_BPONG?>/js/bbcode/xbb.js'></script>
<script type="text/javascript">
	XBB.textarea_id = 'PongtableDescription'; // id of a textarea
	XBB.area_width = '550px';
	XBB.area_height = '200px';
	XBB.state = 'plain'; // 'plain' or 'highlight'
	XBB.lang = 'en_utf8';// 
//<![CDATA[ 

 $(document).ready(function() {
	$("#Pongtableadd").validate({
		rules: {
			  "data[Pongtable][title]":"required"
			, "data[Pongtable][description]":"required"
			, "data[Image][new]":"required"
		},
		messages: {
			  "data[Pongtable][title]":  "Please enter your title of a table."
			, "data[Pongtable][description]":  "Please enter your description of a table."
			, "data[Image][new]":  "Please add a photo of a table."
		}
	});
	//EOF Validation
	
	//$('#AddressAddress,#AddressCity,#AddressProvincestateId,#AddressCountryId').change( requestAddressChange() );

	
});
//EOF ready
</script>
<?php echo $this->Form->create ( 'Pongtable', array ( 'type' => 'file', 'id' => 'Pongtableadd' ) );?>
<?php
	echo $this->Form->input ( 'Image.new',array( 'type' => 'file', 'class'=>'file', 'label' => 'Image' ));
	echo $this->Form->input ( 'title' );
	echo $this->Form->input ( 'Address.address', array( 'type' => 'text' ));
	echo $this->Form->input ( 'Address.city' );
	echo $this->Form->input ( 'Address.provincestate_id' );
	echo $this->Form->input ( 'Address.country_id' );	?>
<div class="descrreq"><?php	echo $this->Form->input ( 'description');?></div>
<?php		echo $this->Form->input ( 'analysis' );?>

<?php echo $this->Form->end ( 'Submit' );?>
<script type="text/javascript">
	XBB.init();
</script>
