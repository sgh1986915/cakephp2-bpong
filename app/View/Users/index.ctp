<script type="text/javascript">
//assign new status
function ChangeStatus(statusID,userID,newstatusID,groupID) {

   if (statusID != newstatusID) {
        tb_remove();
     $.post("/users/changeStatus"
             ,{
                  oldStatusID :  statusID
                 ,userID      :  userID
                 ,newstatusID :  newstatusID
              }
             ,function(response){
                 setTimeout("FinishAssignAjax('"+escape(response)+"',"+userID+","+newstatusID+","+groupID+")", 400);
              });
  } else {
    tb_remove();
  }
}

function FinishAssignAjax(response,userID,newstatusID,groupID){
  response = unescape(response);
  if (response=='error') {
    alert('Error while assign');
  } else {
    location.reload(true);
  }

}
</script>

<div class="users index"> <?php echo $this->Form->create('User',array('id'=>'UserFilter','name'=>'UserFilter','action'=>'index'));?>
  <h2>Users</h2>
    <fieldset>
    <?php echo $this->Form->input('UserFilter.searchby',array('type' => 'select','label'=>'Search By','options' => array('AND'=>'AND','OR'=>'OR')));?>
    <?php echo $this->Form->input('UserFilter.id',array('label'=>'ID')); ?>
    <?php echo $this->Form->input('UserFilter.firstname', array('label' => 'First Name'));?>
    <?php echo $this->Form->input('UserFilter.lgn', array('label' => 'Login'));?>
    <?php echo $this->Form->input('UserFilter.lastname', array('label' => 'Last Name'));?>
    <?php echo $this->Form->input('UserFilter.email');?>
    <?php echo $this->Form->input('UserFilter.city',array('label'=>'City')); ?>
    <?php echo $this->Form->input('UserFilter.provincestate_id',array('type' => 'select','label'=>'State','options' => $states));?>
    <!--
<div class="promocodes"><div class="input text">
    <label for="UserFilterStatus">Status</label>
      <select name="data[UserFilter][status]"  id="UserFilterStatus" >
        <option value="0">doesn't metter</option>
        <?php foreach($groups as $group):?>
        <option disabled ><?php echo $group['Group']['name']; ?> </option>
        <?php foreach ($group['Status'] as $status):?>
        <?php if(is_array($status)):?>
        <option <?php if(isset($this->request->data['UserFilter']['status']) && $this->request->data['UserFilter']['status']==$status['id']){ echo " selected ";}?> value="<?php echo $status['id']; ?>">&nbsp;&nbsp;&nbsp;<?php echo $status['name']; ?> </option>
        <?php endif;?>
        <?php endforeach;?>
        <?php endforeach;?>
      </select>
</div>
    </div>
    -->
    <div class="promocodes" style="margin-top:1px">
      <div style="width:150px; display:inline;">
        <label for="UserFilterIsDeleted">Is deleted</label>
        <?php echo $this->Form->input('UserFilter.is_deleted',array('type'=>'checkbox','label'=>false)); ?>
      </div>
    </div>
    </fieldset>
<div class="clear"></div>
  <?php echo $this->Form->end('Filter');?>
<div class="clear"></div>
  <table cellpadding="0" cellspacing="0" style="background-color:#fafafa; font-size:10px; text-align:center">
    <tr>
      <th><?php echo $this->Paginator->sort('id');?></th>
      <th><?php echo $this->Paginator->sort('firstname');?></th>
      <th><?php echo $this->Paginator->sort('lastname');?></th>
      <th><?php echo $this->Paginator->sort('lgn');?></th>
      <th><?php echo $this->Paginator->sort('email');?></th>
      <th><?php echo $this->Paginator->sort('created');?></th>
      <th><?php echo $this->Paginator->sort('is_deleted');?></th>
      <th class="actions">Groups - Statuses</th>
      <th class="actions">Actions</th>
    </tr>
    <?php
$i = 0;
foreach ($users as $user):
  $class = null;
  if ($i++ % 2 == 0) {
    $class = ' class="altrow"';
  }
?>
    <tr<?php echo $class;?>>
      <td style="font-size:10px"><?php echo $user['User']['id']; ?> </td>
      <td><?php echo $user['User']['firstname']; ?> </td>
      <td><?php echo $user['User']['lastname']; ?> </td>
      <td style="text-align:left"><a href="/u/<?php echo $user['User']['lgn'] ?>"><?php echo $user['User']['lgn'] ?></a> </td>
      <td style="text-align:left"><?php echo $user['User']['email']; ?> </td>
      <td><?php echo $this->Time->niceShort($user['User']['created']); ?> </td>
      <td style="width:50px"><?php echo empty($user['User']['is_deleted'])?"No":"Yes"; ?> </td>
      <td nowrap="nowrap" class="actions"><?php if (!empty($user['Status'])): ?>
        <ul>
          <?php foreach ($user['Status'] as $status):?>
          <li><?php echo $this->Html->link($status['Group']['name'], array('controller'=> 'groups','action'=>'edit', $status['Status']['group_id'])); ?> - <a id="status_<?php echo $user['User']['id']?>" href="/users/changeStatusForm?inlineId=status_<?php echo $user['User']['id']?>&height=150&width=300&modal=true&userID=<?php echo $user['User']['id']?>&groupID=<?php echo $status['Group']['id'] ?>&statusID=<?php echo $status['Status']['id'] ?>" class="thickbox" title="Change status"><?php echo $status['Status']['name']; ?></a> </li>
          <?php endforeach; ?>
        </ul>
        <?php else: ?>
        There are no groups for such user
        <? endif; ?>
      </td>
      <td class="actions" style="letter-spacing:-1px; width:68px"><?php if ($user['User']['id']!=VISITOR_USER): ?>
        <?php echo $this->Html->link('Edit', array('action'=>'settings', $user['User']['id'])); ?> <?php echo empty($user['User']['is_deleted'])?
      $this->Html->link('Delete', array('action'=>'delete', $user['User']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $user['User']['id'])):
      $this->Html->link('Restore', array('action'=>'restore', $user['User']['id']), null, sprintf(__('Are you sure you want to restore # %s?'), $user['User']['id'])); ?>
      <?php echo $this->Html->link('Login', array('action'=>'useSiteAsUser', $user['User']['id'])); ?>

        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
<div class="paging"> <?php echo $this->Paginator->first(3, array(), null, array('class'=>'disabled'));?> <?php echo $this->Paginator->numbers();?> <?php echo $this->Paginator->last(3, array(), null, array('class'=>'disabled'));?> <?php echo $this->element('pagination'); ?> </div>
<!--
<div class="actions">
  <ul>
    <li><span class="addbtn"><?php echo $this->Html->link('New User', array('controller'=> 'users','action'=>'add'), array('class'=>'addbtn')); ?></span></li>
  </ul>
</div>
 -->
