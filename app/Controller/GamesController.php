<?php
class GamesController extends AppController
{

    var $name = 'Games' ;
    var $uses = array('Game','Event','Team','User','Rating','Ratinghistory','Affil','Affilspoint','UsersAffil',
    'OrganizationsUser','Organization');
  
    function getTeamsThatAreNotAssignedToAnEventTheyHaveGamesIn_api($modelid = 644) 
    {
        //Lets just do WSOBP VII right now
     
        if (!$this->isUserSuperAdmin()) {
            return $this->returnJSONResult('Access Denied'); 
        }
        $this->Game->recursive = -1;
        $games = $this->Game->find('all', array('conditions'=>array('status <>'=>'Deleted','event_id'=>$modelid)));
        $teamids = array();
        foreach ($games as $game) {
            $teamids[$game['Game']['team1_id']] = $game['Game']['team1_id'];        
        }      
      
        $TeamsObject = ClassRegistry::init('TeamsObject');
        $TeamsObject->recursive = -1;
        $teamsObjects = $TeamsObject->find(
            'all', array('conditions'=>array(
            'model'=>'Event',
            'model_id'=>$modelid,
            'status <>'=>'Deleted'
            ))
        );
        foreach ($teamsObjects as $teamsObject) {
            unset($teamids[$teamsObject['TeamsObject']['team_id']]);
        } 
        return $this->returnJSONResult($teamids);
    }
    /*function getTestGameData() {
      $player1 = array('email'=>'duenas05@msn.com','pwd'=>'4d585032467cbf3b5a3469b3ca1ed53e');
      $player2 = array('email'=>'duncan.carroll@gmail.com','pwd'=>'00c94f3edfabf350a56a7b3b0d1e2381');
      $player3 = $player1;
      $player4 = array('email'=>'skinny@bpong.com','pwd'=>'e342f99da07f9ec390f1b7437ec2ef48');
      $team1 = array('name'=>'Team Roselawn','Player1'=>$player1,'Player2'=>$player2);
      $team2 = array('name'=>'Blah Blah','Player1'=>$player3,'Player2'=>$player4);
      $array = array('issingles'=>0,'latitude'=>40,'longitude'=>-80,'winner'=>1,'cupdif'=>2,'numots'=>1,'winner'=>1,'Team1'=>$team1,'Team2'=>$team2);
      return $this->returnJSONResult($array);    
    } */
    function m_getTeamVersusTeamGames($team1_id = null, $team2_id = null, $limit = null, $amf = 0) 
    {
                Configure::write('debug', '0');     
        if (isset($this->request->params['form']['team1_id'])) {
            $team1_id = $this->request->params['form']['team1_id']; 
        }
        if (isset($this->request->params['form']['team2_id'])) {
            $team2_id = $this->request->params['form']['team2_id']; 
        }
        if (isset($this->request->params['form']['limit'])) {
            $limit =  $this->request->params['form']['limit']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }
        $this->Game->recursive = -1;
        $conditions['OR'] = array(array('Game.team1_id'=>$team1_id,'Game.team2_id'=>$team2_id),
                              array('Game.team1_id'=>$team2_id,'Game.team2_id'=>$team1_id));
        $conditions['Game.status <>'] = 'Deleted'; 
        $games = $this->Game->find(
            'all', array(
            'conditions'=>$conditions,
            'limit'=>$limit,
            'order'=>array('Game.id'=>'DESC'),
            'contain'=>array('Team1'=>array('User'=>array('fields'=>array('lgn','firstname','lastname','id','email'))),
                        'Team2'=>array('User'=>array('fields'=>array('lgn','firstname','lastname','id','email')))))
        );
        return $this->returnMobileResult($games, $amf);  
    }
    function m_getRecentMobileGames($limit = 10, $timeStamp = 0, $amf = 0) 
    {
        if (isset($this->request->params['form']['limit'])) {
            $limit =  $this->request->params['form']['limit']; 
        }
        if (isset($this->request->params['form']['timeStamp'])) {
            $timeStamp = $this->request->params['form']['timeStamp']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }
        
        //setting limit at 500....we'll need to revisit this
        if ($limit > 500) { 
            $limit = 500; 
        }
        $this->Game->recursive -1;
        //If timestamp is set, lets use that
        if ($timeStamp) {
            $games = $this->Game->find(
                'all', array(
                'conditions'=>array(
                'Game.mobile'=>1,
                'Game.status'=>'Completed',
                'Game.created >'=>$timeStamp),
                'order'=>array('Game.id'=>'DESC'),
                'contain'=>array(
                'Team1'=>array('fields'=>array('id','name'),'User'=>array('fields'=>array('email','id','lgn'))),
                'Team2'=>array('fields'=>array('id','name'),'User'=>array('fields'=>array('email','id','lgn')))),
                'limit'=>$limit)
            );    
        }
        else {
            $games = $this->Game->find(
                'all', array(
                'conditions'=>array(
                'Game.mobile'=>1,
                'Game.status'=>'Completed'),
                'order'=>array('Game.id'=>'DESC'),
                'contain'=>array(
                'Team1'=>array('fields'=>array('id','name'),'User'=>array('fields'=>array('email','id','lgn'))),
                'Team2'=>array('fields'=>array('id','name'),'User'=>array('fields'=>array('email','id','lgn')))),
                'limit'=>$limit)
            );
        }
        return $this->returnMobileResult($games, $amf);
    }
    function getPlayerFromMobileData($playerData) 
    {     
        if (!isset($playerData['email']) || !isset($playerData['pwd']) || !isset($playerData['timestamp'])) { 
            return 'Need an email, password, and a timestamp'; 
        }
        //We're using getOrCreateUser....this function creates a randomly generated password if the account doesnt exist  
              
        return $this->getOrCreateUser($playerData['email'], $playerData['pwd'], $playerData['timestamp']);
    }
    function getTeamFromMobileData($teamData,$isSingles) 
    {
        //     if ($this->getUserID() < 2)
        //       return "You are not logged in.";
        $player1 = $this->getPlayerFromMobileData($teamData['Player1']);
        if (!$player1) { 
            return false; 
        }
        if (!isset($player1['User']['id'])) {
            return $player1; 
        }
        if ($isSingles) { 
            return $this->Team->getSinglesTeam($player1['User']['id']);
        }
        $player2 = $this->getPlayerFromMobileData($teamData['Player2']);
        if (!$player2) {
            return false; 
        }
        if (!isset($player2['User']['id'])) {
            return $player2; 
        }
        return $this->Team->createAndGetCompletedTeam(
            $teamData['name'], 2, array($player1['User']['id'],
            $player2['User']['id']), $user['id']
        );
    }

