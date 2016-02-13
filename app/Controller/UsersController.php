<?php
class UsersController extends AppController
{
    public $name    = 'Users';
    public $helpers = array('Html', 'Form');
    public $uses    = array('User', 'Group','Status','Country','Provincestate','Address','Phone', 'Ranking','Rankinghistory','UsersAffil', 'UsersStatus', 'Album', 'Submission', 'Link', 'UserHistory', 'Game');

    function myprofile() 
    {
        if ($this->getUserID() == 1) { //If this is a visitor, redirect to login
            $this->Access->checkAccess('User', 'u');
        }
        $lgn = $this->getUserLogin();    
        $this->redirect('/u/'.$lgn);
    }
   
    function testUpdateUserStats($startID,$endID) 
    {
        return $this->returnJSONResult($this->User->updateStatsForUsers($startID, $endID));
    }
    function merge_two_users_stats() 
    {
        if (!$this->isUserSuperAdmin()) {
            $this->Session->setFlash('Access Denied.', 'flash_error');
            $this->redirect('/pages/update_stats');
        }
        if ($submit == 1) {
            $this->User->recursive = -1;
            $user_to_move_from_form_data = $this->request->data['User']['user_to_move_from'];
            $user_to_move_from = $this->User->find(
                'first', array('conditions'=>array('OR'=>array(
                'lgn'=>$user_to_move_from_form_data,
                'email'=>$user_to_move_from_form_data,
                'id'=>$user_to_move_from_form_data)))
            );
            
            $user_to_move_to_form_data = $this->request->data['User']['user_to_move_to'];
            $user_to_move_to = $this->User->find(
                'first', array('conditions'=>array('OR'=>array(
                'lgn'=>$user_to_move_to_form_data,
                'email'=>$user_to_move_to_form_data,
                'id'=>$user_to_move_to_form_data)))
            );
                
            if (!$user_to_move_from || !$user_to_move_to) {
                $this->Session->setFlash('One of the users was not found', 'flash_error');
            }
            $userIDToMoveFrom = $user_to_move_from['User']['id'];
            $userIDToMoveTo = $user_to_move_to['User']['id'];
            $Teammate = ClassRegistry::init('Teammate');
            $Teammate->recursive = -1;
            $moveFromTeammates = $Teammate->find(
                'all', array('conditions'=>array(
                'user_id'=>$userIDToMoveFrom,
                'status'=>array('Creator','Pending','Accepted')))
            );
            $moveToTeammates = $Teammate->find(
                'all', array('conditions'=>array(
                'user_id'=>$userIDToMoveTo,
                'status'=>array('Creator','Pending','Approved')))
            );
            //we don't want the teammate to be on the team twice
            foreach ($moveFromTeammates as $moveFromTeammate) {
                $teammateAlreadyExists = false;
                $moveFromTeamID = $moveFromTeammate['Teammate']['team_id'];
                foreach ($moveToTeammates as $moveToTeammate) {
                    if ($moveToTeammate['Teammate']['team_id'] == $moveFromTeamID) {
                        $teammateAlreadyExists = true; 
                    } 
                }
                if ($teammateAlreadyExists) {
                    $moveFromTeammate['Teammate']['status'] = 'Deleted';
                    $Teammate->save($moveFromTeammate);
                }
                else {
                    $moveFromTeammate['Teammate']['user_id'] = $userIDToMoveTo;
                    $Teammate->save($moveFromTeammate);
                }
            }   
            $this->checkUserForDuplicateTeams($userIDToMoveTo);   
            
            /*
            * Now, lets handle ratings
            */
            $Ratinghistory = ClassRegistry::init('Ratinghistory');
            
            $ratingHistories = $Ratinghistory->find(
                'all', array(
                'conditions'=>array(
                    'model'=>'User',
                    'user_id'=>array($userIDToMoveFrom,$userIDToMoveTo)),
                'order'=>array('Ratinghistory.id'=>'ASC'),
                'contain'=>array('Game'=>array('Team1','Team2','Ratinghistory')))
            );
            if ($ratingHistories) {
                $before = $ratingHistories[0]['Ratinghistory']['before'];
            }
            foreach ($ratingHistories as $ratingHistory) {
                $currentTeamID = $ratingHistory['Ratinghistory']['team_id'];
                $opponentsTotalRating = 0;
                $opponentsCount = 0;
                $allRatingHistoriesInGame = $ratingHistory['Game']['Ratinghistory'];
                foreach ($allRatingHistoriesInGame as $oneRatingHistoryInGame) {
                    if ($oneRatingHistoryInGame['team_id'] != $currentTeamID) {
                        $opponentsTotalRating += $oneRatingHistoryInGame['before'];
                        $opponentsCount++;
                    }
                }
                if ($opponentsCount > 0) {
                    $opponentsAverageRating = $opponentsTotalRating / $opponentsCount;
                }
                else {
                    $opponentsAverageRating = INITIAL_PLAYER_RATING;
                }
                
                $cupdif = $Ratinghistory->getEffectiveCupDif($ratingHistory['Game']);
                if ($ratingHistory['Game']['winningteam_id'] == $currentTeamID) {
                    $playerRatingChange = $Ratinghistory->getRatingChange(
                        $before, $opponentsAverageRating, $cupdif
                    );
                    $after = $before + ($ratingHistory['Ratinghistory']['weight'] * $playerRatingChange);
                }
                else {
                    $playerRatingChange = $Ratinghistory->getRatingChange(
                        $opponentsAverageRating, $before, $cupdif
                    );
                    $after = $before - ($ratingHistory['Ratinghistory']['weight'] * $playerRatingChange);
                }
                //return $this->returnJSONResult($ratingHistory['Ratinghistory']['after']);
                $arrayToSave = array(
                    'id'=>$ratingHistory['Ratinghistory']['id'],
                    'before'=>$before,
                    'after'=>$after,
                    'user_id'=>$userIDToMoveTo,
                    'adjustedretro'=>1);
                $Ratinghistory->save($arrayToSave);
                $before = $after;
            }
 
            $this->User->setUserRating($userIDToMoveFrom, 0);
            $this->User->setUserRating($userIDToMoveTo, $after);
            $this->User->updateStatsForUser($userIDToMoveFrom);
            $this->User->updateStatsForUser($userIDToMoveTo);
            $this->User->updateTeamRatings($userIDToMoveTo);    
            $this->Session->setFlash('Users Merged', 'flash_success');
            $this->redirect('/users/merge_two_users_stats');
        }    
    }
    /**
   * This function looks at the users teams, and sees if there are any duplicates, i.e.
   * teams that are complete and have the same # of users
   */
    function checkUserForDuplicateTeams($userID) 
    {
        Configure::write('debug', 0);
        if (!$this->isUserSuperAdmin()) {
            return 'Access Denied'; 
        }
        //First get teamids
        $Teammate = ClassRegistry::init('Teammate');
        $Teammate->recursive = -1;
        $teammatesForUser = $Teammate->find(
            'all', array('conditions'=>array(
            'user_id'=>$userID,
            'status'=>array('Creator','Accepted','Pending')))
        );
        $teamids = Set::extract($teammatesForUser, '{n}.Teammate.team_id');
        $teammates = $Teammate->find(
            'all', array('conditions'=>array(
            'user_id <>'=>$userID,
            'team_id'=>$teamids,
            'status'=>array('Creator','Accepted','Pending')))
        );
                               
        $arrayByTeamID = Set::combine(
            $teammates, '{n}.Teammate.user_id', '{n}.Teammate.user_id',
            '{n}.Teammate.team_id'
        );
        $count = 0;
        foreach ($arrayByTeamID as $teamid1=>$currentTeam1) {
            foreach ($arrayByTeamID as $teamid2=>$currentTeam2) {  
                if (count($currentTeam1)==count($currentTeam2) && $teamid1 != $teamid2) {
                    $teamsAreTheSame = true;
                    foreach ($currentTeam1 as $testUserID) {
                        if (!isset($currentTeam2[$testUserID])) {
                            $teamsAreTheSame = false; 
                        }
                    }
                    if ($teamsAreTheSame) {
                        //We have the teams. Now we need to check to see that 
                        //Team.people_in_team=count(users)
                        $Team = ClassRegistry::init('Team');
                        $teamObjects = $Team->find(
                            'all', array('recursive'=>-1,'conditions'=>array(
                            'id'=>array($teamid1,$teamid2)))
                        );
                        if (count($teamObjects == 2)) {
                            //note that count($currentTeam) should be 1 less than people_in_team,
                            //since we ignored the user we're checking
                            if ((count($currentTeam1)+1) == $teamObjects[0]['Team']['people_in_team'] 
                                && (count($currentTeam1)+1) == $teamObjects[1]['Team']['people_in_team']
                            ) {
                               
                                $result = $this->mergeTwoTeams($teamid1, $teamid2);
                                //If this was successful, we need to start over
                                if ($result == 'ok') {
                                    return $this->checkUserForDuplicateTeams($userID);
                                }
                            }
                        }
                    }                   
                }
            }
        } 
        return 'ok';      
    }
    function update_user_stats() 
    {
       
    }
    function submit_update_user_stats() 
    {
        $dataFromForm = $this->request->data['User']['user_id'];
        $validEmail = '/\\A(?:^([a-z0-9][a-z0-9_\\-\\.\\+]*)@([a-z0-9][a-z0-9\\.\\-]{0,63}\\.(com|org|net|biz|info|name|net|pro|aero|coop|museum|[a-z]{2,4}))$)\\z/i';
        if (is_int($dataFromForm)) {
            $this->User->updateStatsForUser($dataFromForm);
            $this->Session->setFlash('Stats Updated', 'flash_success');
        }
        else {
            $this->User->recursive = -1;
            $user = $this->User->find(
                'first', array('conditions'=>array('OR'=>array(
                'lgn'=>$dataFromForm,
                'email'=>$dataFromForm)))
            );
            if ($user) {
                $this->User->updateStatsForUser($user['User']['id']);
                $this->Session->setFlash('Stats Updated', 'flash_success');    
            }
            else {
                $this->Session->setFlash('User not found', 'flash_error');
            }
        }
        $this->redirect('/users/update_user_stats');
    }
   
    /**
   * Registration page
   * @author vovich
   */
    function registration()
    {
        $this->Access->checkAccess('User', 'c');
        $validationError = 0;
        if (!empty($this->request->data)) {
            $captcha = $this->Session->read('captcha_text');
            if ($captcha == md5(strtolower($this->request->data['Captcha']['text']))) {            
                  /*Storing*/
                  $this->request->data['User']['activation_code'] = $this->ActivationCode(20);
                  $this->request->data['User']['pwd'] = md5($this->request->data['User']['pwd']);
        
                  $is_exist = $this->User->find('first', array('fields'=>array('id'),'conditions'=>array('activation_code'=>$this->request->data['User']['activation_code'] )));
                while (!empty($is_exist) ){
                     $this->request->data['User']['activation_code'] = $this->ActivationCode(20);
                     $is_exist = $this->User->find('first', array('fields'=>array('id'),'conditions'=>array('activation_code'=>$this->request->data['User']['activation_code'] )));
                }
                if (isset($this->request->data['User']['avatar']['size']) && $this->request->data['User']['avatar']['size'] > 0) {
                    if ($this->request->data['User']['avatar']['size'] > 600000) {
                        $this->logErr('Avatar error - incorrect file size');
                        unset($this->request->data['User']['avatar']);
                    } else {
                        if($this->User->correctAvatar($this->request->data['User']['avatar']['name'], $this->request->data['User']['avatar']['size'])) {
                            $uploadedName = $this->User->uploadAvatar($this->genRandomString(7), $this->request->data['User']['avatar']);
                            $this->request->data['User']['avatar'] = $uploadedName;
                        } else {
                            unset($this->request->data['User']['avatar']);
                            $this->logErr('Avatar error - incorrect file format or size');
                            return $this->redirect('/users/settings/');
                      }
                    }
                } else {
                       unset($this->request->data['User']['avatar']);
                }
        
                if (in_array($this->request->data['User']['lgn'], Configure::read('User.ProhibitedNames'))) {
                    $validationError = 1;
                    $this->Session->setFlash('Sorry, you can not use this Nickname', 'flash_error');    
                }              
                if (!$validationError) {     
                    $this->User->create();
                    if ($this->User->save($this->request->data)) {
                        $id = $this->User->getLastInsertID();
            
                        /* Storing address*/
                        if (!empty($this->request->data['Address']['country_id']) || !empty($this->request->data['Address']['address']) || !empty($this->request->data['Address']['city'])) {
                            $address['Address']             = $this->request->data['Address'];
                            $address['Address']['model']    = "User";
                            $address['Address']['model_id'] = $id;
                            $address['Address']['label']    = "Primary Address";
                            $this->Address->create();
                            $this->Address->save($address);
                        }
                        /* EOF Storing address*/
                        /* Storing Phone*/
                        if (!empty($this->request->data['Phone']['phone']) ) {
                            $address['Phone']               = $this->request->data['Phone'];
                            $address['Phone']['model']      = "User";
                            $address['Phone']['model_id']   = $id;
                            $address['Phone']['type']       = "Primary Phone";
                            $this->Phone->create();
                            $this->Phone->save($address);
                        }
                        /* EOF Storing Phone*/
                        /*ADD new status*/
                        $sql = "INSERT INTO users_statuses (user_id,status_id) VALUES ($id,".REGISTRY_STATUS_ID.")";
                        $this->User->query($sql);
                        //Sending Activation code
                        if (!empty($this->request->data['User']['firstname'])) {
                            $username = $this->request->data['User']['firstname']." ".$this->request->data['User']['lastname'];
                        } else {
                            $username = $this->request->data['User']['lgn'];
                        }
                        $result = $this->sendMailMessage(
                            'ActivationEmail', array(
                                  '{USERNAME}'      => $username,
                                 '{EMAIL}'         => $this->request->data['User']['email'],
                                 '{LINK}'          => MAIN_SERVER . "/activation/{$this->request->data['User']['activation_code']}"
                                  ),
                            $this->request->data['User']['email']
                        );
            
                        if (!$result) {
                             $this->logErr('error occured while sendinig  email');
                        }
                        //EOF sending
                            $this->render('registered');
            
                    }
                }
                $this->request->data['User']['pwd'] = "";
                $this->request->data['User']['confirm_pwd'] = "";
                    /*EOF STORING*/
            } else {
                $this->Session->setFlash('Please retype blue letters.', 'flash_error');
            }          
            ///
        
        } else {
            $this->request->data['User']['birthdate']='';
        }

        $this->set('genders', array(''=>'Select one','M'=>'Male','F'=>'Female'));
        /*Countries*/
        $contriesID = $this->Provincestate->find('all', array('fields'=> array('DISTINCT Provincestate.country_id'),'recursive' => -1,'contains' => array(),'conditions'=> array()));
        $contriesIDs = Set::extract($contriesID, '{n}.Provincestate.country_id');
        /*Countries*/
        $countries = $this->Country->find('list', array('conditions'=>array('Country.id' => $contriesIDs)));
        $countries = array('0'=>"Select one") + $countries;


        $this->set('countries', $countries);

        if (empty($this->request->data['Address']['country_id'])) {
            $countryID = 0;
        } else {
            $countryID = $this->request->data['Address']['country_id'];
        }
        $conditions = array('conditions' => array('country_id' => $countryID),
                'fields' => array('id', 'name'),
                'recursive' => -1
        );
        $states = $this->Provincestate->find('list', $conditions);
        if(!empty($states)) {
            $states=array('0'=>"Select one")+$states; 
        }
        else {
            $states=array('0'=>"Select one"); 
        }
        $this->set('states', $states);

        $timeZones = [];//$this->User->Timezone->find('list');
        $this->set('timeZones', $timeZones);
        $this->request->data['User']['old_subscribed'] = 0;
        $this->request->data['User']['subscribed']     = 0;

    }

    /**
     * User Activation
     * @author vovich
     * @param string $actCode - could be empty -then show form with activation code
     */
    function activation($actCode = "") 
    {
        $this->Access->checkaccess('Activation');
        $userInfo = array();
        if (!empty($this->request->data['User']['activation_code'])) {
            $actCode = $this->request->data['User']['activation_code'];
        } elseif (!empty($actCode)) {
            $this->request->data['User']['activation_code'] = $actCode;
        }
        if (!empty($actCode)) {
            $this->User->recursive = -1;
            $conditions = array('activation_code' => $actCode);
            $userInfo   = $this->User->find($conditions, array(), null, -1);
            if (!empty($userInfo)) {
                if (!isset($this->request->data['User']['rememberMe'])) {
                     $this->request->data['User']['rememberMe'] = 0; 
                }
                if (!$this->Access->loggining($userInfo['User']['id'], $this->request->data['User']['rememberMe'])) {
                    $this->logErr('error occured while Loggining');
                } else {
                    /*Change status*/
                    $sql = "DELETE FROM users_statuses WHERE status_id = " . REGISTRY_STATUS_ID . " AND user_id=".$userInfo['User']['id'];
                    $this->User->query($sql);
                    $this->User->habtmAdd('Status', $userInfo['User']['id'], ACTIVE_STATUS_ID);
                    $this->redirect('/activated');
                }
            } else {
                $this->logErr('error occured: Incorrect Activation code');
                $this->set("Error", true);
            }
        }
    }
  
