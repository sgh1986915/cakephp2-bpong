<div class="venuefeatures view">
<h2><?php echo __('Venuefeature');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $venuefeature['Venuefeature']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $venuefeature['Venuefeature']['name']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('Edit Venuefeature'), array('action'=>'edit', $venuefeature['Venuefeature']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Venuefeature'), array('action'=>'delete', $venuefeature['Venuefeature']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $venuefeature['Venuefeature']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Venuefeatures'), array('action'=>'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Venuefeature'), array('action'=>'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Venues'), array('controller'=> 'venues', 'action'=>'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Venue'), array('controller'=> 'venues', 'action'=>'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Venues');?></h3>
	<?php if (!empty($venuefeature['Venue'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('User Id'); ?></th>
		<th><?php echo __('Official Image Id'); ?></th>
		<th><?php echo __('Venuetype Id'); ?></th>
		<th><?php echo __('Name'); ?></th>
		<th><?php echo __('Address'); ?></th>
		<th><?php echo __('City'); ?></th>
		<th><?php echo __('Provincestate Id'); ?></th>
		<th><?php echo __('Postalcode'); ?></th>
		<th><?php echo __('Country Id'); ?></th>
		<th><?php echo __('Description'); ?></th>
		<th><?php echo __('Web Address'); ?></th>
		<th><?php echo __('Phone'); ?></th>
		<th><?php echo __('Latitude'); ?></th>
		<th><?php echo __('Longitude'); ?></th>
		<th><?php echo __('IsApproved'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Updated'); ?></th>
		<th><?php echo __('Deleted'); ?></th>
		<th><?php echo __('Verified'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($venuefeature['Venue'] as $venue):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $venue['id'];?></td>
			<td><?php echo $venue['user_id'];?></td>
			<td><?php echo $venue['official_image_id'];?></td>
			<td><?php echo $venue['venuetype_id'];?></td>
			<td><?php echo $venue['name'];?></td>
			<td><?php echo $venue['address'];?></td>
			<td><?php echo $venue['city'];?></td>
			<td><?php echo $venue['provincestate_id'];?></td>
			<td><?php echo $venue['postalcode'];?></td>
			<td><?php echo $venue['country_id'];?></td>
			<td><?php echo $venue['description'];?></td>
			<td><?php echo $venue['web_address'];?></td>
			<td><?php echo $venue['phone'];?></td>
			<td><?php echo $venue['latitude'];?></td>
			<td><?php echo $venue['longitude'];?></td>
			<td><?php echo $venue['isApproved'];?></td>
			<td><?php echo $venue['created'];?></td>
			<td><?php echo $venue['updated'];?></td>
			<td><?php echo $venue['deleted'];?></td>
			<td><?php echo $venue['verified'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller'=> 'venues', 'action'=>'view', $venue['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller'=> 'venues', 'action'=>'edit', $venue['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('controller'=> 'venues', 'action'=>'delete', $venue['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $venue['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Venue'), array('controller'=> 'venues', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>
