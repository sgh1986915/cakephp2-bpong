<?php
/* SVN FILE: $Id: team.php 8091 2011-12-19 03:54:55Z _skinny $ */
/*
 * @version $Revision: 8091 $
 * @modifiedby $LastChangedBy: _skinny $
 * @lastmodified $Date: 2011-12-19 05:54:55 +0200 (Пн, 19 дек 2011) $
 */
class Team extends AppModel
{

    var $name     = 'Team';
    var $recursive = -1;
    var $actsAs = array('Image'=>array('thumbs'=>array('create'=>true,'width'=>'120','height'=>'120','bgcolor'=>'#FFFFFF')),
            'Containable','Sluggable'=>array('separator' =>  '-',
                                               'label'         => 'name',
                                                                                           'slug'          => 'slug',
                                                                                           'length'       => 100,
                                                                                           'overwrite'  =>  true)
    );

    var $validate = array(
    'name' => array('rule' => array('notEmpty')
                    ,'allowEmpty' => false
                    ,'message'    => 'Name can not be empty.')
    );

    var $hasOne = array(
      'PersonalImage' => array(
                'className' => 'Image',
                'foreignKey' => 'model_id',
                'dependent' => true,
                'conditions' => array('PersonalImage.model'=>'Team','PersonalImage.prop'=>'Personal'),
                'fields' => '',
                'order' => ''
      )
    );
    var $hasMany = array(
            'TeamsObject'=>array(
                'className'=>'TeamsObject',
                'foreignKey'=>'team_id',
                'conditions'=>array('TeamsObject.status <>' => 'Deleted'),
                'dependent'=>false),
            'Ratinghistory'=>array(
                'className'=>'Ratinghistory',
                'foreignKey'=>'team_id',
                'conditions'=>array('Ratinghistory.model'=>'Team')),
            'GameAsTeam1'=>array(
                'className'=>'Game',
                'foreignKey'=>'team1_id'),
            'GameAsTeam2'=>array(
                'className'=>'Game',
                'foreignKey'=>'team2_id'),
            'VenuesTeam'=>array(
                'className'=>'VenuesTeam',
                'foreignKey'=>'team_id')   
    );


    var $hasAndBelongsToMany = array(
      'User' => array('className' => 'User',
            'joinTable' => '',
            'with' => "Teammate",
            'conditions'=>array('Teammate.status'=>array('Creator','Accepted','Pending')),
            'foreignKey' => 'team_id',
            'associationForeignKey' => 'user_id',
            'unique' => true,
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'deleteQuery' => '',
            'insertQuery' => ''
      )


    );

    /**
     * Getting completed teams  by current user
     * @author vovich
     * @param int $userID
     */
    function getUserTeams($userID = null, $fields = " Team.* ", $peopleInTeam = null) 
    {

        if (!$peopleInTeam) {
            $peopleInTeam = ' > 0';
        } else {
            $peopleInTeam = ' = ' . $peopleInTeam;
        }
        $sql = " SELECT ".$fields." FROM teams AS Team " .
        "	  INNER JOIN teammates AS Teammate ON Teammate.team_id = Team.id " .
        "	  AND Teammate.user_id = ".$userID." " .
        "    AND Teammate.status IN ('Creator','Accepted') AND Team.status='Completed' AND people_in_team " . $peopleInTeam;

        return $this->query($sql);
    }

    /**
     * Getting all teams  by current user
     * @author vovich
     * @param int $userID
     */
    function getAllUserTeams($userID = null, $fields = " Team.* ", $peopleInTeam = null) 
    {

        if (!$peopleInTeam) {
            $peopleInTeam = ' > 0';
        } else {
            $peopleInTeam = ' = ' . $peopleInTeam;
        }
        $sql = " SELECT ".$fields." FROM teams AS Team " .
        "	  INNER JOIN teammates AS Teammate ON Teammate.team_id = Team.id " .
        "	  AND Teammate.user_id = ".$userID." " .
        "    AND Teammate.status IN ('Creator','Accepted') AND Team.status <> 'Deleted' AND people_in_team " . $peopleInTeam;
        return $this->query($sql);
    }
    /**
     * Getting all teams  by current user. Includes teams where the user is pending
     * @author vovich
     * @param int $userID
     */
    function getAllUserTeamsIncludingPending($userID = null, $fields = " Team.* ", $peopleInTeam = null) 
    {

        if (!$peopleInTeam) {
            $peopleInTeam = ' > 0';
        } else {
            $peopleInTeam = ' = ' . $peopleInTeam;
        }
        $sql = " SELECT ".$fields." FROM teams AS Team " .
                "      INNER JOIN teammates AS Teammate ON Teammate.team_id = Team.id " .
                "      AND Teammate.user_id = ".$userID." " .
                "    AND Teammate.status IN ('Creator','Accepted','Pending') AND Team.status <> 'Deleted' AND people_in_team " . $peopleInTeam;
        return $this->query($sql);
    }
    /**
     * Getting completed teams  by current user assigned to the TOURNIVENT
     *
     * @author vovich
     * @param  int    $userID
     * @param  string $model
     * @param  int    $modelID
     * 
     * Modified by Skinny. This needs to include teams where the teammate is pending, otherwise it shows nothing for the 
     * user in the signup screen. 
     */
    function getUserAssignedTeams($userID = null,$model = null, $modelID = null) 
    {

        $sql = " SELECT Team.*, Teamsobject.* FROM teams AS Team " .
        "  INNER JOIN teammates AS Teammate ON Teammate.team_id = Team.id " .
        "	  AND Teammate.user_id = ".$userID." " .
        "    AND Teammate.status IN ('Creator','Accepted','Pending') AND Team.status<>'Deleted' " .
        " INNER JOIN teams_objects   as Teamsobject ON Team.id = Teamsobject.team_id " .
        "  AND Teamsobject.model='".$model."' " .
        "  AND Teamsobject.model_id =  " .$modelID.
        "  AND Teamsobject.status <> 'Deleted'";

        return $this->query($sql);
    }

