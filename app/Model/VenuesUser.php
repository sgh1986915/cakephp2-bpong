<?php
class VenuesUser extends AppModel
{
    var $name      = 'VenuesUser';
    var $useTable  = 'venues_users';
    //var $recursive = -1;
    var $actsAs = array('Containable');

    var $belongsTo = array(
      'Venue'=>array('className'=>'Venue',
          'foreignKey'=>'venue_id',
          ),
      'User' => array(
          'className'    => 'User',
          'foreignKey'    => 'user_id'
      )
    );

      
}
?>
