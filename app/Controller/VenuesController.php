<?php
class VenuesController extends AppController
{

    var $name = 'Venues';
    var $helpers = array ( 'Html', 'Form', 'Formenum','Javascript' , 'Address');
    var $uses = array( 'Provincestate', 'Country', 'Venue', 'Manager','Event', 'Image', 'Checkin','Timezone', 'VenuesUser', 'VenuesTeam'); // Add Checkin
    var $components = array('Time');

    var $phonetype = array ( "Cell" => "Cell"
                ,"Home" => "Home"
                ,"Other" => "Other"
                ,"Work" => "Work"
                );
    
    function nbpls() 
    {
        $this->redirect('/venues/index/nbpls');
    }
    function index($type=null) 
    {
        $this->Access->checkAccess('venue', 'l');
        $conditions = array("Venue.is_deleted <> 1");
        if ($type == 'nbpls') {
            $conditions[0] .= ' AND Venue.nbpltype <> "None"';
        }
          /* filter Getting data from the session or from the form*/
        if(!empty($this->request->data['VenueFilter'])) {
            $this->Session->write('VenueFilter', $this->request->data['VenueFilter']);
        }elseif($this->Session->check('VenueFilter')) {
            $this->request->data['VenueFilter']=$this->Session->read('VenueFilter');
        }
    
        //Prepare data for the filter
        if (!empty( $this->request->data['VenueFilter']['name'])) {
            $conditions[0] = $conditions[0] . ' AND Venue.name LIKE "%' . $this->request->data['VenueFilter']['name'] . '%"'; 
        }
    
        if (!empty( $this->request->data['VenueFilter']['state_id'])) {
            $conditions[0] = $conditions[0] . ' AND Address.provincestate_id = ' . $this->request->data['VenueFilter']['state_id'];
        }
        if (!empty ($this->request->data['VenueFilter']['only_nbpl'])) {
            if ($this->request->data['VenueFilter']['only_nbpl'] == 1) {
                $conditions[0] = $conditions[0] . ' AND Venue.nbpltype <> "None"'; 
            }
        }
        //EOF user conditions
        $this->Venue->recursive = 2;
         //  return $this->returnJSONResult($conditions);
        $this->paginate = array(
        'Venue' => array(
            'conditions' => $conditions,  
         'order' => array('Venue.id' => 'DESC')      
        ));
        
        $venues = $this->paginate('Venue');
        $states = $this->Provincestate->find('list', array('fields' => array('id', 'name'), 'conditions' => array('AND' => array('country_id' => 1), 'NOT' => array('shortname' => array('AA', 'AE', 'AP'))), 'order' => array('name' => 'ASC')));

        $this->set('states', $states);           
        
        
        $this->set('venues', $venues);
        
    }
    
    function my() 
    {
        //$this->Access->checkAccess('venue', 'l');
        $userID = $this->Access->getLoggedUserID();    
        $venuesIDs = $this->Manager->getModelsIDs($userID, 'Venue');

        $paginate = array();
        $paginate['conditions'][0] = 'Venue.id IN (' . implode(',', $venuesIDs) . ') AND Venue.is_deleted = 0';
        $paginate['contain']      = array('Venuetype', 'Address');
        $paginate['order'] = array('Venue.id' => 'desc');
        $this->paginate = array('Venue' => $paginate);
        $venues = $this->paginate('Venue');
       
        $this->set('venues', $venues);
    }
    
