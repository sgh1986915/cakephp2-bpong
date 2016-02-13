<table width="50%">
<tr>
	<th>Team name</th>
	<th>People in team</th>
	<th>Status</th>
	<th>Created</th>
	<th>teammates</th>
	<th></th>
</tr>
<?php foreach ($teams as $team):?>
	<tr>
	    <td> <?php echo $team['Team']['name'] ?></td>
        <td> <?php echo $team['Team']['people_in_team'] ?></td>
        <td> <?php echo $team['Team']['status'] ?></td>
        <td> <?php echo $this->Time->niceDate($team['Team']['created']) ?> </td>
        <td> <?php foreach ($team['User'] as $teammate):?>
        		[<?php echo $teammate['lgn']; ?>] <?php echo $teammate['firstname']; ?> <?php echo $teammate['lastname']; ?>;<br/>
        	 <?php endforeach;?>	
        </td>
        <td> 
        	 <?php if (!empty($team['errors'])):?>
        	 	<img alt="Can not assign" src="<?php echo STATIC_BPONG?>/img/error.gif" title="<?php echo htmlspecialchars( $team['errors'])?>">
        	 <?php else:?>
        	 	<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/team_assign.gif" alt="Assign team" title="Assign team"/>', array('action'=>'AssignTournEvent',$this->request->data['TeamAssignment']['model'],$this->request->data['TeamAssignment']['model_id'],$team['Team']['id']), array('escape'=>false)); ?>&nbsp;&nbsp;
        	 <?php endif;?>
        	 <?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/view.gif" alt="View team details" title="View team details"/>', array('action'=>'view' ,$team['Team']['slug'], $team['Team']['id']), array('escape'=>false)); ?>
        </td>
	</tr>
<?php endforeach;?>
</table>