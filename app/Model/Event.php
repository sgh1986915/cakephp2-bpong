<?php
/* SVN FILE: $Id: event.php 5980 2011-06-27 14:38:17Z odikusar $ */
/*
 * @version $Revision: 5980 $
 * @modifiedby $LastChangedBy: odikusar $
 * @lastmodified $Date: 2011-06-27 17:38:17 +0300 (Пн, 27 июн 2011) $
 */
class Event extends AppModel
{

    var $name = 'Event';
    var $recursive = -1;
    var $actsAs = array(
        'Tag',
        'Image'=>array(
                        'thumbs'=>array('create'=>true,'width'=>'120','height'=>'120','bgcolor'=>'#FFFFFF'),
                        'versions'=>array(
                            'middle'=>array('width'=>'215','height'=>'215','bgcolor'=>'#FFFFFF')
                        )    
        ),
        'Containable',
        'Sluggable'=>array('separator' =>  '-',
                                       'label'         => 'name',
                                       'slug'          => 'slug',
                                       'length'       => 100,
                                       'overwrite'  =>  true,
                                       'unique'      => true
        )
    );

    var $validate = array(
        'name' => array('rule' => array('notEmpty')
                      ,'allowEmpty' => false
                      ,'message'    => 'Name can not be empty.'
        ),
        'start_date' => array(
                    'rule' => array('startEndDate'),
                    'message' => 'End date must be later then start date.'
        )
    );

    //The Associations below have been created with all possible keys, those that are not needed can be removed
    var $belongsTo = array(
        'Venue' => array('className' => 'Venue',
                                    'foreignKey' => 'venue_id',
                                    'conditions' => '',
                                    'fields' => '',
                                    'order' => ''
        ),
        'VenueView' => array('className' => 'VenueView',
                                    'foreignKey' => 'venue_id',
                                    'conditions' => '',
                                    'fields' => '',
                                    'order' => ''
        ),            
        'Eventstructure' => array('className'=>'Eventstructure',
                                      'foreignKey'=>'structure_id'
        ),
        'Timezone' => array('className' => 'Timezone',
                                    'foreignKey' => 'timezone_id',
                                    'dependent' => true,
                                    'conditions' => '',
                                    'fields' => '',
                                    'order' => ''
        ),
        'Personal' => array('className' => 'Image',
                                    'foreignKey' => 'official_image_id',
                                    'dependent' => true,
                                    'conditions' => '',
                                    'fields' => '',
                                    'order' => ''
        ),
        'Creator' => array('className' => 'User',
                                    'foreignKey' => 'user_id',
                                    'dependent' => true,
                                    'conditions' => '',
                                    'fields' => '',
                                    'order' => ''
        )
    );

    var $hasOne = array(
        'Owner' => array('className' => 'Manager',
                                    'foreignKey' => 'model_id',
                                    'dependent' => true,
                                    'conditions' => array('Owner.model' => 'Event', 'Owner.is_owner' => 1),
                                    'fields' => '',
                                    'order' => ''
        ),            
        'EventSatellite' => array('className' => 'EventsEvent',
                                    'foreignKey' => 'event_id',
                                    'dependent' => true,
                                    'fields' => '',
                                    'order' => ''
        )
    );
    
    var $hasAndBelongsToMany = array(
        'Tag' => array('className' => 'Tag',
                            'joinTable' => '',
                            'with'=>'ModelsTag',
                            'foreignKey' => 'model_id',
                            'associationForeignKey' => 'tag_id',
                            'unique' => true,
                            'conditions' => array(),
                            'order' => '',
                            'limit' => ''
        ),    
        'Eventfeature' => array('className' => 'Eventfeature',
                            'joinTable' => 'events_eventfeatures',
                            'foreignKey' => 'event_id',
                            'associationForeignKey' => 'eventfeature_id',
                            'unique' => true,
                            'conditions' => '',
                            'fields' => '',
                            'order' => '',
                            'limit' => '',
                            'offset' => '',
                            'finderQuery' => '',
                            'deleteQuery' => '',
                            'insertQuery' => ''
        ),
        'User' => array('className' => 'User',
                            'joinTable' => '',
                            'with' => "Manager",
                            'foreignKey' => 'model_id',
                            'associationForeignKey' => 'user_id',
                            'unique' => true,
                            'conditions' => array('Manager.model' => 'Event','User.is_deleted' => 0),
                            'fields' => '',
                            'order' => '',
                            'limit' => '',
                            'offset' => '',
                            'finderQuery' => '',
                            'deleteQuery' => '',
                            'insertQuery' => ''
        )
    );
    
