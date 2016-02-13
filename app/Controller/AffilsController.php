<?php
class AffilsController extends AppController
{

    var $name    = 'Affils';
    var $uses = array('UsersAffil','Organization','OrganizationsUser','Affil','City','Provincestate');
    var $components = array(
       'Session','Access','Json','RequestHandler','Cookie','Logger','Encryption' 
    );
    function testDecrypt($code) 
    {
        return $this->Encryption->aes_decrypt($code);
    }
    //This is only really meant to be used when the database is small
    function updateUsercountForAllAfills($model) 
    {
        if (!$this->isUserSuperAdmin()) { return 'Access Denied.'; 
        }
        $type = $model;
        
        if ($model == 'School') {
            $table = 'schools'; 
        }  
        elseif ($model == 'City') {
            $table = 'cities';
            $type = array('City','Hometown');
        }
        elseif ($model == 'Greek')
            $table = 'greeks';
        else {
            return false; 
        }
        //First, see if there are any users_affils for users that no longer exist
        $usersAffils = $this->UsersAffil->find(
            'all',
            array('conditions'=>array('UsersAffil.is_deleted'=>0),'contain'=>array('User'))
        );
        foreach ($usersAffils as $checkUsersAffil) {
            if (!$checkUsersAffil['User']['id']) {
                $checkUsersAffil['UsersAffil']['is_deleted'] = 1;
                $this->UsersAffil->save($checkUsersAffil['UsersAffil']);
            }
        }
        
        $Model = $this->UsersAffil->getModel($model);
        $Model->recursive = -1;
        $modelsToZero = $Model->find('all', array('conditions'=>array('userscount >'=>0)));
        foreach ($modelsToZero as $modelToZero) {
            $modelToZero[$model]['userscount'] = 0;
            $Model->save($modelToZero[$model]);
        }
        
        $this->UsersAffil->recursive = -1;
        $usersAffils = $this->UsersAffil->find(
            'all', array('conditions'=>array(
            'model'=>$type,
            'is_deleted'=>0))
        );
        $affilsToCheck = array();
        // return $usersAffils;
        foreach ($usersAffils as $usersAffil) {
            $affilsToCheck[$usersAffil['UsersAffil']['model_id']] = $usersAffil['UsersAffil']['model_id'];   
        }
        //return $affilsToCheck;
        foreach ($affilsToCheck as $affil_id) {
            $this->UsersAffil->updateUserCountForAffil($model, $affil_id);
        }
        return 'ok';
    }
    