    /**
   * Resend activation email to user
   * @author Oleg D.
   */
    function resend_activation() 
    {
      
        if (!empty($this->request->data['User']['email'])) {
            $user = $this->User->find('first', array('conditions' => array('email' => $this->request->data['User']['email'])));
            if (empty($user['User']['id'])) {            
                $this->Session->setFlash('Incorrect Email address.', 'flash_error');    
            } else {
                $user['User']['activation_code'] = trim($user['User']['activation_code']);
            
                if (empty($user['User']['activation_code'])) {
                    $user['User']['activation_code'] = $this->ActivationCode(20); 
                    $this->User->validate = array();
                    // this next line doesn't pass the validation rules...
                     $this->User->save(array('id' => $user['User']['id'], 'activation_code' => $user['User']['activation_code']));    
                }
                   //Sending Activation code
                if (!empty($user['User']['firstname'])) {
                    $username = $user['User']['firstname']." ".$user['User']['lastname'];
                } else {
                    $username = $user['User']['lgn'];
                }
                     $result = $this->sendMailMessage(
                         'ActivationEmail', array(
                         '{USERNAME}'      => $username,
                         '{EMAIL}'         => $user['User']['email'],
                         '{LINK}'          => MAIN_SERVER . "/activation/{$user['User']['activation_code']}"
                          ),
                         $user['User']['email']
                     );
                $this->Session->setFlash('Email has been sent to you.', 'flash_success');
                $this->redirect('/');            
            }
    
        }    

      
    }
  
  
    /**
  * This sends an email to the admin requesting software registration
  * 
  * @param  mixed $login
  * @param  mixed $password
  * @param  mixed $macAddress
  * @return mixed
  */
    function registerWSOBPTournamentSoftware_api($login, $password, $macAddress, $allAddresses = '',$description = '') 
    {
        Configure::write('debug', 0);
        $result = $this->login_api($login, $password, 1);

        if ($result == 'ok') {
            $user = $this->Session->read('loggedUser'); 
            $SoftRegObject = ClassRegistry::init('Softwarereg');
            $newKey['user_id'] = $user['id'];
            $newKey['hardaddress'] = $macAddress;
            $newKey['description'] = $description;
            $newKey['alladdresses'] = $allAddresses;
            $newKey['wsobp'] = 1;
            $newKey['key'] = $SoftRegObject->convertMacAddressWSOBP($macAddress);
            $newKey['accepted'] = 0;
            if (!$SoftRegObject->save($newKey)) { return 'couldnt save'; 
            }
            
            $this->sendMailMessage(
                'TournamentRegistrationRequest', array(
                '{USER_EMAIL}'       => $user['email'],
                '{USER_LOGIN}'      =>$user['lgn'],
                '{MAC_ADDRESS}' =>$macAddress,
                '{KEY}'=>$newKey['key']
                ), TOURNAMENTS_EMAIL
            );        
              return 'ok';     
        }
        else { return $result; 
        }
    }
    /**
  * This sends an email to the admin requesting software registration
  * 
  * @param  mixed $login
  * @param  mixed $password
  * @param  mixed $macAddress
  * @return mixed
  */
    function registerTournamentSoftware_api($login, $password, $macAddress, $allAddresses = '',$description = '') 
    {
        Configure::write('debug', 0);
        $result = $this->login_api($login, $password, 1);

        if ($result == 'ok') {
            $user = $this->Session->read('loggedUser'); 
            $SoftRegObject = ClassRegistry::init('Softwarereg');
            $newKey['user_id'] = $user['id'];
            $newKey['hardaddress'] = $macAddress;
            $newKey['description'] = $description;
            $newKey['alladdresses'] = $allAddresses;
            $newKey['key'] = $SoftRegObject->convertMacAddress($macAddress);
            $newKey['accepted'] = 0;
            if (!$SoftRegObject->save($newKey)) { return 'couldnt save'; 
            }
            
            $this->sendMailMessage(
                'TournamentRegistrationRequest', array(
                '{USER_EMAIL}'       => $user['email'],
                '{USER_LOGIN}'      =>$user['lgn'],
                '{MAC_ADDRESS}' =>$macAddress,
                '{KEY}'=>$newKey['key']
                ), TOURNAMENTS_EMAIL
            );        
              return 'ok';     
        }
        else { return $result; 
        }
    }

  
    /**
   * This is the login function for the api. It is the same as the regular login
   * function, except that there are no redirects or exits. Rather, an error message is returned
   * if it is unable to authenticate.
   * author: skinny
   */
    function login_api($login, $password, $is_md5 = null) 
    {
        Configure::write('debug', '0');
        if($login && $password) {
            $this->request->data['User']['userlogin']=$login;
            $this->request->data['User']['userpwd']=$password;
            if ($is_md5) {
                $password_md5 = $password;        
            } else {
                $password_md5 = md5($password);          
            }
        }

        //$this->Access->checkaccess('LoginForm');
        $validEmail = '/\\A(?:^([a-z0-9][a-z0-9_\\-\\.\\+]*)@([a-z0-9][a-z0-9\\.\\-]{0,63}\\.(com|org|net|biz|info|name|net|pro|aero|coop|museum|[a-z]{2,4}))$)\\z/i';
        $userInfo = array();

        if (!empty($this->request->data)) {
            /*Check if login is email or nickname*/
            if (preg_match($validEmail, $this->request->data['User']['userlogin'])) {
                $conditions = array('email'=>$this->request->data['User']['userlogin'],'pwd' => $password_md5);
                $userInfo   = $this->User->find($conditions, array('id'), null, -1);
            } else {
                $conditions = array('lgn'=>$this->request->data['User']['userlogin'],'pwd'=> $password_md5);
                $userInfo   = $this->User->find($conditions, array('id'), null, -1);
            }

            if (!empty($userInfo['User']['id'])) {

                //check if user not activated yet thenredirect to activate
                $isnew =$this->UsersStatus->field('id', 'user_id='.$userInfo['User']['id'].' AND status_id='.REGISTRY_STATUS_ID);
                if (!empty($isnew)) {
                    $this->Session->write('ActivationUserID', $userInfo['User']['id']);
                    //exit("NotActive");
                    return "Not Active";
                }
                //EOF checking
                if (!isset($this->request->data['User']['rememberMe'])) { $this->request->data['User']['rememberMe'] = 0; 
                }

                //return $userInfo;
                if ($this->Access->loggining($userInfo['User']['id'], $this->request->data['User']['rememberMe'])) {
                    return 'ok'; 
                }
                else { return 'There was a problem logging in'; 
                }
            } else {
                return "Incorrect login or password";
            }

        }   else { return "Incorrect login or password"; 
        }

    }
    /**
   * API Function
   * This function searches for users that match the search criterion provided.
   * Any combination of search terms is acceptable, except you can not search on first
   * name alone.
   * Unless the email is provided as a search term, it is hidden in the results
   * author:skinny
   */
    function findPlayers_api($playerEmail,$playerUsername,$playerFirstname,$playerLastname) 
    {
        Configure::write('debug', 0);
        if (empty($playerEmail) && empty($playerUsername) && empty($playerLastname)) {
            return "Can not search on First Name alone."; 
        }
        // do we allow anyone to search for players?

         $searchConditions['is_deleted'] = '0';
        if ($playerEmail <> "") {
            $searchConditions['email Like'] = $playerEmail;
        }
        if ($playerUsername <> "") {
            $searchConditions['lgn Like'] = $playerUsername;
        }
        if ($playerFirstname <> "") {
            $searchConditions['firstname Like'] = $playerFirstname;
        }
        if ($playerLastname <> "") {
            $searchConditions['lastname Like'] = $playerLastname;
        }
         $this->User->recursive = -1;
         $matchingUsers = $this->User->find('all', array('conditions'=>$searchConditions));
         // If the requester searched based on email, then we can show email. Otherwise,
         // need to go through the array and hide the email. Either way, hide the password.

        foreach ($matchingUsers as &$matchingUser) {
            unset($matchingUser['pwd']);
            if ($playerEmail == "") {
                $matchingUser['email'] = 'hidden';
            }
        }
         return $matchingUsers;
    }
    /**
  * This is just to test a response
  */
    function testCommunication_api() 
    {
        return "ok"; 
    }
    function getMyAccount_api() 
    {
        Configure::write('debug', 0);
        if (!$this->isLoggined()) {
            return "You are not logged in.";
        }
        $user = $this->Session->read('loggedUser');
        if ($user['id']==1) { return "You are not logged in."; 
        }     
        return $user;
    }
    /**
* 
 * API Function
   * This adds a player to the system, then assigns it to the team. It first checks to see
   * that a) the team already exists, and b) the requestor has the right to make add a player
   */
  
    function addNewPlayerToTeam_api($email='',$lgn='',$firstname='',$lastname='',$teamID) 
    {
        Configure::write('debug', 0);
        if (!$this->isLoggined()) {
            return "You are not logged in.";
        }
        $user = $this->Session->read('loggedUser');
        if ($user['id']==1) { return "You are not logged in."; 
        } 
        // IMPORTANT: We will need to check to ensure that the requester is the person who created this team
        // There is currently no way to do this, since there is no record of who the creator is
    
        // Get the team
        $Team = ClassRegistry::init('Team');
        $Team->recursive = -1;
        $team = $this->Team->find('first', array('conditions'=>array('id'=>$teamID)));
        if ($team['Team']['status']=='Completed') { return array('message'=>"Team is already complete."); 
        }
    
        // make sure the team isn't already full
        if ($team['Team']['people_in_team'] <= count($currentTeammates)) {
            return array('message'=>"Team is already full."); 
        }     
      
        $newUser = $this->addNewPlayer_api($email, $lgn, $firstname, $lastname);
    
        if (!($newUser['message']=='ok')) { return $newUser; 
        }
        $newUser = $newUser['newuser'];
        // Create a new teammate
        $newTeammate['Teammate']['requester_id'] = $user['id'];
        $newTeammate['Teammate']['user_id'] = $newUser['id'];
        $newTeammate['Teammate']['team_id'] = $teamID;
        $newTeammate['Teammate']['status'] = 'Pending';
    
        $teamsObject = $Teammates->create();
        if (!$Teammates->save($newTeammate)) { return "Could not add new player."; 
        }
        return array('message'=>'ok','newuser'=>$newUser);
    }
    /**
   * API Function
   * This allows someone to add a new player to the system. An email is the only thing that is
   * required.
   * author:skinny
   */
    function addNewPlayer_api($email='',$lgn='',$firstname='',$lastname='') 
    {
        Configure::write('debug', 0);
        unset($this->request->data);
        if ($email == '') { return "You must provide a valid email address."; 
        }
        $validEmail = '/\\A(?:^([a-z0-9][a-z0-9_\\-\\.\\+]*)@([a-z0-9][a-z0-9\\.\\-]{0,63}\\.(com|org|net|biz|info|name|net|pro|aero|coop|museum|[a-z]{2,4}))$)\\z/i';
        if (!preg_match($validEmail, $email)) { return array('message'=>"You must provide a valid email address."); 
        }

        $this->User->recursive = -1;
        $existingUser = $this->User->find(
            'first', array('conditions'=>array(
            'email'=>$email))
        );
        $this->request->data['User']['email'] = $email;
        if ($existingUser != null) {
            return array('message'=>"User with that email already exists",'User'=>$existingUser['User']);
        }
        //if lgn is screwy, fix it:
      
        // if no lgn was provided, set it equal to the
        //username portion of the email address
        if ($lgn == '') {
            $exp_array = explode("@", $email);
            $new_nick=$my_nick=$exp_array['0'];
        }else {
            //if the supplied username is an email, just use the first part
            $exp_array = explode("@", $lgn);
            if ($exp_array[0] != $lgn) {
                $lgn = $exp_array[0]; 
            }
            //strip the login of non alphanumerics
            $lgn = ereg_replace("[^A-Za-z0-9]", "", $lgn);
            $new_nick = $my_nick = $lgn;
        }
        $i=1;
        $nick_unfree=1;
        while ($nick_unfree==1) {
            $this->User->recursive = -1;
            $unfree_user=$this->User->find('count', array('conditions'=>array('lgn'=>$new_nick)));
            if(!$unfree_user) {
                $nick_unfree=0;
                break;
            }else{
                $new_nick=$my_nick.$i;
            }
            $i++;
        }
         // Generate password
        $new_pwd=substr(uniqid(), -6);

        $this->request->data['User']['lgn']=$new_nick;
        $this->request->data['User']['pwd']=$new_pwd;
        $this->request->data['User']['firstname']=$firstname;
        $this->request->data['User']['lastname']=$lastname;
        /*Storing*/
        $this->request->data['User']['pwd'] = md5($this->request->data['User']['pwd']);
        $this->request->data['User']['activation_code'] = $this->ActivationCode(20);

        $is_exist = $this->User->find('first', array('fields'=>array('id'),'conditions'=>array('activation_code'=>$this->request->data['User']['activation_code'] )));
        while (!empty($is_exist) ){
            $this->request->data['User']['activation_code'] = $this->ActivationCode(20);
            $is_exist = $this->User->find('first', array('fields'=>array('id'),'conditions'=>array('activation_code'=>$this->request->data['User']['activation_code'] )));
        }

        $this->User->create();

        if ($this->User->save($this->request->data)) {
            $id = $this->User->getLastInsertID();
            $this->User->habtmAdd('Status', $id, REGISTRY_STATUS_ID);
            $this->request->data['User']['id'] = $id;
            $this->sendMailMessage(
                'NewPlayerAdded', array(
                         '{LOGIN}'         => $this->request->data['User']['lgn'],
                         '{PASSWORD}'         => $new_pwd,
                         '{EMAIL}'         => $this->request->data['User']['email'],
                         '{LINK}'          => MAIN_SERVER . "/activation/{$this->request->data['User']['activation_code']}"

                 ),
                $this->request->data['User']['email']
            );
            return array('message'=>'ok','newuser'=>$this->request->data['User']);
        }
        else {
            return array('message'=>"Could not save data."); 
        }
    }
    /**
   * Login form
   * @author vovich
   */
    function login($login=null,$password=null) 
    {
        Configure::write('debug', '0');
        if ($this->RequestHandler->isAjax()) {
            $this->layout = false;
        }else{
            $this->redirect('/login');
        }

        if($login&&$password) {
            $this->request->data['User']['userlogin']=$login;
            $this->request->data['User']['userpwd']=$password;

        }

        //$this->Access->checkaccess('LoginForm');
        $validEmail = '/\\A(?:^([a-z0-9][a-z0-9_\\-\\.\\+]*)@([a-z0-9][a-z0-9\\.\\-]{0,63}\\.(com|org|net|biz|info|name|net|pro|aero|coop|museum|[a-z]{2,4}))$)\\z/i';
        $userInfo = array();

        if (!empty($this->request->data)) {

            /*Check if login is email or nickname*/
            if (preg_match($validEmail, $this->request->data['User']['userlogin'])) {
                $conditions = array('email'=>$this->request->data['User']['userlogin'],'pwd'=>md5($this->request->data['User']['userpwd']));
                $userInfo   = $this->User->find($conditions, array('id'), null, -1);
            } else {
                $conditions = array('lgn'=>$this->request->data['User']['userlogin'],'pwd'=>md5($this->request->data['User']['userpwd']));
                $userInfo   = $this->User->find($conditions, array('id'), null, -1);
            }

            if (!empty($userInfo['User']['id'])) {

                //check if user not activated yet thenredirect to activate
                $isnew =$this->UsersStatus->field('id', 'user_id='.$userInfo['User']['id'].' AND status_id='.REGISTRY_STATUS_ID);
                if (!empty($isnew)) {
                    $this->Session->write('ActivationUserID', $userInfo['User']['id']);
                    exit("NotActive");
                }
                //EOF checking
                if (!isset($this->request->data['User']['rememberMe'])) { $this->request->data['User']['rememberMe'] = 0; 
                }

                $this->Access->loggining($userInfo['User']['id'], $this->request->data['User']['rememberMe']);

                if ($this->RequestHandler->isAjax()) {
                    echo "ok";
                    exit();
                }else{
                    $this->redirect('/');
                }
            } else {
                if ($this->RequestHandler->isAjax()) {
                    echo "Error";
                    exit();
                }else{
                    //modded by Povstyanoy
                    //$this->set('Error',true);
                    $this->logErr('error occured: Incorrect login or  password');
                    $this->Session->setFlash('Incorrect login or  password', 'flash_error');
                    $this->redirect('/login');
                    exit();
                }

            }

        }
    }

    /**
   * Registration by AJAX
   * @author Oleg D.
   * int $type :
   * 1 - Normal registration
   * 2 - Pseudo registration - need only email
   */
    function registration_ajax($type=1,$email=null, $lgn=null, $pwd=null, $saveSession = 1)
    {
        Configure::write('debug', '0');
        $this->layout=false;
        if ($this->RequestHandler->isAjax()) {
            $this->layout = false;
            $this->User->recursive=-1;
            $unfree_email=$this->User->find('count', array('conditions'=>array('email'=>$email)));
            $this->request->data['User']['email']=$email;
      
            if (!empty($_REQUEST['firstname'])) {
                $this->request->data['User']['firstname'] = $_REQUEST['firstname'];
            }
            if (!empty($_REQUEST['lastname'])) {
                $this->request->data['User']['lastname'] = $_REQUEST['lastname'];
            }
        
      
      
            //If it's a standart registration.
            if($type==1) {
                $this->Access->checkAccess('User', 'c');  
                if($unfree_email>0) {
                    // Email UNFREE
                    exit('erroremail');
                }
                // Check for unique Nickname
                //$this->User->recursive=-1;
                $unfree_user=$this->User->find('count', array('conditions'=>array('lgn'=>$lgn)));
                if($unfree_user>0) {
                    // Login UNFREE
                    exit('erroruser');
                }

                $this->request->data['User']['lgn']=$lgn;
                $this->request->data['User']['pwd']=$pwd;
                /*Storing*/
                $this->request->data['User']['activation_code'] = $this->ActivationCode(20);
                $is_exist = $this->User->find('first', array('fields'=>array('id'),'conditions'=>array('activation_code'=>$this->request->data['User']['activation_code'] )));
                while (!empty($is_exist) ){
                    $this->request->data['User']['activation_code'] = $this->ActivationCode(20);
                    $is_exist = $this->User->find('first', array('fields'=>array('id'),'conditions'=>array('activation_code'=>$this->request->data['User']['activation_code'] )));
                }


                $this->request->data['User']['pwd'] = md5($this->request->data['User']['pwd']);

        
                $is_exist = $this->User->find('first', array('fields'=>array('id'),'conditions'=>array('activation_code'=>$this->request->data['User']['activation_code'] )));
                while (!empty($is_exist) ){
                     $this->request->data['User']['activation_code'] = $this->ActivationCode(20);
                     $is_exist = $this->User->find('first', array('fields'=>array('id'),'conditions'=>array('activation_code'=>$this->request->data['User']['activation_code'] )));
                }

                $this->User->create();
                if ($this->User->save($this->request->data)) {
                    $id = $this->User->getLastInsertID();

                    $this->User->habtmAdd('Status', $id, ACTIVE_STATUS_ID);
                    //Sending Activation code
                    $username = $this->request->data['User']['lgn'];

                    $result = $this->sendMailMessage(
                        'StoreRegistration', array(
                        '{USERNAME}'      => $username,
                        '{LOGIN}'      => $lgn,
                        '{PASSWORD}'   => $pwd
                        ),
                        $this->request->data['User']['email']
                    );
                    //'{LINK}'          => "http://{$_SERVER['HTTP_HOST']}/activation/{$this->request->data['User']['activation_code']}"
                    if (!isset($this->request->data['User']['rememberMe'])) { $this->request->data['User']['rememberMe'] = 0; 
                    }
                    $this->Access->loggining($id, $this->request->data['User']['rememberMe']);
                    if (!$result) {
                        //echo 'error occured while sendinig registration email';
                    }
                }

                //$this->Access->loggining($id);
                exit('ok');
                //If it's a pseudo registration.
            }elseif($type==2) {
                //note that we're currently not checking access....right now, anyone can do this...
                if($unfree_email>0) {
                    // Email UNFREE
                    $id = $this->User->field('id', array('email' => $email));
                    $login = $this->User->field('lgn', array('email' => $email));
                }else{
                    $login = '';    
                    $password = $this->genRandomString(6);    
                    $this->request->data['User']['lgn'] = $this->genRandomString(15);
                    $this->request->data['User']['pwd'] = $password;
                    $this->request->data['User']['is_hidden'] = 1;

                    /*Storing*/
                    $this->request->data['User']['pwd'] = md5($this->request->data['User']['pwd']);
                    $this->request->data['User']['activation_code'] = $this->ActivationCode(20);

                    $is_exist = $this->User->find('first', array('fields'=>array('id'),'conditions'=>array('activation_code'=>$this->request->data['User']['activation_code'] )));
                    while (!empty($is_exist) ){
                        $this->request->data['User']['activation_code'] = $this->ActivationCode(20);
                        $is_exist = $this->User->find('first', array('fields'=>array('id'),'conditions'=>array('activation_code'=>$this->request->data['User']['activation_code'] )));
                    }
                    $this->User->validate = array();
                    $this->User->create();
                    if ($this->User->save($this->request->data)) {
                        $id = $this->User->getLastInsertID();
                        $login = 'user' . $id;
            
                        $this->User->save(array('id' => $id, 'lgn' => $login));

                        $this->User->habtmAdd('Status', $id, ACTIVE_STATUS_ID);
                        //Sending Activation code
                        $result = $this->sendMailMessage(
                            'HiddenRegistration', array(
                            '{LOGIN}'         => $login,
                            '{PASSWORD}'         => $password,
                            '{EMAIL}'         => $this->request->data['User']['email']
                            ),
                            $this->request->data['User']['email']
                        );

                        if (!$result) {
                            echo 'error occured while sendinig registration email';
                        }

                    }
                    //$this->Access->loggining($id);
                }
                if ($saveSession) {
                    $this->Session->write('hidden_user_id', $id);
                    exit('ok');
                } else {
                    exit($login);    
                }

            }
            /*EOF STORING*/
        }
        exit;
    }
    /**
  *  Show login form with redirection
  */
    function loginForm()
    {
        if ($this->isLoggined()) {
            return $this->redirect('/');    
        }
        $validEmail = '/\\A(?:^([a-z0-9][a-z0-9_\\-\\.\\+]*)@([a-z0-9][a-z0-9\\.\\-]{0,63}\\.(com|org|net|biz|info|name|net|pro|aero|coop|museum|[a-z]{2,4}))$)\\z/i';
        $userInfo = array();
        
        if (!empty($this->request->data)) {
            /*Check if login is email or nickname*/
            if (preg_match($validEmail, $this->request->data['User']['userlogin'])) {
                $conditions = array('email'=>$this->request->data['User']['userlogin'],'pwd'=>md5($this->request->data['User']['userpwd']));
            } else {
                $conditions = array('lgn'=>$this->request->data['User']['userlogin'],'pwd'=>md5($this->request->data['User']['userpwd']));
            }
            $userInfo = $this->User->find('first', array('conditions' => $conditions));
            if (!empty($userInfo['User']['id'])) {

                $isnew =$this->UsersStatus->field('id', 'user_id='.$userInfo['User']['id'].' AND status_id='.REGISTRY_STATUS_ID);
                if (!empty($isnew)) {
                    $this->Session->write('ActivationUserID', $userInfo['User']['id']);
                    return $this->redirect("/activation");
                }

                if (!isset($this->request->data['User']['rememberMe'])) {
                    $this->request->data['User']['rememberMe'] = 0; 
                }

               $this->Access->loggining($userInfo['User']['id'], $this->request->data['User']['rememberMe']);
               $this->redirect($this->request->data['User']['URL']);
            } else {
                  $this->set('Error', true);
            }

        }

        if (empty($this->request->data['User']['URL'])) {
            if ($this->Session->check('URL')) {
                $this->request->data['User']['URL'] =  $this->Session->read('URL');
                $this->Session->delete('URL');
            } elseif(!empty($_SERVER['HTTP_REFERER'])) {
                $this->request->data['User']['URL'] = $_SERVER['HTTP_REFERER'];
            }else { 
                $this->request->data['User']['URL'] ="/";
            }
          
        }  




    }

