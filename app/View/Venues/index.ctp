<div class="venues index" style="padding:0">
<h2><?php echo 'Venues';?></h2>

<?php echo $this->Form->create('Venue',array('id'=>'VenueFilter','name'=>'VenueFilter','action'=>'index'));?>
<fieldset>
<?php echo $this->Form->input('VenueFilter.name',array('label'=>'Name LIKE'));?> 
<?php echo $this->Form->input('VenueFilter.state_id',array('label'=>'State', 'options' => array('0' => 'all') + $states));?>
<div class="promocodes" style="margin-top:1px">
      <div style="width:150px; display:inline;">
        <label for="VenueFilterIsDeleted">Show Only NBPL Venues</label>
            <?php echo $this->Form->input('VenueFilter.only_nbpl',array('label'=>false,'type'=>'checkbox')); ?>
      </div>
    </div>
</fieldset>

<div class="heightpad"></div>
<?php echo $this->Form->end('Filter');?>
<div class="heightpad"></div>

<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('venuetype_id');?></th>
	<th><?php echo $this->Paginator->sort('name');?></th>
	<th><?php echo $this->Paginator->sort('City','Address.city');?></th>
	<th><?php echo $this->Paginator->sort('Provincestate','Provincestate.name');?></th>
	<th><?php echo $this->Paginator->sort('nbpltype');?></th>
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
			<?php echo $venue['Venue']['nbpltype']; ?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/view.gif" alt="View" />', array('action'=>'view', $venue['Venue']['slug']), array('escape'=>false)); ?>
			<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/edit.gif" alt="Edit" />', array('action'=>'edit', $venue['Venue']['id']), array('escape'=>false)); ?>
			<?php echo $this->Html->link('<img src="'.STATIC_BPONG.'/img/delete_.gif" alt="Delete" />', array('action'=>'delete', $venue['Venue']['id']), array('escape'=>false), null, sprintf('Are you sure you want to delete # %s?', $venue['Venue']['id'])); ?>
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