    function matchAffilTypeAndModel($type,$model) 
    {
        switch ($type) {
        case 'Greek':
            return ($model == $type); break;
        case 'School': 
            return ($model == $type); break;   
        case 'Hometown':
            return ($model == 'City'); break;
        case 'City':
            return ($model == 'City'); break; 
        default: 
            return false; break;
        }
    }
    function m_getPlayerRankWithinAffil($model=null,$model_id=null,$user_id=null,$amf = 0) 
    {
        if (isset($this->request->params['form']['model'])) {
            $model= $this->request->params['form']['model']; 
        }
        if (isset($this->request->params['form']['model_id'])) {
            $model_id= $this->request->params['form']['model_id']; 
        }
        if (isset($this->request->params['form']['user_id'])) {
            $user_id = $this->request->params['form']['user_id']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }
        if (!$model || !$model_id) {
            return $this->returnMobileResult('bad parameters', $amf); 
        }
        if ($model == 'City') {
            $model = array('Hometown','City'); 
        } 
        
        //First, get the User and UserAffilobject, so you see his rating
        $user = $this->UsersAffil->find(
            'first', array('conditions'=>array(
            'UsersAffil.user_id'=>$user_id,
            'UsersAffil.model'=>$model,
            'UsersAffil.model_id'=>$model_id),
            'UsersAffil.is_deleted'=>0,
            'contain'=>array('User'))
        );
        if (!$user) {
            return $this->returnMobileResult('User not found, or is not affiliated.', $amf);
        }
        $userRating = $user['User']['rating'];
        if (is_array($model)) {
            $numPlayersBetter = $this->UsersAffil->find(
                'all', array(
                'conditions'=>array(
                    'UsersAffil.model'=>$model,
                    'UsersAffil.model_id'=>$model_id,
                    'UsersAffil.is_deleted'=>0,
                    'User.rating >'=> $userRating
                    ),
                'contain'=>array('User'),
                'fields'=>array('User.id','User.rating')
                )
            );
            $numPlayersBetter = Set::extract($numPlayersBetter, '{n}.User.id');
            $numPlayersBetter = count(array_unique($numPlayersBetter));
            
            $this->UsersAffil->recursive = -1;
            $numPlayersTotal = $this->UsersAffil->find(
                'all', array(
                'conditions'=>array(
                    'UsersAffil.model'=>$model,
                    'UsersAffil.model_id'=>$model_id,
                    'UsersAffil.is_deleted'=>0))
            );
            $numPlayersTotal = Set::extract($numPlayersTotal, '{n}.UsersAffil.user_id');
            $numPlayersTotal = count(array_unique($numPlayersTotal));
        }
        else {
            $numPlayersBetter = $this->UsersAffil->find(
                'count', array(
                'conditions'=>array(
                    'UsersAffil.model'=>$model,
                    'UsersAffil.model_id'=>$model_id,
                    'UsersAffil.is_deleted'=>0,
                    'User.rating >'=>$userRating
                    ),
                'contain'=>array('User'),
                'fields'=>array('User.id'))
            );
                $this->UsersAffil->recursive = -1;
                $numPlayersTotal = $this->UsersAffil->find(
                    'count', array(
                    'conditions'=>array(
                    'UsersAffil.model'=>$model,
                    'UsersAffil.model_id'=>$model_id,
                    'UsersAffil.is_deleted'=>0))
                );
        }        
         // So now, we have the rank and the total. If the rank is 35, we want to return users 31-40.
         // Rounddown( X / 10) * 10 + 1 
        $start = 10 * floor($numPlayersBetter/10) + 1;          
        $closePlayers = $this->getAffilsLeaderBoard($model, $model_id, $start, 10, $amf);
        
        return $this->returnMobileResult(
            array('Rank'=>$numPlayersBetter+1,'Totalplayers'=>$numPlayersTotal,
            'StartRank'=>$start,'Leaderboard'=>$closePlayers), $amf
        );
    }
    
