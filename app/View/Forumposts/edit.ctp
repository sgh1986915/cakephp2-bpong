<?php
	$this->pageTitle = 'Beer Pong Forums' . $this->Forumlinks->last_title( $this->request->params['pass'] ) . " | BPONG.COM";
?>	
<link href="<?php echo STATIC_BPONG?>/js/bbcode/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src='<?php echo STATIC_BPONG?>/js/bbcode/xbb.js'></script>
<script type="text/javascript">
	XBB.textarea_id = 'ForumpostText'; 
	XBB.area_width = '700px';
	XBB.area_height = '400px';
	XBB.state = 'plain'; // 'plain' or 'highlight'
	XBB.lang = 'en_utf8'; 
</script>
<script type="text/javascript">
$(document).ready(function() {
	$("#Addpost").validate({
		rules: {
			"data[Forumpost][text]": "required"
		},
		messages: {
			"data[Forumpost][text]":  "Please enter your description"
		}
	});
	//EOF Validation
	
});
//EOF ready
</script>
<div class="forumposts form">
<?php echo $this->Form->create('Forumpost', array('id' => 'Addpost', 'url' => array('action'=> 'edit', 'id'=> null, $slug)));?>
	<h2><?php echo 'Edit post';?></h2>
<div class="rightbox">
<div class="rightbox_top">&nbsp;</div>
<?php echo $this->element('forumAdvert'); ?>
<div class="rightbox_bottom">&nbsp;</div>
</div>
  <fieldset class="forumn">
    <div class="textarea">
        <?php
            echo $this->Form->input('Forum/actiontype',array('type' => 'hidden','value' => 'edit'));
            echo $this->Form->input('text', array('type'=>'textarea', 'div' => false));
        ?>
    </div>
	</fieldset>
    <div class="input toleft">
		<?php echo $this->Form->end(array('value' => 'Submit','class'=>'submit', 'div' => false));?>
    </div>
</div>

<div class="actions">
	<ul>
		<li><span class="backbtn"><?php echo $this->Html->link('Back to posts', array('action'=>'index', $back_slug ), array('class' => 'backbtn'));?></span></li>
		<li><span class="backbtn"><?php echo $this->Html->link('Back to topics', array('controller'=> 'forumbranches', 'action'=>'index', $back_back_slug ), array('class'=>'backbtn')); ?></span></li>
        	<?php if($Deleted): ?>
		<li><?php echo $this->Html->link('Delete', array('action'=>'delete', $slug), array('class'=>'delbtn'), sprintf('Are you sure you want to delete # %s?', $this->Form->value('Forumpost.id'))); ?></li>
	<?php endif; ?>	

	</ul>
</div>
<script type="text/javascript">
	XBB.init(); 
</script>
