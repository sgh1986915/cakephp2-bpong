<div class="tournaments index">
<h2>Tournaments</h2>
<table cellpadding="0" cellspacing="0">
<tr>
	<th style="width:330px"><?php echo $this->Paginator->sort('name');?></th>
	<th><?php echo $this->Paginator->sort('signup_required');?></th>
	<th><?php echo $this->Paginator->sort('shown_on_front');?></th>
	<th><?php echo $this->Paginator->sort('start_date');?></th>
	<th><?php echo $this->Paginator->sort('end_date');?></th>
	<th class="actions">Actions</th>
</tr>
<?php
$i = 0;
foreach ($tournaments as $tournament):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>

		<td>
			<a href="/tournaments/view/<?php echo $tournament['Tournament']['slug']; ?>"> <?php echo $tournament['Tournament']['name']; ?></a>
		</td>

		<td>
			<?php echo empty ($tournament['Tournament']['signup_required'])?'No':'Yes'; ?>
		</td>
		<td>
			<?php echo empty($tournament['Tournament']['shown_on_front'])?'No':'Yes'; ?>
		</td>
		<td>
			<?php echo $this->Time->niceDate($tournament['Tournament']['start_date']); ?>
		</td>
		<td>
			<?php echo $this->Time->niceDate($tournament['Tournament']['end_date']); ?>
		</td>

		<td class="actions">
			<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/edit.gif" title="Edit" />', array('action'=>'edit', $tournament['Tournament']['slug']), array('escape'=>false)); ?>
			<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/signup_statistics.gif" title="Signup Statistics" />', array('controller'=>'statistics','action'=>'signupsStatistics', 'Tournament',$tournament['Tournament']['id']), array('escape'=>false)); ?>
			<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/teams.gif" title="Teams" />', '/teams/tournamentteams/'.$tournament['Tournament']['slug'], array('escape'=>false)); ?>
			<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/delete_.gif" title="Delete" />', array('action'=>'delete', $tournament['Tournament']['slug']), array('escape'=>false), 'Are you sure you want to delete ?'); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging2" >
	<?php echo $this->Paginator->first(3, array(), null, array('class'=>'disabled'));?>
	<?php echo $this->Paginator->numbers();?>
	<?php echo $this->Paginator->last(3, array(), null, array('class'=>'disabled'));?>
	<?php echo $this->element('pagination'); ?>
</div>
<br />
<div class="actions">
	<ul>
		<li><span class="addbtn"><?php echo $this->Html->link('New Tournament', array('action'=>'add'), array('class'=>'addbtn3')); ?></span></li>
	</ul>
</div>