<?php

class OrganizationsController extends AppController
{

    var $name    = 'Organizations';
    var $uses = array('Organization', 'OrganizationsObject','OrganizationsUser','Country');
    
    /**
     * Show all organizations
     * @author Oleg D.
    */
    function index($filter = null) 
    {    
        $conditions = array('Organization.is_deleted' => 0);
        
        /* filter Getting data from the session or from the form*/
        if ($filter) {
            if(!empty($this->request->data['OrgFilter'])) {
                $this->Session->write('OrgFilterAll', $this->request->data['OrgFilter']);
            } elseif ($this->Session->check('OrgFilterAll')) {
                $this->request->data['OrgFilter'] = $this->Session->read('OrgFilterAll');
            }        
            
            if (!empty ($this->request->data['OrgFilter'])) {
                foreach ($this->request->data['OrgFilter'] as $key => $val) {
                    $this->request->data['OrgFilter'][$key] = trim($val);
                }
                //Prepare data for the filter
                if (!empty( $this->request->data['OrgFilter']['name'])) {
                    $conditions['Organization.name LIKE'] = '%' . $this->request->data['OrgFilter']['name'] . '%';
                }
                if (!empty( $this->request->data['OrgFilter']['city'])) {
                    $conditions['Address.city LIKE'] = '%' . $this->request->data['OrgFilter']['city'] . '%';
                } 
                if (!empty( $this->request->data['OrgFilter']['provincestate_id'])) {
                    $conditions['Address.provincestate_id'] = $this->request->data['OrgFilter']['provincestate_id'];
                }                                                     
            }    
        } else {
            $this->Session->delete('OrgFilterAll');
        }        
        
        $this->paginate = array(
         'conditions' => $conditions, 
         'contain' => array('Address' => array('Provincestate'), 'Image'), 
         'order' => array('Organization.count_users' => 'desc')
        );

        $organizations = $this->paginate('Organization');
        //pr($organizations);
        $countries_states = $this->Organization->Address->setCountryStates('Address', 1);
        $this->set('provincestates', $countries_states['states']);
        $this->set(compact('organizations'));        
    }

    /**
     * Show all organizations
     * @author Oleg D.
    */
    function my($filter = null) 
    {    
        
        $orgIDs = $this->Organization->OrganizationsUser->find('list', array('fields' => array('organization_id', 'organization_id'), 'conditions' => array('user_id' => $this->getUserID(), 'status' => 'accepted')));
        $conditions = array('Organization.is_deleted' => 0, 'Organization.id' => $orgIDs);        
        $this->paginate = array(
         'conditions' => $conditions, 
         'contain' => array('Address' => array('Provincestate'), 'Image'), 
         'order' => array('Organization.count_users' => 'desc')
        );

        $organizations = $this->paginate('Organization');

        $countries_states = $this->Organization->Address->setCountryStates('Address', 1);
        $this->set('provincestates', $countries_states['states']);
        $this->set(compact('organizations'));        
    }    
    /**
     * Show organization by slug
     * @author Oleg D.
     */
    function show($slug = null) 
    {
        if (empty($slug)) {
            $this->Session->setFlash('There is no Organization with such name.', 'flash_error');
            return $this->redirect('/');
        }
        $this->Organization->contain(array('Image', 'Address' => array('Provincestate')));
        $organization = $this->Organization->find('first', array('conditions' => array('Organization.slug' => $slug, 'Organization.is_deleted' => 0)));
        
        if (empty($organization)) {
            $this->Session->setFlash('There is no Organization with such name.', 'flash_error');
            return $this->redirect('/');
        }
        if ($this->Access->getAccess('OrganizationsNews', 'c', $this->Organization->OrganizationsUser->getManagers($organization['Organization']['id']))) {
            $canDeleteNews = $canEditNews = 1;            
        } else {
            $canDeleteNews = $canEditNews = 0;            
        }
        
        $orgUser = $this->Organization->OrganizationsUser->getOrgUser($organization['Organization']['id']);
        $this->set('orgUser',  $orgUser);
        
        $AlbumObject = ClassRegistry::init('Album');    
        $albums = $AlbumObject->find('all', array('order' => array('Album.id' => 'desc'), 'conditions' => array('Album.model_id' => $organization['Organization']['id'], 'Album.model' => 'Organization', 'Album.is_deleted' => 0), 'contain' => array('CoverVideo', 'CoverImage')));
        
        $events = $this->OrganizationsObject->find('all', array('limit' => 5, 'contain' => array('Event'), 'conditions' => array('organization_id' => $organization['Organization']['id'], 'model' => 'Event')));
        $venues = $this->OrganizationsObject->find('all', array('limit' => 5, 'contain' => array('Venue' => array('conditions' => array('Venue.is_deleted <>' => '1')), 'Venue.Address', 'Venue.Address.Provincestate'), 'conditions' => array('OrganizationsObject.organization_id' => $organization['Organization']['id'], 'OrganizationsObject.model' => 'Venue')));
        $news = $this->Organization->OrganizationNews->find('all', array('limit' => 5, 'limit' => '100', 'contain' => array('Image', 'User'), 'conditions' =>  array('OrganizationNews.organization_id' => $organization['Organization']['id'], 'OrganizationNews.is_deleted' => 0), 'order' => array('OrganizationNews.id' => 'desc')));
        
        $this->pageTitle = $organization['Organization']['name'];    
        $this->set('organizationsMenu',  1);                
        $this->set(compact('organization', 'albums', 'events', 'venues', 'news', 'canDeleteNews', 'canEditNews'));
    }
    