    /**
    * Logout and clear all sessions
    * @author vovich
    */
    function logout()
    {
        $_SESSION = array();
        $this->Cookie->delete('loggedUser');
        $this->redirect('/');
    }

    function logout_api() 
    {
        $_SESSION = array();
        $this->Cookie->delete('loggedUser');
        return 'ok';
    }
    /**
   * Show all users
   * @author vovich
   */
    function index() 
    {

        $this->Access->checkAccess('ShowAllUsers', 'r');

        /* filter Getting data from the session or from the form*/
        if(!empty($this->request->data['UserFilter'])) {
            $this->Session->write('UserFilterIndex', $this->request->data['UserFilter']);
        }elseif($this->Session->check('UserFilterIndex')) {
            $this->request->data['UserFilter']=$this->Session->read('UserFilterIndex');
        }
        $conditions = array();
        if (!empty ($this->request->data['UserFilter'])) {
            foreach ($this->request->data['UserFilter'] as $key => $val) {
                $this->request->data['UserFilter'][$key] = trim($val);
            }
        }
        //Prepare data for the filter
        if (!empty( $this->request->data['UserFilter']['firstname'])) {
            $conditions['User.firstname LIKE'] = '%' . $this->request->data['UserFilter']['firstname'] . '%'; 
        }
        if (!empty( $this->request->data['UserFilter']['lastname'])) {
            $conditions['User.lastname LIKE'] = '%' . $this->request->data['UserFilter']['lastname'] . '%'; 
        }
        if (!empty( $this->request->data['UserFilter']['lgn'])) {
            $conditions['User.lgn LIKE'] = '%' . $this->request->data['UserFilter']['lgn'] . '%'; 
        }
        if (!empty( $this->request->data['UserFilter']['id'])) {
            $conditions['User.id'] = $this->request->data['UserFilter']['id']; 
        }

        if (!empty( $this->request->data['UserFilter']['email'])) {
            $conditions['User.email LIKE'] = $this->request->data['UserFilter']['email']; 
        }

        if (!empty( $this->request->data['UserFilter']['city'])) {
            $conditions['Address.city'] = $this->request->data['UserFilter']['city']; 
        }

        if (!empty( $this->request->data['UserFilter']['provincestate_id'])) {
            $conditions['Address.provincestate_id'] = $this->request->data['UserFilter']['provincestate_id']; 
        }

        if (!empty( $this->request->data['UserFilter']['status'])) {
            $conditions['Status.id'] = $this->request->data['UserFilter']['status']; 
        }

        if (!empty( $this->request->data['UserFilter']['searchby'])) {
            $this->User->compare = $this->request->data['UserFilter']['searchby']; 
        }

        if (!empty( $this->request->data['UserFilter']['is_deleted'])) {
            $conditions['User.is_deleted'] = $this->request->data['UserFilter']['is_deleted']; 
        }
        //Getting statuses
        $groups = $this->Group->find('all', array('conditions'=>array('Group.id <>'=>VISITOR_GROUP)));

        $this->paginate['User']['conditions'] = $conditions;
        $this->paginate['User']['contain']    = array('Status', 'reset'=>false);
        $this->User->usePaginate = "custom";
        $users = $this->paginate('User');

        $this->set('users', $users);
        $this->set('groups', $groups);

        $states = $this->Provincestate->find('list', $conditions);
        if(!empty($states)) {
            $states=array('0'=>"Select one")+$states; 
        }
        else {
            $states=array('0'=>"Select one"); 
        }
        $this->set('states', $states);
    }

