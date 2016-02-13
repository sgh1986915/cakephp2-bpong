<h2>My teams</h2>
<?php if (!empty($myTeams)):?>
<table>
  <tr>
    <th>Name</th>
    <th>People in team</th>
    <th>Status</th>
    <th>Created</th>
    <th></th>
  </tr>
  <?php 
  $i = 0; 
  foreach ($myTeams as $team):
    $class = ''; if ($i++ % 2 != 0) { $class = ' class="alt"'; }
  ?>
  <tr<?php echo $class;?>>
    <td><?php echo $team['Team']['name']?></td>
    <td><?php echo $team['Team']['people_in_team']?></td>
    <td><?php echo $team['Team']['status']?></td>
    <td><?php echo $this->Time->niceDate($team['Team']['created'])?></td>
    <td style="text-align:right"><?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/view.gif" alt="View team details" title="View team details"/>', array('action'=>'view',$team['Team']['slug'], $team['Team']['id']), array('escape'=>false)); ?>
      <?php if ($team['Team']['status']!='Deleted'):?>
      &nbsp;&nbsp; <?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/edit.gif" alt="Edit team details" title="Edit team details and assign new user"/>', array('action'=>'edit',$team['Team']['slug'],$team['Team']['id']), array('escape'=>false)); ?>&nbsp;&nbsp; <?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/team_assign.gif" alt="Team assigments" title="Team assigments"/>', array('action'=>'assigments',$team['Team']['slug'],$team['Team']['id']), array('escape'=>false)); ?>&nbsp;&nbsp;
      <?php endif;?>
      <?php if ($team['Team']['status']!="Deleted"):?>
      <?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/delete.gif" alt="Delete this team" title="Delete this team"/>', array('action'=>'delete', $team['Team']['id']), array('escape'=>false),'Are you sure you want to delete this team?'); ?>
      <?php endif;?>
    </td>
  </tr>
  <?php endforeach;?>
</table>
<?php else:?>
<div class="you_have_no">You have no teams</div>
<?php endif;?>
<br />
<!-- invitations-->
<h2>My invitations</h2>
<?php if (!empty($myInvites)):?>
<table>
  <tr>
    <th>Name</th>
    <th>People in team</th>
    <th>Status</th>
    <th>Created</th>
    <th></th>
  </tr>
  <?php foreach ($myInvites as $invite):?>
  <tr>
    <td><?php echo $invite['Team']['name']?></td>
    <td><?php echo $invite['Team']['people_in_team']?></td>
    <td><?php echo $invite['Team']['status']?></td>
    <td><?php echo $this->Time->niceDate($invite['Team']['created'])?></td>
    <td style="text-align:right"><?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/view.gif" alt="View team details" title="View team details"/>', array('action'=>'view', $invite['Team']['slug'],$invite['Team']['id']), array('escape'=>false)); ?>&nbsp;&nbsp;
      <?php if ($invite['Team']['status']=="Created" || $invite['Team']['status']=="Pending" || $invite['Team']['status'] == 'Completed'):?>
      &nbsp;&nbsp; <?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/accept.gif" alt="Accept invitation to this team" title="Accept invitation to this team"/>', array('action'=>'accept', $invite['Team']['id'],urlencode($user['lgn'])), array('escape'=>false)); ?>&nbsp;&nbsp; <?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/decline.gif" alt="Decline invitation to this team" title="Decline invitation to this team"/>', array('action'=>'decline', $invite['Team']['id'],urlencode($user['lgn'])), array('escape'=>false)); ?>
      <?php endif;?>
    </td>
  </tr>
  <?php endforeach;?>
</table>
<?php else:?>
<div class="you_have_no">You have no invitations</div>
<br />
<?php endif;?>
<div class="actions">
  <ul>
    <li><span class="addbtn"><?php echo $this->Html->link('New team', array('action'=>'add'), array('class'=>'addbtn3')); ?></span></li>
  </ul>
</div>
