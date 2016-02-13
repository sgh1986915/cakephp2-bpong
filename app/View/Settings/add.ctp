<div class="settings form">
<?php echo $this->Form->create('Setting');?>
	<fieldset>
 		<legend><?php echo 'Add Setting';?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('value');
        echo $this->Form->input(
                  'Setting/type'
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
		<li><?php echo $this->Html->link('List Settings', array('action'=>'index'));?></li>
	</ul>
</div>
