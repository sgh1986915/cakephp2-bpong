<?php
class TeamsObject extends AppModel
{

    var $name      = 'TeamsObject';
    var $useTable  = 'teams_objects';
    //var $recursive = -1;
    var $actsAs = array('Containable');

    var $belongsTo = array(
      'Event' => array('className' => 'Event',
                  'foreignKey' => 'model_id',
                  'dependent' => false,
                  'conditions' => array('TeamsObject.model'=>'Event','Event.is_deleted <>'=>1),
                  'fields' => '',
                  'order' => ''
      ),
      'User' => array(
                        'className'    => 'User',
                        'foreignKey'    => 'assigner_id'
          ),
          'Team' => array(
                        'className'    => 'Team',
                        'foreignKey'    => 'team_id'
          )
      );

    /**
     *  Remove Tournivent assigment
     * @param int userID
     * @param string model name
     * @param int model id
     * @return array team id
     */
    function removeAssigment($userID = null, $model = "Event", $modelID= null)
    {
        $teams = array();

        $sql = "SELECT DISTINCT Team.id FROM teams_objects AS TeamObject
					INNER JOIN teams AS Team
						ON TeamObject.team_id = Team.id
						AND TeamObject.model = '$model'
						AND TeamObject.model_id = $modelID
						AND TeamObject.status IN ('Created','Approved','Confirmed')
					INNER JOIN teammates AS Teammate
						ON  Team.id = Teammate.team_id
						AND Teammate.status IN ('Creator','Accepted')
						AND Teammate.user_id = $userID";

        $teams = $this->query($sql);

        if (!empty($teams)) {
            $History            = ClassRegistry::init('History');
            $Teammate      = ClassRegistry::init('Teammate');
            foreach ($teams as $team) {
                $this->updateAll(array('status'=>'\'Deleted\''), array('team_id'=>$team['Team']['id'],'model'=>$model,'model_id'=>$modelID));

                /*Getting teammates*/
                $myTeammates = $Teammate->find('list', array('fields'=>array('Teammate.user_id,Teammate.user_id'),'conditions'=>array('Teammate.team_id'=>$team['Team']['id'],'Teammate.status'=>array('Creator','Accepted'))));
                /*Update History*/
                $historyParams = array();
                $historyParams['user_id']   = $_SESSION['loggedUser']['id'];
                $historyParams['model']     = $model;
                $historyParams['model_id'] = $modelID;
                $historyParams['affected_user_id'] =serialize($myTeammates);
                $History->teamAssigment('delete', $team['Team']['id'], $historyParams);

            }

        }
        unset($History);
        unset($Teammate);



        return $teams;

    }
    
