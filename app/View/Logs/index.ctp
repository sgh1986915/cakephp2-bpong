<h2>Log Files</h2>
<?php echo $this->Form->create('filter',array('id'=>'filter','name'=>'filter', 'url'=>'/logs/index', 'type' => 'get'));?>
  <fieldset>
<?php echo $this->Form->input('type',array('label'=>"Log Type", 'type' => 'select', 'options' => array('store' => 'store', 'signup' => 'signup'), 'selected' => $type));?>
    <div class="input select">
      <label for="OrderFilterUserId">Date</label>
      <?php echo $this->Form->year('year',date('Y') - 1 , date('Y'), $year, array(), false);?> <?php echo $this->Form->month('month', $month, array(), false);?> 
  </div>
  </fieldset>
<div class="clear"></div>
<?php echo $this->Form->end('Filter');?>
<div class="clear"></div>
<table>
  <tr>
    <th>Date</th>
    <th>Action</th>
  </tr>
  <?php
$i = 1;
foreach($logFiles as $logFile):
                $i = 1-$i;
                echo ($i==0) ? '<tr class="tdata1">' : '<tr class="tdata2">';
?>
  <td style='text-align:center;'><?php echo substr($logFile, 0, -4);?></td>
    <td style='text-align:center;'><a href="/logs/showLogs/<?php echo $type;?>/<?php echo substr($logFile, 0, -4);?>">Show Logs</a></td>
  </tr>
  <?php endforeach; ?>
</table>
