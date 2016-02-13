<?php
	$this->pageTitle = 'Beer Pong Forums' . $this->Forumlinks->last_title( $this->request->params['pass'] ) . " | BPONG.COM";
?>
<link href="<?php echo STATIC_BPONG?>/js/bbcode/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src='<?php echo STATIC_BPONG?>/js/bbcode/xbb.js'></script>
<script type="text/javascript">
	XBB.textarea_id = 'ForumpostText'; // id of a textarea
	XBB.area_width = '550px';
	XBB.area_height = '200px';
	XBB.state = 'plain'; // 'plain' or 'highlight'
	XBB.lang = 'en_utf8'; // 
</script>
<script type="text/javascript">
$(document).ready(function() {
	$("#Addpost").validate({
		rules: {
			"data[Forumpost][text]": "required",
			"data[Captcha][text]": "required"			
		},
		messages: {
			"data[Forumpost][text]":  "Please enter your description"
		}
	});
	//EOF Validation
	
});
//EOF ready
</script>
<div class="forumtopics index">
  <h2><?php echo $topic_name . ' - Reply';?></h2>
  <div class="forumposts form">
    <div class="rightbox">
      <div class="rightbox_top">&nbsp;</div>
      <?php echo $this->element('forumAdvert'); ?>
      <div class="rightbox_bottom">&nbsp;</div>
    </div>
    <?php echo $this->Form->create('Forumpost', array('url' => array("action" => 'add', 'id' => null, $slug), 'id' => 'Addpost'));?>
    <fieldset class="forumn">
    <?php //echo $this->Form->input('Post name');?>
    <?php
                echo $this->Form->input('Forum.actiontype',array('type' => 'hidden','value' => 'add'));
                //echo $this->Form->input('Forumpost.forumtopic_id',array('type' => 'hidden','value' => $this->request->data['Forumpost']['forumtopic_id']));
                //echo $this->Form->input('Forumtopic.forumbranch_id',array('type' => 'hidden','value' => $this->request->data['Forumtopic']['forumbranch_id']));
                ?>
    <div class="textarea" >
      <?php
                echo $this->Form->input('Forumpost.text', array( 'div' => false));
            ?>
    </div>
    <img src="/captcha/<?php echo rand(1, 10000);?>" alt="captcha" border="0" />
    <br/>
    <?php echo $this->Form->input('Captcha.text', array('div' => false, 'label' => false, 'type' => 'text', 'value' => ''));?><br/>
    Please type the letters shown above
    
    
    <div class="input toleft"><?php echo $this->Form->end(array('value' => 'Submit','class'=>'submit', 'div' => false));?></div>
    </fieldset>
  </div>
</div>
<div class="actions">
  <ul>
    <li><span class="backbtn"><?php echo $this->Html->link('Back to topics', array('controller'=> 'forumbranches', 'action'=>'index', $back_slug), array('class'=>'backbtn')); ?></span></li>
  </ul>
</div>
<script type="text/javascript">
	XBB.init();
</script>
