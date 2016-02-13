<div class="venues index" style="padding:0">
<h2>My Venues</h2>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('venuetype_id');?></th>
	<th><?php echo $this->Paginator->sort('name');?></th>
	<th><?php echo $this->Paginator->sort('City','Address.city');?></th>
	<th><?php echo $this->Paginator->sort('Provincestate','Provincestate.name');?></th>
	<th><?php echo $this->Paginator->sort('phone');?></th>
	<th class="actions"><?php echo 'Actions';?></th>
</tr>
<?php
$i = 0;
foreach ($venues as $venue):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $venue['Venuetype']['name']; ?>
		</td>
		<td>
			<?php echo $venue['Venue']['name']; ?>
		</td>
		<td>
			<?php echo $venue['Address']['city']; ?>
		</td>
		<td>
			<?php echo $venue['Provincestate']['name']; ?>
		</td>
		<td>
			<?php echo $venue['Venue']['phone']; ?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/view.gif" alt="View" />', array('action'=>'view', $venue['Venue']['slug']), array('escape'=>false)); ?>
			<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/edit.gif" alt="Edit" />', array('action'=>'edit', $venue['Venue']['id']), array('escape'=>false)); ?>
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
<span class="ie6_btn"><?php echo $this->Html->link('New Venue', array('action'=>'add'), array('class'=>'addbtn4')); ?></span>
