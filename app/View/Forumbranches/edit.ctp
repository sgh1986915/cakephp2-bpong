<?php
	$this->pageTitle = 'Beer Pong Forums' . $this->Forumlinks->last_title( $this->request->params['pass'] ) . " | BPONG.COM";
?>
<script type="text/javascript">
$(document).ready(function() {
	$("#Addforum").validate({
		rules: {
			  "data[Forumbranch][name]":"required"
			, "data[Forumbranch][description]": "required"
		},
		messages: {
			  "data[Forumbranch][name]":  "Please enter your name"
			, "data[Forumbranch][description]":  "Please enter your description"
		}
	});
	//EOF Validation
	
});
//EOF ready
</script>

<div class="forumbranches form"> <?php echo $this->Form->create('Forumbranch',array( 'id' => 'Addforum', 'url' => array ('controller'=>'forumbranches', 'action' => 'edit', 'id' => null, $slug)));?>
  <h2><?php echo 'Edit Forum';?></h2>
  <div class="rightbox">
    <div class="rightbox_top">&nbsp;</div>
    <?php echo $this->element('forumAdvert'); ?>
    <div class="rightbox_bottom">&nbsp;</div>
  </div>
  <fieldset class="forumn">
  <?php	echo $this->Form->input('name'); ?>
  <div class="textarea"> <?php echo $this->Form->input('description', array('type'=>'textarea', 'div' => false)); ?> </div>
  <div class="input toleft"> <?php echo $this->Form->end(array('value' => 'Submit','class'=>'submit', 'div' => false));?> </div>
  </fieldset>
</div>
<div class="actions">
  <ul>
    <li><span class="backbtn"><?php echo $this->Html->link('Back to forum', array('action'=> 'index', $back_slug), array('class'=>'backbtn'));?></span></li>
    <?php if($Deleted): ?>
    <li><?php echo $this->Html->link('Delete', array('action'=>'delete', $slug), array('class'=>'delbtn'), sprintf('Are you sure you want to delete # %s?', $this->Form->value('Forumbranch.id'))); ?>
      <?php endif; ?>
    </li>
  </ul>
</div>
