<?php
class SignupRoom extends AppModel
{

    var $name = 'SignupRoom';
    var $actsAs = array ('Containable');
    
    
    var $belongsTo = array(
    'Event' => array(
        'className' => 'Event',
        'foreignKey' => 'model_id',
        'order' => '',
        'conditions' => 'SignupRoom.model = "Event"'
        )
    );
    var $hasOne = array(
                        'Creator' =>  array(
                                   'className' => 'SignupRoommate',
                                   'foreignKey' => 'room_id',
                                   'order' => '',
                                   'conditions' => 'Creator.status = "Creator"'
                          )
                        ,  'User' =>  array(
                                   'className' => 'User',
                                   'foreignKey' => '',
                                   'order' => '',
                                   'conditions' => 'Creator.user_id = User.id'
                          )
    );


    function getCompletedRooms($model=null, $model_id=null, $user_id=null)
    {

        $sql = "SELECT r.id, " .
        "   r.status, " .
        "   rm.user_id," .
        " rm.status
					FROM signup_rooms r
					INNER JOIN signup_roommates rm on rm.room_id = r.id
					WHERE r.status in ('Approved','Confirmed')
					AND rm.status in ('Creator','Accepted')
					AND rm.user_id = $user_id
					AND r.model='$model'
					AND r.model_id=$model_id
					";

        return $this->query($sql);

    }
    function getSignupRooms($users, $model, $modelId) 
    {        
        if (is_array($users)) {
            $userList = implode(',', $users); 
        } else {
            $userList = $users;
        }
           $sql = 
        "SELECT * FROM signup_rooms AS rooms 
		LEFT JOIN signup_roommates AS roommates ON rooms.id = roommates.room_id
		LEFT JOIN signup_roommates AS roommates_select ON rooms.id = roommates_select.room_id
		LEFT JOIN users ON users.id = roommates.user_id
		WHERE rooms.model = '" . $model . "' AND rooms.model_id = " . $modelId . " AND rooms.status <> 'Deleted' AND roommates.status <> 'Declined' AND roommates_select.status <> 'Declined' AND roommates_select.user_id IN(" . $userList .")";
        
        $getRooms = $this->query($sql);
        //pr($getRooms);
        //exit;
        $rooms = array();
        $AnswerObject = ClassRegistry::init('Answer');
        if (!empty($getRooms)) {
            foreach ($getRooms as $getRoom) {
                if (!isset($rooms[$getRoom['rooms']['id']])) {
                    $rooms[$getRoom['rooms']['id']] = $getRoom['rooms'];
                }
                $answers = $AnswerObject->getAnswers('Room_for_' . strtolower($model), $modelId, $getRoom['users']['id']);
                if (!empty($answers)) {
                    $rooms[$getRoom['rooms']['id']]['answers'] = $answers;
                }
                $rooms[$getRoom['rooms']['id']]['users'][$getRoom['users']['id']] = $getRoom['users']['id'];
                $rooms[$getRoom['rooms']['id']]['roommates'][$getRoom['roommates']['id']]['roommate'] = $getRoom['roommates'];
                $rooms[$getRoom['rooms']['id']]['roommates'][$getRoom['roommates']['id']]['user'] = $getRoom['users'];    
            }
        }
        //pr($rooms);
        //exit;									


        return $rooms;

    }

    /**
     * Getting people in room
     * @author Alex
     */
    function peopleInRoom( $model=null, $model_id=null, $user_id=null ) 
    {

        $sql = "SELECT packages.people_in_room AS people
				FROM signups AS Signup
				LEFT JOIN packagedetails ON Signup.packagedetails_id = packagedetails.id
				LEFT JOIN packages ON packagedetails.package_id = packages.id
				WHERE Signup.user_id = $user_id AND Signup.model = '$model' AND Signup.model_id = $model_id ";
        $result = $this->query($sql);

        if (empty($result)) {
            return 0;
        } else {
            return (int)$result[0]['packages']['people'];
        }
    }

