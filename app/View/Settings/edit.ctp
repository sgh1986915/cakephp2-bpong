<div class="settings form">
<?php echo $this->Form->create('Setting');?>
	<fieldset>
 		<legend><?php echo 'Edit Setting';?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('value');
        echo $this->Form->input(
                  'type'
    			 ,array(
    			     'label'    => 'Type: '
    				,'div'      => array('class' => 'input')
    				,'type'     => 'select'
    			    ,'options'  => array(
    			                      'STRING' => 'STRING'
                                     ,'INT'    => 'INT'
                                     ,'BOOL'   => 'BOOL'
                                     ,'FLOAT'  => 'FLOAT'
                                   )//array
                    ,'selected' => ""
                    ,'attributes' => null
                    ,'showEmpty' => false
                  )//array
             );//input
		echo $this->Form->input('description');
	?>
	</fieldset>
<?php echo $this->Form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link('Delete', array('action'=>'delete', $this->Form->value('Setting.id')), null, sprintf('Are you sure you want to delete # %s?', $this->Form->value('Setting.id'))); ?></li>
		<li><?php echo $this->Html->link('List Settings', array('action'=>'index'));?></li>
	</ul>
</div>
