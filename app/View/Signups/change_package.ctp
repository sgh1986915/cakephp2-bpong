<?php if (!isset($this->request->data['Question']['Option'])) $this->request->data['Question']['Option'] = array();?>
<script type="text/javascript">
function submitChangePackage () {
	$('#submit_package_button').hide();
	$('#submit_package_loader').show();	
	return true;
}
</script>

<!-- Signup information -->
<div class="p10" style='border: 1px dotted #CCCCCC;margin-top:10px;margin-bottom:10px;'>
<h2>&nbsp;&nbsp;&nbsp;&nbsp;<?php if($isUpgrade):?>Upgrade<?php else:?>Change<?php endif;?> Package</h2>
<!-- Model Information -->
<div id="Attention" class="you_have_no" style='width:400px;'>
		<strong>Attention!</strong><br />
	  Upgrade package will destroy the team and the rooming assignment<br/> for currect sign-up!
</div>

<div style="width:522px; padding:-10px 20px 30px 20px; margin:0 0 30px 20px; float:left" >
<?php echo $this->Form->create('Package',array('id'=>'Step3','name'=>'ChangePackage','url'=>'/signups/changePackage/'.$signupDetails['Signup']['id'], 'onSubmit' => "return submitChangePackage();"));?>
          <div class="users form" style="margin-top:20px">
				<h3 style=" padding-left:20px">Choose the package</h3>
			  <fieldset>
  	  			<?php echo $this->Form->radio('id', Set::combine($packages, '{n}.id', '{n}.info'),array('onchange' => 'changePackage();', 'separator' =>'<br /><br />', 'legend'=>false,'label'=>false,'value'=>$this->request->data['Package']['id']));?>
		      </fieldset>
		</div>
	<div style='padding-top:20px;' class='clear' >	
		<div class="submit" id='submit_package_button'><input type="submit" value="Change"></div>
		<img id='submit_package_loader' src="<?php echo STATIC_BPONG?>/img/ajax_loader_m.gif" border="0" style='display:none;'>
		<br/>
	</div>
</div>
<div class="clear"><br/></div>
</div>