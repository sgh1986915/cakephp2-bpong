<div align="center">
<div style="text-align:center; padding:15px; font-size:14px; width:700px; border:1px dotted #ccc; margin-top:20px"><img src="<?php echo STATIC_BPONG?>/img/notice.gif" alt="notice" width="24" height="24" align="absmiddle" />
	<?php if($error == 1){ ?>
	<br/>Error. You have not current Order now!<br/>
	<?php }else{ ?>
		<br/>
		<b>Thank You for Order!</b><br/>
		Your Order Number: 	<?php echo $orderNumber ?><br/>
		You	can check your order status by link: <a href="/storeOrders/order/<?php echo $orderNumber; ?>">Order Status</a><br/>
		Or <a href="/order_status">Find Order</a>
		
		<br/><br/>
	<?php if ($error == 2):?>
		<span style='color:red;'>Attention! An error occurred while processing the order. Please <a href="/contact">contact us</a> for your order.</span>
	<?php endif;?>	
	
	<?php if (!$testMode) {?>
			<img src="https://www.pepperjamnetwork.com/tracking/trackmerchant.php?PID=585&AMOUNT=<?php echo $amount?>&TYPE=1&OID=<?php echo $orderNumber?>&CURRENCY=USD" height="1" width="1" border="0">
		<?php } ?>
        <?php  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
        ?>
    <?php if (!STORE_AUTH_NET_TEST_MODE) :?>
    <img src="https://aan.atrinsic.com/i_track_sale/548/<?php echo $amount?>/<?php echo $orderNumber; ?>/&sale_status=Pending">
    <img src="https://aan.atrinsic.com/i_track_sale/557/<?php echo $amount?>/<?php echo $orderNumber; ?>/&sale_status=Pending">
    
    <script src="https://trk.acetrk.com/t/EG/track.js?amount=<?php echo $amount?>&order_id=<?php echo $orderNumber; ?>"></script>
    <script type=text/javascript>
       var hostProtocol = (("https:" == document.location.protocol) ? "https" : "http");
       document.write('<scr'+'ipt src="', hostProtocol+
       '://34.xg4ken.com/media/getpx.php?cid=8637972d-445c-4b9a-aa46-fb66e1af76a2','" type="text/JavaScript"><\/scr'+'ipt>');
    </script>
    <script type=text/javascript>
       var params = new Array();
       params[0]='id=8637972d-445c-4b9a-aa46-fb66e1af76a2';
       params[1]='type=conv';
       params[2]='val=<?php echo $amount?>';
       params[3]='orderId=<?php echo $orderNumber; ?>';
       params[4]='promoCode=';
       params[5]='valueCurrency=USD';
       k_trackevent(params,'34');
    </script>
    
    <noscript>
       <img src="https://34.xg4ken.com/media/redir.php?track=1&token=8637972d-445c-4b9a-aa46-fb66e1af76a2&type=conv&val=<?php echo $amount?>&orderId=<?php echo $orderNumber; ?>&promoCode=&valueCurrency=USD" width="1" height="1">
    </noscript>
    <!-- "zDMB - bpong" c/o "Atrinsic",  conversion: 'bpong retargeting conversion pixel' - DO NOT MODIFY THIS PIXEL IN ANY WAY -->
    <script src="https://conversion-pixel.invitemedia.com/pixel?pixelID=28641&partnerID=13&clientID=4238&key=conv&returnType=js&orderID=<?php echo $orderNumber; ?>"></script>
    <noscript>
    <img src="https://conversion-pixel.invitemedia.com/pixel?pixelID=28641&partnerID=13&clientID=4238&key=conv&orderID=<?php echo $orderNumber; ?>" width="1" height="1" />
    </noscript>
    <!-- End of pixel tag -->
    
	<script type="text/javascript">var journeycode='255fc24b-2b48-4ef5-aedf-6710494792d8';var captureConfigUrl='cdsusa.veinteractive.com/CaptureConfigService.asmx/CaptureConfig';</script> 
	<script type="text/javascript">try { var vconfigHost = (("https:" == document.location.protocol) ? "https://" : "http://"); document.write(unescape("%3Cscript src='" + vconfigHost + "configusa.veinteractive.com/vecapture.js' type='text/javascript'%3E%3C/script%3E")); } catch(err) {} </script>    
    
    <?php endif;?>
    
    <?php  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
    ?>					
	<?php } ?>
</div>
</div>

