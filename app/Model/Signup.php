<?php
class Signup extends AppModel
{

    var $name = 'Signup';
    var $recursive = -1;
    var $actsAs = array ('Containable');
    var $belongsTo = array(
    'Event' => array('className' => 'Event',
                                  'foreignKey' => 'model_id',
                                  'dependent' => false,
                                  'fields' => '',
                                  'order' => ''
    ),
    'Packagedetails' => array('className' => 'Packagedetails',
                                  'foreignKey' => 'packagedetails_id',
                                  'dependent' => false,
                                  'conditions' => array(),
                                  'fields' => '',
                                  'order' => ''
    ),
    'User' => array(
                                    'className'    => 'User',
                                    'foreignKey'    => 'user_id'
            )
        );


    /**
     *  Search if such signup already exists and update or create  new one
     *  @author vovich
     */
    function createSignup($modelName = null, $modelId = null, $userID = null, $signup = array()) 
    {

               //searching if such sign up already exists
               $signupID  = $this->field('id', array('user_id' => $userID, 'model'=>$modelName, 'model_id' => $modelId));

        if (!empty($signupID)) {
             $signup['id'] =  $signupID;//signup exist - update
        } else {//create new signup
            if (empty($signup['status'])) {
                $signup['status']   = "new" ; 
            }
            $this->create();
        }

        if ($this->save($signup)) {
            if (empty($signupID)) {
                             
                $signupID = $this->getLastInsertID();
                          
                $SignupsUser = ClassRegistry::init('SignupsUser');
                        $SignupsUser->create();
                        $SignupsUser->save(array('user_id' => $userID, 'signup_id' => $signupID, 'agreement_accepted' => 1));    
            }

        } else {
            return false;
        }

        return     $signupID;

    }
    /**
     *
     * @param $model
     * @param $modelId
     * @return unknown_type
     */
    function getCountRefunds($model = null, $modelId = null, $conditions = "") 
    {

        $sql = "SELECT count(Payment.id) as cnt, sum(amount) as refunds FROM signups AS Signup INNER JOIN payments AS Payment
	    		ON Signup.model = '$model'
	    		AND Signup.model_id = $modelId
	    		AND Payment.model   = 'Signup'
	    		AND Signup.id = Payment.model_id
	    		AND Payment.status = 'Approved'
	    WHERE Payment.amount < 0 AND Signup.status<>'cancelled' ".$conditions;
        $result = $this->query($sql);
        if (!empty($result[0][0]['cnt'])) {
            return array('cnt' => $result[0][0]['cnt'],'refunds' => $result[0][0]['refunds']);
        } else {
            return array('cnt' => 0,'refunds' => 0);
        }

    }
    
