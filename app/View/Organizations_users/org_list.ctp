<h2>Members</h2>
<?php echo $this->Paginator->options(array('url' => $this->passedArgs, 'model' => 'OrganizationsUser')); ?>
<div class="users index">
  <table cellpadding="0" cellspacing="0" class='sub_list sub_list_full sorter' >
    <tr>
      <th><strong>Avatar</strong></th>
      <th><?php echo $this->Paginator->sort('Login', 'lgn', array('sorter' => true));?></th>
      <th><?php echo $this->Paginator->sort('Name', 'firstname', array('sorter' => true));?></th>
      <th><?php echo $this->Paginator->sort('Joined', 'OrganizationsUser.created', array('sorter' => true));?></th>
      <th><?php echo $this->Paginator->sort('Role', 'OrganizationsUser.role', array('sorter' => true));?></th>
      <?php if ($isManager):?>
      <th>Actions</th>
      <?php endif;?>
    </tr>
    <?php
$i = 0;
foreach ($members as $user):
  $class = null;
  if ($i++ % 2 != 0) {
    $class = ' class="gray"';
  }
?>
    <tr<?php echo $class;?>>
      <td style='padding:3px 0px;'><?php if ($user['User']['avatar']):?><?php echo $this->Image->avatar($user['User']['avatar']);?><?php endif;?></td>
      <td style="text-align:left"><a href="/u/<?php echo $user['User']['lgn'] ?>"><?php echo $user['User']['lgn'] ?></a> </td>
      <td><?php if ($user['User']['firstname']) {echo $this->Formater->userName($user['User'], 1);} ?> </td>
      <td><?php  echo $this->Time->niceShort($user['OrganizationsUser']['created'])?></td>
      <td><?php  echo ucfirst($user['OrganizationsUser']['role']);?></td>
      <?php if ($isManager):?>
      <td><a href="/organizations_users/manage/<?php echo $user['OrganizationsUser']['id'];?>" >Manage</a></td>	
      <?php endif;?>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php echo $this->element('simple_paging');?>
</div>

<br/><br/>
<?php if ($isManager):?>
<h2>Pending Members</h2>
<?php if (empty($pendingMembers)):?>
	<div style='text-align:center;padding:10px;'>There are no pending members</div>
<?php else:?>
	<div class="users index">
	  <table cellpadding="0" cellspacing="0" class='sub_list sub_list_full sorter' >
	    <tr>
	      <th>Login</th>
	      <th>Name</th>
	      <th>Joined</th>
	      <th>Role</th>
	      <?php if ($isManager):?>
	      	<th>Actions</th>
	      <?php endif;?>
	    </tr>
	    <?php
	$i = 0;
	foreach ($pendingMembers as $user):
	  $class = null;
	  if ($i++ % 2 != 0) {
	    $class = ' class="gray"';
	  }
	?>
	    <tr<?php echo $class;?>>
	      <td style="text-align:left"><a href="/u/<?php echo $user['User']['lgn'] ?>"><?php echo $user['User']['lgn'] ?></a> </td>
	      <td><?php if ($user['User']['firstname']) {echo $this->Formater->userName($user['User'], 1);} ?> </td>
	      <td><?php  echo $this->Time->niceShort($user['OrganizationsUser']['created'])?></td>
	      <td><?php  echo ucfirst($user['OrganizationsUser']['role']);?></td>
	      <?php if ($isManager):?>
	      <td><a href="/organizations_users/accept/<?php echo $user['OrganizationsUser']['id'];?>" onclick="return confirm('Accept?');">Accept</a>&nbsp;&nbsp;&nbsp;<a href="/organizations_users/decline/<?php echo $user['OrganizationsUser']['id'];?>" onclick="return confirm('Decline?');">Decline</a></td>
	      <?php endif;?>
	    </tr>
	    <?php endforeach; ?>
	  </table>
	</div>
<?php endif;?>

<br/>
<h2>Declined Members</h2>
<?php if (empty($declinedMembers)):?>
	<div style='text-align:center;padding:10px;'>There are no declined members</div>
