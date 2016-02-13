<script type="text/javascript">
$(document).ready(function() {
	$("#Addtopic").validate({
		rules: {
			  "data[Forumtopic][name]":"required"
			, "data[Forumtopic][description]": "required"
		},
		messages: {
			  "data[Forumtopic][name]":  "Please enter topic title"
			, "data[Forumtopic][description]":  "Please enter your description"
		}
	});
	//EOF Validation
	
});
//EOF ready
</script>

<div class="forumtopics form"> <?php echo $this->Form->create('Forumtopic',array( 'id' => 'Addtopic', 'url' => array ('id' => null, 'action' => 'edit', $slug)));?>
  <h2><?php echo 'Edit topic';?></h2>
  <div class="rightbox">
    <div class="rightbox_top">&nbsp;</div>
    <?php echo $this->element('forumAdvert'); ?>
    <div class="rightbox_bottom">&nbsp;</div>
  </div>
  <fieldset class="forumn">
  <?php
            echo $this->Form->input('Forum/actiontype',array( 'type' => 'hidden', 'value' => 'edit' ));
            echo $this->Form->input('name', array('size' => 60));?>
  <div class="textarea">
    <?php
            echo $this->Form->input('description', array('type'=>'textarea', 'div' => false));
        ?>
  </div>
  <div class="input toleft"> <?php echo $this->Form->end(array('value' => 'Submit','class'=>'submit', 'div' => false));?> </div>
  </fieldset>
</div>
<div class="actions">
  <ul>
    <li><span class="backbtn"><?php echo $this->Html->link('Back to topics', array('controller' => 'forumbranches','action'=>'index', $back_slug), array('class'=>'backbtn'));?></span></li>
    <?php if($Deleted): ?>
    <li><?php echo $this->Html->link('Delete topic', array( 'action'=>'delete', $slug ), array('class'=>'delbtn_'), sprintf('Are you sure you want to delete # %s?', $this->Form->value('Forumtopic.id'))); ?></li>
    <?php endif; ?>
  </ul>
</div>