    /**
     * Compare dates
     * @author vovich
     * @param unknown_type $data
     * @return unknown_type
     */
    function startEndDate($data) 
    {
        if (($this->data['Event']['start_date'] > $this->data['Event']['end_date'])
            && ($this->data['Event']['end_date_'] != '')) {
            return false;
        }
        return true;
    }

    /**
     * Get managers by eventId
     * @param unknown_type $eventId
     * @author vovich
     * @return unknown_type
     */
    function getManagersId($eventId = null) 
    {
        //From skinny....removed condition that is_confirmed=1. The manager has the right
        //to confirm it, so theres really no reason to exclude them from access
        $managers = $this->Manager->find(
            'all', array(
            'contain' => array()
            , 'conditions' => array('Manager.model' => 'Event', 'Manager.model_id' => $eventId)
            )
        );

        //managers id's for the checking acces
        $creatorID = $this->field('user_id', array('id' => $eventId));
        $event_managers = array();
        $event_managers[$creatorID] = $creatorID; 
        if (!empty($managers)) {
            foreach ($managers as $m) {
                $event_managers[$m['Manager']['user_id']] = $m['Manager']['user_id'];
            } 
        }
        return $event_managers;
    }    
    /**
     *
     * @param unknown_type $data
     * @author vovich
     * @return unknown_type
     */
    function storeEvent($data) 
    {
        //		if ($data['Event']['timezone_id'] && $data['Event']['start_date'] && $data['Event']['end_date']) {
        //			$eventTimezone = $this->Timezone->read('value', $data['Event']['timezone_id']);
        //
        //			$data['Event']['start_date'] = $this->convertDate($data['Event']['start_date'], $eventTimezone['Timezone']['value']);
        //			$data['Event']['end_date'] = $this->convertDate($data['Event']['end_date'], $eventTimezone['Timezone']['value']);
        //		}
        //
        if (empty($data['Event']['id'])) {
            $this->create();
        }
        if (empty($data['Event']['min_people_team'])) {
            $data['Event']['min_people_team'] = null; 
        }
        if (empty($data['Event']['max_people_team'])) {
            $data['Event']['max_people_team'] = null; 
        }
        if ($this->save($data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $date
     * @param int    $timezoneOffset
     *
     * @return string - converted time according server timezone
     * @author alekz
     */
    function convertDate($date, $timezoneOffset) 
    {
        $timezoneServer = new DateTimeZone(date_default_timezone_get());
        $timeServer = new DateTime('now', $timezoneServer);
        $serverOffset = $timezoneServer->getOffset($timeServer);
        $offset = $serverOffset - $timezoneOffset * 3600;

        $dateStamp = strtotime($date);
        $dateStamp += $offset;
        return date('Y-m-d H:i:s', $dateStamp);
    }
   
    function getMapMarkersOLD($endDate, $conditions = array(), $contain = array('Venue.Address.Provincestate')) 
    {
        $conditions['Event.is_deleted'] = 0;
        $conditions['Event.venue_id > '] = 0;
        $conditions[$this->name.'.end_date >='] = $endDate;
        $events = $this->find(
            'all', array(
            'conditions' => $conditions,
            'contain' => $contain,
            'order' => array('Event.id' => 'DESC'),
            )
        );

        return $events;
    }
        
    /**
     * check access to albums for this model id
     * @author Oleg D.
     */
    function getAlbumUploadAccess($userID, $modelID, $Access, $getAll) 
    {
        $creatorUserID = $this->field('user_id', array('id' => $modelID));
        
        if ($Access->getAccess('EventAlbums', 'c', $creatorUserID)) {
            
            return true;                        
        } elseif ($this->Manager->isManager($userID, 'Event', $modelID)) {
            
            return true;
        } else {
            
            return false;
        }
    }
    /**
     * Return conditions by the Events type
     * @authoe Oleg D.
     */
    function typesConditions($eventsType) 
    {
        
        if ($eventsType == 'tournament') {
            //$basicConditions['EventSatellite.event_id'] = null;
            $basicConditions['OR']['EventSatellite.event_id'] = null;
            $basicConditions['OR']['EventSatellite.relationship_type'] = 'sub_event';
        } elseif ($eventsType == 'all') {
            $basicConditions = array();    
        } else {    
            $basicConditions['EventSatellite.relationship_type'] = $eventsType;            
        }
        
        return $basicConditions;        
    }    
    
}
?>