    function m_getGamesVersus($team1ID = null, $team2ID = null, $amf = 0) 
    {
        if (isset($this->request->params['form']['team1ID'])) {
            $team1ID =  $this->request->params['form']['team1ID']; 
        }
        if (isset($this->request->params['form']['team2ID'])) {
            $team2ID = $this->request->params['form']['team2ID']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }
    
        $this->Game->recursive = -1;
        $games = $this->Game->find(
            'all', array('conditions'=>array(
            'Game.status <>'=>'Deleted',
            'OR'=>array(
                array('Game.team1_id'=>$team1ID,'Game.team2_id'=>$team2ID),
                array('Game.team2_id'=>$team1ID,'Game.team1_id'=>$team2ID))),
            'contain'=>array('Team1','Team2'),
            'order'=>array('Game.id'=>'DESC'))
        );
        return $this->returnMobileResult($games, $amf);
    } 
    function m_saveMobileGame($game = null,$amf = 0,$jsonDecode = 1) 
    {
         Configure::write('debug', '0');
        if (isset($this->request->params['form']['game'])) {
            $game =  $this->request->params['form']['game']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }
        if (isset($this->request->params['form']['jsonDecode'])) {
            $jsonDecode = $this->request->params['form']['jsonDecode']; 
        }
        
        if ($jsonDecode) {
            $game = json_decode($game, true); 
        }
      
        //First, let's try to add the teams;
        if (!isset($game['Team1']) || !isset($game['Team2'])) {
            return $this->returnMobileResult(array('bad'=>'Need to include both teams'), $amf);
        }
        if (!isset($game['winner'])) {
            return $this->returnMobileResult(array('bad'=>'need to know winner'), $amf); 
        }
        $isSingles = $game['issingles'];
        $team1 = $this->getTeamFromMobileData($game['Team1'], $isSingles);
        if (!$team1) { 
            return $this->returnMobileResult(array('Problem with Team 1'=>$team1), $amf); 
        }
        if (!isset($team1['Team']['id'])) {
            return $this->returnMobileResult($team1, $amf); 
        }
      
        $team2 = $this->getTeamFromMobileData($game['Team2'], $isSingles); 
        if (!$team2) {
            return $this->returnMobileResult(array('Problem with Team 2'=>$team1), $amf); 
        }
        if (!isset($team2['Team']['id'])) {
            return $this->returnMobileResult($team2, $amf); 
        }
        /*
        Save the Game
        */       
        if (isset($game['latitude'])) {
            $newGame['Game']['latitude'] = $game['latitude'];
            $newGame['Game']['longitude'] = $game['longitude'];
        }
        if (!isset($game['randstring'])) {
            return $this->returnMobileResult('Need a random string', $amf);
        }
        //Check to see if any game has this random string
        $this->Game->recursive = -1;
        $findGameWithRandomString = $this->Game->find('first', array('conditions'=>array('randstring'=>$game['randstring'])));
        if ($findGameWithRandomString) {
            if ($findGameWithRandomString['Game']['team1_id'] == $team1['Team']['id']) {
                return $this->returnMobileResult('This game has already been submitted', $amf); 
            }
        }
        $newGame['Game']['randstring'] = $game['randstring'];
      
        $newGame['Game']['team1_id'] = $team1['Team']['id'];
        $newGame['Game']['team2_id'] = $team2['Team']['id'];
        if ($game['winner'] == 1) {
            $newGame['Game']['winningteam_id'] = $team1['Team']['id']; 
        }
        else {
            $newGame['Game']['winningteam_id'] = $team2['Team']['id']; 
        }
      
        $newGame['Game']['cupdif'] = $game['cupdif'];
        $newGame['Game']['numots'] = $game['numots'];
        $newGame['Game']['status'] = 'Completed';
        $newGame['Game']['mobile'] = 1;     
        if (!$this->Game->save($newGame['Game'])) {  
            return $this->returnMobileResult(array('cannot save game'=>$game), $amf); 
        }
        $gameID = $this->Game->getLastInsertID();
        /*
        Update stats
        */
        $this->Team->updateStatsForTeam($team1['Team']['id'], 1);
        $this->Team->updateStatsForTeam($team2['Team']['id'], 1);
        /*
        Get the saved game
        */
        $resultGame = $this->Game->find(
            'first', array('conditions'=>array('Game.id'=>$gameID,'Game.status'=>'Completed'),
            'contain'=>array(        
            'Team1'=>array('User'),
            'Team2'=>array('User')))
        );
        /*
        Mark Ratings
        */
        $result = $this->markRatingAndPointsForMobileGame($resultGame, 0);
        if (!$result) {
            return $this->returnMobileResult(array('bad'=>$result), $amf); 
        }
        else {
            $dataToReturn = array('ok'=>array('newGame'=>$resultGame,'changes'=>$this->getRatingAndPointChangesForMobileGame($gameID)));
            return $this->returnMobileResult($dataToReturn, $amf);    
        }
    }
  
    function getRatingAndPointChangesForMobileGame($gameID) 
    {
        $ratingHistories = $this->Ratinghistory->find(
            'all', array(
            'conditions'=>array(
            'model'=>'User',
            'game_id'=>$gameID),
            'contain'=>array('User'))
        );
        $ctr = 0;
        foreach ($ratingHistories as $ratingHistory) {
            $userRatingChangeData[$ctr]['User'] = $ratingHistory['User'];
            $userRatingChangeData[$ctr]['beforeRating'] = $ratingHistory['Ratinghistory']['before'];
            $userRatingChangeData[$ctr]['afterRating'] = $ratingHistory['Ratinghistory']['after'];    
            $ctr++;
        }
        //Now get affil change data
        $affilPoints = $this->Affilspoint->find(
            'all', array(
            'conditions'=>array(
            'Affilspoint.game_id'=>$gameID,
            'Affilspoint.status <>'=>'Deleted'),
            'contain'=>array('School','Greek','City','Organization'))
        );
        $ctr = 0;   
        foreach ($affilPoints as $affilPoint) {
            $affilmodel = $affilPoint['Affilspoint']['model'];
            $affilChangeData[$ctr][$affilmodel] = $affilPoint[$affilmodel];
            $affilChangeData[$ctr]['Affilspoint'] = $affilPoint['Affilspoint'];
            $ctr++;
         
        }
        return array('UserRatingChanges'=>$userRatingChangeData,
                'AffiliationPointsChanges'=>$affilChangeData);
     
    }
  
    private function markRatingAndPointsForMobileGameByID($gameID) 
    {
        $game = $this->Game->find(
            'first', array('conditions'=>array('Game.id'=>$gameID,'Game.status'=>'Completed'),
            'contain'=>array(
                'Event',
                'Team1'=>array('User'),
                'Team2'=>array('User')))
        );
        if (!$game) {
            return array('bad'=>'game not found'); 
        }
        return $this->markRatingAndPointsForMobileGame($game);
    }
    function testMarkRating($startID,$endID) 
    {
        $ctr = 0;
        while ($ctr <= $endID) {
            $game = $this->Game->find(
                'all', array(
                'conditions'=>array(
                'Game.id'=>$ctr,
                'Game.status'=>'Completed'),
                'contain'=>array(
                'Event',
                'Team1'=>array('User'),
                'Team2'=>array('User')
                ))
            );      
            $ctr++;
        }
        return ; 
        /*
        return $this->Game->find('all',array(
        'conditions'=>array(
        'Game.id >='=>$startID,
        'Game.id <='=>$endID,
        'Game.status'=>'Completed'),
        'contain'=>array(
        'Event',
        'Team1'=>array('User'),
        'Team2'=>array('User')
            )));  */
    }
    function markRatingForGames_api($startID,$endID,$weight) 
    {
        if (!$this->isUserSuperAdmin()) {
            return "Access Denied."; 
        }
        $ctr = $startID;
        while ($ctr <= $endID) {
            $game = $this->Game->find(
                'first', array(
                'conditions'=>array(
                'Game.id'=>$ctr,
                'Game.status'=>'Completed'),
                'contain'=>array(
                'Event',
                'Team1'=>array('User'),
                'Team2'=>array('User')))
            );
            if ($game) {
                $this->markRatingForGame($game, $weight); 
            }   
            $ctr++;     
        }
              
        return "ok";
    }
    function markRatingForGameByID_api($id,$weight) 
    {
        if (!$this->isUserSuperAdmin()) {
            return "Access Denied."; 
        }
        $games = $this->Game->find(
            'all', array(
            'conditions'=>array(
              'Game.id'=>$id,
              'Game.status'=>'Completed'),
            'contain'=>array(
              'Event',
              'Team1'=>array('User'),
              'Team2'=>array('User')))
        );
        if (!$games) {
            return "Games not found"; 
        }
        foreach ($games as $game) {
            $this->markRatingForGame($game, $weight);    
        }
        return "ok";
    }
  
