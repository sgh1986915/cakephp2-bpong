<?php class StatisticsController extends AppController
{



    var $name    = 'Statistics';
    var $components = array('Time', 'Csv');
    var $uses = array('User','Statistic', 'Signup');

    /**
* 
* 
   * @author vovivh
   * @param  string $modelname - name of the model for wich will be added new signup
   * @param  int    $modelID   - ID of the model for which new signup will be added 
   
*/
    function signupsStatistics($modelName="Event",$modelID = null) 
    {
        $this->Access->checkAccess('SignupStatistic', 'r');
        $_Model = ClassRegistry::init($modelName);
        $_Model->recursive = -1;
        $modelInformation = $_Model->find('first', array('conditions'=>array($modelName.'.id'=>$modelID)));

        if (empty($modelInformation)) {
            $this->Session->setFlash('Such  '.$modelName.' does not exist.', 'flash_error');
            $this->redirect('/');
      
        }

        $Signup      = ClassRegistry::init('Signup');
        $TeamsObject = ClassRegistry::init('TeamsObject');
        $SignupRoom  = ClassRegistry::init('SignupRoom');
        
        $Signup->recursive = -1;
        $paidIndividualSignups       = $Signup->find(
            'count', array('conditions'=>array(
            'model'=>$modelName,
            'model_id'=>$modelID,
            'status'=>'paid',
            'for_team'=>0))
        );
        $paidTeamSignups       = $Signup->find(
            'count', array('conditions'=>array(
            'model'=>$modelName,
            'model_id'=>$modelID,
            'status'=>'paid',
            'for_team'=>1))
        );
        $partlyPaidIndividualSignups  = $Signup->find(
            'count', array('conditions'=>array(
            'model'=>$modelName,
            'model_id'=>$modelID,
            'status'=>'partly paid',
            'for_team'=>0))
        );
        $partlyPaidTeamSignups  = $Signup->find(
            'count', array('conditions'=>array(
            'model'=>$modelName,
            'model_id'=>$modelID,
            'status'=>'partly paid',
            'for_team'=>1))
        );
        $willPaid = $Signup->find('all', array('fields'=>'sum(total-discount) as willpaid','conditions'=>array('model'=>$modelName,'model_id'=>$modelID,'status'=>array('partly paid','paid'))));
        $paymentRemaining = $Signup->find('all', array('fields'=>'sum(total-paid-discount) as paymentremaining','conditions'=>array('model'=>$modelName,'model_id'=>$modelID,'status'=>array('partly paid','paid'))));
     
        //This represents the final count of players
        $finalPlayerCount = ($partlyPaidIndividualSignups + $paidIndividualSignups) +
        ($partlyPaidTeamSignups + $paidTeamSignups) * $modelInformation[$modelName]['people_team'];
    
        $sql = "SELECT COUNT(*) AS count
          FROM `teams_objects` AS TeamsObject
          INNER JOIN teams as Team on Team.id = TeamsObject.team_id
          WHERE `model` = '$modelName' AND `model_id` = $modelID AND TeamsObject.`status` IN ('Approved', 'Created','Confirmed') AND Team.`status` = 'Completed'";

        $teamsCount = $TeamsObject->query($sql);
        $teamsCount = $teamsCount[0][0]['count'];

        $rooms = $SignupRoom->csvReport($modelName, $modelID);
        $roomsCount = count($rooms);
        $this->set('modelInformation', $modelInformation);
        $this->set('modelName', $modelName);
    
        //Number of outstanding promocodes
        $PromocodesAssignment = ClassRegistry::init('PromocodesAssigment');
        $promocodesCount = $PromocodesAssignment->find(
            'all', array(
            'conditions'=>array('PromocodesAssigment.model'=>$modelName,
                            'PromocodesAssigment.model_id'=>$modelID,
                            'Promocode.type'=>'Free',
                            'Promocode.is_deleted'=>0,
                            'Promocode.uses_count'=>0),
            'contain'=>array('Promocode'))
        ); 

        $this->set('promocodesCount', count($promocodesCount));
        $this->set('paidIndividualSignups', $paidIndividualSignups);
        $this->set('paidTeamSignups', $paidTeamSignups);
        $this->set('partlyPaidIndividualSignups', $partlyPaidIndividualSignups);
        $this->set('partlyPaidTeamSignups', $partlyPaidTeamSignups);
        $this->set('teamsCount', $teamsCount);
        $this->set('roomsCount', $roomsCount);
        $this->set('willPaid', $willPaid);
        $this->set('paymentRemaining', $paymentRemaining);
        $this->set('finalPlayerCount', $finalPlayerCount);
    
        unset($_Model);
        unset($Signup);
        unset($TeamsObject);
        unset($SignupRoom);
    
    }

    /**
* 
* 
   * produce and export a "master list" that includes all information for The WSOBP
     *
   * @author vovich
   * @param  string model
   * @param  int model_id
   
*/
    function masteListCsv($model = null, $model_id = null) 
    {

        $this->Access->checkAccess('SignupStatistic', 'r');
        set_time_limit(700);
        Configure::write('debug', '0');
        $this->layout = null;
        $this->autoLayout = false;
        $this->autoRender = false;

        $signupsIDs = $this->Signup->completedTeamSignups($model, $model_id); 

        $users = $this->Signup->getSignupsUsersTeamsForStatistic($signupsIDs, $model, $model_id);
        $results = $this->Statistic->prepareData($users);
        $statistics = array("Username","Last Name", "First Name",    "DOB",    "Email","Phone","Address 1", "Address 2","Zip Code", "City","State","Country", "Team ID","Team Name", "Signup ID", "Payment Status", "Payment Type");
    
        if ($results) {
            $this->Csv->addGrid($results);
      
        } else {
                $this->Csv->addRow($statistics);
      
        }

        $this->Csv->setFilename("masterList");
        echo $this->Csv->render1();
    
    }
    /**
* 
* 
   *  will combine master list, not teamadded users and not-room-confirmed users into one list
     *
   * @author vovich
   * @param  string model
   * @param  int model_id
   
*/
    function combineCsv($model = null, $model_id = null) 
    {

        $this->Access->checkAccess('SignupStatistic', 'r');
        set_time_limit(700);
        Configure::write('debug', '0');
        $this->layout = null;
        $this->autoLayout = false;
        $this->autoRender = false;

        $signupsIDs = $this->Signup->find('list', array('conditions' => array('Signup.model' => $model, 'Signup.model_id' => $model_id, 'status' => array('paid', 'partly paid')), 'fields' => array('Signup.id', 'Signup.id')));     
        //return $signupsIDs;
        $signups = $this->Signup->getSignupsUsersTeamsForStatistic($signupsIDs, $model, $model_id);

        $notCompletedRoomSignupsIDs = $this->Signup->notCompletedRoomSignups($model, $model_id);
        $notCompletedTeamSignupsIDs = $this->Signup->notCompletedTeamSignups($model, $model_id);

        foreach ($signups as $key => $signup) {
            if (isset($notCompletedRoomSignupsIDs[$signup['s']['id']])) {
                $signups[$key]['room_status'] = 'Incomplete';        
            } else {
                    $signups[$key]['room_status'] = 'Complete';
       
            }
            if (isset($notCompletedTeamSignupsIDs[$signup['s']['id']])) {
                $signups[$key]['team_status'] = 'Incomplete';        
            } else {
                    $signups[$key]['team_status'] = 'Complete';
       
            }           
        }    
        //pr($notCompletedRoomSignupsIDs);
        //pr($signups);
        //exit;
        $results = $this->Statistic->prepareData($signups);
        $statistics = array("Username","Last Name", "First Name", "DOB",    "Email","Phone","Address 1", "Address 2","Zip Code", "City","State","Country", "Team ID","Team Name", "Signup ID", "Payment Status", "Payment Type", "Team Status", "Room Status");
    
        if ($results) {
            $this->Csv->addGrid($results);

      
        } else {
                $this->Csv->addRow($statistics);
      
        }

        $this->Csv->setFilename("CombineReport");
        echo $this->Csv->render1();
    
    }

    /**
* 
* 
   * @author Alex
   
*/
    function notPaidUsersCsv($model = null, $model_id = null) 
    {

        set_time_limit(700);
        $this->Access->checkAccess('SignupStatistic', 'r');
        Configure::write('debug', '0');
        $this->layout = null;
        $this->autoLayout = false;
        $this->autoRender = false;

        $results = $this->User->notPaidUsers($model, $model_id);

        $alldata = array();
        foreach($results as $index => $value) {
            $alldata[$index]["Username"] = $value['u']['lgn'];
            $alldata[$index]["Email"] = $value['u']['email'];
            if ($value['s']['for_team'] == 1) {
                $alldata[$index]["Payment Type"] = 'Team';         
            } else {
                     $alldata[$index]["Payment Type"] = 'Individual';          
            }

      
        }
        $this->Csv->addGrid($alldata);
        $this->Csv->setFilename("NotPaidUsers");
        echo $this->Csv->render1();
    
    }
    /**
* 
     *
 * @param  $model
 * @param  $model_id
 * @return unknown_type
 
*/
    function notRoomConfirmedCsv($model = null, $model_id = null) 
    {

        $this->Access->checkAccess('SignupStatistic', 'r');
        set_time_limit(700);
        Configure::write('debug', '0');
        $this->layout = null;
        $this->autoLayout = false;
        $this->autoRender = false;

        $signupsIDs = $this->Signup->notCompletedRoomSignups($model, $model_id);
        $users = $this->Signup->getSignupsUsersForStatistic($signupsIDs);
        

        $statistics = array("Username","Last Name", "First Name",    "DOB",    "Email","Phone","Address 1", "Address 2","Zip Code", "City","State","Country", "Signup ID", "Payment Status", "Payment Type");
        $results = $this->Statistic->prepareData($users);

        if ($results) {
            $this->Csv->addGrid($results);
      
        } else {
                $this->Csv->addRow($statistics);
      
        }

        $this->Csv->setFilename("NotRoomAddedUsers");
        echo $this->Csv->render1();
    
    }
    /**
* 
     *
 * @param  $model
 * @param  $model_id
 * @return unknown_type
 
*/
    function notTeamAddedCsv($model = null, $model_id = null) 
    {

        $this->Access->checkAccess('SignupStatistic', 'r');
        Configure::write('debug', '0');
        $this->layout = null;
        $this->autoLayout = false;
        $this->autoRender = false;

        $signupsIDs = $this->Signup->notCompletedTeamSignups($model, $model_id);
        $users = $this->Signup->getSignupsUsersForStatistic($signupsIDs);
        

        $statistics = array("Username","Last Name", "First Name",    "DOB",    "Email","Phone","Address 1", "Address 2","Zip Code", "City","State","Country", "Signup ID", "Payment Status", "Payment Type");
        $results = $this->Statistic->prepareData($users);

        if ($results) {
            $this->Csv->addGrid($results);
      
        } else {
                $this->Csv->addRow($statistics);
      
        }
    
        $this->Csv->setFilename("NotTeamAdded");
        echo $this->Csv->render1();
    
    }
    /**
* 
     *
 * @param  $model
 * @param  $model_id
 * @return unknown_type
 
*/
    function notTeamAddedAtAllCsv($model = null, $model_id = null) 
    {

        $this->Access->checkAccess('SignupStatistic', 'r');
        Configure::write('debug', '0');
        $this->layout = null;
        $this->autoLayout = false;
        $this->autoRender = false;

        $signupsIDs = $this->Signup->notCompletedAtAllTeamSignups($model, $model_id);
        $users = $this->Signup->getSignupsUsersForStatistic($signupsIDs);
        

        $statistics = array("Username","Last Name", "First Name",    "DOB",    "Email","Phone","Address 1", "Address 2","Zip Code", "City","State","Country", "Signup ID", "Payment Status", "Payment Type");
        $results = $this->Statistic->prepareData($users);

        if ($results) {
            $this->Csv->addGrid($results);
      
        } else {
                $this->Csv->addRow($statistics);
      
        }
    
        $this->Csv->setFilename("NotTeamAdded");
        echo $this->Csv->render1();
    
    }

      
}
?>