    function view($slug = null) 
    {
        if (empty( $slug ) ) {
            $this->Session->setFlash('Invalid Venue slug.', 'flash_error');
            $this->redirect(array('action' => 'index'));
        }
        
        $id = $this->Venue->findIdBySlug($slug);
        
        if ($id === false) {
            $this->Session->setFlash('Invalid Venue ID.', 'flash_error');
            $this->redirect(array ('action' => 'index' ));
        }
        
        $this->Manager->recursive = 0;
        $managers = $this->Manager->find(
            'all', array(
                     'contain' => array('User')
                    ,'conditions' => array( 'Manager.model' => 'Venue', 'Manager.model_id' => $id )
                )
        );

        //managers id's for the checking acces
        $venue_managers = array();
        if (!empty($managers)) {
            foreach($managers as $m){
                $venue_managers[$m['Manager']['user_id']] = $m['Manager']['user_id'];
            }
        }             
        $canEditVenue = $this->Access->getAccess('venue', 'u', $venue_managers);

        $this->Access->checkAccess('venue', 'r');

        $this->set('Updated', $canEditVenue);
        $this->set('List', $this->Access->getAccess('venue', 'l'));
        $this->set('Created', $this->Access->getAccess('venue', 'c'));
        $this->set('Deleted', $this->Access->getAccess('venue', 'd'));
        
        if (!$id) {
            $this->Session->setFlash('Invalid Venue.', 'flash_error');
            $this->redirect(array ('action' => 'index' ));
        }

        $images = $this->Image->myImages('Venue', $id, 'Personal');
        $this->set('offimage', $images);
    
        $upcomingEvents = $this->Event->find('all', array('conditions' => array('Event.venue_id' => $id, 'Event.is_deleted' => 0, 'Event.start_date >= CURDATE()'), 'order' => array('Event.start_date' => 'asc'), 'limit' => 5));
        $recentEvents = $this->Event->find('all', array('conditions' => array('Event.venue_id' => $id, 'Event.is_deleted' => 0, 'Event.start_date < CURDATE()'), 'order' => array('Event.start_date' => 'desc'), 'limit' => 5));

        $this->Venue->recursive = 2;
        $venue = $this->Venue->read(null, $id);
        
        
        $venueUsers = $this->VenuesUser->find('all', array('conditions' => array('venue_id' => $id), 'contain' => array('User'), 'limit' => 10, 'order' => array('nbplpoints_ytd' => 'desc', 'wins_ytd' => 'desc', 'losses_ytd' => 'asc', 'cupdif_ytd' => 'desc')));
        $venueTeams = $this->VenuesTeam->find('all', array('conditions' => array('venue_id' => $id), 'contain' => array('Team'), 'limit' => 10, 'order' => array('nbplpoints_ytd' => 'desc', 'wins_ytd' => 'desc', 'losses_ytd' => 'asc', 'cupdif_ytd' => 'desc')));

        $this->pageTitle = 'Venue > ' . $venue['Venue']['name'];
        $this->set(compact('venue', 'recentEvents', 'upcomingEvents', 'venueUsers', 'venueTeams'));
        
    }
    /**
    * API Function to add a new venue
    * 
    * @param  mixed $newVenue
    * @return new Venue
    */
    function addVenue_api($newVenue) 
    {
        Configure::write('debug', 0);
        if (!$this->Access->getAccess('venue', 'c')) {
            return "You are not logged in."; 
        }
        $venueToSave['Venue']['name'] =  trim(htmlentities(strip_tags($newVenue['name'])));
        $venueToSave['Venue']['description'] = $newVenue['description'];
        // need to check web_address
        
        $venueToSave['Venue']['venuetype_id'] = $newVenue['venuetype_id'];
        $venueToSave['Venue']['web_address'] = $newVenue['web_address'];
        $this->Venue->create();
        if (!$this->Venue->save($venueToSave)) {
            return 'problem'; 
        }
        Cache::read('markers', 'markers');
        $lastID = $this->Venue->getLastInsertID();
        $lastVenueSlug = $this->Venue->read(array("slug"), $lastID);       
        
        $addressToSave['Address']['address'] = $newVenue['address1'];
        $addressToSave['Address']['address2'] = $newVenue['address2'];
        $addressToSave['Address']['city'] = $newVenue['city'];
        $addressToSave['Address']['country_id'] = $newVenue['country_id'];
        //Assume that the client knows the state id
        if ($newVenue['provincestate_id'] > 0) {      
            $addressToSave['Address']['provincestate_id'] = $newVenue['provincestate_id'];
            $this->Provincestate->recursive = -1;
            $provinceState = $this->Provincestate->find('first', array('conditions'=>array('id'=>$newVenue['provincestate_id'])));  
        }
        else {
            $addressToSave['Address']['provincestate_id'] = 0; 
        }
        
        $this->Country->recursive = -1;
        $country = $this->Country->find('first', array('conditions'=>array('id'=>$newVenue['country_id'])));
        //function getLatLon($address = null, $city=null, $state=null, $country = null) {     
        // For right now, assume US
        $latLon = $this->Venue->Address->getLatLon(
            $addressToSave['Address']['address'], $addressToSave['Address']['city'] = $newVenue['city'],
            (isset($provinceState) ? $provinceState['Provincestate']['name'] : ""), $country['Country']['name']
        );
        $addressToSave['Address']['latitude'] = $latLon['lat'];
        $addressToSave['Address']['longitude'] = $latLon['lon'];    
        $addressToSave['Address'] ['model'] = 'Venue';
        $addressToSave['Address'] ['model_id'] = $lastID;
        $this->Venue->Address->save($addressToSave);
        Cache::read('markers', 'markers');
        //Save Manager info
        $managerToSave['Manager']['user_id']        =    $this->Session->read('loggedUser.id');
        $managerToSave['Manager']['model']            =    "Venue";
        $managerToSave['Manager']['model_id']      =    $lastID;
        $managerToSave['Manager']['is_owner']        =    1;
        $managerToSave['Manager']['is_confirmed']    =    0;
        $this->Manager->save($managerToSave);

        // Save Phone info
        if (!empty($newVenue['phone'])) {
            $phone['Phone'] ['phone'] = trim(htmlentities(strip_tags($newVenue['phone'])));  
            $phone['Phone']['model']        = "Venue";
            $phone['Phone']['model_id']    = $lastID;
            //$phone['Phone']['type']        = "Primary Phone";
            $this->Venue->Phone->create();
            $this->Venue->Phone->save($phone);
        }
        $this->Venue->recursive = 0;
        $returnVenue = $this->Venue->find(
            'first', array(
            'contain' =>  array('Address', 'Venuetype','Phone') ,
            'conditions'=>array('Venue.id'=>$lastID))
        );
        if (!$returnVenue) { return "problem"; 
        }
        return $returnVenue;
    }
    function test_api($lastID) 
    {
        $this->Venue->recursive = 0;
        $returnVenue = $this->Venue->find(
            'first', array(
            'contain' =>  array('Address', 'Venuetype','Phone') ,
            'conditions'=>array('Venue.id'=>$lastID))
        );
        return $returnVenue;
    }
    function add($model = 0, $modelID = 0) 
    {
        $this->Access->checkAccess('venue', 'c');
        $this->set('List', $this->Access->getAccess('venue', 'l'));
        //Get all shortnames of states for javascript (used in GoogleMap change location)
        $this->Provincestate->recursive = -1;
        $states = $this->Provincestate->find('all', array('order'=>'Provincestate.id ASC'));
        $all_states = "{";
        for ($i=0;$i < count($states);$i++){
            $all_states .= $states[$i]['Provincestate']['id'].":'".$states[$i]['Provincestate']['shortname']."'";
            if ($i != count($states) -1 ) {
                $all_states .= ",";
            }
        }
        $all_states .= "}";
        $this->set("all_states", $all_states);
        unset( $all_states, $states );

        if (! empty ( $this->request->data )) {
            //Convert d/m/Y format to Y-m-d
            /*			if (isset($this->request->data['Worktime']['Worktime'][8]['special_date'])) {
            $this->request->data['Worktime']['Worktime'][8]['special_date'] = $this->Time->calendarToSql($this->request->data['Worktime']['Worktime'][8]['special_date']);
            }

            // Delete unnesessary fields of worktime table
            if (! empty ( $this->request->data ['Worktime'] ['Worktime'] )) {
            foreach ( $this->request->data ['Worktime'] ['Worktime'] as $index => $value ) {
            if (! is_array ( $value )) {
             unset ( $this->request->data ['Worktime'] ['Worktime'] [$index] );
            }
            }
            }    */

            //Validate Venue fields
            $this->Venue->set($this->request->data);
            $this->Venue->validates();
            $errvenue = $this->Venue->invalidFields();
            //Validate Address fields
            if (empty ( $this->request->data ['Address'] ['address'] ) ) {
                $this->Venue->Address->invalidate('address', 'Please write an address.');
                $errvenue = 1;
            }
            if (empty ( $this->request->data ['Address'] ['city'] ) ) {
                $this->Venue->Address->invalidate('city', 'Please write a city.');
                $errvenue = 1;
            }
            if (empty ( $errvenue ) ) {
                //strip tags
                $this->request->data ['Venue'] ['name'] = trim(htmlentities(strip_tags($this->request->data ['Venue'] ['name'])));
                //$this->request->data ['Venue'] ['description'] = trim( htmlentities(strip_tags($this->request->data ['Venue'] ['description'])) );
                $this->request->data ['Phone'] ['phone'] = trim(htmlentities(strip_tags($this->request->data ['Phone'] ['phone'])));

                //Save Venue info
                $this->Venue->create();
                $this->Venue->save($this->request->data);
                Cache::read('markers', 'markers');
                $lastID = $this->Venue->getLastInsertID();
                    
                // Save NBPL days
                if (!empty($this->request->data['Nbpldays'])) {
                    if (!empty($this->request->data['Nbpldays']['new'])) {
                        $newDays = $this->request->data['Nbpldays']['new'];
                        unset($this->request->data['Nbpldays']['new']);
                        foreach ($newDays as $nbplDay) {
                            if (!empty($nbplDay['nbplday'])) {
                                $nbplDay['venue_id'] = $lastID;
                                $this->Venue->Nbplday->create();
                                $this->Venue->Nbplday->save($nbplDay);
                            }
                        }
                    }
                    Cache::delete('markers');
                }
                // EOF save NBPL days
                    
                    
                    
                    $lastVenueSlug = $this->Venue->read(array("slug"), $lastID);
                    
                    
                if (!empty($model) && !empty($modelID)) {
                    if ($model == 'Organization') {
                        $OrganizationsObject = ClassRegistry::init('OrganizationsObject');
                        $OrganizationsObject->create();
                        $OrganizationsObject->save(array('model' => 'Venue', 'model_id' => $lastID, 'organization_id' => $modelID, 'user_id' => $this->getUserID()));    
                    }        
                }
                                        
                //Save Address info
                if (empty($this->request->data ['Address'] ['latitude'])) { unset($this->request->data ['Address'] ['latitude']); 
                }
                if (empty($this->request->data ['Address'] ['longitude'])) { unset($this->request->data ['Address'] ['longitude']); 
                }
                $this->request->data ['Address'] ['model'] = "Venue";
                $this->request->data ['Address'] ['model_id'] = $lastID;
                $this->Venue->Address->save($this->request->data);

                //Save Manager info
                $this->request->data['Manager']['user_id']        =    $this->Session->read('loggedUser.id');
                $this->request->data['Manager']['model']            =    "Venue";
                $this->request->data['Manager']['model_id']      =    $lastID;
                if (empty($this->request->data['Manager']['is_owner'])) {
                    $this->request->data['Manager']['is_owner']        =    0;
                } else {
                    $this->request->data['Manager']['is_owner']        =    1;
                }
                $this->request->data['Manager']['is_confirmed']    =    0;
                $this->Manager->save($this->request->data['Manager']);

                // Save Phone info
                if (!empty($this->request->data['Phone']['phone']) ) {
                            $phone['Phone']                = $this->request->data['Phone'];
                            $phone['Phone']['model']        = "Venue";
                            $phone['Phone']['model_id']    = $lastID;
                            //$phone['Phone']['type']		= "Primary Phone";
                            $this->Venue->Phone->create();
                            $this->Venue->Phone->save($phone);
                            unset( $phone );
                }

                //Save image field in Venue table, if it was uploaded
                $imagesave = $this->Venue->Image->find(
                    'first', array('conditions' => array(  'model' => 'Venue'
                                                                                                , 'model_id' => $lastID )) 
                );
                if (! empty ( $imagesave )) {
                    $this->Venue->saveField('official_image_id', $imagesave['Image']['id']);
                }
                unset( $imagesave );

                $this->Session->setFlash('The Venue has been saved', 'flash_success');
                
                $this->redirect(array("action" => "view", $lastVenueSlug["Venue"]["slug"]));
            } else {
                $this->Session->setFlash('The Venue could not be saved. Please, try again.', 'flash_error');
            }
        }
        $this->request->data['Venue']['web_address'] = "http://";
        //$venueactivities = $this->Venue->Venueactivity->find ( 'list' );
        //$venuefeatures = $this->Venue->Venuefeature->find ( 'list' );
        //$worktimes = $this->Venue->Worktime->find ( 'list' );
        $venuetypes = $this->Venue->Venuetype->find('list');
        $this->set('phonetype', $this->phonetype);

        $countries_states = $this->Venue->Address->setCountryStates();
        $timeZones = $this->Timezone->find('list');

        $this->set('countries', $countries_states['countries']);
        $this->set('provincestates', $countries_states['states']);
        $this->set(compact('venuetypes', 'timeZones'));
        $this->set('model', $model);
        $this->set('modelID', $modelID);                            
        $this->set('accessApprove', $this->Access->getAccess('ApproveVenue', 'c'));
    }

