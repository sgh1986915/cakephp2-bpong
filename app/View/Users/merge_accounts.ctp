<h3>Merge Two Accounts</h3>
<?php 
    echo $this->Form->create('User',array('url'=>'/users/merge_accounts'));
    echo $this->Form->input('user_to_move_from',array('label'=>'User to move from (id,email, or lgn)','type'=>'text'));
    echo $this->Form->input('user_to_move_to',array('label'=>'User to move to (id,email,or lgn)','type'=>'text'));
    echo $this->Form->end('Submit');
?>