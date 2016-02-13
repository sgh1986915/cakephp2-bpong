<?php

class TeamsController extends AppController
{
    var $name = 'Teams';
    var $uses    = array('Team','Teammate','Image','TeamsObject', 'Game', 'Signup', 'SignupsUser','User');
    var $teamStatuses = array(''=>' All ','Created'=>'Created','Pending'=>'Pending','Completed'=>'Completed','Deleted'=>'Deleted');
    var $paginate = array ('limit' => 20);   
    function merge_two_teams($submit=0) 
    {
        if (!$this->isUserSuperAdmin()) {
            $this->Session->setFlash('Access Denied.', 'flash_error');
            $this->redirect('/pages/update_stats');
        }
        if ($submit == 1) {
            $teamIDtoDelete = $this->request->data['Team']['teamID_to_delete'];
            $teamIDtoMergeInto = $this->request->data['Team']['teamID_to_merge_into'];    
            $result = $this->mergeTwoTeams($teamIDtoDelete, $teamIDtoMergeInto);
            $this->Session->setFlash($result, 'flash_success');
            $this->redirect('/teams/merge_two_teams');
        }    
    }
    
    function testUpdateStats($teamID) 
    {
        return $this->Team->updateStatsForTeam($teamID);
    }      
    function update_team_stats() 
    {
       
    }
    function submit_update_team_stats() 
    {
        $dataFromForm = $this->request->data['Team']['team_id'];
        // return $this->returnJSONResult($dataFromForm);
        if ($dataFromForm > 1) {
            $this->Team->updateStatsForTeam($dataFromForm, 1);
            $this->Session->setFlash('Stats Updated', 'flash_success');
        }
        else {
            $this->Session->setFlash('Team Not Found', 'flash_error');
        }
      
        $this->redirect('/teams/update_team_stats');
    }
    function findTeamsThatAreReallyComplete() 
    {
        if (!$this->isUserSuperAdmin()) {
            return "only superadmins"; 
        }
        $teams = $this->Team->find(
            'all', array(
            'conditions'=>array('Team.status'=>'Pending'),
            'contain'=>array('User'))
        );
        foreach ($teams as $key=>$team) {
            if ($team['Team']['people_in_team'] != count($team['User'])) {
                unset($teams[$key]); 
            }
            else {
                foreach ($team['User'] as $user) {
                    if ($user['Teammate']['status'] != 'Accepted' && $user['Teammate']['status'] != 'Creator') {
                        unset($teams[$key]); 
                    }
                }
            }
        }
        $results = Set::extract($teams, '{n}.Team.id');
        return $results;
    }
    
    
    /*   function getTestData() {
        $array = array(
            'cupdif'=>34,
            'finalsseed'=>0,
            'infinals'=>0,
            'losses'=>1,
            'model'=>'Event',
            'model_id'=>1507,
            'name'=>'sdfas',
            'rank'=>1,
            'seed'=>8,
            'team_id'=>9486,
            'wins'=>7);
        return $this->returnJSONResult(array($array));
    }
    function updateStatsForTeam($teamid,$updateUser = 0) {
        return $this->Team->updateStatsForTeam($teamid, $updateUser);
    }                                                                                                                            
    function updateStatsForTeams($startID,$endID,$updateUser = 0) {
        return $this->Team->updateStatsForTeams($startID,$endID, $updateUser);
    }  */
    //passwords are md5
    function m_getOrCreateTeam($teamName = null, $player1email = null,$player1pass = null,$player1timestamp = 0,
        $player2email = null,$player2pass = null,$player2timestamp = 0,
        $isSingles = 0,$amf = 0
    ) {
        Configure::write('debug', 0);     
        
        if (isset($this->request->params['form']['teamName'])) {
            $teamName = mysql_escape_string($this->request->params['form']['teamName']); 
        }
        if (isset($this->request->params['form']['player1email'])) {
            $player1email = mysql_escape_string($this->request->params['form']['player1email']); 
        }
        if (isset($this->request->params['form']['player1timestamp'])) {
            $player1timestamp = mysql_escape_string($this->request->params['form']['player1timestamp']); 
        }
        if (isset($this->request->params['form']['player1pass'])) {
            $player1pass = mysql_escape_string($this->request->params['form']['player1pass']); 
        }
        if (isset($this->request->params['form']['player2email'])) {
            $player2email = mysql_escape_string($this->request->params['form']['player2email']); 
        }
        if (isset($this->request->params['form']['player2pass'])) {
            $player2pass = mysql_escape_string($this->request->params['form']['player2pass']); 
        }
        if (isset($this->request->params['form']['player2timestamp'])) {
            $player2timestamp = mysql_escape_string($this->request->params['form']['player2timestamp']); 
        }
        if (isset($this->request->params['form']['isSingles'])) {
            $isSingles = mysql_escape_string($this->request->params['form']['isSingles']); 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = mysql_escape_string($this->request->params['form']['amf']); 
        }    
              
        $this->User->recursive = -1;
        $conditions['email'] = $player1email;
        if ($player1pass == "0" && $player1timestamp == "0") {
            return $this->returnMobileResult(array('Need either a password or a timestamp for player',$player1pass,$player1timestamp), $amf);
        }
        if ($player1pass != "0") {
            $conditions['pwd'] = $player1pass;
        }
        if ($player1timestamp != "0") {
            $conditions['qr_generated <='] = $player1timestamp;
        }
        $player1 = $this->User->find('first', array('conditions'=>$conditions));
        if (!$player1) {
            return $this->returnMobileResult('Could not authenticate player 1', $amf); 
        }
        if ($isSingles == 1) {
            $numPlayers = 1;
            $playerIDs = array($player1['User']['id']);
        }
        else {
            $numPlayers = 2;
            unset($conditions);
            $conditions['email'] = $player2email;
            
            if ($player2email == "0" && $player2timestamp == "0") {
                return $this->returnMobileResult(array('Need either a password or a timestamp for player 2',$player2email,$player2timestamp), $amf);
            }
            if ($player2pass != "0") {
                $conditions['pwd'] = $player2pass;
            }
            if ($player2timestamp != "0") {
                $conditions['qr_generated <='] = $player2timestamp;
            }
            // return $conditions;
            $player2 = $this->User->find('first', array('conditions'=>$conditions));
            if (!$player2) {
                return $this->returnMobileResult('Bad Credentials for player 2', $amf); 
            } 
            if (!$teamName) {
                $teamName = $player1['User']['lgn'].' and '.$player2['User']['lgn'];    
            }
            $playerIDs = array($player1['User']['id'],$player2['User']['id']);   
        }
        $loggedUserID = $this->getUserID();
        if ($loggedUserID > 1) {
            $requesterID = $loggedUserID; 
        }
        else {          
            $requesterID = $player1['User']['id']; 
        }
        $team = $this->Team->createAndGetCompletedTeam(
            $teamName, $numPlayers,
            $playerIDs, $requesterID
        );                           
        if (!$team) {
            return $this->returnMobileResult("There was a problem.", $amf); 
        }
        else {
            return $this->returnMobileResult($team, $amf); 
        }
    }
      
    function mergeTwoTeams_api($teamIDToDelete,$teamIDToMergeInto) 
    {
        Configure::write('debug', 0);  
        $result = $this->mergeTwoTeams($teamIDToDelete, $teamIDToMergeInto);
        return $result;
    }
    