    function edit($id = null) 
    {

        if (! $id && empty ( $this->request->data )) {
            $this->Session->setFlash('Invalid Venue', 'flash_error');
            $this->redirect(array ('action' => 'index' ));
        }
        $this->Manager->recursive = 0;
        $managers = $this->Manager->find(
            'all', array(
                     'contain' => array('User')
                    ,'conditions' => array( 'Manager.model' => 'Venue', 'Manager.model_id' => $id )
                )
        );
        //echo $this->getUserID();
        //managers id's for the checking acces
        $venue_managers = array();
        if (!empty($managers)) {
            foreach($managers as $m){
                $venue_managers[$m['Manager']['user_id']] = $m['Manager']['user_id'];
            } 
        }

        $this->Access->checkAccess('venue', 'u', $venue_managers);
        $this->set('List', $this->Access->getAccess('venue', 'l'));
        
        if ($this->isLoggined()) {
            $userSession = $this->Session->read('loggedUser');
            $userID = $userSession['id'];
        } else {
            $userID = VISITOR_USER;
        }

        if (! empty ( $this->request->data )) {
                        
            // Save NBPL days
            if (!empty($this->request->data['Nbpldays'])) {
                if (!empty($this->request->data['Nbpldays']['new'])) {
                    $newDays = $this->request->data['Nbpldays']['new'];
                    unset($this->request->data['Nbpldays']['new']);
                    foreach ($newDays as $nbplDay) {
                        if (!empty($nbplDay['nbplday'])) {
                            $nbplDay['venue_id'] = $id;
                            $this->Venue->Nbplday->create();
                            $this->Venue->Nbplday->save($nbplDay);
                        }
                    }
                }
                foreach ($this->request->data['Nbpldays'] as $nbplDay) {
                    $this->Venue->Nbplday->save($nbplDay);
                }
                Cache::delete('markers');
            }
            // EOF save NBPL days
            
            $venueBeforeSave = $this->Venue->find('first', array( 'conditions' => array( 'Venue.is_deleted <> 1', 'Venue.id' => $id )));

            /*if (isset($this->request->data['Worktime']['Worktime'][8]['special_date'])) {
            $this->request->data['Worktime']['Worktime'][8]['special_date'] = $this->Time->calendarToSql($this->request->data['Worktime']['Worktime'][8]['special_date']);
            }

            // Delete unnesessary fields of worktime table
            if (! empty ( $this->request->data ['Worktime'] ['Worktime'] )) {
            foreach ( $this->request->data ['Worktime'] ['Worktime'] as $index => $value ) {
            if (! is_array ( $value )) {
             unset ( $this->request->data ['Worktime'] ['Worktime'] [$index] );
            }
            }
            } */
            //Validate Venue fields
            $this->Venue->set($this->request->data);
            $this->Venue->validates();
            $errvenue = $this->Venue->invalidFields();

            /*unset($this->request->data ['Venueactivity']['id']);
				
                if (empty ( $this->request->data ['Venueactivity'] )) {
            $this->Venue->invalidate ( 'Venueactivity' );
            $errvenue = "Venueactivity";
            }
            unset($this->request->data ['Venuefeature']['id']);
            if (empty ( $this->request->data ['Venuefeature'] )) {
            $this->Venue->invalidate ( 'Venuefeature' );
            $errvenue = "Venuefeature";
            }
            unset($this->request->data ['Worktime']['id']);
            if (empty ( $this->request->data ['Worktime'] )) {
            $this->Venue->invalidate ( 'Worktime' );
            $errvenue = "Worktime";
            }
            */
            //Edit Address info
            $address_result = $this->Venue->Address->find('first', array ('conditions' => array("model" => "Venue", "model_id" => $id)));
            if (!empty($address_result)) {
                $this->Venue->Address->id = $address_result['Address']['id'];
                if (empty($this->request->data ['Address'] ['latitude'])) { unset($this->request->data ['Address'] ['latitude']); 
                }
                if (empty($this->request->data ['Address'] ['longitude'])) { unset($this->request->data ['Address'] ['longitude']); 
                }
                $this->Venue->Address->save($this->request->data);
            }

            if (empty ( $errvenue ) ) {
                // strip tags
                $this->request->data ['Venue'] ['name'] = trim(htmlentities(strip_tags($this->request->data ['Venue'] ['name'])));
                //$this->request->data ['Venue'] ['description'] = trim( htmlentities(strip_tags($this->request->data ['Venue'] ['description'])) );
                if (isset($this->request->data ['Phone'] ['phone'])) { $this->request->data ['Phone'] ['phone'] = trim(htmlentities(strip_tags($this->request->data ['Phone'] ['phone']))); 
                }
                //Save Venue
                $this->Venue->id = $id;
                $saved_venue = $this->Venue->save($this->request->data);
                
                
                
                
                
                if ($this->request->data['Venue']['nbpltype'] != $venueBeforeSave['Venue']['nbpltype']) {
                    Cache::delete('markers');
                }
                //Cache::read('markers', 'markers');
                //Save image
                $imagesave = $this->Venue->Image->find('first', array( 'conditions' => array(  'model' => 'Venue', 'model_id' => $id )));
                if (! empty ( $imagesave )) {
                    $this->Venue->saveField('official_image_id', $imagesave['Image']['id']);
                }

                $this->Session->setFlash('The Venue has been saved', 'flash_success');
                $this->redirect(array ('action' => 'view',  $saved_venue['Venue']['slug']));
            } else {
                $this->Session->setFlash('The Venue could not be saved. Please, try again.', 'flash_error');
            }
        }
        if (empty ( $this->request->data )) {
            $this->request->data = $this->Venue->find('first', array( 'conditions' => array( 'Venue.is_deleted <> 1', 'Venue.id' => $id )));
            if (empty( $this->request->data ) ) {
                $this->Session->setFlash('Such Venue is not exist.', 'flash_error');
                $this->redirect("/");
            }
            $this->request->data ['Venue'] ['name'] = html_entity_decode($this->request->data ['Venue'] ['name']);
            //$this->request->data ['Venue'] ['description'] = html_entity_decode( stripslashes ($this->request->data ['Venue'] ['description']) );

            $this->set('images', $this->Image->myImages('Venue', $id, 'All'));
            $this->set('offimage', $this->Image->myImages('Venue', $id, 'Personal'));
        }

        $nbpldays = $this->Venue->Nbplday->find('all', array('conditions' => array('venue_id' => $id)));
        //Managers info
        $this->Manager->recursive = 0;
        $managers = $this->Manager->find(
            'all', array(
                         'contain' => array('User')
                        ,'conditions' => array('Manager.model' => 'Venue','Manager.model_id' => $id)
                    )
        );

        //Get all shortnames of states for javascript (used in GoogleMap change location)
        $states = $this->Provincestate->find('all', array('order'=>'Provincestate.id ASC'));
        $all_states = "{";
        for ($i=0;$i < count($states);$i++){
            $all_states .= $states[$i]['Provincestate']['id'].":'".$states[$i]['Provincestate']['shortname']."'";
            if ($i != count($states) -1 ) {
                $all_states .= ",";
            }
        }
        $all_states .= "}";
        $this->set('all_states', $all_states);
        unset($all_states);

        //$venueactivities = $this->Venue->Venueactivity->find ( 'list' );
        //$venuefeatures = $this->Venue->Venuefeature->find ( 'list' );
        //$worktimes = $this->Venue->Worktime->find ( 'list' );

        $venuetypes = $this->Venue->Venuetype->find('list');
        //echo $this->request->data['Address']['provincestate_id'];
        $countries_states = $this->Venue->Address->setCountryStates('Address', $this->request->data['Address']['country_id']);
        $timeZones = $this->Timezone->find('list');

        $this->set('countries', $countries_states['countries']);
        $this->set('provincestates', $countries_states['states']);
        $this->set(compact('venuetypes', 'managers', 'timeZones', 'userID', 'nbpldays'));
        $this->set('accessApprove', $this->Access->getAccess('ApproveVenue', 'c'));
    }

