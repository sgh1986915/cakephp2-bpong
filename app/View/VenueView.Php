<?php
class VenueView extends AppModel {
    var $name = 'VenueView';
    var $useTable = 'venues_view';
    var $recursive = -1;
    var $actsAs       = array('Containable');
       
	var $hasMany = array(          
            'Nbplday'=>array('className' => 'Nbplday',
                                'foreignKey'=>'venue_id')			
	);   
    /**
     * Get map markers 
     * 
     */
    function getMapMarkers($defaultConditions = array('VenueView.is_deleted' => 0, 'VenueView.nbpltype <> ' => 'None')) {
		$conditions = $defaultConditions;

		$markers = $this->find('all', array(
				'contain' => array('Nbplday'),
	            'conditions' => $conditions,
				'order' => array('VenueView.id' => 'DESC')
	    ));		
		
		return $markers;
    }   
}
?>