<h3>Update User Stats</h3>
<?php 
    echo $this->Form->create('User',array('url'=>'/users/submit_update_user_stats'));
    echo $this->Form->input('user_id',array('label'=>'Enter ID, Username, or Email','type'=>'text'));
    echo $this->Form->end('Submit');
?>