    /**
     * Checking if exist more teams with the same teammates
     * @author vovich
     * @param int $userID
     * @param int $teamID
     * @param int $peopleInTeam
     * @return bool false if there are no teams with the same else true;
     */
    function checkSameTeams($userID = null, $teamID = null,$peopleInTeam = 2) 
    {
        //Getting teams where such user as teammate
        $sql = "SELECT CONVERT(GROUP_CONCAT( DISTINCT team_id) USING cp1251 ) as curTeams FROM teammates AS teammates " .
        "   INNER JOIN   teams as teams ON teams.id = teammates.team_id " .
        "   WHERE teammates.team_id != ".$teamID."  AND teams.people_in_team = ".$peopleInTeam." AND teammates.status IN ('Creator','Accepted')    AND teams.status = 'Completed'  AND teammates.user_id = ".$userID;
        $curTeams = $this->query($sql);
        //$curTeams = $curTeams[0][0]['curTeams'];
        return $curTeams;
        if (empty($curTeams)) {
            return 2; 
        }

        //Getting users for the current team
        $sql = "SELECT CONVERT(GROUP_CONCAT(DISTINCT user_id) USING cp1251 ) as curTeammates FROM teammates AS teammates WHERE team_id = ".$teamID."  AND  (status  IN ('Creator','Accepted') OR user_id = ".$userID." )";
        $curTeammates = $this->query($sql);
        $curTeammates = $curTeammates[0][0]['curTeammates'];

        $sql = " SELECT team_id, count(*) FROM teammates WHERE user_id IN(".$curTeammates.") AND team_id IN (".$curTeams.")" .
          " GROUP BY team_id HAVING count(*) = ".$peopleInTeam;

        $result = $this->query($sql);
        if (empty($result)) {
            return false; 
        }
        else {
            return true; 
        }
    }
    function doesMatchingTeamExistByTeamID($teamID) 
    {
        $team = $this->find(
            'first', array(
            'conditions'=>array(
                'Team.id'=>$teamID,
                'Team.status !='=>'Deleted'),
            'contain'=>array('User')
            )
        );
        if (!$team) {
            return false; 
        }
        $playerIDs = array();
        foreach ($team['User'] as $key=>$user) {
            if ($user['Teammate']['status'] != 'Declined' && $user['Teammate']['status'] != 'Deleted') {
                $playerIDs[$key] = $user['id']; 
            }
        }
        return $this->doesMatchingTeamExistByPlayerIDs($playerIDs, $teamID);
    }
    function updateTeamRating($teamID) 
    {
        $team = $this->find(
            'first', array('conditions'=>array(
            'Team.id'=>$teamID,
            'Team.status <>'=>'Deleted'),
            'contain'=>array('User'))
        );
        if (!$team) {
            return false; 
        }
        $totalRating = 0;
        $count = 0;
        foreach ($team['User'] as $user) {
            if ($user['Teammate']['status'] == 'Accepted' || $user['Teammate']['status'] == 'Creator'  
                || $user['Teammate']['status'] == 'Pending'
            ) {
                    $userRating = $user['rating'];
                if ($userRating == 0) {
                    $userRating = INITIAL_PLAYER_RATING; 
                }
                    $totalRating += $userRating;
                    $count++;
            }
        }
        if ($count > 0) {
            $rating = $totalRating/$count; 
        }
        else {
            $rating = INITIAL_TEAM_RATING; 
        }
        return $this->save(array('id'=>$teamID,'rating'=>$rating));
    }
  


    /**
    *  Return TournEvents list for select  box for assigmnet new Tournievent
    * @author vovich
    * @return array description
    */
    function getTournEventsList()
    {
        $result['0']     = array('Select one');
        $Event           = ClassRegistry::init('Event');


        $Event->recursive = -1;
        $events = $Event->find('list', array('fields'=>array('Event.id','Event.name'),'conditions'=>'Event.start_date >=NOW() AND Event.is_deleted<>1 AND Event.signup_required = 1'));
        if (!empty($events)) {
            $result['Events'] = array();
            foreach ($events as $key=>$value) {
                $result['Events'][$key] = "&nbsp;&nbsp;".strip_tags($value);
            }
        }
        return $result;
    }

