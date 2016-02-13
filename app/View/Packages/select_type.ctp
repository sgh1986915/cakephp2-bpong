<script type="text/javascript">
function savePackagesType () {
	var for_team = $("input[name='for_team']:checked").val();
	$('#for_team').val(for_team);
	$('#Step3').submit();
	tb_remove();
}
</script>
<h1 class="login"><img src="<?php echo STATIC_BPONG;?>/img/logclose.png" id="Close" class="right"  onclick="self.parent.tb_remove();" />Payment Options</h1>
<div class="whitebg nopad">
  <fieldset style='font-size: 15px;color: #333333;line-height: 20px;'>

  Would you like to pay for your entire team entrance,
  or just for yourself (in which case your teammate will have to pay for himself)?

<br/><br/>
  <input name="for_team" type="radio" value="0" checked> Pay for just yourself: $<?php echo sprintf("%01.2f", $package['packagedetails']['price']);?><br/>
  <input name="for_team" type="radio" value="1"> Pay for your whole team: $<?php echo sprintf("%01.2f", $package['packagedetails']['price_team']);?><span class='red'>*</span> <br/>
<br/>
  <?php if ($package['packages']['people_in_room'] > 0): ?>
        <div style='font-style:italic;'>
        <span class='red'>*</span> You should only choose the 'Pay for your whole
        Team' option if your roommates will be EXACTLY
        THE SAME PEOPLE as your teammates. If your room
        will contain any player that is not on your team, or if
        your team will contain any player that is not in your room,
        then each person MUST pay for their entrance.
        </div>
        <br/>
  <?php endif; ?>
  <div class="submit"><input type="button" value="Submit" onclick = 'savePackagesType();'></div>
  </fieldset>
  <div class="clear"></div>
</div>
<div class="clear"></div>
<img src="<?php echo STATIC_BPONG;?>/img/whitebot.png" border="0" style='float:left'>