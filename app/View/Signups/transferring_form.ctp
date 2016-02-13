<?php echo $this->Html->css(array('jquery.autocomplete'))
                  .$this->Html->script(array('jquery.autocomplete'))
        ?>
<script type="text/javascript">
tb_pathToImage = "";
$(document).ready(function() {

//Autocomplete
 $("#autoComplete").autocomplete("/users/autoComplete",  
 {  
	 width: 380,			 
	 minChars: 3,  
	 matchContains: true,
	 cacheLength: 10,  
	 formatItem: function(row, i, max) {
			return row[0];
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

//EOF complete

		// validate signup form on keyup and submit
		/*Form submit*/
		
		  $("#Transferring").validate({
		    submitHandler: function(form) {
		                jQuery(form).ajaxSubmit({
		                  cache: false,
		                  beforeSubmit: beforeSubmit,
		                    success: showResponse
		                });
		        },
		    rules: {
		      "data[Signup][login]": {
		        required: true
		      }
		    }
  });
  //EOF Validation


});
/// EOF ready
///////////////////////////////////////////////////////////
function showResponse(responseText)  {
  $('#Loginloadbtn').hide();
  $('#Loginsubmitbtn').show();

   if (responseText==""){
       tb_remove();
       window.location.reload()
   } else {
       alert(responseText);
   }
}
//////////////////////////////////////////////////////
function beforeSubmit(){
  //$('#error-message').hide('slow');
  $('#Loginsubmitbtn').hide();
  $('#Loginloadbtn').show();
}

//////////////EOF autocomplete///////////////////// 
</script>
<?php echo $this->Form->create('Signup',array('id'=>'Transferring','name'=>'Transferring','url'=>'/signups/transferring/'.$signupDetails['Signup']['id']));?>

<h1 class="login">
  <div class="left">Transferring to another user</div>
  <div class="right"> <img src="<?php echo STATIC_BPONG?>/img/logclose.png" id="Close" onclick="self.parent.tb_remove();" /></div>
</h1>
<div class="whitebg nopad"> 
<fieldset class="narrow">
<?php
    echo $this->Form->input('login',array('legend'=>'Login ','style'=>'width: 150px; margin-left: 10px','id'=>'autoComplete'));    
  ?>
<div id="Loginsubmitbtn" class="submit input">
  <input value="Submit" class="submit" type="submit">
</div>
<div id="Loginloadbtn" style="display:none;"> <img src="<?php echo STATIC_BPONG?>/img/ajax_loader_m.gif" border="0"> </div>
</fieldset>
  <div class="clear"></div>
</div>
<div class="clear"></div>
<img src="<?php echo STATIC_BPONG;?>/img/whitebot.png" border="0" style='float:left'>