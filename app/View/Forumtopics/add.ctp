<link href="<?php echo STATIC_BPONG?>/js/bbcode/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src='<?php echo STATIC_BPONG?>/js/bbcode/xbb.js'></script>
<script type="text/javascript">
	XBB.textarea_id = 'ForumpostText'; // ������������� textarea
	XBB.area_width = '550px';
	XBB.area_height = '200px';
	XBB.state = 'plain'; // 'plain' or 'highlight'
	XBB.lang = 'en_utf8'; // �����������
</script>
<script type="text/javascript">
$(document).ready(function() {
	$("#Addtopic").validate({
		rules: {
			  "data[Forumtopic][name]":"required"
			, "data[Forumtopic][description]": "required"
			, "data[Captcha][text]": "required"	
			, "data[Forumpost][text]": "required"
		},
		messages: {
			  "data[Forumtopic][name]":  "Please enter topic title"
			, "data[Forumtopic][description]":  "Please enter your description"
			, "data[Forumpost][text]":  "Please enter your post"
		}
	});
	//EOF Validation
	
});
//EOF ready
</script>
<div class="forumtopics form"> <?php echo $this->Form->create('Forumtopic', array('url' => array('action'=> 'add', 'id'=> null, $slug), 'id' => 'Addtopic' ));?>
  <h2><?php echo 'Add topic';?></h2>
  <div class="rightbox">
    <div class="rightbox_top">&nbsp;</div>
    <?php echo $this->element('forumAdvert'); ?>
    <div class="rightbox_bottom">&nbsp;</div>
  </div>
  <fieldset class="forumn">
  <?php
		/**
		 * Forum/actiontype used in Models to increment counter of posts and topics only on add action (not for edit)
		 */
		echo $this->Form->input('Forum.actiontype',array('type' => 'hidden','value' => 'add'));
		
		echo $this->Form->input('Forumtopic.forumbranch_id',array( 'type' => 'hidden' ));
		echo $this->Form->input('Forumtopic.name', array('size' => '30px','label'=>'Topic Title'));
        ?>
  <div class="textarea">
    <?php  echo $this->Form->input('Forumtopic.description', array('type'=>'textarea', 'error' => false, 'div' => false));
           echo $this->Form->input('Forumpost.text', array('div' => false));
    ?>
  </div>
      <img src="/captcha/<?php echo rand(1, 10000);?>" alt="captcha" border="0">
    <br/>
    <?php echo $this->Form->input('Captcha.text', array('div' => false, 'label' => false, 'type' => 'text', 'value' => ''));?><br/>
    Please type the letters shown above
  
  </fieldset>
  <div class="input toleft"> <?php echo $this->Form->end(array('value' => 'Submit','class'=>'submit', 'div' => false));?> </div>
  <div class="clear"></div>
  <div class="actions">
    <ul>
      <!-- li><span class="backbtn"><?php echo $this->Html->link('List Forumtopics', array('action'=>'index', $this->request->data['Forumtopic']['forumbranch_id']), array('class'=>'backbtn'));?></span></li-->
      <li><span class="backbtn"><?php echo $this->Html->link('List Forums', array('controller'=> 'forumbranches', 'action'=>'index', $slug), array('class'=>'backbtn')); ?></span></li>
    </ul>
  </div>
</div>
<script type="text/javascript">
	XBB.init(); // �������������� ���������
</script>