    function delete( $id = null ) 
    {
        if (! $id) {
            $this->Session->setFlash('Invalid id for Venue', 'flash_error');
            $this->redirect(array ('action' => 'index' ));
        }

        $this->Access->checkAccess('venue', 'd', $id);

        $this->Venue->delete_venue($id);
        $this->Session->setFlash('Venue deleted', 'flash_success');
        $this->redirect(array ('action' => 'index' ));

    }
    function findVenue_api($searchName,$searchCity,$searchState,$searchCountry = "") 
    {
        Configure::write('debug', 0);
        if ($searchState != '') {
            $states = $this->Provincestate->find(
                'all', array(
                'conditions' => array(
                'OR' => array(
                'name LIKE' => '%' . $searchState . '%',
                'shortname LIKE' => '%' . $searchState . '%'
                )
                ),
                'recursive' => -1
                )
            );
            $provincestateIds = Set::classicExtract($states, '{n}.Provincestate.id');
            $conditions['Address.provincestate_id'] = $provincestateIds;
        }
        if ($searchCountry != '') {
            $countries = $this->Country->find(
                'all', array(
                'conditions' => array(
                'OR' => array(
                    'name LIKE' => '%' . $searchCountry . '%',
                    'shortname LIKE' => '%' . $searchCountry . '%'
                )
                ),
                'recursive' => -1
                )
            );
            $countryIDs = Set::classicExtract($countries, '{n}.Country.id');
            $conditions['Address.country_id'] = $countryIDs;
        }
        if ($searchCity != '') {
            $this->request->data['Venue']['city'] = htmlspecialchars(Sanitize::escape(trim($searchCity)));
            $conditions['Address.city LIKE'] = '%' . $this->request->data['Venue']['city'] . '%';
        }
        if ($searchName != '') {
            $this->request->data['Venue']['name'] = htmlspecialchars(Sanitize::escape(trim($searchName)));
            $conditions['Venue.name LIKE'] = '%' . $this->request->data['Venue']['name'] . '%';        
        }
        $conditions['Venue.is_deleted'] = 0;
        $venues = $this->Venue->find(
            'all', array(
            'contain' => array('Address', 'Venuetype'),
            'conditions' => $conditions
            )
        );
        return $venues;        
    }
    /**
     * search venue by name for assign venue
     * @author vovich
     * @param array $this->request->data['Venue']['name']
     * @param array $this->request->data['Venue']['model']
     * @param array $this->request->data['Venue']['model_id']
     */
    function searchvenue() 
    {
        Configure::write('debug', '0');
        $this->layout = false;
        if ($this->RequestHandler->isAjax() 
            && ($this->request->data['Venue']['name']) 
            || ($this->request->data['Venue']['provincestate']) 
            || ($this->request->data['Venue']['city'])
        ) {

            if (!empty($this->request->data['Venue']['provincestate'])) {
                $this->request->data['Venue']['provincestate'] = htmlspecialchars(Sanitize::escape(trim($this->request->data['Venue']['provincestate'])));
                $states = $this->Provincestate->find(
                    'all', array(
                    'conditions' => array(
                    'OR' => array(
                    'name LIKE' => '%' . $this->request->data['Venue']['provincestate'] . '%',
                    'shortname LIKE' => '%' . $this->request->data['Venue']['provincestate'] . '%'
                    )
                    ),
                    'recursive' => -1
                    )
                );
                $provincestateIds = Set::classicExtract($states, '{n}.Provincestate.id');
                $conditions['Address.provincestate_id'] = $provincestateIds;
            }
            if (!empty($this->request->data['Venue']['city'])) {
                $this->request->data['Venue']['city'] = htmlspecialchars(Sanitize::escape(trim($this->request->data['Venue']['city'])));
                $conditions['Address.city LIKE'] = '%' . $this->request->data['Venue']['city'] . '%';
            }
            if (!empty($this->request->data['Venue']['name'])) {
                $this->request->data['Venue']['name'] = htmlspecialchars(Sanitize::escape(trim($this->request->data['Venue']['name'])));
                $conditions['Venue.name LIKE'] = '%' . $this->request->data['Venue']['name'] . '%';
            }

            $conditions['Venue.is_deleted'] = 0;
            $venues = $this->Venue->find(
                'all', array(
                'contain' => array('Address', 'Venuetype'),
                'conditions' => $conditions
                )
            );

            if (!empty($venues)) {
                foreach ($venues as &$venue) {
                    $venue['Address']['country_name'] = $this->Country->field('name', 'id=' . $venue['Address']['country_id']);
                    $venue['Address']['state_name'] = $this->Provincestate->field('name', 'id=' . $venue['Address']['provincestate_id']);
                }

                $this->set(compact('venues'));
                if (!empty($this->request->data['Venue']['model']) && !empty($this->request->data['Venue']['model_id'])) {
                    $this->set('assignmodel', $this->request->data['Venue']['model']);
                    $this->set('modelID', $this->request->data['Venue']['model_id']);
                }
            } else {
                exit();
            }
        } else {
            exit('Wrong input parameters.');
        }
    }
    
