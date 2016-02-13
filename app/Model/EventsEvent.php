<?php

class EventsEvent extends AppModel
{
    
    var $useTable = 'events_events';
    var $name = 'EventEvent';
    var $recursive = -1;
    var $actsAs = array(
    'Containable'
    );
    
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    var $belongsTo = array(
    'Event' => array('className' => 'Event',
                                'foreignKey' => 'event_id',
                                'conditions' => '',
                                'fields' => '',
                                'order' => ''
    ),
    'Parent' => array('className' => 'Event',
                                'foreignKey' => 'parent_event_id',
                                'dependent' => true,
                                'conditions' => '',
                                'fields' => '',
                                'order' => ''
    )
    );    
}
?>
