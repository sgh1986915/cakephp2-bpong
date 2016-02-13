<?php
class OrganizationsObject extends AppModel
{

    var $name = 'OrganizationsObject';
    var $recursive = -1;
    var $actsAs = array('Containable');

    var $belongsTo = array(
    'Organization' => array('className' => 'Organization',
                                  'foreignKey' => 'organization_id',
                                  'dependent' => false,
                                  'conditions' => array('Organization.is_deleted' => 0),
                                  'fields' => '',
                                  'type' => 'inner',  
                                  'order' => ''
    ),
    'User' => array('className' => 'User',
                                  'foreignKey' => 'user_id',
                                  'dependent' => false,
                                  'conditions' => array(),
                                  'fields' => '',
                                  'order' => ''
    ),
    'Event' => array('className' => 'Event',
                  'foreignKey' => 'model_id',
                  'dependent' => false,
                  'conditions' => array('OrganizationsObject.model' => 'Event', 'Event.is_deleted <>' => 1),
                  'fields' => '',
                  'order' => '',
                  'type' => 'inner'
              ),
    'Venue' => array('className' => 'Venue',
                  'foreignKey' => 'model_id',
                  'dependent' => false,
                  'conditions' => array('OrganizationsObject.model' => 'Venue', 'Venue.is_deleted <>' => 1),
                  'fields' => '',
                  'order' => '',
                    'type' => 'inner'
              ),
    'Team' => array('className' => 'Team',
                  'foreignKey' => 'model_id',
                  'dependent' => false,
                  'conditions' => array('OrganizationsObject.model' => 'Team'),
                  'fields' => '',
                  'order' => '',
                    'type' => 'inner'
              ),                                              
    );
    
}
?>