    function paginate( $conditions = null, $fields = null, $order = null, $limit = null, $page = 1, $recursive = null ) 
    {

        if ($page <= 1) {
            $paging_sql = " LIMIT $limit;";
        } else {
            $paging_sql = " LIMIT " . $limit * ( $page - 1 ) . ", $limit;";
        }

        if (empty($conditions)) {
            $conditions = "1=1";
        } elseif (is_array($conditions)) {
            foreach($conditions as $key => $value) {

                if (preg_match("/^\d+$/", $key) ) {
                    $temp[]= $value;
                } else {
                    $temp[]= "$key = '$value'";
                }
            }
            $conditions = implode(" AND ", $temp);
        }


        if (empty( $order ) ) {
            $order = "SignupRoom.id DESC";
        } elseif (is_array($order)) {
            $temp = array();
            foreach($order as $key => $value) {
                $temp[]= "$key " . strtoupper($value);
            }
            $order = implode(" AND ", $temp);
        }
        /**
* Probably This code used in casinos and room index page only , and therefore it can be chaged without warnings
* If not uncomment underline code and comment another part.
*
* @var mixed
*/
        /*
        $query = "
        SELECT 	UserCreator.lgn
        , Tournament.name
        , Event.name
        , SignupRoom.model
        , SignupRoom.id
        , SignupRoom.status
        FROM `signup_rooms` AS `SignupRoom`
        LEFT JOIN `tournaments` AS `Tournament` ON (`SignupRoom`.`model` = 'Tournament' AND `SignupRoom`.`model_id` = `Tournament`.`id`)
        LEFT JOIN `events` AS `Event` ON (`SignupRoom`.`model` = 'Event' AND `SignupRoom`.`model_id` = `Event`.`id`)
        LEFT JOIN `signup_roommates` AS `Creator` ON (`Creator`.`status` = 'Creator' AND `Creator`.`room_id` = `SignupRoom`.`id`)
        LEFT JOIN `users` AS `UserCreator` ON (`Creator`.`user_id` = `UserCreator`.`id`)
        WHERE " . $conditions . "
        ORDER BY $order
        $paging_sql";
        */
        $query = "
            SELECT     UserCreator.lgn
                    , Tournament.name
                    , Event.name
                    , SignupRoom.model
                    , SignupRoom.id
                    , SignupRoom.status
            FROM `signup_rooms` AS `SignupRoom`
            LEFT JOIN `tournaments` AS `Tournament` ON (`SignupRoom`.`model` = 'Tournament' AND `SignupRoom`.`model_id` = `Tournament`.`id`)
            LEFT JOIN `events` AS `Event` ON (`SignupRoom`.`model` = 'Event' AND `SignupRoom`.`model_id` = `Event`.`id`)
            LEFT JOIN `signup_roommates` AS `Creator` ON (`Creator`.`room_id` = `SignupRoom`.`id`)
            LEFT JOIN `users` AS `UserCreator` ON (`Creator`.`user_id` = `UserCreator`.`id`)
            WHERE " . $conditions . "
            ORDER BY $order
            $paging_sql";


        return $this->query($query);
    }

    /**
     * Custom paginateCount method
     */

    function paginateCount( $conditions = null ) 
    {
        if (empty($conditions)) {
            $conditions = "1=1";
        } elseif (is_array($conditions)) {
            foreach($conditions as $key => $value) {
                if (preg_match("/^\d+$/", $key) ) {
                    $temp[]= $value;
                } else {
                    $temp[]= "$key = '$value'";
                }
            }
            $conditions = implode(" AND ", $temp);
        }

        $query = "
			SELECT 	UserCreator.lgn
					, Tournament.name
					, Event.name
					, SignupRoom.model
					, SignupRoom.id
					, SignupRoom.status
			FROM `signup_rooms` AS `SignupRoom`
			LEFT JOIN `tournaments` AS `Tournament` ON (`SignupRoom`.`model` = 'Tournament' AND `SignupRoom`.`model_id` = `Tournament`.`id`)
			LEFT JOIN `events` AS `Event` ON (`SignupRoom`.`model` = 'Event' AND `SignupRoom`.`model_id` = `Event`.`id`)
			LEFT JOIN `signup_roommates` AS `Creator` ON (`Creator`.`status` = 'Creator' AND `Creator`.`room_id` = `SignupRoom`.`id`)
			LEFT JOIN `users` AS `UserCreator` ON (`Creator`.`user_id` = `UserCreator`.`id`)
			WHERE " . $conditions;

        return count($this->query($query));
    }

    function csvReport($model, $modelID) 
    {
        
        $sql = "SELECT room.id AS room_id, room.status, signup.id AS signup_id, room.people_in_room, user.id, user.firstname, user.lastname, user.lgn, package.name as packagename, question.question, options.optiontext
		FROM signup_rooms AS room
		LEFT JOIN signup_roommates AS roommate ON room.id = roommate.room_id
		LEFT JOIN users AS user ON (roommate.user_id = user.id)
		
		INNER JOIN signups AS signup ON (signup.user_id = user.id AND signup.model = room.model AND signup.model_id = room.model_id AND signup.status IN ('paid', 'partly paid'))
		LEFT JOIN packagedetails AS packagedetail ON signup.packagedetails_id = packagedetail.id
		LEFT JOIN packages AS package ON packagedetail.package_id = package.id
		
		LEFT JOIN answers AS answer ON (answer.model = CONCAT( 'Room_for_', room.model) AND answer.model_id = room.model_id AND answer.user_id = user.id)
		LEFT JOIN options ON answer.option_id = options.id
		LEFT JOIN questions AS question ON question.id = options.question_id
			
		WHERE room.model = '" . $model . "' AND room.model_id = " . $modelID . " AND room.status IN ('Approved', 'Confirmed') AND roommate.status NOT IN ('Declined', 'Pending') ORDER BY room.id ASC";
        
        
        $results = $this->query($sql);
        $rooms = array();
        foreach ($results as $result) {

            $roomID = $result['room']['room_id'];
            
            if (empty($rooms[$roomID]['room_id'])) {
                $rooms[$roomID] = $result['room'] + $result['package'];    
            }

            $rooms[$roomID]['signups'][$result['signup']['signup_id']] = $result['signup']['signup_id'];
            $rooms[$roomID]['roommates'][$result['user']['id']] = $result['user'];
            if ($result['question']['question'] && $result['options']['optiontext']) {
                $rooms[$roomID]['answers'][$result['question']['question']] = $result['options']['optiontext'];    
            }
        }
        return $rooms;
    }

}
?>
