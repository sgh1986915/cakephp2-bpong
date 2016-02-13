<script type="text/javascript">
$(document).ready(function() {

	jQuery.validator.addMethod("alphanumeric", function(value, element) {
		return this.optional(element) || /^[A-Za-z0-9-_]+$/i.test(value);
	}, "Letters, numbers or underscores only please");

//working with addresses and phones
	$('#addressInformation').load("/addresses/view/User/<?php echo $this->request->data['User']['id']?>/<?php echo $this->request->data['User']['id']?>",{cache: false},
		function(){$('#addressInformation').slideDown("slow"); $('#phoneInformation').load("/phones/view/User/<?php echo $this->request->data['User']['id']?>/<?php echo $this->request->data['User']['id']?>",{cache: false},
																				function(){tb_init('a.thickbox, area.thickbox, input.thickbox'); $('#phoneInformation').slideDown("slow");});});
//EOF working with addresses and phones

// validate signup form on keyup and submit
	$("#Profile").validate({
		rules: {
			"data[User][confirm_pwd]": {
				required: false,
				minlength: 5,
				equalTo: "#UserPwd"
			},
			"data[User][email]": {
				required: true,
				email: true
			},
			"data[User][lgn]":{
				required:true,
				maxlength:20,
				minlength: 5,
				alphanumeric:true
			}
		},
		messages: {
			"data[User][confirm_pwd]": {
				minlength: "Your password must be at least 5 characters long",
				equalTo: "Please enter the same password as above"
			},
			"data[User][email]": "Please enter a valid email address",
			"data[User][lgn]": {
				required:"Letters, numbers or underscores only please",
				maxlength: "Max length: 20 characters",
				minlength: "Min length: 5 characters",
				alphanumeric:"Letters, numbers or underscores only please"
			}
		}
	});
	//EOF Validation


});

function selectDgroup(){

   if($("#dgroup_id").val()==0){
   		alert('Please specify Discount Group');
   		return false;
   }else{
   		return true;
   }
}

function DeleteAddress(addressID){
//AJAX call for deleting addresses
			$.post("/addresses/delete/<?php echo $this->request->data['User']['id']?>/"+addressID
               ,{
               		addressID: addressID
                }
               ,function(response){

               		if (response==""){
                		$("#addr_"+addressID).hide("slow");
                	} else {
                		alert(response);
                	}
                });

}

function  DeletePhone(phoneID){
//AJAX call for deleting Phone
			$.post("/phones/delete/<?php echo $this->request->data['User']['id']?>/"+phoneID
               ,{
               		phoneID: phoneID
                }
               ,function(response){
               		if (response==""){
                		$("#phone_"+phoneID).hide("slow");
                	} else {
                		alert(response);
                	}
                });

}

</script>
<?php if ($showGroup): ?>
<script type="text/javascript">
//ONLY for permitted users

$(document).ready(function() {

    $('#UsersGroup').change(function(){
        var id = $(this).val();
		StatusAjaxCall(id);
    });
     StatusAjaxCall($('#UsersGroup').val());
});

function StatusAjaxCall (GroupId) {
    $('#status').hide();
	$('#StatusLoading').show();

	 $.post("/users/groupstatuses"
	           ,{
	           		GroupId :  GroupId
	            }
	           ,function(response){
	               setTimeout("FinishStatusAjax('"+escape(response)+"')", 400);
	            });
	return false;
}

function FinishStatusAjax (response) {
	$('#status').html(unescape(response));
	$('#StatusLoading').hide();
	$('#status').show();
}
//New status
function ShowAssign() {
	$('#AssignLink').toggle('slow');
	$('#NewGroup').toggle('slow');

}

</script>
<?php endif; ?>