    /**
     * Get ID's of all not completed by teams signups
     * This includes the ids of people whose teams are still pending
     * @author Oleg D.
     */    
    function notCompletedTeamSignups($model, $modelID) 
    {
        $TeammateObject = ClassRegistry::init('Teammate');
        $SignupObject = ClassRegistry::init('Signup');
        $SignupsUserObject = ClassRegistry::init('SignupsUser');
        
        $temmatesOfCompletetTeams = $TeammateObject->temmatesOfCompletetTeams($model, $modelID);
            
        $SignupObject->recursize = -1;
        $signups = $SignupObject->find('all', array('conditions' => array('model' => $model, 'model_id' => $modelID, 'status' => array('paid', 'partly paid')), 'order' => array('Signup.id' => 'ASC')));
        $signupIDs = Set::combine($signups, '{n}.Signup.id', '{n}.Signup.id');

        $getAllSignupsUsers = $SignupsUserObject->find('all', array('conditions' => array('signup_id' => $signupIDs)));
        
        $allSignupsUsers = array();
        foreach ($getAllSignupsUsers as $getAllSignupsUser) {
            $allSignupsUsers[$getAllSignupsUser['SignupsUser']['signup_id']][$getAllSignupsUser['SignupsUser']['user_id']] = $getAllSignupsUser;            
        }
        
        $notCompletedSignupIDs = array();
        foreach ($signups as $signup) {
            if (empty($temmatesOfCompletetTeams[$signup['Signup']['user_id']])) {
                $notCompletedSignupIDs[$signup['Signup']['id']] = $signup['Signup']['id'];                         
            } elseif ($signup['Signup']['for_team']) {
                $signupsUsers = array();
                if (isset($allSignupsUsers[$signup['Signup']['id']])) {
                    $signupsUsers = $allSignupsUsers[$signup['Signup']['id']];    
                }                
                if (!empty($signupsUsers)) {
                    foreach ($signupsUsers as $signupsUser) {
                        if (!$signupsUser['SignupsUser']['agreement_accepted'] || !$TeammateObject->isAddressCompleted($signupsUser['SignupsUser']['user_id'])) {
                            $notCompletedSignupIDs[$signup['Signup']['id']] = $signup['Signup']['id'];
                        }
                    }
                }                        
            }     
        }
        return $notCompletedSignupIDs;
    }
        /**
     * Get ID's of all not completed by teams signups
     * This is only people that are not on any teams whatsoever
     * @author Oleg D.
     */    
    function notCompletedAtAllTeamSignups($model, $modelID) 
    {
        $TeammateObject = ClassRegistry::init('Teammate');
        $SignupObject = ClassRegistry::init('Signup');
        $SignupsUserObject = ClassRegistry::init('SignupsUser');
        
        $teammatesOfCompletedOrPendingTeams = $TeammateObject->temmatesOfCompletedOrPendingTeams($model, $modelID);
            
        $SignupObject->recursize = -1;
        $signups = $SignupObject->find('all', array('conditions' => array('model' => $model, 'model_id' => $modelID, 'status' => array('paid', 'partly paid')), 'order' => array('Signup.id' => 'ASC')));
        $signupIDs = Set::combine($signups, '{n}.Signup.id', '{n}.Signup.id');

        $getAllSignupsUsers = $SignupsUserObject->find('all', array('conditions' => array('signup_id' => $signupIDs)));
        
        $allSignupsUsers = array();
        foreach ($getAllSignupsUsers as $getAllSignupsUser) {
            $allSignupsUsers[$getAllSignupsUser['SignupsUser']['signup_id']][$getAllSignupsUser['SignupsUser']['user_id']] = $getAllSignupsUser;            
        }
        
        $notCompletedSignupIDs = array();
        foreach ($signups as $signup) {
            if (empty($teammatesOfCompletedOrPendingTeams[$signup['Signup']['user_id']])) {
                $notCompletedSignupIDs[$signup['Signup']['id']] = $signup['Signup']['id'];                         
            } elseif ($signup['Signup']['for_team']) {
                $signupsUsers = array();
                if (isset($allSignupsUsers[$signup['Signup']['id']])) {
                    $signupsUsers = $allSignupsUsers[$signup['Signup']['id']];    
                }                
                if (!empty($signupsUsers)) {
                    foreach ($signupsUsers as $signupsUser) {
                        if (!$signupsUser['SignupsUser']['agreement_accepted'] || !$TeammateObject->isAddressCompleted($signupsUser['SignupsUser']['user_id'])) {
                            $notCompletedSignupIDs[$signup['Signup']['id']] = $signup['Signup']['id'];
                        }
                    }
                }                        
            }     
        }
        return $notCompletedSignupIDs;
    }
    
    /**
     * Get ID's of all completed by teams signups
     * @author Oleg D.
     */    
    function completedTeamSignups($model, $modelID) 
    {
        $signups = $this->find('list', array('fields' => array('id', 'id'), 'conditions' => array('model' => $model, 'model_id' => $modelID, 'status' => array('paid', 'partly paid'), 'NOT' => array('Signup.id' => $this->notCompletedTeamSignups($model, $modelID)))));
        return $signups;
    }
        
