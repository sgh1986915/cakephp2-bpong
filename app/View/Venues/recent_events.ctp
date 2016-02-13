<h2>Venue <span class="h_subtext">&rsaquo; <?php echo $venue['Venue']['name']; ?> &rsaquo; Recent Events</span></h2>
	
<?php echo $this->Paginator->options(array('url' => $this->passedArgs, 'model' => 'Event')); ?>

  
<table cellpadding="0" cellspacing="0" class='sub_list sub_list_full sorter' >
    <tr>
      <th><?php echo $this->Paginator->sort('Name', 'Event.name', array('sorter' => true));?></th>
      <th><?php echo $this->Paginator->sort('Start Date','Event.start_date');?></th>
	  <th><?php echo $this->Paginator->sort('End Date','Event.end_date');?></th>

    </tr>
    <?php
$i = 0;
if (!empty($events)):
foreach ($events as $event):
  $class = null;
  if ($i++ % 2 != 0) {
    $class = ' class="gray"';
  }
?>
    <tr<?php echo $class;?>>	
		<td><a href="/event/<?php echo $event['Event']['id'];?>/<?php echo $event['Event']['slug'];?>"><?php echo $event['Event']['name'];?></a></td>				
		<td align="center"><?php echo empty($event['Event']['start_date'])?"--":$this->Time->niceShort($event['Event']['start_date']); ?></td>
		<td align="center"><?php if (empty($event['Event']['end_date'])) {echo "--";} else {echo (substr($event['Event']['end_date'],0,10) == '0000-00-00')?"Not Defined":$this->Time->niceShort($event['Event']['end_date']);} ?></td>  
    </tr>
    <?php endforeach; ?>
</table>
<?php else:?>
</table>
<div style='font-size:16px; text-align:center;margin:10px;'>There are no Events</div>
<?php endif;?>
<?php echo $this->element('simple_paging');?>