    /**
     * Show about info organization by slug
     * @author Oleg D.
     */
    function about($slug = null) 
    {
        if (empty($slug)) {
            $this->Session->setFlash('There is no Organization with such name.', 'flash_error');
            return $this->redirect('/');
        }
        $this->Organization->contain(array('Image', 'Address' => array('Provincestate')));
        $organization = $this->Organization->find('first', array('conditions' => array('Organization.slug' => $slug, 'Organization.is_deleted' => 0)));
        
        $orgUser = $this->Organization->OrganizationsUser->getOrgUser($organization['Organization']['id']);
        $this->set('orgUser',  $orgUser);
                
        if (empty($organization)) {
            $this->Session->setFlash('There is no Organization with such name.', 'flash_error');
            return $this->redirect('/');
        }
        
        $this->pageTitle = 'About ' . $organization['Organization']['name'];
        $this->set('organizationsMenu',  1);
            
        $this->set(compact('organization'));
    }
    /**
     * Edit organization
     * @author Oleg D.
     */    
    function edit($id = null) 
    {
        $this->Access->checkAccess('Organization', 'u', $this->Organization->OrganizationsUser->getManagers($id));
        if (empty($id)) {
            $this->Session->setFlash('There is no Organization with such id.', 'flash_error');
            return $this->redirect('/');
        }
                
        $this->Organization->contain('Address', 'Image');
        $organization = $this->Organization->find('first', array('conditions' => array('Organization.id' => $id, 'Organization.is_deleted' => 0)));
        
        $orgUser = $this->Organization->OrganizationsUser->getOrgUser($organization['Organization']['id']);
        $this->set('orgUser',  $orgUser);
                
        if (empty($organization)) {
            $this->Session->setFlash('There is no Organization with such id.', 'flash_error');
            return $this->redirect('/');
        }
                
        if (!empty($this->request->data)) {
            $this->request->data['Organization']['web_address'] = trim($this->request->data['Organization']['web_address']);
            if ($this->Organization->save($this->request->data)) {
                if (!empty($this->request->data['Address']['country_id']) || !empty($this->request->data['Address']['address'])) {
                    $this->request->data['Address']['label'] = 'Business';
                    $this->request->data['Address']['model'] = 'Organization';
                    $this->request->data['Address']['model_id'] = $id;
                                                            
                    $this->Organization->Address->save($this->request->data);    
                }
                $this->Session->setFlash('Organization has been saved.', 'flash_success');
                return $this->redirect('/o/' . $this->request->data['Organization']['slug']);
            }                    
            
        } else {        
            $this->request->data = $organization;        
        }
        
        
        //pr($this->request->data);
        if (!empty($this->request->data['Address']['country_id'])) {
            $countries_states = $this->Organization->Address->setCountryStates('Address', $this->request->data['Address']['country_id']);
        } else {
            $countries_states = $this->Organization->Address->setCountryStates();
        }
        $this->set('countries', $countries_states['countries']);
        $this->set('provincestates', $countries_states['states']);
            
        $this->pageTitle = $organization['Organization']['name'] . ' :: ' . 'Edit';        
        $this->set('organizationsMenu',  1);
        
        $this->set(compact('organization'));        
    }

