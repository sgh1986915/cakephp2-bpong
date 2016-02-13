<h3>Merge Two Teams</h3>
<?php 
    echo $this->Form->create('Team',array('url'=>'/teams/merge_two_teams/1'));
    echo $this->Form->input('teamID_to_delete',array('label'=>'ID of Team to Deleted','type'=>'text'));
    echo $this->Form->input('teamID_to_merge_into',array('label'=>'ID of Team to merge into','type'=>'text'));
    echo $this->Form->end('Submit');
?>