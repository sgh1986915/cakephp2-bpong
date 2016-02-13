<?php
class Eventfeature extends AppModel
{

    var $name = 'Eventfeature';
    var $recursive = -1;
    var $validate = array
    (

         'name'      => array(
                            'rule'       => array('notEmpty')
                             ,'allowEmpty' => false
                             ,'message'    => 'Name can not be empty.'
                           )
    );


    //The Associations below have been created with all possible keys, those that are not needed can be removed
    var $hasAndBelongsToMany = array(
    'Event' => array('className' => 'Event',
                        'joinTable' => 'events_eventfeatures',
                        'foreignKey' => 'eventfeature_id',
                        'associationForeignKey' => 'event_id',
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
