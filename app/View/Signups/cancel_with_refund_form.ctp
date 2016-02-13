
<script type="text/javascript">
tb_pathToImage = "";
$(document).ready(function() {

// validate signup form on keyup and submit
/*Form submit*/

  $("#CancelWithRefund").validate({
    submitHandler: function(form) {
                jQuery(form).ajaxSubmit({
                  cache: false,
                  beforeSubmit: beforeSubmit,
                    success: showResponse
                });
        },
    rules: {
      "data[Signup][amount]": {
        required: true,
        number: true
      }
    }
  });
  //EOF Validation


});

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

function beforeSubmit(){
  //$('#error-message').hide('slow');
  $('#Loginsubmitbtn').hide();
  $('#Loginloadbtn').show();

}


</script>

<?php echo $this->Form->create('Signup',array('id'=>'CancelWithRefund','name'=>'CancelWithRefund','url'=>'/signups/cancelWithRefund/'.$signupDetails['Signup']['id']));?>
<h1 class="login"><img src="<?php echo STATIC_BPONG?>/img/logclose.jpg" id="Close" class="right" style="cursor:pointer; padding:4px 0px 0px 0px;"  onclick="self.parent.tb_remove();" />Cancel with refund</h1>

    <fieldset style="border:none; width: 310px;" class="loginpad">

  <?php
    echo $this->Form->input('amount',array('legend'=>'Amount ','style'=>'width: 50px; margin-left: 10px'));
    echo $this->Form->input('reason',array('type'=>'textarea','label'=>false,'rows'=>4,'cols'=>20,'style'=>'width: 250px;'));
  ?>

     <div id="Loginsubmitbtn">
       <input value="Submit" class="submit" type="submit">
     </div>
       <div id="Loginloadbtn" style="display:none;">
       <img src="<?php echo STATIC_BPONG?>/img/ajax_loader_m.gif" border="0">
     </div>

     </fieldset>
   </form>