    function updateNBPLRatings($maximum = 3)
    {
        $this->Event->recursive = -1;
        $events = $this->Event->find(
            'all', array(
            'conditions'=>array('Event.ratingsupdated'=>0,
                'Event.is_deleted'=>0,
                'Event.type'=>'nbplweekly',
                'Event.iscompleted'=>1),
            'limit'=>1)
        );
        foreach ($events as $event)
        {      
            $result = $this->markRatingForEvent($event['Event']['id'], 15);
            if ($result != "ok") {
                $this->Session->setFlash("Problem: "+$result, 'flash_error');
                $this->redirect('/');
            }
        }
        $this->Session->setFlash(count($events)." events have been marked", 'flash_success');
        $this->redirect('/pages/cron_links');   
    }
    private function markRatingForEvent($eventID,$weight)
    {
        //This assumes that event exists and has not been marked, so it must be called by a function that
        //can check this
        $this->Game->recursive = -1;
        $games = $this->Game->find(
            'all', array(
            'conditions'=>array(
            'Game.event_id'=>$eventID,
            'Game.status'=>'Completed'),
            'fields'=>array('id'))
        );
        foreach ($games as $game)
        {
            $gameToUpdate = $this->Game->find(
                'first', array(
                'conditions'=>array('Game.id'=>$game['Game']['id']),
                'contain'=>array(   
                'Team1'=>array('User'),
                'Team2'=>array('User')))
            );
            $result = $this->markRatingForGame($gameToUpdate, $weight);
            if ($result != "ok") {
                return $result; 
            }
        }        
        $this->Event->recursive = -1;
        $event = $this->Event->find('first', array('conditions'=>array('id'=>$eventID)));
        $event['Event']['ratingsupdated'] = 1;
        $this->Event->save($event);
        return "ok";
    }
    private function markRatingForGame($game,$weight) 
    {
        if ($game['Game']['winningteam_id'] == $game['Game']['team1_id']) {
            $winningTeam = $game['Team1'];
            $losingTeam = $game['Team2'];
        }
        else {
            $winningTeam = $game['Team2'];
            $losingTeam = $game['Team1'];
        }
        /*
        Start with Rating info:
        1. Get teammates ids
        2. Get their ratings
        3. Get team ratings
        4. Get average Teammate Ratings
        */
        $winningTeammatesIDs = Set::extract($winningTeam['User'], '{n}.id');
        $losingTeammatesIDs = Set::extract($losingTeam['User'], '{n}.id');
              
        $winningTeammatesRatings = array();
        $losingTeammatesRatings = array();
        //$winningTeammatesRatings = $this->Ratinghistory->getUserRatings($winningTeammatesIDs);
        //$losingTeammatesRatings = $this->Ratinghistory->getUserRatings($losingTeammatesIDs);  
        foreach ($winningTeam['User'] as $u) {
            if ($u['rating'] == 0) {
                $winningTeammatesRatings[$u['id']] = INITIAL_PLAYER_RATING; 
            }
            else {
                $winningTeammatesRatings[$u['id']]=$u['rating']; 
            }
        }
        foreach ($losingTeam['User'] as $u) {
            if ($u['rating'] == 0) {
                $losingTeammatesRatings[$u['id']] = INITIAL_PLAYER_RATING; 
            }
            else {
                $losingTeammatesRatings[$u['id']] = $u['rating']; 
            }
        }       
        
        //      $winningTeamRating = $winningTeam['rating']; // $this->Ratinghistory->getTeamRating($winningTeam['id'],$winningTeammatesRatings);
        //      $losingTeamRating = $losingTeam['rating']; // $this->Ratinghistory->getTeamRating($losingTeam['id'],$losingTeammatesRatings);
           
        $winningTeammatesAverageRating = $this->Ratinghistory->getTeammatesRating($winningTeammatesRatings);
        $losingTeammatesAverageRating = $this->Ratinghistory->getTeammatesRating($losingTeammatesRatings);
                   
        if ($game['Game']['isforfeit']) { $cupdif = 3; 
        }
        else if ($game['Game']['numots'] > 0) { $cupdif = 1; 
        }
        else { $cupdif = $game['Game']['cupdif']; 
        }
        
        //      $playerRatingChange = $this->Ratinghistory->getRatingChange($winningTeammatesAverageRating,$losingTeammatesAverageRating,$cupdif);
        foreach ($winningTeammatesIDs as $userid) {
            $before = $winningTeammatesRatings[$userid];
            $playerRatingChange = $this->Ratinghistory->getRatingChange(
                $before, $losingTeammatesAverageRating, $cupdif
            );
            $after = $before + ($weight * $playerRatingChange); 
            if (!$this->Ratinghistory->setRating('User', $userid, $winningTeam['id'], $game['Game']['id'], $weight, $before, $after)) {
                return 'problem'; 
            }
            if (!$this->User->setUserRating($userid, $after)) {
                return 'problem'; 
            }
        }       
        foreach ($losingTeammatesIDs as $userid) {
            $before = $losingTeammatesRatings[$userid];
            $playerRatingChange = $this->Ratinghistory->getRatingChange(
                $winningTeammatesAverageRating, $before, $cupdif
            );
            $after = $before - ($weight * $playerRatingChange);
            if (!$this->Ratinghistory->setRating('User', $userid, $losingTeam['id'], $game['Game']['id'], $weight, $before, $after)) {
                return 'Couldnt set User Rating History'; 
            }      
            if (!$this->User->setUserRating($userid, $after)) {
                return 'Couldnt set User Rating'; 
            }  
        }    
        return "ok";              
    }
    private function markRatingAndPointsForMobileGame($game,$skipPoints=0) 
    {        
        if ($game['Game']['winningteam_id'] == $game['Game']['team1_id']) {
            $winningTeam = $game['Team1'];
            $losingTeam = $game['Team2'];
        }
        else {
            $winningTeam = $game['Team2'];
            $losingTeam = $game['Team1'];
        }
        /*
        Start with Rating info:
        1. Get teammates ids
        2. Get their ratings
        3. Get team ratings
        4. Get average Teammate Ratings
        */
        $winningTeammatesIDs = Set::extract($winningTeam['User'], '{n}.id');
        $losingTeammatesIDs = Set::extract($losingTeam['User'], '{n}.id');
              
        
        $winningTeammatesRatings = $this->Ratinghistory->getUserRatings($winningTeammatesIDs);
        $losingTeammatesRatings = $this->Ratinghistory->getUserRatings($losingTeammatesIDs);  
                
        /*   $winningTeamRating = $this->Ratinghistory->getTeamRating($winningTeam['id'],$winningTeammatesRatings);
        $losingTeamRating = $this->Ratinghistory->getTeamRating($losingTeam['id'],$losingTeammatesRatings);*/
           
        $winningTeammatesAverageRating = $this->Ratinghistory->getTeammatesRating($winningTeammatesRatings);
        $losingTeammatesAverageRating = $this->Ratinghistory->getTeammatesRating($losingTeammatesRatings);
        
        $cupdif = $this->Ratinghistory->getEffectiveCupDif($game['Game']);
        
        foreach ($winningTeammatesIDs as $userid) {
                $before = $winningTeammatesRatings[$userid];
                $playerRatingChange = $this->Ratinghistory->getRatingChange(
                    $before, $losingTeammatesAverageRating, $cupdif
                );
                $after = $before + (GAME_WEIGHT_MOBILE * $playerRatingChange); 
                if (!$this->Ratinghistory->setRating('User', $userid, $winningTeam['id'], $game['Game']['id'], GAME_WEIGHT_MOBILE, $before, $after)) {
                    return 'problem'; 
                }
                if (!$this->User->setUserRating($userid, $after)) {
                    return 'problem'; 
                }
        }       
        foreach ($losingTeammatesIDs as $userid) {
                $before = $losingTeammatesRatings[$userid];
                $playerRatingChange = $this->Ratinghistory->getRatingChange(
                    $winningTeammatesAverageRating, $before, $cupdif
                );
                $after = $before - (GAME_WEIGHT_MOBILE * $playerRatingChange);
                if (!$this->Ratinghistory->setRating('User', $userid, $losingTeam['id'], $game['Game']['id'], GAME_WEIGHT_MOBILE, $before, $after)) {
                    return 'problem'; 
                }      
                if (!$this->User->setUserRating($userid, $after)) {
                    return 'problem'; 
                }  
        }                  
        /*
        $teamRatingChange = $this->Ratinghistory->getRatingChange($winningTeamRating,$losingTeamRating,$cupdif);
        $before = $winningTeamRating;
        $after = $winningTeamRating + (GAME_WEIGHT_MOBILE * $teamRatingChange);               
        if (!$this->Ratinghistory->setRating('Team',0,$winningTeam['id'],$game['Game']['id'],GAME_WEIGHT_MOBILE,$before,$after))
            return 'problem';
        if (!$this->Team->setTeamRating($winningTeam['id'],$after))
            return 'Problem';
            
        $before = $losingTeamRating;
        $after = $losingTeamRating - (GAME_WEIGHT_MOBILE * $teamRatingChange);   
        if (!$this->Ratinghistory->setRating('Team',0,$losingTeam['id'],$game['Game']['id'],GAME_WEIGHT_MOBILE,$before,$after))
            return 'problem';         
        if (!$this->Team->setTeamRating($losingTeam['id'],$after))
            return 'problem';
            */
            /*
            */
        /*
         *Mark the Affiliation Points
         */
        foreach ($this->Affil->affil_models as $affil_model) 
        {
            if ($affil_model == 'Organization') {
                $winningTeamAffilIDs = $this->OrganizationsUser->getOrgIDsFromPlayersIDs($winningTeammatesIDs);
                $losingTeamAffillIDs = $this->OrganizationsUser->getOrgIDsFromPlayersIDs($losingTeammatesIDs);
            }
            else 
            { 
                $winningTeamAffilIDs = $this->UsersAffil->getAffilIDsFromPlayersIDs($affil_model, $winningTeammatesIDs);
                $losingTeamAffillIDs = $this->UsersAffil->getAffilIDsFromPlayersIDs($affil_model, $losingTeammatesIDs); 
            }        
            
            $duplicateIDs = array_intersect($winningTeamAffilIDs, $losingTeamAffillIDs);
            $winningTeamAffilIDs = array_diff($winningTeamAffilIDs, $duplicateIDs);
            $losingTeamAffillIDs = array_diff($losingTeamAffillIDs, $duplicateIDs);

            foreach ($winningTeamAffilIDs as $id) 
            {
                if ($affil_model == 'Organization') {
                    if (!$this->Affilspoint->insertAffilsPoint(
                        'Organization', $id,
                        $game['Game'], $winningTeam['id'], 1
                    )) {
                            return 'problem'; 
                    }
                    return 3;
                    $this->Organization->recalculatePoints($id);
                }
                else { 
                    if (!$this->Affilspoint->insertAffilsPoint(
                        $affil_model, $id,
                        $game['Game'], $winningTeam['id'], 1
                    )) {
                            return 'problem'; 
                    }
                    $this->Affilspoint->recalculatePointsForAffil($affil_model, $id);
                }
            }
            foreach ($losingTeamAffillIDs as $id) {    
                if ($affil_model == 'Organization') {
                    if (!$this->Affilspoint->insertAffilsPoint('Organization', $id, $game['Game'], $losingTeam['id'], 0)) {
                        return 'problem'; 
                    }
                    $this->Organization->recalculatePoints($id);
                }
                else {
                    if (!$this->Affilspoint->insertAffilsPoint($affil_model, $id, $game['Game'], $losingTeam['id'], 0)) {
                        return 'problem'; 
                    }
                    $this->Affilspoint->recalculatePointsForAffil($affil_model, $id);
                } 
            } 
        }
        
        return true;                  
    }

  
    //The array must be sorted by BPONGID
    function saveGames_api($games) 
    {
        $firstGame = $games[0];
        if (!$firstGame) { 
            return "invalid data"; 
        }
        if (!$firstGame['event_id'] > 0) { return "invalid data"; 
        } 
        $managers = $this->Event->getManagersId($firstGame['event_id']);
        if (!$this->Access->getAccess('event', 'u', $managers)) {
            return "Access Denied"; 
        }  
    
        // maintain a list of all teams that referenced by these games
        $teamIDs = array();  
        $linkedGamesToCheck = array();   
        $currID = 0;
        //   return 'here2';
        foreach ($games as $currentGame) {
            if (!($currentGame['id'] > $currID)) {
                return 'invalid data'; 
            }
            $currID = $currentGame['id'];
            // Is the game number valid?
            if (!($currentGame['id'] > 0)) { return "invalid data"; 
            }
            // Is the event number valid?
            if (!($currentGame['event_id']==$firstGame['event_id'])) { return "invalid data"; 
            }
        
            // create a list of team ids
            if ($currentGame['team1_id']> 0) {
                array_push($teamIDs, $currentGame['team1_id']); 
            }
            if ($currentGame['team2_id']> 0) {
                array_push($teamIDs, $currentGame['team2_id']); 
            }
            
            // create a list of linked games to check
            if ($currentGame['team1iswinnerof'] > 0) {  
                array_push($linkedGamesToCheck, $currentGame['team1iswinnerof']); 
            }
            if ($currentGame['team2iswinnerof'] > 0) {  
                array_push($linkedGamesToCheck, $currentGame['team2iswinnerof']); 
            }
            if ($currentGame['team1isloserof'] > 0) {  
                array_push($linkedGamesToCheck, $currentGame['team1isloserof']); 
            }               
            if ($currentGame['team2isloserof'] > 0) {  
                array_push($linkedGamesToCheck, $currentGame['team2isloserof']); 
            }
        }
        //    return 'here3';
        $uniqueTeamIDs = $this->custom_array_unique($teamIDs);
        // are teams real?
        $this->Team->recursive = -1;
        $teamResults = $this->Team->find(
            'all', array('conditions'=>array(
            'Team.id'=>$uniqueTeamIDs,
            'Team.status <>'=>'Deleted'))
        );
        if (count($uniqueTeamIDs) != count($teamResults)) { 
            return "Some teams do not exist"; 
        }
        
        // are the linked games real?
        $uniqueLinkedGameIDs = $this->custom_array_unique($linkedGamesToCheck);
        $this->Game->recursive = -1;
        $gameResults = $this->Game->find(
            'all', array('conditions'=>array(
            'Game.id'=>$uniqueLinkedGameIDs,
            'Game.status <>'=>'Deleted'))
        );
        if (count($uniqueLinkedGameIDs) != count($gameResults)) {
            return "invalid data"; 
        }
        // return 'here4';
        // is the requester a manager of the event                                   
        
        //        return 'here5';
        $gamesIDs = Set::extract($games, '{n}.id');  
        // do the games actually exist?
        $this->Game->recursive = -1;
        $matchingGames = $this->Game->find(
            'all', array('conditions'=>array(
            'id'=>$gamesIDs,
            'status <>'=>'Deleted'),
            'order'=>array('id'))
        );
        
        if (count($gamesIDs) != count($matchingGames)) {
            return array("invalid data",$gamesIDs,$matchingGames); 
        }
        foreach ($matchingGames as $matchingGame) {
            // have the event_id's changed? 
            if ($matchingGame['Game']['event_id'] != $firstGame['event_id']) { return "problem"; 
            }    
        }  
    
        for ($ctr = 0; $ctr < count($matchingGames); $ctr++) {
            $matchingGame = $matchingGames[$ctr];
            $game = $games[$ctr];
            if (!matchingGame) {
                return "problem"; 
            }
            if (!$game) {
                return "problem"; 
            }
            //Change of plans...only change the ranks when the team_objects change
            //Only change rankings when rankings change
            // Has the winning team changed, have either teams changed, or have the cup differentials changed? 
            // If so, reverse the previous mark of total w/l/cd, and apply the current games results
            /*
            if (($matchingGame['Game']['winningteam_id'] != $game['winningteam_id']) 
            || ($matchingGame['Game']['team1_id'] != $game['team1_id'])
            || ($matchingGame['Game']['team2_id'] != $game['team2_id'])
            || ($matchingGame['Game']['cupdif'] != $game['cupdif'])
            || ($matchingGame['Game']['isforfeit'] != $game['isforfeit']) 
            || ($matchingGame['Game']['numots'] != $game['numots'])) {
            if ($matchingGame['Game']['winningteam_id'] > 0)
                $this->reverseGameResult($matchingGame);
            if ($game['winningteam_id'] > 0)  {
                $this->markGameResult(array('Game'=>$game)); 
            }
            if (!$this->Game->save($game)) return "problem saving";    
            }  else */
            if (!$this->Game->save($game)) { 
                return "problem saving"; 
            }
        }
        return "ok";
    }
    /**
   * API Function
   * This function takes a game object and saves it. The game must already exist, and the new game
   * data must correspond to the existing game (same id and event_id). The requester must be a manager
   * of the event.
   * @param unknown_type $game
   */
    function saveGame_api($game) 
    {
        if (!$this->isLoggined()) {
            return "You are not logged in.";
        }
        $user = $this->Session->read('loggedUser');
        if ($user['id']==1) { return "You are not logged in."; 
        } 
    
        // Is the game number valid?
        if (!($game['id']>0)) { return "invalid data"; 
        }
    
        // Is the event number valid?
        if (!($game['event_id']>0)) { return "invalid data"; 
        }
    
        // is the requester a manager of the event
        $manager = ClassRegistry::init('Manager');
        if (!$manager->isManager($user['id'], 'Event', $newGame['event_id'])) {
            return "Access Denied. User:".$user['id']."Event:".$game['event_id']; 
        }    
        
        // does the game actually exist?
        $this->Game->recursive = -1;
        $matchingGame = $this->Game->find(
            'first', array('conditions'=>array(
            'id'=>$game['id'],
            'status <>'=>'Deleted'))
        );
        // have the event_id's changed?   
        if (!$matchingGame) { return "game not found"; 
        }
        if (!($matchingGame['Game']['event_id']==$game['event_id'])) { return "problem"; 
        }
      
        // are teams real?
        $this->Team->recursive = -1;
        $team1 = $game['team1_id'];
        if ($team1 > 0) { // if $team1==0, this is ok, because team has not been set yet  
            $teamResults = $this->Team->find(
                'all', array('conditions'=>array(
                'Team.id'=>$team1,
                'Team.status <>'=>'Deleted'))
            );
            if (empty($teamResults)) { return "Team 1 dose not exist"; 
            }
        }
        $team2 = $game['team2_id'];
        if ($team2 > 0) {
            $teamResults = $this->Team->find(
                'all', array('conditions'=>array(
                'Team.id'=>$team2,
                'Team.status <>'=>'Deleted'))
            );
            if (empty($teamResults)) { return "Team 2 dose not exist"; 
            }
        }

        // do the linked games exist and are they part of the same event?
        if ($game['team1iswinnerof'] > 0) {
            if (!$this->doesGameExist($game['team1iswinnerof'], $game['event_id'])) {
                return "problem"; 
            }
        }
        if ($game['team2iswinnerof'] > 0) {
            if (!$this->doesGameExist($game['team2iswinnerof'], $game['event_id'])) {
                return "problem"; 
            }
        }    
        if ($game['team1isloserof'] > 0) {
            if (!$this->doesGameExist($game['team1isloserof'], $game['event_id'])) {
                return "problem"; 
            }
        }    
        if ($game['team1isloserof'] > 0) {
            if (!$this->doesGameExist($game['team1isloserof'], $game['event_id'])) {
                return "problem"; 
            }
        }   
        // Has the winning team changed, have either teams changed, or have the cup differentials changed? 
        // If so, reverse the previous mark of total w/l/cd, and apply the current games results
        /*    if (($matchingGame['Game']['winningteam_id'] != $game['winningteam_id']) 
        || ($matchingGame['Game']['team1_id'] != $game['team1_id'])
        || ($matchingGame['Game']['team2_id'] != $game['team2_id'])
        || ($matchingGame['Game']['cupdif'] != $game['cupdif'])
        || ($matchingGame['Game']['isforfeit'] != $game['isforfeit']) 
        || ($matchingGame['Game']['numots'] != $game['numots'])) {  */
        /*	if ($matchingGame['Game']['winningteam_id'] > 0)
        $this->reverseGameResult($matchingGame);
        if ($game['winningteam_id'] > 0)  {
        $this->markGameResult(array('Game'=>$game)); 
        } */
        if ($this->Game->save($game)) { return "ok"; 
        }    
        //    }  elseif ($this->Game->save($game)) return "ok";
        else { return "problem"; 
        }
    }
  