    /**
    * Check can we assign  the team to the TournEvent
    * @author vovich
    * @param int    $teamID
    * @param string $model
    * @param int    $modelID
    * @param bool   $check_expiration
    * @return string - empty if all right else Error
    */
    function canAssignTeam($teamID = null,$modelID = null,$model = null,$check_expiration = true) 
    {
        $result    = "";
        if ($model=="Events" || $model == "Event") {
            $model = "Event"; 
        }
        else {
            $model = "Tournament"; 
        }
        //check input data
        if (!$modelID && !$model && !$teamID) {
            return "choose event or tournament for the assigment";
        }
        //checking if such team is exist and it's completed
        $teamInformation = $this->find('first', array('conditions'=>array('id'=>$teamID,'is_deleted <>'=>1)));
        if (empty($teamInformation)) {
            return ("can not find such team");
        }
        if ($teamInformation['Team']['status']!='Completed' && $teamInformation['Team']['status']!='Pending' && $teamInformation['Team']['status']!='Created') {
            return ("team is not completed.");
        }
        //checking if this team  has teammates and people_in_team===count(teammates)
        $Teammate = ClassRegistry::init('Teammate');
        $Teammate->recursive = 0;
        $teammates = $Teammate->find('all', array('contain'=>array('User'),'conditions'=>array('team_id'=>$teamID,'status'=>array('Creator','Accepted', 'Pending'))));

        if (empty($teammates)) {
            return "can not find teammates in this team.";
        }

        if(count($teammates)!=$teamInformation['Team']['people_in_team']) {
            return "this team is not completed";
        }


        $Model = ClassRegistry::init($model);
        $Model->recursive = -1;
        $modelInforamtion = $Model->find('first', array('conditions'=>array('id'=>$modelID)));

        if (empty($modelInforamtion)) {
            return "Can not find such ".$model.".";
        }
        //checking signups
        if ($modelInforamtion[$model]['signup_required'] != 1) {
            //signup is not needed
            return;
        }
        //Checking expired
        if ($check_expiration) {
            if ((!empty($modelInforamtion[$model]['finish_signup_date']) && strtotime($modelInforamtion[$model]['finish_signup_date'])<strtotime(date("Y-m-d")) )) {
                return "This ".$model." is expired.";
            }
        }

        //Checking if people in team >=min and <= max
        /*
        if (!empty($modelInforamtion[$model]['people_team']) && $modelInforamtion[$model]['min_people_team']>$teamInformation['Team']['people_in_team']) {
        return "The number of teammates is less than Min people per team for this ".$model;
        }

        if (!empty($modelInforamtion[$model]['max_people_team']) && $modelInforamtion[$model]['max_people_team']<$teamInformation['Team']['people_in_team']) {
        return "The number of teammates is greater than Max people per team for this ".$model;
        }
        */
        //checking if user already in other teams for such model
        //checking signups
        $result = "";

        //checking payments
        $Signup = ClassRegistry::init('Signup');

        foreach ($teammates as $teammate) {
             $Signup->recursive = -1;
             $signupInformation = $Signup->find('first', array('fields'=>array('Signup.status'),'conditions'=>array('model'=>$model,'model_id'=>$modelID,'user_id'=>$teammate['Teammate']['user_id'])));
             //Not signed upped
             /*
             if (empty($signupInformation)) {
	           $result.= " teammate ".$teammate['User']['lgn']." is not signed up to this ".$model."<BR>";
             } elseif ($signupInformation['Signup']['status']!='paid') {
            $result.= " teammate ".$teammate['User']['lgn']." is not paid for this ".$model." <BR>";
             }
            */
            if ($modelInforamtion[$model]['multi_team'] != 1) {
                $cnt = "";
                $sql = "SELECT count(*) as cnt " .
                "  FROM teams_objects AS TeamsObject" .
                "  INNER JOIN teammates AS Teammate ON TeamsObject.model='".$model."' ".
                "  AND TeamsObject.model_id=".$modelID.
                "  AND TeamsObject.team_id = Teammate.team_id " .
                "  AND TeamsObject.team_id <>".$teamID.
                "  AND Teammate.user_id = ".$teammate['Teammate']['user_id'].
                "  AND (Teammate.status='Creator' OR Teammate.status='Accepted') " .
                "  AND TeamsObject.status<>'Deleted'
	          	INNER JOIN teams AS Team ON
		    	Team.id = Teammate.team_id
		    	 AND Team.status = 'Completed'
	          ";
                $cnt = $this->query($sql);

                if ($cnt[0][0]['cnt']>0) {
                    $result.= " teammate ".$teammate['User']['lgn']." signed up to another team  for this ".$model."<BR>";
                }

            }

        }
        return $result;

    }