    /**
   * Add new user
   * @author vovich
   */
    function add() 
    {
        $this->Access->checkAccess('User', 'c');
        if (!empty($this->request->data)) {
            /*debug($this->request->data);
            exit();*/
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash('The User has been saved', 'flash_success');
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash('The User could not be saved. Please, try again.', 'flash_error');
            }
        }
        $conditions = array('hide' => 0);
        $groups = $this->Group->find('list', array('conditions' => $conditions));
        $this->set('groups', $groups);
    }

    /**
   * edit user profile
   * @author vovich
   * @param int $id
   */
    function settings($id = null ) 
    {
        if ($id == 1) {
            exit('This is a visitor profile!');
        }
        set_time_limit(500);
        $this->noCache();
        $validationError = 0;
        $showGroup = false;
        if($id) {
            $this->set('id', $id);
        }
        $userSession = $this->Session->read('loggedUser');
        if (!$id && $this->Session->check('loggedUser')) {
            $userSession = $this->Session->read('loggedUser');
            $id = $userSession['id'];
        }
        if (!$id && empty($this->request->data)) {
            $this->logErr('error occured while Edit profile: empty userID');
            $this->redirect('/');
        }
        //just testing
        $this->Access->checkAccess('User', 'u', $id);

        $passwordchanged = false;
        if (!empty($this->request->data)) {
            /*storing profile*/
            /*Validation*/
            if (empty($this->request->data['User']['pwd'])) {
                unset ($this->request->data['User']['pwd']);
                unset($this->User->validate['pwd']);
            } else {
                $this->request->data['User']['pwd'] = md5($this->request->data['User']['pwd']);
                $passwordchanged = true;
            }

            //unset($this->User->validate['lgn']);

            if ($this->request->data['User']['email'] == $this->request->data['User']['old_email']) {
                unset($this->User->validate['email']['isunique']);
            }

            if (isset($this->request->data['User']['avatar']['size']) && $this->request->data['User']['avatar']['size'] > 0) {
                if ($this->request->data['User']['avatar']['size'] > 600000) {
                    $this->Session->setFlash('Avatar error - incorrect file size', 'flash_error');
                    unset($this->request->data['User']['avatar']);
                } else {
                    if($this->User->correctAvatar($this->request->data['User']['avatar']['name'], $this->request->data['User']['avatar']['size'])) {
                        $oldFile = $this->User->field('avatar', array('User.id' => $id));
                        $uploadedName = $this->User->uploadAvatar($this->request->data['User']['avatar'], $this->getUserID(), $oldFile);
                        $this->request->data['User']['avatar'] = $uploadedName;
                        $this->Session->write('loggedUser.avatar', $uploadedName);
                    } else {
                        unset($this->request->data['User']['avatar']);
                        $this->Session->setFlash('Avatar error - incorrect file format or size', 'flash_error');
                        return $this->redirect('/users/settings/');
                    }
                }
            } else {
                unset($this->request->data['User']['avatar']);
            }
                               
            // User Info validation
            $this->User->recursive = -1;
            $oldUserInfo = $this->User->find('first', array('conditions' => array('User.id' => $id)));
            if ($this->request->data['User']['lgn'] && $this->request->data['User']['lgn'] != $oldUserInfo['User']['lgn']) {
            
                if ($this->UserHistory->find('count', array('conditions' => array('user_id' => $id, 'field' => 'lgn', 'created > date_add(now(), interval -1 month)')))) {
                    $validationError = 1;
                    $this->Session->setFlash('Sorry, but you can change the Nickname only once a month', 'flash_error');    
                }    
                if ($this->UserHistory->find('count', array('conditions' => array('field' => 'lgn', 'old_value' => $this->request->data['User']['lgn'], 'created > date_add(now(), interval -3 month)')))) {
                    $validationError = 1;
                    $this->Session->setFlash('Sorry, you can not use this Nickname', 'flash_error');    
                }
                if (in_array($this->request->data['User']['lgn'], Configure::read('User.ProhibitedNames'))) {
                    $validationError = 1;
                    $this->Session->setFlash('Sorry, you can not use this Nickname', 'flash_error');    
                }        
            } elseif ($this->request->data['User']['lgn'] == $oldUserInfo['User']['lgn']) {
                unset($this->User->validate['lgn']['isunique']);
            }     
        
            /*Storing*/
            $this->User->recursive = -1;
            $this->User->hasAndBelongsToMany = array();
            //pr($this->request->data);
            if (!$validationError) {
                //$this->User->hasAndBelongsToMany = array();
                if ($this->User->save($this->request->data)) {
                    // SAVE USER CHANGES
                    if ($this->request->data['User']['lgn'] && $this->request->data['User']['lgn'] != $oldUserInfo['User']['lgn']) {
                        $this->UserHistory->create();    
                        $this->UserHistory->save(array('field' => 'lgn', 'new_value' => $this->request->data['User']['lgn'], 'old_value' => $oldUserInfo['User']['lgn'],'user_id' => $id));                  
                    }    
                    if ($this->request->data['User']['email'] != $oldUserInfo['User']['email']) {
                        $this->UserHistory->create();    
                        $this->UserHistory->save(array('field' => 'email', 'new_value' => $this->request->data['User']['email'], 'old_value' => $oldUserInfo['User']['email'],'user_id' => $id));                                      
                    }    
                    // EOF SAVE USER CHANGES	          
                    /* Change Login */
                    if(!isset($this->request->data['User']['rememberMe'])) { 
                        $this->request->data['User']['rememberMe'] = 0;
                    }
                    if ($id == $this->getUserID()) {
                        $this->Access->loggining($id, $this->request->data['User']['rememberMe']);
                    }
                     //if password changed, regenerate qr
                    if ($passwordchanged) {
                        $this->User->recursive = -1;
                        $userForQR = $this->User->find('first', array('conditions'=>array('id'=>$id)));
                        $mysqldate = date('Y-m-d H:i:s');                                              
                        $qr_togen = "0;".$id.";".$userForQR['User']['lgn'].";".$userForQR['User']['email'].";0;".$mysqldate;
                        $this->User->generate_and_save_new_qr($qr_togen, $userForQR['User']['email'], $mysqldate);
                    }
              
                      $this->Session->setFlash('Profile has been changed', 'flash_success');
                      $this->goHome();
                      //$this->redirect(array('action'=>'index'));
                } else {
                    $this->Session->setFlash('The Profile could not be saved. Please, try again.', 'flash_error');
                    $this->logErr('The User could not be saved. Please, try again.');
                }
            }
        }//EOF storing

        /*Reading*/
        $this->User->recursive = 2;
        $this->request->data = $this->User->read(null, $id);
        if (empty($this->request->data['User']['birthdate'])) {
            $this->request->data['User']['birthdate']=''; 
        }


        $this->set('genders', array(''=>'Select one','M'=>'Male','F'=>'Female'));
        /*Prepare data*/
        $this->request->data['User']['pwd'] = "";
        if (!empty($this->request->data['User']['email'])) {
            $this->request->data['User']['old_email'] = $this->request->data['User']['email'];
        }

        $this->request->data['User']['subscribed'] = $this->User->Mailinglist->isUserInList($this->request->data['User']['id'], LISTID);
        $this->request->data['User']['old_subscribed'] = $this->request->data['User']['subscribed'];

        if (!empty($this->request->data['User']['lgn'])) {
            $this->request->data['User']['old_lgn'] = $this->request->data['User']['lgn'];
        }

        $timeZones =$this->User->Timezone->find('list');
        $this->set('timeZones', $timeZones);


        /*USER GROUP*/
        $showGroup =false;
        if ($this->Access->getAccess('UserGroup', 'u')) {
            $conditions_group = array('hide' => 0);
            if (!empty($this->request->data['Status'])) {
                $except = array();
                foreach ($this->request->data['Status'] as &$status) {
                    $conditions = array('group_id' => $status['group_id']);
                    $status['Statuses'] = $this->Status->find('list', array('conditions' => $conditions));
                    $except[] = $status['group_id'];
                }
                $conditions_group['NOT'] = array('id' => $except);
                unset($status);
            }

            $groups = $this->Group->find('list', array('conditions' => $conditions_group));
            $this->set('groups', $groups);
            $showGroup = true;
        }


        $this->set('showGroup', $showGroup);

        /*EOF USER GROUP*/

        /*USER DISCOUNT GROUP*/
        $showDiscountGroup =false;
        /* Need to fix or just delete this stuff
        if ($this->Access->getAccess('StoreDiscountgroups','u')) {
        $this->StoreDiscountgroupsMember->recursive=1;
        $discountGroups = $this->StoreDiscountgroupsMember->find('all', array('conditions' => array('StoreDiscountgroupsMember.member_type'=>'user','StoreDiscountgroupsMember.member_id'=>$id,'StoreDiscountgroup.is_deleted<>1')));

        $allDiscountGroups=$this->StoreDiscountgroupsMember->StoreDiscountgroup->find('list',array('conditions'=>array('is_deleted<>1')));
        $allDiscountGroups=array('0'=>"Select discount group")+$allDiscountGroups;
        $this->set('allDiscountGroups',$allDiscountGroups);
        $this->set('discountGroups',$discountGroups);
        $showDiscountGroup = true;
        }
        */
        $this->set('showDiscountGroup', $showDiscountGroup);
        /*EOF USER DISCOUNT GROUP*/
        $histories = $this->UserHistory->find('all', array('conditions' => array('user_id' => $id)));
        $this->set('histories', $histories);    
            
        if ($this->request->data['User']['facebook_id']) {
            $this->request->data['facebook'] = $this->User->getFacebookInfo($this->request->data['User']['facebook_id']);    
        }
        if ($this->request->data['User']['twitter_id']) {
            $this->request->data['twitter'] = $this->User->getTwitterInfo($this->request->data['User']['twitter_id']);    
        }        
    }

    /**
     * Update User statuses from Edit user
     * @author vovich
     * @param int $id
     */
    function updateStatuses($id = null)
    {
        $this->Access->checkAccess('UserGroup', 'u');
        if (!$id) {
            $this->Session->setFlash('Invalid id for User', 'flash_error');
            $this->redirect(array('action'=>'index'));
        }

        $data['Status'] = $this->request->data['Statuses'];

        $this->User->id = $id;
        unset($this->User->validate);
        if ($this->User->save($data)) {
            $this->Session->setFlash('User Updated', 'flash_success');
            $this->redirect('/u/0/'.$id);
        } else{
            $this->Session->setFlash('Error while Updating', 'flash_error');
            $this->logErr('error occured while sendinig registration email');
            $this->redirect('/u/0/'.$id);

        }

    }
    /**
   * Assign new status for the user
   * @author vovich
   * @param int $id
   */
    function assignStatus($id = null)
    {
        $this->Access->checkAccess('UserGroup', 'c');
        if (!$id) {
            $this->Session->setFlash('Invalid id for User', 'flash_error');
            $this->redirect(array('action'=>'index'));
        }
        unset($this->User->validate);
            $this->User->habtmAdd('Status', $id, $this->request->data['Status']['id']);
            $this->Session->setFlash('User Updated', 'flash_success');
        $this->redirect('/u/0/'.$id);
    }

    /**
  * Delete user from group
  * @author vovich
  * @param int $userID
  * @param int $statusID
  */
    function deleteFromGroup($userID = null, $statusID = null) 
    {
        $this->Access->checkAccess('UserGroup', 'd');
        if (!$userID || !$statusID) {
            $this->Session->setFlash('Invalid id ', 'flash_error');
            $this->redirect(array('action'=>'index'));
        }
        if ($this->User->habtmDelete('Status', $userID, $statusID)) {
            $this->redirect('/u/0/'.$userID);
        }
        exit;
    }

    /**
   * Delete user
   * @author vovich
   * @param int $id
   */
    function delete($id = null) 
    {
        $this->Access->checkAccess('User', 'd');
        if (!$id) {
            $this->Session->setFlash('Invalid id for User', 'flash_error');
            $this->logErr('error occured: Invalid id for User.');
            $this->redirect(array('action'=>'index'));
        }

        $this->request->data['User']['id']         = $id;
        $this->request->data['User']['is_deleted'] = 1;
        $this->request->data['User']['deleted']    = date('Y-m-d H:i:s');

        if ($this->User->save($this->request->data, false)) {
            $this->Session->setFlash('User deleted', 'flash_success');
            $this->redirect(array('action'=>'index'));
        }
    }
    /**
   * This allows an admin to log into someones account
   * id can be user.id or user.lgn
   */
    function useSiteAsUser($id = null) 
    {
        $this->Access->checkAccess('User', 'd');
        if (!$id) {
            $this->Session->setFlash('Invalid id for User', 'flash_error');
            $this->logErr('error occured: Invalid id for User.');
            $this->redirect(array('action'=>'index'));
        }
        $this->User->recursive = -1;
        $user = $this->User->find('first', array('conditions'=>array('id'=>$id)));
        if (!$user) { 
            $user = $this->User->find('first', array('conditions'=>array('lgn'=>$id))); 
        }
        if (!$user) {
            $this->Session->setFlash('User could not be found', 'flash_error');
            $this->logErr('error occured: Invalid id for User.');
            $this->redirect(array('action'=>'index'));
        }
        $this->login_api($user['User']['lgn'], $user['User']['pwd'], 1);
        $this->redirect($_SERVER['HTTP_REFERER']); 
        //$this->redirect(array('action'=>'index'));
    
    }

    /**
   * Restore user
   * @author vovich
   * @param int $id
   */
    function restore($id = null) 
    {
        $this->Access->checkAccess('User', 'u');
        if (!$id) {
            $this->Session->setFlash('Invalid id for User', 'flash_error');
            $this->logErr('error occured: Invalid id for User.');
            $this->redirect(array('action'=>'index'));
        }

        $this->request->data['User']['id']         = $id;
        $this->request->data['User']['is_deleted'] = 0;
        $this->request->data['User']['deleted']    = '';

        if ($this->User->save($this->request->data, false)) {
            $this->Session->setFlash('User Restored', 'flash_success');
            $this->redirect(array('action'=>'index'));
        }
    }

    /**
   *  AJAX Show statuses select for the current group
   * @author vovich
   * @param $_POST['GroupId']
   */
    function groupstatuses() 
    {
        Configure::write('debug', 0);
        $this->layout = false;
        if (!empty($_POST['GroupId'])) {
            $conditions = array('group_id'=>$_POST['GroupId']);
        } else {
            $conditions = array('group_id'=>1);
        }

        $statuses = $this->Status->find('list', array('conditions' => $conditions));
        $this->set('statuses', $statuses);

    }


     /**
     * AJAX Show change status Form
     * @author vovich
   * @param $_GET['userID']
   * @param $_GET['groupID']
   * @param $_GET['statusID']
     */
    function changeStatusForm() 
    {
        Configure::write('debug', 0);
         $this->layout = false;
        if (!empty($_GET['groupID']) ) {
            $conditions = array('group_id'=>$_GET['groupID']);
            $this->set('groupID', $_GET['groupID']);
        } else {
            $conditions = array('group_id'=>VISITOR_GROUP);
            $this->set('groupID', VISITOR_GROUP);
        }
        if (!empty($_GET['statusID'])) {
            $this->set('statusID', $_GET['statusID']); 
        }
        if (!empty($_GET['userID'])) {
            $this->set('userID', $_GET['userID']); 
        }
        $statuses = $this->Status->find('list', array('conditions' => $conditions));
        $this->set('statuses', $statuses);

    }
    /**
     * AJAX change status of the user
     * @author vovich
   * @param $_POST['oldStatusID']
   * @param $_POST['userID']
   * @param $_POST['newstatusID']
     */
    function changeStatus()
    {
        Configure::write('debug', 0);
        $this->layout = false;
        if (!empty($_POST['oldStatusID']) && !empty($_POST['userID']) && !empty($_POST['newstatusID']) && $this->RequestHandler->isAjax() ) {
            $this->User->habtmDelete('Status', $_POST['userID'], $_POST['oldStatusID']);
            $this->User->habtmAdd('Status', $_POST['userID'], $_POST['newstatusID']);
            $this->Status->recursive = -1;
            $status = $this->Status->read(null, $_POST['newstatusID']);
            echo $status['Status']['name'];
            exit();

        } else {
            echo "error";
            exit();
        }
    }

    function showUserMenu()
    {
        Configure::write('debug', 1);
        $this->layout = false;

        if ($this->RequestHandler->isAjax()) {
            $this->set('LoggedMenu', $this->Access->getAccess('LoggedMenu'));

        } else {
            $this->logErr('error occured: Not AJAX call');
            exit();
        }
    }

    function showUserSubmenu()
    {
         Configure::write('debug', 0);
        $this->layout = false;

        if ($this->RequestHandler->isAjax()) {
            $this->set('LoggedMenu', $this->Access->getAccess('LoggedMenu'));
            $this->set('AdminMenu', $this->Access->getAccess('AdminMenu'));
        } else {
            $this->logErr('error occured: Not AJAX call');
            exit();
        }


    }

    /**
   * Lost Password form and AJAX processing
   * @author vovich
   */
    function lostPassword()
    {
        Configure::write('debug', 0);
        $this->layout = false;

        if ($this->RequestHandler->isAjax() && !empty($this->request->data['User']['useremail'])) {
            //processing
            $conditions = array('email' => $this->request->data['User']['useremail']);
            $userInfo   = $this->User->find('first', array('conditions'=>$conditions), array(), null, -1);

            if (empty($userInfo['User']['id'])) {
                exit("Error");
            } else {
        
                if (empty($userInfo['User']['activation_code'])) {
                    $activationCode = $this->ActivationCode(20);              
                } else {
                    $activationCode = $userInfo['User']['activation_code'];           
                }
        
                $this->request->data['User']['id'] = $userInfo['User']['id'];
                $this->request->data['User']['activation_date'] = date('Y-m-d H:i:s');
                $this->request->data['User']['activation_code'] = $activationCode;
                $this->User->save($this->request->data, false);

                //Sending Activation code
                if (!empty($userInfo['User']['firstname'])) {
                    $username = $userInfo['User']['firstname']." ".$userInfo['User']['lastname'];
                } else {
                    $username = $userInfo['User']['lgn'];
                }
                $result = $this->sendMailMessage(
                    'ForgotPasswordEmail', array(
                    '{USERNAME}'      => $username,
                     '{EMAIL}'         => $userInfo['User']['email'],
                     '{LGN}'           => $userInfo['User']['lgn'],
                     '{LINK}'          => MAIN_SERVER . "/newpassword/{$activationCode}"
                      ),
                    $this->request->data['User']['useremail']
                );

                if (!$result) {
                    //echo 'error occured while sendinig registration email';
                }
                exit();
            }

        }

    }

    /**
     * User Change password
     * @author vovich
     * @param string $actCode - could not be empty -then show form with activation code
     */
    function newpassword($actCode = "") 
    {

        $userInfo = array();

        if (!empty($actCode)) {
            $conditions = array('activation_code' => $actCode,'(UNIX_TIMESTAMP()-UNIX_TIMESTAMP(activation_date))/(60*60) <'=>ACTIVATION_KODE_EXPIRED);
            $userInfo   = $this->User->find($conditions, array(), null, -1);

            if (!empty($userInfo) ) {
                if (!empty($this->request->data['User'])) {
                    /*Stroring data*/
                    if ($this->request->data['User']['pwd']==$this->request->data['User']['confirm_pwd']) {
                        //$userInfo['User']['activation_code'] = "";
                        $userInfo['User']['pwd'] = md5($this->request->data['User']['pwd']);
                        $this->User->save($userInfo, false);
                        $mysqldate = date('Y-m-d H:i:s');                                              
                        $qr_togen = "0;".$userInfo['User']['id'].";".$userInfo['User']['lgn'].";".$userInfo['User']['email'].";0;".$mysqldate;
                        $this->User->generate_and_save_new_qr($qr_togen, $userInfo['User']['email'], $mysqldate);
            
                        $this->render('passwordhasbeenchanged');
                    } else{
                        $this->logErr('error occured: Incorrect Activation code');
                        $this->set("Error", true);
                    }
                }

            } else {
                $this->logErr('error occured: Incorrect Activation code');
                $this->set("Error", true);
            }

            $this->set('actCode', $actCode);
        } else {
            $this->logErr('error occured: Incorrect Activation code');
            $this->redirect("/");
        }
    }

    /**
   * View User information
   *
   * @param string $login
   */
    function view($login=null)
    {

        $this->Access->checkAccess('User', 'r');
        $canSeeAllPar =$this->Access->getAccess('UserCanSeeAll', 'r');

        if ($login) {
            $login = urldecode($login);
            $contain = array();
            if ($canSeeAllPar) {
                $this->User->recursive = 1;
                $contain = array('Address','Address.Country','Address.Provincestate','Phone');

            } else {
                $this->User->recursive = -1;
            }
            $user = $this->User->find('first', array('conditions'=>array('lgn'=>$login),'contain'=>$contain));
            if(empty($user)) {
                $this->logErr('error occured: Incorrect login.');
                $this->Session->setFlash('Invalid User.', 'flash_error');
                $this->redirect("/");
            }

            if ($canSeeAllPar) {
                //Getting Teams
                $Team   = ClassRegistry::init('Team');
                $teams = $Team->getUserTeams($user['User']['id']);
                unset($Team);
                $this->set('teams', $teams);
                //Getting signups
                $Signup   = ClassRegistry::init('Signup');
                $signups = $Signup->find('all', array('conditions'=>array('Signup.user_id'=>$user['User']['id']),'contain'=>array('Event')));
                unset($Signup);
                $this->set('signups', $signups);
            }

            $this->set(compact('user'));

        } else {
            $this->logErr('error occured: Empty login.');
            $this->redirect("/");
        }

        $this->set('canSeeAllPar', $canSeeAllPar);
    }

    /**
   * Reactivation
   * @author vovich
   * @param $session['ActivationUserID']
   */
    function reactivate()
    {
        if($this->Session->check('ActivationUserID')) {
            $id = $this->Session->read('ActivationUserID');
            $this->User->recursive = -1;
            $this->request->data = $this->User->findbyId($id);
            if (empty($this->request->data)) {
                $this->Session->setFlash('We can not find your email in our DB.', 'flash_error');
                $this->redirect("/activation");
            }
            //Sending Activation code
            if (!empty($this->request->data['User']['firstname'])) {
                $username = $this->request->data['User']['firstname']." ".$this->request->data['User']['lastname'];
            } else {
                $username = $this->request->data['User']['lgn'];
            }
            $result = $this->sendMailMessage(
                'ActivationEmail', array(
                '{USERNAME}'      => $username,
                     '{EMAIL}'         => $this->request->data['User']['email'],
                     '{LINK}'          => MAIN_SERVER . "/activation/{$this->request->data['User']['activation_code']}"
                      ),
                $this->request->data['User']['email']
            );
            $this->Session->setFlash('Email has been sent to you.', 'flash_success');
            $this->redirect("/activation");
        }else{
            $this->Session->setFlash('Invalid User.', 'flash_error');
            $this->redirect("/activation");
        }
    }

    function autoComplete($isEmail = null) 
    {
        Configure::write('debug', '0');
           $this->layout = false;
           $this->User->recursive = -1;

        if (!$isEmail) {
             $users = $this->User->find(
                 'all', array(
                 'conditions'=>array(
                 'User.lgn LIKE'=>
                 $this->request->params['url']['q'].'%'),
                 'fields'=>array(
                 'lgn','email' ,'id'))
             );
        } else {
            $users = $this->User->find(
                'all', array(
                'conditions'=>array(
                'User.email LIKE'=>
                $this->request->params['url']['q'].'%'),
                'fields'=>array(
                'email', 'id','lgn'))
            );

        }
               $this->set('isEmail', $isEmail);
        $this->set('users', $users);
    }
    /**
     * New profile Page
     * @author Oleg D.
     */ 
     
    function profile($userLogin = null, $userID = 0) 
    {
        if ($userLogin) {
            $user = $this->User->find('first', array('conditions' => array('User.lgn' => $userLogin, 'User.is_deleted <>' => 1)));
        } else {
            $user = $this->User->find('first', array('conditions' => array('User.id' => $userID, 'User.is_deleted <>' => 1)));
        }
        if (empty($user['User']['id'])) {
            $this->Session->setFlash('There is no user with such login', 'flash_error');
            $this->redirect("/");
        }
        if ($user['User']['is_hidden'] && !$this->Access->getAccess('Album', 'u', $userID)) {
            $this->Session->setFlash('Access error. This profile is hidden.', 'flash_error');
            $this->redirect("/");
        }
        
        $userID = $user['User']['id'];
        $userLogin = $user['User']['lgn'];

        $this->noCache();
        $loggedUserID = $this->getUserID();
        $isAdmin = 0;
        $myProfile = 0;
        if (!$userID || $userID == $loggedUserID) {
            $myProfile = 1;
            $userID = $loggedUserID;
        }

        if ($this->Access->getAccess('Album', 'u', $userID)) {
            $isAdmin = 1;
        }
     
        $userChart = $this->User->Team->prepareUserChart($userID, 15);
        $teamIDs = $this->User->Team->Teammate->find('list', array('conditions' => array('user_id' => $userID,'status'=>array('Pending','Creator','Accepted')), 'fields' => array('team_id','team_id')));
        $teams = array();
        if (!empty($teamIDs)) {
            $teams = $this->User->Team->find('all', array('contain' => array('User' => array('Address' => array('Provincestate', 'conditions' => array('Address.label' => 'Home', 'Address.is_deleted' => 0)))), 'conditions' => array('Team.id' => $teamIDs)));
            foreach ($teams as $key => $team) {
                $teams[$key]['Team']['averageWin'] = $this->User->Team->calcAverageWins($team['Team']['total_wins'], $team['Team']['total_losses']);
                $teams[$key]['Team']['averageCupdif'] = $this->User->Team->calcAverageCupdif($team['Team']['total_wins'], $team['Team']['total_losses'], $team['Team']['total_cupdif']);
            }
        }
        
        $userStats = $this->User->Team->getPlayerStats($userID);
       
        // User's Oregnizatiions block
        $OrganizationsUser = ClassRegistry::init('OrganizationsUser');
        $orgIDs = $OrganizationsUser->find('list', array('fields'=> array('organization_id', 'organization_id'), 'conditions' => array('user_id' => $userID, 'status' => 'accepted')));
        $Organization = ClassRegistry::init('Organization');
        $organizations = $Organization->find('all', array('contain' => array('Creator', 'Address' => array('Provincestate'), 'Image'), 'conditions' => array('Organization.id' => $orgIDs, 'Organization.is_deleted' => 0)));
        // EOF User's Oregnizatiions block
        $this->Rankinghistory->recursive = -1;
        $Rankinghistory = $this->Rankinghistory->find('first');
        
        $userRanking = $this->Ranking->find(
            'first', array(
            'conditions'=>array(
                'Ranking.model_id'=>$userID,
                'Ranking.model'=>'User',
                'Ranking.history_id'=>$Rankinghistory['Rankinghistory']['id']),
            'contain'=>array('Rankinghistory'),
            'order'=>array('Rankinghistory.id'=>'DESC'))
        );
          // return $this->returnJSONResult($userRanking);
//        if (empty($user['User']['qr_image'])) {  //generate qr code
//            $mysqldate = date('Y-m-d H:i:s');
//            $qr_togen = "0;" . $user['User']['id'] . ";" . $user['User']['lgn'].";" . $user['User']['email'].";0;" . $mysqldate;
//            $this->User->generate_and_save_new_qr($qr_togen, $user['User']['email'], $mysqldate);
//
//            $user = $this->User->find('first', array('conditions' => array('User.lgn' => $userLogin, 'User.is_deleted <>' => 1)));
//        }

           $this->set(compact('userRanking', 'userChart', 'isAdmin', 'myProfile', 'user', 'userID', 'teams', 'userStats', 'organizations'));
    }
    /**
     * Delete Avatar
     * @author Oleg D.
     */
    function deleteAvatar() 
    {
        $userID = $this->getUserID();
        if ($userID !=VISITOR_USER) {

            $this->User->deleteAvatar($userID);
            $this->Session->write('loggedUser.avatar', '');
        }
        $this->Session->setFlash('Your avatar has been deleted', 'flash_success');
        $this->redirect('/users/settings/');

    }

    /**
   * Show all users
   * @author vovich
   */
    function show_all() 
    {

        /* filter Getting data from the session or from the form*/
        if(!empty($this->request->data['UserFilter'])) {
            $this->Session->write('UserFilterAll', $this->request->data['UserFilter']);
        }elseif($this->Session->check('UserFilterAll')) {
            $this->request->data['UserFilter']=$this->Session->read('UserFilterAll');
        }
        $conditions = array('User.is_deleted' => 0, 'User.is_hidden' => 0);
        if (!empty ($this->request->data['UserFilter'])) {
            foreach ($this->request->data['UserFilter'] as $key => $val) {
                $this->request->data['UserFilter'][$key] = trim($val);
            }
        }
        //Prepare data for the filter
        if (!empty( $this->request->data['UserFilter']['firstname'])) {
            $conditions['User.firstname LIKE'] = '%' . $this->request->data['UserFilter']['firstname'] . '%'; 
        }
        if (!empty( $this->request->data['UserFilter']['lastname'])) {
            $conditions['User.lastname LIKE'] = '%' . $this->request->data['UserFilter']['lastname'] . '%'; 
        }
        if (!empty( $this->request->data['UserFilter']['lgn'])) {
            $conditions['User.lgn LIKE'] = '%' . $this->request->data['UserFilter']['lgn'] . '%'; 
        }
        if (!empty( $this->request->data['UserFilter']['email'])) {
            $conditions['User.email LIKE'] = $this->request->data['UserFilter']['email']; 
        }
        if (!empty( $this->request->data['UserFilter']['city'])) {
            $conditions['Address.city'] = $this->request->data['UserFilter']['city']; 
        }
        if (!empty( $this->request->data['UserFilter']['provincestate_id'])) {
            $conditions['Address.provincestate_id'] = $this->request->data['UserFilter']['provincestate_id']; 
        }
        if (!empty( $this->request->data['UserFilter']['status'])) {
            $conditions['Status.id'] = $this->request->data['UserFilter']['status']; 
        }
        if (!empty( $this->request->data['UserFilter']['searchby'])) {
            $this->User->compare = $this->request->data['UserFilter']['searchby']; 
        }


        $this->paginate['User']['conditions'] = $conditions;
        $this->paginate['User']['order'] = array('User.created' => 'desc');
        $this->paginate['User']['contain']    = array('Status', 'reset'=>false);
        $this->User->usePaginate = "custom";
        $users = $this->paginate('User');



        $this->set('users', $users);

        $states = $this->Provincestate->find('list', $conditions);
        if(!empty($states)) {
            $states=array('0'=>"Select one")+$states; 
        }
        else {
            $states=array('0'=>"Select one"); 
        }
        $this->set('states', $states);
    }

    /**
     * Back redirect
     * @author Oleg D.
     */
    function backRedirect() 
    {
        $backURL = '/';
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $backURL = $_SERVER['HTTP_REFERER'];
        }
        $this->redirect($backURL);
        exit;
    }
    
    /**
     * Back url for twitter, facebook API
     * @author Oleg D.
     */
    function apiBackURL() 
    {
        $backURL = '/';
        $previousURL = $this->Session->read('previous_url');
        $this->Session->delete('previous_url');
        switch ($previousURL) {
        case 'store_login' : {
            $backURL = SECURE_SERVER . '/checkout';               
                break;    
            }
        default: {
            $backURL = $previousURL;
        }
        }
        if (empty($backURL)) {
            $backURL = '/';
        }
        return $backURL; 
    }        

    /**
     * Redirect to facebook connect page
     * @author Oleg D.
     */
    function fb_connect($redirect = null, $permissionsType = 'user') 
    {
        $this->layout = false;
        set_time_limit(500); 
             
        if ($redirect) {
            $this->Session->write('previous_url', $redirect);   
        } elseif ($permissionsType != 'admin') {
            $this->Session->write('previous_url', $_SERVER['HTTP_REFERER']);       
        }
        
        App::import('Vendor', 'facebook');
        $Facebook = new Facebook(array('appId' => FACEBOOK_API_KEY, 'secret' => FACEBOOK_SECRET_KEY, 'cookie'=>true));

        // generate facebook session
        $facebook_session = $Facebook->getSession();
        // generate login url
        $fb_params = array();
        //http://developers.facebook.com/docs/reference/api/permissions/
        if ($permissionsType == 'user') {
            $fb_params['req_perms'] = "email,read_stream,user_birthday";
        } elseif ($permissionsType == 'admin') {
            $fb_params['req_perms'] = "email,read_stream,user_birthday,publish_stream,create_event,offline_access,manage_pages";            
        }
        $fb_params['next'] = 'http://www.beerpong.com/users/fb_connect_callback/' . $permissionsType;

        $login_url = $Facebook->getLoginUrl($fb_params);
        return $this->redirect($login_url);
    }
    
    /**
     * Collback facebook page
     * @author Oleg D.
     */
    function fb_connect_callback($permissionsType) 
    {
        set_time_limit(500);
        $this->noCache();
        App::import('Vendor', 'facebook');
        
        $backURL = $this->apiBackURL();
        
        $Facebook = new Facebook(array('appId' => FACEBOOK_API_KEY, 'secret' => FACEBOOK_SECRET_KEY, 'cookie' => true));
        $this->User->recursive = -1;
        $facebook_session = $Facebook->getSession();
        $this->Session->write('facebook_session', (array)$facebook_session);
        $this->Session->write('facebook_session_' . $permissionsType, (array)$facebook_session);        
        $FacebookUser = json_decode($this->file_get_contents_curl('https://graph.facebook.com/me?access_token='.$facebook_session['access_token']));                
        if(!empty($facebook_session)) {
            try{
                $FacebookUser = json_decode($this->file_get_contents_curl('https://graph.facebook.com/me?access_token='.$facebook_session['access_token']));
            }catch(FacebookApiException $e){
                $this->Session->setFlash($e, 'flash_error');
                  return $this->redirect("/");
            }

            if(!empty($FacebookUser) && $FacebookUser->id && $FacebookUser->email) {
                $this->User->recursive = -1;
                $likeUserInfo = array();

                // Find User by Facebook ID and login
                $userInfo = $this->User->find('first', array('conditions' => array('User.facebook_id' => $FacebookUser->id, 'User.is_deleted <> ' => 1)));
                if (!empty($userInfo['User']['id'])) {
                    $this->Access->loggining($userInfo['User']['id'], 1);
                    $this->Session->write('FacebookUser', (array)$FacebookUser);
                    return $this->redirect($backURL);
                    // There is no Username with such Facebook ID
                } else {
                    $likeUserInfo = $this->User->find('first', array('conditions' => array('User.email' => $FacebookUser->email, 'User.is_deleted <> ' => 1)));
                }
                $this->Session->write('FacebookUser', (array)$FacebookUser);
                $this->set('likeUserInfo', $likeUserInfo);

                $this->render();
            } else {
                $this->Session->setFlash('Sorry, we could not authenticate you.  Error code: f.2', 'flash_error');
                  return $this->redirect("/");
            }
        }else{
            $this->Session->setFlash('Sorry, we could not authenticate you.  Error code: f.3', 'flash_error');
              return $this->redirect("/");
        }
    }
    /**
     * Facebook connect finish Page
     * @author Oleg D.
     */
    function fb_connect_finish($authorizeType, $userID = 0) 
    {
        $backURL = $this->apiBackURL();

        $facebookUser = $this->Session->read('FacebookUser');

        if (empty($facebookUser['id'])) {
            $this->Session->setFlash('Sorry, we could not authenticate you.  Error code: f.4', 'flash_error');
              return $this->redirect("/");
        }
        $this->Session->delete('FacebookUser');
        $this->User->recursive = -1;
        switch ($authorizeType) {
        case 'user':  // Select created user
            if ($this->User->find('count', array('conditions' => array('User.id' => $userID, 'User.email' => $facebookUser['email'])))) {

                $this->User->validate = array();
                $this->User->save(array('id' => $userID, 'facebook_id' => $facebookUser['id']));

                $this->Access->loggining($userID, 1);
                return $this->redirect($backURL);
            } else {
                $this->Session->setFlash('Sorry, we could not authenticate you.  Error code: f.5', 'flash_error');
                return $this->redirect("/");
            }
            break;

        case 'login': // Login as created user
            if ($this->login_api($this->request->data['User']['userlogin'], $this->request->data['User']['userpwd']) == 'ok') {
                $userInfo = $this->User->find('first', array('conditions' => array('User.lgn' => $this->request->data['User']['userlogin'])));

                $this->User->validate = array();
                $this->User->save(array('id' => $userInfo['User']['id'], 'facebook_id' => $facebookUser['id']));

                $this->Access->loggining($userInfo['User']['id'], 1);
                return $this->redirect($backURL);
            } else {
                $this->Session->setFlash('Incorrect login or password', 'flash_error');
                $this->goBack();
                exit;
            }
            break;            
        case 'new':  // Create new User

            if ($facebookUser['gender'] == 'male') {
                $gender = 'M';
            } else {
                $gender = 'F';
            }
            $createUser = array();
            $createUser['email'] = $facebookUser['email'];
            $createUser['facebook_id'] = $facebookUser['id'];
            $createUser['firstname'] = $facebookUser['first_name'];
            $createUser['lastname'] = $facebookUser['last_name'];
            $createUser['gender'] = $gender;
            $createUser['lgn'] = $this->genRandomString(15);
                 

            // user should enter new email address
            if ($userID) {
                if (!empty($this->request->data['User']['email']) && !$this->User->find('count', array('conditions' => array('User.email' => $this->request->data['User']['email'])))) {
                    if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $this->request->data['User']['email'])) {
                        $this->Session->setFlash('Incorrect Email', 'flash_error');
                        $this->goBack();
                    } else {
                            $createUser['email'] = $this->request->data['User']['email'];
                    }
                } else {
                    $this->Session->setFlash('User with such Email already exist', 'flash_error');
                    $this->goBack();
                }
            }
            // Get Avatar from Facebook !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! TO FIX BT RACKSPACE AND WITHOUT file_put_contents
            /*
            // Get Avatar from Facebook
            $img = $this->file_get_contents_curl('http://graph.facebook.com/' . $facebookUser['id'] . '/picture?type=large');
            if ($img) {
            $file = WWW_ROOT . 'img' . DS . 'avatars' . DS . $facebookUser['id'] . '.jpg';
            if (file_put_contents($file, $img)) {
            $avatarFile = $this->User->uploadAvatar($this->genRandomString(7), $file);
		        		@unlink($file);
		        		if ($avatarFile) {
            $createUser['avatar'] = $avatarFile;
		        		}
            }
            }
            */
            if (!empty($facebookUser['birthday'])) {
                $bdate = explode('/', $facebookUser['birthday']);
                $bdate = $bdate[2] . '-'. $bdate[0] . '-' . $bdate[1];
                $createUser['birthdate'] = $bdate;
            }
            $password = $this->genRandomString(6);
            $createUser['pwd'] = md5($password);

            $this->User->create();
            $this->User->validate = array();
            if ($this->User->save($createUser)) {

                $userID = $this->User->getLastInsertID();
                if ($this->User->find('count', array('conditions' => array('User.lgn' => $this->escapeURL($facebookUser['username']))))) {
                    $newLogin = $this->escapeURL($facebookUser['username']);
                } else {
                    $newLogin = 'user' . $userID;
                }

                $this->User->validate = array();
                $this->User->save(array('id' => $userID, 'lgn' => $newLogin));

                $sql = "INSERT INTO users_statuses (user_id,status_id) VALUES ($userID," . ACTIVE_STATUS_ID . ")";
                $this->User->query($sql);

                $result = $this->sendMailMessage(
                    'FBandTwitterRegistration', array(
                          '{LOGIN}'      => $newLogin,
                          '{PASSWORD}'      => $password,
                         '{EMAIL}'         => $createUser['email'],
                          ), $createUser['email']
                );


                $this->Session->setFlash('Your User Name: ' . $newLogin . '. You can change it.', 'flash_success');
                $this->Access->loggining($userID, 1);

                return $this->redirect($backURL);
            } else {
                $this->Session->setFlash('Sorry, we could not authenticate you.  Error code: f.1', 'flash_error');
                return $this->redirect("/");
            }
            //escapeURL
            break;

        }
        exit;
    }

    /**
     * Collback twitter page
     * @author Oleg D.
     */
    function twitter_connect_callback() 
    {
        set_time_limit(500);
        $this->noCache();
        $this->User->recursive = -1;
        $backURL = $this->apiBackURL();
        
        /* If the oauth_token is old redirect to the connect page. */
        if (empty($_REQUEST['oauth_token'])) {
            $this->Session->setFlash('Sorry, we could not authenticate you.  Error code: t.1', 'flash_error');
              return $this->redirect("/");
        }

        /* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
        App::import('Vendor', 'TwitterOAuth', array('file' => 'twitter' . DS . 'twitteroauth.php'));

        /* Build TwitterOAuth object with client credentials. */
        $Connection = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_SECRET_KEY, $this->Session->read('oauth_token'), $this->Session->read('oauth_token_secret'));

        $this->Session->delete('oauth_token');
        $this->Session->delete('oauth_token_secret');

        /* Request access tokens from twitter */
        $accessToken = $Connection->getAccessToken($_REQUEST['oauth_verifier']);

        if (empty($accessToken['user_id'])) {
            $this->Session->setFlash('Sorry, we could not authenticate you. Error code: t.2', 'flash_error');
              return $this->redirect("/");
        }

        $TwitterUser = $Connection->get('http://api.twitter.com/1/users/show.json', array('id' => $accessToken['user_id']));
        if(!empty($TwitterUser->id)) {
            $this->User->recursive = -1;
            $userInfo = $this->User->find('first', array('conditions' => array('User.twitter_id' => $TwitterUser->id, 'User.is_deleted <> ' => 1)));
            if (!empty($userInfo['User']['id'])) {
                $this->Access->loggining($userInfo['User']['id'], 1);
                return $this->redirect($backURL);
            }
            // There is no Username with such Twitter ID

            $this->Session->write('TwitterUser', (array)$TwitterUser);
            $this->render();

        } else {
            $this->Session->setFlash('Sorry, we could not authenticate you.  Error code: t.3', 'flash_error');
             return $this->redirect("/");
        }
    }

    /**
     * Twitter connect finish Page
     * @author Oleg D.
     */
    function twitter_connect_finish($authorizeType) 
    {
        
        $backURL = $this->apiBackURL();
        $this->User->recursive = -1;                
        $twitterUser = $this->Session->read('TwitterUser');
        $this->Session->delete('TwitterUser');

        if (empty($twitterUser['id'])) {
            $this->Session->setFlash('Sorry, we could not authenticate you.  Error code: t.4', 'flash_error');
              return $this->redirect("/");
        }
        switch ($authorizeType) {
        case 'login': // Login as created user
            if ($this->login_api($this->request->data['User']['userlogin'], $this->request->data['User']['userpwd']) == 'ok') {
                $userInfo = $this->User->find('first', array('conditions' => array('User.lgn' => $this->request->data['User']['userlogin'])));

                $this->User->validate = array();
                $this->User->save(array('id' => $userInfo['User']['id'], 'twitter_id' => $twitterUser['id']));

                $this->Access->loggining($userInfo['User']['id'], 1);
                return $this->redirect($backURL);
            } else {
                $this->Session->setFlash('Incorrect login or password', 'flash_error');
                return $this->redirect('/users/twitter_connect/');
                exit;
            }
            break;
        case 'new':  // Create new User
            if (empty($this->request->data['User']['email']) || $this->User->find('count', array('conditions' => array('User.email' => $this->request->data['User']['email'])))) {
                $this->Session->setFlash('User with such Email already exist', 'flash_error');
                return $this->redirect('/users/twitter_connect/');
            }
            if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $this->request->data['User']['email'])) {
                $this->Session->setFlash('Incorrect Email', 'flash_error');
                return $this->redirect('/users/twitter_connect/');
            }

            $createUser = array();
            $createUser['twitter_id'] = $twitterUser['id'];
            $createUser['firstname'] = $twitterUser['name'];
            $createUser['email'] = $this->request->data['User']['email'];
            $createUser['lgn'] = $this->genRandomString(15);

            // Get Avatar from Facebook !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! TO FIX BT RACKSPACE AND WITHOUT file_put_contents
            /*
            if(!empty($twitterUser['profile_image_url'])) {
            $img = $this->file_get_contents_curl($twitterUser['profile_image_url']);
            if ($img) {
            $file = WWW_ROOT . 'img' . DS . 'avatars' . DS . $twitterUser['id'] . '_twit.jpg';
            if (file_put_contents($file, $img)) {
               $avatarFile = $this->User->uploadAvatar($this->genRandomString(7), $file);
               @unlink($file);
               if ($avatarFile) {
                $createUser['avatar'] = $avatarFile;
               }
            }
            }
            }
            */
            $password = $this->genRandomString(6);
            $createUser['pwd'] = md5($password);

            $this->User->create();
            $this->User->validate = array();
            if ($this->User->save($createUser)) {

                $userID = $this->User->getLastInsertID();
                if ($this->User->find('count', array('conditions' => array('User.lgn' => $this->escapeURL($twitterUser['screen_name']))))) {
                    $newLogin = $this->escapeURL($twitterUser['screen_name']);
                } else {
                    $newLogin = 'user' . $userID;
                }

                $this->User->validate = array();
                $this->User->save(array('id' => $userID, 'lgn' => $newLogin));

                $sql = "INSERT INTO users_statuses (user_id,status_id) VALUES ($userID," . ACTIVE_STATUS_ID . ")";
                $this->User->query($sql);

                $result = $this->sendMailMessage(
                    'FBandTwitterRegistration', array(
                          '{LOGIN}'      => $newLogin,
                          '{PASSWORD}'      => $password,
                         '{EMAIL}'         => $createUser['email'],
                          ), $createUser['email']
                );


                $this->Session->setFlash('Your User Name: ' . $newLogin . '. You can change it.', 'flash_success');
                $this->Access->loggining($userID, 1);

                return $this->redirect($backURL);
            } else {
                $this->Session->setFlash('Sorry, we could not authenticate you.  Error code: f.1', 'flash_error');
                return $this->redirect("/");
            }

            break;

        }
        exit;
    }
    /**
     * Remove related account
     * @author Oleg D.
     */
    function remove_related_account($accountType, $userID) 
    {
        $this->Access->checkAccess('User', 'u', $userID);
        if ($accountType == 'facebook') {
            $this->User->validate = array();
            $this->User->save(array('id' => $userID, 'facebook_id' => null));
        } elseif ($accountType == 'twitter') {
            $this->User->validate = array();
            $this->User->save(array('id' => $userID, 'twitter_id' => null));
        }
        $this->goBack();

    }    
    /**
     * Users stats page
     * @author Oleg D.
     */
    function stats($userID) 
    {
        $user = $this->User->find('first', array('conditions' => array('User.id' => $userID, 'User.is_deleted <>' => 1), 'contain' => array('Address' => array('Provincestate', 'conditions' => array('Address.label' => 'Home', 'Address.is_deleted' => 0)))));
        
        if (empty($user['User']['id'])) {
            $this->Session->setFlash('There is no user with such login', 'flash_error');
            $this->redirect("/");
        }        
        if ($user['User']['is_hidden'] && $user['User']['id'] != $this->getUserID()) {
            $this->Session->setFlash('Access error. This profile is hidden.', 'flash_error');
            $this->redirect("/");
        }
                
        $user['stats'] = $this->User->Team->getPlayerStats($userID);
        $user['User']['Address'] = $user['Address'];
        //pr($user);
        //exit;
        
        if(!empty($this->request->data['GamesSearch'])) {
            $this->Session->write('games_user_search', $this->request->data['GamesSearch']);
            $this->passedArgs['games_user_search'] = 1;
        }elseif($this->Session->check('games_user_search')) {
            if (!empty($this->passedArgs['games_user_search'])) {
                $this->request->data['GamesSearch'] = $this->Session->read('games_user_search');               
            } else {
                $this->Session->delete('games_user_search');    
            }
        }

           $userTeams = $this->User->Teammate->find(
               'list', array('conditions' => 
               array('user_id' => $userID, 'status <> '=>'Deleted'), 'fields' => array('team_id', 'team_id'))
           );
        if (!empty($this->request->data['GamesSearch']['game_type'])) {

            $teamConditions = array('Team.id' => $userTeams);
            
            if ($this->request->data['GamesSearch']['game_type'] == 'team') {
                $teamConditions['Team.people_in_team >'] = 1;     
            } else {
                $teamConditions['Team.people_in_team'] = 1;    
            }
            $userTeams = $this->User->Team->find('list', array('conditions' => $teamConditions, 'fields' => array('Team.id', 'Team.id')));
        }
        $conditionsGames = array('OR' => array('team1_id' => $userTeams, 'team2_id' => $userTeams), 'AND' => array('Game.status' => 'Completed'));                
        
        //$chartConditions = array();
                        
        if (!empty($this->request->data['GamesSearch']['date_from'])) {
            $conditionsGames['AND']['Game.created >='] = date('Y-m-d', strtotime($this->request->data['GamesSearch']['date_from'])) . '00:00';
            //$chartConditions['date_from'] = date('Y-m-d', strtotime($this->request->data['GamesSearch']['date_from'])) . '00:00';				
        }
        if (!empty($this->request->data['GamesSearch']['date_to'])) {
            $conditionsGames['AND']['Game.created <='] = date('Y-m-d', strtotime($this->request->data['GamesSearch']['date_to'])) . ' 24:00';
            //$chartConditions['date_to'] = date('Y-m-d', strtotime($this->request->data['GamesSearch']['date_to'])) . ' 24:00';				
        }        
        if (!empty($this->request->data['GamesSearch']['event_id'])) {
            $conditionsGames['AND']['Game.event_id'] = $this->request->data['GamesSearch']['event_id']; 
            //$chartConditions['event_id'] = $this->request->data['GamesSearch']['event_id'];	
        }
            
        if (!empty($this->request->data['GamesSearch']['opponent_id'])) {
            $conditionsGames['AND']['OR'] = array('team1_id' => $this->request->data['GamesSearch']['opponent_id'], 'team2_id' => $this->request->data['GamesSearch']['opponent_id']);    
            //$chartConditions['opponent_id'] = $this->request->data['GamesSearch']['opponent_id'];
        }
                          
        $this->paginate = array(
         'limit' => 15,
         'contain' => array('Event' => array('Venue' => array('Address' => array('Provincestate'))), 'Team1', 'Team2', 'Brackettype'),
         'order' => array('Game.created' => 'DESC', 'Game.id' => 'DESC'),
         'conditions' => $conditionsGames
        
        );    
        
        $games = $this->paginate('Game');

        $gameEvents = $gameOpponents = array();        
        
        $this->Game->contain(array('Event'));
        $gameEvents = $this->Game->find(
            'all', 
            array(
                'contain' => array('Event'),
                'fields' => array('Event.id', 'Event.name'), 
                'conditions' => array('OR' => array('team1_id' => $userTeams, 'team2_id' => $userTeams), 'AND' => array('Game.status' => 'Completed')),
            )
        );
        $gameEvents = Set::combine($gameEvents, '{n}.Event.id', '{n}.Event.name');
        
        $gameOpponents = $this->Game->getTeamsOpponents(0, $userTeams);         
        
        $gameIDs = Set::combine($games, '{n}.Game.id', '{n}.Game.id');        
        if (empty($gameIDs)) {
            $gameIDs = array('0' => '0');
        }
        $userRanking = $this->Ranking->find(
            'first', array(
            'conditions'=>array('Ranking.model_id'=>$userID,'Ranking.model'=>'User'),
            'contain'=>array('Rankinghistory'),
            'order'=>array('Rankinghistory.id'=>'DESC'))
        );
     
                
        $userChart = $this->User->Team->prepareUserChart($userID, 15, array(), $gameIDs);
        $this->set(compact('user', 'userChart', 'userRanking', 'gameEvents', 'gameOpponents', 'games', 'userTeams', 'userTeams'));
        
    }
    //This is not working yet, but thats ok
    function m_setMobileStatus($status,$amf = 0) 
    {
        if (isset($this->request->params['form']['status'])) {
            $status = $this->request->params['form']['status']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }
        $userid = $this->getUserID();
        if ($userid < 2) {
            return $this->returnMobileResult('You are not logged in.', $amf); 
        }

        if ($this->User->setMobileStatus($userid, $status)) {
            return $this->returnMobileResult('ok', $amf); 
        }
        else {
            return $this->returnMobileResult('problem', $amf); 
        }
    }    
     // mobile authentication function
    // both authenticates & returns a User object as JSON to the client (speeds things up)
    // returns a JSON string of the user requested
    // and a session variable?
    // by duncan@bpong.com
    function m_login($email=null, $pass=null, $deviceid=null, $c2dm_key=null, $amf = 0) 
     {
        Configure::write('debug', '0');
      
        if (!empty($this->request->params['form']['email'])) {
            // get POST data
            $email = mysql_escape_string($this->request->params['form']['email']);
            $pass = mysql_escape_string($this->request->params['form']['pass']);
            $deviceid = mysql_escape_string($this->request->params['form']['deviceid']);
            $c2dm_key = mysql_escape_string($this->request->params['form']['c2dm_key']);
            $amf = mysql_escape_string($this->request->params['form']['amf']);       
        }            
      
        $BannedDevice = ClassRegistry::init('Banneddevice');
        $isBanned = $BannedDevice->isBanned($deviceid);      
        if (!empty($isBanned['banreason'])) {
              $result = "your device was banned because " . $isBanned['banreason'];
              return $this->returnJSONResponse($result);
        }                   
        //now, authenticate. note that 'userid' could be email
        $validEmail = '/\\A(?:^([a-z0-9][a-z0-9_\\-\\.\\+]*)@([a-z0-9][a-z0-9\\.\\-]{0,63}\\.(com|org|net|biz|info|name|net|pro|aero|coop|museum|[a-z]{2,4}))$)\\z/i';
        $userInfo = array();
        if (preg_match($validEmail, $email)) {
             $conditions = array('email'=>$email,'pwd' => $pass);
        } else {
            $conditions = array('lgn'=>$email,'pwd'=>$pass);
        }
        $this->User->recursive = -1; 
        $userInfo   = $this->User->find(
            'first', array('conditions'=>$conditions,
            'fields'=>array('id','lgn','firstname','lastname', 'qr_string','deviceid', 'email'))
        );
        if (!empty($userInfo['User']['id'])) {          
        
            if (!$this->Access->loggining($userInfo['User']['id'], false)) {
                return $this->returnJSONResult("Could not log you in"); 
            }                              
     
            //Get a singles team. We're not gonna do anything with it, but we want to make sure that everyone has one.
            $this->User->Team->getSinglesTeam($userInfo['User']['id']); 
        
            // register our c2dm code
            if (!empty($c2dm_key)) { 
                $this->User->validate = array();
                $this->User->save(array('id'=>$userInfo['User']['id'],'c2dm_key'=>$c2dm_key));
                //$this->User->id = $userInfo['User']['id'];
                //$this->User->saveField('c2dm_key', $c2dm_key);
            }                                    
            if (empty($userInfo['User']['qr_string'])) {
                $mysqldate = date('Y-m-d H:i:s');            // needs semicolon between day & time or will break     (duncan)
                $qr_togen = "0;".$userInfo['User']['id'].";".$userInfo['User']['lgn'].";".$userInfo['User']['email'].";0;".$mysqldate;
                $this->User->generate_and_save_new_qr($qr_togen, $userInfo['User']['email'], $mysqldate);
            }
            $isFirstLogin = 0;
            // check if the deviceid field of our user is empty
            //$user_deviceid = $this->User->findByDeviceid()
            $db_deviceID = $userInfo['User']['deviceid'];
            
            // if user has no device id then it is their first time ever installing    
            if (!$db_deviceID) {
                $isFirstLogin = 1;
                // in that case, store their deviceid in the DB and log them in
                $this->User->validate = array();
                $this->User->save(array('id'=>$userInfo['User']['id'],'deviceid'=>$deviceid));
            }
            $this->User->recursive = 0;
            $result = $this->User->find(
                'first', array('conditions'=>array(
                'User.id'=>$userInfo['User']['id']),
                'contain'=>array('UsersAffil'=>array('Greek','City','Hometown','School')))
            ); 
            $result['User']['is_first_login'] = $isFirstLogin; 
            foreach ($result['UsersAffil'] as $key => $val) {
                //unset the b.s. affils
                if ($val['model'] != 'City') {
                    unset($result['UsersAffil'][$key]['City']);
                }
                if ($val['model'] != 'Hometown') {
                    unset($result['UsersAffil'][$key]['Hometown']);
                }
                if ($val['model'] != 'Greek') {
                    unset($result['UsersAffil'][$key]['Greek']);
                }
                if ($val['model'] != 'School') {
                    unset($result['UsersAffil'][$key]['School']);
                }
            }  
            //now get ranking
            $this->Ranking->recursive = -1;
            $usersRanking = $this->Ranking->find(
                'first', array(
                'conditions'=>array(
                'model'=>'User',
                'model_id'=>$result['User']['id']),
                'order'=>array('history_id'=>'DESC'))
            );
            if ($usersRanking) {
                $result['User']['ranking'] = $usersRanking['Ranking']['rank'];
            }
            //   $result['User']['ranking'] = $this->Ranking->getUserRank($userInfo['User']['id']);
        
        }       
        else {
            $result = "bad username or password";
        }
        return $this->returnMobileResult($result, $amf);
    }
  
    function m_loginAdmin($email=null, $deviceid=null, $amf = 0) 
    {
        Configure::write('debug', '0');
  
        if (!empty($this->request->params['form']['email'])) {
            // get POST data
            $email = mysql_escape_string($this->request->params['form']['email']);
            $deviceid = mysql_escape_string($this->request->params['form']['deviceid']);
            $amf = mysql_escape_string($this->request->params['form']['amf']);
        }
      
        //now, authenticate. note that 'userid' could be email
        $validEmail = '/\\A(?:^([a-z0-9][a-z0-9_\\-\\.\\+]*)@([a-z0-9][a-z0-9\\.\\-]{0,63}\\.(com|org|net|biz|info|name|net|pro|aero|coop|museum|[a-z]{2,4}))$)\\z/i';
        $userInfo = array();
        if (preg_match($validEmail, $email)) {
            $conditions = array('email'=>$email);
        } else {
            $conditions = array('lgn'=>$email);
        }
        $this->User->recursive = -1;
        $userInfo   = $this->User->find(
            'first', array('conditions'=>$conditions,
            'fields'=>array('id','lgn','pwd', 'firstname','lastname', 'qr_string','deviceid', 'email'))
        );
        if (!empty($userInfo['User']['id'])) {
  
            if (!$this->Access->loggining($userInfo['User']['id'], false)) {
                return $this->returnJSONResult("Could not log you in"); 
            }

            if (empty($userInfo['User']['qr_string'])) {
                $mysqldate = date('Y-m-d H:i:s');            // needs semicolon between day & time or will break     (duncan)
                $qr_togen = "0;".$userInfo['User']['id'].";".$userInfo['User']['lgn'].";".$userInfo['User']['email'].";0;".$mysqldate;
                $this->User->generate_and_save_new_qr($qr_togen, $userInfo['User']['email'], $mysqldate);
            }
          
          
            $this->User->recursive = 0;
            $result = $this->User->find(
                'first', array('conditions'=>array(
                      'User.id'=>$userInfo['User']['id']),
                      'contain'=>array('UsersAffil'=>array('Greek','City','Hometown','School')))
            );  
            foreach ($result['UsersAffil'] as $key => $val) {
                //unset the b.s. affils
                if ($val['model'] != 'City') {
                    unset($result['UsersAffil'][$key]['City']);
                }
                if ($val['model'] != 'Hometown') {
                    unset($result['UsersAffil'][$key]['Hometown']);
                }
                if ($val['model'] != 'Greek') {
                    unset($result['UsersAffil'][$key]['Greek']);
                }
                if ($val['model'] != 'School') {
                    unset($result['UsersAffil'][$key]['School']);
                }
            }
          
          
            $result['User']['ranking'] = $this->Ranking->getUserRank($userInfo['User']['id']);
        }
        else {
            $result = "bad username or password";
        }
        return $this->returnMobileResult($result, $amf);
    }  
        
    function emailAdmin_api($subject,$message) 
    {
        //currently, this just emails skinny
        Configure::write('debug', 0);  
        if (!$this->isLoggined()) { 
            return "You are not logged in."; 
        }
        $user = $this->Session->read('loggedUser');
        if ($user['id']==1) { return "You are not logged in."; 
        }     
        $emailFrom = $user['email'];
        App::import('Vendor', 'PHPMailer', array('file' => 'mailer.class.php'));
        $mailer = new PHPMailer();
        $emailto = "skinny@bpong.com";
        $mailer->AddAddress($emailto, $emailto);             
        $mailer->CharSet = 'utf-8';
        $mailer->Subject = $subject;
        $mailer->Body    = $message;
        $mailer->From = 'no-reply@bpong.com';
        $mailer->FromName = $mailer->From; 
        $mailer->ContentType = 'text/plain';
        return $mailer->Send();          
    }
      
    function captcha() 
    {
              $this->render = false;
              $this->layout = false;
              Configure::write('debug', 0);
              $this->noCache();
        $str = "";
        $length = 0;

        $str = $this->genRandomString(5, 'letters');
        //md5 letters and saving them to session
            
        $this->Session->write('captcha_text', md5($str));
            
        //determine width and height for our image and create it
        $imgW = 120;
        $imgH = 60;
        $image = imagecreatetruecolor($imgW, $imgH);
            
        //setup background color and border color
        $backgr_col = imagecolorallocate($image, 255, 255, 255);
        $border_col = imagecolorallocate($image, 255, 255, 255);
            
        //let's choose color in range of purple color
        $text_col = imagecolorallocate($image, 43, 95, 169);
            
        //now fill rectangle and draw border
        imagefilledrectangle($image, 0, 0, $imgW, $imgH, $backgr_col);
        imagerectangle($image, 0, 0, $imgW-1, $imgH-1, $border_col);
            
        //save fonts in same folder where you PHP captcha script is
        //name these fonts by numbers from 1 to 3
        //we shall choose different font each time
        $fn = rand(1, 4);
        $font = "fonts/" . $fn . ".ttf";
            
        //setup captcha letter size and angle of captcha letters
        $font_size = $imgH / 2.2;
        $angle = rand(-15, 15);
        $box = imagettfbbox($font_size, $angle, $font, $str);
        $x = (int)($imgW - $box[4]) / 2;
        $y = (int)($imgH - $box[5]) / 2;
        imagettftext($image, $font_size, $angle, $x, $y, $text_col, $font, $str);
            
        //now we should output captcha image
        header("Content-type: image/png");
        imagepng($image);
        imagedestroy($image);
        exit();    
    }
      /*
       * Mobile New user function 
       */
      /* use m_getOrCreate...
      function m_newUser($email='',$pass='') {
    
	  	//Configure::write('debug', 2);
        if (isset($this->request->params['form']['email'])) {
            $email = mysql_real_escape_string($this->request->params['form']['email']); 
        }		                                                            

    	// pass is -already- MD5'd coming from android client
        if (isset($this->request->params['form']['pass'])) {
    		$pass = mysql_real_escape_string($this->request->params['form']['pass']);
    	}  
    
	
        $exp_array = explode("@", $email);
        $new_nick=$my_nick=$exp_array['0'];
    
        // lgn has to be unique
    $i=1;
      	$nick_unfree=1;
        while ($nick_unfree==1) {
            $this->User->recursive = -1;
            $unfree_user=$this->User->find('count',array('conditions'=>array('lgn'=>$new_nick)));
            if(!$unfree_user){
               $nick_unfree=0;
               break;
            }else{
                $new_nick=$my_nick.$i;
            }
            $i++;
         }

	     $this->request->data['User']['lgn']=$new_nick;
	     $this->request->data['User']['pwd']=$pass;
	     $this->request->data['User']['email']=$email;
      
	 
	     $this->request->data['User']['activation_code'] = $this->ActivationCode(20);

	     $is_exist = $this->User->find('first',array('fields'=>array('id'),'conditions'=>array('activation_code'=>$this->request->data['User']['activation_code'] )));
	     while (!empty($is_exist) ){
	        $this->request->data['User']['activation_code'] = $this->ActivationCode(20);
	        $is_exist = $this->User->find('first',array('fields'=>array('id'),'conditions'=>array('activation_code'=>$this->request->data['User']['activation_code'] )));
	     }
	
	     $this->User->create();
	
	     if ($this->User->save($this->request->data)) {
	        $id = $this->User->getLastInsertID();
	        $this->User->habtmAdd('Status',$id,REGISTRY_STATUS_ID);
	        $this->request->data['User']['id'] = $id;
	        $this->sendMailMessage('NewPlayerAdded', array(
	                         '{LOGIN}'         => $this->request->data['User']['lgn'],
	                         '{PASSWORD}'         => '************',
	                         '{EMAIL}'         => $this->request->data['User']['email'],
	                         '{LINK}'          => "http://{$_SERVER['HTTP_HOST']}/activation/{$this->request->data['User']['activation_code']}"
	
	            ),
	              $this->request->data['User']['email']
	                    );
	         //return $this->request->data['User'];
             
             //Need to change this....
             return $this->m_getUser($email,$pass); 
	         //$this->redirect('/users/view/' . $userInfo['User']['lgn'] . '.json');
	      }
	      else
	        return "Could not save data.";
      }*/
      
      
      /** 
      * This takes an array of user lgn's, and returns the users, if they exist. The Key in the resulting array in sht elgn
      * 
      * @param mixed $userArray
      */
    function getUsersByLgn_api($userArray) 
    {
        Configure::write('debug', 0);
        $this->User->recursive = -1;
        foreach ($userArray as $userLgn) {              
            $user = $this->User->find('first', array('conditions'=>array('lgn'=>$userLgn)));
            if ($user) {
                $results[$userLgn] = $user['User'];
            }
            else {
                $results[$userLgn] = 'Not found';   
            }
        }
        return $results;
    }
      /**
      * This takes an array of 'user objects'. Each user object contains an email address, as well as potentially
      * lgn, firstname, and lastname. If a user with the email exists, that goes in the return array. Otherwise, we
      * create a new user with the provided information, and return it 
      * 
      * @param mixed $userArray
      */
    function getOrCreateUsersByEmail_api($userArray) 
    {
        Configure::write('debug', 0);
        $loggedUserID = $this->getUserID();
        if ($loggedUserID < 2) {
            return "You are not logged in."; 
        }
        $ctr = 0;
        $results = array();
        foreach ($userArray as $userObject) {
            unset($result);
            if (isset($userObject['firstname'])) { $firstname = $userObject['firstname']; 
            }
            else { $firstname = ''; 
            }
            if (isset($userObject['lastname'])) { $lastname = $userObject['lastname']; 
            }
            else { $lastname = ''; 
            }
            if (isset($userObject['lgn'])) { $lgn = $userObject['lgn']; 
            }
            else { $lgn = ''; 
            }
            if (isset($userObject['email'])) {
                $result = $this->addNewPlayer_api($userObject['email'], $lgn, $firstname, $lastname);
                if (isset($result['User'])) {
                    unset($result['User']['pwd']);
                    $results[$ctr] = $result['User'];
                    $ctr++;
                }
                else if (isset($result['newuser'])) {
                    unset($result['newuser']['pwd']);
                    $results[$ctr] = $result['newuser'];
                    $ctr++;
                }
                else {
                    $results[$ctr] = 'Problem';
                    $ctr++;
                }
                
            }  
        }
        return $results;
    }
    function getOrCreateUserByEmail($userObject) 
    {
          
    } 
      /**
      * This is the new User function for the app. Lgn is optional
      * $pass is md5
      */
    function m_newUser($lgn=null,$email=null,$pass=null,$amf = 0) 
    {
        if (isset($this->request->params['form']['lgn'])) {
            $lgn = mysql_real_escape_string($this->request->params['form']['lgn']);
        }
        if (isset($this->request->params['form']['email'])) {
            $email = mysql_real_escape_string($this->request->params['form']['email']);
        }
        if (isset($this->request->params['form']['pass'])) {
            $pass = mysql_real_escape_string($this->request->params['form']['pass']);
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = mysql_real_escape_string($this->request->params['form']['amf']);
        } 
        if ($email == '') { return "bad email"; 
        }
          $validEmail = '/\\A(?:^([a-z0-9][a-z0-9_\\-\\.\\+]*)@([a-z0-9][a-z0-9\\.\\-]{0,63}\\.(com|org|net|biz|info|name|net|pro|aero|coop|museum|[a-z]{2,4}))$)\\z/i';
        if (!preg_match($validEmail, $email)) {
            return $this->returnMobileResult('Not a valid email', $amf);
        }
        if (!is_string($pass) || strlen($pass) < 5) {
            return $this->returnMobileResult('Bad Password', $amf); 
        }
            
            $this->User->recursive = -1;
            $user = $this->User->find(
                'first', array('conditions'=>array(
                'email'=>$email,
                'is_deleted'=>0),'recursive'=>-1)
            );
            if ($user) {
                return $this->returnMobileResult("A user with that email already exists.", $amf);
            }
            if (strlen($lgn) > 0) {
                //If a lgn is provided, check to see if it's unique. If not return error message
                $user = $this->User->find(
                    'first', array('conditions'=>array(
                    'lgn'=>$lgn))
                );
                if ($user) {
                    return $this->returnMobileResult("Username already exists", $amf);
                }  
                $new_nick = $lgn;                                                                  
            }
            else  //generate lgn from email
            {      
                $exp_array = explode("@", $email);
                $new_nick=$my_nick=$exp_array['0'];
                $i=1;
                $nick_unfree=1;
                while ($nick_unfree==1) {
                    $this->User->recursive = -1;
                    $unfree_user=$this->User->find('count', array('conditions'=>array('lgn'=>$new_nick)));
                    if(!$unfree_user) {
                        $nick_unfree=0;
                        break;
                    }else{
                        $new_nick=$my_nick.$i;
                    }
                    $i++;
                }
            }
                    
            $this->request->data['User']['email'] = $email;
            $this->request->data['User']['lgn']=$new_nick;
            $this->request->data['User']['pwd']=$pass;
            $this->request->data['User']['firstname']='';
            $this->request->data['User']['lastname']='';
            $this->request->data['User']['activation_code'] = $this->ActivationCode(20);

            $is_exist = $this->User->find('first', array('fields'=>array('id'),'conditions'=>array('activation_code'=>$this->request->data['User']['activation_code'] )));
            while (!empty($is_exist) ){
                $this->request->data['User']['activation_code'] = $this->ActivationCode(20);
                $is_exist = $this->User->find('first', array('fields'=>array('id'),'conditions'=>array('activation_code'=>$this->request->data['User']['activation_code'] )));
            }

            $this->User->create();

            if ($this->User->save($this->request->data)) {
                $id = $this->User->getLastInsertID();
                $this->User->habtmAdd('Status', $id, REGISTRY_STATUS_ID);
                $this->request->data['User']['id'] = $id;
                //Generate QR Code
                $mysqldate = date('Y-m-d H:i:s');   // needs semicolon between day & time or will break     (duncan)
                $qr_togen = "0;".$this->request->data['User']['id'].";".$this->request->data['User']['lgn'].";".$this->request->data['User']['email'].";0;".$mysqldate;
                $this->User->generate_and_save_new_qr($qr_togen, $this->request->data['User']['email'], $mysqldate);
                $userInfo = $this->User->find('first', array('conditions'=>array('email'=>$this->request->data['User']['email'])));
                $qrimagelink = IMG_GALLERIES_URL.'/'.$userInfo['User']['qr_image'];
                
                $this->sendMailMessage(
                    'ActivationEmail', array(
                                     '{LINK}'          => MAIN_SERVER . "/activation/{$this->request->data['User']['activation_code']}",  
                                     '{QRIMAGE}' => $qrimagelink
                             ),
                    $this->request->data['User']['email']
                ); 
                return $this->returnMobileResult("ok", $amf);
            }
            else {
                return $this->returnMobileResult("Could not save data.", $amf); 
            }

    }
    function test_qr($text,$email) 
    {
        return $this->User->generate_and_save_new_qr($text, $email, date('Y-m-d;H:i:s')); 
    }
      
      
      /*
    * This function returns the user if it exists. If it does not exist, it creates a new account with a randomly 
    * generated password and returns it. 
    * @author: skinny
      */
    function m_getOrCreateUser($email=null, $pass=null, $amf = 0) 
    {
        if (isset($this->request->params['form']['email'])) {
            $email = mysql_real_escape_string($this->request->params['form']['email']);
        }
          
        if (isset($this->request->params['form']['pass'])) {
            $pass = mysql_real_escape_string($this->request->params['form']['pass']);
      
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = mysql_real_escape_string($this->request->params['form']['amf']);
        }                                                        
        $result = $this->getOrCreateUser($email, $pass);
        return $this->returnMobileResult($result, $amf);
    }
      /*
    * This function returns the user if it exists. If it does not exist, it creates a new account with 
    * the supplied password
    * @author: skinny
      */
    function m_getPlayerFromEmailAndPassword($email=null, $pass=null, $amf = 0)   
    {
        if (isset($this->request->params['form']['email'])) {
            $email = mysql_real_escape_string($this->request->params['form']['email']);
        }
          
        if (isset($this->request->params['form']['pass'])) {
            $pass = mysql_real_escape_string($this->request->params['form']['pass']);
      
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = mysql_real_escape_string($this->request->params['form']['amf']);
        }                                                        
        $result = $this->getPlayerFromEmailAndPassword($email, $pass);
        return $this->returnMobileResult($result, $amf);
    }
      
      
    function m_getUser($email=null, $pass=null, $amf = 0) 
    {
        if (isset($this->request->params['form']['email'])) {
            $email = mysql_real_escape_string($this->request->params['form']['email']);
        }
      
        if (isset($this->request->params['form']['pass'])) {
            $pass = mysql_real_escape_string($this->request->params['form']['pass']);
      
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = mysql_real_escape_string($this->request->params['form']['amf']);
        }
        $result = $this->getPlayerFromEmailAndPassword($email, $pass);
        return $this->returnMobileResult($result, $amf);
    }
          /**
          * What this returns
          * 1) User Record 
          * 2) Rank within Affils.
          * 3) Venue checked into
          * 4) 5 Most Recent Games
          * 5) All Upcoming Games
          * 6) All events that are not complete
          * 7) All Teams
          */      
    function m_viewUserWithDetail($lgnOrEmail = null, $amf = 0) 
    {
        if (isset($this->request->params['form']['lgnOrEmail'])) {
            $lgnOrEmail = mysql_real_escape_string($this->request->params['form']['lgnOrEmail']);
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = mysql_real_escape_string($this->request->params['form']['amf']);
        }
        $validEmail = '/\\A(?:^([a-z0-9][a-z0-9_\\-\\.\\+]*)@([a-z0-9][a-z0-9\\.\\-]{0,63}\\.(com|org|net|biz|info|name|net|pro|aero|coop|museum|[a-z]{2,4}))$)\\z/i';
        if (preg_match($validEmail, $lgnOrEmail)) {
            $conditions = array('email'=>$lgnOrEmail);
        } else {
            $conditions = array('lgn'=>$lgnOrEmail);
        }
        $user = $this->User->find(
            'first', array('conditions'=>$conditions,
            'contain'=>array('UsersAffil'=>array('School','Hometown','City','Greek')))
        );
        if (!$user) {
            return $this->returnMobileResult('User not found', $amf);
        }
        //Clean up affils, and get ranks
        foreach ($user['UsersAffil'] as &$usersAffil) {
            if ($usersAffil['model'] != 'School') { unset($usersAffil['School']);
            }
            if ($usersAffil['model'] != 'City') { unset($usersAffil['City']);
            } 
            if ($usersAffil['model'] != 'Hometown') { unset($usersAffil['Hometown']);
            } 
            if ($usersAffil['model'] != 'Greek') { unset($usersAffil['Greek']);
            } 
            $playerRanking = $this->getPlayerRankWithinAffil(
                $usersAffil['model'], $usersAffil['model_id'], $user['User']['rating'],
                $user['User']['id']
            );
            $usersAffil['rank'] = $playerRanking['Rank'];
            $usersAffil['totalPlayers'] = $playerRanking['Totalplayers']; 
        }
        //Get Checkins
        $userid = $user['User']['id'];
        $Checkin = ClassRegistry::init('Checkin');
        $userCheckin = $Checkin->find(
            'first', array(
            'conditions'=>array(
              'checkedout'=>0,
              'user_id'=>$userid),
            'contain'=>array('Venue'=>array('Address')))
        );
        if ($userCheckin) {
            $user['Checkin'] = $userCheckin;
        } //else, do nothing
        //Get the team information
        $Teammate = ClassRegistry::init('Teammate');
        $myTeammates = $Teammate->find(
            'all', array(
              'conditions'=>array(
                  'Teammate.user_id'=>$user['User']['id'],
                  'Teammate.status'=>array('Creator','Accepted','Pending'),
                  'Team.is_deleted'=>0),
              'contain'=>array(
                  'Team'=>array(
                      'User'=>array('fields'=>array('id','lgn','email','firstname','lastname','avatar','total_wins','total_losses','total_cupdif')))))
        );
        $myteamids = Set::extract($myTeammates, '{n}.Teammate.team_id');
        $myTeams = Set::extract($myTeammates, '{n}.Team');
        $myTeamsResult = array();
        $ctr = 0;
        foreach ($myTeams as $key=>$myTeam) {
            if (count($myTeam['User']) == $myTeam['people_in_team']) {
                $myTeamsResult[$ctr] = $myTeam;
                $ctr++;
            }
        }
        $user['Teams'] = $myTeamsResult;
        //Get Recent Games
        $user['RecentGames'] = $this->Game->find(
            'all', array(
              'conditions'=>array(
                  'OR'=>array('Game.team1_id'=>$myteamids,'Game.team2_id'=>$myteamids),
                  'Game.status'=>'Completed'),
              'contain'=>array(
                  'Team1'=>array('User'=>array('fields'=>array('id','lgn','email','firstname','lastname','avatar','total_wins','total_losses','total_cupdif'))),
                  'Team2'=>array('User'=>array('fields'=>array('id','lgn','email','firstname','lastname','avatar','total_wins','total_losses','total_cupdif'))),
                  'Event'=>array('fields'=>array('name','id'))),
              'limit'=>10,
              'order' => array('Game.id' => 'ASC'))
        );
        
        //Get Upcoming Games
        $user['UpcomingGames'] = $this->Game->find(
            'all', array(
            'conditions'=>array(
              'OR'=>array('Game.team1_id'=>$myteamids,'Game.team2_id'=>$myteamids),
              'Game.status'=>array('Playing','Ready','Not Ready','Unavailable')),
              'contain'=>array(
                  'Team1'=>array('User'=>array('fields'=>array('id','lgn','email','firstname','lastname','avatar','total_wins','total_losses','total_cupdif'))),
                  'Team2'=>array('User'=>array('fields'=>array('id','lgn','email','firstname','lastname','avatar','total_wins','total_losses','total_cupdif'))),
                  'Event'), 
            'order' => array('Game.id' => 'ASC'))
        ); 
          
        //Get all current or upcoming events
        $TeamsObject = ClassRegistry::init('TeamsObject');
        $teamsObjects = $TeamsObject->find(
            'all', array(
            'conditions'=>array(
              'TeamsObject.team_id'=>$myteamids,
              'TeamsObject.model'=>'Event',
              'TeamsObject.status <>'=>'Deleted',
              'Event.is_deleted'=>0,
              'DATE(Event.end_date) >='=>date("Y-m-d")),
            'contain'=>array('Event'=>
                  array('fields'=>array('name','is_deleted','currentround','starttimeofnextround','start_date','end_date')),
              'Team'))
        );
      
        foreach ($teamsObjects as &$teamObject)  {
            if (!$teamObject['Event']['starttimeofnextround']) {
                $teamObject['Event']['starttimeofnextround'] = 0; 
            }
            $startTimeOfNext = strtotime($teamObject['Event']['starttimeofnextround']);    
            $currentTime = time();
            $currentDateTime = Date('h:i:s', $currentTime);                                       
            $teamObject['Event']['timetillnextroundInseconds'] = $startTimeOfNext - $currentTime;
        }
          
            
        $user['Current Events'] = $teamsObjects;      
        return $this->returnMobileResult($user, $amf);
          
    }
    function getPlayerRankWithinAffil($model,$model_id,$userRating,$user_id) 
    {
        if ($model == 'City' || $model == 'Hometown') {
            $model = array('Hometown','City'); 
        } 

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
            $numPlayersBetter = count($this->custom_array_unique($numPlayersBetter));
            
            $this->UsersAffil->recursive = -1;
            $numPlayersTotal = $this->UsersAffil->find(
                'all', array(
                'conditions'=>array(
                    'UsersAffil.model'=>$model,
                    'UsersAffil.model_id'=>$model_id,
                    'UsersAffil.is_deleted'=>0))
            );
            $numPlayersTotal = Set::extract($numPlayersTotal, '{n}.UsersAffil.user_id');
            $numPlayersTotal = count($this->custom_array_unique($numPlayersTotal));
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
                //'fields'=>array('User.id')
                )
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
        $rank = min($numPlayersBetter+1, $numPlayersTotal);
        return array('Rank'=>$numPlayersBetter+1,'Totalplayers'=>$numPlayersTotal);
    }
      
      
    function m_viewUser($lgn=null,$showRecentGames = 0, $showUpcomingGames = 0,$amf = 0) 
    {
        if (isset($this->request->params['form']['lgn'])) { 
            $lgn = mysql_real_escape_string(($this->request->params['form']['lgn'])); 
        }
        if (isset($this->request->params['form']['amf'])) { 
            $amf = mysql_real_escape_string(($this->request->params['form']['amf'])); 
        }
        if (isset($this->request->params['form']['showRecentGames'])) { 
            $showRecentGames = mysql_real_escape_string(($this->request->params['form']['showRecentGames'])); 
        }
        if (isset($this->request->params['form']['showUpcomingGames'])) { 
            $showUpcomingGames = mysql_real_escape_string(($this->request->params['form']['showUpcomingGames'])); 
        }
        if (!$lgn) {
            return $this->returnMobileResult('Invalid Parameters', $amf); 
        }
        $conditions = array('lgn'=>$lgn,'is_deleted'=>0);             
        //dont sent address info/etc....
        $this->User->recursive = -1;
        $user = $this->User->find('first', array('conditions'=>$conditions));
        if (!$user) {
            return $this->returnMobileResult("User does not exist", $amf);  
        }
        if ($showRecentGames > 0 || $showUpcomingGames > 0) {
            $Teammate = ClassRegistry::init('Teammate');
            $myTeammates = $Teammate->find(
                'all', array(
                'conditions'=>array(
                    'Teammate.user_id'=>$user['User']['id'],
                    'Teammate.status'=>array('Creator','Accepted','Pending'),
                    'Team.is_deleted'=>0),
                'contain'=>array('Team'))
            );
            $myteamids = Set::extract($myTeammates, '{n}.Teammate.team_id');
        }
        //     return $user;
        if ($showRecentGames > 0) {
            $user['RecentGames'] = $this->Game->find(
                'all', array(
                'conditions'=>array(
                    'OR'=>array('Game.team1_id'=>$myteamids,'Game.team2_id'=>$myteamids),
                    'Game.status'=>'Completed'),
                'contain'=>array('Team1'=>array('fields'=>array('id','name')),'Team2'=>array('id','name'),'Event'),
                'limit'=>$showRecentGames,
                'order' => array('Game.id' => 'ASC'))
            );
        }
        //    return $user;
        if ($showUpcomingGames > 0) {
            $user['UpcomingGames'] = $this->Game->find(
                'all', array(
                'conditions'=>array(
                'OR'=>array('Game.team1_id'=>$myteamids,'Game.team2_id'=>$myteamids),
                'Game.status'=>array('Playing','Ready','Not Ready','Unavailable')),
                'contain'=>array('Team1'=>array('fields'=>array('id','name')),'Team2'=>array('id','name'),'Event'), 
                'order' => array('Game.id' => 'ASC'))
            ); 
        }
        return $this->returnMobileResult($user, $amf);
    }
      
    function m_validateUser($email=null, $lgn=null, $pass=null) 
    {
      
        if (isset($this->request->params['form']['email'])) {
            $email = mysql_real_escape_string(($this->request->params['form']['email'])); 
        }
        if (isset($this->request->params['form']['pass'])) {
            $pass = mysql_real_escape_string(($this->request->params['form']['pass'])); 
        }
        if (isset($this->request->params['form']['lgn'])) {
            $lgn = mysql_real_escape_string(($this->request->params['form']['lgn'])); 
        }
        if ((!$email && !$lgn) || !$pass) {
            return $this->returnJSONResult('Invalid Parameters'); 
        }
      
        if (!$email) {
            $conditions = array('lgn'=>$lgn,'is_deleted'=>0, 'pwd'=>$pass);
        } else {
            $conditions = array('email'=>$email,'is_deleted'=>0, 'pwd'=>$pass);
        }
          
          
        //dont sent address info/etc....
        $this->User->recursive = -1;
        $user = $this->User->find('first', array('conditions'=>$conditions));
      
        // if user exists, return info
        if (!empty($user['User']['id'])) {
            //  $Team   = ClassRegistry::init('Team');
            //   $teams = $Team->getUserTeams($user['User']['id']);
            //  unset($Team);
            return $this->returnJSONResult(array($user));
        }else {
            return $this->returnJSONResult("User does not exist");
      
        }
    }
      
    function m_viewUsers($userJsonArray=null) 
    {
        if (isset($this->request->params['form']['user_array'])) {
            $userJsonArray = mysql_real_escape_string($this->request->params['form']['user_array']); 
        }  
        
        $size = mysql_real_escape_string($this->request->params['form']['size']);
        
        $userJsonArray = json_decode($usersJsonArray, true);
        
        $this->User->recursive = -1;
        $this->User->find('all', array('conditions'=>array('lgn'=>$userJsonArray,'is_deleted'=>0)));        
        
        //$users = $this->User->returnUsersArray($userJsonArray, $size);
        return $this->returnJSONResult($users);
    }
    function m_getOnlineCount() 
    {      
        return $this->returnJSONResult(1);
    }
    function updateStatsForUser($userid) 
    {
        return $this->User->updateStatsForUser($userid);
    }
    function updateStatsForUsers($startid,$endid) 
    {
        return $this->User->updateStatsForUsers($startid, $endid);
    }
      /**
      * This function takes a list of users and returns their ratings. Either an id or 
      * an email address is provided
      * 
      * @param mixed $userArray
      */
    function getUserRatings_api($userArray) 
    {
        Configure::write('debug', 0);
        $idCtr = 0;
        $emailCtr = 0;
        foreach ($userArray as $userInfo) {
            if (isset($userInfo['email'])) {
                $emails[$emailCtr] = $userInfo['email'];
                $emailCtr++;
            }
            else {
                $ids[$idCtr] = $userInfo['id'];
                $idCtr++;
            }
        }
        $this->User->recursive = -1;
        $users = $this->User->find(
            'all', array(
            'conditions'=>array('OR'=>array(
              'email'=>$emails,
              'id'=>$ids)),
            'fields'=>array('id','email','rating'))
        );
        //   $users = Set::extract($users,'{n}.User');
        return $users;
    }   
    function m_findUsers($term1='',$term2='',$amf = 0) 
    {
        if (isset($this->request->params['form']['term1'])) {
            $term1= mysql_real_escape_string($this->request->params['form']['term1']); 
        }    
        if (isset($this->request->params['form']['term2'])) {
            $term2 = mysql_real_escape_string($this->request->params['form']['term2']); 
        }    
        if (isset($this->request->params['form']['amf'])) {          
            $amf = mysql_real_escape_string($this->request->params['form']['amf']); 
        }
        if ($term1 == '') {
            return $this->returnMobileResult('Search term empty', $amf); 
        }
        $conditions = array();
        $fields = array('firstname','lastname','lgn','id');
        $this->User->recursive = -1;
        if ($term2 == '') {
            $users = $this->User->find(
                'all', array(
                'conditions'=>array(
                    'OR'=>array(
                        'firstname LIKE'=>'%'.$term1.'%',
                        'lastname LIKE'=>'%'.$term1.'%',
                        'lgn LIKE'=>'%'.$term1.'%'),
                    'is_deleted'=>0),
                'fields'=>$fields,
                'limit'=>25    
                )
            );
            return $this->returnMobileResult($users, $amf);
        }
        else {
            $conditions['is_deleted']=0;
            $conditions['firstname LIKE'] = '%'.$term1.'%';
            $conditions['lastname LIKE'] = '%'.$term2.'%';
            $users = $this->User->find(
                'all', array(
                'conditions'=>$conditions,
                'fields'=>$fields,
                'limit=>25')
            );
            return $this->returnMobileResult($users, $amf);
        }
    }
      /**
      *Mobile search function 
      * Only searches on the provided fields. Searches partial text. 
      * At least one field must be provided.
      * Limit of 25 results
      */
    function m_findUsersOld($firstname = '', $lastname = '', $lgn = '',$amf = 0) 
    {
        if (isset($this->request->params['form']['firstname'])) {
            $firstname = mysql_real_escape_string($this->request->params['form']['firstname']); 
        }      
        if (isset($this->request->params['form']['lastname'])) {
            $lastname= mysql_real_escape_string($this->request->params['form']['lastname']); 
        }    
        if (isset($this->request->params['form']['lgn'])) {
            $lgn = mysql_real_escape_string($this->request->params['form']['lgn']); 
        }    
        if (isset($this->request->params['form']['amf'])) {
            $amf = mysql_real_escape_string($this->request->params['form']['amf']); 
        } 
        $conditions = array();
        if ($firstname != '') {
            $conditions['firstname LIKE'] = '%'.$firstname.'%';
        }  
        if ($lastname != '') {
            $conditions['lastname LIKE'] = '%'.$lastname.'%';
        }
        if ($lgn != '') {
            $conditions['lgn LIKE'] = '%'.$lgn.'%';
        } 
        if (empty($conditions)) {
            return $this->returnMobileResult("You must provide at least one field");
        }
        $conditions['is_deleted'] = 0;
        $this->User->recursive = -1;
        $results = $this->User->find(
            'all', array(
            'conditions'=>$conditions,
            'limit'=>25,
            'fields'=>array('firstname','lastname','lgn','id'))
        );
        return $this->returnMobileResult($results, $amf);    
    }   
      
      /**
      * Fill this in later
      * 
      * @param string $email
      * @param string $amf
      */
    function m_inviteFriend($email = null,$amf = 0) 
    {
        if (isset($this->request->params['form']['email'])) {
            $email = mysql_real_escape_string($this->request->params['form']['email']); 
        }      
        if (isset($this->request->params['form']['amf'])) {
            $amf= mysql_real_escape_string($this->request->params['form']['amf']); 
        } 
        
        //Must be logged in to do this
        
        $this->User->recursive = -1;
        $inviterID = $this->getUserID();
        if ($inviterID < 2) {
            return $this->returnMobileResult('You must be logged in to invite someone', $amf); 
        }
        
        $inviter = $this->User->find('first', array('conditions'=>array('id'=>$inviterID)));    
            
        if ($email == '') { return $this->returnMobileResult("bad email", $amf); 
        }
        $validEmail = '/\\A(?:^([a-z0-9][a-z0-9_\\-\\.\\+]*)@([a-z0-9][a-z0-9\\.\\-]{0,63}\\.(com|org|net|biz|info|name|net|pro|aero|coop|museum|[a-z]{2,4}))$)\\z/i';
        if (!preg_match($validEmail, $email)) { return $this->returnMobileResult('bad email'); 
        }
    
        $this->User->recursive = -1;
        $user = $this->User->find(
            'first', array('conditions'=>array(
            'email'=>$email,
            'is_deleted'=>0),'recursive'=>-1)
        );
        if ($user) {
            return $this->returnMobileResult('User already exists', $amf);
        }
        $exp_array = explode("@", $email);
        $new_nick=$my_nick=$exp_array['0'];
        $i=1;
        $nick_unfree=1;
        while ($nick_unfree==1) {
            $this->User->recursive = -1;
            $unfree_user=$this->User->find('count', array('conditions'=>array('lgn'=>$new_nick)));
            if(!$unfree_user) {
                $nick_unfree=0;
                break;
            }else{
                $new_nick=$my_nick.$i;
            }
            $i++;
        }
        
        // Generate password
        $new_pwd=substr(uniqid(), -6);
        $this->request->data['User']['pwd']=$new_pwd;
        $this->request->data['User']['pwd'] = md5($this->request->data['User']['pwd']);        
        $this->request->data['User']['email'] = $email;
        $this->request->data['User']['lgn']=$new_nick;
        $this->request->data['User']['firstname']='';
        $this->request->data['User']['lastname']='';
        $this->request->data['User']['activation_code'] = $this->ActivationCode(20);

        $is_exist = $this->User->find('first', array('fields'=>array('id'),'conditions'=>array('activation_code'=>$this->request->data['User']['activation_code'] )));
        while (!empty($is_exist) ){
            $this->request->data['User']['activation_code'] = $this->ActivationCode(20);
            $is_exist = $this->User->find('first', array('fields'=>array('id'),'conditions'=>array('activation_code'=>$this->request->data['User']['activation_code'] )));
        }

        $this->User->create();

        if ($this->User->save($this->request->data)) {
            $id = $this->User->getLastInsertID();
            $this->User->habtmAdd('Status', $id, REGISTRY_STATUS_ID);
            $this->request->data['User']['id'] = $id;
            //Generate QR Code
            $mysqldate = date('Y-m-d H:i:s');   // needs semicolon between day & time or will break     (duncan)

            $qr_togen = "0;".$this->request->data['User']['id'].";".$this->request->data['User']['lgn'].";".$this->request->data['User']['email'].";0;".$mysqldate;
            $this->User->generate_and_save_new_qr($qr_togen, $this->request->data['User']['email'], $mysqldate);
            $userInfo = $this->User->find('first', array('conditions'=>array('email'=>$this->request->data['User']['email'])));
            $qrimagelink = IMG_QRCODES_URL.'/'.$userInfo['User']['qr_image'];
             
            /**
            *  Send the message here
            */  
            if ($inviter['User']['firstname'] && $inviter['User']['lastname']) {
                $replaceData['{INVITER_NAME}'] = $inviter['User']['firstname'].' '.$inviter['User']['lastname'];
            }
            else {
                $replaceData['{INVITER_NAME}'] = $inviter['User']['lgn'];
            }
            $replaceData['{ACTIVATION_LINK}'] = "http://{$_SERVER['HTTP_HOST']}/activation/{$this->request->data['User']['activation_code']}";
            $replaceData['{LOGIN_EMAIL}'] = $email;
            $replaceData['{LOGIN_PASSWORD}'] = $new_pwd;
            $replaceData['{ANDROID_APPLICATION_LINK}'] = ANDROID_APPLICATION_LINK;
            $replaceData['{QR_LINK}']= $qrimagelink;
            $result = $this->sendMailMessage('MobileInvitationEmail', $replaceData, $email);     
        }
        else {
            return $this->returnMobileResult("Could not save data.", $amf); 
        } 
        return $this->returnMobileResult('ok', $amf);
    }
    /**
     * Regenerate my qr code
     * @author Oleg D.
     */
    
    function regenerate_my_qrcode() 
    {
        $userID = $this->getUserID();
        $this->User->recursive = -1;
        $userInfo = $this->User->find('first', array('conditions' => array('User.id' => $userID)));
        
        $mysqldate = date('Y-m-d H:i:s');                                              
        $qr_togen = "0;" . $userInfo['User']['id'] . ";" . $userInfo['User']['lgn'].";" . $userInfo['User']['email'].";0;" . $mysqldate;
        $this->User->generate_and_save_new_qr($qr_togen, $userInfo['User']['email'], $mysqldate);
        
        //need to get the info again, since the imagename has changed.
        $userInfo = $this->User->find('first', array('conditions' => array('User.id' => $userID)));
        $qrLink = IMG_QRCODES . '/' . $userInfo['User']['qr_image'];
        $result = $this->sendMailMessage('RegenerateQREmail', array('{QR_LINK}' => $qrLink), $userInfo['User']['email']); 
        $this->Session->setFlash('Email has been sent', 'flash_success');      
        return $this->redirect('/u/' . $userInfo['User']['lgn']);    
    }
    
    /**
     * Send me my qr code
     * @author Oleg D.
     */  
    function sendme_qrcode() 
    {
        $userID = $this->getUserID();
        $this->User->recursive = -1;
        $userInfo = $this->User->find('first', array('conditions' => array('User.id' => $userID)));
        $qrLink = IMG_QRCODES . '/' . $userInfo['User']['qr_image'];
        $result = $this->sendMailMessage('RegenerateQREmail', array('{QR_LINK}' => $qrLink), $userInfo['User']['email']); 
        $this->Session->setFlash('Email has been sent', 'flash_success');      
        return $this->redirect('/u/' . $userInfo['User']['lgn']);        
    }
    
    function get_users_from_my_events($userid) 
    {
        if (!$this->isUserSuperAdmin()) {
            return $this->returnJSONResult('Access Denied'); 
        }
        
        //First, get the events that user is a manager of
        $Manager = ClassRegistry::init('Manager');
        $Manager->recursive = -1;
        $managerSearchResult = $Manager->find(
            'all', array('conditions'=>array(
                'Manager.model'=>'Event',
                'Manager.user_id'=>$userid))
        );
        $eventIDs = Set::extract($managerSearchResult, '{n}.Manager.model_id');
        
        $Event = ClassRegistry::init('Event');
        $Event->recursive = -1;
        $events = $Event->find(
            'all', array('conditions'=>array(
            'is_deleted'=>0,
            'id'=>$eventIDs))
        );
        $eventIDs = Set::extract($events, '{n}.Event.id');
        
        $TeamsObject = ClassRegistry::init('TeamsObject');
        $TeamsObject->recursive = -1;
        $teamsObjects = $TeamsObject->find(
            'all', array(
            'conditions'=>array(
                'TeamsObject.status <> '=>'Deleted',
                'TeamsObject.model_id'=>$eventIDs,
                'TeamsObject.model'=>'Event'))
        );
         $teamIDs = Set::extract($teamsObjects, '{n}.TeamsObject.team_id');
         $teamIDs = $this->custom_array_unique($teamIDs);
         
         $Teammate = ClassRegistry::init('Teammate');
         $teammates = $Teammate->find(
             'all', array(
             'conditions'=>array(
                'Teammate.status'=>array('Creator','Pending','Accepted'),
                'Teammate.team_id'=>$teamIDs),
             'contain'=>array('User'))
         );
         $useremails = Set::extract($teammates, '{n}.User.email');
         $useremails = $this->custom_array_unique($useremails);
         $this->set('emails', $useremails);
         $this->layout = false;
         //return $this->returnJSONResult($useremails); 
    }
    
    /**
     * Merge accounts
     * @author Oleg D.
     */  
    function merge_accounts() 
    {      
        $this->Access->checkAccess('User', 'd');

        if (!empty($this->request->data['User']['user_to_move_from'])) {
            $this->User->recursive = -1;
            $user_to_move_from_form_data = $this->request->data['User']['user_to_move_from'];
            $user_to_move_from = $this->User->find(
                'first', array('conditions'=>array('OR'=>array(
                'lgn'=>$user_to_move_from_form_data,
                'email'=>$user_to_move_from_form_data,
                'id'=>$user_to_move_from_form_data)))
            );
            
            $user_to_move_to_form_data = $this->request->data['User']['user_to_move_to'];
            $user_to_move_to = $this->User->find(
                'first', array('conditions'=>array('OR'=>array(
                'lgn'=>$user_to_move_to_form_data,
                'email'=>$user_to_move_to_form_data,
                'id'=>$user_to_move_to_form_data)))
            );
                
            if (!$user_to_move_from || !$user_to_move_to) {
                $this->Session->setFlash('One of the users was not found', 'flash_error');
                $this->redirect('/users/merge_accounts');
            }
            $userIDToMoveFrom = $user_to_move_from['User']['id'];
            $userIDToMoveTo = $user_to_move_to['User']['id'];
            
            
            $Teammate = ClassRegistry::init('Teammate');
            $Teammate->recursive = -1;
            $moveFromTeammates = $Teammate->find(
                'all', array('conditions'=>array(
                'user_id'=>$userIDToMoveFrom,
                'status'=>array('Creator','Pending','Accepted')))
            );
            $moveToTeammates = $Teammate->find(
                'all', array('conditions'=>array(
                'user_id'=>$userIDToMoveTo,
                'status'=>array('Creator','Pending','Approved')))
            );
            //we don't want the teammate to be on the team twice
            foreach ($moveFromTeammates as $moveFromTeammate) {
                $teammateAlreadyExists = false;
                $moveFromTeamID = $moveFromTeammate['Teammate']['team_id'];
                foreach ($moveToTeammates as $moveToTeammate) {
                    if ($moveToTeammate['Teammate']['team_id'] == $moveFromTeamID) {
                        $teammateAlreadyExists = true; 
                    } 
                }
                if ($teammateAlreadyExists) {
                    $moveFromTeammate['Teammate']['status'] = 'Deleted';
                    $Teammate->save($moveFromTeammate);
                } else {
                    $moveFromTeammate['Teammate']['user_id'] = $userIDToMoveTo;
                    $Teammate->save($moveFromTeammate);
                }
            }   
            $this->checkUserForDuplicateTeams($userIDToMoveTo);   
            
            /*
            * Now, lets handle ratings
            */
            $Ratinghistory = ClassRegistry::init('Ratinghistory');
            
            $ratingHistories = $Ratinghistory->find(
                'all', array(
                'conditions'=>array(
                    'model'=>'User',
                    'user_id'=>array($userIDToMoveFrom,$userIDToMoveTo)),
                'order'=>array('Ratinghistory.id'=>'ASC'),
                'contain'=>array('Game'=>array('Team1','Team2','Ratinghistory')))
            );
            if ($ratingHistories) {
                $before = $ratingHistories[0]['Ratinghistory']['before'];
            }
            foreach ($ratingHistories as $ratingHistory) {
                $currentTeamID = $ratingHistory['Ratinghistory']['team_id'];
                $opponentsTotalRating = 0;
                $opponentsCount = 0;
                $allRatingHistoriesInGame = $ratingHistory['Game']['Ratinghistory'];
                foreach ($allRatingHistoriesInGame as $oneRatingHistoryInGame) {
                    if ($oneRatingHistoryInGame['team_id'] != $currentTeamID) {
                        $opponentsTotalRating += $oneRatingHistoryInGame['before'];
                        $opponentsCount++;
                    }
                }
                if ($opponentsCount > 0) {
                    $opponentsAverageRating = $opponentsTotalRating / $opponentsCount;
                }
                else {
                    $opponentsAverageRating = INITIAL_PLAYER_RATING;
                }
                
                $cupdif = $Ratinghistory->getEffectiveCupDif($ratingHistory['Game']);
                if ($ratingHistory['Game']['winningteam_id'] == $currentTeamID) {
                    $playerRatingChange = $Ratinghistory->getRatingChange(
                        $before, $opponentsAverageRating, $cupdif
                    );
                    $after = $before + ($ratingHistory['Ratinghistory']['weight'] * $playerRatingChange);
                }
                else {
                    $playerRatingChange = $Ratinghistory->getRatingChange(
                        $opponentsAverageRating, $before, $cupdif
                    );
                    $after = $before - ($ratingHistory['Ratinghistory']['weight'] * $playerRatingChange);
                }
                //return $this->returnJSONResult($ratingHistory['Ratinghistory']['after']);
                $arrayToSave = array(
                    'id'=>$ratingHistory['Ratinghistory']['id'],
                    'before'=>$before,
                    'after'=>$after,
                    'user_id'=>$userIDToMoveTo,
                    'adjustedretro'=>1);
                $Ratinghistory->save($arrayToSave);
                $before = $after;
            }
 
            $this->User->setUserRating($userIDToMoveFrom, 0);
            $this->User->setUserRating($userIDToMoveTo, $after);
            $this->User->updateStatsForUser($userIDToMoveFrom);
            $this->User->updateStatsForUser($userIDToMoveTo);
            $this->User->updateTeamRatings($userIDToMoveTo);
               
            $queries = array();
            $queries[] = "UPDATE albums SET model_id = " . $userIDToMoveTo . " WHERE model = 'User' AND model_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE albums SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;            
            $queries[] = "UPDATE albums SET last_user_id = " . $userIDToMoveTo . " WHERE last_user_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE addresses SET model_id = " . $userIDToMoveTo . " WHERE model = 'User' AND model_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE answers SET model_id = " . $userIDToMoveTo . " WHERE model = 'User' AND model_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE blogposts SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE blogposts SET last_user_id = " . $userIDToMoveTo . " WHERE last_user_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE checkins SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE comments SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE comments SET last_user_id = " . $userIDToMoveTo . " WHERE last_user_id = " . $userIDToMoveFrom;     
            $queries[] = "UPDATE events SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE events SET last_user_id = " . $userIDToMoveTo . " WHERE last_user_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE forumbranches SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;     
            $queries[] = "UPDATE forumposts SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;        
            $queries[] = "UPDATE forumtopics SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE histories SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE images SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE images SET last_user_id = " . $userIDToMoveTo . " WHERE last_user_id = " . $userIDToMoveFrom;  
            $queries[] = "UPDATE links SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE links SET last_user_id = " . $userIDToMoveTo . " WHERE last_user_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE managers SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE models_tags SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE organizations SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;  
            $queries[] = "UPDATE organizations_objects SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE organizations_users SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;       
            $queries[] = "UPDATE organizations_news SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom; 
            $queries[] = "UPDATE packages_users SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom; 
            $queries[] = "UPDATE payments SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;      
            $queries[] = "UPDATE phones SET model_id = " . $userIDToMoveTo . " WHERE model = 'User' AND model_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE pongtables SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE promocodes SET assign_user_id = " . $userIDToMoveTo . " WHERE assign_user_id = " . $userIDToMoveFrom;   
            $queries[] = "UPDATE signups SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE signups_users SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE signup_roommates SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE softwareregs SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;       
            $queries[] = "UPDATE store_discountgroups_members SET member_id = " . $userIDToMoveTo . " WHERE member_type = 'user' AND member_id = " . $userIDToMoveFrom;       
            $queries[] = "UPDATE store_orders SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;        
            $queries[] = "UPDATE store_resellers SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;        
            $queries[] = "UPDATE tags SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE teams_users SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;              
            $queries[] = "UPDATE teammates SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE teams_users SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom; 
            $queries[] = "UPDATE users_affils SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE venues_users SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE videos SET user_id = " . $userIDToMoveTo . " WHERE user_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE videos SET last_user_id = " . $userIDToMoveTo . " WHERE last_user_id = " . $userIDToMoveFrom;        
            $queries[] = "UPDATE videos SET last_user_id = " . $userIDToMoveTo . " WHERE last_user_id = " . $userIDToMoveFrom;
            $queries[] = "UPDATE users SET is_deleted= 1, deleted = '" . date('Y-m-d H:i:s') . "' WHERE id = " . $userIDToMoveFrom;            
            
            foreach ($queries as $query) {
                $this->User->query($query);
            }
            
            $this->Session->setFlash('Users Merged', 'flash_success');

            $this->redirect('/users/merge_accounts');
        }        
    }
}

?>