    /** 
   * Determines whether a game with the given ID exists and belongs to the given Event
   * author:skinny
   */
    function doesGameExist($gameID, $eventID) 
    {
        $this->Game->recursive = 1;
        $result = $this->Game->find(
            'first', array('conditions'=>array(
            'Game.id'=>$gameID,
            'Game.status <>' =>'Deleted'))
        );
        if ($result) { return true; 
        }
        else { return false; 
        }
    }
    /**
   * API Function
   * Saves an array of games
   * author:skinny
   * */
    function saveGames_old($games) 
    {
        if (!$this->isLoggined()) {
            return "You are not logged in.";
        }
        $user = $this->Session->read('loggedUser');
        if ($user['id']==1) { return "You are not logged in."; 
        }         
        $problem = false;
        $gamesaved = true;
        foreach ($games as $game) {       
            $result = $this->saveGame_api($game);
            if ($result == "ok") { $gamesaved = true; 
            }
            else { return $result; 
            }
        }
        if ($gamesaved) {
            if ($problem) { 
                return "Not all games were saved."; 
            }
            else { 
                return "ok"; 
            }
        }
        else { 
            return "No games were saved."; 
        }
    }    
    /**
   * Deletes an array of games
   * Author:skinny
   */
    function deleteGames_api($games) 
    {
        if (!$this->isLoggined()) {
            return "You are not logged in.";
        }
        $user = $this->Session->read('loggedUser');
        if ($user['id']==1) { return "You are not logged in."; 
        } 
        $problem = false;
        $gamedeleted = false;
        foreach ($games as $game) {
            $result = $this->deleteGame_api($game);
            if ($result == "ok") { $gamedeleted = true; 
            }
            else { $problem = true; 
            }
        }
        if ($gamedeleted) {
            if ($problem) { 
                return "Not all games were deleted."; 
            }
            else { 
                return "ok"; 
            }
        }
        else { 
            return "No games were deleted."; 
        }
    }
    /**
   * Deletes a game
   * author:skinny
   */
    function deleteGame_api($game) 
    {    
        if (!$this->isLoggined()) {
            return "You are not logged in.";
        }
        $user = $this->Session->read('loggedUser');
        if ($user['id']==1) { return "You are not logged in."; 
        } 

          
        //is user a manager of this event?
        $this->Game->recursive = -1;
        $gameToDelete = $this->Game->find(
            'first', array('conditions'=>array(
            'status <>'=>'Deleted',
            'id'=>$game['id']
            ))
        ); 
        if (empty($gameToDelete)) { return "Game not found."; 
        }
        /*     if ($gameToDelete['Game']['winningteam_id'] > 0)
        $this->reverseGameResult($gameToDelete); */
        $gameToDelete['Game']['status'] = 'Deleted';
        if ($this->Game->save($gameToDelete)) {
            return "ok";
        }
        else { return ('Could not delete game: '.$game['id']); 
        }
    }
    /**
   * API Function
   * adds a games to an event
   */
    function addGameToEvent_api($newGame)
    {
      
        if (!$this->isLoggined()) {
            return "You are not logged in.";
        }
        $user = $this->Session->read('loggedUser');
        if ($user['id']==1) { return "You are not logged in."; 
        } 
        $this->Game->recursive = -1;
        // does this game already exist?
        $doesGamesExist = $this->Game->find(
            'first', array('conditions'=>array(
            'event_id' => $newGame['event_id'],
            'gamenumber'=> $newGame['gamenumber']
            ))
        );
        if ($doesGameExist) { return "Game already exists"; 
        }
        if (isset($newGame['id'])) {
            if ($newGame['id']>0) { return "BPONG ID greater than 0, cannot add"; 
            }
            else { unset($newGame['id']); 
            }
        } 
        // Does event exist?
        $eventID = $newGame['event_id'];
        $doesEventExist = $this->Event->find(
            'first', array('conditions'=>array(
            'id'=>$eventID))
        );
        if (!$doesEventExist) { return 'Event does not exist 3'; 
        }
                                         
        // is the requester a manager of the event
        $manager = ClassRegistry::init('Manager');
        $manager->recursive = -1;
        $managerSearchResult = $manager->find(
            'all', array('conditions'=>array(
            'Manager.model'=>'Event',
            'Manager.model_id'=>$newGame['event_id'],
            'Manager.user_id'=>$user['id']))
        );
        if (empty($managerSearchResult)) { 
            return "Access Denied. User:".$user['id']."Event:".$newGame['event_id']; 
        }                                         
                                         
                                         
        $this->Game->create();
        if ($this->Game->save($newGame)) {
            $newGame['id'] = $this->Game->getLastInsertID();
            // Is there a winning team? If so, mark wins/losses/cupdif.
            /*	  if ($newGame['winningteam_id'] > 0)
            $this->markGameResult(array('Game'=>$newGame)); */
             return $newGame ;
        }
        else { return 'Problem saving game'; 
        }
    }  
    function addGamesToEvent_api($newGames) 
    {
        $eventID = 0;
        foreach($newGames as $newGame) {
            if ($eventID == 0) {
                $eventID = $newGame['event_id']; 
            }
            if ($eventID != $newGame['event_id']) {
                return "Not all event ids are the same. Could not add."; 
            }
            if (isset($newGame['id'])) {
                if ($newGame['id']>0) { return "BPONG ID greater than 0, cannot add"; 
                }
                else { unset($newGame['id']); 
                }
            } 
              
        }
         $doesEventExist = $this->Event->find(
             'first', array('conditions'=>array(
             'id'=>$eventID))
         );
        if (!$doesEventExist) { return 'Event does not exist 3'; 
        }
         //Check access
         $managers = $this->Event->getManagersId($eventID);
        if (!$this->Access->getAccess('event', 'u', $managers)) {
            return "Access Denied. User:".$user['id']."Event:".$newGame['event_id']; 
        }
                                          

         $ctr = 0; 
        foreach($newGames as $newGame) {
            unset($newGame['id']);
            unset($newGame['_explicitType']);
            $newGame['mobile'] = 0;
            $this->Game->recursive = -1;
            $this->Game->create();
            if ($this->Game->save(array('Game'=>$newGame))) {
                $newGame['id'] = $this->Game->getLastInsertID();
                // Is there a winning team? If so, mark wins/losses/cupdif.
                /*	  if ($newGame['winningteam_id'] > 0)
                $this->markGameResult(array('Game'=>$newGame)); */
                 
                 $results[$ctr] = $newGame;
                 $ctr++;          
            }
            else { return array('Problem saving game'=>$newGame); 
            }         
        }
         return $results;
    }
    /*
    * API Function
    * Returns all of the games associated with an event
    * author:skinny
    */
    function getEventGames_api($eventID,$round = 'all')
    {
        $this->Game->recursive = -1;
        if ($round == 'all') {
            return $this->Game->find(
                'all', array('conditions'=>array(
                'event_id'=>$eventID,
                'status <>'=>'Deleted'))
            ); 
        }
        else { 
            return $this->Game->find(
                'all', array('conditions'=>array(
                'event_id'=>$eventID,
                'status <> '=>'Deleted',
                'round' => $round
                ))
            ); 
        }
    }

  
  