    /**
     * Calculate Average wins/losses
     * @author Oleg D.
     */

    function calcAverageWins($totalWins, $totalLosses) 
    {
        if (!$totalLosses && !$totalWins || !$totalWins) {
            $averageWin = 0;
        } else{
            $averageWin = number_format($totalWins / ($totalWins + $totalLosses), 4);
        }

        return $averageWin;
    }

    /**
     * Calculate Average Cupdif
     * @author Oleg D.
     */
    function calcAverageCupdif($totalWins, $totalLosses, $totalCupdif) 
    {
        if ($totalWins || $totalWins) {
            $averageCupdif = number_format($totalCupdif / ($totalWins + $totalLosses), 2);
        } else{
            $averageCupdif = 0;
        }

        return $averageCupdif;
    }

    /**
     * Get Teammate Stats
     * @author Oleg D.
     */

    function getPlayerStats($userID) 
    {
        $userTeams = $this->Teammate->getUserTeamsIDs($userID);
        if (!empty($userTeams)) {
            $userTeamsString = implode(',', $userTeams);
            $teamResults = $this->query(
                "SELECT SUM(total_wins) as wins, SUM(total_losses) as losses, SUM(total_cupdif) as cupdifs
			FROM teams
			WHERE id IN(" . $userTeamsString . ")"
            );
            $stats = $teamResults[0][0];
            $stats['total_games'] = $stats['losses'] + $stats['wins'];
            $stats['average_wins'] = $this->calcAverageWins($stats['wins'], $stats['losses']);
            $stats['average_cupdif'] = $this->calcAverageCupdif($stats['wins'], $stats['losses'], $stats['cupdifs']);
        } else {
            $stats = array('wins' => 0, 'losses' => 0, 'cupdifs' => 0, 'total_games' => 0, 'average_cupdif' => 0, 'average_wins' => 0);
        }
        return $stats;
    }

    /**
     * Prepare team stats chart
     * @author Oleg D.
     */
    function prepareTeamStatsChart($teamID, $limit = false, $conditions = array(), $gameIDs = array()) 
    {
        $conditions_string = '';
        $order = "ORDER BY games.created DESC, games.id DESC";
        if (!empty($gameIDs)) {
            $gameIDsList = implode(",", $gameIDs);
            $conditions_string = ' AND games.id IN(' . $gameIDsList . ')';
            $order = ' ORDER BY FIELD(games.id, ' . $gameIDsList . ')';

        } else {
            if (!empty($conditions)) {
                if (!empty($conditions['date_from'])) {
                    $conditions_string.= ' AND games.created >= "' . $conditions['date_from'] . '" ';
                }
                if (!empty($conditions['date_to'])) {
                    $conditions_string.= ' AND games.created <= "' . $conditions['date_to'] . '" ';
                }
                if (!empty($conditions['event_id'])) {
                    $conditions_string.= ' AND games.event_id = ' . $conditions['event_id'];
                }
                if (!empty($conditions['opponent_id'])) {
                    $conditions_string.= " AND (team1_id = " . $conditions['opponent_id'] . " OR team2_id = " . $conditions['opponent_id'] . ")";
                }
            }
        }
        $sql =
        "SELECT * FROM games
		LEFT JOIN teams as team1 ON games.team1_id = team1.id
		LEFT JOIN teams as team2 ON games.team2_id = team2.id
		WHERE games.status = 'Completed' AND (team1_id = " . $teamID . " OR team2_id = " . $teamID . ")
		" . $conditions_string . " " . $order;

        if ($limit) {
            $sql .=' LIMIT ' . $limit;
        }
        $games = $this->query($sql);

        $chart = array();
        if (!empty($games)) {
            $games = array_reverse($games);
            $i = 0;
            foreach ($games as $game) {
                if ($game['games']['winningteam_id'] != $teamID) {
                    $game['games']['cupdif'] = $game['games']['cupdif']*-1;
                }
                if ($game['team1']['id'] == $teamID) {
                    $chart['opponents'][$i] = htmlspecialchars($game['team2']['name']);
                } else {
                    $chart['opponents'][$i] = htmlspecialchars($game['team1']['name']);
                }
                $chart['ots'][$i] = $game['games']['numots'];

                $chart['values'][$i] = $game['games']['cupdif'];
                $chart['dates'][$i] = date('m/d/y', strtotime($game['games']['created']));
                $i++;
            }
        }

        return $chart;

    }

    /**
     * Prepare user chart
     * @author Oleg D.
     */
    function prepareUserChart($userID, $limit = false, $conditions = array(), $gameIDs = array()) 
    {
        $chart = array();
        $teams = $this->Teammate->getUserTeamsIDs($userID);
        if (!empty($teams)) {
            $chart = $this->geatTeamsGamesForChart($teams, $limit, $conditions, $gameIDs);
        }
        
        return $chart;
    }
        
