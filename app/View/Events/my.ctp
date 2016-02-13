<div class="events index">
<h2>My Events</h2>
<table cellpadding="0" cellspacing="0">
<tr >
	<th><?php echo $this->Paginator->sort('id');?></th>
	<th><?php echo $this->Paginator->sort('name');?></th>
	<th><?php echo $this->Paginator->sort('start date','start_date');?></th>
	<th><?php echo $this->Paginator->sort('end date','start_date');?></th>
	<?php /*?>
	<th><?php echo $this->Paginator->sort('is_approved');?></th>
	<?php */ ?>
	<th><?php echo $this->Paginator->sort('comments');?></th>
	<th>rate</th>
	<th class="actions">Actions</th>
</tr>
<?php 
  $i = 0; 
foreach ($events as $event):
    $class = ''; if ($i++ % 2 != 0) { $class = ' class="alt"'; }
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $event['Event']['id']; ?>
		</td>
		<td>
		<?php echo $this->Html->link($event['Event']['name'], '/event/' . $event['Event']['id'] . '/' . $event['Event']['slug']); ?>
		</td>
		<td align="center">
			<?php echo empty($event['Event']['start_date'])?"--":$this->Time->niceShort($event['Event']['start_date']); ?>
		</td>
		<td align="center">
			<?php if (empty($event['Event']['end_date'])) {echo "--";} else {echo (substr($event['Event']['end_date'],0,10) == '0000-00-00')?"Not Defined":$this->Time->niceShort($event['Event']['end_date']);} ?>
		</td>
		<?php /*?>
		<td align="center">
			<?php echo empty($event['Event']['is_approved'])?"No":"Yes"; ?>
		</td>
		<?php */ ?>
		<td align="center">
			<?php echo $event['Event']['comments'];?>
		</td>		
		<td align="center">
			<?php echo $event['Event']['votes_plus'] - $event['Event']['votes_minus'];?>
		</td>		
		<td class="actions">
			<?php if ($accessApprove):?>
				<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/teams.gif" title="Teams" />', '/teams/eventteams/'.$event['Event']['slug'], array('escape'=>false)); ?>
				<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/signup_statistics.gif" title="Signup Statistics" />', array('controller'=>'statistics','action'=>'signupsStatistics', 'Event',$event['Event']['id']), array('escape'=>false)); ?>			
			<?php endif;?>			
			<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/edit.gif" alt="Edit" />', array('action'=>'edit', $event['Event']['id']), array('escape'=>false)); ?>
			<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/delete_.gif" alt="Delete" />', array('action'=>'delete', $event['Event']['id']), array('escape'=>false), sprintf('Are you sure you want to delete # %s?', $event['Event']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $this->Paginator->first(3, array(), null, array('class'=>'disabled'));?>
	<?php echo $this->Paginator->numbers();?>
	<?php echo $this->Paginator->last(3, array(), null, array('class'=>'disabled'));?>
	<?php echo $this->element('pagination'); ?>
</div>
<div class="actions">
	<ul>
		<li><span class="addbtn"><?php echo $this->Html->link('New Event', array('action'=>'add'), array('class'=>'addbtn')); ?></span></li>
	</ul>
</div>
