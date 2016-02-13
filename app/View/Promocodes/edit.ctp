<!-- required plugins -->
<script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/date.js"></script>
<!--[if IE]><script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/jquery.bgiframe.js"></script><![endif]-->
<!-- jquery.datePicker.js -->
<script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/jquery.datePicker.js"></script>
<script type="text/javascript">

function deleteUser(deleteUser)  {
	if(confirm('Are you sure you want to remove user from this promocode?')) {
	    $("#PromocodeAssignUserId").val('');
	    $("#assign_user_email").html('');
	}
}	
function assignUser(id, email)  {
    $("#PromocodeAssignUserId").val(id);
    $("#assign_user_email").html(email);
}	
$(document).ready(function() {


    //MANAGER VALIDATION
	$("#Members").validate({
		submitHandler: function(form) {
                jQuery(form).ajaxSubmit({
                    beforeSubmit: Hidemember,
                    success: showResponse
                });
        },
		rules: {
				"data[Members][email]": {
				  required: true,
				  email: true
			    }
		},
		messages: {
			"data[Members][email]": "Please enter a valid email address"
		}
	});
	function Hidemember(){
		 $("#SubmitButton").hide('slow');
	      $("#ERROR").hide();
	      $("#MemberInformation").hide(function(){ $('#Loading').show();});
	}

	function showResponse(responseText)  {
			 
	   		 $('#Loading').hide('slow'); 
	 		 $("#SubmitButton").show('slow');
	 		 
		  if (responseText==""){  		
		  		$('#ERROR').show('slow');
		  } else {
		  		$('#MemberInformation').html(responseText);
		  		$('#MemberInformation').show('slow');
		  }  
	}	
	
	//EOF MANAGER Validation		
	

$('#PromocodeAssigments').load("/promocodes_assigments/view/<?php echo $this->request->data['Promocode']['id']?>",{cache: false},function(){tb_init('a.thickbox, area.thickbox, input.thickbox');});

	 $('.date-pick').datePicker({clickInput:true}).dpSetStartDate('01/01/2007').click(function(){$(this).attr("value",'')});

// validate signup form on keyup and submit
	$("#PromocodeEditForm").validate({
		rules: {
			"data[Promocode][type]": "required",
			"data[Promocode][code]": "required",
			"data[Promocode][number_of_uses]": "required"
		},
		messages: {
		}
	});
	//EOF Validation


});
//EOF ready

function generateCode(){

	$('#Generate').hide();
	$('#generateloadbtn').show();
	$.ajaxSetup({cache:false});
	$.getJSON('/promocodes/generateCode', {cache: false},
			  function(answer){
				  $('#generateloadbtn').hide();
				  $('#Generate').show();
				  $('#PromocodeCode').val(answer);
			  }
	);

}

function DeleteAssigment(assigmentID){
//AJAX call for deleting assigments
			$.post("/promocodes_assigments/delete/"+assigmentID
               ,{
               		assigmentID: assigmentID
                }
               ,function(response){
               		if ($.trim(response)==""){
                		$("#assigment_"+assigmentID).hide("slow");
                	} else {
                		alert(response);
                	}
                });

}


</script>

<h2>Edit promocode</h2>
<?php echo $this->Form->create('Promocode',array('name'=>'PromocodeEditForm','id'=>'PromocodeEditForm','url'=>'/promocodes/edit/'.$this->request->data['Promocode']['id']));?>
  <fieldset>
  <?php
		echo $this->Form->hidden('old_code');
		echo $this->Form->hidden('id');
		echo $this->Form->input('type');?>
    <?php echo $this->Form->input('code');?>
    
      <input type="button" name="name" id="Generate" value="Generate new code" onclick="generateCode();" class="submit_gray"/>
      <div id="generateloadbtn" style="display:none;"> <img src="<?php echo STATIC_BPONG?>/img/ajax_loader_m.gif" border="0"> </div>
  <?php
		echo $this->Form->input('value');
		echo $this->Form->input('number_of_uses');
		echo $this->Form->input('expiration_date', array('type'=> 'text', 'size' => 10 ,'class' => 'date-pick dp-applied'));
		echo $this->Form->input('threshold');
	?>
  <div class="input text">
    <label for="PromocodeNumberOfUses">Assigned User</label>
    <?php echo $this->Form->hidden('assign_user_id');?> <span id='assign_user_email'>
    <?php if(isset($this->request->data['User']['email'])) { echo $this->request->data['User']['email']; echo '  <a href="#" onclick="deleteUser(); return false;">remove</a>' ;}?>
    </span> </div>
  <!-- Assigned promocodes -->
  <div id="PromocodeAssigments">
    <!-- For AJAX showing assigments information don't remove this div -->
  </div>
  <?php echo $this->Form->input('description',array('cols'=>620));?>
  </fieldset>
  <div class="clear"></div>
  <?php echo $this->Form->end('Submit');?> <?php echo $this->Form->create('Members',array('id'=>'Members','name'=>'Members','url'=>'/promocodes/findByEmail'));?> <div class="heightpad"></div>
  <h4>Find and add user to Promocode</h4>
  <label>Email</label>
  <input id="MemberEmail" type="text" value="" name="data[User][email]"/><br />
<div class="submit"><input type="submit" value="Find" /></div>
  <div id="Loading" style="display:none;text-align:center;"> <?php echo $this->Html->image(STATIC_BPONG.'/img/loading.gif',array('id'=>'StatusLoading')) ?> </div>
  <div id="ERROR" style="display: none;">Can't find such user.</div>
  <div id="MemberInformation" style="display: none;"></div>
  <?php echo $this->Form->end();?> <br/>

