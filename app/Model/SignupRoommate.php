<?php
class SignupRoommate extends AppModel
{

    var $name = 'SignupRoommate';

    var $belongsTo = array(
    'User' => array('className' => 'User',
                                  'foreignKey' => 'user_id',
                                  'dependent' => false,
                                  'conditions' => '',
                                  'fields' => '',
                                  'order' => ''
    )
    ,'Room' => array('className' => 'SignupRoom',
                                  'foreignKey' => 'room_id',
                                  'dependent' => false,
                                  'conditions' => '',
                                  'fields' => '',
                                  'order' => ''
    ) 
    );

    function getMyRoomInfo( $user_id = null, $model = null, $modelId = null ) 
    {
         $this->unbindModel(array ( "belongsTo" => array('User', 'Room')));
        $this->bindModel(
            array ("belongsTo" => array(
            'RoomCreator' => array('className' => 'SignupRoommate',
                                        'foreignKey' => '',
                                        'conditions' => 'RoomCreator.status = "Creator" AND `SignupRoommate`.`room_id` = `RoomCreator`.`room_id`',
                                        'fields' => '',
                                        'order' => ''
            ),
            'Room' => array('className' => 'SignupRoom',
                                        'foreignKey' => '',
                                        'conditions' => '`Room`.`id` = `RoomCreator`.`room_id` AND `Room`.`status` <> "Deleted"',
                                        'fields' => '',
                                        'order' => ''
            ),
            "Creator" => array('className' => 'User',
                                        'foreignKey' => '',
                                        'conditions' => '`RoomCreator`.`user_id` = Creator.id',
                                        'fields' => '',
                                        'order' => ''
            ),
            'Address' => array('className' => 'Address',
                                        'foreignKey' => '',
                                        'conditions' => '`Address`.`model` = \'User\' AND `Address`.`model_id` = `Creator`.`id` AND `Address`.`label` = \'Home\'',
                                        'fields' => '',
                                        'order' => ''
            ),
            'Phone' => array('className' => 'Phone',
                                        'foreignKey' => '',
                                        'conditions' => '`Phone`.`model` = \'User\' AND `Phone`.`model_id` = `Creator`.`id` AND `Phone`.`type` = \'Home\'',
                                        'fields' => '',
                                        'order' => ''
            ),
                    
            )
            ) 
        );
        $this->SignupRoommate->recursive = 1;
        $room = $this->find(
            'first', array('conditions' => array(
                                                      'SignupRoommate.user_id' => $user_id
                                                    , 'SignupRoommate.status' => 'Creator'
                                                    ,'Room.model' =>$model
                                                    ,'Room.model_id' => $modelId
                                                    , 'Room.status <> \'Deleted\''))
        );
                                            
        if(!empty($room['Room'] )) {
            //	return $room;
        }

        if (!empty($room['Room']['id'])) {
            $roommates = $this->query(
                "
				SELECT *
				FROM `signup_roommates` AS `SignupRoommate`
				LEFT JOIN `users` AS `User` ON `SignupRoommate`.`user_id` = `User`.`id`
				LEFT JOIN `addresses` AS `Address` ON (`Address`.`model` = 'User' AND `Address`.`model_id` = `User`.`id` AND `Address`.`label` = 'Home' )
				LEFT JOIN `provincestates` AS `Provincestate` ON (`Address`.`provincestate_id` = `Provincestate`.`id`)
				LEFT JOIN `phones` AS `Phone` ON (`Phone`.`model` = 'User' AND `Phone`.`model_id` = `User`.`id` AND `Phone`.`type` = 'Home' )
				WHERE `SignupRoommate`.`room_id` = " . $room['Room']['id'] . " AND `SignupRoommate`.`status` = 'Accepted'
				GROUP BY `User`.`id`;
			"
            );
            $room['Mates'] = $roommates;
        }

        if(!empty($room['Room'] )) {
            return $room;
        }

        return false;

    }

    function getUserRoomInfo( $user_id ) 
    {

        $room = $this->find(
            'first', array('conditions' => array(
                                                      'SignupRoommate.user_id' => $user_id
                                                    , 'SignupRoommate.status' => 'Creator'
                                                    , 'Room.status <> \'Deleted\''))
        );

        if (!empty( $room ) ) {
            return $room;
        }
        return false;
    }

