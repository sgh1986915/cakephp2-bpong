<h3>Merge the Stats of Two Users</h3>
This page allows you to move the stats of one user onto another. I.e., a user has created two accounts, and we
need to consolidate.
<?php 
    echo $this->Form->create('User',array('url'=>'/users/merge_two_users_stats/1'));
    echo $this->Form->input('user_to_move_from',array('label'=>'User to move from (id,email, or lgn)','type'=>'text'));
    echo $this->Form->input('user_to_move_to',array('label'=>'User to move to (id,email,or lgn)','type'=>'text'));
    echo $this->Form->end('Submit');
?>