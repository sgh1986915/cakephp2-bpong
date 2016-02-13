<h2>History</h2>

<?php echo $this->Form->create('history',array('id'=>'HistoryFilter','name'=>'HistoryFilter','url'=>'/history'));?>
<fieldset>
<!--<div class="promocodes"><?php echo $this->Form->input('HistoryFilter.name',array('label'=>'Name LIKE'));?></div>-->
<?php echo $this->Form->input('HistoryFilter.type');?>
</fieldset>
<div class="heightpad"></div>
<?php echo $this->Form->end('Filter');?>
<div class="heightpad"></div>
<?php if (!empty($history)): ?>
<table>
<tr>
  <th><?php echo $this->Paginator->sort('Creator','user_id');?></th>
  <th><?php echo $this->Paginator->sort('model');?></th>
  <th><?php echo $this->Paginator->sort('type');?></th>
  <th><?php echo $this->Paginator->sort('created');?></th>
  <th>Link</th>
  <th>Description</th>
</tr>
<?php foreach ($history as $h): ?>
<tr>
  <td> <?php echo $h['User']['lgn'] ?></td>
  <td> <?php echo $h['History']['model'] ?></td>
  <td> <?php echo $h['History']['type'] ?></td>
  <td> <?php echo $this->Time->niceDate($h['History']['created']) ?> </td>
  <td> <a href="<?php echo MAIN_SERVER.$h['History']['link'] ?>">Link</a></td>
  <td> <?php echo $h['History']['description'] ?></td>
</tr>
<?php endforeach; ?>
</table>
<div class="paging">
  <?php echo $this->Paginator->first(3, array(), null, array('class'=>'disabled'));?>
  <?php echo $this->Paginator->numbers();?>
  <?php echo $this->Paginator->last(3, array(), null, array('class'=>'disabled'));?>
  <?php echo $this->element('pagination'); ?>
</div>

<?php else:?>
  There are no history records.
<?php endif;?>
