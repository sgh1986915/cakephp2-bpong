<?php
class VenuesTeam extends AppModel
{
    var $name      = 'VenuesTeam';
    var $useTable  = 'venues_teams';
    //var $recursive = -1;
    var $actsAs = array('Containable');

    var $belongsTo = array(
      'Venue'=>array('className'=>'Venue',
          'foreignKey'=>'venue_id',
          ),
      'Team' => array(
          'className'    => 'Team',
          'foreignKey'    => 'team_id'
      )
    );

      
}
?>