    /**
     * Autocompleter for venues search form
     *
     * @param  string $fieldName - name, city, provincestate - searchable fields
     * @return string - |\n-separated autocomplete result
     */
    function autocomplete($fieldName = 'name') 
    {
        Configure::write('debug', 0);
        $this->autoRender = false;
        if (!in_array($fieldName, array('name', 'city', 'provincestate')) || !$this->RequestHandler->isAjax()) {
            $this->Session->setFlash('Invalid address', 'flash_error');
            return $this->redirect('/');
        }
        $this->request->data[$fieldName] = htmlspecialchars(Sanitize::escape($_GET['q']));
        $this->request->data['limit'] = Sanitize::paranoid($_GET['limit']);

        if ($fieldName == 'provincestate') {
            $venues = $this->Provincestate->find(
                'all', array(
                'conditions' => array(
                'Provincestate.name LIKE' => $this->request->data[$fieldName] . '%'
                ),
                'recursive' => -1
                )
            );
            $result = Set::classicExtract($venues, "{n}.Provincestate.name");
        } elseif ($fieldName == 'city') {
            $this->City = ClassRegistry::init('City');
            $venues = $this->City->find(
                'all', array(
                'fields' => array('city_accent'),
                'conditions' => array(
                'country' => array('ca', 'uk', 'us'),
                "{$fieldName} LIKE" => $this->request->data[$fieldName].'%'
                ),
                'limit' => $this->request->data['limit'],
                'group' => array('city_accent'),
                'order' => array('country' => 'desc', 'city' => 'asc'),
                'recursive' => -1
                )
            );
            $result = Set::classicExtract($venues, "{n}.City.city_accent");
        } else {
            $venues = $this->Venue->find(
                'all', array(
                'fields' => array($fieldName),
                'conditions' => array("{$fieldName} LIKE" => $this->request->data[$fieldName].'%'),
                'limit' => $this->request->data['limit'],
                'group' => array($fieldName),
                'recursive' => -1
                )
            );
            $result = Set::classicExtract($venues, "{n}.Venue.{$fieldName}");
        }
        echo htmlspecialchars_decode(implode("|\n", $result));
    }



    /**
     * Activation manager
     * @author Povstyanoy
     * @param string $model
     * @param int    $modelID
     * @param int    $venueID
     */
    function assignVenue($model=null,$modelID=null,$venueID=null)
    {
        if ($model && $venueID && $modelID) {

            $this->Venue->recursive = -1;
            $conditions = 'id='.$venueID;
            $venue = $this->Venue->find('first', compact('conditions'));

            if (empty($venue)) {
                $this->Session->setFlash('Can not find venue.', 'flash_error');
                $this->logErr('error occured: Can\'t find such venue.');
                return $this->redirect($_SERVER['HTTP_REFERER']);
            }

            $this->request->data[$model]['id']       = $modelID;
            $this->request->data[$model]['venue_id'] = $venueID;
            $this->$model->save($this->request->data);
            Cache::read('markers', 'markers');

            $this->Session->setFlash('Venue has been assigned.', 'flash_success');
            return $this->redirect($_SERVER['HTTP_REFERER']);

        } else {
            $this->Session->setFlash('Error with assign Manager.', 'flash_error');
            $this->logErr('error occured: Error with assign Manager.');
            $this->redirect($_SERVER['HTTP_REFERER']);
        }

    }

    /**
     * Find latitude and longitude for available address of Venues and save to Address table
     * @author Povstyanoy
     */
    function generateCoordinates( $password = "" ) 
    {
        if ($password != "novohudonosor" ) {
            exit("You don't have rights to run this script");
        }

        $this->autoRender = false;
        $this->Venue->Address->recursive = 0;
        $addresses = $this->Venue->Address->find(
            'all', array ('conditions' => array(
                                                                          'latitude' => null
                                                                        , 'longitude' => null
                                                                        , 'model' => 'Venue' ))
        );
        //print_r($addresses);
        //die;
        App::import('Vendor', 'GoogleMapAPI', array('file' => 'class.GoogleMapAPI.php'));
        $map = new GoogleMapAPI();
        $map->setAPIKey(GOOGLE_API_KEY);

        foreach ($addresses as $index => $address) {
            $geocode = $map->getCoordsByAddress(
                $address['Address']['address'], $address['Address']['city'], $address['Provincestate']['name'], $address['Country']['name'] 
            );
            $result = false ;
            if (!empty($geocode)) {
                $result = $this->Venue->Address->updateAll(
                    array( 'latitude' => "'".$geocode['lat']."'"
                                                                    , 'longitude' => "'".$geocode['lon']."'"), array ("Address.id = {$address['Address']['id']}" )
                );
                echo $index;print_r($geocode);var_dump($result);
                echo "<br />";
            }
        }
    }
    
    function generateslug( $password = "") 
    {

        if ($password != "may_become_hot") {
            echo "Password is invalid.";
            die;
        }

        $this->Venue->contain();
        $venues = $this->Venue->find('all', array("conditions" => array("Venue.is_deleted <> 1")));

        foreach ( $venues as $venue ) {
            $newvenue['Venue']['id'] = $venue['Venue']['id'];
            $newvenue['Venue']['modified'] = $venue['Venue']['modified'];
            $newvenue['Venue']['name'] = $venue['Venue']['name'];
            $this->Venue->save($newvenue, false);
            unset( $newvenue );
        }
        Cache::read('markers', 'markers');
        echo "Completed";
                die;

    }
    //If ($amf) returns in AMF, otherwise XML
    function m_returnVenuesWithinBounds($lat='',$lng='',$radius='',$limit=10,$amf=0) 
    {
        if (!empty($this->request->params['form']['lat'])) {
            $lat = $this->request->params['form']['lat']; 
        }
        if (!empty($this->request->params['form']['lng'])) {
            $lng = $this->request->params['form']['lng']; 
        }
        if (!empty($this->request->params['form']['radius'])) {
            $radius = $this->request->params['form']['radius']; 
        }
        if (!empty($this->request->params['form']['limit'])) {
            $limit = $this->request->params['form']['limit']; 
        }    
        if (!empty($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }    
            
        // Don't allow more than 25
        // if ($limit > 25) $limit = 25;        
        $addresses = $this->Venue->Address->getModelAddressesWithinRadius('Venue', $lat, $lng, $radius, $limit);
        //   return $addresses;
        $venueIDs = Set::extract($addresses, '{n}.addresses.model_id');
        $this->Venue->recursive = -1;
        $venues = $this->Venue->find(
            'all', array('conditions'=>array(
            'id'=>$venueIDs,
            'is_deleted'=>0
            ))
        );
        $venues = Set::combine($venues, '{n}.Venue.id', '{n}.Venue');
        $ctr = 0;
        $result = array();
        //return $venues;
        foreach ($addresses as $address) {       
            if (isset($venues[$address['addresses']['model_id']])) {
                $result[$ctr]['Distance'] = $address[0]['Distance'];
                $result[$ctr]['Address'] = $address['addresses'];
                $result[$ctr]['Venue'] = $venues[$address['addresses']['model_id']];
                $ctr++;
            }
        }
        return $this->returnMobileResult($result, $amf);
        
        $ctr = 0;   
        $result = array();      
        foreach ($addresses as $address) {
            foreach ($venues as $venue) {
                //return $venue['Venue']['id'];
                //return $result['addresses']['id'];
                if ($venue['Venue']['id'] == $address['addresses']['model_id']) {
                    $result[$ctr]['Distance'] = $address[0]['Distance'];
                    $result[$ctr]['Address'] = $address['addresses'];
                    $result[$ctr]['Venue'] = $venue['Venue'];
                    $ctr++;                 
                }
            }

        }
        return $this->returnMobileResult($result, $amf);
    }
    function m_checkout($amf = 0) 
    {
        Configure::write('debug', '0');
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['amf']; 
        }
                     
        $userid = $this->getUserID();   
        //if this is visitor, or not logged in, return error
        if ($userid < 2) {
            return $this->returnMobileResult(array('message'=>'You are not logged in.'), $amf); 
        }
        //get the current checkins
        $this->Checkin->recursive = -1;
        $checkins = $this->Checkin->find(
            'all', array(
            'conditions'=>array(
                'user_id'=>$userid,
                'checkedout'=>0))
        );
        foreach ($checkins as $checkin) {
            $checkin['Checkin']['checkedout'] = 1;
            $this->Checkin->save($checkin);
            $this->Venue->updateUsersCount($checkin['Checkin']['venue_id']);
        }
        return $this->returnMobileResult('ok', $amf);
    }