    /**
   * This marks the wins, losses, and cupdifs of the teams associated with the game. It does not check 
   * to see if the game has already been marked, so the caller needs to know this
   * Doesn't return anything
   * 
   * This is basically obsolete.
   * 
   * author:skinny
   */
    /*
    function markGameResult($game) {       
    // is the requester a manager of the event
    $user = $this->Session->read('loggedUser');    
    $manager = ClassRegistry::init('Manager');
    $manager->recursive = -1;
    $managerSearchResult = $manager->find('all', array('conditions'=>array(
                'Manager.model'=>'Event',
            	'Manager.model_id'=>$game['Game']['event_id'],
            	'Manager.is_confirmed'=>'1',
                'Manager.user_id'=>$user['id'])));
    if (empty($managerSearchResult)) return "here"; //"Access Denied.";  	
    if ($game['Game']['winningteam_id'] > 0) {
    	$winningTeamID = $game['Game']['winningteam_id'];
    	if ($game['Game']['team1_id'] != $winningTeamID)
    		$losingTeamID = $game['Game']['team1_id'];
    	else
    		$losingTeamID = $game['Game']['team2_id'];
    } else return ;
    // All forfeits are counted as 3 cup differentials for total
   	if ($game['Game']['isforfeit'] > 0) {
    $this->Team->markWin($winningTeamID,3);
    $this->Team->markLoss($losingTeamID,3);
   	}
   	// if there were ot's, total cupdif changes by 1
    elseif ($game['Game']['numots'] > 0) {
    	$this->Team->markWin($winningTeamID,1);
    	$this->Team->markLoss($losingTeamID,1);
    }
    else {
    	$this->Team->markWin($winningTeamID,$game['Game']['cupdif']);
    	$this->Team->markLoss($losingTeamID,$game['Game']['cupdif']);
    }
    }
    */
    /**
   * This reverses the winns, losses, and cupdifs of the teams associated with the game. It is used 
   * for when a game result has been cleared/deleted/etc.
   * Doesn't return anything
   * author:skinny
   */
    /* function reverseGameResult($game) {
       
    // is the requester a manager of the event
    $user = $this->Session->read('loggedUser');    
    $manager = ClassRegistry::init('Manager');
    $manager->recursive = -1;
    $managerSearchResult = $manager->find('all', array('conditions'=>array(
                'Manager.model'=>'Event',
            	'Manager.model_id'=>$game['Game']['event_id'],
            	'Manager.is_confirmed'=>'1',
                'Manager.user_id'=>$user['id'])));
    if (empty($managerSearchResult)) return ;  	
    if ($game['Game']['winningteam_id'] > 0) {
    	$winningTeamID = $game['Game']['winningteam_id'];
    	if ($game['Game']['team1_id'] != $winningTeamID)
    		$losingTeamID = $game['Game']['team1_id'];
    	else
    		$losingTeamID = $game['Game']['team2_id'];
    } else return ;
    // if this was a forfeit, it was marked at three cups
   	if ($game['Game']['isforfeit'] > 0) {
   		$this->Team->unmarkWin($winningTeamID,3);
   		$this->Team->unmarkLoss($losingTeamID,3);
   	}
   	// if there were ot's, total cupdif changed by 1
    elseif ($game['Game']['numots'] > 0) {
   		$this->Team->unmarkWin($winningTeamID,1);
   		$this->Team->unmarkLoss($losingTeamID,1);
    }
    else {
   		$this->Team->unmarkWin($winningTeamID,$game['Game']['cupdif']);
   		$this->Team->unmarkLoss($losingTeamID,$game['Game']['cupdif']);
    }
    } */
    /**
   *  It is not recommended to use this method. Team statistics should be calculated based
   * on teams_objects.wins,teams_objects.losses, etc. This way, if we are missing individual 
   * game data, we still get records correct.
   * 
   * Note that there is a function in team_objects_controller for this
   * 
   * @param unknown_type $startTeamID
   * @param unknown_type $endTeamID
   */              /*
    function updateStatisticsForAllTeams($startTeamID = 1,$endTeamID = 100000) {
      for ($teamID = $startTeamID; $teamID <= $endTeamID; $teamID++) {
        $this->updateTeamStatistics($teamID);
      }
      return "ok" ;
    }                  */
    /**
   * Show Event Games by ajax
   * @author Oleg D.
   */
    function ajaxShowEventGames($eventID, $teamID = null) 
    {         
        $paginate['limit'] = 8;
        $paginate['contain'] = array('Brackettype', 'Team1.TeamsObject.model_id = '.$eventID, 'Team2.TeamsObject.model_id = '.$eventID);
        $paginate['conditions'] = array('Game.event_id' => $eventID, 'Game.status' => 'Completed');
        if ($teamID) {
            $paginate['conditions']['OR'] = array('team1_id' => $teamID, 'team2_id' => $teamID);
            $paginate['limit'] = 100;
        }
        $paginate['order'] = array('Game.id' => 'desc');

        $this->paginate = array('Game' => $paginate);
        $games = $this->paginate('Game');
        $this->set(compact('games'));
        $this->render();     
    }
  
