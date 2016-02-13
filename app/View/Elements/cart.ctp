<table class="carttopbox">
  <tr>
    <td class="textright"><b><span id='cart_items'><?php echo $appcartItems;?></span></b> items <br />
      Total cost: <b>$<b id='cart_tcost'><?php echo sprintf("%01.2f",$appcartTotal);?></b></b></td>
    <td><a href="<?php echo MAIN_SERVER; ?>/shopping_cart" class="cart">&nbsp;</a></td>
    <td class="small"><a href="<?php echo MAIN_SERVER; ?>/store/terms" class="actions">Terms &amp; Conditions</a>&nbsp;|&nbsp;<a href="<?php echo MAIN_SERVER; ?>/order_status" class="actions">Order Status</a></td>
  </tr>
</table>
<div class="clear"></div>