    function m_checkin($venueid = null,$amf = 0) 
    {
        Configure::write('debug', '0');
        if (isset($this->request->params['form']['venueid'])) {
            $venueid = $this->request->params['form']['venueid']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }
                     
        $userid = $this->getUserID();   
        //if this is visitor, or not logged in, return error
        if ($userid < 2) {
            return $this->returnMobileResult(array('message'=>'You are not logged in.'), $amf); 
        }
        
        //Check to ensure that Venue exists
        $this->Venue->recursive = -1;
        $venue = $this->Venue->find('first', array('conditions'=>array('id'=>$venueid,'is_deleted <>'=>1)));
        if (!$venue) {
            $this->returnMobileResult(array('result'=>'Venue does not exist'), $amf); 
        }
        //See if user is already checked in somewhere. If so, check out, and decreent counter
        $this->Checkin->recursive = -1;
        $checkins = $this->Checkin->find(
            'all', array(
            'conditions'=>array(
                'user_id'=>$userid,
                'checkedout'=>0))
        );
        foreach ($checkins as $checkin) {
            $checkin['Checkin']['checkedout'] = 1;
            $this->Checkin->save($checkin);
            $this->Venue->updateUsersCount($checkin['Checkin']['venue_id']);
        }    

        //Check user in, and increment Venues counter
        $this->Checkin->create();
        $newCheckin['Checkin']['venue_id'] = $venueid;
        $newCheckin['Checkin']['user_id'] = $userid;
        if (!$this->Checkin->save($newCheckin['Checkin'])) {
            return $this->returnMobileResult(array('problem: '=>$newCheckin['Checkin']), $amf); 
        }
        $this->Venue->updateUsersCount($venueid);
        
        return $this->returnMobileResult('ok', $amf);
    }
    /*
    function m_createOpenTable($name,$description,$lat,$lon,$amf = 0) {
    	if (isset($this->request->params['form']['name']))
    		$name = $this->request->params['form']['name'];
    	if (isset($this->request->params['form']['description']))
    		$description = $this->request->params['form']['description'];
    if (isset($this->request->params['form']['lat']))
    		$lat = $this->request->params['form']['lat'];
    	if (isset($this->request->params['form']['lon']))
    		$lon = $this->request->params['form']['lon'];
    	if (isset($this->request->params['form']['amf']))
    		$amf = $this->request->params['form']['amf'];
    		
    	$loggedUserID = $this->Access->getLoggedUserID();   
        if (!($loggedUserID > 0)) return $this->returnMobileResult(array('result'=>'You are not logged in.'),$amf);
        if (isset($this->request->data['name'])) {
            $name = $this->request->data['name'];
            $description = $this->request->data['description'];
            $lat = $this->request->data['lat'];
            $lon = $this->request->data['lon'];
        }
        if (!(strlen($name) > 0)) 
            return $this->returnMobilResult(array('result'=>'Invalid name'),$amf);  
            
        $newVenue['Venue']['name'] = $name;
        $newVenue['Venue']['description'] = $description;
        // For open tables, venuetype_id = 6
        $newVenue['Venue']['venuetype_id'] = 6;
        $this->Venue->create ();
        $this->Venue->save ($newVenue);
        Cache::read('markers', 'markers');
        $lastID = $this->Venue->getLastInsertID ();
        
        $newAddress['Address']['latitude'] = $lat;
        $newAddress['Address']['longitude'] = $lon;
        $newAddress['Address']['model'] = 'Venue';
        $newAddress['Address']['model_id'] = $lastID;
        $this->Venue->Address->save ( $newAddress);

        //Save Manager info
        $newManager['Manager']['user_id']        =    $loggedUserID;
        $newManager['Manager']['model']            =    'Venue';
        $newManager['Manager']['model_id']      =    $lastID;
        $newManager['Manager']['is_owner']        =    1;
        $newManager['Manager']['is_confirmed']    =    1;
        $this->Manager->save($newManager);

        $this->m_checkin($lastID,$amf);
        return $this->returnMobileResult(array('venueid'=>$lastID),$amf);
    }  */
    
    function m_listUsersInVenue($venueid,$amf = 0) 
    {
        if (isset($this->request->params['form']['venueid'])) {
            $venueid = $this->request->params['form']['venueid']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }

        $result  = $this->getUsersInVenue($venueid);        
        if ($result) {
            return $this->returnMobileResult($result, $amf); 
        }
        else {
            return $this->returnMobileResult('none', $amf); 
        }
    }
    function getUsersInVenue($venueid) 
    {
        //Check to ensure that Venue exists
        $this->Venue->recursive = -1;
        $venue = $this->Venue->find(
            'first', array('fields'=>array('id','id'),
            'conditions'=>array('id'=>$venueid,'is_deleted <>'=>1))
        );
        if (!$venue) { return $this->returnMobileResult(array('result'=>'Venue does not exist'), $amf); 
        } 
        
        $venueCheckins = $this->Checkin->find(
            'all',
            array(
                'conditions'=>array(
                      'Checkin.venue_id'=>$venueid,
                      'Checkin.checkedout <>'=>1,
                      'User.is_deleted'=>0
                      ),
                'contain'=>array('User'))
        );

                $ctr = 0;
                $result = array();
        foreach($venueCheckins as $venueCheckin) {
            $result[$ctr] = $venueCheckin['User'];
            unset($result[$ctr]['email']);
            $result[$ctr]['lastname'] = substr($result[$ctr]['lastname'], 0, 1);  
            $ctr++;   
        }
                //return 1;
                return $result;                     
    }
    function m_getVenue($venueid=null,$amf=0) 
    {
        if (isset($this->request->params['form']['venueid'])) {
            $venueid = $this->request->params['form']['venueid']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }
            
        $result = $this->Venue->find(
            'first', array('conditions'=>array(
            'Venue.id'=>$venueid,
            'Venue.is_deleted'=>0),
            'contain'=>array('Address','Phone'))
        );
        if (!$result) {
            return $this->returnMobileResult('Venue does not exist', $amf); 
        }
        $usersInVenue = $this->getUsersInVenue($venueid);
        if ($usersInVenue) { $result['Users'] = $usersInVenue; 
        }
        else { $result['Users'] = array(); 
        }
        $result['Upcoming Events'] = $this->getUpcomingEvents($venueid);
        if ($result) {
            return $this->returnMobileResult($result, $amf); 
        }
        else {
            return $this->returnMobileResult('Venue does not exist', $amf); 
        }
    }
    function getUpcomingEvents($venueid) 
    {
        $findArray = array(
            'conditions' => array('Event.venue_id' => $venueid,
                    'Event.is_deleted'=>0, 
                    'Event.end_date > CURDATE()'),
            'contain' => array(), 
            'order' => array('Event.start_date' => 'ASC')
        );
        $this->Event->recursive = -1;
        $events = $this->Event->find('all', $findArray);
        return $events;
    }
    