    /**
     * Add organization
     * @author Oleg D.
     */    
    function add() 
    {
        $this->Access->checkAccess('Organization', 'c');
        if (!empty($this->request->data)) {
            $this->request->data['Organization']['web_address'] = trim($this->request->data['Organization']['web_address']);
            
            
            if ($this->request->data['Organization']['web_address'] == 'http://') {
                unset($this->request->data['Organization']['web_address']);                            
            }                                                           
            $this->request->data['Organization']['user_id'] = $this->getUserID();
            $this->Organization->create();    
            if ($this->Organization->save($this->request->data)) {
                $id = $this->Organization->getLastInsertID();
                $this->Organization->OrganizationsUser->create();
                $this->Organization->OrganizationsUser->save(array('organization_id' => $id, 'user_id' => $this->getUserID(), 'role' => 'creator', 'status' => 'accepted'));
                if (!empty($this->request->data['Address']['country_id']) || !empty($this->request->data['Address']['address'])) {                    
                    $this->request->data['Address']['label'] = 'Business';
                    $this->request->data['Address']['model'] = 'Organization';
                    $this->request->data['Address']['model_id'] = $id;
                    $country = $this->Organization->Address->Country->find(
                        'first', array('conditions'=>
                        array('id'=>$this->request->data['Address']['country_id']))
                    );
                    $latLon = $this->Organization->Address->getLatLon(
                        $this->request->data['Address']['address'], $this->request->data['Address']['city'],
                        array('id'=>$this->request->data['Address']['provincestate_id']),
                        array('id'=>$this->request->data['Address']['country_id'])
                    );
                    $this->request->data['Address']['latitude'] = $latLon['lat'];
                    $this->request->data['Address']['longitude'] = $latLon['lon'];
                    
                    $this->Organization->Address->create();                                        
                    $this->Organization->Address->save($this->request->data);    
                    //save latitude and longitude to table
                    $this->Organization->save(
                        array(
                        'id'=>$id,
                        'latitude'=>$latLon['lat'],
                        'longitude'=>$latLon['lon'])
                    );
                }
                $this->Session->setFlash('Organization has been saved.', 'flash_success');
                return $this->redirect('/o/' . $this->request->data['Organization']['slug']);
            }                    
            
        }        
        
        //pr($this->request->data);
        if (!empty($this->request->data['Address']['country_id'])) {
            $countries_states = $this->Organization->Address->setCountryStates('Address', $this->request->data['Address']['country_id']);
        } else {
            $countries_states = $this->Organization->Address->setCountryStates();
        }
        $this->set('countries', $countries_states['countries']);
        $this->set('provincestates', $countries_states['states']);
            
        $this->pageTitle = "Add new Organization";        
        //$this->set('organizationsMenu',  1);
        
    }
    /**
     * Delete organization
     * @author Oleg D.
     */
    function delete($id) 
    {
        $this->Access->checkAccess('Organization', 'd', $this->Organization->OrganizationsUser->getManagers($id));        
        $this->Organization->delete($id);
        $this->Session->setFlash('Organization has been deleted', 'flash_success');        
        return $this->redirect('/organizations');        
        
    }
    