    function mergeTwoTeams_old($teamIDToDelete, $teamIDToMergeInto) 
    {
        /**
         * Objects to consider: 
         * Teamates: Will delete the teammates of the old team
         * Personal Image: Will use the image of the new team only - should delete old
         * Games: Transfer to new team
         * Teams_Object: Will transfer to new team
         * Team: Add Wins/Losses/CupDif, and delete
         * Ratinghistory: Transfer to new team
         */
        // For right now, only allow skinny to do this
        if (!$this->isLoggined()) {
            return "You are not logged in.";
        }
        $user = $this->Session->read('loggedUser');
        if ($user['id']!=25) { return "Only Skinny can do this right now."; 
        } 
        
        //Team
        $this->Team->recursive = -1;
        $teamToDelete = $this->Team->find(
            'first', array('conditions'=>array('id'=>$teamIDToDelete,
            'status <> '=>'Deleted'))
        );
        if (!teamToDelete) { return "Team to delete does not exist"; 
        }
        
        $teamToMergeInto = $this->Team->find(
            'first', array('conditions'=>array('id'=>$teamIDToMergeInto,
            'status <>'=>'Deleted'))
        );
        if (!$teamToMergeInto) { return "Team to merge into does not exist"; 
        }
        
        $teamToMergeInto['Team']['total_wins'] += $teamToDelete['Team']['total_wins'];
        $teamToMergeInto['Team']['total_losses'] += $teamToDelete['Team']['total_losses'];
        $teamToMergeInto['Team']['total_cupdif'] += $teamToDelete['Team']['total_cupdif'];
        $this->Team->save($teamToMergeInto);
        
        $teamToDelete['Team']['status'] = 'Deleted';
        $this->Team->save($teamToDelete);
        
        //Game
        $Game = ClassRegistry::init('Game');
        $Game->recursive = -1;
        $games = $Game->find(
            'all', array('conditions'=>array('status <> '=>'Deleted',
            'OR'=>array(
            'team1_id'=>$teamIDToDelete,
            'team2_id'=>$teamIDToDelete,
            'winningteam_id'=>$teamIDToDelete)))
        );
        foreach ($games as $game) {
            if ($game['Game']['team1_id'] == $teamIDToDelete) {
                $game['Game']['team1_id'] = $teamIDToMergeInto; 
            }
            if ($game['Game']['team2_id'] == $teamIDToDelete) {
                $game['Game']['team2_id'] = $teamIDToMergeInto; 
            }
            if ($game['Game']['winningteam_id'] == $teamIDToDelete) {
                $game['Game']['winningteam_id'] = $teamIDToMergeInto; 
            }    
            $Game->save($game);        
        }   
        //Ratinghistory
        $Ratinghistory = ClassRegistry::init('Ratinghistory');
        $Ratinghistory->recursive = -1;
        $ratinghistories = $Ratinghistory->find('all', array('conditions'=>array('team_id'=>$teamIDToDelete)));
        foreach ($ratinghistories as $ratinghistory) {
            $ratinghistory['Ratinghistory']['team_id'] = $teamIDToMergeInto;
            $Ratinghistory->save($ratinghistory);
        }     
        
        // Teammate
        $Teammate = ClassRegistry::init('Teammate');
        $Teammate->recursive = -1;
        $teammates = $Teammate->find(
            'all', array('conditions'=>array('team_id'=>$teamIDToDelete,
            'status <>'=>'Deleted'))
        );
        foreach ($teammates as $teammate) {
            $teammate['Teammate']['status'] = 'Deleted';
            $Teammate->save($teammate);
        }
        
        //TeamsObject
        $TeamsObject = ClassRegistry::init('TeamsObject');
        $TeamsObject->recursive = -1;
        $teamsObjects = $TeamsObject->find('all', array('conditions'=>array('team_id'=>$teamIDToDelete,'status <>' =>'Deleted' )));
        foreach ($teamsObjects as $teamsObject) {
            //check to see if this one already exists
            $doesTeamObjectAlreadyExist = $TeamsObject->find(
                'first', array('conditions'=>array(
                'team_id'=>$teamIDToMergeInto,
                'model_id'=>$teamsObject['TeamsObject']['model_id'],
                'model'=>$teamsObject['TeamsObject']['model']))
            );
            if ($doesTeamObjectAlreadyExist) {
                $teamsObject['TeamsObject']['status'] = 'Deleted';   
            }
            else { 
                $teamsObject['TeamsObject']['team_id'] = $teamIDToMergeInto;
            }
            $TeamsObject->save($teamsObject);
        }
        
        //Image
        $Image = ClassRegistry::init('Image');
        $Image->recursive = -1;
        $images = $Image->find('all', array('conditions'=>array('model'=>'Team','model_id'=>$teamIDToDelete)));
        foreach ($images as $image) {
            $Image->delete($image['Image']['id']);
        }
        return $teamIDToDelete.' has been merged into '.$teamIDToMergeInto;
    }
    
    
    function getSinglesTeam_api($playerID) 
    {
        Configure::write('debug', 0);
        return $this->Team->getSinglesTeam($playerID);
        if (!$this->isLoggined()) {
            return "You are not logged in.";
        }
        $user = $this->Session->read('loggedUser');
        if ($user['id']==1) { return "You are not logged in."; 
        } 
        $matchingTeamResult = $this->doesMatchingTeamExist_api(array($playerID));
        // this needs to be fixed
        if ($matchingTeamResult) {
            return $matchingTeamResult;
        }
        else {
            $UserObject = ClassRegistry::init('User');
            $user = $UserObject->find(
                'first', array('conditions'=>array(
                'User.id'=>$playerID))
            );
            if (!$user) { return "Player does not exist."; 
            }
            if ($user['User']['status'] == 'Deleted') { return "Player has been deleted."; 
            }
            $username = $user['User']['lgn'];
            $firstname = $user['User']['firstname'];
            $lastname = $user['User']['lastname'];
            $email = $user['User']['email'];
            if (strlen($firstname) == 0) {
                if (strlen($lastname) == 0) {
                    //use lgn or email
                    if (strlen($username) == 0) {
                        //use the username portion of the email address
                        $exp_array = explode("@", $email);
                         $newTeamName = $my_nick=$exp_array['0'];
                    }
                    else { 
                        $newTeamName = $username; 
                    }
                }
                else {
                    $newTeamName = $lastname;
                }
            }
            else {
                if (strlen($lastname) == 0) {
                    $newTeamName = $firstname; 
                }
                else { 
                    $newTeamName = $firstname.' '.$lastname; 
                }
            }
            
            return $this->addNewTeam_api($newTeamName, 1, array($playerID));
        }
    }
    /**
     * This adds a new team to the site, then assigns it to the tournament
     */
    function addNewTeamToTournament_api($newTeamName,$seed,$numPlayers,$playerIDs,$tournID) 
    {
        Configure::write('debug', 0);  
        if (!$this->isLoggined()) {
            return "You are not logged in.";
        }
        $user = $this->Session->read('loggedUser');
        if ($user['id']==1) { return "You are not logged in."; 
        } 
    
        if (!($tournID > 0)) { return array('message'=>'Invalid parameters'); 
        }
        // ensure that logged user is a manager of this event
        if (!$this->Manager->isManager($user['id'], 'Event', $eventID)) {
            return "Access Denied"; 
        }                        
      
        $newTeam = $this->addNewTeam_api($newTeamName, $numPlayers, $playerIDs);
    
        if ($newTeam['message'] == "Matching Team Exists") {
            $newTeam = $newTeam['Team']; 
        }
        if (!isset($newTeam['Team'])) { return $newTeam; 
        }
        $EventObject = ClassRegistry::init('Event');

         // Now, we need to make sure there is not already another team using the same name in this event

         $requestedTeamName = $newTeamName;

         $nameToTry = $requestedTeamName;
         $unfreename = $TeamsObject->find(
             'all', array('conditions'=>array(
                    'model_id'=>$tournID,
                    'name'=>$nameToTry,
                    'model'=>'Event',
                    'status <>'=>'Deleted'))
         );  
         $ctr = 1;
         while (!empty($unfreename)) {
             $nameToTry = $requestedTeamName.$ctr;
             $unfreename = $TeamsObject->find(
                 'all', array('conditions'=>array(
                   'model_id'=>$tournID,
                   'name'=>$nameToTry,
                   'model'=>'Event',
                   'status <>'=>'Deleted'))
             );   
             $ctr++;          
            }
            $teamObject['TeamsObject']['id']         = null;   
            $teamObject['TeamsObject']['model']         = 'Event';
            $teamObject['TeamsObject']['model_id']     = $tournID;
            $teamObject['TeamsObject']['assigner_id']  = $user['id'];
            $teamObject['TeamsObject']['name']          = $nameToTry;
            $teamObject['TeamsObject']['status']          = 'Created';
            $teamObject['TeamsObject']['team_id']       = $newTeam['id'];
            $teamObject['TeamsObject']['seed']          = $seed;

            if($TeamsObject->save($teamObject)) {
                  return array('message'=>'ok',$newTeam);
            } else {
                return array('message'=>'Error while Storing Information');
            }  
    }
    function addNewTeams_api($teams) 
    {
        Configure::write('debug', 0);   
        $ctr = 0;
        foreach ($teams as $team) {
            $result[$ctr] = $this->addNewTeam_api($team['name'], $team['people_in_team'], $team['playerIDs']);    
            if ($result[$ctr] == 'You are not logged in.') {
                return 'You are not logged in.';            
            }
            $ctr++;
        }
        return $result;
    }
    /**
     * This adds a new team to the site. The players must be unique, i.e. if there is already a team
     * with the exact same players, that team is returned as the result
     * @param string $newTeamName
     * @param array  $playerIDs
     */
    function addNewTeam_api($newTeamName = '',$numPlayers = 0,$playerIDs = null) 
    {
            Configure::write('debug', 0);  
        if (!$this->isLoggined()) {
            return "You are not logged in.";
        }
        $user = $this->Session->read('loggedUser');
        if ($user['id']==1) { return "You are not logged in."; 
        } 
        if (count($playerIDs) > $numPlayers) { return array('message'=>"Too many players."); 
        }
        $requester = $user;
      
        $User = ClassRegistry::init('User');
        $User->recursive = -1;
        if (!$newTeamName) { return array('message'=>"Team Name Blank"); 
        }  
      
        if ($playerIDs) {     
            // First check to see if that each player is a real player 
            $matchingUsers = $User->find(
                'all', array('conditions'=>array(
                'id'=>$playerIDs,
                'is_deleted'=>0))
            );
            if (count($matchingUsers) != count($playerIDs)) {
                return array('message'=>"One of the players does not exist",
                'numplayers'=>$numPlayers,
                'playerIDs'=>$playerIDs); 
            } 
            
            // Now check to see if there is a team that has the same players
            if ($numPlayers == count($playerIDs)) {
                $result = $this->doesMatchingTeamExist_api($playerIDs);
                if (!empty($result)) {
                    $result['message'] = 'Matching Team Exists';
                    return $result;              
                }
            }
        }    
      
        $newTeam['Team']['name'] = $newTeamName;
        $newTeam['Team']['description'] = '';
        $newTeam['Team']['people_in_team'] = $numPlayers;
        if ($numPlayers == count($playerIDs)) {
            $newTeam['Team']['status'] = 'Completed'; 
        }
        else {
            $newTeam['Team']['status'] = 'Pending'; 
        }
        $this->Team->create();
        if (!$this->Team->save($newTeam)) { return array('mesage'=>"Team could not be saved"); 
        }
        $newTeamID = $this->Team->getLastInsertID();

        if ($playerIDs) {  
            foreach ($playerIDs as $playerID) { 
                unset($newTeammate);
                $this->Team->Teammate->create();
                $newTeammate['Teammate']['requester_id'] = $requester['id'];  
                $newTeammate['Teammate']['team_id'] = $newTeamID;      
                $newTeammate['Teammate']['status'] = 'Pending';
                 $newTeammate['Teammate']['user_id'] = $playerID;
                if (!$this->Team->Teammate->save($newTeammate)) {
                    return array('message'=>"Team saved, but players could not be added.",'Team'=>$newTeam); 
                }      
            }
        }
        $returnTeam = $this->Team->find(
            'first', array(
            'conditions'=>array(
              'Team.id'=>$newTeamID),
            'contain'=>array('User'))
        );
        foreach ($returnTeam['User'] as &$user) {
            unset($user['pwd']);
            $user['email'] = 'hidden';
        }
        $returnTeam['message'] = 'ok';
        return $returnTeam;
    }  
    /**
  * This function takes an array. Each element of the array is an array of playerIDs
  * It returns an array containing all teams that match.
  * 
  * @param mixed $arrayOfPlayerArrays
  */
    function doMatchingTeamsExist_api($arrayOfPlayerArrays) 
    {
              Configure::write('debug', 0);  
        $ctr = 0;
        foreach ($arrayOfPlayerArrays as $playerIDS) {
            $result = $this->doesMatchingTeamExist_api($playerIDS);
            if ($result) {
                $returnArray[$ctr] = $result;
                $ctr++;
            }
        }
        //  return $ctr;
        return $returnArray;
    }
    /**
  * This function determines whether thers is currently a team that has exactly the same users as in $playerIDs 
  * Returns false if there is no team, and the Team ID number if there is a team
  * @param mixed $teamName
  * @param mixed $playerIDs
  */
    function doesMatchingTeamExist_api($playerIDs) 
    {  
        $result = $this->Team->doesMatchingTeamExistByPlayerIDs($playerIDs);
        if ($result) {
            foreach ($result['User'] as &$user) {
                unset($user['email']);
            }
        }
        return $result;
    }
    /**
   * API Function
   * Get all teams assigned to an event
   * author:skinny
   */
    function findTeams_api($teamName, $playerEmail, $playerLastName, $playerUsername) 
    {
              Configure::write('debug', 0);  
        //this currently returns a full user. we should remove email address
        if ($teamName == "" && $playerEmail=="" && $playerLastName=="" && $playerUsername="") { return null; 
        }
        $searchConditions =  array();
        $searchOnPlayers = false;
        if ($playerEmail <> "") {
            $searchOnPlayers = true;
            $searchConditions['User.email Like'] = $playerEmail;
        }
        if ($playerUsername <> "") {
            $searchOnPlayers = true;
            $searchConditions['User.lgn Like'] = $playerUsername;
        }
        if ($playerLastName <> "") {
            $searchOnPlayers = true;
            $searchConditions['User.lastname'] = $playerLastName;
        }
        $searchConditions['Team.status <>'] = 'Deleted';
        if ($searchOnPlayers) {
            $Teammates = ClassRegistry::init('Teammate');
            $Teammates->recursive = 1;
            $matchingteammates = $Teammates->find('all', array('fields'=>array('Team.id'),'conditions'=>$searchConditions));
            $matchingTeamIDSByPlayer = Set::extract($matchingteammates, '{n}.Team.id');
        }
        if ($teamName <> "") {
            // first search Teams
            $TeamsObject = ClassRegistry::init('TeamsObject');
            $TeamsObject->recursive = -1;
            $matchingTeamsByAssignment = $TeamsObject->find(
                'all', array('fields'=>array('DISTINCT team_id'),
                'conditions'=>array(
                'status != '=>'Deleted',
                'name Like'=>$teamName))
            );
            $this->Team->recursive = -1;
            $matchingTeamsByName = $this->Team->find(
                'all', array('conditions'=>array(
                'status <>' => 'Deleted',
                'name Like'=>$teamName),'fields'=>array('id'))
            );
            if (!empty($matchingTeamsByName) && !empty($matchingTeamsByAssignment)) {
                $matchingTeamIDSByTeamName = array_merge(
                    Set::extract($matchingTeamsByAssignment, '{n}.TeamsObject.team_id'),
                    Set::extract($matchingTeamsByName, '{n}.Team.id')
                ); 
            }
            else if (!empty($matchingTeamsByName)) {
                $matchingTeamIDSByTeamName = Set::extract($matchingTeamsByName, '{n}.Team.id'); 
            }
            else if (!empty($matchingTeamsByAssignment)) {
                $matchingTeamIDSByTeamName =Set::extract($matchingTeamsByAssignment, '{n}.TeamsObject.team_id'); 
            }
            else { $matchingTeamIDSByTeamName = array(); 
            }
        }

        if ($teamName <> "" && $searchOnPlayers) {
            $searchIDs = array_intersect($matchingTeamIDSByPlayer, $matchingTeamIDSByTeamName); 
        }
        else if ($teamName <> "") {
            $searchIDs = $matchingTeamIDSByTeamName;
        }
        else if ($searchOnPlayers) {
            $searchIDs = $matchingTeamIDSByPlayer; 
        }
        else {
            $searchIDs = array(); 
        }
        $this->Team->recursive = 1;
        $returnArray = $this->Team->find(
            'all', array('contain'=>array('User'),'conditions'=>array(
            'id'=>$searchIDs))
        );
        //  return $returnArray;
        //hide email and password
        foreach ($returnArray as &$t) {
            foreach ($t['User'] as &$u)  {
                $u['email'] = 'hidden';
                unset($u['pwd']);
            }
        }
        return $returnArray;
    }

    function addPlayersToTeam_api($teamID,$playerIDS) 
    {
              Configure::write('debug', 0);  
        $result = 'ok';
        foreach ($playerIDS as $playerID) {
            $result = $this->addPlayerToTeam_api($teamID, $playerID);
            if ($result == 'You are not logged in.') { return $result; 
            }
        }
        return $result;
    }
    /**
  * This function adds a player to a team. We must check a few things:
  * 1) The requester must be ther user who created the team
  * 2) The team is not yet 'full'
  * 3) If adding this player will make the team full, there is not already another team with the exact same players
  * 
  * @param  mixed $teamID
  * @param  mixed $playerID
  * @return mixed
  */
    function addPlayerToTeam_api($teamID,$playerID) 
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
    
        $User = ClassRegistry::init('User');
        $User->recursive = -1;
        $newPlayer = $User->find('first', array('conditions'=>array('id'=>$playerID)));
        if (!$newPlayer) { return array('message'=>"Player does not exist"); 
        }
    
        // Get the team
        $this->Team->recursive = -1;
        $team = $this->Team->find('first', array('conditions'=>array('id'=>$teamID)));
        if ($team['Team']['status']=='Completed') { return array('message'=>'Team is already complete.'); 
        }
        // get the current teammates
        $Teammates = ClassRegistry::init('Teammate');
        $Teammates->recursive = -1;
        $currentTeammates = $Teammates->find(
            'all', array('conditions'=>array('team_id'=>$teamID,
            'status <>'=>'Deleted'))
        );
        // make sure that the new player isn't already on the team
        foreach ($currentTeammates as $currentTeammate) {
            if ($currentTeammate['Teammate']['user_id'] == $playerID) {
                return array('message'=>"Player is already on team"); 
            }
        }
    
