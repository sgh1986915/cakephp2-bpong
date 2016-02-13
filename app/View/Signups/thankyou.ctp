<div align="center">
<div style="text-align:center; padding:15px; font-size:14px; width:700px; border:1px dotted #ccc; margin-top:20px"><img src="<?php echo STATIC_BPONG?>/img/notice.gif" alt="notice" width="24" height="24"  />
	<br/><br/>
	<?php if (empty($modelInfo['thankyou'])):?>
	<p>
		Thanks for signing up!<BR>
	<?php else:?>
			<?php echo $modelInfo['thankyou'];?>
	<?php endif;?>
		<br/><br/>
		<a href="<?php echo SECURE_SERVER?>/signups/completeSignup/<?php echo $signupID;?>"><img src="<?php echo STATIC_BPONG;?>/img/buttons/complete_signup.gif" alt="" border="0"/></a>
		<br/><br/>
	
	
	<?php if (isset($signupID)):?>
		<iframe src="https://t.pepperjamnetwork.com/track?PID=585&AMOUNT=0&TYPE=2&OID=<?php echo $signupID?>&CURRENCY=USD" height="1" width="1" frameborder="0">
		</iframe>
        <?php  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
        ?>
        <?php 
        $amount = 0;
        if (isset($eTracking['Price'])) {
            $amount = $eTracking['Price'];    
        }
        ?>
        <?php if (!STORE_AUTH_NET_TEST_MODE) :?>
        <script src="https://trk.acetrk.com/t/EG/track.js?amount=<?php echo $amount?>&order_id=<?php echo $signupID; ?>"></script>
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
           params[3]='orderId=<?php echo $signupID; ?>';
           params[4]='promoCode=';
           params[5]='valueCurrency=USD';
           k_trackevent(params,'34');
        </script>
        
        <noscript>
           <img src="https://34.xg4ken.com/media/redir.php?track=1&token=8637972d-445c-4b9a-aa46-fb66e1af76a2&type=conv&val=<?php echo $amount?>&orderId=<?php echo $signupID; ?>&promoCode=&valueCurrency=USD" width="1" height="1">
        </noscript>
        
        <!-- "zDMB - bpong" c/o "Atrinsic",  conversion: 'bpong retargeting conversion pixel' - DO NOT MODIFY THIS PIXEL IN ANY WAY -->
		<script src="https://conversion-pixel.invitemedia.com/pixel?pixelID=28641&partnerID=13&clientID=4238&key=conv&returnType=js&orderID=<?php echo $signupID; ?>"></script>
      
        <noscript>
        <img src="https://conversion-pixel.invitemedia.com/pixel?pixelID=28641&partnerID=13&clientID=4238&key=conv&orderID=<?php echo $signupID; ?>" width="1" height="1" />
        </noscript>
        <!-- End of pixel tag -->
        
        <?php endif;?>
        <?php  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
        ?>		
		
		
	<?php endif;?>
</div>
</div>