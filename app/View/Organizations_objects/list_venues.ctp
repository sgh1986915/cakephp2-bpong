<h2 class='hr'>Venues<?php if ($isManager):?> &nbsp;<a href="/organizations_objects/add_venue/<?php echo $organization['Organization']['id'];?>">Add Existing Venue</a>&nbsp;<span class="hr_subtext">|</span>&nbsp;<a href="/venues/add/Organization/<?php echo $organization['Organization']['id'];?>">Create New Venue</a><?php endif;?></h2>
<?php echo $this->Paginator->options(array('url' => $this->passedArgs, 'model' => 'Organization')); ?>
<br/>
<?php echo $this->Form->create('Organization',array('id'=>'OrgFilter','name'=>'OrgFilter', 'url' => '/organizations_objects/list_venues/' . $slug . '/1/', 'class' => 'userfilter'));?>
    <fieldset>
		<?php echo $this->Form->input('OrgVenueFilter.name', array('label' => 'Venue Name:'));?>
		<div class='submit'>
			<input type="submit" value="Filter"/>
		</div>
  	</fieldset>
  </form>
  <div class="clear"></div><br/>
  
<table cellpadding="0" cellspacing="0" class='sub_list sub_list_full sorter' >
    <tr>
      <th><?php echo $this->Paginator->sort('Type', 'Venue.venuetype_id', array('sorter' => true));?></th>
      <th><?php echo $this->Paginator->sort('Name', 'Venue.name', array('sorter' => true));?></th>
      <th><?php echo $this->Paginator->sort('City','Address.city');?></th>
      <th><strong>State</strong></th>
      <?php if ($isManager):?>
      <th><strong>Actions</strong></th>	
      <?php endif;?>
    </tr>
    <?php
$i = 0;
if (!empty($objects)):
foreach ($objects as $object):
  $class = null;
  if ($i++ % 2 != 0) {
    $class = ' class="gray"';
  }
?>
    <tr<?php echo $class;?>>	
		<td><?php if(!empty($object['Venue']['Venuetype']['name'])):?><?php echo $object['Venue']['Venuetype']['name'];?><?php endif;?></td>
		<td><a href="/venues/view/<?php echo $object['Venue']['slug'];?>"><?php echo $object['Venue']['name'];?></a></td>
		<td><?php echo ucfirst(strtolower($object['Venue']['Address']['city']));?></td>
		<td><?php if (!empty($object['Venue']['Address']['Provincestate']['name'])):?><?php echo ucfirst(strtolower($object['Venue']['Address']['Provincestate']['name']));?><?php endif;?></td>					
    	<?php if ($isManager):?>
    	<td><a onclick='return confirm("Are you sure?");' href="/organizations_objects/remove/<?php echo $organization['Organization']['id'];?>/<?php echo $object['OrganizationsObject']['id'];?>">Remove</a></td>
    	<?php endif;?>
    </tr>
    <?php endforeach; ?>
</table>
<?php else:?>
</table>
<div style='font-size:16px; text-align:center;margin:10px;'>There are no Events</div>
<?php endif;?>
<?php echo $this->element('simple_paging');?>