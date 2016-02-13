<?php

class TeamsObjectsController extends AppController
{
      
    var $name = 'TeamsObjects' ;
    var $uses = array('Event','TeamsObject','Team');
    function test_updateNBPLPointsForEvent($eventID) 
    {
        return $this->TeamsObject->updateNBPLPointsForEvent($eventID);
    }
    function test_updateVenueStatsForTeam($teamID,$venueID) 
    {
        return $this->Team->updateVenueStatsForTeam($teamID, $venueID); 
    }
    //the changeseeds function should prob be removed.
    function changeSeeds_api($newSeedObjects) 
    {
          $changesMadeOk = true;
          //return $newSeedObjects;
        foreach ($newSeedObjects as $object) {
            $resultString = $this->changeSeed_api(
                $object['model_id'],
                $object['team_id'], $object['seed']
            );
            if ($resultString <> 'ok') { $changesMadeOk = false; 
            }  
        }
        if ($resultString) { return "ok"; 
        }
        else { return "There was a problem saving the seeds"; 
        }
    }
    function save_api($teamObject) 
    {


          //if ($teamObject['id'] < 1) return "Invalid id provided";
          //for right now, only allow changes if model=='Event'
        if (!($teamObject['model']=='Event')) { return "Invalid model."; 
        }
          $managers = $this->Event->getManagersId($teamObject['model_id']);
        if (!$this->Access->getAccess('event', 'u', $managers)) {
            return "Access Denied";
        }
            $this->TeamsObject->recursive = -1;

            //Handle unsigned to signed conversion
        if($teamObject['cupdif'] > 2147483647) {
            $teamObject['cupdif']-= (4294967295 + 1); 
        }
            
            $objectToSave = $this->TeamsObject->find(
                'first', array('conditions'=>array(
                'status <>'=>'Deleted',
                'model_id'=>$teamObject['model_id'],
                'model'=>$teamObject['model'],
                'team_id'=>$teamObject['team_id']))
            ); 
               
        if (!($objectToSave['TeamsObject']['id']>0)) { return "Could not save Ranking"; 
        }
            // should also check validity of model_id, team_id, etc
            //need to confirm that user is manager of this event  
            
            $teamObject['assigner_id']=$objectToSave['TeamsObject']['assigner_id'];
            $teamObject['id'] = $objectToSave['TeamsObject']['id'];
            //return $objectToSave;
        if ($this->TeamsObject->save($teamObject)) { $result = "ok"; 
        }
        else { $result = "Could not save"; 
        }

            $saveStatsResult = $this->Team->updateStatsForTeam($teamObject['team_id'], 1);
        if (!$saveStatsResult) {
            return 'Could not update team stats'; 
        }
            return $result;
    }
    function saveMany_api($teamObjects) 
    {

          $changesMadeOk = true;
        foreach ($teamObjects as $teamObject) {
            $resultString = $this->save_api($teamObject);
            if ($resultString != 'ok') { 
                return $resultString; 
            }
        }

            return "ok";      
    }
    function changeSeed_api($eventID = null, $teamID = null, $newSeed = -1)  
    {
        if (!eventID || !teamID) { return 'Null parameters'; 
        }
        if (!$this->isLoggined()) {
             return "You are not logged in"; 
        }
         $this->TeamsObject->recursive = -1;  
           
         $currentTeamsObject = $this->TeamsObject->find(
             'all', array('conditions'=>array(
              'model_id'=>$eventID,
              'team_id'=>$teamID,
              'model'=>'Event',
              'status <>'=>'Deleted'))
         );
           
         //$currentTeamsObject = $this->TeamsObject->find('all');     
         if (!$currentTeamsObject) { return "Team is not assigned to event"; 
            }
            $currentTeamsObject[0]['TeamsObject']['seed'] = $newSeed;
            if ($this->TeamsObject->save($currentTeamsObject[0])) {
                return 'ok';
            } else { return 'Error while Saving'; 
            }
    }
      
    /**
       * Show Final Standings by ajax
       * @author Oleg D.
       */
    function ajaxFinalStandings($eventID) 
    {
    
        $paginate['limit'] = 20;
            $paginate['conditions'] = array('TeamsObject.model_id' => $eventID, 'TeamsObject.model' => 'Event', 'TeamsObject.status' => 'Created', 'TeamsObject.rank > ' => 0);
            $paginate['order'] = array(
                'TeamsObject.rank' => 'ASC',
                'TeamsObject.wins'=>'DESC',
                'TeamsObject.losses'=>'ASC',
                'TeamsObject.cupdif'=>'DESC');
            $paginate['contain'] = array('Team');
            
            $this->paginate = array('TeamsObject' => $paginate);
        $teams = $this->paginate('TeamsObject');
    
        $this->set(compact('teamItems', 'teams', 'eventID'));
        $this->render();             
    }  
      
    /**
       * Show Final Standings
       * @author Oleg D.
       */
    function finalStandings($eventID) 
    {
            
        $event = $this->Event->read(null, $eventID);
              
        $paginate['limit'] = 10000;
            $paginate['conditions'] = array('TeamsObject.model_id' => $eventID, 'TeamsObject.model' => 'Event', 'TeamsObject.status' => 'Created', 'TeamsObject.rank > ' => 0);
            $paginate['order'] = array(
                'TeamsObject.rank' => 'ASC',
                'TeamsObject.wins'=>'DESC',
                'TeamsObject.losses'=>'ASC',
                'TeamsObject.cupdif'=>'DESC');
            $paginate['contain'] = array('Team');
            
            $this->paginate = array('TeamsObject' => $paginate);
        $teams = $this->paginate('TeamsObject');
            
        $this->set(compact('teamItems', 'teams', 'eventID', 'event'));
        $this->render();             
    }     
}
?>
