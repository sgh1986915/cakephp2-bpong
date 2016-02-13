<h2>Promocodes</h2>
<?php if(!empty($promocodes)):?>
<table cellpadding="0" cellspacing="0">
<tr>
  <th>Code</th>
  <th>Type</th>
  <th>Value</th>
  <th>Limit</th>
  <th>Used</th>
  <th>Expiration date</th>
</tr>
<?php 
$i = 0; 
foreach ($promocodes as $promocode): 
$class = ''; if ($i++ % 2 != 0) { $class = ' class="alt"'; }
?>
<tr<?php echo $class;?>>
    <td><?php echo $promocode['Promocode']['code'] ?></td>
    <td><?php echo $promocode['Promocode']['type'] ?></td>
    <td><?php echo $promocode['Promocode']['value'] ?></td>
    <td><?php echo $promocode['Promocode']['number_of_uses'] ?></td>
    <td><?php echo $promocode['Promocode']['uses_count'] ?></td>
    <td align="center"><?php echo empty($promocode['Promocode']['expiration_date'])?"---":$this->Time->niceDate($promocode['Promocode']['expiration_date']) ?></td>
  </tr>
<?php endforeach; ?>
<?php if (isset($total)):?>
<tr style="background-color:#E2E2E2">
  <td colspan="3">Total:</td>
  <td>$<?php echo $total;?></td>
  <td colspan="4"></td>
</tr>
<?php endif;?>
</table>
<br />
<br/>
<?php else: ?>
  <BR>
  <div style="background:#fafafa; border:1px dotted #ccc; padding:20px; text-align:center">You have no promocodes</div>
<?php endif; ?>
