<script type="text/javascript">
function saveAffil () {
	var orgSlug = $('#org_id').val();
	if (!orgSlug) {
		alert('Please select Organization');
		return false;
	}
	window.location.href = '/o_join/' + orgSlug + '/1/profile/'	
	tb_remove();
	return false;
}
</script>
<h1 class="login"><img src="<?php echo STATIC_BPONG;?>/img/logclose.png" id="Close" class="right"  onclick="self.parent.tb_remove();" />Select Organization</h1>
<div class="whitebg nopad">
  <fieldset style='font-size: 15px;color: #333333;line-height: 20px;width:90% !important;'>
  <br/>
  <?php echo $this->Form->input('Affil.org_id',array('id'=>'org_id','type' => 'select','style'=>'width:190px;', 'label' => false,'options' => array('0' => 'Select Organization') + $organizations));?>
  <br/>
  <div class="submit"><input type="button" value="Join" onclick = 'saveAffil();'></div>
  <br/>
  </fieldset>
    <br/>
  <div class="clear"></div>
</div>
<div class="clear"></div>
<img src="<?php echo STATIC_BPONG;?>/img/whitebot.png" border="0" style='float:left'>