    function m_getAffilsLeaderBoard($model = null,$model_id = null,$start = 0,$limit = 25,$amf = 0) 
    {
        if (isset($this->request->params['form']['model'])) {
            $model= $this->request->params['form']['model']; 
        }
        if (isset($this->request->params['form']['model_id'])) {
            $model_id= $this->request->params['form']['model_id']; 
        }
        if (isset($this->request->params['form']['start'])) {
            $start = $this->request->params['form']['start']; 
        }
        if (isset($this->request->params['form']['limit'])) {
            $limit= $this->request->params['form']['limit']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }
        return $this->getAffilsLeaderBoard($model, $model_id, $start, $limit);
    }
    function getAffilsLeaderBoard($model = null,$model_id = null,$start = 0,$limit = 25,$amf = 0) 
    {
        if (isset($this->request->params['form']['model'])) {
            $model= $this->request->params['form']['model']; 
        }
        if (isset($this->request->params['form']['model_id'])) {
            $model_id= $this->request->params['form']['model_id']; 
        }
        if (isset($this->request->params['form']['start'])) {
            $start = $this->request->params['form']['start']; 
        }
        if (isset($this->request->params['form']['limit'])) {
            $limit = $this->request->params['form']['limit']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }

        if (!$model || !$model_id) {
            return $this->returnMobileResult('bad parameters', $amf); 
        }
        if ($model == 'City') {
            $model = array('City','Hometown');
        }
        //we expect the input to start at 1, so adjust
        $limitStart = $start - 1;
        $userAffils = $this->UsersAffil->find(
            'all', array(
            'conditions'=>array(
                'UsersAffil.model'=>$model,
                'UsersAffil.model_id'=>$model_id,
                'UsersAffil.is_deleted'=>0,
                ),
            'contain'=>array('User'),
            'order'=>array('User.rating'=>'DESC','User.id'=>'ASC'),
            'limit'=>$limitStart.','.$limit)
        );

            $users = Set::extract($userAffils, '{n}.User');
            $currentID = 0;
            $ctr = 0;
            //there could be duplicate users (thats why we sort by User.id)
            $results = array();
        foreach ($users as &$user) {
            if (!$user['id'] || $user['id'] == $currentID) {
                unset($users[$ctr]);
                $ctr--;          
            }       
            else {
                $results[$ctr] = $user;
                $currentID = $user['id'];
            }
            $ctr++;
        }
            return $this->returnMobileResult($results, $amf);
    }
    /*
    $model = array('Greek','School','City')
    */
    function m_getTopAffilsByType($model = null,$limit = 25,$amf = 0) 
    {
        if (isset($this->request->params['form']['model'])) {
            $model= $this->request->params['form']['model']; 
        }
        if (isset($this->request->params['form']['limit'])) {
            $limit= $this->request->params['form']['limit']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }
        //return 2;
        $Model = $this->UsersAffil->getModel($model);
        if (!$Model) {
            return $this->returnMobileResult('bad model', $amf); 
        }
        $findArray['limit'] = $limit;
        if ($model == 'City') {
            $findArray['order'] = array('City.points'=>'DESC'); 
        }
        else {
            $findArray['order'] = array('points'=>'DESC'); 
        }
        $findArray['conditions'] = array();
        if ($model == 'City') {
            $findArray['contain'] = array('Provincestate','Country');
        }
        $Model->recursive = -1;
        //        return $findArray;
        $results = $Model->find('all', $findArray);
        //If this is a City, make the results smaller by carving out
        //the Country and State
        if ($model == 'City') {
            foreach ($results as &$result) {
                $result['City']['country_name'] = $result['Country']['name'];
                $result['City']['country_shortname'] = $result['Country']['shortname'];
                $result['City']['state_name'] = $result['Provincestate']['name'];
                $result['City']['state_shortname'] = $result['Provincestate']['shortname'];
                unset($result['Country']);
                unset($result['Provincestate']); 
            }
        }
        
        return $this->returnMobileResult($results, $amf);
    }
    
    /** 
    Returns one object for each type of Affiliation. 
    also be multiple Organization Affiliations.
      */
      
    function m_getAffilsByUser($userid, $amf = 0) 
    {
        if (isset($this->request->params['form']['userid'])) {
            $userid = $this->request->params['form']['userid']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }
        $usersAffils = $this->UsersAffil->find(
            'all', array('conditions'=>array(
                'user_id'=>$userid,
                'is_deleted'=>0),
            'contain'=>array('School','Greek','City','Hometown'))
        ); 
             
        foreach ($usersAffils as &$usersAffil) {
            if ($usersAffil['UsersAffil']['model'] != 'School') {
                unset($usersAffil['School']); 
            }
            if ($usersAffil['UsersAffil']['model'] != 'Greek') {
                unset($usersAffil['Greek']); 
            }
            if ($usersAffil['UsersAffil']['model'] != 'Hometown') {
                unset($usersAffil['Hometown']); 
            }
            if ($usersAffil['UsersAffil']['model'] != 'City') {
                unset($usersAffil['City']); 
            }
        }                                                              
            

        //Now get Organization Affiliation
        
        $orgAffils = $this->OrganizationsUser->find(
            'all', array('conditions'=>array(
            'OrganizationsUser.user_id'=>$userid,'OrganizationsUser.status'=>'accepted'),
            'contain'=>array('Organization'))
        );
        $result = array_merge($usersAffils, $orgAffils);        
        //$result = array('temp'=>array($orgAffils,$usersAffils));
        //fill this in            
        return $this->returnMobileResult($result, $amf);   
    }                                                                  

