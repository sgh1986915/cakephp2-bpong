<?php
// Make list of the json attributes for recalculation
$recalc_attrs='';
$var_products='';
$valid_products='';
    foreach($products as $product){
		$recalc_attrs.='"products['.$product['id'].']"'.':$("#quantity_'.$product['id'].'").val(),';
		$var_products.='var product'.$product['id'].'=$("#quantity_'.$product['id'].'").val();   ';
		$valid_products.='if(!product'.$product['id'].'.match(re) && $("#tr_'.$product['id'].'").css("display")!="none" ||product'.$product['id'].'>500)
		{valid=0;}';
	}
$recalc_attrs.='cache: false' ;

$nexturl='/carts/login';
if($user_id != VISITOR_USER){
	$nexturl=SECURE_SERVER."/checkout";
}

?>
<script type="text/javascript">
// Update cart
function cart_update(cart_tcost,cart_items){
 	$("#cart_tcost").html(cart_tcost);
 	$("#cart_items").html(cart_items);
};
// Select Promocode
function select_promocode(){
 	$("#promo_button").hide();
 	$("#promocode").slideDown();

};
//Update tottals (footer) of the cart
function totals_update(order_amount,weight_total,order_total,ship_total,order_tax,discount){
	$("#weight_total").html(weight_total);
	$("#order_total").html(order_total);
	$("#ship_total").html(ship_total);
	$("#order_amount").html(order_amount);
	$("#order_discount").html(discount);
	if(order_tax==''){
		order_tax='0.00';
	}
	$("#order_tax").html(order_tax);
};
//add one product to the cart
function cart_add(product_id){
	$("#plus_"+product_id).attr({src: '<?php echo STATIC_BPONG?>/img/loading.gif'});
	$.ajaxSetup({cache:false});
	$.getJSON('/carts/add_ajax/'+product_id+'/'+'1', {cache: false},
	  function(options){
	 		$("#quantity_"+product_id).val(options['this_quantity']);
	 		$("#tprice_"+product_id).html(options['this_tcost']);
	 		$("#tdiscount_"+product_id).html(options['this_tdiscount']);

	 		cart_update(options['cart_tcost'],options['cart_items']);
	 		totals_update(options['order_amount'],options['weight_total'],options['order_total'],options['ship_total'],options['order_tax'],options['cart_tdiscount']);
	 		$("#plus_"+product_id).attr({src: '<?php echo STATIC_BPONG?>/img/plus.gif'});
          	if(options['stock']=='out'){
          		alert('\nThe quantity you have ordered of product "'+options['products_stock']+'"\nexceeds the quantity we '+
					'currently have in stock. \nIf you continue to order this quantity, your '+
					'order will be backordered.\n\n ');
	        }
	        if(options['stock']=='low'){
				alert('\nPlease note that the inventory level of this item '+
				'is low,  \nand it is possible that somebody else could buy the remaining '+
				'inventory \nbefore you checkout.\n\n ');
			}
	    });
};

