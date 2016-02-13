<?php 
class Checkin extends AppModel
{
    var $name = 'Checkin';
    var $actsAs = array ('Containable');
    var $belongsTo = array (
    'Venue' => array('className' => 'Venue',
                'foreignKey' => 'venue_id',
                'conditions' => array('Venue.is_deleted <>'=>1)
    ),
    'User' => array('className'=>'User',
                'foreignKey'=>'user_id')
    );
}
?>
