<script type="text/javascript">
	function showArray(k) {

		$('#array_'+k).slideDown();
		}
</script>

<h2>Log Files</h2>
<?php echo $this->Form->create('filter',array('id'=>'filter','name'=>'filter', 'url'=>'/logs/showLogs/' . $type . '/' . $logFile, 'type' => 'get'));?>
  <fieldset>
<?php echo $this->Form->input('user_id',array('label'=>'User ID', 'value' => $userID));?>
  </fieldset>
<div class="clear"></div>
<?php echo $this->Form->end('Filter');?>
<div class="clear"></div>
<table>
  <tr>
    <th>Time</th>
    <th>Title</th>
    <th>UserID</th>
    <th>Model</th>
    <th>Model ID</th>
    <th>Description</th>
    <th>Array</th>
  </tr>
  <?php
$i = 1;
$k = 0;
foreach($logs as $log):
                $k++;
                $i = 1-$i;
                echo ($i==0) ? '<tr class="tdata1">' : '<tr class="tdata2">';
?>
  <td><?php if(!empty($log['Tm'])) echo $log['Tm']?></td>
    <td><?php if(!empty($log['Tle'])) echo $log['Tle'];?></td>
    <td><?php if (!empty($log['Uid'])): ?>
      <a href="/u/0/<?php echo $log['Uid'];?>"><?php echo $log['Uid'];?></a>
      <?php endif;?></td>
    <td><?php if(!empty($log['Mdl'])) echo $log['Mdl'];?></td>
    <td><?php if(!empty($log['Mdlid'])) echo $log['Mdlid'];?></td>
    <td><?php if(!empty($log['Dn'])) echo $log['Dn'];?></td>
    <td><?php if (!empty($log['Ary'])): ?>
      <a href="#TB_inline?height=1000&width=1000&inlineId=array<?php echo $k;?>&modal=true" class="thickbox">show array</a>
      <div style='display:none;'  id='array<?php echo $k;?>'>
        <input type="submit"  value="close" onclick="tb_remove()" />
        <pre style='float:none;background:none;color:white;font-size:11px;'><?php print_r(unserialize(stripslashes($log['Ary'])));?></pre>
      </div>
      <?php endif;?>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
