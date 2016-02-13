<?php
	$this->pageTitle = 'Beer Pong Forums' . $this->Forumlinks->last_title( $this->request->params['pass'] ) . " | BPONG.COM";
?>
<script type="text/javascript">
$(document).ready(function() {
	$("#Addforum").validate({
		rules: {
			  "data[Forumbranch][name]":"required"
			, "data[Forumbranch][description]": "required"
			, "data[Captcha][text]": "required"	
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

<h2><?php echo 'Add Forum';?></h2>
<div class="rightbox">
  <div class="rightbox_top">&nbsp;</div>
  <?php echo $this->element('forumAdvert'); ?>
  <div class="rightbox_bottom">&nbsp;</div>
</div>
<div class="forumbranches form"> <?php echo $this->Form->create('Forumbranch', array('url' => array('action' => 'add','id' => null, $slug), 'id' => 'Addforum'));?>
    <fieldset class="forumn">
  <?php	echo $this->Form->input('name');?>
  <div class="textarea">
    <?php echo $this->Form->input('description', array('type'=>'textarea', 'error' => false, 'div' => false)); ?>
    <div class="clear"></div>
  </div>
      <img src="/captcha/<?php echo rand(1, 10000);?>" alt="captcha" border="0">
    <br/>
    <?php echo $this->Form->input('Captcha.text', array('div' => false, 'label' => false, 'type' => 'text', 'value' => ''));?><br/>
    Please type the letters shown above
  <div class="input toleft"> <?php echo $this->Form->end(array('value' => 'Submit','class'=>'submit', 'div' => false));?> </div>
  </fieldset>
</div>
<div class="actions">
  <ul>
    <li><span class="backbtn"><?php echo $this->Html->link('Back to forum', array('action'=>'index'), array('class'=>'backbtn'));?></span></li>
  </ul>
</div>