    /**
     * Show organizations albums
     * @author Oleg D.
     */
    function albums($slug = null) 
    {
        if (empty($slug)) {
            $this->Session->setFlash('There is no Organization with such name.', 'flash_error');
            return $this->redirect('/');
        }
        //$this->Organization->contain(array('Image', 'Address' => array('Provincestate')));
        $organization = $this->Organization->find('first', array('conditions' => array('Organization.slug' => $slug, 'Organization.is_deleted' => 0)));
        
        if (empty($organization)) {
            $this->Session->setFlash('There is no Organization with such name.', 'flash_error');
            return $this->redirect('/');
        }
        
        $orgUser = $this->Organization->OrganizationsUser->getOrgUser($organization['Organization']['id']);
        $this->set('orgUser',  $orgUser);
                
        $this->pageTitle = $organization['Organization']['name'] . ' :: Albums';
        $this->set('organizationsMenu',  1);
            
        $this->set(compact('organization'));
    }
    function m_findOrganizations($searchTerm,$country_id,$state_id,$limit = 10,$amf = 0) 
    {
        if (isset($this->request->params['form']['searchTerm'])) {
            $searchTerm = $this->request->params['form']['searchTerm']; 
        }
        if (isset($this->request->params['form']['country_id'])) {
            $country_id = $this->request->params['form']['country_id']; 
        }
        if (isset($this->request->params['form']['state_id'])) {
            $state_id = $this->request->params['form']['state_id']; 
        }
        if (isset($this->request->params['form']['limit'])) {
            $limit = $this->request->params['form']['limit']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }  
        
        if ($searchTerm != '') {
            $conditions['Organization.name LIKE'] = '%' . $searchTerm . '%';
        }
        if ($country_id > 0) {
            $conditions['Address.city LIKE'] = '%' . $country_id . '%';
        } 
        if ($state_id> 0 ) {
            $conditions['Address.provincestate_id'] = $state_id;
        } 
        $results = $this->Organization->find(
            'all', array(
            'conditions' => $conditions, 
            'contain' => array('Address' => array('Provincestate'), 'Image'), 
            'limit'=>$limit)
        );
        return $this->returnMobileResult($results, $amf);        
    }
    function m_getClosestOrganizations($lat,$lng,$radius,$limit=10,$amf = 0) 
    {
        if (isset($this->form['params']['lat'])) {
            $lat = $this->form['params']['lat']; 
        }
        if (isset($this->form['params']['lng'])) {
            $lng = $this->form['params']['lng']; 
        }
        if (isset($this->form['params']['radius'])) {
            $radius = $this->form['params']['radius']; 
        }
        if (isset($this->form['params']['limit'])) {
            $limit = $this->form['params']['limit']; 
        }    
        if (isset($this->form['params']['amf'])) {
            $amf = $this->form['params']['amf']; 
        }    
            
        // Don't allow more than 25
        if ($limit > 25) { $limit = 25; 
        }        
        $addresses = $this->Organization->Address->getModelAddressesWithinRadius('Organization', $lat, $lng, $radius, $limit);  
        $venueIDs = Set::extract($addresses, '{n}.addresses.model_id');
        $organizations = $this->Organization->find(
            'all', array('conditions'=>array(
                'Organization.id'=>$venueIDs,
                'Organization.is_deleted'=>0),
            'contain' => array('Address' => array('Provincestate'), 'Image'))
        );
            
        //return $organizations;                            
        $ctr = 0;   
        $result = array();      
        foreach ($addresses as $address) {
            foreach ($organizations as $organization) {
                if ($organization['Organization']['id'] == $address['addresses']['model_id']) {
                    $result[$ctr]['Distance'] = $address[0]['Distance'];
                    $result[$ctr]['Address'] = $address['addresses'];
                    $result[$ctr]['Organization'] = $organization['Organization'];
                    $ctr++;                 
                }
            }

        }
        return $this->returnMobileResult($result, $amf);
    }
    function m_getPlayerRankWithinOrg($org_id,$user_id,$amf = 0) 
    {
        if (isset($this->request->params['form']['org_id'])) {
            $org_id = $this->request->params['form']['org_id']; 
        }
        if (isset($this->request->params['form']['user_id'])) {
            $user_id = $this->request->params['form']['user_id']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }
        
        //First, get the User and UserAffilobject, so you see his rating
        $user = $this->OrganizationsUser->find(
            'first', array('conditions'=>array(
            'OrganizationsUser.user_id'=>$user_id,
            'OrganizationsUser.organization_id'=>$org_id,
            'OrganizationsUser.status'=>array('accepted','invited','pending')),
            'contain'=>array('User'))
        );
        if (!$user) {
            return $this->returnMobileResult('User not found, or is not affiliated with Organization.', $amf);
        }
        $userRating = $user['User']['rating'];
        $numPlayersBetter = $this->OrganizationsUser->find(
            'count', array(
                'conditions'=>array(
                    'OrganizationsUser.organization_id'=>$org_id,
                    'OrganizationsUser.status'=>array('accepted','invited','pending'),
                    'User.rating >'=>$userRating
                    ),
                'contain'=>array('User'),
                'fields'=>array('User.id'))
        );
                $this->OrganizationsUser->recursive = -1;
                $numPlayersTotal = $this->OrganizationsUser->find(
                    'count', array(
                    'conditions'=>array(
                    'OrganizationsUser.organization_id'=>$org_id,
                    'OrganizationsUser.status'=>array('accepted','invited','pending')))
                );
                // So now, we have the rank and the total. If the rank is 35, we want to return users 31-40.
                // Rounddown( X / 10) * 10 + 1 
                $start = 10 * floor($numPlayersBetter/10) + 1;          
                $closePlayers = $this->m_getOrgLeaderBoard($org_id, $start, 10, $amf);
                return $this->returnMobileResult(
                    array('rank'=>$numPlayersBetter+1,'numplayerstotal'=>$numPlayersTotal,
                    'Leaderboard'=>$closePlayers), $amf
                );
    }
    function m_viewOrgWithLeaderboard($org_id = null,$amf = 0) 
    {
        if (isset($this->request->params['form']['org_id'])) {
            $org_id = mysql_escape_string($this->request->params['form']['org_id']); 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = mysql_escape_string($this->request->params['form']['amf']); 
        }
        $this->Organization->recursive = -1;
        $result = $this->Organization->find('first', array('id'=>$org_id));
        $result['Leaderboard'] = $this->getOrgLeaderBoard($org_id, 1, 10);
        return $this->returnMobileResult($result, $amf);
    }