    /**
     * Show list of all events of current venue
     * @author Oleg D.
     */
    function all_events($venueID) 
    {
        $this->Venue->recursive = -1;
        $venue = $this->Venue->find('first', array('conditions'=>array('Venue.id' => $venueID, 'Venue.is_deleted <>' => 1)));
        if (empty($venue['Venue']['id'])) {
            $this->Session->setFlash('Venue ID error.', 'flash_error');
            return $this->redirect('/');
        }
        $this->paginate = array(
         'conditions' => array('Event.venue_id' => $venueID), 
         'contain' => array(), 
         'order' => array('Event.start_date' => 'desc')
        );

        $events = $this->paginate('Event');
                
        $this->pageTitle = $venue['Venue']['name'] . ' > ' . 'Events';                    
        $this->set(compact('venue', 'events'));
    } 
       
    /**
     * Show list of recent events of current venue
     * @author Oleg D.
     */
    function recent_events($venueID) 
    {
        $this->Venue->recursive = -1;
        $venue = $this->Venue->find('first', array('conditions'=>array('Venue.id' => $venueID, 'Venue.is_deleted <>' => 1)));
        if (empty($venue['Venue']['id'])) {
            $this->Session->setFlash('Venue ID error.', 'flash_error');
            return $this->redirect('/');
        }
        $this->paginate = array(
         'conditions' => array('Event.venue_id' => $venueID, 'Event.start_date < CURDATE()'), 
         'contain' => array(), 
         'order' => array('Event.start_date' => 'desc')
        );

        $events = $this->paginate('Event');
                
        $this->pageTitle = $venue['Venue']['name'] . ' > ' . 'Events';                    
        $this->set(compact('venue', 'events'));
    }  
    function mergeVenues($array) 
    {
        foreach ($array as $key=>$object) {
            return array($key,$object);
            $this->mergeTwoTeams($object[0], $object[1]);
        }
        return 'ok';
    }
    function mergeTwoVenues($venueIDToDelete,$venueIDToMergeInto) 
    {
        if (!$this->isUserSuperAdmin()) {
            return "Access Denied.";
        }
        //Delete Addresses
        $this->Venue->Address->recursive = -1;
        $addressesToDelete = $this->Venue->Address->find(
            'all', array('conditions'=>array(
            'is_deleted'=>0,
            'model'=>'Venue',
            'model_id'=>$venueIDToDelete))
        );
        foreach ($addressesToDelete as $address) {
            $address['is_deleted'] = 1;
            $address['deleted'] =  date('Y-m-d H:i:s');
            $this->Venue->Address->save($address);
        }
        //Merge Checkins. Get the old checkins, the new checkins, then filter and change
        $this->Checkin->recursive = -1;
        $oldCheckins = $this->Checkin->find(
            'all', array(
            'conditions'=>array(
                'venue_id'=>$venueIDToDelete,
                'checkedout'=>0))
        );
        $newCheckins = $this->Checkin->find(
            'all', array(
            'conditions'=>array(
                'venue_id'=>$venueIDToDelete,
                'checkedout'=>0))
        );
        $newCheckinsByUserID = Set::combine($newCheckins, '{n}.Checkin.user_id', '{n}.Checkin');
        foreach ($oldCheckins as $checkinToModify) {
            $userid = $checkinToModify['Checkin']['user_id'];
            if (isset($newCheckinsByUserID[$userid])) {
                //Duplicate. Delete old checkin
                $checkinToModify['Checkin']['checkedout'] = 1;
                $this->Checkin->save($checkinToModify);
            }
            else {
                //move the checkin to new venue
                $checkinToModify['Checkin']['venue_id'] = $venueIDToMergeInto;
                $this->Checkin->save($checkinToModify);
            }
        }
        //Events
        $this->Event->recursive = -1;
        $events = $this->Event->find(
            'all', array('conditions'=>array(
            'venue_id'=>$venueIDToDelete,
            'is_deleted'=>0))
        );
        foreach ($events as $event) {
            $event['Event']['venue_id'] = $venueIDToMergeInto;
            $this->Event->save($event);
        }
        //Skip managers...
        //organizations_objects
        $OrganizationsObjects = ClassRegistry::init('OrganizationsObject');
        $OrganizationsObjects->recursive = -1;
        $oldOrgObjects = $OrganizationsObjects->find(
            'all', array(
            'conditions'=>array(
                'model'=>'Venue',
                'model_id'=>$venueIDToDelete,
                ))
        );
            $newOrgObjects = $OrganizationsObjects->find(
                'all', array(
                'conditions'=>array(
                'model'=>'Venue',
                'model_id'=>$venueIDToMergeInto,
                ))
            );
            $newOrgObjectsByOrgID = Set::combine(
                $newOrgObjects,
                '{n}.OrganizationsObject.organization_id', '{n}.OrganizationsObject'
            );
            foreach ($oldOrgObjects as $orgObjectToModify) {
                $orgID = $orgObjectToModify['OrganizationsObject']['organization_id'];
                if (isset($newOrgObjectsByOrgID[$orgID])) {
                    $orgObjectToModify['OrganizationsObject']['is_deleted'] = 1;
                    $OrganizationsObjects->save($orgObjectToModify);
                }
                else {
                    $orgObjectToModify['OrganizationsObject']['model_id'] = $venueIDToMergeInto;
                    $OrganizationsObjects->save($orgObjectToModify);                
                }
            }
            //Delete the old venue
            $this->Venue->recursive = -1;
            $venue = $this->Venue->find('first', array('conditions'=>array('id'=>$venueIDToDelete)));
            if ($venue) {
                $venue['Venue']['is_deleted'] = 1;
                $venue['Venue']['deleted'] = date('Y-m-d H:i:s'); 
                $this->Venue->save($venue);
            }
            return 'ok';
    }  
    function updateNBPLVenues() 
    {
        $this->Access->checkAccess('venue', 'u', array());
        $this->Venue->recursive = -1;
        $venues = $this->Venue->find(
            'all', array('conditions'=>array(
                'is_deleted'=>0,
                'nbpltype'=>array('Flagship','Member')),
            'contain'=>array('Nbplday')
            )
        );
        foreach ($venues as $venue) {
            $this->getNBPLVenueUpToDate($venue['Venue'], $venue['Nbplday']);
        }
        
        $this->Session->setFlash('NBPL Events up to date', 'flash_success');
        $this->redirect('/');
    }
    /*
    private function getVenueUpToDateByID($id) {
        if (!$this->isUserSuperAdmin())
            return $this->returnJSONResult('Access Denied');
        $this->Venue->recursive = -1;
        $venue = $this->Venue->find('first',array('conditions'=>array('id'=>$id)));
        return $this->getNBPLVenueUpToDate($venue['Venue']);
    } */
    function testGetVenueUpToDate($venueid) 
    {
        $venue = $this->Venue->find(
            'first', array('conditions'=>array(
                'is_deleted'=>0,
                'Venue.id'=>$venueid,
                'nbpltype'=>array('Flagship','Member')),
            'contain'=>array('Nbplday')
            )
        );
        if (!$venue) { return 'not found'; 
        }
        return $this->getNBPLVenueUpToDate($venue['Venue'], $venue['Nbplday']);
    }
    function getNBPLVenueUpToDate($venue,$nbpldays) 
    {
        //How this works:
        //Search for all events of type nbplweekly at this venue with start date after today
        //Check to see if there is one a week from now, one two weeks from now,
        // and one three weeks from now. For any day, if its missing, create one.     
        $Event = ClassRegistry::init('Event');
        $Event->recursive = -1;
        
        $existingEvents = $Event->find(
            'all', array('conditions'=>array(
            'venue_id'=>$venue['id'],
            'type'=>'nbplweekly',
            'is_deleted'=>0,
            'start_date >= CURDATE()'
            ))
        );
        unset($existingEventsByDate);
        foreach ($existingEvents as $existingEvent) {
            $existingEventStartDate = $existingEvent['Event']['start_date'];
            $existingEventStartDate = date("Y-m-d", strtotime($existingEventStartDate));
            $existingEventsByDate[$existingEventStartDate] = $existingEvent;
        }
        foreach ($nbpldays as $currentNBPLDayObject) {
            $nbplday = $currentNBPLDayObject['nbplday'];
            $nbplstarttime = $currentNBPLDayObject['nbplstarttime'];
            if ($venue['nbplstartday']) { 
                $nbplstartday = strtotime($venue['nbplstartday']); 
            }
            else {
                $nbplstartday = strtotime('2012-02-29'); //For now, start all events after Feb 28th
            }            $nbplstartday = max($nbplstartday, time());       
            $nextEventDate = date("Y-m-d", strtotime('next '.$nbplday, $nbplstartday));
            if (!isset($existingEventsByDate[$nextEventDate])) {
                $this->addNBPLEvent($venue, $nextEventDate.' '.$nbplstarttime);
            }
            //return ;
            $secondEventDate = date("Y-m-d", strtotime('next '.$nbplday.' +7 days', $nbplstartday));
            if (!isset($existingEventsByDate[$secondEventDate])) {
                $this->addNBPLEvent($venue, $secondEventDate.' '.$nbplstarttime);
            }
            $thirdEventDate = date("Y-m-d", strtotime('next '.$nbplday.' +14 days', $nbplstartday));   
            //return $thirdEventDate;
            if (!isset($existingEventsByDate[$thirdEventDate])) {
                $this->addNBPLEvent($venue, $thirdEventDate.' '.$nbplstarttime);
            }
        }
        return ;
    }
    private function addNBPLEvent($venue,$startDate) 
    {
        Configure::write('debug', 0);
        $stringDateOfEvent = date("n/j/Y", strtotime($startDate));
        //If this has been called, we know the event needs to be created
        $newEvent['Event']['type'] = 'nbplweekly';
        $newEvent['Event']['venue_id'] = $venue['id'];
        $newEvent['Event']['user_id'] = $this->getUserID();
        $newEvent['Event']['name'] = $stringDateOfEvent.' NBPL Weekly Tournament at '.$venue['name'];
        $newEvent['Event']['is_room'] = 0;
        $newEvent['Event']['start_date'] = $startDate; 
        //FOR NOW, EVENT IS FOUR HOURS
        $eventLength = 4 * 60 * 60;                                  
        $newEvent['Event']['end_date'] =date('Y-m-d\TH:i:s.uP', $eventLength + strtotime($startDate)); 
        $newEvent['Event']['signup_required'] = 1;
        $newEvent['Event']['finish_signup_date'] = $startDate;
        $newEvent['Event']['multi_team'] = 1;
        $newEvent['Event']['max_people_team'] = 2;
        $newEvent['Event']['min_people_team'] = 2;
        $newEvent['Event']['people_team'] = 2;
        $newEvent['Event']['timezone_id'] = $venue['timezone_id'];
        $newEvent['Event']['thankyou'] = 'Thank you for signing up for an NBPL event. Your signup is not complete. Please click below to create your team.';
        //NEED AGREEMENT
        //NEED DETAILS FIELD IN VENUE
        $Event = ClassRegistry::init('Event');
        $Event->create();
        $Event->save($newEvent);   
        $eventID = $Event->getLastInsertID();
       
        $newPackage['model'] = 'Event';
        $newPackage['model_id'] = $eventID;
        $newPackage['name'] = 'NBPL Weekly Entrance';
        $newPackage['description'] = '';
        $newPacakge['people_in_room'] = 0;
        $Package = ClassRegistry::init('Package');
        $Package->create();
        $Package->save($newPackage);
        $packageID = $Package->getLastInsertID();
       
       
        $newPackageDetails['package_id'] = $packageID;
        $newPackageDetails['start_date'] = date('Y-m-d');
        $newPackageDetails['end_date'] = $startDate;
        $newPackageDetails['price'] = 0;
        $newPackageDetails['price_team'] = 0;
        $newPackageDetails['deposit'] = 0;
        $Packagedetail = ClassRegistry::init('Packagedetail');
        $Packagedetail->create();
        $Packagedetail->save($newPackageDetails);
       
        //get venue managers, and make manager of this event
        $this->Manager->recursive = -1;
        $managers = $this->Manager->find(
            'all', array('conditions'=>array(
            'model'=>'Venue',
            'model_id'=>$venue['id']))
        );             
        foreach ($managers as $manager) {
            unset($newManager);
            $newManager['model'] = 'Event';
            $newManager['user_id'] = $manager['Manager']['user_id'];
            $newManager['model_id'] = $eventID;
            $newManager['is_confirmed'] = 1;
            $this->Manager->create();
            $this->Manager->save($newManager);
        }
    }
    function testTheory() 
    {
        $n = strtotime('next Tuesday', strtotime('2012-03-01'));
        return date('Y-m-d', $n);
    }
    function ajaxShowManagers($id) 
    {
        $this->request->data = $this->Venue->find('first', array('conditions'=>array('Venue.id'=>$id),'contain'=>array('User')));
    }
    function nbpl_nights() 
    {
        $zip = '';
        $geocode = array();
       
        if (!empty($_REQUEST['zip'])) {
            $zip = trim($_REQUEST['zip']);
        }
        $ObjVenueView = ClassRegistry::init("VenueView");
        $conditions = array('VenueView.is_deleted' => 0, 'VenueView.nbpltype <> ' => 'None');
        $order = array('VenueView.id' => 'DESC');
        $fields = array('*');
        if (!empty($zip)) { 
            App::import('Vendor', 'GoogleMapAPI', array('file' => 'class.GoogleMapAPI.php'));
            $map = new GoogleMapAPI();
            $map->setAPIKey(GOOGLE_MAP_KEY);
            $geocode = $map->geoGetCoords('UNITED STATES ' . $zip);
            if (!empty($geocode['lat']) && !empty($geocode['lon'])) {
                $fields[] = "(((acos(sin((" . $geocode['lat'] . " * pi()/180)) * sin((latitude*pi()/180)) + cos((" . $geocode['lat'] . " *pi()/180)) * cos((latitude * pi()/180)) 
				* cos(((" . $geocode['lon'] . "- longitude)*pi()/180)))) * 180/pi()) * 60 *1.1515) as distance"; 
                $order = array('distance' => 'ASC') + $order;           
            } else {
                $zip = '';
                 $this->Session->setFlash('Incorrect Zip Code', 'flash_error');
            }
        }        

       
        $venues = $ObjVenueView->find('all', array('fields' => $fields, 'contain' => array('Nbplday'), 'conditions' => $conditions, 'order' => $order));
        $this->set(compact('venues', 'zip', 'geocode'));    
    }
}
?>
