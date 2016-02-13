<h2>Promocodes</h2>
<?php echo $this->Form->create('Promocode',array('id'=>'PromocodeFilter','name'=>'PromocodeFilter','action'=>'index'));?>
<fieldset>
<?php echo $this->Form->input('PromocodeFilter.type');?> <?php echo $this->Form->input('PromocodeFilter.status');?> <?php echo $this->Form->input('PromocodeFilter.value');?>
<label>Assigned to User</label>
<?php echo $this->Form->input('PromocodeFilter.assigned_user', array('type' => 'checkbox', 'label' => false, 'div' => false));?> <?php echo $this->Form->input('PromocodeFilter.number_of_uses');?> <?php echo $this->Form->input('PromocodeFilter.code');?> <?php echo $this->Form->input('PromocodeFilter.uses_count');?>
</fieldset>
<div class="clear"></div>
<?php echo $this->Form->end('Filter');?>
<div class="heightpad"></div>
<?php if(!empty($promocodes)):?>
<table cellpadding="0" cellspacing="0">
  <tr>
    <th><?php echo $this->Paginator->sort('code');?></th>
    <th><?php echo $this->Paginator->sort('type');?></th>
    <th><?php echo $this->Paginator->sort('value');?></th>
    <th><?php echo $this->Paginator->sort('# of uses','Promocode.number_of_uses');?></th>
    <th><?php echo $this->Paginator->sort('uses count','Promocode.uses_count');?></th>
    <th><?php echo $this->Paginator->sort( 'Exp. date', 'expiration_date');?></th>
    <th>Assigned User</th>
    <th><?php echo $this->Paginator->sort('is_deleted');?></th>
    <th></th>
  </tr>
  <?php 	$i = 0; 
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
    <td><a href="/users/view/<?php echo $promocode['User']['lgn'];?>"><?php echo $promocode['User']['lgn'];?></a></td>
    <td><?php echo $promocode['Promocode']['is_deleted']?"Yes":"No" ?></td>
    <td><a href="/promocodes/edit/<?php echo $promocode['Promocode']['id'] ?>">Edit</a>&nbsp;<a href="/promocodes/delete/<?php echo $promocode['Promocode']['id'] ?>">Delete</a></td>
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
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><span class="addbtn"><a href="/promocodes/add" class="addbtn">Create new promocode</a></span></td>
    <td style="text-align:right"><span class="addbtn "><a href="/promocodes/export" target="blank" class="addbtn" >Export promocodes</a></span></td>
  </tr>
</table>
<br/>
<div class="paging" style="margin-top:20px"> <?php echo $this->Paginator->first(3, array(), null, array('class'=>'disabled'));?> <?php echo $this->Paginator->numbers();?> <?php echo $this->Paginator->last(3, array(), null, array('class'=>'disabled'));?> <?php echo $this->element('pagination'); ?> </div>
<?php else: ?>
<BR>
<div style="background:#fafafa; border:1px dotted #ccc; padding:20px; text-align:center">You have no promocodes</div>
<span class="addbtn"><a href="/promocodes/add" class="addbtn">Create new promocode</a></span>
<?php endif; ?>
