<script type="text/javascript">
function ragsSubmit(){
	$('#Loginloadbtn').show();
}
$(document).ready(function() {
    $("#setUserTags").autocomplete("/tags/autocomplete/<?php echo $modelName;?>", {
        		width: 320,
        		max: 4,
        		highlight: false,
        		multiple: true,
        		multipleSeparator: ", ",
        		scroll: true,
        		scrollHeight: 300
    });				   				   			   				   
});

</script>


<?php echo $this->Form->create('UserTag',array('id'=>'UserTag','UserTag'=>'Login','url'=>'/tags/ajaxAddUsers/', 'onsubmit' => 'ragsSubmit();'));?>
<h1 class="tb_tags_header"><img src="<?php echo STATIC_BPONG;?>/img/logclose.png" id="Close" class="right" style="cursor:pointer;"  onclick="self.parent.tb_remove();" />Add your tags</h1>

    <fieldset style="border:none;" class="loginpad">
	<?php
		echo $this->Form->input('tags',array('legend'=>'','width'=>'100','label'=>false,'value'=>'', 'id' => 'setUserTags'));
		echo $this->Form->hidden('modelID',array('value' => $modelID));
		echo $this->Form->hidden('modelName',array('value' => $modelName));
		echo $this->Form->hidden('authorID',array('value' => $authorID));
	?>
	<span style='color:#B7B7B7;'>separate multiple tags with commas</span>
	<br/><br/>
     <div id="Loginsubmitbtn"  class="submit">
     	<input value="Submit" type="submit">
     </div>
      <div id="Loginloadbtn" style="display:none;">
     	<img src="<?php echo STATIC_BPONG;?>/img/ajax_loader_m.gif" border="0">
     </div>
     </fieldset>
   </form>

    <div class="loginmenu" style='height:0px;padding-bottom:0px;'>

    </div>
<img src="<?php echo STATIC_BPONG;?>/img/tb_tags_bottom.png" border="0">