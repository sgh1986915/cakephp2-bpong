<div class="venuetypes view">
<h2><?php echo __('Venuetype');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $venuetype['Venuetype']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $venuetype['Venuetype']['name']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('Edit Venuetype'), array('action'=>'edit', $venuetype['Venuetype']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Venuetype'), array('action'=>'delete', $venuetype['Venuetype']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $venuetype['Venuetype']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Venuetypes'), array('action'=>'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Venuetype'), array('action'=>'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Venues'), array('controller'=> 'venues', 'action'=>'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Venue'), array('controller'=> 'venues', 'action'=>'add')); ?> </li>
	</ul>
</div>
	<div class="related">
		<h3><?php echo __('Related Venues');?></h3>
	<?php if (!empty($venuetype['Venue'])):?>
		<dl>	<?php $i = 0; $class = ' class="altrow"';?>
			<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $venuetype['Venue']['id'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('User Id');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $venuetype['Venue']['user_id'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Official Image Id');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $venuetype['Venue']['official_image_id'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Venuetype Id');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $venuetype['Venue']['venuetype_id'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Name');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $venuetype['Venue']['name'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Address');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $venuetype['Venue']['address'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('City');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $venuetype['Venue']['city'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Provincestate Id');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $venuetype['Venue']['provincestate_id'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Postalcode');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $venuetype['Venue']['postalcode'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Country Id');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $venuetype['Venue']['country_id'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Description');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $venuetype['Venue']['description'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Web Address');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $venuetype['Venue']['web_address'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Phone');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $venuetype['Venue']['phone'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Latitude');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $venuetype['Venue']['latitude'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Longitude');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $venuetype['Venue']['longitude'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('IsApproved');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $venuetype['Venue']['isApproved'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $venuetype['Venue']['created'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Updated');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $venuetype['Venue']['updated'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Deleted');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $venuetype['Venue']['deleted'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Verified');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $venuetype['Venue']['verified'];?>
&nbsp;</dd>
		</dl>
	<?php endif; ?>
		<div class="actions">
			<ul>
				<li><?php echo $this->Html->link(__('Edit Venue'), array('controller'=> 'venues', 'action'=>'edit', $venuetype['Venue']['id'])); ?></li>
			</ul>
		</div>
	</div>
	