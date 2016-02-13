<div class="checktotal">
	<?php if($appcartDiscount){ ?>
    <label>Order Total Discount:</label> <strong>$<span id='order_discount'><?php echo sprintf("%01.2f",$appcartDiscount)?></span></strong>
    <div class="clear"></div>
	<?php } ?>
    <label>Order Price:</label> <strong>$<span id='order_amount'><?php echo sprintf("%01.2f",($appcartAmount))?></span></strong>
    <div class="clear"></div>
    <label>Order Shipping + Handling:</label> <strong><span id='ship_total'><?php if(!$appShipHandTotal){ echo 'Cost Not Calculated';}else{echo '$'.sprintf("%01.2f",$appShipHandTotal);}?></span></strong>
    <?php
    	// Show Tax for Step 2 
    	if(isset($changeTax)){
    		$appSalesTax=$changeTax;
    		$appcartTotal+=$changeTax;
    	}
    	if(isset($appSalesTax)&&$appSalesTax>0){ ?>
    	<div class="clear"></div>
    	<label>Sales Tax:</label> <strong>$<span id='order_tax'><?php echo sprintf("%01.2f",$appSalesTax)?></span></strong>
	<?php } ?>
    <div class="clear"></div>
    <label>Order Total Price:</label> <strong>$<span id='order_total'><?php echo sprintf("%01.2f",$appcartTotal)?></span></strong>
</div>
<div class="note"><img src="/img/info.gif" />Customers shipping orders outside of the United States are responsible for clearing the order through customs and for any customs duties that may be required to receive the goods.</div>

