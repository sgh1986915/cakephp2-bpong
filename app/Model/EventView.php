<?php
/* SVN FILE: $Id: event.php 3489 2010-06-02 13:47:55Z vovich $ */
/*
 * @version $Revision: 3489 $
 * @modifiedby $LastChangedBy: vovich $
 * @lastmodified $Date: 2010-06-02 16:47:55 +0300 (Ср, 02 июн 2010) $
 */
class EventView extends AppModel {

	var $useTable = 'events_view';
	var $name = 'EventView';
	var $recursive = -1;
	var $actsAs = array(
		'Tag',
		'Image'=>array('thumbs'=>array('create'=>true,'width'=>'120','height'=>'120','bgcolor'=>'#FFFFFF')),
		'Containable',
		'Sluggable'=>array('separator' =>  '-',
									   'label'         => 'name',
                                       'slug'          => 'slug',
                                       'length'       => 100,
                                       'overwrite'  =>  true,
                                       'unique'      => true)
	);

	var $validate = array(
		'name' => array('rule' => array('notEmpty')
	                  ,'allowEmpty' => false
        	          ,'message'    => 'Name can not be empty.'),
        'start_date' => array(
        			'rule' => array('startEndDate'),
        			'message' => 'End date must be later then start date.'
    	)	          
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
		'Venue' => array('className' => 'VenueView',
							'foreignKey' => 'venue_id',
							'conditions' => '',
							'fields' => '',
							'order' => ''
		),
		/*'Creator' => array('className' => 'Creator',
							'foreignKey' => 'user_id',
							'conditions' => '',
							'fields' => '',
							'order' => ''
		),*/
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
		)
	);

 	var $hasOne = array(
		'Owner' => array('className' => 'Manager',
							'foreignKey' => 'model_id',
							'dependent' => true,
							'conditions' => array('Owner.model' => 'Event','Owner.is_owner' => 1),
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
					'conditions' => array('Tag.model' => 'Event'),
					'fields' => '',
					'order' => '',
					'limit' => '',
					'offset' => '',
					'finderQuery' => '',
					'deleteQuery' => '',
					'insertQuery' => ''
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
	function startEndDate($data){
	   if (($this->request->data['Event']['start_date'] > $this->request->data['Event']['end_date']) 
			     && ($this->request->data['Event']['end_date_'] != '')) {
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
	function getManagersId($eventId = NULL) {
		$managers = $this->Manager->find('all',array(
    			     'contain' => array()
    			    ,'conditions' => array('Manager.model' => 'Event','Manager.model_id'=>$eventId)
    			)
    	);
		 
    	//managers id's for the checking acces
    	$event_managers = array();
    	if (!empty($managers))
	    	foreach($managers as $m) {
				$event_managers[] = $m['Manager']['user_id'];
	    	}

	     return $event_managers;
	}
	/**
	 * 
	 * @param unknown_type $data
	 * @author vovich
	 * @return unknown_type
	 */
	function storeEvent ($data) { 
		
		if (empty($data['Event']['id'])) {
			$this->create();
		}
		
		if (empty($data['Event']['min_people_team']))
			$data['Event']['min_people_team'] = NULL;
		if (empty($data['Event']['max_people_team']))
			$data['Event']['max_people_team'] = NULL;
		
		if ($this->save($data)) {
			return true;
		} else {
			return false;
		}
		
	}
    /**
     * Get event markers 
     * 
     */
    function getMapMarkers($endDate, $defaultConditions = array(), $args = array()) {
		$conditions = $defaultConditions;
    	$conditions['EventView.is_deleted'] = 0;
		$conditions['EventView.venue_id > '] = 0;
		if ($endDate) {
			$conditions['EventView.end_date >='] = $endDate;
		}

		if (!empty($args['name'])) {
			$args['name'] = Sanitize::escape($args['name']);
			$conditions['EventView.name LIKE'] = '%' . $args['name'] . '%';
		}
		if (!empty($args['date'])) {
			$args['date'] = Sanitize::escape($args['date']);
			$conditions['DATE(EventView.start_date) <='] = $args['date'];
			$conditions['DATE(EventView.end_date) >='] = $args['date'];
		}
		if (!empty($args['state_id'])) {
			$args['state_id'] = Sanitize::escape($args['state_id']);
			$conditions['Venue.provincestate_id'] = $args['state_id'];
		}

		if (!empty($args['lgn'])) {
			$args['lgn'] = Sanitize::escape($args['lgn']);
			$users = $this->User->find('all', array(
				'conditions' => array(
					'lgn' => $args['lgn']
				),
				'recursive' => -1
			));
			$userIds = Set::classicExtract($users, '{n}.User.id');
			$conditions['EventView.user_id'] = $userIds;
		}
		
		//$contains = array('Venue', 'Creator', 'EventSatellite');
		$contains = array('Venue', 'EventSatellite');
		$markers = $this->find('all', array(
				'contain' => $contains,
	            'conditions' => $conditions,
				'order' => array('EventView.id' => 'DESC')
	    ));		
		
		return $markers;
    }
    	
	function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
	    $fields = array(); 
	    if (!empty($extra['extra']['count_fields'])) {
	    	$fields	= $extra['extra']['count_fields'];
	    }  
	    if (!empty($extra['extra']['count_contains'])) {
	    	$contains = $extra['extra']['count_contains'];
	    } else {
	    	$contains = array(); 
	    } 
	    
		$count = $this->find('count', array(
	         	'fields' => $fields,
				'contain' => $contains,
	            'conditions' => $conditions
	    ));
	    return $count;
	 }
	 
	 
	 
}
?>