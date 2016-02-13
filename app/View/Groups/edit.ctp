<div class="groups form">
<?php echo $this->Form->create('Group');?>
	<fieldset>
 		<legend>Edit Group</legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
	?>
	</fieldset>
	<!-- Show Group Statuses -->
	<table cellpadding="0" cellspacing="0">
		<tr>
			<th>Status</th>
			<th>Default</th>
			<th>Action</th>
		</tr>
		<?php if(!empty($this->request->data['Status'])): ?>
			<?php foreach ($this->request->data['Status'] as $status): ?>
			<?php if (is_array($status)): ?>
			<tr>
				<td class="actions"><?php echo $status['name'] ?></td>
				<td class="actions"><?php echo $status['id']==$this->request->data['Group']['defstats_id']?"Yes":"" ?></td>
				<td class="actions">
				    <?php echo $this->Html->link('Edit', array('controller'=> 'statuses','action'=>'edit', $status['id'])); ?>
			        <?php if ($status['id'] !== $this->request->data['Group']['defstats_id']): echo $this->Html->link('Delete', array('controller'=> 'statuses','action'=>'delete', $status['id']), null, sprintf('Are you sure you want to delete # %s?', $status['name'])); endif;?>
				</td>
			</tr>
			<?php endif; ?>
			<?php endforeach; ?>
		<?php else: ?>
		<tr><td colspan="3">There are no statuses for such group</td></tr>
		<?php endif; ?>
	</table>	
<?php echo $this->Form->end('Submit');?>
</div> 