<script type="text/javascript">
$(document).ready(function() {
	$("#shippig").validate({
				submitHandler: function(form) {
                jQuery(form).ajaxSubmit({
                    beforeSubmit: beforeSubmit,
                    success: afterSubmit
                });
        }});
});
function afterSubmit(responseText)  {
	if(responseText=='ok'){
		GotoStep(3,'nothing');
	}else{
		alert(responseText);
		$('#submit_loader').hide();
		$('#next').show();
	}
	$('#Previous').attr("disabled",false);
}
function beforeSubmit(){
	$('#Previous').attr("disabled","disabled");
	$('#next').hide();
	$('#submit_loader').show();
}
</script>
<?php echo $this->element('checkout_header'); ?>

<span style="display:none;color:#FFFFFF;"><?php echo $handlingTotal; ?></span> <?php echo $this->Form->create('shippig',array('name'=>'shippigForm','id'=>'shippig','url'=>'/checkouts/saveShipping'));?>
<?php	
		$numberGroups = count($shippingGroups);
		foreach ($shippingGroups as $groupsKey => $shippingGroup) {
			$international = $shippingGroup['international'];
			if ($international) {
				$internationalTag = 'international';	
			}else{
				$internationalTag = 'local';				
			}
			if ($numberGroups > 1) {
				$productsList = '';
				foreach ($shippingGroup['warehouses'] as $warehouseID => $warehouse) {
					foreach ($warProductNames[$warehouseID] as $productID => $productName) {
						$productsList .= $productName . ', ';
					}
				}
	  ?>
<h3 style='color:blue;'>
Products: <?php echo  substr($productsList,0,-2); ?> could be shipped by:
<h3>
<?php	}else{ ?>
<?php } ?>
<?php	$i = 1;
			foreach ($shippingGroup['shippings'] as $shipCompanyTag => $shipMethods) {
				if(isset($savedShipGroups[$groupsKey]))			
				
			?>
<h3 style='margin-left:5px;'><?php echo $shipCompanyTitles[$shipCompanyTag]; ?> + Handling</h3>
<table class="pay_table">
  <?php 
				foreach ($shipMethods as $methodTag => $methodPrice) {

						$methodID = $shippingNames[$shipCompanyTag][$internationalTag][$methodTag]['id'];
						$methodName = $shippingNames[$shipCompanyTag][$internationalTag][$methodTag]['name'];
						$checked='';
						if ($i==1 && !isset($savedShipGroups[$groupsKey])) {
							$checked='checked';
						}elseif(isset($savedShipGroups[$groupsKey]) && $methodID == $savedShipGroups[$groupsKey]){
							$checked='checked';
						}					
				?>
  <tr>
    <td><?php echo $methodName;?></td>
    <td width='80'>$<?php echo sprintf("%01.2f",($methodPrice + $shippingGroup['handlingPrice']));?><span style="display:none;color:#EFEFEF;"><?php echo $shippingGroup['handlingPrice']; ?></span></td>
    <td width='10'><input name="data[shipping][group][<?php echo $groupsKey;?>]" type="radio" value="<?php echo $methodID;?>" <?php echo $checked;?>></td>
  </tr>
  <?php	
				$i++; }?>
</table>
<?php  }	?>
<?php } ?>
<div class='step_previous'  id='prev'>
  <input type="button" value="Previous" id='Previous' class="submit" onclick="GotoStep(1,'prev');" />
</div>
<div class='step_next'  id='next' style='float:left;'>
  <input type="submit" value="Next" class="submit4" />
</div>
<div id='submit_loader' style='float:left;display:none;height:18px;width:78px;text-align:center;'><img src="<?php echo STATIC_BPONG?>/img/ajax_loader_m_green.gif" border="0"></div>
</form>
<?php echo $this->element('checkout_footer');  ?> 