    function geatTeamsGamesForChart($teams, $limit = 15, $conditions = array(), $gameIDs = array()) 
    {
        $chart = array();
        $conditions_string = '';
        $order = "ORDER BY  games.created DESC, games.id DESC";
        if (!empty($gameIDs)) {
            $gameIDsList = implode(",", $gameIDs);
            $conditions_string = ' AND games.id IN(' . $gameIDsList . ')';
            $order = ' ORDER BY FIELD(games.id, ' . $gameIDsList . ')';

        } else {
            if (!empty($conditions)) {
                if (!empty($conditions['date_from'])) {
                    $conditions_string.= ' AND games.created >= "' . $conditions['date_from'] . '" ';
                }
                if (!empty($conditions['date_to'])) {
                    $conditions_string.= ' AND games.created <= "' . $conditions['date_to'] . '" ';
                }
                if (!empty($conditions['event_id'])) {
                    $conditions_string.= ' AND games.event_id = ' . $conditions['event_id'];
                }
                if (!empty($conditions['opponent_id'])) {
                    $conditions_string.= " AND (team1_id = " . $conditions['opponent_id'] . " OR team2_id = " . $conditions['opponent_id'] . ")";
                }
            }
        }

        if (!empty($teams)) {
            $teamsString = implode(',', $teams);
            $sql =
            "SELECT * FROM games
			LEFT JOIN teams as team1 ON games.team1_id = team1.id
			LEFT JOIN teams as team2 ON games.team2_id = team2.id
			WHERE  games.status = 'Completed' AND (team1_id IN(" . $teamsString . ") OR team2_id IN(" . $teamsString . "))
			" . $conditions_string . " " . $order;
            if ($limit) {
                $sql .=' LIMIT ' . $limit;
            }
            $games = $this->query($sql);
            $chart = array();
            if (!empty($games)) {
                $games = array_reverse($games);
                $i = 0;
                foreach ($games as $game) {
                    if (!isset($teams[$game['games']['winningteam_id']])) {
                        $game['games']['cupdif'] = $game['games']['cupdif']*-1;
                    }
                    if (isset($teams[$game['team1']['id']])) {
                        $chart['opponents'][$i] = htmlspecialchars($game['team2']['name']);
                        $chart['user_teams'][$i] = htmlspecialchars($game['team1']['name']);
                    } else {
                        $chart['opponents'][$i] = htmlspecialchars($game['team1']['name']);
                        $chart['user_teams'][$i] = htmlspecialchars($game['team2']['name']);
                    }
                    $chart['ots'][$i] = $game['games']['numots'];
                    $chart['values'][$i] = $game['games']['cupdif'];
                    $chart['dates'][$i] = date('m/d/y', strtotime($game['games']['created']));
                    $i++;
                }
            }
        }
        return $chart;        
    }
    function saveStatistics($teamID,$wins,$losses,$cupdif) 
    {
        $this->recursive = -1;
        $teamToMark = $this->find('first', array('conditions'=>array('id'=>$teamID)));
        if (!$teamToMark) { return ; 
        }
        $teamToMark['Team']['total_wins'] = $wins;
        $teamToMark['Team']['total_losses'] = $losses;
        $teamToMark['Team']['total_cupdif'] = $cupdif;
        $this->save($teamToMark);
    }
    function testWTF($teamID, $model, $modelID, $signup) 
    {
        $result = array();
        $sql ="SELECT * FROM teammates
        LEFT JOIN users ON users.id = teammates.user_id
        LEFT JOIN signups ON signups.user_id = teammates.user_id AND signups.model = '" . $model . "' AND signups.model_id = " . $modelID . "
        WHERE teammates.team_id = " . $teamID . " AND teammates.status NOT IN('Declined', 'Deleted')";
        $queryResults = $this->query($sql);

        $result['waiting_for_signup'] = array();
        $result['waiting_for_accept'] = array();

        foreach ($queryResults as $queryResult) {
            if (!$signup['Signup']['for_team'] && empty($queryResult['signups']['id'])) {
                $result['waiting_for_signup'][] = $queryResult['users'];
            }
            if ($queryResult['teammates']['status'] == 'Pending') {
                $result['waiting_for_accept'][] = $queryResult['users'];
            }
        }

        if (!$signup['Signup']['for_team']) {

        } else {
            $SignupsUser = ClassRegistry::init('SignupsUser');
            $Teammate = ClassRegistry::init('Teammate');

            $signupsUsers = $SignupsUser->find('all', array('conditions' => array('signup_id' => $signup['Signup']['id'])));
            if (!empty($signupsUsers)) {
                foreach ($signupsUsers as $signupsUser) {
                    if (!$signupsUser['SignupsUser']['agreement_accepted'] || !$Teammate->isAddressCompleted($signupsUser['SignupsUser']['user_id'])) {
                        $result['waiting_for_signup'][] = $signupsUser['SignupsUser']['user_id'];
                        return $signupsUser['SignupsUser']['user_id'];
                    }
                }
            }
        }
        return $result;
    }
       
