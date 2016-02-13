<?php
class Workday extends AppModel
{

    var $name = 'Workday';
    var $validate = array(
    'name' => array('alphanumeric')
    );

    //The Associations below have been created with all possible keys, those that are not needed can be removed
    var $hasAndBelongsToMany = array(
    'Venue' => array('className' => 'Venue',
                        'joinTable' => 'venues_worktimes',
                        'foreignKey' => 'workday_id',
                        'associationForeignKey' => 'venue_id',
                        'unique' => true,
                        'conditions' => '',
                        'fields' => '',
                        'order' => '',
                        'limit' => '',
                        'offset' => '',
                        'finderQuery' => '',
                        'deleteQuery' => '',
                        'insertQuery' => ''
    )
    );

}
?>
