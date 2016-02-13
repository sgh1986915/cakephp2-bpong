
<h2>Payments</h2>
<!--
<div class="toleft">
<fieldset>
<h2>Filter</h2>
<?php
	echo $this->Form->input('Filter.from');
	echo $this->Form->input('Filter.to');
	echo $this->Form->input('Filter.status');
?>
</fieldset>
</div>
<?php echo $this->Form->end('Submit');?>
 -->
 <?php if(!empty($payments)):?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('payment_date');?></th>
	<th><?php echo $this->Paginator->sort('model');?></th>
	<th><?php echo $this->Paginator->sort('amount');?></th>
	<th><?php echo $this->Paginator->sort('status');?></th>
	<th><?php echo $this->Paginator->sort('reason');?></th>
	<th><?php echo $this->Paginator->sort('description');?></th>
    <th></th>
    <th></th>
</tr>
<?php 
  $i = 0; 
foreach ($payments as $payment): 
    $class = ''; if ($i++ % 2 != 0) { $class = ' class="alt"'; }
?>
<tr<?php echo $class;?>>
		<td><?php echo $this->Time->niceShort($payment['Payment']['payment_date']) ?></td>
		<td><?php echo $payment['Payment']['model'] ?></td>
		<td><?php echo $payment['Payment']['amount']>=0?'$':'-$'; echo sprintf("%.2f",abs($payment['Payment']['amount'])); ?></td>
		<td><?php echo $payment['Payment']['status'] ?></td>
		<td><?php echo $payment['Payment']['reason'] ?></td>
		<td><?php echo $payment['Payment']['description'] ?></td>
		<td></td>
		<td></td>
</tr>
<?php endforeach; ?>
</table>
<div class="paging">
	<?php echo $this->Paginator->prev('<< '.__('previous'), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $this->Paginator->numbers();?>
	<?php echo $this->Paginator->next(__('next').' >>', array(), null, array('class'=>'disabled'));?>
</div>
<?php else: ?>
	<div class="you_have_no">You have no payments</div>
<?php endif; ?>