    function teamInfoForSignup($teamID, $model, $modelID, $signup) 
    {
        $result = array();
        $sql ="SELECT * FROM teammates
		LEFT JOIN users ON users.id = teammates.user_id
		LEFT JOIN signups ON signups.user_id = teammates.user_id AND signups.model = '" . $model . "' AND signups.model_id = " . $modelID . "
		WHERE teammates.team_id = " . $teamID . " AND teammates.status NOT IN('Declined', 'Deleted')";
        $queryResults = $this->query($sql);

        $result['waiting_for_signup'] = array();
        $result['waiting_for_accept'] = array();

        foreach ($queryResults as $queryResult) {
            if (!$signup['Signup']['for_team'] && empty($queryResult['signups']['id'])) {
                $result['waiting_for_signup'][] = $queryResult['users'];
            }
            if ($queryResult['teammates']['status'] == 'Pending') {
                $result['waiting_for_accept'][] = $queryResult['users'];
            }
        }

        if (!$signup['Signup']['for_team']) {

        } else {
            $SignupsUser = ClassRegistry::init('SignupsUser');
            $Teammate = ClassRegistry::init('Teammate');

            $signupsUsers = $SignupsUser->find('all', array('conditions' => array('signup_id' => $signup['Signup']['id'])));
            if (!empty($signupsUsers)) {
                foreach ($signupsUsers as $signupsUser) {
                    if (!$signupsUser['SignupsUser']['agreement_accepted'] || !$Teammate->isAddressCompleted($signupsUser['SignupsUser']['user_id'])) {
                        $result['waiting_for_signup'][] = $signupsUser['SignupsUser']['user_id'];
                    }
                }
            }
        }
        return $result;
    }
    /**
    * This gets the singles team for the user. This assumes the User exists and has not been deleted. Does not hide email
    * 
    * @param mixed $playerID
    */

    function getSinglesTeam($playerID,$requesterID = 1) 
    {
        //first, see if this team exists
        $matchingTeamResult = $this->doesMatchingTeamExistByPlayerIDs(array($playerID));
        if ($matchingTeamResult) {
            return $matchingTeamResult;
        }
        $this->User->recursive = -1;
        $user = $this->User->find(
            'first', array('conditions'=>array(
            'User.id'=>$playerID))
        );
        $username = $user['User']['lgn'];
        $firstname = $user['User']['firstname'];
        $lastname = $user['User']['lastname'];
        $email = $user['User']['email'];
        if (strlen($firstname) == 0) {
            if (strlen($lastname) == 0) { 
                $newTeamName = $username; 
            } 
            else { 
                $newTeamName = $lastname; 
            }
        }
        else {
            if (strlen($lastname) == 0) {
                $newTeamName = $firstname; 
            }
            else { 
                $newTeamName = $firstname.' '.$lastname; 
            }
        }
            return $this->addNewConfirmedTeam($newTeamName, 1, array($playerID), $requesterID);
    }
        
