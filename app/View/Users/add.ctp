<script type="text/javascript">
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

</script>  

<div class="users form">
<?php echo $this->Form->create('User');?>
	<h2><?php echo __('Add User');?></h2>
	<fieldset>
	<?php 
		echo $this->Form->input('firstname');
		echo $this->Form->input('middlename');
		echo $this->Form->input('lastname');
		echo $this->Form->input('lgn');
		echo $this->Form->input('pwd');
		//echo $this->Form->input('gender');
		echo $this->Form->input('email');
		echo $this->Form->input('address');
		echo $this->Form->input('city');
		echo $this->Form->input('state');
		echo $this->Form->input('postalcode');
		echo $this->Form->input('country');
		//echo $this->Form->input('Group');
	?>
	</fieldset>
    <h2>Groups and statuses</h2>
	<fieldset style="background:none;">
 		<table cellpadding="0" cellspacing="0" style="width:48%; background-color:#dfebfb;" >
		<tr>
			<th class="actions">Group</th>
			<th class="actions">Status</th>
		</tr>
		<tr>
			<td>
				<?php echo $this->Form->input('Users.group',array('label'=>false)); ?>
			</td>
			<td width="300px">
				<div id="status"> 
					<!-- For ajax -->
				</div>
				<?php echo $this->Html->image(STATIC_BPONG.'/img/loading.gif',array('id'=>'StatusLoading','style'=>'display:none;')) ?> 
			</td>
		</tr>
		</table>
	</fieldset>		
<?php echo $this->Form->end('Submit');?>
</div>