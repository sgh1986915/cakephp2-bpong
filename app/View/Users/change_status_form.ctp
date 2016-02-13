<h1 class="login"><img src="<?php echo STATIC_BPONG;?>/img/logclose.jpg" id="Close" class="right" style="cursor:pointer; padding:4px 0px 0px 0px;"  onclick="self.parent.tb_remove();" />Change user status to:</h1>

<fieldset style="border:none;" class="loginpad">
<?php echo $this->Form->input('Status.new_id',array('type' => 'select','label'=>'','options' => $statuses,'selected'=>$statusID));?>
<input id="Login" value="Change"  onclick="ChangeStatus(<?php echo $statusID.','.$userID ?>,$('#StatusNewId').val(),<?php echo $groupID ?>);" type="submit"  class="submit_gray" >&nbsp;

</fieldset>