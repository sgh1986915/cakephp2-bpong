<?php

class EventsEventsController extends AppController
{

    var $name = 'EventsEvents';
    var $uses    = array('EventsEvent');
    
    /**
     * Add new relation
     * @author Oleg D.
     */
    function addRelation($eventID, $parentEventID, $relationType) 
    {
        $this->Access->getAccess('EventRelationship', 'c');                
        if ($eventID && $parentEventID && $relationType) {            
            if (!$this->EventsEvent->find('count', array('conditions' => array('event_id' => $eventID, 'parent_event_id' => $parentEventID, 'relationship_type' => $relationType)))) {
                $event = array('event_id' => $eventID, 'parent_event_id' => $parentEventID, 'relationship_type' => $relationType);    
                $this->EventsEvent->create();
                $this->EventsEvent->save($event);                    
            }            
        }
        
        exit;
    }

    function parentsList($id) 
    {
        $this->Access->getAccess('EventRelationship', 'l');        
        $this->EventsEvent->contain(array('Parent'));
        $events = $this->EventsEvent->find('all', array('conditions' => array('event_id' => $id)));    
        $this->set('events', $events);
    }
    function delete($id) 
    {    
        $this->Access->getAccess('EventRelationship', 'd');            
        $this->EventsEvent->delete($id);    
        exit();
    }
}
?>