        // make sure the team isn't already full
        if ($team['Team']['people_in_team'] <= count($currentTeammates)) {
            return array('message'=>'Team is already full.'); 
        }
        // Now, we need to make sure this team doesn't already exist. 
        if ($team['Team']['people_in_team'] == (count($currentTeammates) + 1)) {
            $matchingTeam = $this->doesMatchingTeamExist_api(
                array_merge(array($playerID), Set::extract($currentTeammates, '{n}.Teammates.user_id'))
            );
            if ($matchingTeam) {
                return array('message'=>'Matching Team Exists','Team'=>$matchingTeam['Team']); 
            }
        }
    
        // Create a new teammate
        $newTeammate['Teammate']['requester_id'] = $user['id'];
        $newTeammate['Teammate']['user_id'] = $newPlayer['User']['id'];
        $newTeammate['Teammate']['team_id'] = $teamID;
        $newTeammate['Teammate']['status'] = 'Pending';
    
        $teamsObject = $Teammates->create();
        if (!$Teammates->save($newTeammate)) { return array('message'=>'Could not add new player.'); 
        }
        return array('message'=>'ok');
    }
    function getTeam_api($teamID) 
    {
        Configure::write('debug', 0);  
        $this->Team->recursive = 1;
        $matchingTeamObject = $this->Team->find(
            'first', array(
            'contain'=>array('User'),
            'conditions'=>array(
            'Team.status <>'=>'Deleted',
            'Team.id'=>$teamID
            ))
        );
        return $matchingTeamObject;
    }
    /**
  * API Function
  * Input is an array of teamIds
  * 
  * @param  mixed $teamIDs
  * @return mixed
  */
    function getTeams_api($teamIDs) 
    {
              Configure::write('debug', 0);  
        $matchingTeamObject = $this->Team->find(
            'all', array(
            'contain'=>array('User'),
            'conditions'=>array(
            'Team.status <>'=>'Deleted',
            'Team.id'=>$teamIDs
            ))
        );
        return $matchingTeamObject;
    }
    /**
   * API Function
   * Get the teams assigned to an event
   * @author: skinny
   */
    function getEventTeams_api($eventID) 
    {
              Configure::write('debug', 0);  
        if (!$this->isLoggined()) {
            return "You are not logged in.";
        }
        $TeamsObject = ClassRegistry::init('TeamsObject');
        $TeamsObject->recursive = -1;
        $matchingTeamsObjects = $TeamsObject->find(
            'all', array(
            'order' => 'seed',
            'conditions'=>array(
            'model'=>'Event',
            'model_id'=>$eventID,
            'status <>'=>'Deleted'
            ))
        );
        $teamsIDs = Set::extract($matchingTeamsObjects, '{n}.TeamsObject.team_id');
        $Teammate = ClassRegistry::init('Teammate');
        $Teammate->recursive = 0;
        $matchingTeammates = $Teammate->find(
            'all', array('conditions'=>array(
            'team_id'=>$teamsIDs,
            'Team.status <>'=>'Deleted'))
        );
        $this->Team->recursive = -1;
        $matchingTeams = $this->Team->find(
            'all', array('conditions'=>array(
            'id'=>$teamsIDs,
            'status <>'=>'Deleted'))
        );
        //if there are no matching teams, return empty array
        if (empty($matchingTeams)) { return array(); 
        }
        $ctr = 0;
        //now loop through and assign teammates to teams, and 'teamsobjects' to teams

        foreach ($matchingTeams as $matchingTeam) {
            $teammateCounter = 0;
            $teamToAdd['Team'] = $matchingTeam['Team'];
            $teamID = $matchingTeam['Team']['id'];
            foreach ($matchingTeammates as $matchingTeammate) {
                if ($matchingTeammate['Teammate']['team_id'] == $teamID) {
                    $teamToAdd['Users'][$teammateCounter]['id'] = $matchingTeammate['User']['id'];
                    $teamToAdd['Users'][$teammateCounter]['firstname'] = $matchingTeammate['User']['firstname'];
                    $teamToAdd['Users'][$teammateCounter]['lastname'] = $matchingTeammate['User']['lastname'];
                    $teamToAdd['Users'][$teammateCounter]['lgn'] = $matchingTeammate['User']['lgn'];
                    $teammateCounter++;

                }
            }
            //if there are no teammates, make 'Users' an empty array
            if ($teammateCounter == 0) {
                $teamToAdd['Users'] = array(); 
            }
            foreach ($matchingTeamsObjects as $matchingTeamsObject) {
                if ($matchingTeamsObject['TeamsObject']['team_id'] == $teamID) {
                    $teamToAdd['TeamsObject'] = $matchingTeamsObject['TeamsObject'];
                    $teamToAdd['Team']['name'] = $matchingTeamsObject['TeamsObject']['name'];
                }
            }
            $returnArray[$ctr] = $teamToAdd;
            $ctr++;
            $teamToAdd = null;
        }
        return $returnArray;
    }
    /**
   * Show all teams to the administrator
   * @author vovich
   */
    function index() 
    {
        $this->Access->checkAccess('Team', 'l');

        $conditions = array();

        /* filter Getting data from the session or from the form*/
        if(!empty($this->request->data['TeamFilter'])) {
            $this->Session->write('TeamFilter', $this->request->data['TeamFilter']);
        }elseif($this->Session->check('TeamFilter')) {
            $this->request->data['TeamFilter']=$this->Session->read('TeamFilter');
        }

        //Prepare data for the filter
        if (!empty( $this->request->data['TeamFilter']['name'])) {
            $conditions['Team.name LIKE'] = $this->request->data['TeamFilter']['name']; 
        }

        if (!empty( $this->request->data['TeamFilter']['status'])) {
            $conditions['Team.status'] = $this->request->data['TeamFilter']['status'];
        }
        // User conditions
        if (!empty( $this->request->data['TeamFilter']['lgn'])) {
            $foo = $this->Team->Teammate->find('all', array('fields'=>array('DISTINCT Teammate.team_id'),'contain'=>array('User'),'conditions'=>array('User.lgn LIKE'=>$this->request->data['TeamFilter']['lgn'])));
            $teamsIDs = Set::extract($foo, '{n}.Teammate.team_id');
            $conditions['Team.id'] = $teamsIDs;
        }
        //EOF user conditions

        $this->Team->recursive = 0;
        $teams = $this->paginate('Team', $conditions);

        $this->set('teams', $teams);
        $this->set('statuses', $this->teamStatuses);
    }



    /**
   *  show teams bu logged user
   * @author vovich
   */
    function myteams() 
    {

        $this->checkLoggined();

        $user  = $this->Session->read('loggedUser');
        $Teammate = ClassRegistry::init('Teammate');
        $Teammate->recursive = 1;
        $myTeams = $Teammate->find('all', array('conditions'=>array('Teammate.user_id'=>$user['id'],'Teammate.status'=>array('Creator','Accepted'),'Team.status <>'=>'Deleted')));
        $Teammate->recursive = 1;
        $myInvites = $Teammate->find('all', array('conditions'=>array('Teammate.user_id'=>$user['id'],'Teammate.status'=>'Pending','Team.status'=>array('Created','Pending','Completed'))));
        unset($Teammate);

        $this->set('user', $user);
        $this->set('myTeams', $myTeams);
        $this->set('myInvites', $myInvites);

    }

    /**
   * Create new Team
   * @author vovich
   */
    function add() 
    {
        $this->Access->checkAccess('Team', 'c');
        if (!$this->isLoggined()) {
             $this->Session->setFlash('You are not logged', 'flash_error');
             $this->redirect(MAIN_SERVER);
        }
        $user = $this->Session->read('loggedUser');

        if (!empty($this->request->data)) {
            $this->request->data['Team']['status'] = 'Created';
            if ($this->request->data['Team']['people_in_team'] ==1) {
                $this->request->data['Team']['status'] = 'Completed';
            }
      
            $this->request->data['Team']['creator_user_id'] = $this->getUserID();
            $this->Team->save($this->request->data);
            $teamID = $this->Team->getLastInsertID();
            $Teammate = ClassRegistry::init('Teammate');
            $Teammate->save(array('user_id'=>$user['id'],'team_id'=>$teamID,'status'=>'Creator','requester_id'=>$user['id']));
            unset($Teammate);

            $this->Session->setFlash('Team has been created.', 'flash_success');
             $this->redirect(array('controller'=>'teams','action'=>'myteams'));
        }

    }

    /**
   * Update team
   * @author vovich
   * @param string $slug
   * @param int    $teamID
   * @access owner (statuses Creator and Accepted)
   */
    function edit($slug = null, $teamID = null) 
    {

        $this->checkLoggined();
        //Getting owners for checking access
        $Teammate = ClassRegistry::init('Teammate');
        $teammates = $Teammate->find('list', array('fields'=>array('user_id','user_id'),'conditions'=>array('team_id'=>$teamID,'Teammate.status'=>array('Creator','Accepted'))));
        $this->Access->checkAccess('Team', 'u', $teammates);
        $Teammate->recursive = 0;
        $teammates = $Teammate->find('all', array('conditions'=>array('team_id'=>$teamID),'contain'=>array('User')));
        unset($Teammate);

        $teamInfo = $this->Team->find('first', array('conditions'=>array('Team.id'=>$teamID)));
        if (empty($teamInfo)) {
            $this->Session->setFlash('Can not find such team.', 'flash_error');
            $this->redirect('/');
        }
        $user = $this->Session->read('loggedUser');

        //Storing data
        if (!empty($this->request->data)) {

            $Teammate = ClassRegistry::init('Teammate');
            $teammatesCnt = $Teammate->find('count', array('conditions'=>array('team_id'=>$teamID,'Teammate.status'=>array('Creator','Accepted'))));

            if ($teammatesCnt>=$this->request->data['Team']['people_in_team']) {
                $this->request->data['Team']['people_in_team'] = $teammatesCnt;
                $this->request->data['Team']['status']                   = "Completed";

                $doesTeamAlreadyExist = $this->Team->doesMatchingTeamExistByTeamID($teamID);
                if ($doesTeamAlreadyExist) {
                    /**
                * Merge this team into the existing one
                */
                    $result = $this->mergeTwoTeams($teamID, $doesTeamAlreadyExist['Team']['id']);                
                    $this->Session->setFlash('Duplicate team exists. Teams have been merged. Remember, you can use a different team name for each Event you play in.', 'flash_error');
                    $this->redirect(
                        MAIN_SERVER.'/nation/beer-pong-teams/team-info/'.$doesTeamAlreadyExist['Team']['slug'].
                        '/'.$doesTeamAlreadyExist['Team']['id']
                    ); 
                }
                /*
                if ($this->request->data['Team']['status']  != $teamInfo['Team']['status']  && $this->Team->checkSameTeams($user['id'],$teamID,$teammatesCnt)) {
                $this->Session->setFlash('You can not update people in team such as a team with the same teammates already exists.');
                $this->redirect(array('controller'=>'teams','action'=>'edit',$slug,$teamID));
                }
                */
            }
      
            if ($this->Team->save($this->request->data)) {

                //sending an email to the all users in team that completed
                $Teammate->recursive = 0;
                $usersInTeam = $Teammate->find('all', array('conditions'=>array('team_id'=>$teamID, 'Teammate.status'=>array('Creator','Accepted') )));
                $_teammates  ="";
                foreach ($usersInTeam as $userInTeam) {
                    $_teammates .= "Nick name: ".$userInTeam['User']['lgn']."<br>";
                     $_teammates .= "First name: ".$userInTeam['User']['firstname']."<br>";
                     $_teammates .= "Last name: ".$userInTeam['User']['lastname']."<br>";
                     $_teammates .= "Email name: ".$userInTeam['User']['email']."<br>";
                     $_teammates .= "<br>";
                }

                foreach ($usersInTeam as $userInTeam) {
                    $result = $this->sendMailMessage(
                        'TeamIsCompleted', array(
                        '{TEAMNAME}'       => $this->request->data['Team']['name'],
                        '{FNAME}'              => $userInTeam['User']['firstname'],
                        '{LNAME}'              => $userInTeam['User']['lastname'],
                        '{DESCRIPTION}'    => $this->request->data['Team']['description'],
                        '{TEAMMATES}'      => $_teammates,
                        '{VIEWTEAMLINK}'  => "<a href='" . MAIN_SERVER . "/nation/beer-pong-teams/team-info/{$slug}/{$teamID}'>View team</a>"
                        ),
                        $userInTeam['User']['email']
                    );
                }

                  $this->Session->setFlash('The team has been updated.', 'flash_success');
                  $this->redirect(array('controller'=>'teams','action'=>'edit',$slug,$teamID));
                  //$this->redirect("/teams/edit/".$slug."/".$teamID);
            }
        }//EOF storing data

        $this->request->data = $teamInfo;
        if ($this->request->data['Team']['status']=='Deleted') {
              $this->Session->setFlash('This team has been deleted', 'flash_success');
              $this->redirect(array('controller'=>'teams','action'=>'view',$slug,$teamID));
        }

        $images=$this->Image->myImages('Team', $teamID);
        $user = $this->Session->read('loggedUser');
    
        if (!isset($Teammate)) {
            $Teammate = ClassRegistry::init('Teammate');
        }
        $canInviteMoreTeammates = true;
        $allTeammatesIncludingPendingCnt = $Teammate->find('count', array('conditions'=>array('team_id'=>$teamID,'Teammate.status'=>array('Creator','Accepted','Pending'))));
        if ($allTeammatesIncludingPendingCnt >= $this->request->data['Team']['people_in_team']) {
            $canInviteMoreTeammates = false;
        }
    
        $this->set('canInviteMoreTeammates', $canInviteMoreTeammates);
        $this->set('user', $user);
        $this->set('images', $images);
        $this->set('teammates', $teammates);

    }

    /**
   * View team assigments
   * @author vovich
   * @param string $slug
   * @param int    $teamID
   * @access All
   */
    function assigments($slug = null, $teamID = null) 
    {
        //Getting team's assigments

        $this->TeamsObject->recursive = 0;
        $teamAssigments = $this->paginate('TeamsObject', array('team_id'=>$teamID));

        $tournEvents = $this->Team->getTournEventsList();

        $this->set('tournEvents', $tournEvents);
        $user = $this->Session->read('loggedUser');

        $this->set('teamAssigments', $teamAssigments);
        $this->set('user', $user);
        $this->set('teamID', $teamID);

    }

    /**
   * Assign team to the Tournevent
   * @author vovich
   * @param int $signupID
   * @access All
   */
    function assignTeam($signupID = null) 
    {
        Configure::write('debug', 0);
        $information = 'Choose the team.';

        $this->checkLoggined();

        $user = $this->Session->read('loggedUser');

        //Getting signup information
        $Signup = ClassRegistry::init('Signup');
        $Signup->recursive = 0;
        $signupDetails = $Signup->find('first', array('conditions' => array( 'Signup.id' => $signupID )));

        if (empty($signupDetails) ) {
            $this->Session->setFlash('Can not find such sign up', 'flash_error');
            $this->redirect(MAIN_SERVER);
        }

        $Package = ClassRegistry::init('Package');
        $Package->recursive = -1;
        $allowTeams = $Package->field('allow_team', array('Package.id' => $signupDetails['Packagedetails']['package_id']));
        if (!$allowTeams) {
            $this->Session->setFlash('Teams are not allowed for this package', 'flash_error');
            return $this->redirect(MAIN_SERVER);
        }
        // getting all use team
        $_teams =  $this->Team->getUserTeams($signupDetails['Signup']['user_id'], " Team.id, Team.name ");
        if (empty($_teams)) {
            $this->Session->setFlash('Can not find teams', 'flash_error');
            $this->redirect(MAIN_SERVER);
        }
        $teams = array();
        if (count($_teams)>1) {
            $teams[0] = 'Choose the team.';
        } else {
             $information  = $this->requestAction("/teams/getTeamInformation/".$_teams[0]['Team']['id']."/".$signupDetails['Signup']['model_id']."/".$signupDetails['Signup']['model'], array( 'return' => 0 ));
        }

        foreach ($_teams as $team) {
              $teams[$team['Team']['id']] = $team['Team']['name'];
        }
        unset($_teams);

        $this->set('information', $information);
        $this->set('teams', $teams);
        $this->set('signupDetails', $signupDetails);
    }



    /**
   * View team
   * @author vovich
   * @param string $slug
   * @param int    $teamID
   * @access All
   */
    function view($slug = null, $teamID = null,$alsoPlayedInEventID = null) 
    {
        //Explanation: Teams can have different names for different events. Sometimes, a person will follow a link from 
        //somewhere else and be confused at the Team Name. Example: 'Team X'' is playing in Event Y under the name 'Different Team'.
        //When looking at the results from Event Y, a user clicks on the link for 'Different Teams' profile, only to come to the
        //page for 'Team X's profile. However, we want the end URL for a profile to also be the same, just in case the link
        //gets shared on Facebook. So, we take the event they just played in, cache it to the session variable, and then redirect
        //so that its not in the URL anymore
        if (isset($alsoPlayedInEventID)) {
            $this->Session->write('alsoPlayedInEventID', $alsoPlayedInEventID);
            $this->redirect('/teams/view/'.$slug.'/'.$teamID);
        }
        $checkAlsoPlayedInEventID = $this->Session->read('alsoPlayedInEventID');
        if ($checkAlsoPlayedInEventID) {
            $alsoPlayedInEventID = $checkAlsoPlayedInEventID; 
        }
      
        $this->checkLoggined();
        if (!$teamID) {
             $this->Session->setFlash('Team ID error', 'flash_error');
             $this->redirect('/');
        }

        //Getting owners for checking access
        $Teammate = ClassRegistry::init('Teammate');
        $teammates = $Teammate->find('list', array('fields'=>array('user_id','user_id'),'conditions'=>array('team_id'=>$teamID)));
        $this->Access->checkAccess('Team', 'r', $teammates);
        $Teammate->recursive = 0;
        $teammates = $Teammate->find('all', array('conditions'=>array('team_id' => $teamID, 'status' => array('Accepted', 'Creator','Pending')),'contain'=>array('User' => array('Address'))));
        foreach ($teammates as $teammateKey => $teamma) {
            $teammates[$teammateKey]['stats'] = $this->Team->getPlayerStats($teammates[$teammateKey]['User']['id']);
        }

        unset($Teammate);

        $user = $this->Session->read('loggedUser');
        $this->request->data  = $this->Team->find('first', array('conditions'=>array('Team.id'=>$teamID)));   
    
        $images=$this->Image->myImages('Team', $teamID);
        $this->TeamsObject->recursive = 0;
        $this->paginate = array(
        'conditions' => array('TeamsObject.team_id' => $teamID, 'TeamsObject.status <>' => 'Deleted'),
        'order' => array('Event.end_date' => 'DESC')
        );
        $teamAssigments = $this->paginate('TeamsObject');
    
        unset($alsoPlaysAs);
        if (isset($alsoPlayedInEventID)) {
            $this->TeamsObject->recursive = -1;
            $eventThatTeamPlayedIn = $this->TeamsObject->find(
                'first', array('conditions'=>array(
                'model_id'=>$alsoPlayedInEventID,
                'model'=>'Event',
                'status <> '=>'Deleted',
                'team_id'=>$teamID))
            );
            if ($eventThatTeamPlayedIn && ($eventThatTeamPlayedIn['TeamsObject']['name'] != $this->request->data['Team']['name'])) {
                $alsoPlaysAs = $eventThatTeamPlayedIn['TeamsObject']['name']; 
            }            
            else { 
                unset($alsoPlaysAs); 
            }
        }
    
        $this->set('averageWin', $this->Team->calcAverageWins($this->request->data['Team']['total_wins'], $this->request->data['Team']['total_losses']));
        $this->set('averageCupdif', $this->Team->calcAverageCupdif($this->request->data['Team']['total_wins'], $this->request->data['Team']['total_losses'], $this->request->data['Team']['total_cupdif']));
        $this->set('teamAssigments', $teamAssigments);
        $this->set('images', $images);
        $this->set('teammates', $teammates);
        $this->set('user', $user);
        if (isset($alsoPlaysAs)) {
            $this->set('alsoPlaysAs', $alsoPlaysAs); 
        }
    }
    /**
   * Create new team and assign to new event
   * @author Oleg D.
   */
    function create_and_assign($model, $modelID, $signupID = null, $peopleInTeam = 2) 
    {
        $this->Access->checkAccess('Team', 'c');
        if (!empty($this->request->data)) {
            if (empty($this->request->data['Team']['people_in_team'])) {
                $this->request->data['Team']['people_in_team'] = 2;
            }
            $this->request->data['Team']['status'] = 'Created';
            if ($this->request->data['Team']['people_in_team'] ==1) {
                $this->request->data['Team']['status'] = 'Completed';
            }

            $this->request->data['Team']['creator_user_id'] = $this->getUserID();
            $this->Team->save($this->request->data);
            $teamID = $this->Team->getLastInsertID();
            $Teammate = ClassRegistry::init('Teammate');

            $Teammate->save(array('user_id' => $this->getUserID(),'team_id' => $teamID, 'status' => 'Creator', 'requester_id' => $this->getUserID()));
            unset($Teammate);

            //$this->Session->setFlash('Team has been created.');
            $this->Session->write('new_created_team_id', $teamID);
            $this->redirect('/signups/signupDetails/' . $signupID . '/tab-team/');
        }

        $this->set(compact('model', 'modelID', 'signupID', 'peopleInTeam'));
    
    } 
    /**
   * AJAX assign team to event
   * @author Oleg D.
   */ 
    function ajax_assign_from_signup($model, $modelID, $teamID = null, $signupID = null, $teamNameToUse = null) 
    {
        if (!$teamID) {
            $teamID = intval($this->request->data['TournEvent']['team_id']);
        }
        //Inserted by Skinny. We're now giving people the ability to use a different team name for this event. Pass that name to the next
        //screen. If its an empty string, we'll use the current team name
        if (!$teamNameToUse) {
            $teamNameToUse = $this->request->data['TournEvent']['alternate_name']; 
        }
    
        $team = $this->Team->find('first', array('conditions' => array('id' => $teamID)));
    
        $Teammate = ClassRegistry::init('Teammate');
        $teammates = $Teammate->find('all', array('conditions'=>array('team_id'=>$teamID, 'NOT' => array('Teammate.status' => array('Deleted', 'Declined'))),'contain'=>array('User')));
        //pr($teammates);
        //pr($team);
        $neededTeammatesCnt = intval($team['Team']['people_in_team'] - count($teammates));
        if (count($teammates) > $team['Team']['people_in_team']) {
            exit('Error T.1.1');
        }
        //Added by Skinny. If the current user is pending, lets accept him now. If this makes the team complete, mark team as completed.
        if ($neededTeammatesCnt > 0) {
            $teamComplete = false; 
        }
        else {    
            $teamComplete = true; 
        }
  
        foreach ($teammates as &$teammate) {
            // If this is the current user, and he's pending, accept the invite
            if ($teammate['Teammate']['status'] == 'Pending' && $teammate['Teammate']['user_id'] == $this->getUserID()) {
                $teammate['Teammate']['status'] = 'Accepted';
                $this->Teammate->save($teammate);
            }
            // Check each teammate. If none of them are still pending, the team is complete
            if ($teammate['Teammate']['status'] == 'Pending') {
                $teamComplete = false; 
            }
        }
        unset($teammate); // Added by Oleg D.
    
        if ($teamComplete && $team['Team']['status'] != "Completed") {
            $team['Team']['status'] = 'Completed';
            $this->Team->save($team);
        }
    

    
        if ($signupID) {
            $signup = $this->Signup->find('first', array('conditions' => array('Signup.id' => $signupID), 'contain' => array('Event')));
            if ($signup['Signup']['for_team']) {
                foreach ($teammates as $teammate) {
                    if ($teammate['User']['id'] != $signup['Signup']['user_id'] && !$this->SignupsUser->find('count', array('conditions' => array('SignupsUser.user_id' => $teammate['User']['id'], 'SignupsUser.signup_id' => $signup['Signup']['id'])))) {
                        $this->SignupsUser->create();
                        $this->SignupsUser->save(array('user_id' => $teammate['User']['id'], 'signup_id' => $signup['Signup']['id']));
                        $result = $this->sendMailMessage(
                            'SignupInvitation', array(
                             '{FNAME}'         => $teammate['User']['firstname'],
                             '{LNAME}'         => $teammate['User']['lastname'],
                             '{EVENT_NAME}'    => $signup['Event']['name'],
                             '{SIGNUP_LINK}'  =>  MAIN_SERVER . "/signups/signupDetailsTeammate/" . $signup['Signup']['id']
                              ),
                            $teammate['User']['email']
                        );                    
                    }                    
                }
            }
        }
        /// Send invitintation if paid for entire team!
    
        $this->set(compact('model', 'modelID', 'signupID', 'teamID', 'team', 'teammates', 'neededTeammatesCnt', 'teamNameToUse'));          
    }  
    /**
   *  Accept user to the team
   *  @author vovich
   *  @param int    $teamID
   *  @param string $userName
   */
    function accept($teamID = null, $userName = null, $signupID = null) 
    {
        $this->layout = false;
        $userName = urldecode($userName);
        if (!empty($teamID) && !empty($userName)) {
            //Getting User information
            $conditions = array('lgn'=>$userName);
            $this->Team->User->recursive = -1;
            $userInfo  = $this->Team->User->find('first', array('conditions'=>$conditions));
            if (empty($userInfo)) {
                $this->Session->setFlash('Can not find such user.', 'flash_error');
                $this->redirect('/');
            }
            $teamInfo = $this->Team->find('first', array('conditions'=>array('Team.id'=>$teamID,'Team.status <>'=>'Deleted')));
            if (empty($teamInfo)) {
                $this->Session->setFlash('Can not find such team.', 'flash_error');
                $this->redirect("/");
          }

            $Teammate = ClassRegistry::init('Teammate');

            $Teammate->recursive = -1;
            $teammateInfo = $Teammate->find('first', array('conditions'=>array('user_id'=>$userInfo['User']['id'],'team_id'=>$teamID)));
            $Teammate->recursive = -1;
            $teammatesCnt =  $Teammate->find('count', array('conditions'=>array('team_id'=>$teamID,'Teammate.status'=>array('Creator','Accepted'))));
            //check if user was invited
            if (empty($teammateInfo)) {
                $this->Session->setFlash('You were not invited to this team.', 'flash_error');
                $this->redirect(array('controller'=>'teams','action'=>'view', $teamInfo['Team']['slug'],$teamID));
            }
            //check invited user status
            if ($teammateInfo['Teammate']['status']!="Pending") {
                $this->Session->setFlash('You can not be added to this team because your status in team is '.$teammateInfo['Teammate']['status'], 'flash_error');
                $this->redirect(array('controller'=>'teams','action'=>'view',$teamInfo['Team']['slug'],$teamID));
            }
            //team people in team <= teams count
            if ($teamInfo['Team']['people_in_team']<=$teammatesCnt) {
                $this->Session->setFlash('You can not be added to this team such as this team is full(completed)', 'flash_error');
                $this->redirect(array('controller'=>'teams','action'=>'view',$teamInfo['Team']['slug'],$teamID));
            }
            //checking team status
            //edited by Skinny - removed check for Completed....we're already checking the teammate count....if the team isn't full,
            //ignore the Completed status
            if ($teamInfo['Team']['status']=="Deleted") {
                $this->Session->setFlash('You can not be added to this team because team status is '.$teamInfo['Team']['status'], 'flash_error');
                $this->redirect(array('controller'=>'teams','action'=>'view',$teamInfo['Team']['slug'],$teamID));
            }

            //update team status if needed
            if ($teammatesCnt+1 == $teamInfo['Team']['people_in_team']) {
                $doesTeamAlreadyExist = $this->Team->doesMatchingTeamExistByTeamID($teamID); 
                if ($doesTeamAlreadyExist) {
                    /**
                * Merge this team into the existing one
                */
                    $result = $this->mergeTwoTeams($teamID, $doesTeamAlreadyExist['Team']['id']);                
                    $this->Session->setFlash('Duplicate team exists. Teams have been merged. Remember, you can use a different team name for each Event you play in.', 'flash_error');
                    $this->redirect(
                        MAIN_SERVER.'/nation/beer-pong-teams/team-info/'.$doesTeamAlreadyExist['Team']['slug'].
                        '/'.$doesTeamAlreadyExist['Team']['id']
                    ); 
                }
          
                /* 	 if ($this->Team->checkSameTeams($userInfo['User']['id'],$teamID,$teamInfo['Team']['people_in_team'])) { 
                $this->Session->setFlash('Another team with the same teammates already exists.');
                 $this->redirect('/');
                } */

                $teamInfo['Team']['status'] = 'Completed';
                $this->Team->save($teamInfo);

                //sending an email to the all users in team that completed
                $Teammate->recursive = 0;
                $usersInTeam = $Teammate->find('all', array('conditions'=>array('team_id'=>$teamID, 'Teammate.status'=>array('Creator','Accepted') )));
                $_teammates  ="";
                foreach ($usersInTeam as $userInTeam) {
                    $_teammates .= "Nick name: ".$userInTeam['User']['lgn']."<br>";
                    $_teammates .= "First name: ".$userInTeam['User']['firstname']."<br>";
                    $_teammates .= "Last name: ".$userInTeam['User']['lastname']."<br>";
                    $_teammates .= "Email name: ".$userInTeam['User']['email']."<br>";
                    $_teammates .="<br>";
                }

                foreach ($usersInTeam as $userInTeam) {
                    $result = $this->sendMailMessage(
                        'TeamIsCompleted', array(
                        '{TEAMNAME}'      => $teamInfo['Team']['name'],
                        '{FNAME}'         => $userInTeam['User']['firstname'],
                        '{LNAME}'         => $userInTeam['User']['lastname'],
                        '{DESCRIPTION}'   => $teamInfo['Team']['description'],
                        '{TEAMMATES}'     => $_teammates,
                        '{VIEWTEAMLINK}'  => "<a href='" . MAIN_SERVER . "/nation/beer-pong-teams/team-info/{$teamInfo['Team']['slug']}/{$teamInfo['Team']['id']}'>View team</a>"
                        ),
                        $userInTeam['User']['email']
                    );

                }

            }

            $teammateInfo['Teammate']['status'] = "Accepted";
            $Teammate->save($teammateInfo);

            //Send email to the requester
            $this->Team->User->recursive = -1;
            $requestor = $this->Team->User->find('first', array('fields'=>array('email'),'conditions'=>array('id'=>$teammateInfo['Teammate']['requester_id'])));
            if (!empty($requestor)) {
                $result = $this->sendMailMessage(
                    'AcceptTeamRequest', array(
                          '{TEAMNAME}'      => $teamInfo['Team']['name'],
                       '{FNAME}'         => $userInfo['User']['firstname'],
                       '{LNAME}'         => $userInfo['User']['lastname'],
                       '{LOGIN}'         => $userInfo['User']['lgn'],
                       '{DESCRIPTION}'   => $teamInfo['Team']['description'],
                       '{VIEWTEAMLINK}'  => "<a href='" . MAIN_SERVER . "/nation/beer-pong-teams/team-info/{$teamInfo['Team']['slug']}/{$teamInfo['Team']['id']}'>View team</a>"
                        ),
                    $requestor['User']['email']
                );
            }
            //send email to the teammate
            $result = $this->sendMailMessage(
                'AcceptToTheTeam', array(
                      '{TEAMNAME}'      => $teamInfo['Team']['name'],
                     '{FNAME}'         => $userInfo['User']['firstname'],
                     '{LNAME}'         => $userInfo['User']['lastname'],
                     '{DESCRIPTION}'   => $teamInfo['Team']['description'],
                     '{VIEWTEAMLINK}'  => "<a href='" . MAIN_SERVER . "/nation/beer-pong-teams/team-info/{$teamInfo['Team']['slug']}/{$teamInfo['Team']['id']}'>View team</a>"
                      ),
                $userInfo['User']['email']
            );
            $this->User->updateTeamRatings($userInfo['User']['id']);

            $this->Session->setFlash('Your teammate invitation has been accepted.', 'flash_success');
            if ($signupID) {
                return $this->redirect('/signups/signupDetails/' . $signupID . '/tab-team/'); 
            }
            else {
                return $this->redirect(array('controller'=>'teams','action'=>'view',$teamInfo['Team']['slug'],$teamID)); 
            }

        } else {
            $this->Session->setFlash('Error with input parameters.', 'flash_error');
            $this->redirect("/");
        }


    }

    /**
   *  Accept user to the team
   *  @author vovich
   *  @param int    $teamID
   *  @param string $userName
   */
    function decline($teamID = null, $userName = null) 
    {

        $this->layout = false;
        $userName = urldecode($userName);
        if (!empty($teamID) && !empty($userName)) {
            //Getting User information
            $conditions = array('lgn'=>$userName);
            $this->Team->User->recursive = -1;
            $userInfo  = $this->Team->User->find('first', array('conditions'=>$conditions));
            if (empty($userInfo)) {
                $this->Session->setFlash('Can not find such user.', 'flash_error');
                $this->redirect("/");
            }
            $teamInfo = $this->Team->find('first', array('conditions'=>array('Team.id'=>$teamID,'Team.status <>'=>'Deleted')));
            if (empty($teamInfo)) {
                $this->Session->setFlash('Can not find such team.', 'flash_error');
                $this->redirect("/");
            }

            $Teammate = ClassRegistry::init('Teammate');

            $Teammate->recursive = -1;
            $teammateInfo = $Teammate->find('first', array('conditions'=>array('user_id'=>$userInfo['User']['id'],'team_id'=>$teamID)));
            //check if user was invited
            if (empty($teammateInfo)) {
                $this->Session->setFlash('You were not invited to this team.', 'flash_error');
                $this->redirect(array('controller'=>'teams','action'=>'view',$teamInfo['Team']['slug'],$teamID));
            }
            //user can not decline from the deleted or completed team
            if ($teamInfo['Team']['status']=="Completed" || $teamInfo['Team']['status']=="Deleted") {
                $this->Session->setFlash('You can not decline this invitation because team status is '.$teamInfo['Team']['status'], 'flash_error');
                $this->redirect(array('controller'=>'teams','action'=>'view',$teamInfo['Team']['slug'],$teamID));
            }

            $teammateInfo['Teammate']['status'] = "Declined";
            $Teammate->save($teammateInfo);

            //Send email to the requester
            $this->Team->User->recursive = -1;
            $requestor = $this->Team->User->find('first', array('fields'=>array('email'),'conditions'=>array('User.id'=>$teammateInfo['Teammate']['requester_id'])));
            if (!empty($requestor)) {
                $result = $this->sendMailMessage(
                    'AcceptTeamRequest', array(
                    '{TEAMNAME}'      => $teamInfo['Team']['name'],
                       '{FNAME}'         => $userInfo['User']['firstname'],
                       '{LNAME}'         => $userInfo['User']['lastname'],
                       '{LOGIN}'         => $userInfo['User']['lgn'],
                       '{DESCRIPTION}'   => $teamInfo['Team']['description'],
                       '{VIEWTEAMLINK}'  => "<a href='" . MAIN_SERVER . "/nation/beer-pong-teams/team-info/{$teamInfo['Team']['slug']}/{$teamInfo['Team']['id']}'>View team </a>"
                        ),
                    $requestor['User']['email']
                );
            }
            $this->User->updateTeamRatings($userInfo['User']['id']);
            $this->Team->updateTeamRating($teamID);

            $this->Session->setFlash('You have declined an invitation.', 'flash_success');
            $this->redirect(array('controller'=>'teams','action'=>'view',$teamInfo['Team']['slug'],$teamID));

        } else {
            $this->Session->setFlash('Error with input parameters.', 'flash_error');
            $this->redirect("/");
        }

    }

    /**
   * Delete team
   * @author vovich
   * @param int $teamID
   */
    function delete($teamID = null) 
    {

        $this->checkLoggined();
        //Check to see if this team has games associated with it. If so, you can't
        //delete
        $Game = ClassRegistry::init('Game');
        $Game->recursive = -1;
        $matchingGame = $Game->find(
            'first', array('conditions'=>array(
            'status <>'=>'Deleted',
            'OR'=>array('team1_id'=>$teamID,'team2_id'=>$teamID)))
        );
        if ($matchingGame) {
            $this->Session->setFlash('You can not delete a team that has games associated with it', 'flash_error');
            return $this->redirect('/nation/beer-pong-teams/myteams');
        }
    
        $user = $this->Session->read('loggedUser');

        //Getting owners for checking access
        $Teammate = ClassRegistry::init('Teammate');
        $teammates = $Teammate->find('list', array('fields'=>array('user_id','user_id'),'conditions'=>array('Teammate.team_id'=>$teamID,'Teammate.status'=>array('Creator','Accepted'))));
        $this->Access->checkAccess('Team', 'd', $teammates);

        $teamInfo = $this->Team->find('first', array('conditions'=>array('Team.id'=>$teamID)));
        if (empty($teamInfo)) {
             $this->Session->setFlash('Can not find such team.', 'flash_error');
             $this->redirect(MAIN_SERVER);
        }

        $teamInfo['Team']['status']     = 'Deleted';
        $teamInfo['Team']['is_deleted'] = 1;
        $teamInfo['Team']['deleted']    = date('Y-m-d H:i:s');
        $this->Team->save($teamInfo);

        $teamObjectInformation = $this->TeamsObject->find('first', array('conditions'=>array('TeamsObject.team_id'=>$teamID,'TeamsObject.status <>'=>'Deleted')));
        if (!empty($teamObjectInformation)) {
            $teamObjectInformation['TeamsObject']['status'] = 'Deleted';
            $this->TeamsObject->save($teamObjectInformation);

            $History       = ClassRegistry::init('History');
            $Teammate      = ClassRegistry::init('Teammate');
            /*Getting teammates*/
            $myTeammates = $Teammate->find('list', array('fields'=>array('Teammate.user_id,Teammate.user_id'),'conditions'=>array('Teammate.team_id'=>$teamID,'Teammate.status'=>array('Creator','Accepted'))));
            /*Update History*/
            $historyParams = array();
            $historyParams['user_id']   = $_SESSION['loggedUser']['id'];
            $historyParams['model']     =  $teamObjectInformation['TeamsObject']['model'] ;
            $historyParams['model_id'] =  $teamObjectInformation['TeamsObject']['model_id'] ;
            $historyParams['affected_user_id'] =serialize($myTeammates);
            $History->teamAssigment('delete', $teamID, $historyParams);

            unset($History);
            unset($Teammate);
            $Teammate = ClassRegistry::init('Teammate');

        }
        //sending an email to the all users in team that completed
        $Teammate->recursive = 0;
        $usersInTeam = $Teammate->find('all', array('conditions'=>array('team_id'=>$teamID, 'Teammate.status'=>array('Creator','Accepted') )));

        foreach ($usersInTeam as $userInTeam) {
            $result = $this->sendMailMessage(
                'TeamHasBeenDeleted', array(
                '{TEAMNAME}'      => $teamInfo['Team']['name'],
                '{FNAME}'             => $userInTeam['User']['firstname'],
                '{LNAME}'             => $userInTeam['User']['lastname'],
                '{DESCRIPTION}'   => $teamInfo['Team']['description'],
                '{REMOVER}'         => $user['lgn']
                  ),
                $userInTeam['User']['email']
            );

        }

        unset($Teammate);
        $this->Session->setFlash('The team has been deleted.', 'flash_error');
        $this->redirect($_SERVER['HTTP_REFERER']);

    }

    /**
   * Show TournEvents information Ajax call
   * @author vovich
   * @param int    $teamID
   * @param string $model
   * @param int    $modelID
   * @return string
   */
    function getTournEventInformation($teamID = null,$modelID = null,$model = null)
    {

        $result  = "";
        Configure::write('debug', 0);
        $this->layout = false;

        if (!$modelID && !$model && !$teamID) {
            exit("Choose event or tournament.");
        }

        $errors = $this->Team->canAssignTeam($teamID, $modelID, $model);

        if ($model=="Events") {
            $_model = "Event"; 
        }
        else {
            $_model = "Tournament"; 
        }

        $Model = ClassRegistry::init($_model);
        $Model->recursive = -1;
        $modelInformation = $Model->find('first', array('conditions'=>array('id'=>$modelID)));
        if (empty($modelInformation)) {
            exit('Can not find such '.$_model);
        }

        $this->set('model', $_model);
        $this->set('errors', $errors);
        $this->set('modelInformation', $modelInformation);
    }

    /**
   * Show Team information Ajax call and show can Team will be assigned to the givven tournivent
   * @author vovich
   * @param int    $teamID
   * @param string $model
   * @param int    $modelID
   * @return string
   */
    function getTeamInformation($teamID = null,$modelID = null,$model = null)
    {

        $result  = "";
        Configure::write('debug', 0);
        $this->layout = false;

        if (!$modelID && !$model && !$teamID) {
            exit("Choose event or tournament.");
        }

        $errors = $this->Team->canAssignTeam($teamID, $modelID, $model);

        if ($model=="Events" || $model=="Event") {
            $_model = "Event"; 
        }
        else {
            $_model = "Tournament"; 
        }

        $Model = ClassRegistry::init($_model);
        $Model->recursive = -1;
        $modelInformation = $Model->find('first', array('conditions'=>array('id'=>$modelID)));
        if (empty($modelInformation)) {
            exit('Can not find such '.$_model);
        }
        //Gectting team information
        $Teammate = ClassRegistry::init('Teammate');
        $Teammate->recursive = 0;
        $teammates = $Teammate->find('all', array('conditions'=>array('team_id'=>$teamID,'Teammate.status'=>array('Creator','Accepted')),'contain'=>array('User')));
        unset($Teammate);

        $teamInformation  = $this->Team->find('first', array('conditions'=>array('Team.id'=>$teamID),'contain'=>array()));

        $this->set('teammates', $teammates);
        $this->set('teamInformation', $teamInformation);
        $this->set('model', $_model);
        $this->set('errors', $errors);
        $this->set('modelInformation', $modelInformation);
    }


    /**
   *Assign Team to the TournEvent
   *@param $model    - not requered
   *@param $modelId - not requered
   *@param $teamId  - not req
   * @author vovich
   */
    function AssignTournEvent($model = null, $modelId = null, $teamId = null, $signupID = null,$nameToUse = null)
    {
     
        $backURL = $_SERVER['HTTP_REFERER'];
     
        if ($signupID) {
            $backURL = '/signups/signupDetails/' . $signupID . '/tab-team/';    
        }
       
        if  ($model) {
            $this->request->data['TournEvent']['model']     = $model ;
        }    
        if  ($modelId) {
            $this->request->data['TournEvent']['object_id'] = $modelId;
        }    
        if  ($teamId) {
            $this->request->data['TournEvent']['team_id']   = $teamId ;
        }    
        if ($nameToUse) {
            $this->request->data['TournEvent']['nameToUse'] = $nameToUse;
        }
        //TeamsObject
        if (empty($this->request->data['TournEvent']['model'])) {
            $this->Session->setFlash('Please choose the Event or Tournament.', 'flash_error');
            $this->redirect($backURL);
        }

        //Getting owners for checking access
        $Teammate = ClassRegistry::init('Teammate');
        $teammates = $Teammate->find('list', array('fields'=>array('user_id','user_id'),'conditions'=>array('team_id'=>$this->request->data['TournEvent']['team_id'],'Teammate.status'=>array('Creator','Accepted'))));
        $this->Access->checkAccess('Team', 'u', $teammates);
        unset($Teammate);

        $teamInfo  = $this->Team->find('first', array('conditions'=>array('Team.id'=>$this->request->data['TournEvent']['team_id'])));
        if (empty($teamInfo)) {
            $this->Session->setFlash('Can not find  such team or team is not completed.', 'flash_error');
            $this->redirect($backURL);
        }

        $errors = $this->Team->canAssignTeam($this->request->data['TournEvent']['team_id'], $this->request->data['TournEvent']['object_id'], $this->request->data['TournEvent']['model']);
        if (!empty($errors)) {
            $this->Session->setFlash("You can not assign this team:<br>".$errors, 'flash_error');
            $this->redirect($backURL);
        }

        if ($this->request->data['TournEvent']['model']=="Events") {
            $_model = "Event"; 
        }
        else {
            $_model = "Tournament"; 
        }
      
        if ($this->request->data['TournEvent']['model']=="Event") {
            $_model = "Event"; 
        }

        $assigned = $this->TeamsObject->find('count', array('conditions'=>array('TeamsObject.status <>'=>'Deleted','TeamsObject.model'=>$_model,'TeamsObject.model_id'=>$this->request->data['TournEvent']['object_id'],'TeamsObject.team_id'=>$this->request->data['TournEvent']['team_id'])));
        if ($assigned>0) {
            $this->Session->setFlash("This team is already assigned to this ".$_model, 'flash_error');
            $this->redirect($backURL);
        }


        $this->checkLoggined();
        $user = $this->Session->read('loggedUser');

        $teamObject['TeamsObject']['model']         = $_model;
        $teamObject['TeamsObject']['model_id']     = $this->request->data['TournEvent']['object_id'];
        $teamObject['TeamsObject']['assigner_id']  = $user['id'];
        if (!$nameToUse || $nameToUse == '') {
            $teamObject['TeamsObject']['name']          = $teamInfo['Team']['name'];
        }
        else 
        {
            $teamObject['TeamsObject']['name']         = $nameToUse;    
        }
        $teamObject['TeamsObject']['status']          = 'Created';
        $teamObject['TeamsObject']['team_id']       = $this->request->data['TournEvent']['team_id'];

        if($this->TeamsObject->save($teamObject)) {
            /*History*/
            $History       = ClassRegistry::init('History');
            $Teammate      = ClassRegistry::init('Teammate');
            /*Getting teammates*/
            $myTeammates   = $Teammate->find('list', array('fields'=>array('Teammate.user_id,Teammate.user_id'),'conditions'=>array('Teammate.team_id'=>$this->request->data['TournEvent']['team_id'],'Teammate.status'=>array('Creator','Accepted'))));
            /*Update History*/
            $historyParams = array();
            $historyParams['user_id']   = $user['id'];
            $historyParams['model']     =  $_model ;
            $historyParams['model_id']  =  $this->request->data['TournEvent']['object_id'] ;
            $historyParams['affected_user_id'] = serialize($myTeammates);
            $History->teamAssigment('add', $this->request->data['TournEvent']['team_id'], $historyParams);

            unset($History);
            unset($Teammate);
            /*EOF HISTORY*/

            $this->Session->setFlash("Team has been assigned.", 'flash_success');
            $this->redirect($backURL);
        } else {
            $this->Session->setFlash("Error while storing information.", 'flash_error');
            $this->redirect($backURL);
        }

    }

    /**
   * show teams for the event
   * @author vovich
   * @param string $slug
   */
    function eventteams($slug = null) 
    {

        $eventIformation = array();
        $Event   = ClassRegistry::init('Event');
        $Manager = ClassRegistry::init('Manager');

        $Event->recursive = -1;
        $modelInformation = $Event->find('first', array('conditions'=>array('slug'=>$slug)));

        if (empty($modelInformation)) {
            $this->Session->setFlash('Can not find such Event', 'flash_error');
            $this->redirect(MAIN_SERVER);
        }

        $managers = $Manager->find(
            'list', array(
            'fields'=>array('user_id','user_id')
              ,'conditions' => array('Manager.model' => 'Event','Manager.model_id'=>$modelInformation['Event']['id'])
            )
        );

        $this->Access->checkAccess('event', 'u', $managers);


        $conditions = array();
        $conditions['TeamsObject.model']    = 'Event';
        $conditions['TeamsObject.model_id'] = $modelInformation['Event']['id'];

        /* filter Getting data from the session or from the form*/
        if(!empty($this->request->data['TeamFilter'])) {
            $this->Session->write('EventTeamFilter', $this->request->data['TeamFilter']);
        }elseif($this->Session->check('EventTeamFilter')) {
            $this->request->data['TeamFilter']=$this->Session->read('EventTeamFilter');
        }

        //Prepare data for the filter
        if (!empty( $this->request->data['TeamFilter']['name'])) {
            $conditions['Team.name LIKE'] = $this->request->data['TeamFilter']['name']; 
        }

        if (!empty( $this->request->data['TeamFilter']['status'])) {
            $conditions['Team.status'] = $this->request->data['TeamFilter']['status'];
        }
        $this->TeamsObject->recursive = 0;
        $teams = $this->paginate('TeamsObject', $conditions);


        $this->set('teams', $teams);
        $this->set('statuses', $this->teamStatuses);


        $this->set('model', 'Event');
        $this->set('modelIformation', $modelInformation);
        unset($managers);
        unset($Event);
        unset($Manager);

        $this->render('tourneventsteams');
    }

    /**
   * show teams for the tournaments
   * @author vovich
   * @param string $slug
   */
    function tournamentteams($slug = null) 
    {
        /*
        $eventIformation = array();
        $Tournament = ClassRegistry::init('Tournament');
        $Manager     = ClassRegistry::init('Manager');

        $Tournament->recursive = -1;
        $modelInformation = $Tournament->find('first',array('conditions'=>array('slug'=>$slug)));

        if (empty($modelInformation)) {
        $this->Session->setFlash('Can not find such Tournament');
        $this->redirect(MAIN_SERVER);
        }

        $managers = $Manager->find('list',array(
          'fields'=>array('user_id','user_id')
              ,'conditions' => array('Manager.model' => 'Tournament','Manager.model_id'=>$modelInformation['Tournament']['id'])
          )
        );

        $this->Access->checkAccess('Tournament','u',$managers);

        $conditions = array();
        $conditions['TeamsObject.model'] = 'Tournament';
        $conditions['TeamsObject.model_id'] = $modelInformation['Tournament']['id'];

        /* filter Getting data from the session or from the form*/ /*
         if(!empty($this->request->data['TeamFilter'])){
            $this->Session->write('TournamentTeamFilter',$this->request->data['TeamFilter']);
         }elseif($this->Session->check('TournamentTeamFilter')){
            $this->request->data['TeamFilter']=$this->Session->read('TournamentTeamFilter');
         }

        //Prepare data for the filter
        if (!empty( $this->request->data['TeamFilter']['name']))
        $conditions['Team.name LIKE'] = $this->request->data['TeamFilter']['name'];

        if (!empty( $this->request->data['TeamFilter']['status'])){
        $conditions['Team.status'] = $this->request->data['TeamFilter']['status'];
        }
        $this->TeamsObject->recursive = 0;
        $teams = $this->paginate('TeamsObject',$conditions);


        $this->set('teams',$teams);
        $this->set('statuses',$this->teamStatuses);


        $this->set('model','Tournament');
        $this->set('modelIformation',$modelInformation);
        unset($managers);
        unset($Tournament);
        unset($Manager);

        $this->render('tourneventsteams');
        */
    }

    /**
* Show teams for the current tournament
* @author vovich
* @param int $tournamentID
*/
    function wsobp($tournamentID = null) 
    {

        if (!$tournamentID && $this->Session->check('Tournament')) {
            $TournamentSession = $this->Session->read('Tournament');
            $tournamentID = $TournamentSession['id'];
        }

        $conditions['Team.status'] = 'Completed';
        $eventIformation = array();
        $conditions      = array();
        $conditions['TeamsObject.model']     = 'Event';
        $conditions['TeamsObject.model_id']  = $tournamentID;
        $conditions['Team.status']                   = 'Completed';
        $conditions['TeamsObject.status']    = 'Created';
    
        /* filter Getting data from the session or from the form*/
        if(!empty($this->request->data['TeamFilter'])) {
            $this->Session->write('TeamsFilter', $this->request->data['TeamFilter']);
        }elseif($this->Session->check('TeamsFilter')) {
            $this->request->data['TeamFilter']=$this->Session->read('TeamsFilter');
        }

        //Prepare data for the filter
        if (!empty( $this->request->data['TeamFilter']['name'])) {
            $conditions['OR']['Team.name LIKE'] = "%".$this->request->data['TeamFilter']['name']."%";
            $conditions['OR']['TeamsObject.name LIKE'] = "%".$this->request->data['TeamFilter']['name']."%";
        }
    
    
    

        $this->TeamsObject->contain(array('Team', 'reset' => false));
        $teams = $this->paginate('TeamsObject', $conditions);
        foreach ($teams as $key=>&$val) {
            $personalImage = $this->TeamsObject->Team->PersonalImage->find('first', array('fields'=>array('filename'),'conditions'=>array('model'=>'Team','model_id'=>$val['Team']['id'],'prop'=>'Personal')));
            $val['PersonalImage']  = $personalImage['PersonalImage'];
        }

        $this->set('teams', $teams);
        $this->set('statuses', $this->teamStatuses);

    }

    /**
   * Action to fix slug for migration
   * @author vovich
   */
    function __generateslug() 
    {

        $this->Team->recursive=-1;
        $teams = $this->Team->find('all');

        foreach ( $teams as $team ) {
            unset($team['Team']['slug']);
            $this->Team->save($team);
        }
        exit('Slug generation finished</pre>');

    }

    function __convertImages() 
    {

        $images = $this->TeamsObject->Team->PersonalImage->find('all', array('fields'=>array('filename'),'conditions'=>array('model'=>'Team','prop'=>'Personal')));

        App::import('Vendor', 'example', array('file' => 'class.upload.php'));

        $this->settings['baseDir']     = WWW_ROOT.'img'.DS.'Team'.DS;
        $this->settings['thumbsDir'] = WWW_ROOT.'img'.DS.'Team'.DS.'thumbs'.DS;

        if ($handle1 = opendir($this->settings['baseDir'])) {
            while (false !== ($file = readdir($handle1))) {

                 $handle = new upload($this->settings['baseDir'].$file);

                if ($handle->uploaded) {
                     // $image['PersonalImage']['filename'] = str_replace('.jpg','',$image['PersonalImage']['filename']);
                     //$image['PersonalImage']['filename'] = str_replace('.gif','',$image['PersonalImage']['filename']);
                    //$image['PersonalImage']['filename'] = str_replace('.png','',$image['PersonalImage']['filename']);
                    //$handle->file_new_name_body   = $image['PersonalImage']['filename'];
                    $handle->image_resize               = true;
                    //$handle->image_ratio_crop = true;
                    $handle->image_resize         = true;
                    $handle->image_x              = 120;
                    $handle->image_ratio_y        = true;
                    //$handle->image_x                        = 120;
                    //$handle->image_y                        = 120;
                     //$handle->image_ratio_fill      = 'C';
                    $handle->process($this->settings['thumbsDir']);
                    if ($handle->processed) {
                        echo 'image resized<br>';
                        $handle->clean();
                    } else {
                        echo 'error : ' . $handle->error.'<br>';
                    }
                } else {
                    echo $this->settings['baseDir'].$file." is not uploaded<br>";
                }


            }


            closedir($handle1);
        }
        exit();
    }

    /**
   * Show all teams for the user
   * @author vovich
   */
    function allTeams() 
    {
        $conditions = array();
        $conditions['Team.status'] =array('Completed');

        /* filter Getting data from the session or from the form*/
        if(!empty($this->request->data['TeamFilter'])) {
            $this->Session->write('TeamsFilter', $this->request->data['TeamFilter']);
        }elseif($this->Session->check('TeamsFilter')) {
            $this->request->data['TeamFilter']=$this->Session->read('TeamsFilter');
        }

        //Prepare data for the filter
        if (!empty( $this->request->data['TeamFilter']['name'])) {
            $conditions['Team.name LIKE'] = "%".$this->request->data['TeamFilter']['name']."%"; 
        }

        $this->paginate = array('conditions'=>$conditions,'contain'=>array('PersonalImage'));
        //$this->Team->recursive = 0;
        $teams = $this->paginate('Team');
        $this->set('teams', $teams);
    }
    /**
* AJAX Find teams by name
* @author vovich
*/
    function findByName()
    {
        Configure::write('debug', '1');
        $this->layout = false;

        if ($this->RequestHandler->isAjax() && $this->request->data['TeamAssignment']['name']) {

            $conditions = array('Team.name LIKE ' => $this->request->data['TeamAssignment']['name']."%",'Team.is_deleted <>' => 1) ;
            $teams   = $this->Team->find('all', array( 'contain' => array('User','PersonalImage'),'conditions'=>$conditions));
            foreach ($teams as $key => $value) {
                         $teams[$key]['errors'] =  $this->Team->canAssignTeam($value['Team']['id'], $this->request->data['TeamAssignment']['model_id'], $this->request->data['TeamAssignment']['model']);
            }

            $this->set(compact('teams'));
        } else {
            exit();
        }

    }
    
    /**
       * Teams stats page
       * @author vovich
       */
    function stats($slug = null, $teamID = null) 
    {
    
        $this->checkLoggined();
    
        //Getting owners for checking access
        $Teammate = ClassRegistry::init('Teammate');
        $teammates = $Teammate->find(
            'list', array('fields'=>array('user_id','user_id'),
            'conditions'=>array('team_id'=>$teamID,'status'=>array('Accepted','Creator','Pending')))
        );
        $this->Access->checkAccess('Team', 'r', $teammates);
        $Teammate->recursive = 0;
        $teammates = $Teammate->find(
            'all', array(
            'conditions'=>array('team_id' => $teamID,'Teammate.status'=>array('Accepted','Creator','Pending')),
            'contain'=>array('User' => array('Address' => array('Provincestate', 'conditions' => array('Address.label' => 'Home', 'Address.is_deleted' => 0)))))
        );
        foreach ($teammates as $teammateKey => $teamma) {
            $teammates[$teammateKey]['stats'] = $this->Team->getPlayerStats($teammates[$teammateKey]['User']['id']);
        }
        unset($Teammate);

        $user = $this->Session->read('loggedUser');
        $team = $this->Team->find('first', array('conditions'=>array('Team.id'=>$teamID)));           
        $this->request->data['Team'] = $team['Team'];
        
        $conditionsGames = array('OR' => array('team1_id' => $teamID, 'team2_id' => $teamID), 'AND' => array('Game.status' => 'Completed'));

        if(!empty($this->request->data['GamesSearch'])) {
            $this->Session->write('games_search', $this->request->data['GamesSearch']);
            $this->passedArgs['games_search'] = 1;
        }elseif($this->Session->check('games_search')) {
            if (!empty($this->passedArgs['games_search'])) {
                $this->request->data['GamesSearch'] = $this->Session->read('games_search');               
            } else {
                $this->Session->delete('games_search');    
            }
        }
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
            'contain' => array('Brackettype', 'Event' => array('Venue' => array('Address' => array('Provincestate'))), 'Team1', 'Team2'),
            'order' => array('Game.created' => 'DESC','Game.id'=>'DESC'),
            'conditions' => $conditionsGames
        
        );    
        $games = $this->paginate('Game');    
          
        $gameIDs = Set::combine($games, '{n}.Game.id', '{n}.Game.id');        
        if (empty($gameIDs)) {
            $gameIDs = array('0' => '0');
        }
                
        $this->Game->contain(array('Event'));
        $gameEvents = $this->Game->find(
            'all', 
            array(
                'contain' => array('Event'),
                'fields' => array('Event.id', 'Event.name'), 
                'conditions' => array('OR' => array('team1_id' => $teamID, 'team2_id' => $teamID), 'AND' => array('Game.status' => 'Completed')),
            )
        );
        $gameEvents = Set::combine($gameEvents, '{n}.Event.id', '{n}.Event.name');
        
        $gameOpponents = $this->Game->getTeamsOpponents($teamID);   
                  
        $this->set('games', $games);
        $this->set('gameOpponents', $gameOpponents);
        $this->set('gameEvents', $gameEvents);
        $this->set('averageWin', $this->Team->calcAverageWins($this->request->data['Team']['total_wins'], $this->request->data['Team']['total_losses']));
        $this->set('averageCupdif', $this->Team->calcAverageCupdif($this->request->data['Team']['total_wins'], $this->request->data['Team']['total_losses'], $this->request->data['Team']['total_cupdif']));
        $this->set('chartInfo', $this->Team->prepareTeamStatsChart($teamID, 15, array(), $gameIDs));
        $this->set('teammates', $teammates);
        $this->set('user', $user);
    }
      
    function testing() 
    {
        $this->Team->recursive = 1;
        return $this->Team->find('all', array('contain'=>array('TeamsObject'),'conditions'=>array('id'=>2080)));
    }
      
      /**
       * Remove team from signup
       *
       * @author 
       */
    function remove_from_signup($signupID, $teamID) 
    {
        $signupDetails = $this->Signup->find('first', array('conditions' => array('Signup.id' => $signupID)));
        
        //If this team already has games for this event, don't allow this  
        if (!$signupDetails) {
            $this->Session->setFlash('Signup not found.', 'flash_error');
            $this->redirect(MAIN_SERVER);
        }
        if ($signupDetails['Signup']['model'] == 'Event') {
            $game = $this->Game->find(
                'first', array('conditions'=>array(
                'OR'=>array(
                    'Game.team1_id'=>$teamID,
                    'Game.team2_id'=>$teamID),
                'Game.status <>'=>'Deleted',
                'Game.event_id'=>$signupDetails['Signup']['model_id']))
            );
            if ($game) {
                $this->Session->setFlash('You can not change a team once it has played a game in an Event', 'flash_error');
                $this->redirect(SECURE_SERVER . '/signups/signupDetails/' . $signupID . '/tab-team/');
            }
        }
                                    
        $this->Access->checkAccess('Signup', 'u', $signupDetails['Signup']['user_id']);
        
        $this->TeamsObject->recursive = -1;      
        $teamsObject = $this->TeamsObject->find('first', array('conditions' => array('TeamsObject.status <>' => 'Deleted', 'TeamsObject.team_id' => $teamID, 'TeamsObject.model' => $signupDetails['Signup']['model'], 'TeamsObject.model_id' => $signupDetails['Signup']['model_id'])));       

        if (!empty($teamsObject['TeamsObject']['id'])) {
            $this->TeamsObject->save(array('id' => $teamsObject['TeamsObject']['id'], 'status' => 'Deleted'));
            $this->SignupsUser->deleteAll(array('SignupsUser.signup_id' => $signupID, 'SignupsUser.user_id <>' => $signupDetails['Signup']['user_id']), false);
            $this->Session->setFlash('Team has been removed from your Signup.', 'flash_success');    
        }
        
        return $this->redirect('/signups/signupDetails/' . $signupID . '/tab-team/');
    }
      
    function m_getUserTeams($userid=null, $amf = 0) 
    {
        if (isset($this->request->params['form']['userid'])) {
            $userid = mysql_escape_string($this->request->params['form']['userid']); 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = mysql_escape_string($this->request->params['form']['amf']); 
        }
    
        $user = $this->User->find(
            'first', array(
            'conditions'=>array('User.id'=>$userid),
            'contain'=>array('Team'))
        );
        $teamIDs = Set::extract($user['Team'], '{n}.id');
        //return $user['Team'];
        $teams = $this->Team->find(
            'all', array(
            'conditions'=>array('Team.is_deleted'=>0,'Team.id'=>$teamIDs),
            'contain'=>array('User'))
        );    
        foreach ($teams as &$team) {
            foreach ($team['User'] as &$user) {
                unset($user['email']);
            }
        }
        return $this->returnMobileResult($teams, $amf);
    }
    function m_getMyTeams($userid = null,$amf = 0) 
    {
        if (isset($this->request->params['form']['amf'])) {
            $amf = mysql_escape_string($this->request->params['form']['amf']); 
        }
        
        if (isset($this->request->params['form']['userid'])) {
            $userid = mysql_escape_string($this->request->params['form']['userid']); 
        }
        

        $userWithTeams = $this->User->find(
            'first', array(
            'conditions'=>array('User.id'=>$userid),
            'contain'=>array(
                'Team'=>array(
                    'conditions'=>array(
                        'Teammate.status'=>array('Creator','Accepted')))))
        );
        if (!$userWithTeams) {
            return $this->returnMobileResult('User does not exist', $amf);
        }
        $userWithPendingTeams = $this->User->find(
            'first', array(
            'conditions'=>array('User.id'=>$userid),
            'contain'=>array(
                'Team'=>array(
                    'conditions'=>array(
                        'Teammate.status'=>'Pending'))))
        );                                
            
        $teamIDs = Set::extract($userWithTeams['Team'], '{n}.id');
        // return $userWithTeams;
        $pendingTeamIDs = Set::extract($userWithPendingTeams['Team'], '{n}.id');
        //return $user['Team'];
        $teams = $this->Team->find(
            'all', array(
            'conditions'=>array('Team.is_deleted'=>0,'Team.id'=>$teamIDs),
            'contain'=>array('User'))
        );    
        $pendingTeams = $this->Team->find(
            'all', array(
            'conditions'=>array('Team.is_deleted'=>0,'Team.id'=>$pendingTeamIDs),
            'contain'=>array('User'))
        );    
        return $this->returnMobileResult(array('CompletedTeams'=>$teams,'PendingTeams'=>$pendingTeams), $amf);
    }
    function m_acceptTeammateInvitation($teamID = null,$amf = 0) 
    {
        if (isset($this->request->params['form']['teamID'])) {
            $teamID = mysql_escape_string($this->request->params['form']['teamID']); 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = mysql_escape_string($this->request->params['form']['amf']); 
        }
        $userid = $this->getUserID();
        if ($userid < 2) { return $this->returnMobileResult("You are not logged in.", $amf); 
        }
    
        $this->Teammate->recursive = -1;
        $teammate = $this->Teammate->find(
            'first', array('conditions'=>array(
            'team_id'=>$teamID,
            'user_id'=>$userid,
            'status'=>'Pending'))
        );
        if (!$teammate) {
            return $this->returnMobileResult('Invitation not found', $amf); 
        }        
        
        $teammate['Teammate']['status'] = 'Accepted';
        $this->Teammate->save($teammate);
        //now get the team and update the team status to completed
        $team= $this->Team->find(
            'first', array('conditions'=>array('Team.id'=>$teamID),
            'contain'=>array('User'))
        );
        if (!$team || $team['Team']['is_deleted'] == 1) {
            return $this->returnMobileResult('This Team has been deleted.'); 
        }
        $numAccepts = 0;
        foreach ($team['User'] as $user) {
            if ($user['Teammate']['status'] == 'Accepted' || $user['Teammate']['status'] == 'Creator') {
                $numAccepts++; 
            }
        }
        if ($numAccepts == $team['Team']['people_in_team']) {
            $this->Team->save(array('id'=>$teamID,'status'=>'Completed'));
        }
        return 'ok';
    }
                                                     /*
        
	    Configure::write('debug', 0);
	    if (isset($this->request->params['form']['userid']))
		    $userid = mysql_escape_string($this->request->params['form']['userid']);
	    if (isset($this->request->params['form']['user_lgn']))
		    $user_lgn = mysql_escape_string($this->request->params['form']['user_lgn']);	

	    if (!$user_lgn || !$userid)
		    return $this->returnJSONResult(array('Invalid parameters','userid'=>$userid,'user_lgn'=>$user_lgn));
	    
        $Teammate = ClassRegistry::init('Teammate');
        $Teammate->recursive = 1;
    //added pending
        $myTeams = $Teammate->find('all',array('fields'=>array('Team.id','Team.name', 'Team.description'),'conditions'=>array('Teammate.user_id'=>$userid,'Teammate.status'=>array('Creator','Accepted','Pending'),'Team.status <>'=>'Deleted')));
        $cleanTeams = array();
        
        $i = 0;
        foreach($myTeams as $team) {
    	    array_push($cleanTeams, $team['Team']);
    	    $cleanTeams[$i]['people_in_team'] = "1";
    	    $teammates = $Teammate->find('all',array('fields'=>array('User.lgn'),'conditions'=>array('team_id'=>$team['Team']['id'])));
    	    foreach($teammates as $teammate) {
	    	    if (!empty($teammate)) {
		    	    if ($teammate['User']['lgn'] != $user_lgn) {
		    		    $cleanTeams[$i]['teammate_lgn'] = $teammate['User']['lgn'];
		    		    $cleanTeams[$i]['people_in_team'] = "2";
		    	    }
		        }
    	    }
    	    $i++;
        }
	    $this->returnJSONResult($cleanTeams);
      }
      */
    /**
  * if ($showUpcomingGames > 0), this returns all upcoming games (including Event if applicable)
  * if ($showRecentGames > 0), this returns all games, period (including Event if applicable)
  */
      
    function m_viewTeam($teamID = null,$showRecentGames = 0, $showUpcomingGames = 0, $amf = 0) 
    {
        if (isset($this->request->params['form']['teamID'])) {
            $teamID = $this->request->params['form']['teamID']; 
        }
        if (isset($this->request->params['form']['showRecentGames'])) {
            $showRecentGames= $this->request->params['form']['showRecentGames']; 
        }
        if (isset($this->request->params['form']['$showUpcomingGames'])) {
            $showUpcomingGames= $this->request->params['form']['$showUpcomingGames']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf= $this->request->params['form']['amf']; 
        }

        $team = $this->Team->find(
            'first', array(
            'conditions'=>array(
            'Team.id'=>$teamID,
            'Team.is_deleted'=>0),
            'contain'=>array('PersonalImage','User'))
        );
        
        if (!$team) { return $this->returnMobileResult('Team not found', $amf); 
        }
        if ($showRecentGames > 0) {
            $recentGames = $this->Game->find(
                'all', array(
                'conditions'=>array(
                'OR'=>array('Game.team1_id'=>$teamID,'Game.team2_id'=>$teamID),
                'Game.status'=>'Completed'),
                'contain'=>array('Team1'=>array('fields'=>array('id','name')),'Team2'=>array('id','name'),'Event'), 
                'limit'=>$showRecentGames,
                'order' => array('Game.id' => 'ASC'))
            );
            $team['RecentGames'] = $recentGames;
        }
        if ($showUpcomingGames > 0) {
            $upcomingGames = $this->Game->find(
                'all', array(
                'conditions'=>array(
                'OR'=>array('Game.team1_id'=>$teamID,'Game.team2_id'=>$teamID),
                'Game.status'=>array('Playing','Ready','Not Ready','Unavailable')),
                'contain'=>array('Team1'=>array('fields'=>array('id','name')),'Team2'=>array('id','name'),'Event'), 
                'order' => array('Game.id' => 'ASC'))
            );
            $team['UpcomingGames'] = $upcomingGames;          
        }
        return $this->returnMobileResult($team, $amf);
    }

    function m_viewTeamWithRecentGames($teamID = null, $numGames = 5, $amf = 0) 
    {
        if (isset($this->request->params['form']['teamID'])) {
            $teamID = $this->request->params['form']['teamID']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf= $this->request->params['form']['amf']; 
        }
        $team = $this->Team->find(
            'first', array(
            'conditions'=>array(
            'Team.id'=>$teamID,
            'Team.is_deleted'=>0),
            'contain'=>array('PersonalImage','User'))
        );
        if (!$team) { return $this->returnMobileResult('Team not found'); 
        }
      
        $recentGames = $this->Game->find(
            'all', array(
            'conditions'=>array(
            'OR'=>array('Game.team1_id'=>$teamID,'Game.team2_id'=>$teamID),
            'Game.status'=>'Completed'),
            'contain'=>array('Team1','Team2','Event'),
            'limit'=>$numGames,
            'order' => array('Game.id' => 'ASC'))
        );
        $team['RecentGames'] = $recentGames;
        return $this->returnMobileResult($team, $amf);
    }
    /**
   * View team
   * @author duncan
   * @param int $teamID
   * @access All
   */
    /*
    function m_viewTeam ($teamID = NULL) {
    $teamID = 388;
	    //Getting owners for checking access
	    $Teammate = ClassRegistry::init('Teammate');
	    $Teammate->recursive = 0;
	    $teammates = $Teammate->find('all',array('fields'=>array('User.lgn','Teammate.team_id'),'conditions'=>array('team_id'=>$teamID)));
	
	    $teams  = $this->Team->find('first',array('conditions'=>array('Team.id'=>$teamID)));
    $images=$this->Image->find('first',array('fields'=>array('filename','model_id'),'conditions'=>array('model_id'=>$teamID, 'model'=>'Team')));
	    $this->TeamsObject->recursive = 0;
	    
	    $results = array();
	    
	    if (!empty($teams)) { array_push($results, $teams); }
	    if (!empty($images)) { array_push($results, $images); }
	  	if (!empty($teammates)) { array_push($results, $teammates); }
	    
	    return $this->returnJSONResult($results);
    }  */
    function moveOnePlayersTeamsToAnother_api($userIDToMoveFrom,$userIDToMoveTo) 
    {
              Configure::write('debug', 0);  
        if (!$this->isUserSuperAdmin()) {
            return "Access Denied"; 
        }
        //Check both users
        $this->User->recursive = -1;
        $users = $this->User->find('all', array('conditions'=>array('id'=>array($userIDToMoveFrom,$userIDToMoveTo))));
        if (count($users) != 2) {
            return "Invalid userids"; 
        }
            
        $this->Teammate->recursive = -1;
        $moveFromTeammates = $this->Teammate->find(
            'all', array('conditions'=>array(
            'user_id'=>$userIDToMoveFrom,
            'status'=>array('Creator','Pending','Accepted')))
        );
        $moveToTeammates = $this->Teammate->find(
            'all', array('conditions'=>array(
            'user_id'=>$userIDToMoveTo,
            'status'=>array('Creator','Pending','Approved')))
        );
        //we don't want the teammate to be on the team twice
        $teammateAlreadyExists = false;
        //       return array($moveFromTeammates,$moveToTeammates);
        foreach ($moveFromTeammates as $moveFromTeammate) {
            $moveFromTeamID = $moveFromTeammate['Teammate']['team_id'];
            foreach ($moveToTeammates as $moveToTeammate) {
                if ($moveToTeammate['Teammate']['team_id'] == $moveFromTeamID) {
                    $teammateAlreadyExists = true; 
                } 
            }
            if ($teammateAlreadyExists) {
                $moveFromTeammate['Teammate']['status'] = 'Deleted';
                $this->Teammate->save($moveFromTeammate);
            }
            else {
                $moveFromTeammate['Teammate']['user_id'] = $userIDToMoveTo;
                $this->Teammate->save($moveFromTeammate);
            }
        }   
        $this->User->updateStatsForUser($userIDToMoveFrom);
        $this->User->updateStatsForUser($userIDToMoveTo); 
        
        //Need to deal with Rating/Ranking separately
        return 'ok';
    }            
    function m_getTeamLeaderBoardByRating($start = 1, $limit = 10, $amf = 0) 
    {
        if (isset($this->request->params['form']['start'])) {
            $start= $this->request->params['form']['start']; 
        }
        if (isset($this->request->params['form']['limit'])) {
            $limit= $this->request->params['form']['limit']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf= $this->request->params['form']['amf']; 
        }
        if ($start < 1) {
            $start = 1; 
        }
        $limitStart = $start - 1;
        $results = $this->Team->find(
            'all', array(
            'conditions'=>array(
            'Team.status'=>array('Created','Completed','Pending'),
            'Team.people_in_team >'=>1,
            'Team.total_wins >'=>0),
            'contain'=>array('User'),
            'order'=>array('Team.rating'=>'DESC'),
            'limit'=>$limitStart.','.$limit)
        );
        return $this->returnMobileResult($results, $amf);
    }
    function m_getTeamWithDetails($teamID = null, $amf = 0) 
    {
        if (isset($this->request->params['form']['teamID'])) {
            $teamID = $this->request->params['form']['teamID']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf= $this->request->params['form']['amf']; 
        }
     
        $team = $this->Team->find(
            'first', array(
            'conditions'=>array('Team.id'=>$teamID,'Team.status <>'=>'Deleted'),
            'contain'=>array('User'))
        );
        if (!$team) {
            return $this->returnMobileResult('Team not found', $amf);
        }
        $team['Recent Games'] = $this->Game->find(
            'all', array(
            'conditions'=>array(
            'OR'=>array('Game.team1_id'=>$teamID,'Game.team2_id'=>$teamID),
            'Game.status'=>'Completed'),
            'contain'=>array('Team1'=>array('User'),'Team2'=>array('User')),
            'order'=>array('Game.id'=>'DESC'),
            'limit'=>10)
        );
      
        $numTeams = $this->Team->find(
            'count', array('conditions'=>array(
            'Team.status <>'=>'Deleted',
            'Team.rating >'=>0))
        );
        $numTeamsBetter = $this->Team->find(
            'count', array('conditions'=>array(
            'Team.status <>'=>'Deleted',
            'Team.rating >'=>$team['Team']['rating']))
        );
      
        $team['Total Teams'] = $numTeams;
        if ($numTeamsBetter >= $numTeams) {
            $team['Rank'] = $numTeams; 
        }
        else { 
            $team['Rank'] = $numTeamsBetter+1; 
        }
        return $this->returnMobileResult($team, $amf);
    }
    /*
    * Mobile Search function. Searches on partial text. Limit of 25
    */
    function m_findTeams($name='',$amf=0) 
    {
        if (isset($this->request->params['form']['name'])) {
            $name = $this->request->params['form']['name']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf= $this->request->params['form']['amf']; 
        }
        //return $name;
        if (!$name || $name=='') {
            return $this->returnMobileResult('You must provide a search term', $amf);
        }
        $conditions['Team.name LIKE'] = '%'.$name.'%';
        $conditions['Team.status <>'] = 'Deleted';
        $this->Team->recursive = -1;
        $teams = $this->Team->find(
            'all', array(
            'conditions'=>$conditions,        
            //     'fields'=>array('Team.name','Team.id'),
            'limit'=>50, 
            'contain'=>array('User'=>array('fields'=>array('lgn','id','lastname','firstname')))
            )
        );
        $results = array();
        $ctr = 0;
        foreach ($teams as $team) {
            if ($team['Team']['people_in_team'] == count($team['User'])) {
                $results[$ctr] = $team;
                $ctr++;
            }
        }
        return $this->returnMobileResult($results, $amf);
    }
}
?>
