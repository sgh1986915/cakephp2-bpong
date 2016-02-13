<h2>Payments for the <?php echo $modelData['Signup']['model'] ?>: <?php echo  $modelData[$modelData['Signup']['model']]['name'] ?></h2>

<?php if(!empty($payments)):?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('payment_date');?></th>
	<th><?php echo $this->Paginator->sort('model');?></th>
	<th><?php echo $this->Paginator->sort('amount');?></th>
	<th><?php echo $this->Paginator->sort('status');?></th>
	<th><?php echo $this->Paginator->sort('reason');?></th>
	<th><?php echo $this->Paginator->sort('description');?></th>
</tr>
<?php foreach ($payments as $payment): ?>
<tr>
		<td><?php echo $this->Time->niceShort($payment['Payment']['payment_date']) ?></td>
		<td><?php echo $payment['Payment']['model'] ?></td>
		<td><?php echo $payment['Payment']['amount']>=0?'$':'-$'; echo abs($payment['Payment']['amount']); ?></td>
		<td><?php echo $payment['Payment']['status'] ?></td>
		<td><?php echo $payment['Payment']['reason'] ?></td>
		<td><?php echo $payment['Payment']['description'] ?></td>
</tr>
<?php endforeach; ?>
</table>
<div class="paging">
	<?php echo $this->Paginator->prev('<< '.__('previous'), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $this->Paginator->numbers();?>
	<?php echo $this->Paginator->next(__('next').' >>', array(), null, array('class'=>'disabled'));?>
</div>
<?php else: ?>
	You have no payments.
<?php endif; ?>