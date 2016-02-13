<?php echo $this->element('mce_init', array('name' => 'ContentContent')); ?>

<form name="EditContent" method="post">

<div class="contents form">
Content
	<fieldset>
 		<legend>Edit Content</legend>
	<?php
		echo $this->Form->input('Content.id',array('type'=>'hidden'));
		echo $this->Form->input('Content.language_id',array('type' => 'select','label'=>'','Language' => $languages));
		echo $this->Form->input('Content.title');
		echo $this->Form->input(
              'Content.content'
             ,array(
                 'label' => 'Content: '
                ,'type'  => 'textarea'
        	    ,'div'   => array('class' => 'input')
        	    ,'cols'  => '40'
              )//array
           )//input
	?>
	</fieldset>
</div>
<?php echo $this->Form->end('Submit');?>