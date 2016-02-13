<script type="text/javascript">
$(document).ready(function() {

// validate signup form on keyup and submit
/*Form submit*/

	$("#PackageEditForm").validate({
		submitHandler: function(form) {
                jQuery(form).ajaxSubmit({
                    beforeSubmit: beforeSubmit,                   
                    success: showResponse
                });
        },
		rules: {
			"data[Package][name]": "required"
		},
		messages: {
			"data[Package][name]": "This field can not be empty!"
		}
	});
	//EOF Validation

	<?php if ($this->request->data['Package']['people_in_room']):?> 
	$('#people_in_room').hide();
	$('#have_rooms').attr('checked', 'checked');
	<?php endif;?>
	
});
function beforeSubmit () {
	  $("#form_loader").show();	
	  tb_remove();	
}
//After Submit
function showResponse(responseText)  {	
  if (responseText=="Error"){
  		$('#error-message').show('slow');
  	    $("#form_loader").hide();
  } else {
	    $('#form_loader').show();
  		$('#PackagesInformation').load("/packages/view/<?php echo $modelName.'/'.$modelID?>",{cache: false},function(){$('#form_loader').hide();});
  }
}
function clickHaveRooms () {
	if ($('#people_in_room').css('display') == 'none') {
		$('#people_in_room').show();
	} else {
		$('#people_in_room').hide();
	}	
	return false;	
}
</script>

<div class="phone form">
<h1 class="login"><img src="<?php echo STATIC_BPONG;?>/img/logclose.png" id="Close" class="right"  onclick="self.parent.tb_remove();" />Edit  package</h1>
<div style="background-color:#FFFFFF" class="whitebg nopad">
<?php echo $this->Form->create('Package',array('name'=>'PackageEditForm','id'=>'PackageEditForm','url'=>'/packages/edit/'.$modelName.'/'.$modelID.'/'.$packageID));?>
	<fieldset>
	<div class="error" id="error-message" <?php echo isset($Error)?"":"style='display: none;'" ?>>Error while editing.</div>
	<?php echo $this->Form->input('Package.name', array('style'=>'width:150px')); ?>                        
            <?php if (!empty($model[$modelName]['is_room'])):?>            
	            <div class="input text">
	           		<label for="AdditionHaveRooms">This package does not have rooms</label>
	            	<?php echo $this->Form->input('Addition.have_rooms',array('type' => 'checkbox', 'id' => 'have_rooms', 'checked' => false, 'onchange' => 'return clickHaveRooms();', 'label' => false, 'div' => false));?>
	            </div>
	            <div class="input text" id='people_in_room'>
		            <?php echo $this->Form->input('Package.people_in_room', array('style'=>'width:50px', 'div' => false));?>
	            </div>
            <?php endif;?>   
			<?php echo $this->Form->input('Package.description');?>
<br class='clear'/>
  <table border="0" cellspacing="0" cellpadding="0" class="boxes">
  	<?php if (isset($AdminMenu) && !empty($AdminMenu)): ?>
    <tr>
      <td class="labeltd">Is hidden</td>
      <td><?php echo $this->Form->input('Package.is_hidden',array('type'=>'checkbox','label'=>false)); ?></td>
    </tr>
    <tr>
      <td class="labeltd">Allow Team to Play</td>
      <td><?php echo $this->Form->input('Package.allow_team',array('type'=>'checkbox', 'checked' => 'checked' , 'label'=>false)); ?></td>
    </tr>
    <?php else:?>
	<?php echo $this->Form->input('Package.is_hidden',array('type'=>'hidden','label'=>false, 'value' => 1)); ?>   
	<?php echo $this->Form->input('Package.allow_team',array('type'=>'hidden','label'=>false, 'value' => 1)); ?>  
    <?php endif;?>
  </table>
  </fieldset>
<?php echo $this->Form->end('Submit');?>
<div class="tb_bottom_l"><div class="tb_bottom_r"></div></div>
</div>
</div>