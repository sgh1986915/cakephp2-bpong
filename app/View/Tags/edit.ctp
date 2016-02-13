<h2>Edit Tag</h2>
<div class="storeSlots form"> <?php echo $this->Form->create('Tag');?>
  <fieldset>
  <div class="input text">
    <label for="TagTag">Model: </label>
    <span class="fs21"><?php echo $this->request->data['Tag']['model'];?></span></div>
  <?php echo $this->Form->input('tag');
 	echo $this->Form->hidden('id');
?>
  </fieldset>
  <?php echo $this->Form->end('Submit');?> </div>
