<!-- required plugins -->
<script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/date.js"></script>
<!--[if IE]><script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/jquery.bgiframe.js"></script><![endif]-->
<!-- jquery.datePicker.js -->
<script type="text/javascript" src="<?php echo STATIC_BPONG?>/js/datapicker/jquery.datePicker.js"></script>
<script type="text/javascript"><!--
$(document).ready(function() {
	//Start data picker initiation
	//added by Edward
	$('.date-pick').datePicker()
				   .dpSetStartDate('01/01/2007')
				   .click(function(){$(this).attr("value",'')});

});
//EOF ready

--></script>

<h2>Accounting Page</h2>
<?php echo $this->Form->create('Signup',array('id'=>'SignupFilter','name'=>'SignupFilter','action'=>'reports'));?>
  <fieldset>
  <?php echo $this->Form->input('SignupFilter.from',array('class' => 'date-pick dp-applied'));?>
  <?php echo $this->Form->input('SignupFilter.to',array('class' => 'date-pick dp-applied'));?>
  </fieldset>
<div class="heightpad"></div>
<?php echo $this->Form->end('Filter');?>
<div class="heightpad"></div>
<?php if (!empty($signups)): ?>
<table cellpadding="0" cellspacing="0">
  <tr>
    <th>Name</th>
    <th>Signups</th>
    <th>Refunds cnt</th>
    <th>Refunds amount</th>
    <th>Paid</th>
    <th>Discount</th>
    <th>Total</th>
    <th>Start date</th>
    <th>End date</th>
  </tr>
  <?php 
    $paid     = 0;
    $discount = 0;
    $total    = 0;
    $refunds  = 0
?>
  <?php foreach ($signups as $signup): ?>
  <?php 
    $paid     = $paid + floatval($signup[0]['paid']);
    $discount = $discount +floatval($signup[0]['discount']);
    $total    = $total + floatval($signup[0]['total']);
    $refunds  = $refunds + floatval($signup[0]['refunds']);

?>
  <tr>
    <td><?php echo $signup[$signup['Signup']['model']]['name']; ?> </td>
    <td><?php echo $signup[0]['signups'] ?></td>
    <td><?php echo $signup[0]['refundscnt'] ?></td>
    <td><?php echo $signup[0]['refunds'] >=0 ?'$':'-$'; echo sprintf("%.2f",abs($signup[0]['refunds'])); ?></td>
    <td> $<?php echo sprintf("%.2f", $signup[0]['paid']) ?></td>
    <td> $<?php echo sprintf("%.2f", $signup[0]['discount'])?></td>
    <td> $<?php echo sprintf("%.2f", $signup[0]['total']) ?></td>
    <td><?php echo $this->Time->niceDate($signup[$signup['Signup']['model']]['start_date']) ?> </td>
    <td><?php echo $this->Time->niceDate($signup[$signup['Signup']['model']]['end_date']) ?> </td>
  </tr>
  <?php endforeach; ?>
  <tr>
    <td colspan="3">&nbsp;</td>
    <td><br></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3"><b>Total:</b></td>
    <td><b><?php echo $refunds >=0 ?'$':'-$'; echo sprintf("%.2f",abs($refunds)); ?></td>
    <td><b>$<?php echo sprintf("%.2f",$paid)?></b></td>
    <td><b>$<?php echo sprintf("%.2f",$discount)?></b></td>
    <td><b>$<?php echo sprintf("%.2f",$total)?></b></td>
    <td></td>
  </tr>
</table>
<?php else:?>
There are no Events or Tournaments for such criteria.
<?php endif;?>
