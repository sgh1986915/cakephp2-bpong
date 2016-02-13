<?php

class OrganizationsObjectsController extends AppController
{

    var $name    = 'OrganizationsObjects';
    var $uses = array('OrganizationsObject', 'OrganizationsUser');
    
    /**
     * Show list of events of current organization
     * @author Oleg D.
     */
    function list_events($slug = null, $filter = null) 
    {
        if (empty($slug)) {
            $this->Session->setFlash('There is no Organization with such name.', 'flash_error');
            return $this->redirect('/');
        }

        $organization = $this->OrganizationsObject->Organization->find('first', array('conditions' => array('Organization.slug' => $slug, 'Organization.is_deleted' => 0)));
        $orgUser = $this->OrganizationsUser->getOrgUser($organization['Organization']['id']);
        $this->set('orgUser',  $orgUser);
        
        if ($this->Access->getAccess('OrganizationsObject', 'u', $this->OrganizationsUser->Organization->OrganizationsUser->getManagers($organization['Organization']['id']))) {
            $isManager = 1;
        } else {
            $isManager = 0;
        }
                        
        if (empty($organization)) {
            $this->Session->setFlash('There is no Organization with such name.', 'flash_error');
            return $this->redirect('/');
        }

        $conditions = array('OrganizationsObject.organization_id' => $organization['Organization']['id'], 'OrganizationsObject.model' => 'Event');
        
        /* filter Getting data from the session or from the form*/
        if ($filter) {
            if(!empty($this->request->data['OrgEventFilter'])) {
                $this->Session->write('OrgEventFilter', $this->request->data['OrgEventFilter']);
            } elseif ($this->Session->check('OrgEventFilter')) {
                $this->request->data['OrgEventFilter'] = $this->Session->read('OrgEventFilter');
            }        
            
            if (!empty ($this->request->data['OrgEventFilter'])) {
                foreach ($this->request->data['OrgEventFilter'] as $key => $val) {
                    $this->request->data['OrgEventFilter'][$key] = trim($val);
                }
                //Prepare data for the filter
                if (!empty( $this->request->data['OrgEventFilter']['name'])) {
                    $conditions['Event.name LIKE'] = '%' . $this->request->data['OrgEventFilter']['name'] . '%';
                }                                                
            }    
        } else {
            $this->Session->delete('OrgEventFilter');
        }        
        
        $this->paginate = array(
         'conditions' => $conditions, 
         'contain' => array('Event'), 
         'order' => array('Event.id' => 'desc')
        );

        $objects = $this->paginate('OrganizationsObject');
        
        
        $this->pageTitle = $organization['Organization']['name'] . ' :: ' . 'Events';        
        $this->set('organizationsMenu',  1);            
    
        $this->set(compact('organization', 'objects', 'slug', 'isManager'));
    }
    
    /**
     * Show list of venues of current organization
     * @author Oleg D.
     */
    function list_venues($slug = null, $filter = null) 
    {
        if (empty($slug)) {
            $this->Session->setFlash('There is no Organization with such name.', 'flash_error');
            return $this->redirect('/');
        }

        $organization = $this->OrganizationsObject->Organization->find('first', array('conditions' => array('Organization.slug' => $slug, 'Organization.is_deleted' => 0)));
        $orgUser = $this->OrganizationsUser->getOrgUser($organization['Organization']['id']);
        $this->set('orgUser',  $orgUser);
                
        if (empty($organization)) {
            $this->Session->setFlash('There is no Organization with such name.', 'flash_error');
            return $this->redirect('/');
        }
        if ($this->Access->getAccess('OrganizationsObject', 'u', $this->OrganizationsUser->Organization->OrganizationsUser->getManagers($organization['Organization']['id']))) {
            $isManager = 1;
        } else {
            $isManager = 0;
        }
        
        $conditions = array('OrganizationsObject.organization_id' => $organization['Organization']['id'], 'OrganizationsObject.model' => 'Venue');
        
        /* filter Getting data from the session or from the form*/
        if ($filter) {
            if(!empty($this->request->data['OrgVenueFilter'])) {
                $this->Session->write('OrgVenueFilter', $this->request->data['OrgVenueFilter']);
            } elseif ($this->Session->check('OrgVenueFilter')) {
                $this->request->data['OrgVenueFilter'] = $this->Session->read('OrgVenueFilter');
            }        
            
            if (!empty ($this->request->data['OrgVenueFilter'])) {
                foreach ($this->request->data['OrgVenueFilter'] as $key => $val) {
                    $this->request->data['OrgVenueFilter'][$key] = trim($val);
                }
                //Prepare data for the filter
                if (!empty( $this->request->data['OrgVenueFilter']['name'])) {
                    $conditions['Venue.name LIKE'] = '%' . $this->request->data['OrgVenueFilter']['name'] . '%';
                }                                                
            }    
        } else {
            $this->Session->delete('OrgVenueFilter');
        }        
        
        $this->paginate = array(
         'conditions' => $conditions, 
         'contain' => array('Venue' => array('conditions' => array('Venue.is_deleted <>' => '1')), 'Venue.Venuetype', 'Venue.Address', 'Venue.Address.Provincestate'), 
         'order' => array('Venue.id' => 'desc')
        );
        $this->OrganizationsObject->Venue->contain = array('Venuetype', 'Address');
        $objects = $this->paginate('OrganizationsObject');
        
        //pr($objects);exit;
        
        $this->pageTitle = $organization['Organization']['name'] . ' :: ' . 'Events';        
        $this->set('organizationsMenu',  1);            
    
        $this->set(compact('organization', 'objects', 'slug', 'isManager'));
    }
    /**
     * Add (Assign) Event
     * @author Oleg D.
     */
    function add_event($id = null, $eventID = null) 
    {
        if (empty($id)) {
            $this->Session->setFlash('There is no Organization with such name.', 'flash_error');
            return $this->redirect('/');
        }

        $organization = $this->OrganizationsUser->Organization->find('first', array('conditions' => array('Organization.id' => $id, 'Organization.is_deleted' => 0)));
        $this->Access->checkAccess('OrganizationsObject', 'u', $this->OrganizationsUser->Organization->OrganizationsUser->getManagers($organization['Organization']['id']));
        
        if (empty($organization)) {
            $this->Session->setFlash('There is no Organization with such name.', 'flash_error');
            return $this->redirect('/');
        }            
        
        if ($eventID) {
            if (!$this->OrganizationsObject->find('count', array('conditions' => array('organization_id' => $id, 'model' => 'Event', 'model_id' => $eventID)))) {
                $this->OrganizationsObject->create();
                $this->OrganizationsObject->save(array('organization_id' => $id, 'model' => 'Event', 'model_id' => $eventID, 'user_id' => $this->getUserID()));                    
            }
            $this->Session->setFlash('Event has been added', 'flash_success');
            return $this->redirect('/o_events/' . $organization['Organization']['slug']);                    
        }
        
        $orgUser = $this->OrganizationsUser->getOrgUser($organization['Organization']['id']);
        $this->set('orgUser',  $orgUser);

        $this->pageTitle = $organization['Organization']['name'] . ' :: ' . 'Add Event';        
        $this->set('organizationsMenu',  1);            
    
        $this->set(compact('organization', 'id'));
    }
    
