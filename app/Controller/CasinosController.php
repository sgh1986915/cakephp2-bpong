<?php class CasinosController extends AppController
{



    var $name    = 'Casinos';
    var $helpers = array('Html', 'Form');
    var $uses     = array( 'Signup', 'SignupRoom', 'SignupRoommate', 'Question', 'Answer', 'Package' );
    var $components = array('Time', 'Csv');

    function index() 
    {
        $this->Access->checkAccess('casino', 'l');

        $criteria = array();
        $criteria[0] ="SignupRoom.status IN ('Approved', 'Confirmed')";
        if (!empty( $this->request->data ) ) {

            if (!empty($this->request->data['Criteria']['status'])) {
                $criteria[0] = "SignupRoom.status = '" . $this->request->data['Criteria']['status'] . "'";
      
            }
            if (!empty($this->request->data['Criteria']['tornevent'])) {
                $temp = explode("_", $this->request->data['Criteria']['tornevent']);
                $criteria[ ucfirst($temp[0]) . ".shortname"] = $temp[1];
      
            }

            if (!empty($this->request->data['Criteria']['user'])) {
                $criteria['UserCreator.lgn'] = $this->request->data['Criteria']['user'];
      
            }
     
        }
        $roomexists = $this->SignupRoom->find('count', array('conditions' => array("SignupRoom.status IN ('Approved', 'Confirmed')")));
        $this->set('csvNotEmpty', $roomexists);
        $type_options = $this->SignupRoom->find('all', array('conditions' => $criteria[0]));

          $all_options = array();
          $all_status_options = array();
          
        foreach($type_options as $options) {
            $all_status_options[$options['SignupRoom']['status']] = $options['SignupRoom']['status'];
            if (!empty($options['Tournament']['id'])) {
                $all_options['tournament_' . $options['Tournament']['shortname']] = $options['Tournament']['name'];
           
            }elseif (!empty($options['Event']['id'])) {
                        $all_options['event_' . $options['Event']['shortname']] = $options['Event']['name'];
          
            }
          
        }
          
          $this->paginate['conditions'] = $criteria;

          $this->set('type_options', $all_options);
          $this->set('status_options', $all_status_options);
        $this->set('rooms', $this->paginate("SignupRoom"));

    
    }

    function view( $id = null) 
    {

        $id = (int)$id;
        if (empty($id)) {
            $this->Session->setFlash('Invalid Id.', 'flash_error');
            return $this->redirect("/");
            exit;
     
        }

        $this->Access->checkAccess('casino', 'r', $id);

        $this->SignupRoom->id = $id;
        $current_room = $this->SignupRoom->read();

        $packagename = $this->Package->getPackageName(
            $current_room['User']['id'], $current_room['SignupRoom']['model'], $current_room['SignupRoom']['model_id'] 
        );

        $this->set('packagename', $packagename);

        $answers = $this->Answer->getAnswers(
            'Room_for_'.strtolower($current_room['SignupRoom']['model']), $current_room['SignupRoom']['model_id'], $current_room['Creator']['user_id'] 
        );
        $this->set('answers', $answers);
        
        $this->set('roomInfo', $this->SignupRoommate->getMyRoomInfo($current_room['Creator']['user_id'], $current_room['SignupRoom']['model'], $current_room['SignupRoom']['model_id']));
        
    }

    /**
* 
* 
     * Saving questions
     *
     * @author vovich
     
*/
    function __storeAnswers( $modelName=null, $modelID=null, $userID=null, $answers = array() ) 
    {
        //Storing answers
                          $this->Question->recurcive = -1;
                          $questions = $this->Question->find('all', array('conditions'=>array('model'=>$modelName,'model_id'=>$modelID)));
         $this->Answer->deleteAll(array('model'=>$modelName,'model_id'=>$modelID,'user_id'=>$userID));

        foreach ($questions as $question) {
            if (!empty($answers['Question']['Option'.$question['Question']['id']])) {
                foreach ($answers['Question']['Option'.$question['Question']['id']] as $option) {
                    $answer = array();
                    $answer['model']     = $modelName;
                    $answer['model_id']  = $modelID;
                    $answer['user_id']   = $userID;
                    $answer['option_id'] = $option;
                    if (!empty($answers['Question']['input_'.$option])) {
                        $answer['text'] = $answers['Question']['input_'.$option];
               
                    }
                    $this->Answer->create();
                    $this->Answer->save($answer);
            
                }
            
            }
                           
        }
        return true;
    
    }

    /**
* 
* 
     * @author Povstyanoy
     
*/
    /*	function _getSignupDetails ( $singupId = null, $isBoolean = false, $recursive = 1 ) {
            if ( !$singupId ) {
            if (!$isBoolean) {
            $this->Session->setFlash('Incorrect ID', 'flash_error');
				return $this->redirect('/');
				exit();
    } else {
            return false;
    }
            }
            $this->Signup->recursive = $recursive;
            $signupDetails = $this->Signup->find('first',array('conditions' => array( 'Signup.id' => $singupId )));
            if (empty($signupDetails)) {
            if ( !$isBoolean ) {
            $this->Session->setFlash('Can not find such signup.', 'flash_error');
				return $this->redirect('/');
				exit();
    } else {
            return false;
    }
            }
            //Getting packages
            if (!empty($signupDetails['Packagedetails']['package_id'])) {
            $this->Package->recursive = -1;
            $packageInformation = $this->Package->find('first',array('conditions'=>array('Package.id'=>$signupDetails['Packagedetails']['package_id'])));
            $signupDetails['Package'] = $packageInformation['Package'];
            }
            return $signupDetails;
            }
            */
    function changecode() 
    {
        Configure::write('debug', '0');
        $this->layout = false;
        
             //Is request ajax?
        if (!$this->RequestHandler->isAjax() ) {
            $this->Session->setFlash('This action is not permitted for you.', 'flash_error');
            return $this->redirect($_SERVER['HTTP_REFERER']);
        
        }
        /*
                if ( !$this->Access->getAccess( 'casino', 'w' )) {
              echo "You don't have permissions for that!!!";
              exit();
                }
            */    
        if (!empty( $this->request->params['form']['id'] ) && !empty( $this->request->params['form']['code'] ) ) {
            
                 $roommate_id = explode("_", $this->request->params['form']['id']);
            $roommate_id = (int)$roommate_id[1];
            $conf_code = addslashes($this->request->params['form']['code']);
            
            if (empty($roommate_id)) {
                echo "No ID";
                exit();
         
            }
            
                 $this->SignupRoommate->id = $roommate_id;
            if ($this->SignupRoommate->saveField('confirmation_code', $conf_code)) {
                
                if ($this->SignupRoommate->isAllConfCodeInRoomFilled($roommate_id) ) {
                    $this->SignupRoommate->setRoomStatus($roommate_id);
       
                }
                
                     echo 0;
                exit();
      
            } else {
                   $errorMessage = "Error(s):<br />";
                foreach ($this->SignupRoommate->validationErrors as $value) {
                    $errorMessage .= "$value <br />";
       
                }
                   echo $errorMessage;
                   exit();
      
            }
     
        }
        
    }
    
    function getCsv($model = null, $modelID = null) 
    {
        Configure::write('debug', '0');
        ${$model} = ClassRegistry::init($model);
        $modelFind = ${$model}->read(null, $modelID);
        
        $this->layout = null;
        $this->autoLayout = false;
        $this->autoRender = false;
        
        $rooms = $this->SignupRoom->csvReport($model, $modelID);
        $numRoommates = 0;
        $allQuestions = array();
        foreach ($rooms as $room) {
            if (count($room['roommates']) > $numRoommates) {
                $numRoommates = count($room['roommates']);    
            }
            foreach ($room['answers'] as $question => $answer) {
                $allQuestions[$question] = $question;    
            }                
        }

        $alldata = array();    
        $index = 0;
        foreach($rooms as $room) {        
             $alldata[$index]["Room ID"] = $room['room_id'];
            $alldata[$index]["People in room"] = $room['people_in_room'];
                        
             $alldata[$index]["Room Type"] = $room['packagename'];
            
            foreach ($allQuestions as $question) {
                $alldata[$index][$question] = '';    
            }
            
            foreach ($room['answers'] as $question => $answer) {
                $alldata[$index][$question] = $answer;    
            }
            for($i=1; $i<=$numRoommates; $i++) {
                $alldata[$index]['Roommate ' . $i . ' login'] = '';
                $alldata[$index]['Roommate ' . $i . ' name'] = '';        
            }
            $i = 1;
            foreach ($room['roommates'] as $roommate) {
                $alldata[$index]['Roommate ' . $i . ' login'] = $roommate['lgn'];
                $alldata[$index]['Roommate ' . $i . ' name'] = $roommate['firstname'] . ' ' . $roommate['lastname'];
                $i++;
      
            }
                                    
             //$alldata[$index]["Arrival Date"] = date('m/d/Y', strtotime($modelFind[$model]['start_date']));
            //$alldata[$index]["Departure Date"] = date('m/d/Y', strtotime($modelFind[$model]['end_date']));
            
            
             $index++;
     
        }

        $this->Csv->addGrid($alldata);
        $this->Csv->setFilename("Rooms");
        echo $this->Csv->render1();
    
    }
    
    function __convertDate($sqlDate) 
    {
        if (empty($sqlDate)) {
            return "";
        
        }
        $sqlDate = explode('-', $sqlDate);
        return $sqlDate[1] . "/" . $sqlDate[2] . "/" . $sqlDate[0];
    
    }
    
      
      }
?>
