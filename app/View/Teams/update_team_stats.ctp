<h3>Update Team Stats</h3>
<?php 
    echo $this->Form->create('Team',array('url'=>'/teams/submit_update_team_stats'));
    echo $this->Form->input('team_id',array('label'=>'Team ID','type'=>'text'));
    echo $this->Form->end('Submit');
?>