<?php
/* SVN FILE: $Id: events_controller.php 8063 2011-12-18 19:21:38Z _skinny $ */
/*
 * @version $Revision: 8063 $
 * @modifiedby $LastChangedBy: _skinny $
 * @lastmodified $Date: 2011-12-18 21:21:38
 */
class EventsController extends AppController
{

    var $name = 'Events';
    var $uses    = array('Event','Eventstructure', 'EventView','Manager','Signup','Question','Venue','Provincestate', 'Country', 'EventsEvent', 'Game');
    //var $helpers = array ( 'Html', 'Form', 'Javascript' , 'Address');
    var $helpers = array ( 'Html', 'Form', 'Address');
    //var $components = array('Time','Emails.Mailer', 'RequestHandler');
    var $phonetype = array ( "Cell" => "Cell"
                            ,"Home" => "Home"
                            ,"Other" => "Other"
                            ,"Work" => "Work"
                            );

    function beforeFilter() 
    {
        parent::beforeFilter();
        //$this->Session->setFlash ( 'Page under construction.' );
        //$this->redirect ( "/" );
    }

    function m_getEventsImAssignedTo($limit = 20,$amf = 0) 
    {
        if (isset($this->request->params['form']['limit'])) { $limit = $this->request->params['form']['limit']; 
        }
        if (isset($this->request->params['form']['amf'])) { $amf = $this->request->params['form']['amf']; 
        }
        if (!$this->isLoggined()) {
            return $this->returnMobileResult("You are not logged in.", $amf);
        }
        $user = $this->Session->read('loggedUser');
        if ($user['id']==1) { return $this->returnMobileResult("You are not logged in.", $amf); 
        }

        return $this->m_getEventsUserIsAssignedTo($user['id'], $limit);
    }

    function m_getEventsUserIsAssignedTo($userid,$limit=20,$amf = 0) 
    {
        if (isset($this->request->params['form']['userid'])) { $amf = $this->request->params['form']['userid']; 
        }
        if (isset($this->request->params['form']['limit'])) { $limit = $this->request->params['form']['limit']; 
        }
        if (isset($this->request->params['form']['amf'])) { $amf = $this->request->params['form']['amf']; 
        }
        //First get ids of all teams that im on
        $Teammate = ClassRegistry::init('Teammate');
        $Teammate->recursive = 0;
        $myTeammates = $Teammate->find(
            'all', array(
            'conditions'=>array(
                'Teammate.user_id'=>$userid,
                'Team.is_deleted'=>0),
            'contain'=>array('Team'))
        );
        $myteamids = Set::extract($myTeammates, '{n}.Teammate.team_id');
        
        $TeamsObject = ClassRegistry::init('TeamsObject');
        $TeamsObject->recursive = 0;
        $myTeamObjects = $TeamsObject->find(
            'all', array(
            'conditions'=>array(
            'TeamsObject.status <>'=>'Deleted',
            'Event.is_deleted'=>0,
            'TeamsObject.model'=>'Event',
            'TeamsObject.team_id'=>$myteamids))
        );
        $myEventIDs = Set::extract($myTeamObjects, '{n}.TeamsObject.model_id');
        
        //now get the events
        $this->Event->recursive = 0;
        $myEvents = $this->Event->find(
            'all', array('conditions'=>array('Event.id'=>$myEventIDs),
            'contain'=>array('Venue'))
        );
        //	return $myEvents;
        return $this->returnMobileResult($myEvents, $amf);
    }
    
    /**
     * API Function
     * Get all events of user and return as an array
     * @author skinny
     */
    function getMyEvents_api($limit = 20) 
    {
            Configure::write('debug', 0);  
        // Currenty, this just sends the Venue and EventStructure (this is all we need for this tournament app).
        // Should we consider sending more for the api?
            $userid = $this->getUserID();
        if ($userid < 2) {
            return "You are not logged in.";
        }
            
            $eventIDs = $this->Manager->getModelsIDs($this->getUserID(), 'Event');
            $findArray = array(
                'conditions'=>array(
                        'Event.id'=>$eventIDs,
                        'Event.is_deleted'=>'0'),
                'contain'=>array('Venue','Eventstructure'),
                'order'=>array('Event.id'=>"DESC"));
        if ($limit != 'all') {
            $findArray['limit'] = $limit; 
        }
            
            $events = $this->Event->find('all', $findArray);

            return $events;

    }

    /**
     * API Function
     * This returns an event object.
     * @author skinny
     */
    function getEvent_api($eventID) 
    {
        Configure::write('debug', 0);  
        return $this->Event->find(
            'first', array('contain'=>array('Venue','Eventstructure'),
            'conditions'=>array('Event.id'=>$eventID,'Event.is_deleted'=>0))
        );
    } 
        
