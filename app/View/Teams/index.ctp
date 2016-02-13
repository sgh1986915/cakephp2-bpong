<h2>All Teams</h2>
<?php echo $this->Form->create('Team',array('id'=>'TeamFilter','name'=>'TeamFilter','action'=>'index'));?>
<fieldset>
<?php echo $this->Form->input('TeamFilter.name',array('label'=>'Name LIKE'));?> <?php echo $this->Form->input('TeamFilter.lgn',array('label'=>'User login LIKE'));?> <?php echo $this->Form->input('TeamFilter.status');?>
</fieldset>
<div class="heightpad"></div>
<?php echo $this->Form->end('Filter');?>
<div class="heightpad"></div>
<?php if (!empty($teams)): ?>
<table>
  <tr>
    <th><?php echo $this->Paginator->sort('name');?></th>
    <th><?php echo $this->Paginator->sort('people_in_team');?></th>
    <th><?php echo $this->Paginator->sort('status');?></th>
    <th><?php echo $this->Paginator->sort('created');?></th>
    <th></th>
  </tr>
  <?php 
  $i = 0; 
  foreach ($teams as $team): 
    $class = ''; if ($i++ % 2 != 0) { $class = ' class="alt"'; }
  ?>
  <tr<?php echo $class;?>>
    <td><?php echo $team['Team']['name'] ?></td>
    <td><?php echo $team['Team']['people_in_team'] ?></td>
    <td><?php echo $team['Team']['status'] ?></td>
    <td><?php echo $this->Time->niceDate($team['Team']['created']) ?> </td>
    <td>
	<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/view.gif" alt="View team details" title="View team details"/>', array('action'=>'view',$team['Team']['slug'], $team['Team']['id']), array('escape'=>false)); ?>
	<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/edit.gif" alt="Edit team details" title="Edit team details and assign new user"/>', array('action'=>'edit',$team['Team']['slug'],$team['Team']['id']), array('escape'=>false)); ?>
	<?php if ($team['Team']['status']!="Deleted"):?>
    <?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/team_assign.gif" alt="Team assigments" title="Team assigments"/>', array('action'=>'assigments',$team['Team']['slug'], $team['Team']['id']), array('escape'=>false)); ?>
	<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/delete.gif" alt="Delete this team" title="Delete this team"/>', array('action'=>'delete', $team['Team']['id']), array('escape'=>false),'Are you sure you want to delete this team?'); ?>
    <?php endif;?>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<div class="paging"> <?php echo $this->Paginator->first(3, array(), null, array('class'=>'disabled'));?> <?php echo $this->Paginator->numbers();?> <?php echo $this->Paginator->last(3, array(), null, array('class'=>'disabled'));?> <?php echo $this->element('pagination'); ?> </div>
<?php else:?>
There are no teams matching the criteria.
<?php endif;?>