    /**
     * Get ID's of all not completed by rooms signups
     * @author Oleg D.
     */    
    function notCompletedRoomSignups($model, $modelID) 
    {
        $notCompletedSignupIDs = array();
        // get all correct signups 
        $sql = "SELECT * FROM signups s
				LEFT JOIN signups_users su ON su.signup_id = s.id 
				LEFT JOIN packagedetails pd ON pd.id = s.packagedetails_id
				LEFT JOIN packages p ON p.id = pd.package_id
				WHERE s.model = '" . $model . "' AND s.model_id = " . $modelID . " AND s.status IN ('paid', 'partly paid')";

        $signupResults = $this->query($sql);
        $signups = array();
        foreach ($signupResults as $signupResult) {
            $signupID = $signupResult['s']['id'];
            $signups[$signupID]['signup'] = $signupResult['s'];
            $signups[$signupID]['package'] = $signupResult['p'];
            $signups[$signupID]['packagedetails'] = $signupResult['pd'];
            $signups[$signupID]['users'][$signupResult['su']['user_id']] = $signupResult['su'];
            
            // remove signups with packages without rooms
            if (!$signups[$signupID]['package']['people_in_room']) {
                unset($signups[$signupID]);    
            }                            
        }               
        $peopleInTeam = $this->{$model}->field('people_team', array($model. '.id' => $modelID));
        $sql = "SELECT * FROM signup_rooms AS room 
				LEFT JOIN signup_roommates AS roommate ON room.id = roommate.room_id
				WHERE room.model = '" . $model . "' AND room.model_id = " . $modelID . " AND room.status IN ('Approved', 'Confirmed') AND roommate.status NOT IN ('Declined', 'Pending')";
        
        $roommates = $this->query($sql);
        
        $rooms = array();
        foreach ($signups as $signup) {
            $rooms = array();
            if ($signup['signup']['for_team']) { 
                $neededRooms = $peopleInTeam / $signup['package']['people_in_room'];              
            } else {
                $neededRooms = 1;
            }    
                        
            foreach ($roommates as $roommate) {
                if (isset($signup['users'][$roommate['roommate']['user_id']])) {
                    $rooms[$roommate['roommate']['room_id']] =  $roommate['room'];      
                }        
            }
            if (!$neededRooms || !count($rooms) || $neededRooms > count($rooms)) {
                $notCompletedSignupIDs[$signup['signup']['id']] = $signup['signup']['id'];    
            }            
        }    

        return $notCompletedSignupIDs;
    }    
    