//delete on product from the cart
function cart_del(product_id){
	$("#minus_"+product_id).attr({src: '<?php echo STATIC_BPONG?>/img/loading.gif'});
	$.ajaxSetup({cache:false});
	$.getJSON('/carts/del_ajax/'+product_id+'/'+1, {cache: false},
	  function(options){
		  	if (options['promocode_error'] == 1) {
		  		window.location.reload(true);
		  		return false;
		    }
	 		$("#quantity_"+product_id).val(options['this_quantity']);
	 		$("#tprice_"+product_id).html(options['this_tcost']);
			$("#tdiscount_"+product_id).html(options['this_tdiscount']);

	 		cart_update(options['cart_tcost'],options['cart_items']);
	 		totals_update(options['order_amount'],options['weight_total'],options['order_total'],options['ship_total'],options['order_tax'],options['cart_tdiscount']);

	 		$("#minus_"+product_id).attr({src: '<?php echo STATIC_BPONG?>/img/minus.gif'});
          	if(options['stock']=='out'){
          		alert('\nThe quantity you have ordered of product "'+options['products_stock']+'"\nexceeds the quantity we '+
					'currently have in stock. \nIf you continue to order this quantity, your '+
					'order will be backordered.\n\n ');
	        }
	        if(options['stock']=='low'){
				alert('\nPlease note that the inventory level of this item '+
				'is low,  \nand it is possible that somebody else could buy the remaining '+
				'inventory \nbefore you checkout.\n\n ');
			}
	    });
};
//delete all product (all quantity) from the cart
function product_del(product_id){
	$("#del_"+product_id).attr({src: '<?php echo STATIC_BPONG?>/img/loading.gif'});
	$.ajaxSetup({cache:false});
	$.getJSON('/carts/product_delAjax/'+product_id, {cache: false},
	  function(options){
		  	if (options['promocode_error'] == 1) {
		  		window.location.reload(true);
		  		return false;
		    }

	 		cart_update(options['cart_tcost'],options['cart_items']);
	 		totals_update(options['order_amount'],options['weight_total'],options['order_total'],options['ship_total'],options['order_tax'],options['cart_tdiscount']);
	 		$("#tr_"+product_id).hide();
	    });

};
// show recalculate burron
function show_recalculate(){
			if($("#recal_box").css("display")=='none'){
	 			$("#recal_box").slideDown();
			}
};
// checkout 0 - button 'recalculate'; 1 - button 'checkout';
function recalculate(checkout){

	var re = /^[0-9]+$/;
	var valid=1;
	var loader='<img src="<?php echo STATIC_BPONG?>/img/ajax_loader_m.gif" border="0">';
	var box=$("#recal_box").html();
	<?php echo $var_products; ?>
	<?php echo $valid_products; ?>
	if (!valid){
		$("#validation").show();
		//$("#validation").fadeOut(10000);

		return false;
	}else{


		if($("#recal_box").css("display")=="none"&&checkout==1){
			top.location.href ='<?php echo $nexturl; ?>';
			return 1;
		}else{
			$("#validation").hide();
			$('#recal_box').html(loader);

			if(checkout==1){
				$('#checkout').html(loader);
			}
			$.ajaxSetup({cache:false});
			$.getJSON("/carts/recalculateAjax/",{<?php echo $recalc_attrs; ?>},
			  	function(options){
				  	if (options['promocode_error'] == 1) {
				  		window.location.reload(true);
				  		return false;
				    }
        	  		if(checkout==1){
							top.location.href ='<?php echo $nexturl; ?>';
							return 1;
			  		}else{
			  			cart_update(options['cart_tcost'],options['cart_items']);
			 			totals_update(options['order_amount'],options['weight_total'],options['order_total'],options['ship_total'],options['order_tax'],options['cart_tdiscount']);
			  			$('#recal_box').slideUp("slow");
			  			$.each(options.products, function(pid,tprice){
		          			$("#tprice_"+pid).html(tprice);
		          		});
		          		$.each(options.discounts, function(pid,discamount){
		          			$("#tdiscount_"+pid).html(discamount);
		          		});
		          		if(options['stock_products']){
		          			alert('\nThe quantity you have ordered of products:\n '+options['stock_products']+'\nexceeds the quantity we'+
							'currently have in stock. \nIf you continue to order this quantity, your'+
							'order will be backordered.\n\n ');
		          		}
		          		$("#recal_box").html(box);
			  		}
			  	}
			);
		}
	}
}