<?php else:?>

	<div class="users index">
	  <table cellpadding="0" cellspacing="0" class='sub_list sub_list_full sorter' >
	    <tr>
	      <th>Login</th>
	      <th>Name</th>
	      <th>Joined</th>
	      <th>Role</th>
	      <?php if ($isManager):?>
	      <th>Actions</th>
	      <?php endif;?>
	    </tr>
	    <?php
	$i = 0;
	foreach ($declinedMembers as $user):
	  $class = null;
	  if ($i++ % 2 != 0) {
	    $class = ' class="gray"';
	  }
	?>
	    <tr<?php echo $class;?>>
	      <td style="text-align:left"><a href="/u/<?php echo $user['User']['lgn'] ?>"><?php echo $user['User']['lgn'] ?></a> </td>
	      <td><?php if ($user['User']['firstname']) {echo $this->Formater->userName($user['User'], 1);} ?> </td>
	      <td><?php  echo $this->Time->niceShort($user['OrganizationsUser']['created'])?></td>
	      <td><?php  echo ucfirst($user['OrganizationsUser']['role']);?></td>
	      <?php if ($isManager):?>
	      <td><a href="/organizations_users/accept/<?php echo $user['OrganizationsUser']['id'];?>" onclick="return confirm('Accept?');">Accept</a></td>	      
	    <?php endif;?>
	    </tr>
	    <?php endforeach; ?>
	  </table>
	</div>
<?php endif;?>
<br/>
<h2>Invited Members</h2>
<?php if (empty($invitedMembers)):?>
	<div style='text-align:center;padding:10px;'>There are no invited members</div>
<?php else:?>

	<div class="users index">
	  <table cellpadding="0" cellspacing="0" class='sub_list sub_list_full sorter' >
	    <tr>
	      <th>Login</th>
	      <th>Name</th>
	      <th>Joined</th>
	      <th>Role</th>
	    </tr>
	    <?php
	$i = 0;
	foreach ($invitedMembers as $user):
	  $class = null;
	  if ($i++ % 2 != 0) {
	    $class = ' class="gray"';
	  }
	?>
	    <tr<?php echo $class;?>>
	      <td style="text-align:left"><a href="/u/<?php echo $user['User']['lgn'] ?>"><?php echo $user['User']['lgn'] ?></a> </td>
	      <td><?php if ($user['User']['firstname']) {echo $this->Formater->userName($user['User'], 1);} ?> </td>
	      <td><?php  echo $this->Time->niceShort($user['OrganizationsUser']['created'])?></td>
	      <td><?php  echo ucfirst($user['OrganizationsUser']['role']);?></td>
	    </tr>
	    <?php endforeach; ?>
	  </table>
	</div>
<?php endif;?>
<script type="text/javascript">
	function findUser() {
		var lgn = $('#user_name').val();
		var email = $('#user_email').val();
		
		if (lgn || email) {
			$('#obj_list').hide();
			$('#loader').show();
			$.post("/organizations_users/find_user/", { "lgn": lgn, "email": email, "organization_id": <?php echo $organization['Organization']['id'];?>},
					   function(data) {
						$('#loader').hide();
						$('#obj_list').html(data);
						$('#obj_list').show();						
			});
		}
		return false;
	}
</script>

<h2 class='hr'>Invite Members</h2>
<br/><br/>
<?php echo $this->Form->input('User.email', array('style' => 'width:200px;', 'label' => 'Email', 'id' => 'user_email')); ?>
<label for="user_email">OR</label><br/>
<?php echo $this->Form->input('User.lgn', array('style' => 'width:200px;', 'label' => 'Username', 'id' => 'user_name')); ?>
<br/><br/>
<div class="submit"><input type="submit" class="submit" value="Find" onclick='return findUser();'></div>
<br/>
<div id='loader' style='text-align:center;display:none;width:100%;' >
	<img src="<?php echo STATIC_BPONG?>/img/ajax-loader.gif" alt="" border="0">
</div>

<div id='obj_list' style='text-align:center;width:100%;' ></div>

<br/><br/><br/><br/>
<?php endif;?>