    /**
   * Show Affil Games by ajax
   * @author Oleg D.
   */
    function ajaxShowAffilGames($modelName, $id) 
    {         
        $teams = $this->Team->Teammate->getAffilActiveTeamsIDs($modelName, $id);
        if (!empty($teams)) {
            $chartInfo = $this->Team->geatTeamsGamesForChart($teams, 15);
        }        
        $this->paginate = array(
            'limit' => 10,
            'fields' => array('Event.name', 'Event.id', 'Team1.name', 'Team1.id', 'Team1.slug', 'Team2.name', 'Team2.id', 'Team2.slug', 'Brackettype.*', 'Game.*'),
            'contain' => array('Event', 'Team1', 'Team2', 'Brackettype'),
            'order' => array('Game.created' => 'DESC', 'Game.id' => 'DESC'),
            'conditions' => array('OR' => array('team1_id' => $teams, 'team2_id' => $teams), 'AND' => array('Game.status' => 'Completed'))
        
        );        
        $games = $this->paginate('Game');
        $this->set(compact('games'));
        $this->render();     
    } 
    /**
   * Show Event Games
   * @author Oleg D.
   */
    function showEventGames($eventID, $teamID = null) 
    {
        $event = $this->Event->read(null, $eventID);
            
        $paginate['limit'] = 10000;
        $paginate['contain'] = array('Brackettype', 'Team1.TeamsObject.model_id = '.$eventID, 'Team2.TeamsObject.model_id = '.$eventID);
        $paginate['conditions'] = array('Game.event_id' => $eventID, 'Game.status' => 'Completed');
        if ($teamID) {
            $paginate['conditions']['OR'] = array('team1_id' => $teamID, 'team2_id' => $teamID);
        }
        $paginate['order'] = array('Game.id' => 'desc');

        $this->paginate = array('Game' => $paginate);
        $games = $this->paginate('Game');
        $this->set(compact('games', 'event'));
        $this->render();     
    }  
    /**
   * Get opponents by event_id
   * @author Oleg D.
   */    
    function get_event_opponents() 
    {
        Configure::write('debug', '0');
        if (!empty($_REQUEST['team_id'])) {
            $opponents = $this->Game->getTeamsOpponents(intval($_REQUEST['team_id']), array(), intval($_REQUEST['event_id']));                
        } elseif (!empty($_REQUEST['user_id'])) {
             $userTeams = $this->Team->User->Teammate->find('list', array('conditions' => array('user_id' => $_REQUEST['user_id']), 'fields' => array('team_id', 'team_id')));              
            $opponents = $this->Game->getTeamsOpponents(0, $userTeams, intval($_REQUEST['event_id']));    
        }
      
        $result = '<option value="0">Select Opponent</option>';    
        foreach ($opponents as $opponentID => $opponentName) {
            $result.='<option value="' . $opponentID . '">'.$opponentName.'</option>';    
        }    
        echo $result;
        exit;          
      
      
        exit;
    }
    /**
   * Get events by opponent_id
   * @author Oleg D.
   */    
    function get_opponent_events() 
    {
        Configure::write('debug', '0');
        if (!empty($_REQUEST['team_id'])) {        
            $myTeams = intval($_REQUEST['team_id']);            
        } elseif (!empty($_REQUEST['user_id'])) {
            $myTeams = $this->Team->User->Teammate->find('list', array('conditions' => array('user_id' => $_REQUEST['user_id']), 'fields' => array('team_id', 'team_id')));          
        }
        if ($_REQUEST['opponent_id']) {
            $gameEvents = $this->Game->find(
                'all', 
                array(
                'contain' => array('Event'),
                'fields' => array('Event.id', 'Event.name'), 
                'conditions' => 
                array('OR' => 
                array(0 => array('team1_id' => $myTeams, 'team2_id' => intval($_REQUEST['opponent_id'])), 
                1 => array('team1_id' => intval($_REQUEST['opponent_id']), 'team2_id' => $myTeams)), 
                'AND' => array('Game.status' => 'Completed'))
                )
            );
        } else {
            $gameEvents = $this->Game->find(
                'all', 
                array(
                'contain' => array('Event'),
                'fields' => array('Event.id', 'Event.name'), 
                'conditions' => 
                array('OR' => array('team1_id' => $myTeams, 'team2_id' => $myTeams), 
                'AND' => array('Game.status' => 'Completed'))
                )
            );            
        }
        $gameEvents = Set::combine($gameEvents, '{n}.Event.id', '{n}.Event.name');      
      
      
        $result = '<option value="0">Select Tournament</option>';      
        foreach ($gameEvents as $eventID => $eventName) {
            $result.='<option value="' . $eventID . '">'.$eventName.'</option>';    
        }    
        echo $result;     
        exit;
    }
    function m_getTeamsGamesForEvent($teamid,$eventid,$amf = 0) 
    {
        if (isset($this->request->params['form']['teamid'])) { $teamid= $this->request->params['form']['teamid']; 
        }
        if (isset($this->request->params['form']['eventid'])) { $eventid = $this->request->params['form']['eventid']; 
        }
        if (isset($this->request->params['form']['amf'])) { $amf = $this->request->params['form']['amf']; 
        }
        $games = $this->getTeamsGamesForEvent($teamid, $eventid);   
        return $this->returnMobileResult($games, $amf);                                                          
    }                                                           
    function getTeamsGamesForEvent($teamid,$eventid) 
    {                    
        if ($teamid< 1 || $eventid < 1) {
            return $this->returnMobileResult('Invalid Parameters', $amf); 
        }
        $games = $this->Game->find(
            'all', array(
            'conditions'=>array(
            'Game.event_id'=>$eventid,
            'OR'=>array('Game.team1_id'=>$teamid,'Game.team2_id'=>$teamid),
            'Game.status <>'=>'Deleted'),
            'contain'=>array('Team1','Team2','Brackettype'))
        );
        return $games;
    }
    function m_getGamesForEventByUser($userid,$eventid,$amf = 0) 
    {
        if (isset($this->request->params['form']['userid'])) { $userid = $this->request->params['form']['userid']; 
        }
        if (isset($this->request->params['form']['eventid'])) { $eventid = $this->request->params['form']['eventid']; 
        }
        if (isset($this->request->params['form']['amf'])) { $amf = $this->request->params['form']['amf']; 
        }
             //First we need to determine which teams we're talking about...
        //get teamids 
        $Teammate = ClassRegistry::init('Teammate');
        $Teammate->recursive = 0;
        $myTeammates = $Teammate->find(
            'all', array(
            'conditions'=>array(
                'Teammate.user_id'=>$userid,
                'Teammate.status'=>array('Creator','Accepted','Pending'),
                'Team.is_deleted'=>0),
            'contain'=>array('Team'))
        );
        $myteamids = Set::extract($myTeammates, '{n}.Teammate.team_id');
        
        //get all the teamobjects that are associated with the event
        $TeamsObject = ClassRegistry::init('TeamsObject');
        $TeamsObject->recursive = 0;
        $myTeamObjects = $TeamsObject->find(
            'all', array(
            'conditions'=>array(
            'TeamsObject.status <>'=>'Deleted',
            'Event.is_deleted'=>0,
            'TeamsObject.model'=>'Event',
            'TeamsObject.model_id'=>$eventid,
            'TeamsObject.team_id'=>$myteamids))
        );
        $myTeamIDsInEvent = Set::extract($myTeamObjects, '{n}.TeamsObject.team_id');
        // for right now, assume its length 0
        $results = array();         
        foreach ($myTeamIDsInEvent as $id) {
            $results = array_merge($results, $this->getTeamsGamesForEvent($id, $eventid));
        }
        return $this->returnMobileResult($results, $amf);
    }
    function m_getMyGamesForEvent($eventID,$amf = 0) 
    {
        if (isset($this->request->params['form']['eventid'])) { $eventid = $this->request->params['form']['eventid']; 
        }
        if (isset($this->request->params['form']['amf'])) { $amf = $this->request->params['form']['amf']; 
        }
          
        if (!$this->isLoggined()) {
            return $this->returnMobileResult("You are not logged in.", $amf);
        }
        $user = $this->Session->read('loggedUser');
        if ($user['id']==1) { return $this->returnMobileResult("You are not logged in.", $amf); 
        }
        $userid = $user['id'];
        return $this->m_getGamesForEventByUser($userid, $eventID);
    }
    function m_getMostRecentGamesForUser($userIDOrEmail = null,$start = 0,$limit= 10, $amf = 0) 
    {
        if (isset($this->request->params['form']['userIDOrEmail'])) { $userIDOrEmail = $this->request->params['form']['userIDOrEmail']; 
        }
        if (isset($this->request->params['form']['start'])) { $start = $this->request->params['form']['start']; 
        }  
        if (isset($this->request->params['form']['limit'])) { $limit = $this->request->params['form']['limit']; 
        }
        if (isset($this->request->params['form']['amf'])) { $amf = $this->request->params['form']['amf']; 
        }
        //$userIDorEmail could be user.id, user.lgn, or user.email. Figure out which one...
        $this->User->recursive = -1;
        $user = $this->User->find('first', array('conditions'=>array('id'=>$userIDOrEmail)));
        if (!$user) { 
            $user = $this->User->find('first', array('conditions'=>array('email'=>$userIDOrEmail)));
        }
        if (!$user) {
            $user = $this->User->find('first', array('conditions'=>array('lgn'=>$userIDOrEmail)));
        }
        if (!$user) {
            return $this->returnMobileResult('User not found', $amf);
        }
        $userID = $user['User']['id'];
            //First we need to determine which teams we're talking about...
        //get teamids 
        if ($limit > 20) {
            $limit = 20; 
        }
        $Teammate = ClassRegistry::init('Teammate');
        $Teammate->recursive = 0;
        $myTeammates = $Teammate->find(
            'all', array(
            'conditions'=>array(
                'Teammate.user_id'=>$userID,
                'Teammate.status'=>array('Creator','Accepted','Pending'),
                'Team.is_deleted'=>0),
            'contain'=>array('Team'))
        );
        $myteamids = Set::extract($myTeammates, '{n}.Teammate.team_id');
        //return $myteamids;
        $games = $this->Game->find(
            'all', array('conditions'=>array(
            'Game.status'=>'Completed',
            'OR'=>array('Game.team1_id'=>$myteamids,'Game.team2_id'=>$myteamids)),
            'contain'=>array(
                'Team1'=>array('User'=>array('fields'=>array('email','lgn'))),
                'Team2'=>array('User'=>array('fields'=>array('email','lgn'))),
                'Ratinghistory'=>array('conditions'=>array('Ratinghistory.user_id'=>$userID,'Ratinghistory.model'=>'User'))
                ), //=>array('fields'=>array('id'))
            'limit'=>$start.','.$limit,
            'order'=>array('Game.id'=>'DESC'))
        );
        foreach ($games as &$game) {
            if (count($game['Ratinghistory']) > 0) {
                $game['Ratinghistory'] = $game['Ratinghistory']['0'];
            }
        }
          return $this->returnMobileResult($games, $amf);
    }
}
?>