    /**
    This sends back each UsersAffil. Thus, there could be two of the same user if this is a city (if current=hometown)
    */
    function m_getUsersByAffil($model,$modelid,$amf = 0) 
    {
        if (isset($this->request->params['form']['model'])) {
            $model= $this->request->params['form']['model']; 
        }
        if (isset($this->request->params['form']['modelid'])) {
            $modelid = $this->request->params['form']['modelid']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }
        $result = $this->UsersAffil->getUsersFromAffil($model, $modelid);
        return $this->returnMobileResult($result, $amf);
    }
    /*
    This just returns the Affil. If you want more info, use m_viewAffil
    */
    function m_getAffil($model,$modelid,$amf = 0) 
    {
        if (isset($this->request->params['form']['model'])) {
            $model = $this->request->params['form']['model']; 
        }
        if (isset($this->request->params['form']['modelid'])) {
            $modelid = $this->request->params['form']['modelid']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }
        $Model = $this->UsersAffil->getModel($model);
        if (!$Model) {
            return 'sdf'; 
        }
                               
        $Model->recursive = -1;
        $result = $Model->find('first', array('conditions'=>array('id'=>$modelid)));
        return $this->returnMobileResult($result, $amf);
    }
    /*
    * This contains:
    * 1) # Players Affiliated
    * 2) Leaderboard
    * 
    */
    