    /**
     * Add (Assign) Venue
     * @author Oleg D.
     */
    function add_venue($id = null, $venueID = null) 
    {
        if (empty($id)) {
            $this->Session->setFlash('There is no Organization with such name.', 'flash_error');
            return $this->redirect('/');
        }

        $organization = $this->OrganizationsUser->Organization->find('first', array('conditions' => array('Organization.id' => $id, 'Organization.is_deleted' => 0)));
        $this->Access->checkAccess('OrganizationsObject', 'u', $this->OrganizationsUser->Organization->OrganizationsUser->getManagers($organization['Organization']['id']));        
        if (empty($organization)) {
            $this->Session->setFlash('There is no Organization with such name.', 'flash_error');
            return $this->redirect('/');
        }    
                
        if ($venueID) {
            if (!$this->OrganizationsObject->find('count', array('conditions' => array('organization_id' => $id, 'model' => 'Venue', 'model_id' => $venueID)))) {
                $this->OrganizationsObject->create();
                $this->OrganizationsObject->save(array('organization_id' => $id, 'model' => 'Venue', 'model_id' => $venueID, 'user_id' => $this->getUserID()));                    
            }
            $this->Session->setFlash('Venue has been added', 'flash_success');
            return $this->redirect('/o_venues/' . $organization['Organization']['slug']);                    
        }
        
        $orgUser = $this->OrganizationsUser->getOrgUser($organization['Organization']['id']);
        $this->set('orgUser',  $orgUser);

        $this->pageTitle = $organization['Organization']['name'] . ' :: ' . 'Add Event';        
        $this->set('organizationsMenu',  1);            
    
        $this->set(compact('organization', 'id'));
    }
    
    /**
     * Find Event to addd it to organization
     * @author Oleg D.
     */
    function find_event() 
    {
        Configure::write('debug', 0);
        
        $conditions = array();
        $name = trim($_POST['name']);
        $conditions['Event.name LIKE'] = '%' . $name . '%';
        $conditions['Event.is_deleted'] = 0;
        
        $objects = array();
        if (!empty($conditions)) {
            $objects = $this->OrganizationsObject->Event->find('all', array('conditions' => $conditions));
        }
        $this->set('objects', $objects);    
        $this->set('organizationID', intval($_POST['organization_id']));    
    }
    
    /**
     * Find Venue to addd it to organization
     * @author Oleg D.
     */
    function find_venue() 
    {
        Configure::write('debug', 0);
        $conditions = array();
        $conditions['Venue.is_deleted'] = 0;
        if (!empty($_POST['name'])) {
            $name = trim($_POST['name']);
            $conditions['Venue.name LIKE'] = '%' . $name . '%';
        }
        if (!empty($_POST['address'])) {
            $address = trim($_POST['address']);
            $conditions['OR']['Address.city LIKE'] = '%' . $address . '%';
            $conditions['OR']['Address.address LIKE'] = '%' . $address . '%';                    
        }
        $objects = array();
        if (!empty($conditions)) {
            $objects = $this->OrganizationsObject->Venue->find('all', array('conditions' => $conditions, 'contain' => array('Address' => array('Provincestate'))));
        }
        $this->set('objects', $objects);    
        $this->set('organizationID', intval($_POST['organization_id']));    
    }
    
    function remove($organizationID, $id) 
    {
        $organization = $this->OrganizationsUser->Organization->find('first', array('conditions' => array('Organization.id' => $organizationID, 'Organization.is_deleted' => 0)));
        $this->Access->checkAccess('OrganizationsObject', 'u', $this->OrganizationsUser->Organization->OrganizationsUser->getManagers($organization['Organization']['id']));        
        if (empty($organization)) {
            $this->Session->setFlash('There is no Organization with such name.', 'flash_error');
            return $this->redirect('/');
        }    
        $this->OrganizationsObject->delete($id);        
        $this->goBack();
    }
    
}
?>