<div class="users form">
  <?php if(isset($id)) $url = '/users/settings/'.$id; else $url = '/users/settings';?>
  <?php echo $this->Form->create('User',array('id'=>'Profile','name'=>'Profile','url'=>$url, 'enctype' => "multipart/form-data"));?>
  <h2>Edit Profile</h2>
  <fieldset>
  <?php
		echo $this->Form->hidden('id');
		echo $this->Form->hidden('old_email');
		echo $this->Form->input('lgn', array('label' => 'User Name <span class="red">*</span>'));
		echo $this->Form->input('email', array('label' => 'Email <span class="red">*</span>'));
		echo $this->Form->input('pwd',array('type'=>'password', 'label' => 'Password <span class="red">*</span>'));
		echo $this->Form->input('confirm_pwd',array('type'=>'password', 'label' => 'Confirm Password  <span class="red">*</span>'));
		echo $this->Form->input('avatar',array('type'=>'file', 'class'=>'file'));?>
  <span style='font-size:90%;'>Image types allowed: jpg, gif, png. Maximum  image size: 500KB</span><br/>
  <?php if ($this->request->data['User']['avatar']):?>
  <?php echo $this->Image->avatar($this->request->data['User']['avatar'], false, 185);?>
  <br />
  <a href="/users/deleteAvatar/" onclick="confirm('Are you sure, you want to delete Avatar?')">Delete Avatar</a> <br/>
  <?php endif;?>      
  <div id="additional1">
    <?php
		echo $this->Form->input('firstname');
		echo $this->Form->input('middlename');
		echo $this->Form->input('lastname');
		echo $this->Form->input('gender');
		echo $this->Form->input('birthdate', array('minYear' => 1930,'maxYear' => 2005,'empty'=>"choose"));
		echo $this->Form->input('User.timezone_id',array('type' => 'select','label'=>'Time zone','options' => $timeZones));
	?>
	<div style='padding-left:70px;'>
    <div class="show" style='width:100% !important;'>
      <label for="UserShowDetails">Show optional information publicly</label>
      <?php
            echo $this->Form->input('User.show_details',array('type'=>'checkbox','label'=>false, 'div' => false));
            ?>
    </div>
    <div class="clear"></div>
    <div class="show" style='width:100% !important;'>
      <label for="UserSubscribed">Subscribe to the mail list </label>
      <?php
            echo $this->Form->input('User.subscribed',array('type'=>'checkbox','label'=>false, 'div' => false));
            echo $this->Form->hidden('User.old_subscribed');
            ?>
    </div>

    <div class="show" style='width:100% !important;'>
      <label for="UserSubscribed">Hide profile from public</label>
    <?php echo 	$this->Form->input('is_hidden', array('label' => false, 'div' => false,'type' => 'checkbox'));?>
    </div>
    <div class="clear"></div>
  </div>
  </fieldset>
  <br />
  <div class="clear"></div>
  <?php echo $this->Form->end('Submit');?>




<?php if (!empty($this->request->data['twitter']) || !empty($this->request->data['facebook'])):?>
<h4>Related accounts</h4>
  <table cellpadding="0" cellspacing="0" id="GroupsTable"  style="width:48%; background-color:#dfebfb;">
    <tr>
      <th class="actions">Site</th>
      <th class="actions">User Name / Link</th>
      <th class="actions">Actions</th>
    </tr>
	<?php if (!empty($this->request->data['facebook'])):
	if (!empty($this->request->data['facebook']['username'])) {
		$facebookName = $this->request->data['facebook']['username'];
	} else {
		$facebookName = $this->request->data['facebook']['name'];
	}

	?>
	    <tr>
	      <td class="actions">facebook.com</td>
	      <td class="actions"><a href="<?php echo $this->request->data['facebook']['link'];?>"><?php echo $facebookName;?></a></td>
	      <td class="actions"><a href="/users/remove_related_account/facebook/<?php echo $this->request->data['User']['id'];?>" onclick='return confirm("Are you sure, you want to remove this relation?")'>remove relation</a></td>
	   </tr>
	<?php endif;?>
	<?php if (!empty($this->request->data['twitter'])):?>
	    <tr>
	      <td class="actions">twitter.com</td>
	      <td class="actions"> <a href="http://twitter.com/<?php echo $this->request->data['twitter']['screen_name'];?>"><?php echo $this->request->data['twitter']['screen_name'];?></a></td>
	      <td class="actions"><a href="/users/remove_related_account/twitter/<?php echo $this->request->data['User']['id'];?>" onclick='return confirm("Are you sure, you want to remove this relation?")'>remove relation</a></td>
	   </tr>
	<?php endif;?>
</table>

<?php endif;?>

<?php if ($showDiscountGroup && !empty($histories)): ?>
	<h4>History</h4>
  <table cellpadding="0" cellspacing="0" id="GroupsTable"  style="width:48%; background-color:#dfebfb;">
    <tr>
      <th class="actions">Field</th>
      <th class="actions">New Value</th>
      <th class="actions">Old Value</th>
      <th class="actions">Date</th>
    </tr>
    <?php foreach ($histories as $history): ?>
    <tr>
      <td class="actions"><?php echo $history['UserHistory']['field']; ?></td>
      <td class="actions"><?php echo $history['UserHistory']['new_value']; ?></td>
      <td class="actions"><?php echo $history['UserHistory']['old_value']; ?></td>
      <td class="actions"><?php echo date('m.d.Y', strtotime($history['UserHistory']['created'])); ?></td>
   </tr>
    <?php endforeach; ?>
  </table>
<?php endif;?>

  <!-- Show address information -->
  <h4 >Address information</h4>
  <div id="addressInformation" style="display:none;" class="details">
    <!-- Please don't remove this DIV it's for AJAX -->
  </div>
  <!-- EOF Address information -->
  <br />
  <!-- Show Phone information -->
  <h4>Phones</h4>
  <div id="phoneInformation" style="display: none;" class="details">
    <!-- Please don't remove this DIV it's for AJAX -->
  </div>
  <!-- EOF Phone information -->