    function m_getOrgLeaderBoard($org_id = null,$start = 0,$limit = 25,$amf = 0) 
    {
        if (isset($this->request->params['form']['org_id'])) {
            $org_id = mysql_escape_string($this->request->params['form']['org_id']); 
        }
        if (isset($this->request->params['form']['start'])) {
            $start = mysql_escape_string($this->request->params['form']['start']); 
        }
        if (isset($this->request->params['form']['limit'])) {
            $limit= mysql_escape_string($this->request->params['form']['limit']); 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = mysql_escape_string($this->request->params['form']['amf']); 
        }
        $result = $this->getOrgLeaderBoard($org_id, $start, $limit);
        return $this->returnMobileResult($result, $amf);
    }
    function getOrgLeaderBoard($org_id = null,$start = 0,$limit = 25) 
    {
        if (!$org_id) {
            return 'bad parameters'; 
        }
        $this->OrganizationsUser->recursive = -1;
        
        $limitStart = $start-1;
        $orgsUsers = $this->OrganizationsUser->find(
            'all', array(
            'conditions'=>array(
                'OrganizationsUser.organization_id'=>$org_id,
                'OrganizationsUser.status'=>array('pending','accepted','invited')),
            'contain'=>array('User'),
            'order'=>array('User.rating'=>'DESC'),
            'limit'=>$limitStart.','.$limit)
        );
        
        $currentRank = $start;
        foreach ($orgsUsers as $orgUser) {
            unset($orgUser['User']['email']);
            $results[$currentRank] = $orgUser;
            $currentRank++;
        }
        
        return $results;
    }
    function updateLatLong($orgid) 
    {
        $organization = $this->Organization->find(
            'first', array(
            'conditions'=>array('Organization.id'=>$orgid,'Organization.is_deleted'=>0),
            'contain'=>array('Address'))
        );
        if (!$organization) {
            return ; 
        }
        $latLon = $this->Organization->Address->getLatLon(
            $organization['Address']['address'],
            $organization['Address']['city'],
            array('id'=>$organization['Address']['provincestate_id']),
            array('id'=>$organization['Address']['country_id'])
        );
        $this->Organization->save(
            array(
            'id'=>$orgid,
            'latitude'=>$latLon['lat'],
            'longitude'=>$latLon['lon'])
        );
    }
    
    /**
     * Select Hometown by AJAX
     * @author Oleg D.
     */
    function ajaxSelectOrganization() 
    {
        //Configure::write('debug', '0');
        $userID = $this->getUserID();
        $myOrgIDs = $this->Organization->OrganizationsUser->find('list', array('fields' => array('organization_id', 'organization_id'), 'conditions' => array('OrganizationsUser.user_id' => $userID, 'OrganizationsUser.status <> ' => 'deleted')));
        $organizations = $this->Organization->find('list', array('fields' => array('slug', 'name'), 'conditions' => array('NOT' => array('Organization.id' => $myOrgIDs), 'AND' => array('Organization.is_deleted' => 0))));
        
        $this->set(compact('userID', 'organizations'));
    }
    
}
?>
