<?php class RoomsController extends AppController
{

    var $name    = 'Rooms';
    var $helpers = array('Html', 'Form');
    var $uses     = array( 'Signup', 'SignupRoom', 'SignupRoommate', 'Question', 'Answer', 'Package' );
    var $components = array('Time');

    function index() 
    {
        $this->Access->checkAccess('room', 'l');

        $criteria = array();
        if (!empty( $this->request->data ) ) {

            if (!empty($this->request->data['Criteria']['status'])) {
                $criteria['SignupRoom.status'] = $this->request->data['Criteria']['status'];
            }
            if (!empty($this->request->data['Criteria']['tornevent'])) {
                $temp = explode("_", $this->request->data['Criteria']['tornevent']);
                $criteria[ ucfirst($temp[0]) . ".shortname"] = $temp[1];
            }

            if (!empty($this->request->data['Criteria']['user'])) {
                $criteria['UserCreator.lgn'] = $this->request->data['Criteria']['user'];
            }
        }

        $type_options = $this->SignupRoom->find('all');

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

          $this->set('type_options', $all_options);
          $this->set('status_options', $all_status_options);
        $this->set('rooms', $this->paginate("SignupRoom", $criteria));

    }

    function view( $id = null) 
    {
        $id = (int)$id;
        if (empty($id)) {
            $this->Session->setFlash('Invalid Id.', 'flash_error');
            return $this->redirect("/");
            exit;
        }

        $this->Access->checkAccess('room', 'r', $id);

        $this->SignupRoom->id = $id;
        $current_room = $this->SignupRoom->read();

        $room_neighbors = array();
        $this->SignupRoommate->recursive = 0;
        $room_neighbors = $this->SignupRoommate->find('all', array( 'conditions' => array(   'SignupRoommate.room_id' => $id )));

        $this->set('rooms', $room_neighbors);

        $answers = $this->Answer->getAnswers(
            'Room_for_'.strtolower($current_room['SignupRoom']['model']), $current_room['SignupRoom']['model_id'], $current_room['Creator']['user_id'] 
        );
        $this->set('answers', $answers);
    }

    /**
     * Delete function for Admin
     *
     * @author Povstyanoy
     * @param  int $signupId
     */
    function delete_room( $id = null) 
    {
        $id = (int)$id;
        if (empty($id)) {
            $this->Session->setFlash('Invalid Id.', 'flash_error');
            return $this->redirect("/rooms");
            exit;
        }

        $this->Access->checkAccess('room', 'd');

        $this->SignupRoom->id = $id;

        $room = $this->SignupRoom->read();

        $model     = $room['SignupRoom']['model'];
        $model_id = $room['SignupRoom']['model_id'];
        $user_id    = $room['Creator']['user_id'];
        $roomInfo = $this->SignupRoommate->getEmailsOfRoomUsers($id);

        // Send an Email for all people in current room
        foreach( $roomInfo as $value ) {
            $result = $this->sendMailMessage(
                'DeletedRoom', array(
                     '{FNAME}'         => $value['User']['firstname'],
                     '{LNAME}'         => $value['User']['lastname']
                      ),
                $value['User']['email']
            );
            if (!$result) {
                $this->logErr('error occured while sendinig DeletedRoom email');
            }
        }

        $this->SignupRoom->recursive = -1;
        $this->SignupRoom->saveField('status', 'Deleted');

        $this->Answer->deleteAll(array( 'model' => 'Room_for_'.strtolower($model), 'model_id' => $model_id, 'user_id' => $user_id ));

        $this->SignupRoommate->unbindModel(array('belongsTo' => array('User', 'Room')));
        $this->SignupRoommate->updateAll(array( 'status' => '\'Declined\'' ), array( 'room_id' => $id ));

        if(isset($this->request->params['requested'])) {
            // for the request action
            return;
        }else{
            $this->Session->setFlash('The room was deleted.', 'flash_error');
            return $this->redirect("/rooms");
            exit();
        }
    }

    /**
     * Saving questions
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
     * Questions page for room create
     * @author Povstyanoy
     * @param $model Tournament or Event
     * @param $id    Id of model
     * @param $signupId SignUp id
     */
    function createRoom( $signupId, $userRole = 'creator') 
    {
        $signupDetails = $this->_getSignupDetails($signupId);
        
        if ($userRole == 'creator') {
            $backUrl = '/signups/signupDetails/' . $signupId . '/tab-rooms'; 
            $userID = $signupDetails['Signup']['user_id'];    
            $usersAccess = array($userID => $userID);    
        } else {
            $backUrl = '/signups/signupDetailsTeammate/' . $signupId . '/tab-rooms';
            $userID = $this->getUserID();
            $SignupsUser = ClassRegistry::init('SignupsUser');
            $signupUsers = $SignupsUser->find('all', array('conditions' => array('signup_id' => $signupId)));
            $signupUserIDs = Set::combine($signupUsers, '{n}.SignupsUser.user_id', '{n}.SignupsUser.user_id');
            $signupUsers = Set::combine($signupUsers, '{n}.SignupsUser.user_id', '{n}.SignupsUser');            
            $usersAccess = $signupUserIDs;
        }
        $this->Access->checkAccess('Signup', 'r', $usersAccess);        
        

        $model     = $signupDetails['Signup']['model'];
        $model_id= $signupDetails['Signup']['model_id'];

        //Getting Room
        $this->SignupRoommate->recursive = 1;
        $isIaccepted = $this->SignupRoommate->find(
            'first', array( 'conditions' => array(
                                                                          'SignupRoommate.user_id' => $userID
                                                                        , 'SignupRoommate.status'    => 'Accepted'
                                                                        , 'Room.model' => $model
                                                                        , 'Room.model_id' => $model_id
                                                                        , 'Room.status <> \'Deleted\'')
            )
        );

        //Getting Room
        $this->SignupRoommate->recursive = 1;
        $room = $this->SignupRoommate->find(
            'first', array( 'conditions' => array(
                                                                  'SignupRoommate.user_id' => $userID
                                                                , 'SignupRoommate.status'    => 'Creator'
                                                                , 'Room.model' => $model
                                                                , 'Room.model_id' => $model_id
                                                                , 'Room.status <> \'Deleted\'')
            )
        );
        
        //		$this->SignupRoom->id = $room['SignupRoommate']['room_id'];
        //		$myroom = $this->SignupRoom->read();

        $myroom = $room['Room'];

        if ($signupDetails['Signup']['status'] != 'paid') {
            $this->Session->setFlash('The Room could not be created. You don\'t pay full price.', 'flash_error');
            return $this->redirect($backUrl);
            exit();
        }

        // If I was accepted by somebody.
        if (!empty( $isIaccepted )) {
            $this->Session->setFlash('You were accepted by somebody already. You cant create the room.', 'flash_error');
            return $this->redirect($backUrl);
            exit();
        }

        // If room exist then redirect back, because user can't have more than one room
        if (!empty( $myroom ) ) {
            $this->Session->setFlash('The Room could not be created. You already have one.', 'flash_error');
            return $this->redirect($backUrl);
            exit();
        }
        $signupQuestion = $this->Question->find('count', array('conditions' => array('model' => 'Room_for_' . strtolower($model), 'model_id' => $model_id)));
        
        if (!empty($this->request->data['Question']) || empty($signupQuestion)) {            
            if ($this->__storeAnswers('Room_for_' . strtolower($model), $model_id, $userID, $this->request->data) ) {

                $this->SignupRoom->create();
                $room = array();
                $room['SignupRoom']['model'] = $model;
                $room['SignupRoom']['model_id'] = $model_id;
                $room['SignupRoom']['people_in_room'] = (int)$this->SignupRoom->peopleInRoom($model, $model_id, $signupDetails['Signup']['user_id']);
                //If package is for one person, than room status is approved
                if ($room['SignupRoom']['people_in_room'] <= 1 || ($signupDetails['Signup']['for_team'] && $userRole == 'creator' && $room['SignupRoom']['people_in_room'] == $signupDetails[$signupDetails['Signup']['model']]['people_team'])) {
                    $room['SignupRoom']['status'] = 'Approved';
                    $room['SignupRoom']['approve_time'] = date("Y-m-d");
                } else {
                    $room['SignupRoom']['status'] = 'Created';
                }
                
                if ($this->SignupRoom->save($room) ) {
                    $roomID = $this->SignupRoom->getLastInsertID();
                    $to_save['SignupRoommate']['user_id'] = $userID;
                    $to_save['SignupRoommate']['room_id'] = $roomID;
                    $to_save['SignupRoommate']['status'] = "Creator";
                    $this->SignupRoommate->create();
                    $this->SignupRoommate->save($to_save);
                    unset($to_save);
                    
                    
                    // IF for team, check package, add teammates as roommates
                    if ($signupDetails['Signup']['for_team'] && $userRole == 'creator') {                                                
                        if ($room['SignupRoom']['people_in_room'] == $signupDetails[$signupDetails['Signup']['model']]['people_team']) {
                             $SignupsUser = ClassRegistry::init('SignupsUser');
                            $signupUsers = $SignupsUser->find('all', array('conditions' => array('signup_id' => $signupDetails['Signup']['id'])));
                            foreach ($signupUsers as $signupUser) {    
                                if ($signupUser['SignupsUser']['user_id']!=$signupDetails['Signup']['user_id']) {                                 
                                    $to_save['SignupRoommate']['user_id'] = $signupUser['SignupsUser']['user_id'];
                                    $to_save['SignupRoommate']['room_id'] = $roomID;
                                    $to_save['SignupRoommate']['status'] = "Accepted";
                                    $this->SignupRoommate->create();
                                    $this->SignupRoommate->save($to_save);
                                    unset($to_save);     
                                }
                            }     
                                
                        }    
                    }
                    $this->Session->setFlash('Room saved.', 'flash_success');
                    return $this->redirect($backUrl);
                    exit;
                } else {
                    $this->Session->setFlash('The Room could not be saved. Please, try again.', 'flash_error');
                }
            } else {
                $this->Session->setFlash('The Room could not be saved. Please try again.', 'flash_error');
            }

        }
        $this->Session->setFlash('The Room could not be saved. Please specify room preferences.', 'flash_error');
        return $this->redirect($backUrl);
    }

    /**
     * @author Povstyanoy
     */
    function acceptRoommate( $signupId, $roommate_id_to_accept ) 
    {
        $signupDetails = $this->_getSignupDetails($signupId);
        $this->Access->checkAccess('Signup', 'r', $signupDetails['Signup']['user_id']);

        $model     = $signupDetails['Signup']['model'];
        $model_id= $signupDetails['Signup']['model_id'];

        $signupRoom = $this->SignupRoommate->_getMyRoomId($signupDetails['Signup']['user_id'], $model, $model_id);

        //Get who request my room and accepted it
        if (!empty( $signupRoom ) ) {
            $this->SignupRoommate->recursive = -1;
            $roommateRequests = $this->SignupRoommate->find(
                'all', array( 'conditions' => array(       'room_id' => $signupRoom
                                                                                                        , 'status'    => 'Accepted'
                                                                                                        , 'user_id <> ' . $signupDetails['Signup']['user_id'] )
                                                                            )
            );
            if (!empty ( $roommateRequests ) ) {
                $this->Session->setFlash("Someone already accepted your request. You can't accept requests.", 'flash_error');
                return $this->redirect(array( 'controller'=> 'signups', 'action' => 'signupDetails', $signupId ));
                exit();
            }
        }
        //Get a roommate to accept
        $this->SignupRoommate->recursive = -1;
        $roommate = $this->SignupRoommate->find(
            'first', array( 'conditions' => array(       'user_id' => $signupDetails['Signup']['user_id']
                                                                                        , 'id' => $roommate_id_to_accept
                                                                                        , 'status'    => 'Pending' ))
        );
        //Quantity of users in room
        $mates_in_room = $this->SignupRoommate->currentQuantityOfPeople($roommate['SignupRoommate']['room_id']);

        if (!empty( $roommate ) ) {
            //Accept mate
            $this->SignupRoommate->id = $roommate_id_to_accept;
            $this->SignupRoommate->saveField('status', 'Accepted');

            if (!empty( $signupRoom ) ) {
                $this->SignupRoom->id = $signupRoom;
                $roomdeleted = $this->SignupRoom->saveField('status', 'Deleted');

            }

            //change status of room. If room is full then status is Approved
            $this->SignupRoom->id = $roommate['SignupRoommate']['room_id'];
            if ($signupDetails['Package']['people_in_room'] > ($mates_in_room + 1) ) {
                $this->SignupRoom->saveField('status', 'Pending');
            } else {
                $this->SignupRoom->saveField('status', 'Approved');
                $this->SignupRoom->saveField('approve_time', date("Y-m-d"));
                //Decline another users with status pending in this room
                $this->SignupRoommate->declinePendings($roommate['SignupRoommate']['room_id']);
            }

            //Decline All other requests
            //decline users, who want to my room
            if (!empty( $signupRoom ) ) {
                $this->SignupRoommate->recursive = -1;
                $roommateToDecline = $this->SignupRoommate->find(
                    'all', array( 'conditions' => array(       'room_id' => $signupRoom
                                                                                                            , 'status'    => 'Pending'))
                );
            }
            //decline users, who request me
            $roommates = $this->SignupRoommate->find(
                'first', array( 'conditions' => array(
                                                                                          'user_id' => $signupDetails['Signup']['user_id']
                                                                                        , 'status'    => 'Pending' ))
            );
            $to_decline = array();
            if (!empty( $roommateToDecline ) ) {
                foreach ( $roommateToDecline as $index => $value ) {
                    $to_decline[] = $value['SignupRoommate']['id'];
                }
            }
            if (!empty( $roommates ) ) {
                foreach ( $roommates as $index => $value ) {
                    $to_decline[] = $value['SignupRoommate']['id'];
                }
            }

            if (!empty( $to_decline ) ) {
                $this->SignupRoommate->updateAll(array('status' => "'Declined'"), array( 'SignupRoommate.id' => $to_decline ));
            }

            unset( $roommateToDecline, $to_decline );
            $this->Session->setFlash("You accepted the rooming request.", 'flash_success');
        } else {
            $this->Session->setFlash("Room was not accepted.", 'flash_error');
        }
        return $this->redirect(array( 'controller'=> 'signups', 'action' => 'signupDetails', $signupId, 'tab-rooms' ));
        exit();
    }

    /**
     * @author Povstyanoy
     */
    function declineRoommate( $signupId, $room_id_to_decline ) 
    {
        $signupDetails = $this->_getSignupDetails($signupId);
        $this->Access->checkAccess('Signup', 'r', $signupDetails['Signup']['user_id']);

        //Get a roommate to decline
        $this->SignupRoommate->recursive = -1;
        $roommate = $this->SignupRoommate->find(
            'first', array( 'conditions' => array(       'user_id' => $signupDetails['Signup']['user_id']
                                                                                        , 'id' => $room_id_to_decline
                                                                                        , 'status'    => 'Pending' ))
        );
        if (!empty( $roommate ) ) {
            $this->SignupRoommate->id = $room_id_to_decline;
            $this->SignupRoommate->saveField('status', 'Declined');
        }

        $this->Session->setFlash("You declined a request.", 'flash_success');
        return $this->redirect(array( 'controller'=> 'signups', 'action' => 'signupDetails', $signupId ));
        exit();
    }

    /**
     * @author Povstyanoy
     */
    function _getSignupDetails( $signupId = null, $isBoolean = false, $recursive = 1 ) 
    {
        if (!$signupId ) {
            if (!$isBoolean) {
                $this->Session->setFlash('Incorrect ID', 'flash_error');
                return $this->redirect('/');
                exit();
            } else {
                return false;
            }
        }

        $this->Signup->recursive = $recursive;
        $signupDetails = $this->Signup->find('first', array('conditions' => array( 'Signup.id' => $signupId )));

        if (empty($signupDetails)) {
            if (!$isBoolean ) {
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
            $packageInformation = $this->Package->find('first', array('conditions'=>array('Package.id'=>$signupDetails['Packagedetails']['package_id'])));
            $signupDetails['Package'] = $packageInformation['Package'];
        }

        return $signupDetails;
    }


    function inviteMate() 
    {
        Configure::write('debug', '0');
        $this->layout = false;

        if (!$this->RequestHandler->isAjax() ) {
            echo "Incorrect request.";
            exit();
        }

        //Get data from form
        $signupId = (int)$this->request->params['form']['signUpId'];
        $user_id = (int)$this->request->params['form']['user_id'];

        //Get details about signup
        $signupDetails = $this->_getSignupDetails($signupId, true);

        if ($signupDetails === false ) {
            echo "Parameters is wrong.";
            exit();
        }

        if (empty( $user_id ) ) {
            echo "User id is wrong.";
            exit();
        }

        //Check access
        $accessGranted = $this->Access->getAccess('Signup', 'r', $signupDetails['Signup']['user_id']);
        if ($accessGranted === false ) {
            echo "Access denied.";
            exit();
        }

        $my_user_id = $signupDetails['Signup']['user_id'];
        $model     = $signupDetails['Signup']['model'];
        $model_id= $signupDetails['Signup']['model_id'];

        $checkUser = $this->checkFindedMate($user_id, $signupDetails);

        if (!empty( $checkUser ) ) {
            echo $checkUser;
            exit;
        }

        //find room id for current owner
        $CreatorRoomId = $this->SignupRoommate->_getMyRoomId(
            $signupDetails['Signup']['user_id'], $model, $model_id 
        );
        //Quantity of users in room
        $mates_in_room = $this->SignupRoommate->currentQuantityOfPeople($CreatorRoomId);
        //Check, Is room full of people?
        if ($signupDetails['Package']['people_in_room'] <= $mates_in_room ) {
            echo "You can not add mates.The Room is full.";
            exit();
        }

        //Accept roommate
        if (!empty( $CreatorRoomId ) ) {
            $create_record = array();
            $create_record['SignupRoommate']['user_id'] = $user_id;
            $create_record['SignupRoommate']['status'] = "Pending";
            $create_record['SignupRoommate']['room_id'] = $CreatorRoomId;
            $this->SignupRoommate->create();
            $result = $this->SignupRoommate->save($create_record);
            unset( $create_record );

            //change status of room. If room is full then status is Approved
            $this->SignupRoom->id = $CreatorRoomId;
            if ($signupDetails['Package']['people_in_room'] > $mates_in_room ) {
                $this->SignupRoom->saveField('status', 'Pending');
            } else {
                $this->SignupRoom->saveField('status', 'Approved');
                $this->SignupRoom->saveField('approve_time', date("Y-m-d"));
                //Decline another users with status pending in this room
                $this->SignupRoommate->declinePendings($CreatorRoomId);
            }

            if ($result ) {
                $this->Signup->recursive = 1;
                $checkUserSingup = $this->Signup->find(
                    'first', array( 'conditions' => array(
                                                                          'user_id' => $user_id
                                                                        , 'model'    => $signupDetails['Signup']['model']
                                                                        , 'model_id'=> $signupDetails['Signup']['model_id'] )
                    )
                );

                //Sending email				
                if (!empty($checkUserSingup)) {
                    $result = $this->sendMailMessage(
                        'RequestRoommate', array(
                                         '{FNAME}'         => $signupDetails['User']['firstname'],
                                         '{LNAME}'         => $signupDetails['User']['lastname'],
                                       '{LINK}'          => "http://{$_SERVER['HTTP_HOST']}/signups/signupDetails/{$signupDetails['Signup']['id']}"
                                          ),
                        $checkUserSingup['User']['email']
                    );
                } else {
                    $result = $this->sendMailMessage(
                        'RequestRoommateWithoutSignup', array(
                                         '{FNAME}'         => $signupDetails['User']['firstname'],
                                         '{LNAME}'         => $signupDetails['User']['lastname'],
                        '{EVENT_NAME}'         => $signupDetails['Event']['name'],
                                       '{EVENT_LINK}'          => "http://" . $_SERVER['HTTP_HOST'] . "/events/" . $signupDetails['Event']['slug'] . "/" . $signupDetails['Event']['slug']
                                          ),
                        $checkUserSingup['User']['email']
                    );    
                }            
                if (!$result) {
                    $this->logErr('error occured while sendinig password change email');
                }
                echo "0";
                exit();
            } else {
                $errorMessage = "Can not invite this person. Try again later.";
            }
        }
        exit();
    }

    /**
     * Ajax request to find roommates by criteria
     *
     * @param  int    $this->request->params['form']['signUpId']
     * @param  string $this->request->params['form']['email']
     * @param  string $this->request->params['form']['lastname']
     * @param  string $this->request->params['form']['nickname']
     * @author Povstyanoy
     */
    function findAllRoommate() 
    {
        Configure::write('debug', '0');
        $this->layout = false;

        if (!$this->RequestHandler->isAjax() ) {
            echo "Incorrect request.";
            exit();
        }

        $diverrorb = '<div id="finderror">';
        $diverrore = '</div>';

        //Get data from form
        $signupId = $this->request->params['form']['signUpId'];
        $email = trim($this->request->params['form']['email']);
        $lastname = trim($this->request->params['form']['lastname']);
        $nickname = trim($this->request->params['form']['nickname']);

        //Get details about signup
        $signupDetails = $this->_getSignupDetails($signupId, true);

        if ($signupDetails === false ) {
            echo $diverrorb . "Parameters are wrong" . $diverrore;
            exit();
        }

        if (empty( $email ) && empty( $lastname ) && empty( $nickname ) ) {
            echo $diverrorb . "Criteria is empty" . $diverrore;
            exit();
        }

        //Check access
        $accessGranted = $this->Access->getAccess('Signup', 'r', $signupDetails['Signup']['user_id']);
        if ($accessGranted === false ) {
            echo $diverrorb . "Access denied" . $diverrore;
            exit();
        }

        $my_user_id = $signupDetails['Signup']['user_id'];
        $model     = $signupDetails['Signup']['model'];
        $model_id= $signupDetails['Signup']['model_id'];

        //Validate email address
        $isValidEmail = false;
        if (!empty( $email ) ) {
            $objValidate = new Validation();
            $isValidEmail = $objValidate->email($email);
            unset( $objValidate );

            if (!$isValidEmail) {
                echo $diverrorb . "Email is incorrect" . $diverrore;
                exit();
            }
        }

        if (!$this->RequestHandler->isAjax() ) {
            echo $diverrorb . "Incorrect request" . $diverrore;
            exit();
        }

        //find room id for current owner
        $CreatorRoomId = $this->SignupRoommate->_getMyRoomId($signupDetails['Signup']['user_id'], $model, $model_id);
        //Quantity of users in room
        $mates_in_room = $this->SignupRoommate->currentQuantityOfPeople($CreatorRoomId);
        //Check, Is room full of people?
        if ($signupDetails['Package']['people_in_room'] <= $mates_in_room ) {
            echo $diverrorb . "You can not add mates.The Room is full." . $diverrore;
            exit();
        }
        //Check your payments
        if ($signupDetails['Signup']['status'] != 'paid' ) {
            echo $diverrorb . "Please pay full price to invite mates." . $diverrore;
            exit();
        }

        //Create criteria
        $criteria = array();
        if($isValidEmail ) {
            $criteria['email'] = $email;
        }

        if(!empty( $lastname ) ) {
            $criteria['lastname'] = $lastname;
        }

        if(!empty( $nickname ) ) {
            $criteria['lgn'] = $nickname;
        }

        $users = array();
        if (!empty( $criteria) ) {
            //Exclude my Id
            $criteria[] = "id <> $my_user_id";
            //Get list of users by criteria
            $this->Signup->User->recursive = -1;
            $users = $this->Signup->User->find('all', array( 'conditions' => $criteria ));
        }

        if (empty( $users ) ) {
            echo $diverrorb . "Can not find user(s) matching your criteria." . $diverrore;
            exit();
        }

        foreach($users as $index => $user) {
            $users[$index]['User']['checked_status'] = 
            $this->checkFindedMate($user['User']['id'], $signupDetails);
            if (!$isValidEmail) {
                $users[$index]['User']['email'] = "";
            }
            $users[$index]['User']['lastname'] = strtoupper(substr($users[$index]['User']['lastname'], 0, 1)).".";
        }
        $this->set('users', $users);
        $this->set('signupId', $signupId);        
    }

    function checkFindedMate( $user_id, $signupDetails) 
    {
        //Check user to signup
         $checkUserSingup = $this->Signup->find(
             'first', array( 'conditions' => array(
                                                                      'Signup.user_id' => $user_id
                                                                    , 'Signup.model'    => $signupDetails['Signup']['model']
                                                                    , 'Signup.model_id'=> $signupDetails['Signup']['model_id'] )
             )
         );

         //check user is not accepted already
         /*Added by vovich*/
         $userAccepted = $this->SignupRoommate->checkIsUserAccepted($signupDetails['Signup']['model'], $signupDetails['Signup']['model_id'], $user_id);
         if (!empty($userAccepted) ) {
             return "This User already has a roommate.";
            }
            /*EOF*/
            /*$this->SignupRoommate->recursive = -1;
            $userAccepted = $this->SignupRoommate->find('all', array( 'conditions' => array(   'user_id' => $user_id,'room_id' =>$roomId
																							, 'status'	=> 'Accepted' )
            ));

            if ( !empty( $userAccepted ) ) {
            return "The User accepted already.";
            }*/
            /*
            if( $this->SignupRoommate->isUserAlreadyRequestedByMe( 	  $signupDetails['Signup']['user_id']
																	, $user_id
																	, $signupDetails['Signup']['model']
																	, $signupDetails['Signup']['model_id'] ) ) {
            return "You already requested this roommate.";
            }
            if ( empty ( $checkUserSingup )) {
            return "User has not signed up.";
            }
            //Check user to pay full price
            if ($checkUserSingup['Signup']['status'] != 'paid') {
            return "User has not paid full price.";
            }
            //check user packages for matching
            if ( $signupDetails['Packagedetails']['package_id'] != $checkUserSingup['Packagedetails']['package_id'] ) {
            return "User has a different package: ".
            $signupDetails['Packagedetails']['package_id'].' vs '.
            $checkUserSingup['Packagedetails']['package_id'];
            }
            */
            return "";
    }


    /** 
     * THIS FUNCTION SHOULD BE REFACTORED AND TESTED FOR SIGNUP WITH PAID FOR TEAM
     * Delete function for User
     *
     * @author Povstyanoy
     * @param  int $signupId
     */
    function delete( $id = null, $signupId = null, $userRole = 'creator' ) 
    {
        
        if ($userRole == 'creator') {
            $backUrl = '/signups/signupDetails/' . $signupId . '/tab-rooms'; 
        } else {
            $backUrl = '/signups/signupDetailsTeammate/' . $signupId . '/tab-rooms';    
        }
        
        $signupDetails = $this->_getSignupDetails($signupId);

        //$this->Access->checkAccess('SignupRoom', 'd', $signupDetails['Signup']['user_id'] );

        //Getting Room
        $this->SignupRoommate->recursive = 1;
        $signupRoom = $this->SignupRoommate->find(
            'first', array('conditions' => array(
                                                                                  'SignupRoommate.user_id' => $signupDetails['Signup']['user_id']
                                                                                , 'SignupRoommate.status' => 'Creator'
                                                                                , 'Room.model' => $signupDetails['Signup']['model']
                                                                                , 'Room.model_id' => $signupDetails['Signup']['model_id']
                                                                                , 'Room.status <> \'Deleted\'' )
                                                                )
        );
        //pr($signupRoom);	exit;
        //pr($signupRoom);
        //exit;
        if (empty( $signupRoom['SignupRoommate']['room_id'] ) ) {
            $this->Session->setFlash("You dont have a room", 'flash_error');
            return $this->redirect($backUrl);
            exit();
        }

        //Get who request my room and accepted it
        $this->SignupRoommate->recursive = -1;
        $roommateRequests = $this->SignupRoommate->find(
            'all', array( 'conditions' => array(       'room_id' => $signupRoom['SignupRoommate']['room_id']
                                                                                                    , 'status'    => 'Accepted'
                                                                                                    , 'user_id <> ' . $signupDetails['Signup']['user_id'] )
                                                                        )
        );

        if ($signupRoom['SignupRoommate']['status'] == 'Approved' ) {
            $this->Session->setFlash("Your room is approved. You can't delete the room.", 'flash_error');
            return $this->redirect($backUrl);
            exit();
        }

        if ($signupRoom['SignupRoommate']['status'] == 'Confirmed' ) {
            $this->Session->setFlash("Your room is confirmed. You can't delete the room.", 'flash_error');
            return $this->redirect($backUrl);
            exit();
        }

        if (!empty ( $roommateRequests) && !$signupDetails['Signup']['for_team']) {
            $this->Session->setFlash("Someone accepted your request. You can't delete the room.", 'flash_error');
            return $this->redirect($backUrl);
            exit();
        }

         //Decline All other requests
        //decline users, who want to my room
        $this->SignupRoommate->recursive = -1;
        $roommateToDecline = $this->SignupRoommate->find(
            'all', array( 'conditions' => array(       'room_id' => $signupRoom['SignupRoommate']['room_id']
                                                                                                        , 'status'    => 'Pending'))
        );
        //decline users, who request me
        $roommates = $this->SignupRoommate->find(
            'all', array( 'conditions' => array(
                                                                                          'user_id' => $signupDetails['Signup']['user_id']
                                                                                        , 'SignupRoommate.status'    => 'Pending' ))
        );
        $to_decline = array();
        if (!empty( $roommateToDecline ) ) {
            foreach ( $roommateToDecline as $index => $value ) {
                $to_decline[] = $value['SignupRoommate']['id'];
            }
        }
        if (!empty( $roommates ) ) {
            foreach ( $roommates as $index => $value ) {
                $to_decline[] = $value['SignupRoommate']['id'];
            }
        }

        if (!empty( $to_decline ) ) {
            $this->SignupRoommate->updateAll(array('status' => "'Declined'"), array( 'SignupRoommate.id' => $to_decline ));
        }
        unset( $roommateToDecline, $to_decline );

        //delete my room
        $this->SignupRoom->id = $signupRoom['SignupRoommate']['room_id'];
        $this->Answer->deleteAll(
            array( 'model' => 'Room_for_'.strtolower($signupDetails['Signup']['model'])
                                            , 'model_id' => $signupDetails['Signup']['model_id']
                                            , 'user_id' => $signupDetails['Signup']['user_id'] ) 
        );
        $result = $this->SignupRoom->saveField('status', 'Deleted');

        if ($result ) {
            $this->Session->setFlash("The room was deleted.", 'flash_success');
            return $this->redirect($backUrl);
            exit();
        }

        $this->Session->setFlash("The room was not deleted. Please try again.", 'flash_error');
        return $this->redirect($backUrl);
        exit();

    }



    function iWasInvited($signupID = null) 
    {
        $signupID = $this->request->params['signupID'];
        $signupDetails = $this->_getSignupDetails($signupID);
        $user_id = $signupDetails['Signup']['user_id'];

        $model     = $signupDetails['Signup']['model'];
        $model_id= $signupDetails['Signup']['model_id'];

        $this->set('isTimeOver', $this->isTimeOver($signupDetails[ $signupDetails['Signup']['model'] ]['finish_signup_date']));

        if (!$this->SignupRoommate->isRoomCreated($user_id, $signupDetails['Signup']['model'], $signupDetails['Signup']['model_id']) ) {
            $this->SignupRoommate->recursive = 0;
            $this->SignupRoommate->unbindModel(array ("belongsTo" => array('User','Room')));
            $this->SignupRoommate->bindModel(
                array ("belongsTo" => array(
                'RoomCreator' => array('className' => 'SignupRoommate',
                'foreignKey' => '',
                'conditions' => 'RoomCreator.status = "Creator" AND `SignupRoommate`.`room_id` = `RoomCreator`.`room_id`',
                'fields' => '',
                'order' => ''
                ),
                "Creator" => array('className' => 'User',
                                        'foreignKey' => '',
                                        'conditions' => '`RoomCreator`.`user_id` = Creator.id',
                                        'fields' => '',
                                        'order' => ''
                )
                )
                ) 
            );
            $roommateRequestedMe = $this->SignupRoommate->find(
                'all', array( 'conditions' => array(
                                                                                      'SignupRoommate.user_id' => $user_id
                                                                                    , 'SignupRoommate.status'    => 'Pending'))
            );
            $this->set("roommateRequestedMe", $roommateRequestedMe);
            $this->set("signupID", $signupID);
            $this->render();
            return true;
        }

        if ($this->SignupRoommate->isRoomCreated($user_id, $signupDetails['Signup']['model'], $signupDetails['Signup']['model_id'])
            && $this->SignupRoommate->isMyRoomEmpty($user_id, $model, $model_id)
            && !$this->SignupRoommate->isIAcceptedInRooms($user_id, $model, $model_id)
        ) {

            $this->SignupRoommate->recursive = 0;

            $this->SignupRoommate->unbindModel(array ("belongsTo" => array('User','Room')));
            $this->SignupRoommate->bindModel(
                array ("belongsTo" => array(
                'RoomCreator' => array('className' => 'SignupRoommate',
                'foreignKey' => '',
                'conditions' => 'RoomCreator.status = "Creator" AND `SignupRoommate`.`room_id` = `RoomCreator`.`room_id`',
                'fields' => '',
                'order' => ''
                ),
                "Creator" => array('className' => 'User',
                                        'foreignKey' => '',
                                        'conditions' => '`RoomCreator`.`user_id` = Creator.id',
                                        'fields' => '',
                                        'order' => ''
                )
                )
                ) 
            );

            $roommateRequestedMe = $this->SignupRoommate->find(
                'all', array( 'conditions' => array(
                                                                                      'SignupRoommate.user_id' => $user_id
                                                                                    , 'SignupRoommate.status'    => 'Pending'))
            );
            $this->set("signupID", $signupID);
            $this->set("roommateRequestedMe", $roommateRequestedMe);
            $this->render();
            return true;
        }

        return false;
    }

    function commonInfo($signupID = null) 
    {
        
        $signupID = $this->request->params['signupID'];
        $signupDetails = $this->_getSignupDetails($signupID);
        $user_id = $signupDetails['Signup']['user_id'];
        $model     = $signupDetails['Signup']['model'];
        $model_id= $signupDetails['Signup']['model_id'];

        if ($this->SignupRoommate->isIAcceptedInRooms($user_id, $model, $model_id) ) {
            $creator_id = $this->SignupRoommate->getRoomCreator($user_id);
            $answers = $this->Answer->getAnswers(
                'Room_for_' . strtolower($signupDetails['Signup']['model']), $signupDetails['Signup']['model_id'], $creator_id 
            );
            $this->set('answers', $answers);
            $this->set('roomInfo', $this->SignupRoommate->getRoomInfo($user_id, $signupDetails['Signup']['model'], $signupDetails['Signup']['model_id']));
            $this->render();
            return true;
        }
        if ($this->SignupRoommate->isRoomCreated($user_id, $signupDetails['Signup']['model'], $signupDetails['Signup']['model_id']) ) {
            $answers = $this->Answer->getAnswers(
                'Room_for_'.strtolower($signupDetails['Signup']['model']), $signupDetails['Signup']['model_id'], $signupDetails['Signup']['user_id'] 
            );
            $this->set('answers', $answers);
            $this->set('roomInfo', $this->SignupRoommate->getMyRoomInfo($user_id, $signupDetails['Signup']['model'], $signupDetails['Signup']['model_id']));

            $this->render();
            return true;

        }
        return false;
    }

    function createRoomView( $signupID = null ) 
    {
        $signupID = $this->request->params['signupID'];
        $signupDetails = $this->_getSignupDetails($signupID);
        $user_id = $signupDetails['Signup']['user_id'];
        $model     = $signupDetails['Signup']['model'];
        $model_id= $signupDetails['Signup']['model_id'];

        if (!$this->SignupRoommate->isRoomCreated($user_id, $signupDetails['Signup']['model'], $signupDetails['Signup']['model_id'])
            &&  !$this->SignupRoommate->isIAcceptedInRooms($user_id, $model, $model_id)
            &&    !$this->isTimeOver($signupDetails[$signupDetails['Signup']['model']]['finish_signup_date']) 
        ) {
            $this->set("signupID", $signupID);
            $this->render();
        }
        return false;
    }

    function deleteRoomView( $signupID = null ) 
    {
        $signupID = $this->request->params['signupID'];
        $signupDetails = $this->_getSignupDetails($signupID);
        $user_id = $signupDetails['Signup']['user_id'];
        $model     = $signupDetails['Signup']['model'];
        $model_id= $signupDetails['Signup']['model_id'];

        if ($this->SignupRoommate->isRoomCreated($user_id, $signupDetails['Signup']['model'], $signupDetails['Signup']['model_id'])
            &&  $this->SignupRoommate->isMyRoomEmpty($user_id, $model, $model_id) 
        ) {
            $this->set("signupID", $signupID);
            $this->render();
        }
        return false;
    }

    function findingView($signupID = null) 
    {
        $signupID = $this->request->params['signupID'];
        $signupDetails = $this->_getSignupDetails($signupID);
        $user_id = $signupDetails['Signup']['user_id'];
        //		$signupDetails = $this->_getSignupDetails ($signupID);

        $model     = $signupDetails['Signup']['model'];
        $model_id= $signupDetails['Signup']['model_id'];

        if ($this->SignupRoommate->isRoomCreated($user_id, $signupDetails['Signup']['model'], $signupDetails['Signup']['model_id'])
            &&  !$this->SignupRoommate->isMyRoomFilled(
                $user_id, $signupDetails['Package']['people_in_room'], $model, $model_id 
            ) ) {
            $this->render();
        }
        return false;
    }

    function usersWereInvited($signupID = null) 
    {
        $signupID = $this->request->params['signupID'];
        $signupDetails = $this->_getSignupDetails($signupID);
        $user_id = $signupDetails['Signup']['user_id'];
        $model     = $signupDetails['Signup']['model'];
        $model_id= $signupDetails['Signup']['model_id'];

        $myHistory = $this->SignupRoommate->myInvitedHistory($user_id, $model, $model_id);
        if ($this->SignupRoommate->isRoomCreated($user_id, $signupDetails['Signup']['model'], $signupDetails['Signup']['model_id'])
            &&  ! empty($myHistory) 
        ) {
            $this->set("myHistory", $myHistory);
            $this->render();
        }
        return false;

    }

    function isTimeOver( $checkTime ) 
    {
        $signuptime = strtotime($checkTime);//$signupDetails[$signupDetails['Signup']['model']]['finish_signup_date']
        if (!empty( $signuptime ) && strtotime(date("Y-m-d")) > $signuptime ) {
            return true;
        }
        return false;
    }
      }
?>
