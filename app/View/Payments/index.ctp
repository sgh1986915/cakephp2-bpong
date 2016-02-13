
<h2>Payments</h2>

<?php echo $this->Form->create('Payment',array('id'=>'PaymentFilter','name'=>'PaymentFilter','action'=>'index'));?>
<fieldset>
<?php echo $this->Form->input('PaymentFilter.model');?>
<?php echo $this->Form->input('PaymentFilter.status');?>
<?php echo $this->Form->input('PaymentFilter.user_id',array('label'=>'User id','size'=>10, 'type' => 'text'));?>
<?php echo $this->Form->input('PaymentFilter.user_email',array('label'=>'User email'));?>
<?php echo $this->Form->input('PaymentFilter.promocode',array('label'=>'Promocode'));?>
<?php echo $this->Form->input('PaymentFilter.user_lastname',array('label'=>'User lastname (Like)'));?>
<?php echo $this->Form->input('PaymentFilter.user_login',array('label'=>'User login (Like)'));?>
</fieldset>
<div class="heightpad"></div>
<?php echo $this->Form->end('Filter');?>
<div class="heightpad"></div>
<?php if(!empty($payments)):?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('id');?></th>
	<th><?php echo $this->Paginator->sort('payment_date');?></th>
	<th><?php echo $this->Paginator->sort('model');?></th>
	<th><?php echo $this->Paginator->sort('amount');?></th>
	<th><?php echo $this->Paginator->sort('status');?></th>
	<th><?php echo 'user name';?></th>
	<th><?php echo 'description';?></th>
	<th><?php echo $this->Paginator->sort('reason');?></th>
	<th><?php echo 'promocode';?></th>
</tr>
<?php 
  $i = 0; 
foreach ($payments as $payment): 
    $class = ''; if ($i++ % 2 != 0) { $class = ' class="alt"'; }
?>
<?php 
$promocodesString = '-';
if(!empty($payment['Promocode'])) {
    $promocodes = array();
    foreach ($payment['Promocode'] as $promocode) {
	$promocodes[] = $promocode['code'];
    }
    $promocodesString = implode(', ', $promocodes);
}
?>

<tr<?php echo $class;?>>
		<td><?php echo $payment['Payment']['id'] ?></td>
		<td><?php echo $this->Time->niceShort($payment['Payment']['payment_date']) ?></td>
		<td><?php echo $payment['Payment']['model'] ?></td>
		<td><?php echo $payment['Payment']['amount']>=0?'$':'-$'; echo sprintf("%.2f",abs($payment['Payment']['amount'])); ?></td>
		<td><?php echo $payment['Payment']['status'] ?></td>
		<td><a href="/users/view/<?php echo $payment['User']['lgn'] ?>"><?php echo $payment['User']['lgn'] ?></a></td>
		<td><?php echo $payment['Payment']['description'] ?></td>
		<td><?php echo $payment['Payment']['reason'] ?></td>
		<td style="text-align: center;"><?php echo $promocodesString ?></td>
		<!--<td><?php echo !empty($payment['Signup']['link'])?"<a href='".$payment['Signup']['link']."'>".$payment['Payment']['description']."</a>":$payment['Payment']['description'] ?></td>-->
</tr>
<?php endforeach; ?>
<?php if (isset($total)):?>
<tr style="background-color:#E2E2E2">
	<td colspan="3">Total:</td>
	<td>$<?php echo $total;?></td>
	<td colspan="5"></td>
</tr>
<?php endif;?>
</table>
<div class="paging">
	<?php echo $this->Paginator->first(3, array(), null, array('class'=>'disabled'));?>
	<?php echo $this->Paginator->numbers();?>
	<?php echo $this->Paginator->last(3, array(), null, array('class'=>'disabled'));?>
	<?php echo $this->element('pagination'); ?>
</div>
<?php else: ?>
	<div class="you_have_no">You have no payments</div>
<?php endif; ?>