    function m_getAffilWithDetails($model=null,$modelid=null,$amf = 0) 
    {
        if (isset($this->request->params['form']['model'])) {
            $model = $this->request->params['form']['model']; 
        }
        if (isset($this->request->params['form']['modelid'])) {
            $modelid = $this->request->params['form']['modelid']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }
        $Model = $this->UsersAffil->getModel($model);
        if (!$Model) {
            return 'sdf'; 
        }
                                                    
        $Model->recursive = -1;
        $result = $Model->find('first', array('conditions'=>array('id'=>$modelid)));
        $result['Leaderboard'] = $this->getAffilsLeaderBoard($model, $modelid, 1, 10);
        //Get the # players affiliated
        $this->UsersAffil->recursive = -1;
        $type = $model;
        if ($type == 'City') {
            $type = array('City','Hometown');
        }
        $userAffils = $this->UsersAffil->find(
            'all', array(
            'conditions'=>array('is_deleted'=>0,
                                'model'=>$type,
                                'model_id'=>$modelid))
        );
        $userids = Set::extract($userAffils, '{n}.UsersAffil.user_id');
        $distinctIDs = $this->custom_array_unique($userids);
        $result['Numplayers'] = count($distinctIDs);     
        // If this is a school, get the city/state
        if ($model == 'School') {
            if ($result['School']['city_id'] > 0) {
                $city = $this->City->find(
                    'first', array('conditions'=>array('City.id'=>$result['School']['city_id']),
                    'contain'=>array('Provincestate'))
                );
                $result['School']['city'] = $city['City']['name'];
                $result['School']['state'] = $city['Provincestate']['name'];                
            }
            else {
                $result['School']['city'] = "";
                $result['School']['state'] = "";
            }                                   
        }
        if ($model == 'City') {
            if ($result['City']['provincestate_id'] > 0) {
                $this->Provincestate->recursive = -1;
                $state = $this->Provincestate->find(
                    'first', array(
                    'conditions'=>array('Provincestate.id'=>$result['City']['provincestate_id']))
                );
                $result['City']['state'] = $state['Provincestate']['name'];
            }
            else {
                $result['City']['state'] = "";
            }
        }
        
        return $this->returnMobileResult($result, $amf);
    } 
    /*
    * This contains most leaderboard
    */
    function m_getAffilWithLeaderboard($model,$modelid,$amf = 0) 
    {
        if (isset($this->request->params['form']['model'])) {
            $model = $this->request->params['form']['model']; 
        }
        if (isset($this->request->params['form']['modelid'])) {
            $modelid = $this->request->params['form']['modelid']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }
        $Model = $this->UsersAffil->getModel($model);
        if (!$Model) {
            return 'sdf'; 
        }
                               
        $Model->recursive = -1;
        $result = $Model->find('first', array('conditions'=>array('id'=>$modelid)));
        $result['Leaderboard'] = $this->getAffilsLeaderBoard($model, $modelid, 1, 10);
        return $this->returnMobileResult($result, $amf);
    }
    /**
*   This sets the affiliation for the logged user
*    Type is in {'Greek','School','Hometown','Current','Organization'} 
*   modelid is, for instance Greek.id or City.id
*/
    function m_setAffil($model = null,$modelid = null,$amf = 0)  
    {
        if (isset($this->request->params['form']['model'])) {
            $model = $this->request->params['form']['model']; 
        }   
        if (isset($this->request->params['form']['modelid'])) {
            $modelid= $this->request->params['form']['modelid']; 
        }            
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }
        $result = $this->setAffil($model, $modelid);
        return $this->returnMobileResult($result, $amf);
    }            
    /**
    * This unsets the desired Affiliation for the logged user. 
    */
    function m_unsetAffil($model = null,$amf = 0) 
    {
        if (isset($this->request->params['form']['model'])) {
            $model = $this->request->params['form']['model']; 
        }               
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }        
        //Deal with Orgs?
        $Model = $this->UsersAffil->getModel($model);
        if (!$Model) {
            return $this->returnMobileResult('Bad Model', $amf);
        }
        $userid = $this->getUserID();
        if ($userid < 2) {
            return $this->returnMobileResult('You are not logged in', $amf);
        }
        $time_now = date("Y-m-d H:i:s", time()); 
        // Does the user already have this type of Affiliation? If so, remove that affiliation,
        // and update that affiliations usercount
        $matchingUsersAffils = $this->UsersAffil->find(
            'all', array('conditions'=>array(
                'user_id'=>$userid,
                'model'=>$model,
                'is_deleted'=>0))
        );                 
        foreach ($matchingUsersAffils as $matchingUsersAffil) { //this should only be one at most of course...
            $matchingUsersAffil['UsersAffil']['is_deleted'] = 1;
            $matchingUsersAffil['UsersAffil']['deleted'] = $time_now;
            if (!$this->UsersAffil->save($matchingUsersAffil)) {
                return 'problem'; 
            }
            if (!$this->UsersAffil->updateUserCountForAffil($model, $matchingUsersAffil['UsersAffil']['model_id'])) {
                return 'problem'; 
            }
        }
        return $this->returnMobileResult('ok', $amf);
    }
    
    private function setAffil($model,$modelid) 
    {
        if ($model == 'Organization') {
            return $this->setAffilOrg($modelid);            
        }                           
        //First check to make sure the Affil exists
        $Model = $this->UsersAffil->getModel($model);
        if (!$Model) {
            return 'Bad Model';
        }
        $Model->recursive = -1;
        $checkModel = $Model->find('first', array('conditions'=>array('id'=>$modelid)));
        if (!$checkModel) {
            return "Affil does not exist";
        }
        $time_now = date("Y-m-d H:i:s", time());  
        $user = $this->Session->read('loggedUser');  
        if (!$user || $user['id'] < 2) { 
            return 'You are not logged in'; 
        }
        $userid = $user['id'];
        $this->UsersAffil->recursive = -1; 
     
        //Is this user already associated with this affil in the same type? If so, we're done...
        $matchingUsersAffils = $this->UsersAffil->find(
            'first', array('conditions'=>array(
            'user_id'=>$userid,
            'is_deleted'=>0,
            'model'=>$model,
            'model_id'=>$modelid))
        );
        if ($matchingUsersAffils) {
            return 'ok'; 
        }   
        unset($matchingUsersAffils); 
            
        // Does the user already have this type of Affiliation? If so, remove that affiliation,
        // and update that affiliations usercount
        $matchingUsersAffils = $this->UsersAffil->find(
            'all', array('conditions'=>array(
                'user_id'=>$userid,
                'model'=>$model,
                'is_deleted'=>0))
        );                 
        foreach ($matchingUsersAffils as $matchingUsersAffil) { //this should only be one at most of course...
            $matchingUsersAffil['UsersAffil']['is_deleted'] = 1;
            $matchingUsersAffil['UsersAffil']['deleted'] = $time_now;
            if (!$this->UsersAffil->save($matchingUsersAffil)) {
                return 'problem'; 
            }
            if (!$this->UsersAffil->updateUserCountForAffil($model, $matchingUsersAffil['UsersAffil']['model_id'])) {
                return 'problem'; 
            }
        }
              
        //Create the new User_Affil object
        $newUsersAffil['user_id']  = $userid;
        $newUsersAffil['model_id'] = $modelid;
        $newUsersAffil['model'] = $model;
        $this->UsersAffil->create();
        $this->UsersAffil->save($newUsersAffil);
           $this->UsersAffil->updateUserCountForAffil($model, $modelid);
     
        return 'ok'; 
    }
    private function m_unsetAffilOrg($orgID = null,$amf = 0) 
    {
        if (isset($this->request->params['form']['orgID'])) {
            $orgID = $this->request->params['form']['orgID']; 
        }               
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        } 
            
        $userID = $this->getUserID();
        if ($userID < 2) { return 'You are not logged in.'; 
        } 
        //Get the record from OrganizationsUser
        $this->OrganizationsUser->recursive = -1;
        $orgsUsers = $this->OrganizationsUser->find(
            'all', array(
            'conditions'=>array(
            'user_id'=>$userID,
            'organization_id'=>$orgID,
            'role <>'=>'Creator',
            'status <>'=>'Deleted'))
        );
            
        foreach ($orgsUsers as $orgUser) {
            $orgsUser['OrganizationsUser']['status'] = 'Deleted';
            $this->OrganizationsUser->save($orgsUser['OrganizationsUser']);
        }
        return 'ok';
    }
 
    private function setAffilOrg($orgID)  
    {
        $userID = $this->getUserID();
        if ($userID < 2) { return 'You are not logged in.'; 
        }

        $organization = $this->Organization->find(
            'first',
            array(
                'conditions'=>array('Organization.id'=>$orgID,'Organization.is_deleted'=>0),
                'contain'=>array('Creator'))
        );        
                                     
        if (empty($organization)) {
            return 'Organization does not exist';
        }                

        $orgUser = $this->OrganizationsUser->getOrgUser($organization['Organization']['id']);
        $this->set('orgUser',  $orgUser);
       
        if (!empty($orgUser['id'])) {
            if ($orgUser['status'] == 'invited') {
                //Need to accept the invitation
                if ($this->accept_org_invitation($orgID)) {
                    return 'ok'; 
                }
                else {
                    return 'problem'; 
                }                
            }
            else { //already assigned, so we're good
                return 'ok'; 
            }
        }        
        
        $this->OrganizationsUser->create();    
        $this->OrganizationsUser->save(
            array('organization_id' => $organization['Organization']['id'], 
            'user_id' => $userID, 
            'role' => 'member', 
            'status' => 'pending')
        );
        $this->sendMailMessage(
            'OrganizationMemberJoinRequest', array(    
                 '{FNAME}'     => $organization['Creator']['firstname'],
                 '{LNAME}'     => $organization['Creator']['lastname'],
                 '{ORG_NAME}'     => $organization['Organization']['name'],        
                 '{MEMBERS_LINK}'      => MAIN_SERVER . '/o_members/' . $organization['Organization']['slug']),
            $organization['Creator']['email']
        );                
        return 'user conditionally assigned to organization';
    }
    
    private function accept_org_invitation($id) 
    {
        
        $organizationsUser = $this->OrganizationsUser->find(
            'first', 
            array(
                'conditions' => 
                    array('OrganizationsUser.id' => $id, 
                        'OrganizationsUser.user_id' => $this->getUserID()), 
                        'contain' => array('Organization', 'User'))
        );
            
        if (!empty($organizationsUser) && $organizationsUser['OrganizationsUser']['status'] == 'invited') {
            $this->OrganizationsUser->save(array('id' => $id, 'status' => 'accepted'));    
            $result = "ok";
        } else {
            $result = "problem";
        }
        $this->OrganizationsUser->recalculateMembersCount($organizationsUser['OrganizationsUser']['organization_id']);    
        return $result;
    }

}