    function updateStatsForTeam($teamID,$updateUsers = 0) 
    {            
        $results['total_wins'] = 0;
        $results['total_losses'] = 0;
        $results['total_cupdif'] = 0;
        $results['rating'] = 0;
        
        //First, tally up all mobile games
        $this->GameAsTeam1->recursive = -1;
        $games = $this->GameAsTeam1->find(
            'all', array('conditions'=>array(
            'OR'=>array(
                'GameAsTeam1.team1_id'=>$teamID,
                'GameAsTeam1.team2_id'=>$teamID),
            'GameAsTeam1.mobile'=>1,
            'GameAsTeam1.status'=>'Completed'),
            'contain'=>array())
        );
        
        $team = $this->find(
            'first', array(
            'conditions'=>array(
                'Team.id'=>$teamID),
            'contain'=>array(
                'User'
            ))
        );

        
        if (!$team) {
            return false; 
        }
        foreach ($games as $game) {
            if ($game['GameAsTeam1']['winningteam_id'] == $teamID) {
                      $results['total_wins']++;
                     $results['total_cupdif'] += $this->getEffectiveCupDif($game['GameAsTeam1']);
            }
            else {
                        $results['total_losses']++;
                        $results['total_cupdif'] -= $this->getEffectiveCupDif($game['GameAsTeam1']);                
          }
        }

            $teamObjects = $this->TeamsObject->find(
                'all', array('conditions'=>array(
                'TeamsObject.team_id'=>$teamID,
                'TeamsObject.status <>'=>'Deleted',
                'TeamsObject.model'=>'Event'
                //            ,'Event.is_deleted'=>0  Causing weird shit to happen, but you know what....the event shouldnt be deleted if there are active teamObjects that have stats
                ),
                'contain'=>array('Event','Team'))
            );
        foreach ($teamObjects as $object) {
            $results['total_wins'] += $object['TeamsObject']['wins'];
            $results['total_losses'] +=  $object['TeamsObject']['losses'];
            $results['total_cupdif'] += $object['TeamsObject']['cupdif'];               
        }

            $results['id'] = $teamID;
        foreach ($team['User'] as $user) {
            $results['rating'] += $user['rating'];
            if ($updateUsers) {
                $this->User->updateStatsForUser($user['id']);
            }
        }
        if (count($team['User']) && (count($team['User']) == $team['Team']['people_in_team'])) {
            $results['rating'] /= count($team['User']); 
        }
        else {  
            $results['rating'] = 0; 
        }
            
            $this->save($results);
                                           
            return 'ok';
    }
    function updateVenueStatsForTeam($teamID,$venueID) 
    {
        //Get the Team. Will need this when we want to look at the users
        $team = $this->find(
            'first', array(
            'conditions'=>array(
                'Team.id'=>$teamID,
                'Team.status <>'=>'Deleted'),
            'contain'=>array('User'))
        );
        if (!$team) {
            return; 
        }
        $Event = ClassRegistry::init('Event');
        $Event->recursive = -1;
        $events = $Event->find(
            'all', array('conditions'=>array(
            'venue_id'=>$venueID,
            'type'=>'nbplweekly',
            'is_deleted'=>0))
        );
        $teamVenueStats = array('team_id'=>$teamID,'venue_id'=>$venueID,'wins'=>0,'losses'=>0,
            'cupdif'=>0,'nbplpoints'=>0,
            'wins_ytd'=>0,'losses_ytd'=>0,'cupdif_ytd'=>0,'nbplpoints_ytd'=>0);
        $eventIDs = Set::extract($events, '{n}.Event.id');
        $events = Set::combine($events, '{n}.Event.id', '{n}.Event');
        
        $this->TeamsObject->recursive = -1;
        $teamsObjects = $this->TeamsObject->find(
            'all', array('conditions'=>array(
            'team_id'=>$teamID,
            'model_id'=>$eventIDs,
            'model'=>'Event',
            'status <>'=>'Deleted'))
        );
        foreach ($teamsObjects as $teamsObject) {
            $teamVenueStats['wins'] += $teamsObject['TeamsObject']['wins'];
            $teamVenueStats['losses'] += $teamsObject['TeamsObject']['losses'];
            $teamVenueStats['cupdif'] += $teamsObject['TeamsObject']['cupdif'];
            $teamVenueStats['nbplpoints'] += $teamsObject['TeamsObject']['nbplpoints'];
            $event = $events[$teamsObject['TeamsObject']['model_id']];
            $startDateTime = strtotime($event['start_date']);
            $startOfThisYear = strtotime(CURRENT_YEAR.'-01-01 00:00:00');
            if ($startDateTime > $startOfThisYear) {
                $teamVenueStats['wins_ytd'] += $teamsObject['TeamsObject']['wins'];
                $teamVenueStats['losses_ytd'] += $teamsObject['TeamsObject']['losses'];
                $teamVenueStats['cupdif_ytd'] += $teamsObject['TeamsObject']['cupdif'];
                $teamVenueStats['nbplpoints_ytd'] += $teamsObject['TeamsObject']['nbplpoints'];              
            }
        }
        $this->VenuesTeam->recursive = -1;
        $existingTeamsVenue = $this->VenuesTeam->find(
            'first', array('conditions'=>array(
            'team_id'=>$teamID,
            'venue_id'=>$venueID))
        );
        if ($existingTeamsVenue) {
            $teamVenueStats['id'] = $existingTeamsVenue['VenuesTeam']['id'];
            $this->VenuesTeam->save($teamVenueStats);
        } else {
            $this->VenuesTeam->create();
            $this->VenuesTeam->save($teamVenueStats);
        }
        //Now, scan through the teammates, and update them
        foreach ($team['User'] as $user) {
            //Get all the Teams for this user 
            $this->Teammate->recursive = -1;
            $teammates = $this->Teammate->find(
                'all', array('conditions'=>array(
                'user_id'=>$user['id'],
                'status'=>array('Creator','Pending','Accepted')))
            );
            $teamidsForUser = Set::extract($teammates, '{n}.Teammate.team_id');
            $venuesTeams = $this->VenuesTeam->find(
                'all', array('conditions'=>array(
                'venue_id'=>$venueID,
                'team_id'=>$teamidsForUser))
            );
                
            $userVenueStats = array('user_id'=>$user['id'],'venue_id'=>$venueID);
            $userVenueStats['wins'] = array_sum(Set::extract($venuesTeams, '{n}.VenuesTeam.wins'));
            $userVenueStats['losses'] = array_sum(Set::extract($venuesTeams, '{n}.VenuesTeam.losses'));
            $userVenueStats['cupdif'] = array_sum(Set::extract($venuesTeams, '{n}.VenuesTeam.cupdif'));
            $userVenueStats['nbplpoints'] = array_sum(Set::extract($venuesTeams, '{n}.VenuesTeam.nbplpoints'));
            $userVenueStats['wins_ytd'] = array_sum(Set::extract($venuesTeams, '{n}.VenuesTeam.wins_ytd'));
            $userVenueStats['losses_ytd'] = array_sum(Set::extract($venuesTeams, '{n}.VenuesTeam.losses_ytd'));
            $userVenueStats['cupdif_ytd'] = array_sum(Set::extract($venuesTeams, '{n}.VenuesTeam.cupdif_ytd'));
            $userVenueStats['nbplpoints_ytd'] = array_sum(Set::extract($venuesTeams, '{n}.VenuesTeam.nbplpoints_ytd'));
            $this->User->saveVenuesUserStats($userVenueStats);
        }
    }
    function setTeamRating($teamID,$rating) 
    {
        $this->recursive = -1;
        $team = $this->find(
            'first', array('conditions'=>array(
            'id'=>$teamID))
        );
        if ($team) {
            $team['Team']['rating'] = $rating;
            return $this->save($team);
        }
        else {       
            return false;
        }
    }
    //For right now at least....
    function updateStatsForTeams($startID,$endID,$updateUsers = 0) 
    {
        $ctr = $startID;
        while ($ctr <= $endID) {
            $result = $this->updateStatsForTeam($ctr, $updateUsers);
            $ctr++;
        }
        return true;
    }
    

