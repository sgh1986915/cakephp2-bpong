<h2>Add new Tag</h2>
<div class="storeSlots form"> <?php echo $this->Form->create('Tag');?>
  <fieldset>
  <label for="TagTag">Model:</label>
  <span class="fs21"><?php echo $model;?></span>
  <div class="clear"></div>
  <?php echo $this->Form->input('tag');?> <?php echo $this->Form->hidden('model', array('value' => $model));?>
  </fieldset>
  <?php echo $this->Form->end('Submit');?> </div>