    /**
     * Get ID's of all not completed by teams signups
     * @author Oleg D.
     */        
    function getSignupsUsersForStatistic($signupsIDs) 
    {
        set_time_limit(700);
        $results = array();
        
        if (!empty($signupsIDs)) {
            $signupsList = implode(',', $signupsIDs);
            //$usersIDs = $this->find('list', array('conditions' => array('Signup.id' => $signupsIDs), 'fields' => array('user_id', 'user_id'), 'order' => array('user_id' => 'ASC')));						
            $SignupsUserObject = ClassRegistry::init('SignupsUser');        
            $usersIDs = $SignupsUserObject->find('list', array('conditions' => array('SignupsUser.signup_id' => $signupsIDs), 'fields' => array('user_id', 'user_id'), 'order' => array('user_id' => 'ASC')));
            
            //$SignupsUserObject = ClassRegistry::init('SignupsUser');
            //$usersIDs = $SignupsUserObject->find('list', array('conditions' => array('signup_id' => $signupsIDs), 'fields' => array('user_id', 'user_id'), 'order' => array('user_id' => 'ASC')));			
            //INNER JOIN signups_users su ON su.user_id = u.id AND su.signup_id IN (" . $signupsList . ")
            //INNER JOIN signups s ON s.id = su.signup_id			
            
            
            $signupsList = implode(',', $signupsIDs);
            $usersList = implode(',', $usersIDs);
            
            $sql ="
				SELECT DISTINCT u.id as user_id, lgn, gender, lastname, firstname, birthdate as DOB, email, phones, address, address2, city, p.name as state, postalcode, c.name as country, s.status, s.for_team, s.id 
				FROM users u 
				INNER JOIN signups_users su ON su.user_id = u.id
				INNER JOIN signups s ON s.id = su.signup_id AND s.id IN (" . $signupsList . ")				
				LEFT JOIN (SELECT model_id, max(id) AS min_id FROM addresses WHERE model = 'User' AND model_id IN (" . $usersList . ") AND (label='Home' OR label is NULL) AND is_deleted <>1 GROUP BY model_id ) one_per_user ON one_per_user.model_id = u.id 
				LEFT JOIN (SELECT GROUP_CONCAT(DISTINCT phone.phone) AS phones, model_id FROM phones AS phone WHERE phone.model = 'User' AND phone.model_id IN (" . $usersList . ") AND phone.is_deleted <>1 GROUP BY model_id) phones ON phones.model_id = s.user_id 
				LEFT JOIN addresses a on a.id = one_per_user.min_id 
				LEFT JOIN countries c on a.country_id = c.id 
				LEFT JOIN provincestates p on a.provincestate_id = p.id	
				WHERE u.id IN (" . $usersList . ") ORDER BY s.id ASC 					
			";
            $results = $this->query($sql);
        }
        return $results;
    }
    /**
     * Get ID's of all not completed by teams signups
     * @author Oleg D.
     */        
    function getSignupsUsersTeamsForStatistic($signupsIDs, $model, $modelID) 
    {
        set_time_limit(700);
        $results = array();
        
        if (!empty($signupsIDs)) {
            $signupsList = implode(',', $signupsIDs);
                        
            //$usersIDs = $this->find('list', array('conditions' => array('Signup.id' => $signupsIDs), 'fields' => array('user_id', 'user_id'), 'order' => array('user_id' => 'ASC')));			
            $SignupsUserObject = ClassRegistry::init('SignupsUser');            
            $usersIDs = $SignupsUserObject->find('list', array('conditions' => array('SignupsUser.signup_id' => $signupsIDs), 'fields' => array('user_id', 'user_id'), 'order' => array('user_id' => 'ASC')));
            
            //$SignupsUserObject = ClassRegistry::init('SignupsUser');
            //$usersIDs = $SignupsUserObject->find('list', array('conditions' => array('signup_id' => $signupsIDs), 'fields' => array('user_id', 'user_id'), 'order' => array('user_id' => 'ASC')));			
            //INNER JOIN signups_users su ON su.user_id = u.id AND su.signup_id IN (" . $signupsList . ")
            //INNER JOIN signups s ON s.id = su.signup_id			
                        
            $signupsList = implode(',', $signupsIDs);
            $usersList = implode(',', $usersIDs);
            
            $signupSql ="
				SELECT DISTINCT u.id as user_id, lgn, gender, lastname, firstname, birthdate as DOB, email, phones, address, address2, city, p.name as state, postalcode, c.name as country, s.status, s.for_team, s.id
				FROM users u  
				INNER JOIN signups_users su ON su.user_id = u.id
				INNER JOIN signups s ON s.id = su.signup_id AND s.id IN (" . $signupsList . ")	
				LEFT JOIN (SELECT model_id, max(id) AS min_id FROM addresses WHERE model = 'User' AND model_id IN (" . $usersList . ") AND (label='Home' OR label is NULL) AND is_deleted <>1 GROUP BY model_id ) one_per_user ON one_per_user.model_id = u.id 
				LEFT JOIN (SELECT GROUP_CONCAT(DISTINCT phone.phone) AS phones, model_id FROM phones AS phone WHERE phone.model = 'User' AND phone.model_id IN (" . $usersList . ") AND phone.is_deleted <>1 GROUP BY model_id) phones ON phones.model_id = s.user_id 
				LEFT JOIN addresses a on a.id = one_per_user.min_id 
				LEFT JOIN countries c on a.country_id = c.id 
				LEFT JOIN provincestates p on a.provincestate_id = p.id	
				WHERE u.id IN (" . $usersList . ") ORDER BY s.id ASC 					
			";
            $teams = $this->eventTeams($model, $modelID);
            //exit;
            $signups = $this->query($signupSql);
            foreach ($signups as $key => $signup) {
                foreach ($teams as $team) {
                    if (isset($team['teammates'][$signup['u']['user_id']])) {
                        $signups[$key]['team'] = $team;    
                    }
                    if (empty($signups[$key]['team'])) {
                        $signups[$key]['team'] = array('id' => '', 'name' => '');
                    }
                }
            }
        }
        
        return $signups;
    }
    /**
     * Get all teams of event
     * @author Oleg D.
     */
    function eventTeams($model, $modelID) 
    {
        
        $sql = "SELECT t.*, tm.user_id
		FROM teams t
		INNER JOIN teammates tm ON t.id = tm.team_id
		INNER JOIN teams_objects tob ON t.id = tob.team_id
		WHERE tob.model = '" . $model . "' AND tob.model_id = " . $modelID . " AND tob.status = 'created' AND tm.status IN ('Creator', 'Accepted', 'Pending') ORDER BY t.id ASC";
        
        $results = $this->query($sql);
        $teams = array();
        foreach ($results as $result) {
            $teamID = $result['t']['id'];
            if (!isset($teams[$result['t']['id']])) {
                $teams[$result['t']['id']] = $result['t'];
            }
            $teams[$result['t']['id']]['teammates'][$result['tm']['user_id']] = $result['tm']['user_id'];    
        }

        return $teams;
    }
    

}
?>