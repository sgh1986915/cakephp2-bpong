<?php
class RatingsController extends AppController
{
    var $name = 'Ratings';
    var $uses    = array('Rating','Ratinghistory','Game','User','Team','Teammate','Ranking','Rankinghistory',
        'Affil','Affilspoint','UsersAffil','TeamsObject','Event');  

    //These functions just shows the calculations
    function getRatingChange($winnerRating,$loserRating,$cupdif) 
    {
        return $this->Ratinghistory->getRatingChange($winnerRating, $loserRating, $cupdif);            
    }    
    function showratingchangerange() 
    {
        if (!$this->isLoggined()) {
             $this->redirect('/');
        }
        $user = $this->Session->read('loggedUser');
        //only allow superadmins to view right now
        if (!$this->canUserViewStats($user['id'])) {
            return $this->redirect('/'); 
        }
            
            
        for ($diffCtr = 0; $diffCtr <= 30; $diffCtr += 1) {
            for ($cdCtr = -5; $cdCtr <=5; $cdCtr++) {
                if ($cdCtr < 0) {
                    $results[$diffCtr][$cdCtr] = -$this->Ratinghistory->getRatingChange(0, $diffCtr*50, -$cdCtr); 
                }
                else {
                    $results[$diffCtr][$cdCtr] = $this->Ratinghistory->getRatingChange($diffCtr*50, 0, $cdCtr); 
                }
            }
        }
        //return $results;
        $this->set('results', $results);
    }
    function updateNBPLRatings($maximum = 5)
    {
         $this->Event->recursive = -1;
         $events = $this->Event->find(
             'all', array(
              'conditions'=>array('Event.ratingsupdated'=>0,
                'Event.is_deleted'=>0,
                'Event.type'=>'nbplweekly',
                'Event.iscompleted'=>1),
             'limit'=>$maximum)
         );
         foreach ($events as $event)
         {      
             $result = $this->markRatingForEvent($event['Event']['id'], 15);
             if ($result != "ok") {
                 return $result;
                }
            }
            if (count($events) == $maximum) {
                $this->Session->setFlash(count($events)." events have been marked - You should run the script again.", 'flash_success'); 
            }
            else {
                $this->Session->setFlash(count($events)." events have been marked - You should take a snapshot of the rankings.", 'flash_sucess'); 
            }
            $this->redirect('/pages/update_stats');   
    }
    private function markRatingForEvent($eventID,$weight)
    {
        if (!$this->isUserSuperAdmin()) {
            return $this->returnJSONResult('Not Logged in'); 
        }
      
        $teamsByID = array();
        $usersByID = array();
        $teamsObjects = $this->TeamsObject->find(
            'all', array('conditions'=>array(
            'TeamsObject.status'=>array('Created','Approved','Confirmed','Pending'),
            'TeamsObject.model'=>'Event',
            'TeamsObject.model_id'=>$eventID,
            'Team.status'=>array('Created','Pending','Completed')),
            'contain'=>array('Team'=>array('User')))
        );
        foreach ($teamsObjects as $teamsObject)
        {
            $currentTeam = $teamsObject['Team'];
            $teamsByID[$currentTeam['id']] = $currentTeam;
            foreach ($currentTeam['User'] as $user)
            {
                $usersByID[$user['id']] = $user;
                if ($usersByID[$user['id']]['rating']==0) {
                    $usersByID[$user['id']]['rating'] = INITIAL_PLAYER_RATING; 
                }
            }
        }
    
    
        $this->Game->recursive = -1;
        $games = $this->Game->find(
            'all', array(
            'conditions'=>array(
            'Game.event_id'=>$eventID,
            'Game.status'=>'Completed'))
        );
        foreach ($games as $game)
        {
            if ($game['Game']['winningteam_id'] == $game['Game']['team1_id']) {
                $winningTeam = $teamsByID[$game['Game']['team1_id']];
                $losingTeam = $teamsByID[$game['Game']['team2_id']];
            }
            else {
                $winningTeam = $teamsByID[$game['Game']['team2_id']];
                $losingTeam = $teamsByID[$game['Game']['team1_id']];
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
        
            foreach ($winningTeam['User'] as $u) {
                $currentUser = $usersByID[$u['id']];
                if ($currentUser['rating'] == 0) { //theres something wrong if its this low, so assume 0
                    $winningTeammatesRatings[$currentUser['id']] = INITIAL_PLAYER_RATING; 
                }
                else {
                    $winningTeammatesRatings[$currentUser['id']]=$currentUser['rating']; 
                }
            }
            foreach ($losingTeam['User'] as $u) {
                $currentUser = $usersByID[$u['id']];
                if ($currentUser['rating'] == 0) { //theres something wrong if its this low, so assume 0
                    $losingTeammatesRatings[$currentUser['id']] = INITIAL_PLAYER_RATING; 
                }
                else {
                    $losingTeammatesRatings[$currentUser['id']] = $currentUser['rating']; 
                }
            }   
            $winningTeammatesAverageRating = $this->Ratinghistory->getTeammatesRating($winningTeammatesRatings);
            $losingTeammatesAverageRating = $this->Ratinghistory->getTeammatesRating($losingTeammatesRatings);
            if ($game['Game']['isforfeit']) { $cupdif = 3; 
            }
            else if ($game['Game']['numots'] > 0) { $cupdif = 1; 
            }
            else { $cupdif = $game['Game']['cupdif']; 
            }   
            foreach ($winningTeammatesIDs as $userid) {
                //pull the before from the users array, because thats where we'll change it
                $before = $usersByID[$userid]['rating'];
                $playerRatingChange = $this->Ratinghistory->getRatingChange(
                    $before, $losingTeammatesAverageRating, $cupdif
                );
                $after = $before + ($weight * $playerRatingChange); 
                if (!$this->Ratinghistory->setRating('User', $userid, $winningTeam['id'], $game['Game']['id'], $weight, $before, $after)) {
                    return $this->returnJSONResult('couldnt set winners rating history'); 
                }
                $usersByID[$userid]['rating'] = $after;
            }    
            foreach ($losingTeammatesIDs as $userid) {
                $before = $usersByID[$userid]['rating'];
                $playerRatingChange = $this->Ratinghistory->getRatingChange(
                    $winningTeammatesAverageRating, $before, $cupdif
                );
                $after = $before - ($weight * $playerRatingChange);
                if (!$this->Ratinghistory->setRating('User', $userid, $losingTeam['id'], $game['Game']['id'], $weight, $before, $after)) {
                    return $this->returnJSONResult('Couldnt set User Rating History'); 
                }      
                $usersByID[$userid]['rating'] = $after;
            }    
        } 
        //now update all the user ratings      
        foreach ($usersByID as $user)
        {
            if (!$this->User->setUserRating($user['id'], $user['rating'])) {
                return 'Couldnt set User Rating'; 
            }  
        }

        $this->Event->recursive = -1;
        $event = $this->Event->find('first', array('conditions'=>array('id'=>$eventID)));
        $event['Event']['ratingsupdated'] = 1;
        $this->Event->save($event);
        return "ok";
    }
    /*
    private function markEvent($eventID, $weight = .1) {
     $this->Game->recursive = -1;
     $games = $this->Game->find('all',array('conditions'=>array(
      'event_id' => $eventID,
      'status' => 'Completed')));
     $count = 0;
        if (!$games) {
            $this->Session->setFlash('No games found.');
            $this->redirect('/events/all');
        }
        foreach ($games as $game) {
            $result = $this->markGame($game,$weight);
            if ($result == 'ok')
                $count++;
            else return $result;
        }
        return $count;
        $this->Session->setFlash($count.' games rated.');
        $this->redirect('/events/all');
        return 'ok';
    }
    function markGamesByID($startingGameID,$endingGameID,$weight = .1) {
     if (!$this->Session->check('loggedUser')) {
             return "Not logged in.";
        }
        $user = $this->Session->read('loggedUser');
            
        //only allow superadmins to do this
        if (!$this->isUserSuperAdmin($user['id']))
    return "Only skinny has access to this"; 
        	
        	
        $this->Game->recursive = -1;
        $games = $this->Game->find('all',array(
            'conditions'=>array(
                'id >=' => $startingGameID,
                'id <=' => $endingGameID),
            'order'=>array('id'=>'ASC')));
        foreach($games as $game) {
            if (!$this->markGame($game,$weight))
                return 'problem';
        }
        return 'ok';
    }*/
    //If a user has a rating, but did not play in event, adjust them
        
    function adjustUsersForParticipationBasedOnEvent($eventID) 
    {
            
        //only allow superadmins to do this
        if (!$this->isUserSuperAdmin()) {
            return "Only Superadmins have access to this"; 
        }
             
                
        //First get an array consisting of a userid for each person that we have
        //a rating for
        $allUserIDs = $this->Rating->getAllUserIDs();
            
        //Get a list of all team ids associated with the event
        $TeamsObjects = ClassRegistry::init('TeamsObject');
        $TeamsObjects->recursive = -1;
        $matchingTeamObjects = $TeamsObjects->find(
            'all', array('conditions'=>array(
            'model'=>'Event',
            'model_id'=>$eventID,
            'status <>'=>'Deleted'))
        );
        $matchingTeamIDs = Set::extract($matchingTeamObjects, '{n}.TeamsObject.team_id');
        $matchingTeamIDs = array_unique($matchingTeamIDs);
            
        //Now, get a list of all user ids for teammates on those teams
        $Teammates = ClassRegistry::init('Teammate');
        $Teammates->recursive = -1;
        $matchingTeammates = $Teammates->find(
            'all', array('conditions'=>array(
            'team_id'=>$matchingTeamIDs,
            'status'=>array('Creator','Pending','Accepted')))
        );
        $matchingUserIDs = Set::extract($matchingTeammates, '{n}.Teammate.user_id');

        $idsToAdjust = array_diff($allUserIDs, $matchingUserIDs);

        // return $idsToAdjust;
        foreach ($idsToAdjust as $id) {
            $this->adjustUserRatingForParticipation($id);
        }
        return 'ok';
    }
    // Basically, if you don't play for a year, your rating gets cut
    function adjustUserRatingForParticipation($userID) 
    {
        $this->User->recursive = -1;
        $user= $this->User->find('first', array('conditions'=>array('id'=>$userID)));
        if (!$user) {
            return 'User doesnt exist';
        }
        $oldRating = $user['User']['rating'];
        $ratingChange = (($oldRating - 5000) / 5);
        if ($ratingChange < 0) {
            $ratingChange = 0; 
        }
        
        //function addRating($model,$userid,$teamid,$gameID,$weight,$before,$after,$adjustment = 0) {
        $result = $this->Ratinghistory->addRating('User', $userID, 0, 0, 100, $oldRating, $oldRating - $ratingChange, 1);
        $user['User']['rating'] = $oldRating- $ratingChange;
        $this->User->save($user['User']);
    }
    function teamHistory($teamID) 
    {
        if (!$this->Session->check('loggedUser')) {
             $this->redirect('/');
        }
        $user = $this->Session->read('loggedUser');
        //only allow superadmins to view right now
        if (!$this->canUserViewStats($user['id'])) {
            $this->redirect('/'); 
        }
            
            
        $ratingPaginate['conditions'] = array(
            'Ratinghistory.model'=>'Team',
            'Ratinghistory.team_id'=>$teamID);
        $ratingPaginate['order'] = array('Ratinghistory.id'=>'DESC');
        $ratingPaginate['contain'] = array('Game'=>(
            array(
                'Team1',
                'Team2',
                'Ratinghistory',
                'Event'=>array('fields'=>array('name','shortname','id','slug')))));
            
        //            return $this->Ratinghistory->find('all',$ratingPaginate);
        $this->paginate = array('Ratinghistory' => $ratingPaginate);
        $this->set('ratingChanges', $this->paginate('Ratinghistory'));
            
        $this->Team->recursive = -1;
        $team = $this->Team->find('first', array('conditions'=>array('id'=>$teamID)));
        $this->set('team', $team);
    }
    function userHistory($userID) 
    {
        if (!$this->Session->check('loggedUser')) {
             $this->redirect('/');
        }
        $user = $this->Session->read('loggedUser');
        //only allow superadmins to view right now
        //if (!$this->canUserViewStats($user['id']))
        //    $this->redirect('/');
            
        $ratingPaginate['conditions'] = array(
            'Ratinghistory.model'=>'User',
            'Ratinghistory.user_id'=>$userID);
        $ratingPaginate['order'] = array('Ratinghistory.id'=>'DESC');
        $ratingPaginate['contain'] = array('Team','Game'=>(
            array(
                'Team1',
                'Team2',
                'Ratinghistory',
            'Event'=>array('fields'=>array('name','shortname','id','slug')))));
            
        $this->paginate = array('Ratinghistory' => $ratingPaginate);
        $this->set('ratingChanges', $this->paginate('Ratinghistory'));
            
        $this->User->recursive = -1;
        $user = $this->User->find('first', array('conditions'=>array('id'=>$userID)));
        $this->set('user', $user);
    }
    /* From the old ratings_controller....might want to put this in rankings
    var $components = array('Csv');
       
    function exportRatingsCSV() {
        $results = $this->Rating->getRatingsForCSV();
        return $results;
        if ($results) {
            $this->Csv->addGrid($results);
        } 

        $this->Csv->setFilename("ratings");
        echo $this->Csv->render1();
    }      
    */
        
        
    /* 
    function updateRatingsFromHistory() {
         $ratings = $this->Rating->find('all',array('contain'=>array()));
         foreach ($ratings as $rating) {
             $model = $rating['Rating']['model'];
             $model_id = $rating['Rating']['model_id'];
             if ($model == 'Team') {
                 $ratingHistory = $this->RatingHistory->find('first',array(
                     'conditions'=>array(
                         'model'=>'Team',
                         'team_id'=>$model_id),
                     'contain'=>array(),
                     'order'=>array('id'=>'DESC')));
             }
             else {
                 $ratingHistory = $this->RatingHistory->find('first',array(
                     'conditions'=>array(
                         'model'=>'User',
                         'user_id'=>$model_id),
                     'contain'=>array(),
                     'order'=>array('id'=>'DESC')));
             }
             $rating['Rating']['rating'] = $ratingHistory['RatingHistory']['after'];
             $this->RatingHistory->save($rating);
         }
         return "ok";
     }
     */

  
}
?>
