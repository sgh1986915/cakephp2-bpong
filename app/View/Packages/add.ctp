<script type="text/javascript">
$(document).ready(function() {

// validate signup form on keyup and submit
/*Form submit*/

	$("#PackageAddForm").validate({
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
<h1 class="login"><img src="<?php echo STATIC_BPONG;?>/img/logclose.png" id="Close" class="right"  onclick="self.parent.tb_remove();" />Add new package</h1>
<div class="whitebg nopad"> <?php echo $this->Form->create('Package',array('name'=>'PackageAddForm','id'=>'PackageAddForm','url'=>'/packages/add/'.$modelName.'/'.$modelID));?>
  <fieldset>
  <div class="error" id="error-message" <?php echo isset($Error)?"":"style='display: none;'" ?>>Error while adding.</div>
<?php
            echo $this->Form->input('Package.name', array('style'=>'width:150px'));
            echo $this->Form->input('Packagedetail.price', array('style'=>'width:150px', 'label' => 'Price per person: $', 'default' => '00.00'));
            echo $this->Form->input('Packagedetail.price_team', array('style'=>'width:150px', 'label' => 'Price per team: $', 'default' => '00.00'));
?>                        
            <?php if (!empty($model[$modelName]['is_room'])):?>            
	            <div class="input text">
	           		<label for="AdditionHaveRooms">This package does not have rooms</label>
	            	<?php echo $this->Form->input('Addition.have_rooms',array('type' => 'checkbox', 'id' => 'have_rooms', 'checked' => false, 'onchange' => 'return clickHaveRooms();', 'label' => false, 'div' => false));?>
	            </div>
	            <div class="input text" id='people_in_room'>
		            <?php echo $this->Form->input('Package.people_in_room', array('style'=>'width:50px', 'div' => false));?>
	            </div>
            <?php endif;?>
                       
<?php 		echo $this->Form->input('Package.description');?>
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
  <div class="clear"></div>
</div>
<div class="clear"></div>
<img src="<?php echo STATIC_BPONG;?>/img/whitebot.png" border="0" style='float:left'>