</script>
<?php if(isset($products)&&count($products)){ ?>

<h2>Shopping cart</h2>
<div class="clear"></div>
<table class="carttable" cellpadding="0" cellspacing="0">
  <tr>
    <th></th>
    <th>Product Name</th>
    <th>Price</th>
    <th>Quantity</th>
    <?php /* <th width="100">Weight</th>*/ ?>
    <th></th>
    <?php if($total_discount){ ?>
    <th>Total Discount</th>
    <?php } ?>
    <th class="alignr">Total Price</th>
  </tr>
  <?php
    $i = 1;
    //echo "<pre>";
    //print_r($modifs);
    foreach($products as $product):
                    $i = 1-$i;
                    echo ($i==0) ? '<tr class="tdata1" id="tr_'.$product['id'].'">' : '<tr class="tdata2" id="tr_'.$product['id'].'">';
    ?>
  <td><?php if ($product['image']):?>
      <img src="<?php echo STATIC_BPONG?>/img/StoreProduct/thumbs/<?php echo $product['image']; ?>" alt="" border="0" style="text-decoration:none" />
      <?php else:?>
      <?php endif;?>
    </td>
    <td><a href="<?php echo $product['link']?>" onclick="window.open(this);return false;"><?php echo $product['name']?></a></td>
    <td>$<?php echo sprintf("%01.2f",$product['price']);?></td>
    <td><div class="quantbox"> <a href="javascript:cart_add(<?php echo $product['id']?>)" class="noborder" title="Add the one"><img id="plus_<?php echo $product['id'];?>" src="<?php echo STATIC_BPONG?>/img/store/quantmore.png" alt="+" /></a></div>
      <div class="quantbox2">
        <input class="quant" onKeyPress="show_recalculate();" name="quantity_<?php echo $product['id']?>" type="text" id='quantity_<?php echo $product['id']?>' value="<?php echo $product['quantity']?>">
      </div>
      <div class="quantbox"> <a href="javascript:cart_del(<?php echo $product['id']?>)" class="noborder" title="Remove the one"><img id="minus_<?php echo $product['id'];?>" src="<?php echo STATIC_BPONG?>/img/store/quantless.png" alt="more" /></a></div></td>
    <?php /* <td align='left'><?php if($product['weight']>0){ ?> <?php echo sprintf("%01.2f",$product['weight'])?> oz.<?php } ?></td>*/?>
    <td class="alignc"><a href="javascript:product_del(<?php echo  $product['id']?>)" onclick="return confirm('Are you sure you would like to remove this item?')" class="fs10">Remove Item</a></td>
    <?php
        if(!isset($product['discount'])){
        	$product['discount']=0;
        }
        if($total_discount){ ?>
    <td>$<span id='tdiscount_<?php  echo $product['id']?>'><?php echo sprintf("%01.2f",$product['discount']);?></span></td>
    <?php } ?>
    <td class="thered alignr">$<span id='tprice_<?php  echo $product['id']?>'><?php echo sprintf("%01.2f",($product['price']*$product['quantity'])-$product['discount'])?></span></td>
  </tr>
  <?php endforeach;?>
</table>
<div style='display:none; width:150px;text-align:center;'  id='recal_box'>
  <input type="button"  value="Recalculate" class="submit2" onclick="recalculate(0);"/>
</div>
<div id='validation' class="valid_quant" style="display:none">Please specify correct quantity</div>
<div style='float:right; clear:both'>
  <input type="button" id='checkout' value="" class="proceed_check" onclick="recalculate(1);" />
</div>
<?php if (!$usingDiscount):?>
<div style="float:left">
  <form id='promoform' action="/carts/addPromocode" method="post">
    <div style='text-align: left;' id='promocode'>I have a <strong>Promo Code</strong>: <?php echo $this->Form->input('Promocode.value',array('type' => 'text','label' => false, 'div' => false, 'class' => 'inputpromocode'));?>
      <input type="submit" value="" class="submitpromo"/>
    </div>
    <!-- <input  style='width:100px;height:25px;' id='promo_button' type="button" onclick="select_promocode()" value="Add Promo code"/>-->
  </form>
</div>
<?php endif;?>
<div class="clear"></div>
<?php	if (isset($promocodes) && !empty($promocodes)) : ?>
<h3>Promo codes: </h3>
<table border="0" cellpadding="0" cellspacing="1" style="background-color:#fafafa;">
  <?php		foreach ($promocodes as $promocodeID => $promocode): ?>
  <tr>
    <td align='center' width='20'><a href="/carts/deletePromocode/<?php echo $promocodeID;?>" onclick="return confirm('Are you sure?')" class="noborder"><img
			src="<?php echo STATIC_BPONG?>/img/del.png" alt="-" /></a></td>
    <td  align='left'><?php echo $promocode['code'];  ?></td>
  </tr>
  <?php		endforeach;	 ?>
</table>
<?php	endif; ?>
<div class="clear" style="margin-top:10px"></div>
<?php echo $this->element('checkout_totals');  ?>
<?php }else{ ?>
<div class="products" style='text-align:center;'>
  <div class="you_have_no">Your cart is empty</div>
</div>
<?php } ?>