    function getTeamIds($model, $modelid) 
    {
        $this->recursive = 0;
        $teamsObjects = $this->find(
            'all', array(
            'conditions'=>array(
                'TeamsObject.status <> '=>'Deleted',
                'TeamsObject.model_id'=>$modelid,
                'TeamsObject.model'=>$model,
                'Team.status <> '=>'Deleted'),
            'contain'=>array('Team'))
        );  
            
        $teamIDs = Set::extract($teamsObjects, '{n}.Team.id');
        return $teamIDs;
    }
    function updateNBPLPointsForEvent($eventID) 
    {
        /*This Function, goes through each of the teams_object items and gives the proper number of
          * points to each team. The event must be complete 
          */
        
        //Get the event:
        $this->Event->recursive = -1;
        $event = $this->Event->find(
            'first', array('conditions'=>array(
            'id'=>$eventID,
            'type'=>'nbplweekly'))
        );
        if (!$event) {
            return false; 
        }
        //Get teams_objects
        $this->recursive = -1;
        $teamsObjects = $this->find(
            'all', array(
            'conditions'=>array(
                'model'=>'Event',
                'model_id'=>$eventID,
                'status <>'=>'Deleted'),
            'order'=>array('rank'=>'ASC'))
        );
        if ($event['Event']['iscompleted']) {
            $numTeams = count($teamsObjects);
            $rankCounts = array();
            foreach ($teamsObjects as $teamsObject) {
                $currentRank = $teamsObject['TeamsObject']['rank'];
                if (isset($rankCounts[$currentRank])) {
                    $rankCounts[$currentRank]++;
                }
                else {
                    $rankCounts[$currentRank] = 1;
                }
                //lets say it goes 1,2,2,4. Then 2nd and 3rd need to be split
            }
            unset($teamsObject);
            foreach ($teamsObjects as $teamsObject) {
                $currentRank = $teamsObject['TeamsObject']['rank'];
                //return $this->getNBPLPointsByRank(1,1,6);
                $teamsObject['TeamsObject']['nbplpoints'] = 
                    $this->getNBPLPointsByRankAndCount($currentRank, $rankCounts[$currentRank], $numTeams);
                $this->save($teamsObject);
            }
        }
        else {
            foreach ($teamsObjects as $teamsObject) {
                $teamsObject['TeamsObject']['nbplpoints'] = 0;
                $this->save($teamsObject);
            }
        }
        //Now, we've updated the points for an event, we need to update the points
        //for each team that is associated with this venue
        unset($teamsObject);
        if ($event['Event']['venue_id'] > 0) {
            foreach ($teamsObjects as $teamsObject) {
                $this->Team->updateVenueStatsForTeam(
                    $teamsObject['TeamsObject']['team_id'],
                    $event['Event']['venue_id']
                );
            }
        }
    }
    private function getNBPLPointsByRankAndCount($rank,$count,$numTeams) 
    {
        $rank = intval($rank); $count = intval($count); $numTeams = intval($numTeams);
        $total = 0;
        for ($ctr = $rank; $ctr < $rank+$count;$ctr++) {
            $total += $this->getNBPLPointsByRank($ctr, $numTeams);
        }
        return $total / $count;
    }
    private function getNBPLPointsByRank($rank,$numTeams) 
    {
        $rank = intval($rank); $numTeams = intval($numTeams);
        if ($numTeams < 10) {
            if ($rank < 1) { return 0; 
            }
            if ($rank == 1) { return 10; 
            }
            if ($rank < 4) { return 5; 
            }
            return 1;
        }
        elseif ($numTeams < 30) {
            if ($rank < 1) { return 0; 
            }
            if ($rank == 1) { return 15; 
            }
            if ($rank < 4) { return 10; 
            }
            if ($ank < 6) { return 5; 
            }
            return 1;          
        }
        else {
            if ($rank < 1) { return 0; 
            }
            if ($rank == 1) { return 25; 
            }
            if ($rank < 4) { return 15; 
            }
            if ($ank < 6) { return 8; 
            }
            if ($ank < 8) { return 5; 
            }
            return 1;          
        }   
    }
    /*
    function updateStatsForTeam($teamID) {
            $teamObjects = $this->find('all',array('conditions'=>array(
                'Team.id'=>$teamID,
                'Event.is_deleted'=>0),
                'contain'=>array('Event','Team')));
            $results['total_wins'] = 0;
            $results['total_losses'] = 0;
            $results['total_cupdif'] = 0;
            foreach ($teamObjects as $object) {
                $results['total_wins'] += $object['TeamsObject']['wins'];
                $results['total_losses'] +=  $object['TeamsObject']['losses'];
                $results['total_cupdif'] += $object['TeamsObject']['cupdif'];               
            }
            $results['id'] = $teamID;
            if ($this->Team->save($results))
                return 'ok';
            else 
                return 'fuck';
    }
    function updateStatsForTeams($startID,$endID) {
            $teamData = $this->find('all',array('conditions'=>array(
                'Team.id >= '=> $startID,
                'Team.id <= '=> $endID,
                'Event.is_deleted'=>0),
                'contain'=>array('Event','Team')));
            $ok = 1;
            foreach ($teamData as $teamObjects) {
                //if this is the first time we're seeing this team in the loop, initialize results to 0
                if (!isset($results[$teamObjects['Team']['id']])) {
                    $results[$teamObjects['Team']['id']]['total_wins'] = 0;
                    $results[$teamObjects['Team']['id']]['total_losses'] = 0;
                    $results[$teamObjects['Team']['id']]['total_cupdif'] = 0; 
                    $results[$teamObjects['Team']['id']]['id'] = $teamObjects['Team']['id'];                   
                }
                $results[$teamObjects['Team']['id']]['total_wins'] += $teamObjects['TeamsObject']['wins'];
                $results[$teamObjects['Team']['id']]['total_losses'] += $teamObjects['TeamsObject']['losses'];
                $results[$teamObjects['Team']['id']]['total_cupdif'] += $teamObjects['TeamsObject']['cupdif'];
            }
            foreach ($results as $result) {
                
                if (!$this->Team->save($result))
                    $ok = 0;
            }
            return array('ok'=>$ok);
    }  */
}
?>