    function createAndGetCompletedTeam($teamName,$numPlayers,$playerIDs,$requesterID) 
    {
        if ($numPlayers != count($playerIDs)) {
            return false; 
        }
        $result = $this->doesMatchingTeamExistByPlayerIDs($playerIDs);                    
        if ($result) {
            return $result; 
        }
        else {
            return $this->addNewConfirmedTeam($teamName, $numPlayers, $playerIDs, $requesterID); 
        }
    }
    /**
    * This assumes that the team does not exist (i.e. we've already checked for a team with the same players)
    * 
    * It also assumes we have confirmation (i.e. this should only be called if everyone is logged in), and all
    * players exist (i.e. all data has been scrubbed). 
    * 
    * It does not hide the email
    */
    
    function addNewConfirmedTeam($newTeamName,$numPlayers,$playerIDs,$requesterID) 
    {  
        if (count($playerIDs) > $numPlayers) { return false; 
        }
          
        if (!$newTeamName) { return 'Need a Team Name'; 
        }  
                
        
        $newTeam['Team']['name'] = $newTeamName;
        $newTeam['Team']['description'] = '';
        $newTeam['Team']['people_in_team'] = $numPlayers;
        if ($numPlayers == count($playerIDs)) {
            $newTeam['Team']['status'] = 'Completed'; 
        }
        else {
            $newTeam['Team']['status'] = 'Pending'; 
        }
        $this->create();
        if (!$this->save($newTeam)) { return 'Could not save'; 
        }
        $newTeamID = $this->getLastInsertID();
  
        foreach ($playerIDs as $playerID) { 
                unset($newTeammate);
                $this->Teammate->create();
                $newTeammate['requester_id'] = $requesterID;  
                $newTeammate['team_id'] = $newTeamID;      
                $newTeammate['status'] = 'Accepted';
                $newTeammate['user_id'] = $playerID;
            if (!$this->Teammate->save($newTeammate)) {
                return false; 
            }      
        }
        $returnTeam = $this->find(
            'first', array(
            'conditions'=>array(
                  'Team.id'=>$newTeamID),
            'contain'=>array('User'))
        );
        return $returnTeam;
    }
    
  
    /*
    This looks at a list of playerids, and determines where a team has the same players (this includes pending).
    If $ignoreTeamID is set, it ignores that team
    * @author skinny 
    */
    
    function doesMatchingTeamExistByPlayerIDs($playerIDs,$ignoreTeamID = 0) 
    {   
        $ctr = 0; 
        $this->Teammate->recursive = -1;
        //First, see if there is a team_id for which all users are a teammate of
        foreach ($playerIDs as $playerID) {
            $results[$ctr] =  $this->Teammate->find(
                'all', array('conditions'=>array(
                'user_id'=>$playerID,
                'status'=>array('Creator','Accepted','Pending')))
            );
            $results[$ctr] = Set::extract($results[$ctr], '{n}.Teammate.team_id'); 
            $ctr++;          
        }
        $mergedResults = $results[0];
        $ctr2 = 1; 
        while ($ctr2 < $ctr) {
      
            $mergedResults = array_intersect($mergedResults, $results[$ctr2]);    
            $ctr2++;
        }
        // mergedResults contains the ids of all the teams that potentially match. Now we need to see if there is 
        //one of these teams is a) not deleted, and b) has the right number of people
        foreach ($mergedResults as $teamid) {
            if ($teamid != $ignoreTeamID) {
                $team = $this->find(
                    'first', array('conditions'=>
                    array('Team.id'=>$teamid,
                    'Team.status <>'=>'Deleted',
                    'Team.people_in_team'=>count($playerIDs)),
                    'contain'=>array('User'))
                );
                if ($team) {
                    return $team;                   
                } 
            }
        }
        return false;
    }
}    
?>
