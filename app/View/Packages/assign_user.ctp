<?php echo $this->Html->css(array('jquery.autocomplete'))
                  .$this->Html->script(array('jquery.autocomplete'))
?>

<script type="text/javascript">
$(document).ready(function() {

	 $("#UserEmail").autocomplete("/users/autoComplete/email",  
			 {  
		 			 width: 380,			 
					 minChars: 3,  
					 matchContains: true,
					 cacheLength: 10,  
					 formatItem: function(row, i, max) {
							return row[1];
						},
					  formatMatch: function(row, i, max) {
							return row.name + " " + row.to;
						},
					  formatResult: function(row) {
							return row.to;
						}
					 //scrollHeight: 220,					   
					 //autoFill: false  
			  });  
	
// validate signup form on keyup and submit
/*Form submit*/
	$("#PackageAssignUserForm").validate({
		submitHandler: function(form) {
                jQuery(form).ajaxSubmit({
                    success: showResponse
                });
        },
		rules: {
			
		},
		messages: {
			
		}
	});
	//EOF Validation

});

//After Submit
function showResponse(responseText)  {
  if (responseText=="Error"){
  		$('#error-message').show('slow');
  } else {
  		$('#PackagesInformation').load("/packages/view/<?php echo $modelName.'/'.$modelID?>",{cache: false},function(){tb_init('a.thickbox, area.thickbox, input.thickbox');});
  		tb_remove();
  }
}
</script>

<div class="phone form">
<h1 class="login"><img src="<?php echo STATIC_BPONG?>/img/logclose.jpg" id="Close" class="right" style="cursor:pointer; pediting:4px 0px 0px 0px;"  onclick="self.parent.tb_remove();" />Assign new user to the package</h1>
<div style="background-color:#FFFFFF">
<?php echo $this->Form->create('User',array('name'=>'PackageAssignUserForm','id'=>'PackageAssignUserForm','url'=>'/packages/assignUser/'.$modelName.'/'.$modelID.'/'.$packageID));?>
	<fieldset>
	<div class="error" id="error-message" <?php echo isset($Error)?"":"style='display: none;'" ?>>Error while adding.</div>
	<?php
            echo $this->Form->input('User.email');
	?>
	
	</fieldset>
<?php echo $this->Form->end('Submit');?>
<div class="tb_bottom_l"><div class="tb_bottom_r"></div></div>
</div>
</div>