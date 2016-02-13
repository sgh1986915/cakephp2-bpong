<?php 
	$MENU = Configure::read('Main.Menu');
?>
<div id="footer">
  <div class="innerfooter">
    <table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <th width='22%'>Store</th>
        <th width='33%'>The World Series of Beer Pong&trade;</th>
        <th width='20%'>BPONG Nation</th>
        <th width='25%'>The Vault</th>
      </tr>
      <tr>
        <td>
          <?php foreach ($MENU['Store'] as $menu):?>
          	<?php if (!empty($menu['link'])):?>
          		<a href="<?php echo $menu['link'];?>"><?php echo $menu['name'];?></a><br />
          	<?php endif;?>
          <?php endforeach;?>	
        </td>
        <td> 
          <?php foreach ($MENU['Wsobp'] as $menu):?>
            <?php if (!empty($menu['link'])):?>
          		<a href="<?php echo $menu['link'];?>"><?php echo $menu['name'];?></a><br />
          	<?php endif;?>         		
          <?php endforeach;?>
        </td>
        <td>
          <?php foreach ($MENU['Nation'] as $menu):?>
            <?php if (!empty($menu['link'])):?>
          		<a href="<?php echo $menu['link'];?>"><?php echo $menu['name'];?></a><br />
          	<?php endif;?>       	
          <?php endforeach;?>
        </td>
        <td>
          <?php foreach ($MENU['Vault'] as $menu):?>
            <?php if (!empty($menu['link'])):?>
          		<a href="<?php echo $menu['link'];?>"><?php echo $menu['name'];?></a><br />
          	<?php endif;?>          	
          <?php endforeach;?>
         </td>
      </tr>
    </table>
  </div>
  <div style="text-align:right; padding:10px 60px 10px 0">
  <div class="iconsset">
  <a href="http://www.facebook.com/BPONG"><img src="<?php echo STATIC_BPONG?>/img/home/icon_fb.gif"></a>
  <a href="http://twitter.com/BPONG"><img src="<?php echo STATIC_BPONG?>/img/home/icon_tw.gif"></a>
  <a href="http://www.myspace.com/wsobp"><img src="<?php echo STATIC_BPONG?>/img/home/icon_ms.gif"></a>
  <a href="/contact"><img src="<?php echo STATIC_BPONG?>/img/home/icon_mail.gif"></a>
  </div>
  <a href="<?php echo MAIN_SERVER;?>">Home</a> &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
  <a href="<?php echo MAIN_SERVER;?>/resellers">Resellers Program</a> &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
  <a href="<?php echo MAIN_SERVER;?>/terms">Terms & Conditions</a> &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
  <a href="<?php echo MAIN_SERVER;?>/pressroom">Press Room</a> &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
  <a href="<?php echo MAIN_SERVER;?>/contact">Contact us</a> &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
  <a href="<?php echo MAIN_SERVER;?>/privacy">Privacy Policy</a>
</div>
</div>
<?php if (LIVE_WEBSITE):?>
<!-- Atrinsic Landing Code -->
<SCRIPT LANGUAGE="JAVASCRIPT" SRC="//track.sendtraffic.com/script/v2_2.js"></SCRIPT>
<SCRIPT LANGUAGE="JAVASCRIPT">
<!--
	SENDROI_TrackHit('e467a4fb-a40b-4335-84bf-ed802cb4c42b');
//-->
</SCRIPT>
<NOSCRIPT>
<IMG SRC="//track.sendtraffic.com/track.aspx?ckid=e467a4fb-a40b-4335-84bf-ed802cb4c42b">
</NOSCRIPT>
<!-- End of Atrinsic Landing Code -->
	<?php if (!HTTPS_CONNECT):?>	
		<!-- "zDMB - bpong" c/o "Atrinsic",  segment: 'bpong retargeting langing page pixel' - DO NOT MODIFY THIS PIXEL IN ANY WAY -->
		<script src="https://segment-pixel.invitemedia.com/pixel?pixelID=28640&partnerID=13&clientID=4238&key=segment&returnType=js"></script>
		<noscript>
		<img src="https://segment-pixel.invitemedia.com/pixel?pixelID=28640&partnerID=13&clientID=4238&key=segment" width="1" height="1" />
		</noscript>
		<!-- End of pixel tag -->
	<?php endif;?>
<?php endif;?>
