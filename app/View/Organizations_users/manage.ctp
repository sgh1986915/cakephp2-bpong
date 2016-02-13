<h2>Manage organization's member</h2>
<br/>

<?php echo $this->Form->create('OrganizationsUser', array('enctype'=>"multipart/form-data", 'url' => '/organizations_users/manage/' . $id));?>

<div class="input select">
<label for="OrganizationsUserStatus">User</label>
<a href="/u/<?php echo $organizationsUser['User']['lgn'] ?>"><?php echo $organizationsUser['User']['lgn'] ?></a>
</div>
<?php echo $this->Form->hidden('OrganizationsUser.id');?>
<?php echo $this->Form->input('OrganizationsUser.role', array('type' => 'select', 'options' => array('member' => 'member', 'manager' => 'manager', 'creator' => 'creator')));?>
<?php echo $this->Form->input('OrganizationsUser.status', array('type' => 'select', 'options' => array('pending' => 'pending', 'declined' => 'declined', 'accepted' => 'accepted')));?>
<br/>
<div class="submit"><?php echo $this->Form->end(array('value' => 'Submit','class'=>'submit', 'div' => false));?></div>