    function getEventWithTeamsAndGames_api($eventID) 
    {
        Configure::write('debug', 0);  
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
                'Teammate.team_id'=>$teamsIDs,
                'Teammate.status'=>array('Pending','Accepted'),
                'Team.status <>'=>'Deleted'),
                'contain'=>array('User'=>array('fields'=>array('id','lgn','firstname','lastname','rating')),'Team'))
            );
            $Teams = ClassRegistry::init('Team');
            $Teams->recursive = -1;
            $matchingTeams = $Teams->find(
                'all', array('conditions'=>array(
                'id'=>$teamsIDs,
                'status <>'=>'Deleted'))
            );
            if (empty($matchingTeams)) { $returnTeams = array(); 
            }
            else {
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
                            $teamToAdd['Users'][$teammateCounter]['rating'] = $matchingTeammate['User']['rating'];
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
                    $returnTeams[$ctr] = $teamToAdd;
                    $ctr++;
                    $teamToAdd = null;
                }
            }
            //$this->Event->recursive = 0;
            //$this->Event->id = $eventID;
            $result = $this->Event->find(
                'first', array(
                'conditions'=>array('Event.id'=>$eventID,'Event.is_deleted'=>0),  
                'contain'=>array('Venue','Eventstructure'))
            );
            $GameObject = ClassRegistry::init('Game');
            $GameObject->recursive = -1;
            $games = $GameObject->find(
                'all', array('conditions'=>array(
                'event_id'=>$eventID,
                'status <>'=>'Deleted'))
            );
            $result['Games'] = $games;
            $result['Teams'] = $returnTeams;
            return $result;
    }
    /**
     * API Function
     * This saves the event that is passed as a parameter
     * @authoer skinny
     * @param unknown_type $eventToSave
     */
    function save_api($eventToSave) 
    {
        Configure::write('debug', 0);
        // Check to see if user is logged                          
        $managers = $this->Event->getManagersId($eventToSave['id']);
        if (!$this->Access->getAccess('event', 'u', $managers)) {
            return "You are not logged in.";
        }

        //Check to see that Event exists
        $this->Event->recursive = -1;
        $currentEvent = $this->Event->find(
            'first', array('conditions'=>
              array('id'=>$eventToSave['id']))
        );
        if (!$currentEvent) { return "Event does not exist"; 
        }

        if (isset($eventToSave['structurename'])) {
            $structureForEvent = $this->Event->Eventstructure->find(
                'first', array('conditions'=>array(
                'name'=>$eventToSave['structurename']))
            );
            if (empty($structureForEvent)) {
                return 'Structure not found'; 
            }
            $eventToSave['structure_id'] = $structureForEvent['Eventstructure']['id'];
            unset($eventToSave['structurename']);
        }

        //       $this->Event->id = $eventID;
        unset($newEventData);

        $newEventData['Event'] = $eventToSave;
        if (!$this->Event->save($newEventData)) {
            return "Could not save data.";
        }
        if ($currentEvent['Event']['iscompleted'] != $eventToSave['iscompleted']) {
            Cache::delete('last_event_results');
        }
        $TeamsObject = ClassRegistry::init('TeamsObject');
        $TeamsObject->updateNBPLPointsForEvent($eventToSave['id']);
        
        return "ok";
    }
    /**
     * API Function
     * This removes a team from an event
     * @author skinny
     * @param unknown_type $teamID
     */
    function removeTeamFromEvent_api($eventID, $teamID)   
    {
        Configure::write('debug', 0);  
        if (!$this->Access->getAccess('event', 'u', $managers = $this->Event->getManagersId($eventID))) {
            return 'Access Denied';            
        }
          
        $TeamsObject = ClassRegistry::init('TeamsObject');
        $TeamsObject->recursive = -1;
        $matchingTeamsObjects = $TeamsObject->find(
            'all', array('conditions'=>array(
            'model'=>'Event',
            'model_id'=>$eventID,
            'team_id'=>$teamID,
            'status <>'=>'Deleted',
            ))
        );
        $teamDeleted = false;
        if (!empty($matchingTeamsObjects)) {
            foreach($matchingTeamsObjects as $matchingTeamsObject) {
                $matchingTeamsObject['TeamsObject']['status'] = 'Deleted';
                if (!$TeamsObject->save($matchingTeamsObject)) {
                    return "Problem Removing Team"; 
                }
            }
        }
        //Update the teams statistics
        $Team = ClassRegistry::init('Team');
        $Team->updateStatsForTeam($teamID);
        //$TeamsObject->updateStatsForTeam($teamID);
        return "ok";
    }
    function currentstatus($eventID,$ajax=0) 
    {
        $this->Event->recursive = -1;
        $event = $this->Event->find('first', array('conditions'=>array('id'=>$eventID)));
        if (!$event) {
            $this->Session->setFlash('Invalid Event.', 'flash_error');
            $this->logErr('error occured: Invalid Event.');
            $this->redirect('/');
        }
        $currentRound = $event['Event']['currentround'];

        $startTimeOfNext = strtotime($event['Event']['starttimeofnextround']);
        $currentTime = time();
        $currentDateTime = Date('h:i:s', $currentTime);

        $timeRemaining = $startTimeOfNext - $currentTime;
        $minutesTillNextRound = floor($timeRemaining / 60);
        $secondsTillNextRound = $timeRemaining - ($minutesTillNextRound * 60);
        if ($ajax) {
            return $this->returnJSONResult(
                array(
                'currentRound'=>$currentRound,
                'startTimeOfNextRound'=>$startTimeOfNext,
                'currentDateTime'=>$currentDateTime)
            );
        }
          
        $this->set('event', $event);
        $this->set('currentRound', $currentRound);
        $this->set('startTimeOfNextRound', $startTimeOfNext);
        $this->set('minutesTillNextRound', $minutesTillNextRound);
        $this->set('secondsTillNextRound', $secondsTillNextRound);
        $this->set('currentDateTime', $currentDateTime);

    }

    function setTimeUntilNextRound_api($eventID,$currentRound,$minutesTillNextRound) 
    {
        Configure::write('debug', 0);  
        if (!$this->Access->getAccess('event', 'u', $this->Event->getManagersId($eventID))) {
            return 'Access Denied';            
        }

        $this->Event->recursive -1;
        $event = $this->Event->find(
            'first', array('conditions'=>array(
            'id'=>$eventID,
            'is_deleted'=>0))
        );
        if (!$event) {
            return "Event does not exist"; 
        }

        $event['Event']['currentround'] = $currentRound;
        $event['Event']['starttimeofnextround'] = date('Y-m-d G:i:s', time() + $minutesTillNextRound * 60);

        return $this->Event->save($event);
    }
    /**
     * This removes an array of teams from an event
     * @param $eventID: The id of the event to be removed
     * @param $teamIDS: An array containing the ids of the teams to remove from the event
     */
    function removeTeamsFromEvent_api($eventID,$teamIDS) 
    {
        Configure::write('debug', 0);  
        if (!$this->Access->getAccess('event', 'u', $this->Event->getManagersId($eventID))) {
            return 'Access Denied';            
        }

        $problem = false;
        $TeamsObject = ClassRegistry::init('TeamsObject');
        $TeamsObject->recursive = -1;
        foreach ($teamIDS as $teamID) {
             $matchingTeamsObjects = $TeamsObject->find(
                 'all', array('conditions'=>array(
                 'model'=>'Event',
                 'model_id'=>$eventID,
                 'team_id'=>$teamID,
                 'status <>'=>'Deleted',
                 ))
             );
             $teamDeleted = false;
             if (!empty($matchingTeamsObjects)) {
                 foreach($matchingTeamsObjects as $matchingTeamsObject) {
                     $matchingTeamsObject['TeamsObject']['status'] = 'Deleted';
                     if (!$TeamsObject->save($matchingTeamsObject)) {
                         $problem; 
                        }
                    }
             }
        }
        if ($problem) {
            return "problem"; 
        }
        else {
            return "ok"; 
        }

    }
    /**
     * This assigns a set of teams to an event with the given seed. The requester must be a manager of the event.
     * @param $eventID - The id of the event
     * @param $teamsAndSeeds - An array where each object contains the teamid and the seed
     * If there are no changes in team names, then this just returns "ok". If there are changes, this returns an array where
     * the first item is "ok", and the second item is an array containing team.id, and newTeamName
     */
    function assignTeamsToEvent_api($eventID, $teamsAndSeeds) 
    {
        Configure::write('debug', 0);  
        if (!$this->Access->getAccess('event', 'u', $this->Event->getManagersId($eventID))) {
            return 'Access Denied';            
        }
        
        $Team = ClassRegistry::init('Team');
        $Team->recursive = -1;
        $TeamsObject = ClassRegistry::init('TeamsObject');
         //Check to see if Team is already assigned to Event
        $TeamsObject->recursive = -1;
         $ctr = 0; // This helps us build the returnArray
        foreach ($teamsAndSeeds as $teamAndSeed) {
            $teamID = $teamAndSeed['teamid'];
            $seed = $teamAndSeed['seed'];
            $teamNameToUse = $teamAndSeed['name'];
            //make sure team exists.
            if ($teamID > 0) {
                $currentTeam = $Team->find(
                    'all', array('conditions'=>array(
                    'id'=>$teamID,
                    'status <>'=>'Deleted'))
                );
                if (!empty($currentTeam)) {
                    //is team already in event
                    $isTeamInEvent = $TeamsObject->find(
                        'all', array('conditions'=>array(
                        'model_id'=>$eventID,
                        'team_id'=>$teamID,
                        'model'=>'Event',
                        'status <>'=>'Deleted'
                        ))
                    );
                    if (empty($isTeamInEvent)) {
                        if (isset($teamObject['TeamsObject'])) {
                            unset($teamObject['TeamsObject']); 
                        }
                        // Check to see if there is already a team with the same name
                        $assignedTeamName = $teamNameToUse;
                        $isNameOk = $TeamsObject->find(
                            'all', array('conditions'=>array(
                            'model_id'=>$eventID,
                            'name'=>$assignedTeamName,
                            'model'=>'Event',
                            'status <>'=>'Deleted'))
                        );
                        $teamNameNumber = 1;
                        while (!empty($isNameOk)) {
                            $assignedTeamName = $teamNameToUse.$teamNameNumber;
                            $isNameOk = $TeamsObject->find(
                                'all', array('conditions'=>array(
                                'model_id'=>$eventID,
                                'name'=>$assignedTeamName,
                                'model'=>'Event',
                                'status <>'=>'Deleted'))
                            );
                            $teamNameNumber++;
                        }
                        $returnArray[$ctr] = array($currentTeam[0]['Team']['id'],$assignedTeamName);
                        $ctr++;
                        $teamObject['TeamsObject']['id']         = null;
                        $teamObject['TeamsObject']['model']         = 'Event';
                        $teamObject['TeamsObject']['model_id']     = $eventID;
                        $teamObject['TeamsObject']['assigner_id']  = $user['id'];
                        $teamObject['TeamsObject']['name']          = $assignedTeamName;
                        $teamObject['TeamsObject']['status']          = 'Created';
                        $teamObject['TeamsObject']['team_id']       = $teamID;
                        $teamObject['TeamsObject']['seed']          = $seed;
                        if(!$TeamsObject->save($teamObject)) {
                            return "Error while Storing Information";
                        }
                    }
                }
            }
        }
         return array("ok",$returnArray);
    }
    /**
     * This assigns a team to an event with a certain seed. The requested must be a manager of the event.
     *
     * @param int    $eventID
     * @param int    $teamID
     * @param int    $seed
     * @param string $newName - If $newName<>"", this is the team name that will be assigned.
     *
     * If there is already a team in this event that has the same name as whatever team we're trying to add, this returns an array where
     * the first item is the message 'ok', the second item is the bpong id, and the third item is the new team name;
     */
    function assignTeamToEvent_api($eventID = null, $teamID = null, $seed = 0,$newName = '') 
    {
         Configure::write('debug', 0);  
        if (!$this->Access->getAccess('event', 'u', $this->Event->getManagersId($eventID))) {
            return 'Access Denied';            
        }
         //make sure team exists. If team_id < 0, don't bother because this is a dummy team
        if ($teamID > -1) {
            $Team = ClassRegistry::init('Team');
            $Team->recursive = -1;
            $currentTeam = $Team->find('all', array('conditions'=>array('id'=>$teamID)));
            if (empty($currentTeam)) { return 'Team Does Not Exist'; 
            }

            //check to see if the team has been deleted
            if ($currentTeam[0]['Team']['status'] == 'Deleted') { return 'Team has been deleted'; 
            }
        }
         $TeamsObject = ClassRegistry::init('TeamsObject');
         //Check to see if Team is already assigned to Event
         $TeamsObject->recursive = -1;

         $isTeamInEvent = $TeamsObject->find(
             'all', array('conditions'=>array(
             'model_id'=>$eventID,
             'team_id'=>$teamID,
             'model'=>'Event',
             'status <>'=>'Deleted'
             ))
         );
         if (!empty($isTeamInEvent)) { return "Team already assigned to event"; 
         }
            $user = $this->Session->read('loggedUser');
            // Now, we need to make sure there is not already another team using the same name in this event

            if (strlen($newName) == 0) {
                $requestedTeamName = $currentTeam[0]['Team']['name']; 
            }
            else {
                $requestedTeamName = $newName; 
            }
            $nameToTry = $requestedTeamName;
            $unfreename = $TeamsObject->find(
                'all', array('conditions'=>array(
                    'model_id'=>$eventID,
                    'name'=>$nameToTry,
                    'model'=>'Event',
                    'status <>'=>'Deleted'))
            );
            $ctr = 1;
            while (!empty($unfreename)) {
                $nameToTry = $requestedTeamName.$ctr;
                $unfreename = $TeamsObject->find(
                    'all', array('conditions'=>array(
                    'model_id'=>$eventID,
                    'name'=>$nameToTry,
                    'model'=>'Event',
                    'status <>'=>'Deleted'))
                );
                $ctr++;
            }
            $teamObject['TeamsObject']['id']         = null;
            $teamObject['TeamsObject']['model']         = 'Event';
            $teamObject['TeamsObject']['model_id']     = $eventID;
            $teamObject['TeamsObject']['assigner_id']  = $user['id'];
            $teamObject['TeamsObject']['name']          = $nameToTry;
            $teamObject['TeamsObject']['status']          = 'Created';
            $teamObject['TeamsObject']['team_id']       = $teamID;
            $teamObject['TeamsObject']['seed']          = $seed;

            if($TeamsObject->save($teamObject)) {
                  return array("ok",$teamID,$nameToTry);
            } else {
                  return "Error while Storing Information";
            }
    }
     /**
     * Show user created event
     * @author vovich
     */
    function my() 
    {
        //$this->Access->checkAccess('event','l');
        $userId = $this->Access->getLoggedUserID();

        $eventPaginate = array();
        $eventPaginate['conditions'] = array('Event.is_deleted'=>0,'Manager.user_id'=>$userId,'Manager.model' => "Event");
        $eventPaginate['contain']      = array('Event','reset'=>false);
        $eventPaginate['order'] = array('Event.id' => 'desc');
        $this->paginate = array('Manager' => $eventPaginate);
        $events = $this->paginate('Manager');

        $this->set('accessApprove', $this->Access->getAccess('ApproveEvent', 'c'));
        $this->set('events', $events);
    }

    /**
     * Handles data from search form
     * @author alekz
     */
    function search() 
    {
        // the page we will redirect to
        $url = $this->request->data['EventView']['url'];
        unset($this->request->data['EventView']['url']);
        if(isset($this->request->data['EventView'])) {
            foreach ($this->request->data['EventView'] as $key => $param){
                if (!empty($param)) {
                    $url.='/'. $key . ':'. $param;
                }
            }
        }
        //// redirect the user to the url
        return $this->redirect($url);
    }


    /**
     * Show all events (for management)
     * @author vovich
     */
    function index($eventsType = 'all') 
    {
        $this->helpers [] = 'Newpaginator';

        // GOOGlE MAPS MARKERS
        $basicConditions = array('EventView.is_deleted' => 0);
        $basicConditions = $basicConditions + $this->Event->typesConditions($eventsType);

        $markers = $this->EventView->getMapMarkers(date('Y-m-d'), $basicConditions, $this->passedArgs);
        //pr($markers);
        // EOF GOOGlE MAPS MARKERS

        $url = $this->passedArgs;
        $url['controller'] = 'events';
        $url['action'] = 'events_list/' . $eventsType;
        $eventList = $this->requestAction(Router::url($url), array('return'));
        $this->set('markers', $markers);

        $states = $this->Provincestate->find('list', array('fields' => array('id', 'name'), 'conditions' => array('AND' => array('country_id' => 1), 'NOT' => array('shortname' => array('AA', 'AE', 'AP'))), 'order' => array('name' => 'ASC')));

        $this->set('states', $states);
        $this->set('eventList', $eventList);
        $this->set('eventsType', $eventsType);
        $this->set('menu_detect_var', 'events_' . $eventsType);
    }

    /**
     * Show list of all events
     * @author alekz
     */
    function events_list($eventsType) 
    {
        $eventPaginate['conditions'] = array('EventView.is_deleted' => 0 /* ,'EventView.is_approved'=>1 */);
        if (!empty($this->passedArgs['name'])) {
            $this->request->data['EventView']['name'] = $this->passedArgs['name'] = Sanitize::escape($this->passedArgs['name']);
            $eventPaginate['conditions']['EventView.name LIKE'] = '%' . $this->passedArgs['name'] . '%';
        }
        if (!empty($this->passedArgs['date'])) {
            $this->request->data['EventView']['date'] = $this->passedArgs['date'] = Sanitize::escape($this->passedArgs['date']);
            $eventPaginate['conditions']['DATE(EventView.start_date) <='] = $this->passedArgs['date'];
            $eventPaginate['conditions']['DATE(EventView.end_date) >='] = $this->passedArgs['date'];
        }
        if (!empty($this->passedArgs['state_id'])) {
            $this->request->data['EventView']['state_id'] = $this->passedArgs['state_id'] = Sanitize::escape($this->passedArgs['state_id']);
            $eventPaginate['conditions']['Venue.provincestate_id'] = $this->passedArgs['state_id'];
        }

        if (!empty($this->passedArgs['lgn'])) {
            $data['EventView']['lgn'] = $this->passedArgs['lgn'] = Sanitize::escape($this->passedArgs['lgn']);
            $users = $this->EventView->User->find(
                'all', array(
                'conditions' => array(
                'lgn' => $this->passedArgs['lgn']
                ),
                'recursive' => -1
                )
            );
            $userIds = Set::classicExtract($users, '{n}.User.id');
            $eventPaginate['conditions']['EventView.user_id'] = $userIds;
        }

        if (empty($this->passedArgs['past_events'])) {
            $eventPaginate['conditions']['DATE(EventView.end_date) >='] = date("Y-m-d");
            $eventPaginate['order'] = array('EventView.start_date' => "ASC");
        } else {
            $eventPaginate['order'] = array('EventView.start_date' => "DESC");
        }
        $eventPaginate['fields'] = array('DISTINCT(EventView.id)', 'EventView.*', 'Venue.*');
        $eventPaginate['contain'] = array('Venue', 'EventSatellite');
        $eventPaginate['limit'] = 10;
        $eventPaginate['extra']['count_fields'] = 'DISTINCT(EventView.id)';
        $eventPaginate['extra']['count_contains'] = array('Venue', 'EventSatellite');
        $eventPaginate['conditions'] = $eventPaginate['conditions'] + $this->Event->typesConditions($eventsType);

        //pr($eventPaginate);
        $this->paginate = array('EventView' => $eventPaginate);
        $events = $this->paginate('EventView');
        //pr($events);
        //exit;

        App::import('Model', 'Comment');
        $Comment = new Comment();
        $userId = $this->Access->getLoggedUserID();
        $eventsIds = Set::extract($events, '/EventView/id');
        $this->set('votes', $Comment->Vote->getVotes('Event', $eventsIds, $userId));
        //security
        //pr($events);
        $this->set('canVote', $this->Access->returnAccess('Vote_Event', 'c'));
        $this->set('events', $events);
        $this->set('eventsType', $eventsType);

    }

    /**
     * Show all events (for management)
     * @author vovich
     */
    function all() 
    {
        $this->Access->checkAccess('ApproveEvent', 'c');
        $this->Event->recursive = -1;
        $eventPaginate['conditions'] = array();

        /* filter Getting data from the session or from the form*/
        if(!empty($this->request->data['EventFilter'])) {
            $this->Session->write('EventFilter', $this->request->data['EventFilter']);
        }elseif($this->Session->check('EventFilter')) {
            $this->request->data['EventFilter']=$this->Session->read('EventFilter');
        }

        //Prepare data for the filter
        if (!empty( $this->request->data['EventFilter']['name'])) {
            $eventPaginate['conditions']['Event.name LIKE'] = "%".$this->request->data['EventFilter']['name']."%";
             
        }
        //Prepare data for the filter
        if (!empty( $this->request->data['EventFilter']['venueName'])) {
            $eventPaginate['conditions']['Venue.name LIKE'] = "%".$this->request->data['EventFilter']['venueName']."%";
             
        }    
        
        $eventPaginate['conditions']['Event.is_deleted'] = 0;
        $eventPaginate['order'] = array('Event.id' => 'desc');
        $eventPaginate['contain'] = array('Venue');

        $this->paginate = array('Event' => $eventPaginate);
        $this->set('events', $this->paginate('Event'));
    }
    /**
     * Show all NBPL events (for management)
     * @author vovich
     */
    function allnbplweeklies($onlyPastIncomplete = 0) 
    {
        $this->Access->checkAccess('ApproveEvent', 'c');
        $this->Event->recursive = -1;
        $eventPaginate['conditions'] = array();

        /* filter Getting data from the session or from the form*/
        if(!empty($this->request->data['EventFilter'])) {
            $this->Session->write('EventFilter', $this->request->data['EventFilter']);
        }elseif($this->Session->check('EventFilter')) {
            $this->request->data['EventFilter']=$this->Session->read('EventFilter');
        }

        //Prepare data for the filter
        if (!empty( $this->request->data['EventFilter']['name'])) {
            $eventPaginate['conditions']['Event.name LIKE'] = "%".$this->request->data['EventFilter']['name']."%";
             
        }
        //Prepare data for the filter
        if (!empty( $this->request->data['EventFilter']['venueName'])) {
            $eventPaginate['conditions']['Venue.name LIKE'] = "%".$this->request->data['EventFilter']['venueName']."%";
        }
        if (!empty( $this->request->data['EventFilter']['show_only_past']) && $this->request->data['EventFilter']['show_only_past'] == 1) {
            $eventPaginate['conditions']['Event.start_date <'] = date('Y-m-d');
        }    
        if ($onlyPastIncomplete) {
            $eventPaginate['conditions']['Event.start_date <'] = date('Y-m-d');
            $eventPaginate['conditions']['Event.iscompleted'] = 0;
        }
        if (!empty( $this->request->data['EventFilter']['hide_completed']) && $this->request->data['EventFilter']['hide_completed'] == 1) {
            $eventPaginate['conditions']['iscompleted'] = 0;
        }
        /*if (!empty( $this->request->data['EventFilter']['venueName']))    {
        $eventPaginate['conditions']['Venue.name LIKE'] = "%".$this->request->data['EventFilter']['venueName']."%";
        } */       
        $eventPaginate['conditions']['Event.type']= 'nbplweekly';
        $eventPaginate['conditions']['Event.is_deleted'] = 0;
        $eventPaginate['order'] = array('Event.id' => 'desc');
        $eventPaginate['contain'] = array('Venue');

        $this->paginate = array('Event' => $eventPaginate);
        $this->set('events', $this->paginate('Event'));
    }
    /**
     * Show all completed events
     */
    function allcompleted($showOnlyGamesThatHaveBeenMarked = 0) 
    {
        $this->Access->checkAccess('ApproveEvent', 'c');
        $this->Event->recursive = -1;
        $eventPaginate['conditions'] = array('iscompleted'=>1,
                                        'is_deleted'=>0);
        if ($showOnlyGamesThatHaveBeenMarked) {
            $eventPaginate['conditions']['emailssent'] = 0; 
        }
        $eventPaginate['order'] = array('end_date' => 'desc');

        $this->paginate = array('Event' => $eventPaginate);
        $this->set('events', $this->paginate('Event'));
    }

    /**
     * Fetches all events in JSON (for calendar)
     * @author alekz
     */
    function json_events($eventsType) 
    {
        $this->request->data['start'] = date('Y-m-d H:i:s', $_GET['start']);
        $this->request->data['end'] = date('Y-m-d H:i:s', $_GET['end']);
        $this->Event->recursive = -1;
        $this->autoRender = false;
        if ($this->RequestHandler->isAjax()) {
            Configure::write('debug', 0);
        }

        $events = $this->Event->find(
            'all', array(
            'fields' => array('id', 'name AS title', 'slug', 'start_date AS start', 'end_date AS end'),
            'contain' => array('EventSatellite'),
            'conditions' => array(
            'OR' => array(
            'Event.start_date BETWEEN ? AND ?' => array($this->request->data['start'], $this->request->data['end']),
            'Event.end_date BETWEEN ? AND ?' => array($this->request->data['start'], $this->request->data['end'])
            ),
            'Event.is_deleted' => 0
            , $this->Event->typesConditions($eventsType)
            )
            )
        );

        $events = Set::classicExtract($events, '{n}.Event');
        foreach ($events as &$event) {
            $event['url'] = Router::url(array('controller' => 'events', 'action' => 'view', $event['slug']));
            unset($event['slug']);
        }
        $events = json_encode($events);
        echo $events;
    }

    function sharethis_frame($shareTitle = '', $shareUrl = '') 
    {
        $this->layout = false;
        $this->set(compact('shareTitle', 'shareUrl'));
    }

    /**
     * Autocompleter for event list search form
     *
     * @param  string $fieldName - name or lgn - searchable fields
     * @return string - |\n-separated autocomplete result
     */
    function autocomplete($fieldName = 'name') 
    {
        if (!in_array($fieldName, array('name', 'lgn'))) {
            $this->Session->setFlash('Invalid address', 'flash_error');
            return $this->redirect('/');
        }
        $this->request->data[$fieldName] = Sanitize::paranoid($_GET['q']);
        $this->request->data['limit'] = Sanitize::paranoid($_GET['limit']);

        if (!$this->RequestHandler->isAjax()) {
            $this->Session->setFlash('Invalid address', 'flash_error');
            return $this->redirect('/');
        }
        Configure::write('debug', 0);
        $this->autoRender = false;

        if ($fieldName == 'lgn') {
            $users = $this->Event->query(
                "
		SELECT `lgn` FROM `users` AS `User`
		INNER JOIN `managers` AS `Owner` ON `Owner`.`user_id` = `User`.`id`
		WHERE `Owner`.`model` = 'Event'
		AND `User`.`lgn` LIKE '%{$this->request->data[$fieldName]}%'
		GROUP BY `User`.`id`"
            );
            $result = Set::classicExtract($users, "{n}.User.{$fieldName}");
        } else {
            $events = $this->Event->find(
                'all', array(
                'fields' => array($fieldName),
                'conditions' => array("{$fieldName} LIKE" => '%' . $this->request->data[$fieldName] . '%'),
                'limit' => $this->request->data['limit']
                )
            );

            $result = Set::classicExtract($events, "{n}.Event.{$fieldName}");
        }
        echo implode("|\n", $result);
    }

    /**
     * Show  event
     * @author vovich
     * @paramchar $slug
     */
    function view($id, $slug = null) 
    {
        if (!is_numeric($id)) {
            $id = $this->Event->field('id', array('slug' => $id, 'is_deleted' => 0));
        }

        if ($this->isLoggined()) {
             $userSession = $this->Session->read('loggedUser');
        }

        $this->Access->checkAccess('event', 'r');
        $canAssign2Event = $this->__canAssign2Event();
        /*Check if user already assigned to the view*/
        //$isAssigned = $this->Event->isUserAssigned($userSession['id'],$id);

        if (!$id) {
            $this->Session->setFlash('Invalid Event.', 'flash_error');
            $this->logErr('error occured: Invalid Event.');
            return $this->redirect('/');
        }
        $this->Event->recursive = 1;
        $event = $this->Event->find(
            'first', array(
            'conditions' => array('Event.id' => $id, 'Event.is_deleted' => 0),
            'contain' => array('Tag', 'Venue', 'Venue.Phone', 'Venue.Address', 'Venue.Address.Provincestate', 'Venue.Address.Country', 'Eventfeature', 'Image', 'Timezone'),
            )
        );
        if (empty($event['Event']['id'])) {
            $this->Session->setFlash('Incorrect events name', 'flash_error');
            return $this->redirect('/');
        }

        $images=$this->Event->Image->myImages('Event', $id, 'Personal');
        $this->set('images', $images);
        if(count($images)) {
            $rels_images = array();
            foreach($images as $image) {
                $rels_images[] = MAIN_SERVER."/img/Event/".$image['Image']['filename'] ;
            }
            $this->set('rels_images', $rels_images);
        }
        //managers id's for the checking acces
        $event_managers = $this->Event->getManagersId($id);
        $canEditEvent = $this->Access->getAccess('event', 'u', $event_managers);

        $this->pageTitle =$event['Event']['name'];
        //For the facebook share
        $description  = "What: ".strip_tags($event['Event']['name'])."; ";
        $description .= "When: ".$this->Time->niceDate($event['Event']['start_date']).";";
        if (!empty ($event['Venue']['Address'])) {
            $description .=" Where: ".strip_tags($event['Venue']['Address']['address']).", ".strip_tags($event['Venue']['Address']['city']).", ".@strip_tags($event['Venue']['Address']['Provincestate']['name']);
        }


        $this->set("meta_description", $description);

        $this->EventsEvent->contain('Event', 'Parent');

        //		$isFinishedEvent = 0;
        //		if (strtotime(date('Y-m-d')) > strtotime($event['Event']['end_date'])) {
        //			$isFinishedEvent = 1;
        //		}
        $isFinishedEvent = $event['Event']['iscompleted'];

        $GameObject = ClassRegistry::init('Game');
        // $gamesCount == Total # of Completed Games
        $gamesCount = $GameObject->find('count', array('conditions' => array('Game.event_id' => $id, 'Game.status' => 'Completed')));
        // $totalGamesCount == Total # of Games (includes 'Playing'/'Ready'/etc

        $totalGamesCount = $GameObject->find('count', array('conditions'=>array('Game.event_id'=>$id,'Game.status <> ' => 'Deleted')));

        $this->set('isFinishedEvent', $isFinishedEvent);
        $this->set('gamesCount', $gamesCount);
        $this->set('totalGamesCount', $totalGamesCount);
        //EOF For the facebook share
        $this->set('event', $event);
        $this->set('canAssign2Event', $canAssign2Event);
        $this->set('canEditEvent', $canEditEvent);
        $this->set('canAddEvent', $this->Access->getAccess('event', 'c'));

        /*Votes and comments*/
        App::import('Model', 'Comment');
        $Comment = new Comment();


        $OrganizationsObject = ClassRegistry::init('OrganizationsObject');
        $organizations = $OrganizationsObject->find('all', array('conditions' => array('model_id' => $id, 'model' => 'Event'), 'contain' => array('Organization' => array('Image'))));

        //pr($organizations);
        //exit;


        $userId = $this->Access->getLoggedUserID();
        $this->set('comments', $Comment->getCommentsTree('Event', $event['Event']['id']));
        $this->set('commentVotes', $Comment->Vote->getCommentVotes('Event', $event['Event']['id'], $userId));
        $this->set('votes', $Comment->Vote->getVotes('Event', $event['Event']['id'], $userId));
        $this->set('organizations', $organizations);

        $this->set('canVoteBlogpost', $this->Access->returnAccess('Vote_Event', 'c'));
        $this->set('canVoteComment', $this->Access->returnAccess('Vote_Comment', 'c'));
        $this->set('canComment', $this->Access->returnAccess('Comment_Event', 'c'));
        $this->set('canDeleteComment', $this->Access->returnAccess('Comment', 'd'));
        $this->set('managers', $this->Event->getManagersId($event['Event']['id']));
        unset($Comment);
    }
    /**
      * Add New Event through API
      */
    function addEvent_api($newEvent) 
    {
        Configure::write('debug', 0);  
        $newEvent['api'] = 1;
        $userId = $this->Access->getLoggedUserID();
        if (!($userId > 1)) {
            return "You are not logged in."; 
        }
        if (!$this->Access->getAccess('event', 'c')) { return "You are not logged in."; 
        }

        $newEvent['user_id'] = $userId;
        if (isset($newEvent['structurename'])) {
            $structureForEvent = $this->Event->Eventstructure->find(
                'first', array('conditions'=>array(
                'name'=>$newEvent['structurename']))
            );
            if (empty($structureForEvent)) {
                return 'Structure not found'; 
            }
            $newEvent['structure_id'] = $structureForEvent['Eventstructure']['id'];
            unset($newEvent['structurename']);
        }
        unset($newEvent['id']);
        $eventToSave['Event'] = $newEvent;
        if (!$this->Event->create()) { return "Could not create event."; 
        }
        if (!$this->Event->save($eventToSave)) { return array('error'=>$eventToSave); 
        }
        $lastID = $this->Event->getLastInsertID();
        $this->Manager->createManager("Event", $lastID, $userId);

        return $this->getEvent_api($lastID);
    }

    /**
     * Add new Event
     * @author vovich
     */
    function add($model = 0, $modelID = 0) 
    {
        $this->Access->checkAccess('event', 'c');
        $userId = $this->Access->getLoggedUserID();
        //$err = false;

        if (!empty($this->request->data)) {
            $this->request->data['Event']['start_date'] = $this->Time->calendarToSql($this->request->data['Event']['start_date_']) .
            " " . $this->Time->timeToSql($this->request->data['Event']['start_time']['hour'], $this->request->data['Event']['start_time']['min'], $this->request->data['Event']['start_time']['meridian']);

            $this->request->data['Event']['end_date'] = $this->Time->calendarToSql($this->request->data['Event']['end_date_']) .
            " " .
            $this->Time->timeToSql($this->request->data['Event']['end_time']['hour'], $this->request->data['Event']['end_time']['min'], $this->request->data['Event']['end_time']['meridian']);

            if (!empty($this->request->data['Event']['finish_signup_date_'])) {
                $this->request->data['Event']['finish_signup_date'] = $this->Time->calendarToSql($this->request->data['Event']['finish_signup_date_']) .
                " " . $this->Time->timeToSql($this->request->data['Event']['finish_signup_time']['hour'], $this->request->data['Event']['finish_signup_time']['min'], $this->request->data['Event']['finish_signup_time']['meridian']);
            }

            $this->request->data['Event']['user_id'] = $this->getUserID();
            if ($this->Event->storeEvent($this->request->data)) {
                $lastID = $this->Event->getLastInsertID();


                if (!empty($model) && !empty($modelID)) {
                    if ($model == 'Organization') {
                        $OrganizationsObject = ClassRegistry::init('OrganizationsObject');
                        $OrganizationsObject->create();
                        $OrganizationsObject->save(array('model' => 'Event', 'model_id' => $lastID, 'organization_id' => $modelID, 'user_id' => $this->getUserID()));
                    }
                }


                if (!empty($this->request->data['Event']['satellite_request']) && $this->request->data['Event']['satellite_request']) {
                    if ($this->request->data['Event']['satellite_request']) {
                        $user = $this->Session->read('loggedUser');
                        $this->sendMailMessage(
                            'EventSatelliteRequest', array(
                            '{USER_EMAIL}'       => $user['email'],
                            '{EVENT_LINK}'        => MAIN_SERVER.'/event/'.$lastID,
                            '{EVENT_ID}'              => $lastID
                            ), TOURNAMENTS_EMAIL
                        );
                    }
                }

                //Assign new manager to the Event
                $this->Manager->createManager("Event", $lastID, $userId);

                /* Store Venue */
                if (!empty($this->request->data['Venue']['venueUse'])) {
                    if ($this->request->data['Venue']['venueUse'] == "use") {
                        if (empty($this->request->data['Venue']['id'])) {
                            exit("Venue name is empty");
                        } else {
                            $data['id'] = $lastID;
                            $data['venue_id'] = $this->request->data['Venue']['id'];
                            $this->Event->save($data, false, array('venue_id'));
                        }
                    } else {
                        /* Create new venue */
                        $venueId = $this->Event->Venue->storeVenue($this->request->data['Venue']);
                        /* Update EVENT venue_id */
                        if ($venueId) {
                            $data['id'] = $lastID;
                            $data['venue_id'] = $venueId;
                            $this->Event->save($data, false);
                        }
                    }
                }
                /* EOF VEnue Creation */
                /* Sending email */
                $this->request->data['approveLink'] = '<a href="' . MAIN_SERVER . '/events/approve/' . $lastID . '">' . MAIN_SERVER . '/events/approve/' . $lastID . '</a>';
                $this->request->data['editLink'] = '<a href="' . MAIN_SERVER . '/events/edit/' . $lastID . '">' . MAIN_SERVER . '/events/edit/' . $lastID . '</a>';

                /*
                $this->Mailer->prepare('NewEvent', array(
                'to' => $this->Event->User->getAdmins(),
                'data' => $this->request->data
                ));
                $this->Mailer->send();
                */

                /* Sending Email */
                Cache::delete('turnament');
                Cache::delete('markers');
                Cache::delete('last_events');

                $this->Session->setFlash('The Event has been saved', 'flash_success');
                return $this->redirect('/events/edit/' . $lastID . '#tab-5');
            }
        }

        $this->set('accessApprove', $this->Access->getAccess('ApproveEvent', 'c'));
        $this->set('images', array());
        $this->set('action', 'add');

        $this->set('model', $model);
        $this->set('modelID', $modelID);

        $this->__initVars();
        $this->noCache();
    }

    /**
     * Init variables for the view
     * @return unknown_type
     */
    function __initVars()
    {
        $eventfeatures = $this->Event->Eventfeature->find('list');
        $this->set('buttonlist', $this->Access->getAccess('eventViewAll', 'l'));
        $this->set('phonetype', $this->phonetype);
        $this->set('venueactivities', $this->Venue->Venueactivity->find('list'));
        $venuetypes       = $this->Venue->Venuetype->find('list');
        $countries_states = $this->Venue->Address->setCountryStates();
        $this->set('countries', $countries_states['countries']);
        $this->set('states', $countries_states['states']);
        $timeZones = $this->Event->Timezone->find('list');
        $this->set(compact('eventfeatures', 'timeZones', 'venuetypes'));

    }
    /**
     * Edit Event by owner or other group
     * @author vovich
     * @param int $id
     */
    function edit($id = null, $tab = null) 
    {
        $userID = $this->Access->getLoggedUserID();
        //managers id's for the checking acces
        $event_managers = $this->Event->getManagersId($id);
        $this->Access->checkAccess('event', 'u', $event_managers);

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash('Invalid Event', 'flash_error');
            $this->logErr('error occured: Invalid Event.');
            return $this->redirect(array('action'=>'index'));
        }
        if (!empty($this->request->data)) {
            /* Store Venue */
            if (!empty($this->request->data['Venue']['venueUse'])) {
                if ($this->request->data['Venue']['venueUse'] == "use") {
                    if (empty($this->request->data['Venue']['id'])) {
                        exit("Venue name is empty");
                    } else {
                        $data['id'] = $id;
                        $data['venue_id'] = $this->request->data['Venue']['id'];
                        $this->Event->save($data, false);
                    }
                } else {
                    /* Create new venue */
                    $venueId = $this->Event->Venue->storeVenue($this->request->data['Venue']);
                    /* Update EVENT venue_id */
                    if ($venueId) {
                        $data['id'] = $id;
                        $data['venue_id'] = $venueId;
                        $this->Event->save($data, false);
                    }
                }
            }            
    
            
            
            if (!empty($this->request->data['Event']['start_date_']) && !empty($this->request->data['Event']['start_time'])) {
                $this->request->data['Event']['start_date'] = $this->Time->calendarToSql($this->request->data['Event']['start_date_']).
                                                     " ".
                                                     $this->Time->timeToSql($this->request->data['Event']['start_time']['hour'], $this->request->data['Event']['start_time']['min'], $this->request->data['Event']['start_time']['meridian']);
            }

            if (!empty($this->request->data['Event']['end_date_']) && !empty($this->request->data['Event']['end_time'])) {
                $this->request->data['Event']['end_date'] = $this->Time->calendarToSql($this->request->data['Event']['end_date_']).
                                                     " ".
                                                     $this->Time->timeToSql($this->request->data['Event']['end_time']['hour'], $this->request->data['Event']['end_time']['min'], $this->request->data['Event']['end_time']['meridian']);
            }

            if (!empty($this->request->data['Event']['finish_signup_date_'])) {
                $this->request->data['Event']['finish_signup_date'] = $this->Time->calendarToSql($this->request->data['Event']['finish_signup_date_']) .
                " " . $this->Time->timeToSql($this->request->data['Event']['finish_signup_time']['hour'], $this->request->data['Event']['finish_signup_time']['min'], $this->request->data['Event']['finish_signup_time']['meridian']);
            }


            if (!empty($this->request->data['Event']['start_date']) && !empty($this->request->data['Event']['end_date']) && ($this->request->data['Event']['start_date'] > $this->request->data['Event']['end_date']) && ($this->request->data['Event']['end_date_'] != '') && (substr($this->request->data['Event']['end_date'], 0, 10) != '0000-00-00')) {
                $this->Session->setFlash('End date must be later than start date', 'flash_error');
            } else {
                if ($this->Event->storeEvent($this->request->data)) {
                    Cache::delete('turnament');
                       Cache::delete('markers');
                       Cache::delete('last_events');
                    $this->updateTtournamentMenu();
                    //EOF storing
                    $this->Session->setFlash('The Event has been saved', 'flash_success');
                    return $this->redirect(array('action'=>'my'));
                } else {
                    $this->Session->setFlash('The Event could not be saved. Please try again.', 'flash_error');
                    $this->logErr('error occured The Event could not be saved. Please try again.');
                }
            }
        }
        /*INIT FOR NEW EVENT*/
        if (empty($this->request->data)) {
            $this->Event->recursive = 2;
            $this->request->data = $this->Event->find('first', array('conditions'=>array('Event.id'=>$id),'contain'=>array('Tag', 'Venue.Address','Eventfeature','User')));
            //Check to see if event id is bogus
            if (!$this->request->data) {
                $this->Session->setFlash('Invalid Event ID', 'flash_error');
                return $this->redirect('/');
            }


            $this->request->data['Event']['old_shown_on_front'] = $this->request->data['Event']['shown_on_front'];

            if (!empty($this->request->data['Venue']['Address'])) {
                $this->request->data['Venue']['Address']['country_name'] = $this->Country->field('name', 'id='.$this->request->data['Venue']['Address']['country_id']);
                $this->request->data['Venue']['Address']['state_name']   = $this->Provincestate->field('name', 'id='.$this->request->data['Venue']['Address']['provincestate_id']);
            }

            $eventTimezone = $this->Event->Timezone->read('value', $this->request->data['Event']['timezone_id']);

            //			$startDateStamp = strtotime($this->request->data['Event']['start_date']);
            //			$endDateStamp = strtotime($this->request->data['Event']['end_date']);
            //			$startDateStamp = $this->Time->convert($startDateStamp, $eventTimezone['Timezone']['value']);
            //			$endDateStamp = $this->Time->convert($endDateStamp, $eventTimezone['Timezone']['value']);
            //			$this->request->data['Event']['start_date'] = date('Y-m-d H:i:s', $startDateStamp);
            //			$this->request->data['Event']['end_date'] = date('Y-m-d H:i:s', $endDateStamp);

            list($start_date,$start_time) = explode(' ', $this->request->data['Event']['start_date']);
            list($end_date,$end_time)    = explode(' ', $this->request->data['Event']['end_date']);

            $this->request->data['Event']['start_date_'] = $this->Time->sqlToCalendar($start_date);
            $this->request->data['Event']['end_date_']   = $this->Time->sqlToCalendar($end_date);

            if (!empty($this->request->data['Event']['finish_signup_date'])) {
                list($finish_date,$finish_time) = explode(' ', $this->request->data['Event']['finish_signup_date']);
                $this->request->data['Event']['finish_signup_date_'] = $this->Time->sqlToCalendar($finish_date);
                $this->request->data['Event']['finish_signup_time']  = $finish_time;
            }

            $this->request->data['Event']['start_time']  = $start_time;
            $this->request->data['Event']['end_time']    = $end_time;
        }


        $relationshipAccess = $this->Access->getAccess('EventRelationship', 'c');
        if ($relationshipAccess) {
            $this->EventsEvent->contain(array('Parent'));
            $events = $this->EventsEvent->find('all', array('conditions' => array('event_id' => $id)));
            $this->set('events', $events);
        }
        $this->set('relationshipAccess', $relationshipAccess);

        $this->set('accessApprove', $this->Access->getAccess('ApproveEvent', 'c'));
        $this->set('userID', $userID);
        $this->set('tab', $tab);
        $this->set('buttonlist', $this->Access->getAccess('eventViewAll', 'l'));
        $this->set('offimage', $this->Event->Image->myImages('Event', $id, 'Personal'));
        $this->__initVars();
        if (empty($this->request->data['Tag'])) {
            $this->request->data['Tag'] = array();
        }
        $this->noCache();
        $this->set('action', 'edit');
    }
    /**
     * Delete Event
     * @author vovich
     * @param int $id
     */
    function delete($id = null) 
    {
        $event_managers = $this->Event->getManagersId($id);
        $this->Access->checkAccess('event', 'd', $event_managers);
        if (!$id) {
            $this->Session->setFlash('Invalid id for Event', 'flash_error');
            $this->logErr('error occured: Invalid Event.');
            $this->goBack('/events/all');
        }
        if ($this->Game->find('count', array('conditions' => array('event_id' => $id, 'status <>' => 'Deleted')))) {
            $this->Session->setFlash('This Event has games associated with it, and therefore can not be deleted.', 'flash_error');
            $this->goBack('/events/all');
            exit;
        }
        $this->request->data['Event']['id']         = $id;
        $this->request->data['Event']['is_deleted'] = 1;
        $this->request->data['Event']['deleted']    = date('Y-m-d H:i:s');

        if ($this->Event->save($this->request->data, false)) {
            Cache::delete('turnament');
            Cache::delete('markers');
            Cache::delete('last_events');

            $this->Session->setFlash('Event deleted', 'flash_success');
        }
        $this->goBack('/events/all');
    }

    /**
     * Check if Current User can assign to this event, in future will be rules
     * @author vovich
     */
    function __canAssign2Event()
    {
        $canAssign2Event = false;
        $canAssign2Event = $this->Access->getAccess('canAssign2Even', 'r');
        return $canAssign2Event;
    }

    /**
     * assign to the event for the logged user
     *
     * @param int $evenID
     */
    function assign($eventID=null)
    {
        $userSession = array();
        if (!$eventID) {
            $this->logErr('error occured: Invalid Event.');
            return $this->redirect("/event/".$eventID);
        }
        if ($this->isLoggined()) {
             $userSession = $this->Session->read('loggedUser');
        }
        if ($userSession['id']!=VISITOR_USER) {
            $this->Event->query("INSERT INTO events_users (event_id,user_id,registered) VALUES(".$eventID.",".$userSession['id'].",NOW())");
        }
        return $this->redirect("/event/".$eventID);

    }


    /**
     * Remove assign of the logged user
     *
     * @param int $evenID
     */
    function removeAssign($eventID=null)
    {
        $userSession = array();
        if (!$eventID) {
            $this->logErr('error occured: Invalid Event.');
            return $this->redirect("/");
        }
        if ($this->isLoggined()) {
             $userSession = $this->Session->read('loggedUser');
        }
        if ($userSession['id']!=VISITOR_USER) {
            $this->Event->query("DELETE FROM events_users WHERE event_id=".$eventID." AND user_id=".$userSession['id']);
        }
        return $this->redirect("/event/".$eventID);

    }

    /**
     *  Assign tournament to the event we will have new functionality but Billy doesn't answered
     *  @author vovich
     *  @param int $eventId
     */
    function assignTournament($eventId=null)
    {
        /*	if (!$eventId) {
        $this->Session->setFlash('Error with ID');
        $this->logErr('error occured: Invalid Event.');
        return $this->redirect("/");
        }

        $managers = $this->Manager->find('list',array('fields'=>array('user_id','user_id'),'conditions' => array('Manager.model' => 'Event','Manager.model_id'=>$eventId)));
        $this->Access->checkAccess('event','u',$managers);

        if (!empty($this->request->data['Tournament']['tournament'])){
         $EventTournament = ClassRegistry::init('EventTournament');
         if (empty($this->request->data['Tournament']['is_satellite']))
					 	 $this->request->data['Tournament']['is_satellite'] = 0;
         $cnt = $EventTournament->find('count',array('conditions'=>array('event_id'=>$eventId,'tournament_id'=>$this->request->data['Tournament']['tournament'])));
         if ($cnt>0) {
					 	 $EventTournament->updateAll(array('is_satellite'=>$this->request->data['Tournament']['is_satellite']),array('event_id'=>$eventId,'tournament_id'=>$this->request->data['Tournament']['tournament']));
					 	 $this->Session->setFlash('Tournament assigment has been changed.');
					     $this->redirect("/events/edit/".$eventId);
         } else {
						  $eventTournamentData = array();
						  $eventTournamentData['event_id']      = $eventId;
						  $eventTournamentData['tournament_id'] = $this->request->data['Tournament']['tournament'];
						  $eventTournamentData['is_satellite']  = $this->request->data['Tournament']['is_satellite'];
						  $EventTournament->save($eventTournamentData);
         }
         unset($EventTournament);
         $this->Session->setFlash('Tournament has been assigned.');
         $this->redirect("/events/edit/".$eventId);
        } else {
        $this->Session->setFlash('Tournament can not be empty.');
        return $this->redirect("/events/edit/".$eventId);
        }

        $this->Session->setFlash('Tournament has been assigned.');
        return $this->redirect("/events/edit/".$eventId);
        */
    }

    /**
     * Remove Tournament from assigments
     * @author vovich
     * @param int $eventId
     * @param int $tournamentId
     */
    function removeTournament($eventId=null,$tournamentId=null) 
    {

        /*if (!$eventId || !$tournamentId) {
        $this->Session->setFlash('Error with ID');
        $this->logErr('error occured: Invalid Event.');
        return $this->redirect("/");
        }

        $managers = $this->Manager->find('list',array('fields'=>array('user_id','user_id'),'conditions' => array('Manager.model' => 'Event','Manager.model_id'=>$eventId)));
        $this->Access->checkAccess('event','u',$managers);

         $EventTournament = ClassRegistry::init('EventTournament');

         $cnt = $EventTournament->find('count',array('conditions'=>array('event_id'=>$eventId,'tournament_id'=>$tournamentId)));
         if ($cnt>0) {
					 	 $EventTournament->deleteAll(array('event_id'=>$eventId,'tournament_id'=>$tournamentId),false);
					 	 $this->Session->setFlash('Tournament assigment has been removed.');
					     $this->redirect("/events/edit/".$eventId);
         } else {
         $this->Session->setFlash('Can not find such assigment.');
					    $this->redirect("/events/edit/".$eventId);
         }
         unset($EventTournament);
         $this->Session->setFlash('Tournament has been assigned.');
         $this->redirect("/events/edit/".$eventId);*/
    }

    /**
     * Action to fix slug for migration
     * @author vovich
     */
    function generateslug() 
    {
        /*$this->Event->recursive=-1;
        $events = $this->Event->find('all');

        foreach ( $events as $event ) {
        unset($event['Event']['slug']);
        $this->Event->save($event);
        }
        exit('Slug generation finished</pre>');
        */    
    }

    /**
 * Approve article
 * @param unknown_type $eventId
 * @return unknown_type
 */
    function approve($eventId = null) 
    {
        if (!$eventId) {
            $this->Session->setFlash(__('Invalid Event.'), 'flash_error');
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
        $event_managers = $this->Event->getManagersId($eventId);
        $this->Access->checkAccess('ApproveEvent', 'u', $event_managers);
        $event = $this->Event->find('first', array('contain'=>array('User'),'conditions'=>array('Event.id'=>$eventId)));

        if (!empty($event)) {
                 $event['Event']['is_approved'] = 1;
              $this->Event->save($event, false);

              /*Sending email*/
                 $event['Event']['viewlink'] = '<a href="'.MAIN_SERVER.'/events/view/'.$event['Event']['slug'].'">'.MAIN_SERVER.'/events/view/'.$event['Event']['slug'].'</a>';
                 $emailTo = "";
            if (!empty($event['User'])) {
                foreach ($event['User'] as $user) {
                            $emailTo[] = $user['email'];
                }
            }
                 $this->Mailer->prepare(
                     'ArticleApproved', array(
                                        'to'    => $emailTo,
                                        'data' => $event['Event']
                     )
                 );
                $this->Mailer->send();
                /*Sending Email*/
        }
        $this->Session->setFlash(__('Event has been approved.'), 'flash_success');
         $this->redirect($_SERVER['HTTP_REFERER']);
    }

    function ajaxShowManagers($id) 
    {
        $this->request->data = $this->Event->find('first', array('conditions'=>array('Event.id'=>$id),'contain'=>array('User')));
    }

    /**
     * AJAX Find parents
     * @author vovich
     */
    function findParents()
    {
        Configure::write('debug', '1');
        $this->layout = false;

        if ($this->RequestHandler->isAjax() && $this->request->data['Event']['find']) {
            $conditions['OR'] = array('Event.name LIKE'  => '%' . $this->request->data['Event']['find'] . '%', 'Event.description LIKE'  => '%' . $this->request->data['Event']['find'] . '%', 'Event.slug LIKE'  => '%' . $this->request->data['Event']['find'] . '%',) ;
            $conditions['AND'] = array('id <>' => $this->request->data['Event']['id'], 'Event.is_deleted <> ' => 1, 'Event.allow_satellite' => 1);
            $events   = $this->Event->find('all', array('contain' => array(), 'conditions' => $conditions, 'limit' => '100', 'order' => array('id' => 'DESC')));

            $this->set(compact('events'));
            $this->set('toEventID', $this->request->data['Event']['find']);
        } else {
            exit();
        }

    }
    /**
     * Events results
     * @author Oleg D.
     */
    function results() 
    {
        $eventPaginate['conditions'] = array('EventView.is_deleted' => 0, 'EventView.end_date <= NOW()', 'EventView.iscompleted' => 1);
        $eventPaginate['fields'] = array('DISTINCT(EventView.id)', 'EventView.*', 'Venue.*');
        $eventPaginate['contain'] = array('Venue', 'EventSatellite');
        $eventPaginate['limit'] = 10;
        $eventPaginate['extra']['count_fields'] = 'DISTINCT(EventView.id)';
        $eventPaginate['conditions'] = $eventPaginate['conditions'];
        $eventPaginate['order'] = array('EventView.end_date' => "DESC");

        //pr($eventPaginate);
        $this->paginate = array('EventView' => $eventPaginate);
        $events = $this->paginate('EventView');

        App::import('Model', 'Comment');
        $Comment = new Comment();
        $userId = $this->Access->getLoggedUserID();
        $eventsIds = Set::extract($events, '/EventView/id');
        $this->set('votes', $Comment->Vote->getVotes('Event', $eventsIds, $userId));
        //security
        $this->set('canVote', $this->Access->returnAccess('Vote_Event', 'c'));
        $this->set('events', $events);

    }
    /**
     * Wsobp results
     * @author Oleg D.
     */
    function wsobp_results() 
    {
        $eventPaginate['conditions'] = array('EventView.is_deleted' => 0, 'EventView.end_date <= NOW()', 'EventView.type' => 'wsobp', 'EventView.iscompleted' => 1);
        $eventPaginate['fields'] = array('DISTINCT(EventView.id)', 'EventView.*', 'Venue.*');
        $eventPaginate['contain'] = array('Venue', 'EventSatellite');
        $eventPaginate['limit'] = 10;
        $eventPaginate['extra']['count_fields'] = 'DISTINCT(EventView.id)';
        $eventPaginate['conditions'] = $eventPaginate['conditions'];
        $eventPaginate['order'] = array('EventView.end_date' => "DESC");

        //pr($eventPaginate);
        $this->paginate = array('EventView' => $eventPaginate);
        $events = $this->paginate('EventView');

        App::import('Model', 'Comment');
        $Comment = new Comment();
        $userId = $this->Access->getLoggedUserID();
        $eventsIds = Set::extract($events, '/EventView/id');
        $this->set('votes', $Comment->Vote->getVotes('Event', $eventsIds, $userId));
        //security
        $this->set('canVote', $this->Access->returnAccess('Vote_Event', 'c'));
        $this->set('events', $events);

    }

    /**
     * Ajax show relationhip for events
     * @author Oleg D.
     */
    function ajax_show_relationship($eventID) 
    {
        $this->paginate = array(
        'contain' => array('Event', 'Parent'),
        'conditions' => array('OR' => array('EventsEvent.parent_event_id' => $eventID, 'EventsEvent.event_id' => $eventID), 'AND' => array('Event.is_deleted' => 0)),
        'limit' => 10
        );
        $subEvents = $this->paginate('EventsEvent');
        $this->set(compact('subEvents', 'eventID'));
        $this->render();
    }

    /**
     * Ajax show teams for events
     * @author Oleg D.
     */
    function ajax_show_teams($eventID) 
    {

        $TeamsObject = ClassRegistry::init('TeamsObject');
        $this->paginate = array(
        'contain' => array('Team' => array('PersonalImage')),
        'conditions' => array('TeamsObject.model' => 'Event', 'TeamsObject.model_id' => $eventID, 'TeamsObject.status <>' => 'Deleted'),
        'order' => array('TeamsObject.id' => 'DESC'),
        'limit' => 10
        );
        $teams = $this->paginate($TeamsObject);

        $this->set(compact('teams', 'eventID'));
        $this->render();
    }

    function eventIsCompleteMessageAllUsers($eventID) 
    {
        $event_managers = $this->Event->getManagersId($eventID);
        $this->Access->checkAccess('event', 'u', $event_managers);

        $this->Event->recursive = -1;
        $event = $this->Event->find('first', array('conditions'=>array('id'=>$eventID)));

        $TeamsObject = ClassRegistry::init('TeamsObject');
        $teamIDs = $TeamsObject->getTeamIds('Event', $eventID);

        $Teammate = ClassRegistry::init('Teammate');
        $Teammate->recursive = 0;
        $teammates = $Teammate->find(
            'all', array(
            'conditions'=>array(
                'Teammate.team_id'=>$teamIDs,
                'Teammate.status <>' => 'Deleted',
                'User.is_deleted'=>0),
            'contain'=>array('User','Team'))
        );


        foreach ($teammates as $teammate) {
              $this->sendMailMessage(
                  'NewResultsEntered', array(
                  '{TEAMLINK}'       => MAIN_SERVER."/nation/beer-pong-teams/team-info/".$teammate['Team']['slug']."/".$teammate['Team']['id'],
                  '{TEAMNAME}'     => $teammate['Team']['name'],
                  '{EVENTNAME}' => $event['Event']['name'],
                  '{EVENTLINK}' => MAIN_SERVER . '/event/'. $event['Event']['id'] . '/' .$event['Event']['slug'],
                  '{RESULTSCONTACT}' => MAIN_SERVER . '/contact/results'

                  ), $teammate['User']['email']
              );
        }
        $event['Event']['emailssent'] = 1;
        $this->Event->save($event);

        $this->Session->setFlash(count($teammates).' messages have been sent', 'flash_success');
        $this->redirect('allcompleted');

    }

    function facebook_publish($eventID) 
    {
        Configure::write('debug', 1);
        if (!$this->Session->check('facebook_session_admin')) {
            $this->Session->write('previous_url', $_SERVER['REQUEST_URI']);
            return $this->redirect('/users/fb_connect/0/admin');
            exit;
        }

        $event = $this->Event->find(
            'first', array(
            'conditions' => array('Event.id' => $eventID, 'Event.is_deleted' => 0),
            'contain' => array('Tag', 'Venue', 'Venue.Phone', 'Venue.Address', 'Venue.Address.Provincestate', 'Venue.Address.Country', 'Eventfeature', 'Image', 'Timezone'),
            )
        );
        if (empty($event['Event']['id'])) {
            exit('Event Error');
        }
        $facebook_session_admin = $this->Session->read('facebook_session_admin');

        App::import('Vendor', 'facebook');
        $Facebook = new Facebook(array('appId' => FACEBOOK_API_KEY, 'secret' => FACEBOOK_SECRET_KEY, 'cookie'=>true, 'fileUpload' => true));

        if (empty($event['Venue']['Address']['address'])) {
            $this->Session->setFlash('Event has not been published - it has not venue with correct address', 'flash_error');
            return $this->redirect('/events/all/');
        }

        $start_date = date('Y-m-d H:i:s', strtotime($event['Event']['start_date']));
        $end_date = date('Y-m-d H:i:s', strtotime($event['Event']['end_date']));
        if (strtotime($event['Event']['end_date']) <= strtotime($event['Event']['start_date'])) {
            $end_date = '';
        }
        if (!empty($event['Image']['0']['filename'])) {
            $image = WWW_ROOT . 'img' . DS . 'Event' . DS . 'middle' . DS . $event['Image']['0']['filename'];
        } else {
            $image = WWW_ROOT . 'img' . DS . 'event_default.jpg';
        }

        $data = array(
        'access_token' => $facebook_session_admin['access_token'],
        'name' => $event['Event']['name'],
        'description' => strip_tags($event['Event']['description'] . ' ' . 'More information: ' . 'http://www.bpong.com/event/' . $event['Event']['id'] . '/' . $event['Event']['slug']),
        'location' => $event['Venue']['name'],
        'street' => $event['Venue']['Address']['address'],
        'city' => $event['Venue']['Address']['city'],
        'page_id' => '95752737260', //'Live Group',
        //'page_id' => '290103747681455', //'Test group',
        'privacy_type' => 'OPEN', // OPEN, CLOSED, SECRET
        'start_time' => $start_date, // timezone info is stripped
        'end_time' => $end_date,
        basename($image) => '@' . realpath($image)
        );
        if ($Facebook->api('/' . $facebook_session_admin['uid'] . '/events/', 'post', $data)) {
            //pr($event);
            $this->Event->validate = array();
            $this->Event->save(array('is_facebook_published' => 1, 'id' => $eventID));
            $this->Session->setFlash('Event has been published', 'flash_success');
        }
        return $this->redirect('/events/all/');
        exit;
    }

    function testAdd() 
    {
        $newEvent = array(
            'arecupdifsrecorded'=>1,
            'finalscompleted'=>0,
            'gamescreated'=>0,
            'iscompleted'=>0,
            'max_people_team'=>0,
            'messageforprojector'=>0,
            'min_people_team'=>0,
            'name'=>'Friday Night',
            'numgroups'=>'1',
            'numrounds'=>'2',
            'numteamsintobracket'=>0,
            'orignumteams'=>0,
            'structure_id'=>1,
            'user_id',
            'venue_id'=>572
            );
 
        if (!$this->Event->create()) { return "Could not create event."; 
        }
        if (!$this->Event->save($newEvent)) { return array('error'=>$newEvent); 
        }
        $lastID = $this->Event->getLastInsertID();
        //return $lastID;
        $result = $this->Event->find(
            'first', array('conditions'=>array('Event.id'=>$lastID),
            'contain'=>array('Eventstructure'))
        );
        return $this->returnJSONResult(array($lastID,$result));
    }
}
?>