</div>
<?php if ($showGroup): ?>
<!-- user statuses  -->
<br />
<h2>Groups and statuses</h2>
<form id="UpdateStatuses" method="post" action="/users/updateStatuses/<?php echo $this->request->data['User']['id']; ?>">
  <table cellpadding="0" cellspacing="0" id="GroupsTable"  style="width:48%; background-color:#dfebfb;">
    <tr>
      <th class="actions">Group</th>
      <th class="actions">Status</th>
      <th class="actions">Action</th>
    </tr>
    <?php if(!empty($this->request->data['Status'])): ?>
    <?php $statusCnt = count($this->request->data['Status']); ?>
    <?php foreach ($this->request->data['Status'] as $status): ?>
    <tr>
      <td class="actions"><?php echo $status['Group']['name'] ?></td>
      <td class="actions" ><?php echo $this->Form->input('Statuses][][status_id',array('type' => 'select','label'=>false,'options' => $status['Statuses'],'selected'=>$status['id']));?></td>
      <td class="actions"><?php if ($statusCnt>1): echo $this->Html->link('Remove from the group', array('controller'=> 'users','action'=>'deleteFromGroup', $this->request->data['User']['id'],$status['id']), null, sprintf('Are you sure you want to remove user from the group %s?',$status['Group']['name'])); endif;?>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php else: ?>
    <tr>
      <td colspan="3">There are no statuses for such group</td>
    </tr>
    <?php endif; ?>
  </table>
  <?php echo $this->Form->end('Submit');?>
</form>
<div id="NewGroup" style="display: none;">
  <form id="NewStatusForm" method="post" action="/users/assignStatus/<?php echo $this->request->data['User']['id']; ?>">
    <div style="float: left;  margin:-10px 10px"> <?php echo $this->Form->input('Users.group',array('label'=>false)); ?> </div>
    <div style="float: left; margin-top:-10px">
      <div id="status">
        <!-- For ajax -->
      </div>
      <?php echo $this->Html->image(STATIC_BPONG.'/img/loading.gif',array('id'=>'StatusLoading','style'=>'display:none; margin-left: 20px;')) ?> </div>
    <div style="float: left; margin-left:10px"> <a href="javascript: ShowAssign();">Hide</a> </div>
    <?php echo $this->Form->end('Submit');?>
  </form>
</div>
<? if (!empty($groups)) : ?>
<div class="right"><a href="javascript: ShowAssign();" id="AssignLink"><img src="<?php echo STATIC_BPONG?>/img/btn_assign.gif" alt="assign new user" border="0" /></a></div>
<?php endif; ?>
<!-- EOF USER STATUSES -->
<?php endif; ?>
<?php if ($showDiscountGroup): ?>
<!-- USERS DISCOUNT GROUPS  -->
<div class="heightpad"></div>
<h2>Discount Groups</h2>
<table cellpadding="0" cellspacing="0" id="GroupsTable"  style="width:48%; background-color:#dfebfb;">
  <tr>
    <th class="actions">Group</th>
    <th class="actions">Actions</th>
  </tr>
  <?
		?>
  <?php foreach ($discountGroups as $discountGroup): ?>
  <tr>
    <td class="actions"><?php echo $discountGroup['StoreDiscountgroup']['name']; ?></td>
    <td class="actions"><?php echo $this->Html->link('Discounts', array('controller'=> 'store_discounts','action'=>'show_discounts',$discountGroup['StoreDiscountgroup']['id']));?> <?php echo $this->Html->link('All members', array('controller'=> 'StoreDiscountgroups','action'=>'show_members',$discountGroup['StoreDiscountgroup']['id']));?> <?php echo $this->Html->link('Remove from the group', array('controller'=> 'StoreDiscountgroupsMembers','action'=>'delete',$discountGroup['StoreDiscountgroupsMember']['id'],$this->request->data['User']['id']), null, sprintf('Are you sure you want to remove user from discount group %s?',$discountGroup['StoreDiscountgroup']['name']));?> </td>
  </tr>
  <?php endforeach; ?>
</table>
<form  method="post" action="/StoreDiscountgroupsMembers/add/user/<?php echo $this->request->data['User']['id']; ?>" onsubmit='return selectDgroup();'>
  <?php echo $this->Form->input('dgroup_id',array('id'=>'dgroup_id','type' => 'select','label'=>false,'div'=>false,'style'=>'width:200px;','class'=>"loading",'options' => $allDiscountGroups));?>
  <input type="submit" value="Assign to new Discount group"  style='width:250px;'/>
</form>
<br/>
<!-- EOF USER DISCOUNT GROUPS -->
<?php endif; ?>
