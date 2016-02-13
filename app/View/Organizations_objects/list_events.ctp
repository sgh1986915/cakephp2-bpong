<h2 class='hr'>Events<?php if ($isManager):?> &nbsp;<a href="/organizations_objects/add_event/<?php echo $organization['Organization']['id'];?>">Add Existing Event</a>&nbsp;<span class="hr_subtext">|</span>&nbsp;<a href="/events/add/Organization/<?php echo $organization['Organization']['id'];?>">Create New Event</a><?php endif;?></h2>

<?php echo $this->Paginator->options(array('url' => $this->passedArgs, 'model' => 'Organization')); ?>
<br/>
<?php echo $this->Form->create('Organization',array('id'=>'OrgFilter','name'=>'OrgFilter', 'url' => '/organizations_objects/list_events/' . $slug . '/1/', 'class' => 'userfilter'));?>
    <fieldset>
		<?php echo $this->Form->input('OrgEventFilter.name', array('label' => 'Event Name:'));?>
		<div class='submit'>
			<input type="submit" value="Filter"/>
		</div>
  	</fieldset>
  </form>
  <div class="clear"></div><br/>
  
<table cellpadding="0" cellspacing="0" class='sub_list sub_list_full sorter' >
    <tr>
      <th><?php echo $this->Paginator->sort('Name', 'Event.name', array('sorter' => true));?></th>
      <th><?php echo $this->Paginator->sort('Start Date','Event.start_date');?></th>
	  <th><?php echo $this->Paginator->sort('End Date','Event.end_date');?></th>
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
		<td><a href="/event/<?php echo $object['Event']['id'];?>/<?php echo $object['Event']['slug'];?>"><?php echo $object['Event']['name'];?></a></td>				
		<td align="center"><?php echo empty($object['Event']['start_date'])?"--":$this->Time->niceShort($object['Event']['start_date']); ?></td>
		<td align="center"><?php if (empty($object['Event']['end_date'])) {echo "--";} else {echo (substr($object['Event']['end_date'],0,10) == '0000-00-00')?"Not Defined":$this->Time->niceShort($object['Event']['end_date']);} ?></td>  
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