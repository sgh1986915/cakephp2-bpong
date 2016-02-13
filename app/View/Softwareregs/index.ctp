<h2>Software Registrations</h2>
<?php echo $this->Form->create('Softwarereg',array('id'=>'SoftwareregFilter','name'=>'SoftwareregFilter','action'=>'index'));?>
<fieldset>
<?php echo $this->Form->input('SoftwareregFilter.email');?> 
<?php echo $this->Form->input('SoftwareregFilter.lgn');?>
</fieldset>
<div class="clear"></div>
<?php echo $this->Form->end('Filter');?>
<div class="heightpad"></div>
<?php if(!empty($softwareregs)):?>
<table cellpadding="0" cellspacing="0">
  <tr>
    <th><?php echo $this->Paginator->sort('User ID');?></th>
    <th>Username</th>
    <th>Email</th>
    <th>Description</th>
    <th width="120"><?php echo $this->Paginator->sort('Key');?></th>
    <th>Accepted</th>
    <th>Banned</th>
    <th width="100">Actions</th>
    <th>WS</th>
    <th>Pr</th>
  </tr>
  <?php 
  $i = 0;
  foreach ($softwareregs as $softwarereg): 
  $class = ''; if ($i++ % 2 != 0) { $class = ' class="alt"'; }
  ?>
  <tr<?php echo $class;?>>
    <td><?php echo $softwarereg['Softwarereg']['user_id'] ?></td>
    <td><a href="/u/<?php echo $softwarereg['User']['lgn']; ?>"><?php echo $softwarereg['User']['lgn'] ?></a></td>
    <td><?php echo $softwarereg['User']['email']?></td>
    <td><?php echo $softwarereg['Softwarereg']['description'] ?></td>    
    <td><?php echo $softwarereg['Softwarereg']['key'] ?></td>    
    <td><?php if ($softwarereg['Softwarereg']['accepted']) echo 'Yes'; else echo "No";  ?></td> 
    <td><?php if ($softwarereg['Softwarereg']['banned']) echo 'Yes'; else echo 'No';?></td> 
    <td><?php if (!$softwarereg['Softwarereg']['accepted']): ?>
        <a href="/softwareregs/accept/<?php echo $softwarereg['Softwarereg']['id']; ?>">Accept</a>
        <?php endif; ?>
        <?php if (!$softwarereg['Softwarereg']['banned']): ?>
        <a href="/softwareregs/ban/<?php echo $softwarereg['Softwarereg']['id']; ?>">Ban</a>
        <?php else: ?>    
        <a href="/softwareregs/unban/<?php echo $softwarereg['Softwarereg']['id']; ?>">UnBan</a>
        <?php endif; ?>
        <a href="/softwareregs/hide/<?php echo $softwarereg['Softwarereg']['id']; ?>">Hide</a>
    </td>  
    <td><?php echo $softwarereg['Softwarereg']['wsobp'];?></td>
   	<td><?php echo $this->Html->link($softwarereg['Softwarereg']['premium'], array('action'=>'togglePremium', $softwarereg['Softwarereg']['id']), array('escape'=>false), sprintf('Are you sure you want to toggle the premium setting of this registration?', $softwarereg['Softwarereg']['id'])); ?>
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
<div class="paging" style="margin-top:20px"> <?php echo $this->Paginator->first(3, array(), null, array('class'=>'disabled'));?> <?php echo $this->Paginator->numbers();?> <?php echo $this->Paginator->last(3, array(), null, array('class'=>'disabled'));?> <?php echo $this->element('pagination'); ?> </div>
<?php else: ?>
<BR>
<div style="background:#fafafa; border:1px dotted #ccc; padding:20px; text-align:center">There are no Software Registrations</div>
<?php endif; ?>
