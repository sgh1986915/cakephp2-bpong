<?php class UsersAffilsController extends AppController
{


    
    var $name = 'UsersAffils';
    var $uses = array('UsersAffil');
    
    /**
* 
* 
     * Select Hometown by AJAX
     *
     * @author Oleg D.
     
*/
    function ajaxSelectHometown() 
    {
        Configure::write('debug', '0');
        $userID = $this->getUserID();
        $affil = $this->UsersAffil->find('first', array('contain' => array('Hometown'), 'conditions' => array('UsersAffil.user_id' => $userID, 'UsersAffil.model' => 'Hometown', 'UsersAffil.is_deleted' => 0)));
        $countryID = 0;
        if (!empty($affil['Hometown']['id'])) {
            $this->request->data['Affil'] = $affil['Hometown'];
            $countryID = $this->request->data['Affil']['country_id'];
     
        }
        
        $AddressObject = ClassRegistry::init('Address');
        $countries_states = $AddressObject->setCountryStates('Address', $countryID);
        
        $this->set('countries', $countries_states['countries']);
        $this->set('states', $countries_states['states']);
        $this->set(compact('userID'));
    
    }
    
    /**
* 
* 
     * Select City by AJAX
     *
     * @author Oleg D.
     
*/
    function ajaxSelectCity() 
    {
        Configure::write('debug', '0');
        $userID = $this->getUserID();
        $affil = $this->UsersAffil->find('first', array('contain' => array('City'), 'conditions' => array('UsersAffil.user_id' => $userID, 'UsersAffil.model' => 'City', 'UsersAffil.is_deleted' => 0)));
        $countryID = 0;
        if (!empty($affil['City']['id'])) {
            $this->request->data['Affil'] = $affil['City'];
            $countryID = $this->request->data['Affil']['country_id'];
     
        }
        
        $AddressObject = ClassRegistry::init('Address');
        $countries_states = $AddressObject->setCountryStates('Address', $countryID);
        
        $this->set('countries', $countries_states['countries']);
        $this->set('states', $countries_states['states']);
        $this->set(compact('userID'));
    
    }
    
    /**
* 
* 
     * Select School by AJAX
     *
     * @author Oleg D.
     
*/
    function ajaxSelectSchool() 
    {
        Configure::write('debug', '0');
        $userID = $this->getUserID();
        $affil = $this->UsersAffil->find('first', array('contain' => array('School'), 'conditions' => array('UsersAffil.user_id' => $userID, 'UsersAffil.model' => 'School', 'UsersAffil.is_deleted' => 0)));
        
        $countryID = 0;
        if (!empty($affil['School']['id'])) {
            $this->request->data['Affil'] = $affil['School'];
            $countryID = $this->request->data['Affil']['country_id'];
            $this->request->data['Affil']['city'] = $this->UsersAffil->City->field('name', array('id' => $this->request->data['Affil']['city_id']));
     
        }

        $AddressObject = ClassRegistry::init('Address');
        $countries_states = $AddressObject->setCountryStates('Address', $countryID);
        
        $this->set('countries', $countries_states['countries']);
        $this->set('states', $countries_states['states']);
        $this->set(compact('userID'));        
    }
    
    /**
* 
* 
     * Select Greek by AJAX
     *
     * @author Oleg D.
     
*/
    function ajaxSelectGreek() 
    {
        Configure::write('debug', '0');
        $userID = $this->getUserID();
        $affil = $this->UsersAffil->find('first', array('contain' => array('Greek'), 'conditions' => array('UsersAffil.user_id' => $userID, 'UsersAffil.model' => 'Greek', 'UsersAffil.is_deleted' => 0)));
        $countryID = 0;
        if (!empty($affil['Greek']['id'])) {
            $this->request->data['Affil'] = $affil['Greek'];
     
        }
        $this->set(compact('userID'));
    
    }
                
    /**
* 
* 
     * Save City/Hometown
     *
     * @author Oleg D.
     
*/
    
    function save_city($model = 'City') 
    {
        Configure::write('debug', '0');
        
        $userID = $this->getUserID();        
        $name = trim(ucwords(strtolower($_REQUEST['name'])));
        $countryID = intval($_REQUEST['country_id']);
        $stateID = intval($_REQUEST['state_id']);
        
        if (!$name || !$countryID || !$stateID) {
            exit('error');
     
        }         
                    
        $city = $this->UsersAffil->City->find('first', array('conditions' => array('City.name' => $name, 'City.country_id' => $countryID, 'City.provincestate_id' => $stateID)));        
        if (!empty($city['City']['id'])) {
            $modelID = $city['City']['id'];        
        } else {
            $this->UsersAffil->City->create();
            $this->UsersAffil->City->save(array('name' => $name, 'shortname' => $name, 'country_id' => $countryID, 'provincestate_id' => $stateID));
            $modelID = $this->UsersAffil->City->getLastInsertID();            
        }
        $currentAffil = $this->UsersAffil->find('first', array('conditions' => array('model' => $model, 'user_id' => $userID,'is_deleted' => 0)));
        
        if (empty($currentAffil['UsersAffil']['id']) || $currentAffil['UsersAffil']['model_id'] != $modelID) {
            if (!empty($currentAffil['UsersAffil']['id'])) {
                $this->UsersAffil->delete($currentAffil['UsersAffil']['id']);
                $this->UsersAffil->City->updateAll(array('City.userscount' => 'City.userscount - 1'), array('City.id' => $currentAffil['UsersAffil']['model_id']));    
            }
            $newAffil = array('user_id' => $userID, 'model' => $model, 'model_id' => $modelID);
            $this->UsersAffil->create(); 
            $this->UsersAffil->save($newAffil);    
            $this->UsersAffil->City->updateAll(array('City.userscount' => 'City.userscount + 1'), array('City.id' => $modelID));    
        }
        exit('ok');    
    }
    
    /**
* 
* 
     * Save Greek
     *
     * @author Oleg D.
     
*/
    
    function save_greek() 
    {
        Configure::write('debug', '0');
        $model = 'Greek';
        $userID = $this->getUserID();        
        $name = trim(ucwords(strtolower($_REQUEST['name'])));
        $type = trim($_REQUEST['type']);
                
        if (!$name || !$type) {
            exit('error');
     
        }         
                    
        $greek = $this->UsersAffil->Greek->find('first', array('conditions' => array('Greek.name' => $name, 'Greek.type' => $type)));        
        if (!empty($greek['Greek']['id'])) {
            $modelID = $greek['Greek']['id'];        
        } else {
            $this->UsersAffil->Greek->create();
            $this->UsersAffil->Greek->save(array('name' => $name, 'shortname' => $name, 'type' => $type));
            $modelID = $this->UsersAffil->Greek->getLastInsertID();            
        }
        $currentAffil = $this->UsersAffil->find('first', array('conditions' => array('model' => $model, 'user_id' => $userID, 'is_deleted' => 0)));
        
        if (empty($currentAffil['UsersAffil']['id']) || $currentAffil['UsersAffil']['model_id'] != $modelID) {
            if (!empty($currentAffil['UsersAffil']['id'])) {
                $this->UsersAffil->delete($currentAffil['UsersAffil']['id']);
                $this->UsersAffil->Greek->updateAll(array('Greek.userscount' => 'Greek.userscount - 1'), array('Greek.id' => $currentAffil['UsersAffil']['model_id']));    
            }
            $newAffil = array('user_id' => $userID, 'model' => $model, 'model_id' => $modelID);
            $this->UsersAffil->create(); 
            $this->UsersAffil->save($newAffil);    
            $this->UsersAffil->Greek->updateAll(array('Greek.userscount' => 'Greek.userscount + 1'), array('Greek.id' => $modelID));    
        }
        exit('ok');    
    }
    
    /**
* 
* 
     * Save School
     *
     * @author Oleg D.
     
*/
    
    function save_school() 
    {
        Configure::write('debug', '0');
        $model = 'School';
        $userID = $this->getUserID();        
        $name = trim(ucwords(strtolower($_REQUEST['name'])));
        $countryID = intval($_REQUEST['country_id']);
        $stateID = intval($_REQUEST['state_id']);
        $city = trim(ucwords(strtolower($_REQUEST['city'])));
                
        if (!$name || !$countryID || !$name || !$city) {
            exit('error');
     
        }         
        
        $city = $this->UsersAffil->City->find('first', array('conditions' => array('City.name' => $city, 'City.country_id' => $countryID, 'City.provincestate_id' => $stateID)));        
        if (!empty($city['City']['id'])) {
            $cityID = $city['City']['id'];        
        } else {
            $this->UsersAffil->City->create();
            $this->UsersAffil->City->save(array('name' => $city, 'shortname' => $city, 'country_id' => $countryID, 'provincestate_id' => $stateID));
            $cityID = $this->UsersAffil->City->getLastInsertID();            
        }        
                    
        $school = $this->UsersAffil->School->find('first', array('conditions' => array('School.name' => $name, 'School.country_id' => $countryID, 'School.provincestate_id' => $stateID, 'School.city_id' => $cityID)));        
        if (!empty($school['School']['id'])) {
            $modelID = $school['School']['id'];        
        } else {
            $this->UsersAffil->School->create();
            $this->UsersAffil->School->save(array('name' => $name, 'country_id' => $countryID, 'provincestate_id' => $stateID, 'city_id' => $cityID));
            $modelID = $this->UsersAffil->School->getLastInsertID();            
        }
        $currentAffil = $this->UsersAffil->find('first', array('conditions' => array('model' => $model, 'user_id' => $userID, 'is_deleted' => 0)));
        
        if (empty($currentAffil['UsersAffil']['id']) || $currentAffil['UsersAffil']['model_id'] != $modelID) {
            if (!empty($currentAffil['UsersAffil']['id'])) {
                $this->UsersAffil->delete($currentAffil['UsersAffil']['id']);
                $this->UsersAffil->School->updateAll(array('School.userscount' => 'School.userscount - 1'), array('School.id' => $currentAffil['UsersAffil']['model_id']));    
            }
            $newAffil = array('user_id' => $userID, 'model' => $model, 'model_id' => $modelID);
            $this->UsersAffil->create(); 
            $this->UsersAffil->save($newAffil);    
            $this->UsersAffil->School->updateAll(array('School.userscount' => 'School.userscount + 1'), array('School.id' => $modelID));    
        }
        exit('ok');    
    }
        
    
    function usersProfileBlock($userID) 
    {
        $this->layout = false;
        $myUserID = $this->getUserID();
        if ($userID == $myUserID) {
            $myProfile = 1;        
        } else {
            $myProfile = 0;
     
        }
        
        // Hometown block
        $this->UsersAffil->belongsTo['Hometown']['conditions'] = array();                    
        $hometown = $this->UsersAffil->find('first', array('contain' => array('Hometown' => array('Country', 'Provincestate')), 'conditions' => array('UsersAffil.model' => 'Hometown', 'UsersAffil.user_id' => $userID, 'UsersAffil.is_deleted' => 0)));
                
        if (empty($hometown['Hometown']['id'])) {
            $hometown = '';
     
        } else {
              $hometown = $hometown['Hometown']['name'] . ', ' . $hometown['Hometown']['Provincestate']['shortname'];            
        }
        // EOF Hometown block
        // City block		
        $this->UsersAffil->belongsTo['City']['conditions'] = array();    
        $city = $this->UsersAffil->find('first', array('contain' => array('City' => array('Country', 'Provincestate')), 'conditions' => array('UsersAffil.model' => 'City', 'UsersAffil.user_id' => $userID, 'UsersAffil.is_deleted' => 0)));
        
        if (empty($city['City']['id'])) {
            $city = '';
     
        } else {
              $city = $city['City']['name'] . ', ' . $city['City']['Provincestate']['shortname'];            
        }    
        // EOF City block
        
        // School block		
        $this->UsersAffil->belongsTo['School']['conditions'] = array();    
        $school = $this->UsersAffil->find('first', array('contain' => array('School' => array('Country', 'Provincestate')), 'conditions' => array('UsersAffil.model' => 'School', 'UsersAffil.user_id' => $userID, 'UsersAffil.is_deleted' => 0)));
        
        if (empty($school['School']['id'])) {
            $school = '';
     
        } else {
              $school = $school['School']['name'] . ', ' . $school['School']['Provincestate']['shortname'];            
        }    
        // EOF School block
        
        // Greek block		
        $this->UsersAffil->belongsTo['Greek']['conditions'] = array();    
        $greek = $this->UsersAffil->find('first', array('contain' => 'Greek', 'conditions' => array('UsersAffil.model' => 'Greek', 'UsersAffil.user_id' => $userID, 'UsersAffil.is_deleted' => 0)));

        if (empty($greek['Greek']['id'])) {
            $greek = '';
     
        } else {
              $greek = $greek['Greek']['name'];            
        }    
        // EOF School block			
        $this->set(compact('hometown', 'city', 'school', 'greek', 'myProfile'));
        $this->render();
            
    }
    function ajax_affils_users($modelName, $modelID) 
    {
        $Rankinghistory = ClassRegistry::init('Rankinghistory');
        $mostRecentHistory = $Rankinghistory->getLatestHistory(); 
        
        $paginate = array(
        'limit' => 15,
        'fields' => array('User.id', 'User.lgn', 'User.total_wins', 'User.total_losses', 'User.total_cupdif', 'Ranking.rank'),
        'conditions' => array('UsersAffil.model' => $modelName, 'UsersAffil.model_id' => $modelID), 
        'order' => array('Ranking.rank' => 'ASC'),
        'joins' => array(
        array('table' => 'users', 'alias' => 'User', 'type' => 'LEFT', 'conditions' => array('User.id = UsersAffil.user_id')),
        array('table' => 'rankings', 'alias' => 'Ranking', 'type' => 'LEFT', 'conditions' => array('Ranking.model_id = User.id', 'Ranking.model' => 'User', 'Ranking.history_id' => $mostRecentHistory['Rankinghistory']['id']))
        ));
        
        $this->paginate = array('UsersAffil' => $paginate);
        $users = $this->paginate('UsersAffil');
        $this->set(compact('users'));    
        $this->render();    
    }
      
}
?>