    function getRoomInfo( $user_id, $model, $model_id ) 
    {
        $this->unbindModel(array ("belongsTo" => array('User', 'Room')));
        $this->bindModel(
            array ("belongsTo" => array(
            'RoomCreator' => array('className' => 'SignupRoommate',
                                        'foreignKey' => '',
                                        'conditions' => 'RoomCreator.status = "Creator" AND `SignupRoommate`.`room_id` = `RoomCreator`.`room_id`',
                                        'fields' => '',
                                        'order' => ''
            ),
            'Room' => array('className' => 'SignupRoom',
                                        'foreignKey' => '',
                                        'conditions' => '`Room`.`id` = `RoomCreator`.`room_id`',
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
        $this->recursive = 1;
        $room = $this->find(
            'first', array('conditions' => array(
                                                      'SignupRoommate.user_id' => $user_id
                                                    , 'SignupRoommate.status' => 'Accepted'
                                                    , 'Room.status <> \'Deleted\''
                                                    , 'Room.model' => $model
                                                    , 'Room.model_id' => $model_id))
        );
                                                    

        $this->bindModel(
            array ("belongsTo" => array(
                                            'Room' => array('className' => 'SignupRoom',
                                                                  'foreignKey' => 'room_id',
                                                                  'dependent' => false,
                                                                  'conditions' => '',
                                                                  'fields' => '',
                                                                  'order' => ''
                                                            )
                                            ,'User' => array('className' => 'User',
                                                                  'foreignKey' => 'user_id',
                                                                  'dependent' => false,
                                                                  'conditions' => '',
                                                                  'fields' => '',
                                                                  'order' => ''
                                            )

             ) ) 
        );

        /*		$this->recursive = 1;
        $roommates = $this->find('all', array('conditions' => array(
													  'SignupRoommate.room_id' => $room['Room']['id']
													, 'SignupRoommate.status' => 'Accepted')));*/
        if (!empty($room['Room']['id'])) {
            $roommates = $this->query(
                "
				SELECT *
				FROM `signup_roommates` AS `SignupRoommate`
				LEFT JOIN `users` AS `User` ON `SignupRoommate`.`user_id` = `User`.`id`
				WHERE `SignupRoommate`.`room_id` = " . $room['Room']['id'] . " AND `SignupRoommate`.`status` = 'Accepted';
			"
            );
            $room['Mates'] = $roommates;
        }

        if (!empty( $room ) ) {
            return $room;
        }
        return false;
    }


    function declinePendings( $room_id = null) 
    {
        if (empty($room_id)) {
            return false;
        }
        //Decline another requsts of users
        $this->recursive = -1;
        $declineAnotherRequests = $this->find(
            'all', array( 'conditions' => array(   'room_id' => $room_id
                                                                                                , "status = 'Pending'" )
            )
        );

        if (!empty($declineAnotherRequests)) {
            $to_decline = array();
            foreach ( $declineAnotherRequests as $toDecline ) {
                $to_decline[] = $toDecline['SignupRoommate']['id'];
            }
            $this->updateAll(array('status' => 'Declined'), array( 'id' => $to_decline ));
        }
        return true;
    }

    function isRoomCreated( $user_id, $model = "", $model_id = 0  ) 
    {
        $this->unbindModel(array ("belongsTo" => array('User', 'Room')));
        $this->bindModel(
            array ("belongsTo" => array(
                                            'Room' => array('className' => 'SignupRoom',
                                                                  'foreignKey' => 'room_id',
                                                                  'dependent' => false,
                                                                  'conditions' => '',
                                                                  'fields' => '',
                                                                  'order' => ''
                                                            )
                            ) ) 
        );

        $this->recursive = 1;

        $room = $this->find(
            'first', array('conditions' => array(
                                                      'SignupRoommate.user_id' => $user_id
                                                    , 'SignupRoommate.status' => 'Creator'
                                                    , 'Room.status <> \'Deleted\''
                                                    ,'Room.model' => $model
                                                    ,'Room.model_id' => $model_id
                                                    ))
        );

        if (!empty( $room ) ) {
            return true;
        }
        return false;
    }

    function _getMyRoomId( $user_id, $model = "", $model_id = 0 ) 
    {
        if (!empty($user_id)) {
            $room = $this->query(
                "
				SELECT *
				FROM `signup_roommates` AS `SignupRoommate`
				LEFT JOIN `signup_rooms` AS `Room` ON `SignupRoommate`.`room_id` = `Room`.`id`
				WHERE `SignupRoommate`.`user_id` = $user_id
						AND `SignupRoommate`.`status` = 'Creator'
						AND `Room`.`status` <> 'Deleted'
						AND `Room`.`model` = '$model'
						AND `Room`.`model_id` = $model_id
				LIMIT 1;
			"
            );
            if (!empty( $room[0]['SignupRoommate']['room_id'] ) ) {
                return $room[0]['SignupRoommate']['room_id'];
            }
        }
        return 0;

    }

    function isIAcceptedInRooms( $user_id = null, $model = '', $model_id = 0 ) 
    {
        if (!empty($user_id)) {
            $room = $this->query(
                "
				SELECT *
				FROM `signup_roommates` AS `SignupRoommate`
				LEFT JOIN `signup_rooms` AS `Room` ON `SignupRoommate`.`room_id` = `Room`.`id`
				WHERE `SignupRoommate`.`user_id` = $user_id
						AND `SignupRoommate`.`status` = 'Accepted'
						AND `Room`.`status` <> 'Deleted'
						AND `Room`.`model` = '$model'
						AND `Room`.`model_id` = $model_id
				LIMIT 1;
			"
            );
            if (!empty( $room[0]['SignupRoommate']['room_id'] ) ) {
                return true;
            }
        }
        return false;
    }

    function myInvitedHistory( $user_id, $model, $model_id ) 
    {
        $room_id = $this->_getMyRoomId($user_id, $model, $model_id);
        if (!empty($room_id)) {
            $users = $this->query(
                "
				SELECT *
				FROM `signup_roommates` AS `SignupRoommate`
				LEFT JOIN `users` AS `User` ON `SignupRoommate`.`user_id` = `User`.`id`
				WHERE `SignupRoommate`.`room_id` = $room_id
						AND `SignupRoommate`.`status` IN ('Pending','Declined');
			"
            );
            return $users;
        }
        return array();
    }

    function isRoomEmpty( $room_id ) 
    {
        $this->cacheQueries = false;
        $room = $this->find(
            'first', array('conditions' => array(
                                                      'SignupRoommate.room_id' => $room_id
                                                    , 'SignupRoommate.status' => 'Accepted'))
        );
        if (!empty( $room ) ) {
            return false;
        }
        return true;
    }

    function isMyRoomEmpty( $user_id, $model, $model_id ) 
    {
        $myRoomId = $this->_getMyRoomId($user_id, $model, $model_id);
        if (!empty($myRoomId)) {
            return $this->isRoomEmpty($myRoomId);
        }
        return false;
    }

    function isMyRoomFilled( $user_id, $people_in_package, $model, $model_id ) 
    {
        $myRoomId = $this->_getMyRoomId($user_id, $model, $model_id);

        if (!empty( $myRoomId )) {
            return $this->isRoomFilled($myRoomId, $people_in_package);
        }
        return true;
    }

    function isRoomFilled( $room_id, $people_in_package ) 
    {
        $people_in_package = (int)$people_in_package;
        if ($people_in_package > $this->currentQuantityOfPeople($room_id) ) {
            return false;
        }
        return true;
    }

    /**
     * Find quantity of people in room at this moment
     *
     * @param  int $room
     * @return int
     * @author Povstyanoy
     */
    function currentQuantityOfPeople( $room_id = null) 
    {
        if (empty($room_id)) {
            return 0;
        }
        //Quantity of users in room
        $this->recursive = -1;
        $mates_in_room = $this->find(
            'count', array( 'conditions' => array(    'room_id' => $room_id
                                                                                        , "(status = 'Accepted' OR status = 'Creator')" ))
        );
        return (int)$mates_in_room;
    }

    function isUserAlreadyRequestedByMe( $my_user_id, $user_id, $model, $model_id ) 
    {
        $room_id = $this->_getMyRoomId($my_user_id, $model, $model_id);
        if (!empty($room_id)) {
            $user = $this->find(
                'first', array('conditions' => array(
                                                          'SignupRoommate.user_id' => $user_id
                                                        , 'SignupRoommate.room_id' => $room_id
                                                        , 'SignupRoommate.status' => 'Pending'))
            );
            if (!empty( $user ) ) {
                return true;
            }
        }
        return false;
    }

    function getRoomCreator( $user_id ) 
    {
        if (!empty($user_id)) {
            $room = $this->query(
                "
				SELECT Creator.user_id
				FROM `signup_roommates` AS `SignupRoommate`
				LEFT JOIN `signup_rooms` AS `Room` ON `SignupRoommate`.`room_id` = `Room`.`id`
				LEFT JOIN `signup_roommates` AS `Creator` ON (`SignupRoommate`.`room_id` = `Creator`.`room_id` AND `Creator`.`status` = 'Creator')
				WHERE `SignupRoommate`.`user_id` = $user_id
						AND `Room`.`status` <> 'Deleted'
				LIMIT 1;
			"
            );
            if (!empty( $room[0]['Creator']['user_id'] ) ) {
                return $room[0]['Creator']['user_id'];
            }
        }
        return 0;

    }

    /**
     *  function for getting count of rooms for the current user and current model (Rooms status Created and Confirmed, rommate status Creator and Accepted)
     * @author vovich
     * @param string $model   model name 'Tournament','Event'
     * @param int    $modelID id of the model
     * @param int    $userID  user id
     * @return int rooms count
     */
    function getCountRooms($model = null, $modelID = null, $userID = null) 
    {

        $sql = "SELECT COUNT(*) as cnt FROM signup_roommates as SignupRoommate " .
                           "  INNER JOIN signup_rooms as SignupRoom " .
                           " ON SignupRoommate.status IN ('Creator','Accepted')  " .
                           " AND SignupRoommate.user_id=".$userID."  " .
                           " AND SignupRoommate.room_id=SignupRoom.id " .
                           " AND SignupRoom.model='".$model."' " .
                           " AND SignupRoom.model_id=".$modelID." " .
                           " AND SignupRoom.status IN ('Confirmed','Approved') ";

        $result = $this->query($sql);

        return $result[0][0]['cnt'];

    }
    
    function isAllConfCodeInRoomFilled( $roommate_id = null ) 
    {
        if ($roommate_id == null ) {
            return false;
        }

        $sql = "SELECT id 
				FROM signup_roommates
				WHERE room_id = (	SELECT room_id 
									FROM signup_roommates 
									WHERE id = $roommate_id	)
						AND confirmation_code IS NULL;";

        $result = $this->query($sql);
        
        if (!empty( $result ) ) {
            return false;
        }
        
        return true;
    }

    function setRoomStatus( $roommate_id = null, $status = "Confirmed" ) 
    {
        if ($roommate_id == null ) {
            return false;
        }

        $sql = "UPDATE signup_rooms 
				SET signup_rooms.status = '$status' 
				WHERE signup_rooms.id = (	SELECT room_id 
								FROM signup_roommates 
								WHERE signup_roommates.id = $roommate_id )";

        $result = $this->query($sql);
        
        if (!empty( $result ) ) {
            return $result;
        }
    }

    function getEmailsOfRoomUsers( $room_id = null) 
    {
        if ($room_id == null ) {
            return false;
        }

        $sql = "
			SELECT User.id as user_id
				, User.firstname AS firstname
				, User.lastname AS lastname
				, User.email AS email
				, Roommate.status AS status
			from signup_roommates AS Roommate
			LEFT JOIN users AS User ON User.id = Roommate.user_id
			WHERE Roommate.status IN ('Accepted', 'Creator')
				AND Roommate.room_id = $room_id;
		";

        return $this->query($sql);
    }
    
    /**
 * Checking is user already in any room for such tournevent
 * @param unknown_type $model
 * @param unknown_type $modelId
 * @param unknown_type $userId
 * @author vovich
 * @return unknown_type
 */
    function checkIsUserAccepted($model = null, $modelId = null, $userId = null) 
    {
    
        $sql = "SELECT SignupRoom.id FROM signup_rooms AS SignupRoom 
                                            INNER JOIN signup_roommates AS SignupRoommate 
                                            ON SignupRoom.id = SignupRoommate.room_id 
                                            AND SignupRoommate.user_id = $userId 
                                            AND model = '$model' 
                                            AND model_id= $modelId 
                                            AND SignupRoommate.status='Accepted'
                                            AND SignupRoom.status='Approved'";
    
        $result = $this->query($sql);
    
        return $result;
    }    
    /**
    * @author Skinny
    * Accept the invitation, assuming that the user is pending
    */
    function acceptRoomInvitation($userID,$model,$modelID) 
    {
        $roommate = $this->find(
            'first', array('conditions'=>array(
                'SignupRoommate.user_id'=>$userID,
                'SignupRoommate.status <>'=>'Declined',
                'Room.model'=>$model,
                'Room.model_id'=>$modelID,
                'Room.status <>'=>'Deleted'
                ),
            'contain'=>array('Room'))
        );  
        if (!empty($roommate['Room']['id'])) {                           
            if (!empty($roommate)) {              
                if ($roommate['SignupRoommate']['status'] == 'Pending') {
                    $roommate['SignupRoommate']['status'] = 'Accepted';
                    $this->save($roommate['SignupRoommate']);    
                }
            }
            //Now, find all the roommates that are accepted/creator. If count(roommates)=expected count, complete the room
            $this->recursive = -1;
            $roommatesCount = $this->find(
                'count', array('conditions'=>array(
                'room_id'=>$roommate['Room']['id'],
                'status'=>array('Creator','Accepted')
                ))
            );
            if ($roommatesCount == $roommate['Room']['people_in_room']) {
                $roommate['Room']['status'] = 'Approved';
                $roommate['Room']['approve_time'] = date("Y-m-d");
                $this->Room->save($roommate['Room']);
            }
        }
        return 'ok';
    }
}
?>
