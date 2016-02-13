<?php
class Ratinghistory extends AppModel
{
    var $name = 'Ratinghistory';
    var $actsAs = array('Containable');    
    var $belongsTo = array(
        'User' => array('className' => 'User',
            'foreignKey' => 'user_id',
            'conditions' => array('Ratinghistory.model' =>'User'),
            'fields' => '',
            'order' => ''
        ),
        'Team' =>array('className' =>'Team',
            'foreignKey'=>'team_id',
            'conditions'=>array(),
            'fields'=>'',
            'order'=>''
            ),
        'Game' =>array('className' =>'Game',
            'foreignKey'=>'game_id',
            'fields'=>'')
        );
        
    
    /**
    * This is the master function that calculates the unweighted change in ratings when two teams play
    */
    function getRatingChange($winnerRating, $loserRating, $cupdif) 
    {
         $winnerExpectedWinPct = $this->getExpectedWinPct($winnerRating, $loserRating);  
         $possibleMovement = $this->getInterpolatedMovementFromArray($winnerExpectedWinPct * 100, $cupdif);  
         return max($possibleMovement / 50, 0); 
         // we're dividing by 50 to normalize the weights. I.e., what was a weight of 2 is now a weight of 100
    }
    function clearHistory() 
    {
        $query = "DELETE FROM `ratinghistories` WHERE 1";
        $this->query($query);
        $query = "DELETE FROM `ratings` WHERE 1";
        $this->query($query);      
        return "ok";
    }
    function addRating($model,$userid,$teamid,$gameID,$weight,$before,$after,$adjustment = 0) 
    {
        $newRating['Ratinghistory']['model'] = $model;
        if ($model == 'User') {   
            $newRating['Ratinghistory']['user_id'] = $userid; 
        }
        else {
            $newRating['Ratinghistory']['user_id'] = 0; 
        }
        $newRating['Ratinghistory']['team_id'] = $teamid;
        $newRating['Ratinghistory']['game_id'] = $gameID;
        $newRating['Ratinghistory']['weight'] = $weight;
        $newRating['Ratinghistory']['before'] = $before;
        $newRating['Ratinghistory']['after'] = $after;
        $newRating['Ratinghistory']['adjustment'] = $adjustment;
        $this->create(); 
        $result = $this->save($newRating);
        return $result;
    }
    function setRating($model,$userID,$teamID,$gameID,$weight,$before,$after) 
    {        
        $result = $this->addRating($model, $userID, $teamID, $gameID, $weight, $before, $after);
        return $result;
    }
    /**
    * Gets Team A's expected win %
    * 
    * @param mixed $ratingA
    * @param mixed $ratingB
    */
    function getExpectedWinPct($ratingA,$ratingB) 
    {
        $exp = ($ratingB - $ratingA) / ELO_DIVISOR;
        return 1 / (1 + pow(10, $exp)); 
    }
    /*
    Inputs:
    $winPCT: For the team that won, prob that they would win before the game
    $cupDif: The Cup Dif
    Returns:
    
    say winpct is 50 and CD = 2
    lowpoint = 50
    highpoint = 55
    (matrix[50][2] * 5 + matrix[55][2] * 0) / 5 => 74.1
    */
    function getInterpolatedMovementFromArray($winPCT, $cupDif) 
    { 
        $lowPoint = 5 * floor($winPCT / 5);
        $highPoint = $lowPoint + 5;
        //       return array('win'=>$winPCT,'low'=>$lowPoint,'high'=>$highPoint);
        $matrix = $this->winPercentToCupDifMatrix;
        if ($cupDif > 5) { $cupDif = 5; // Ignore outliers
        }        if ($cupDif == 1) { $cupDif = 2; // 1 is close to 2, so lets treat them the same
        }        if ($cupDif < -5) { $cupDif = -5; 
        }
        if ($cupDif == -1) { $cupDif = -2; // This was -1....why?
        }        if ($cupDif == 0) { $cupDif = 1; 
        }
        $interpolatedPoint = (($matrix[$lowPoint][$cupDif] * ($highPoint - $winPCT)) + ($matrix[$highPoint][$cupDif] * ($winPCT - $lowPoint))) / 5;  
        return $interpolatedPoint - 50;
    }
    /**
    * This is a matrix of percentages. Example: If Team has a 95% Chance of winning, 
    */
    var $winPercentToCupDifMatrix = array(
        100 => array(5 => 0,4 => 0,3 => 0,2 => 0,1 => 0,-1 => 0,-2 => 0,-3 => 0,-4 => 0,-5 => 0), 
        95 => array(5 => 55.6,4 => 40.7,3 => 28,2 => 18.2,1 => 9.5,-1 => 3.9,-2 => 2.1,-3 => 1.1,-4 => 0.6,-5 => 0.2), 
        90 => array(5 => 67.6,4 => 53.7,3 => 40.2,2 => 28.7,1 => 16.8,-1 => 7.8,-2 => 4.4,-3 => 2.5,-4 => 1.3,-5 => 0.6), 
        85 => array(5 => 75,4 => 62.4,3 => 49.4,2 => 37.4,1 => 23.3,-1 => 11.6,-2 => 6.6,-3 => 3.9,-4 => 2.1,-5 => 1), 
        80 => array(5 => 80,4 => 68.8,3 => 56.6,2 => 44.5,1 => 29.3,-1 => 15.6,-2 => 9,-3 => 5.4,-4 => 2.9,-5 => 1.4), 
        75 => array(5 => 83.7,4 => 73.8,3 => 62.3,2 => 50.5,1 => 34.8,-1 => 19.6,-2 => 11.6,-3 => 7.1,-4 => 4,-5 => 2), 
        70 => array(5 => 86.5,4 => 77.8,3 => 67.3,2 => 56.1,1 => 40.2,-1 => 23.6,-2 => 14.3,-3 => 9,-4 => 5.1,-5 => 2.5), 
        65 => array(5 => 89.2,4 => 81.6,3 => 72.1,2 => 61.5,1 => 45.5,-1 => 27.6,-2 => 16.9,-3 => 10.8,-4 => 6.2,-5 => 3.2), 
        60 => array(5 => 91.2,4 => 84.5,3 => 75.9,2 => 66,1 => 50.4,-1 => 31.7,-2 => 19.6,-3 => 12.8,-4 => 7.5,-5 => 3.9), 
        55 => array(5 => 92.8,4 => 87,3 => 79.3,2 => 70.1,1 => 55.2,-1 => 35.9,-2 => 22.6,-3 => 15,-4 => 9.1,-5 => 4.8), 
        50 => array(5 => 94.1,4 => 89.2,3 => 82.4,2 => 74.1,1 => 59.8,-1 => 40.2,-2 => 25.9,-3 => 17.6,-4 => 10.8,-5 => 5.9), 
        45 => array(5 => 95.2,4 => 90.9,3 => 85,2 => 77.4,1 => 64.1,-1 => 44.8,-2 => 29.9,-3 => 20.7,-4 => 13,-5 => 7.2), 
        40 => array(5 => 96.1,4 => 92.5,3 => 87.2,2 => 80.4,1 => 68.3,-1 => 49.6,-2 => 34,-3 => 24.1,-4 => 15.5,-5 => 8.8), 
        35 => array(5 => 96.8,4 => 93.8,3 => 89.2,2 => 83.1,1 => 72.4,-1 => 54.5,-2 => 38.5,-3 => 27.9,-4 => 18.4,-5 => 10.8), 
        30 => array(5 => 97.5,4 => 94.9,3 => 91,2 => 85.7,1 => 76.4,-1 => 59.8,-2 => 43.9,-3 => 32.7,-4 => 22.3,-5 => 13.5), 
        25 => array(5 => 98,4 => 96,3 => 92.9,2 => 88.4,1 => 80.4,-1 => 65.2,-2 => 49.5,-3 => 37.7,-4 => 26.2,-5 => 16.3), 
        20 => array(5 => 98.6,4 => 97.1,3 => 94.6,2 => 91,1 => 84.4,-1 => 70.7,-2 => 55.5,-3 => 43.4,-4 => 31.2,-5 => 20), 
        15 => array(5 => 99,4 => 97.9,3 => 96.2,2 => 93.4,1 => 88.4,-1 => 76.7,-2 => 62.6,-3 => 50.6,-4 => 37.6,-5 => 25), 
        10 => array(5 => 99.4,4 => 98.7,3 => 97.5,2 => 95.6,1 => 92.2,-1 => 83.2,-2 => 71.3,-3 => 59.8,-4 => 46.3,-5 => 32.4), 
        5 => array(5 => 99.8,4 => 99.4,3 => 98.9,2 => 97.9,1 => 96.1,-1 => 90.5,-2 => 81.8,-3 => 72,-4 => 59.3,-5 => 44.4), 
        0 => array(5 => 100,4 => 100,3 => 100,2 => 100,1 => 100,-1 => 100,-2 => 100,-3 => 100,-4 => 100,-5 => 100)
    );
    function getUserRating($userID) 
    {
        $this->recursive = -1;
        $rating = $this->find(
            'first', array('order'=>array('Ratinghistory.id'=>'DESC'), 'conditions'=>array(
            'model'=>'User',
            'user_id'=>$userID))
        );
        if ($rating) {
            return $rating['Ratinghistory']['after'];
        }
        else { 
            return INITIAL_PLAYER_RATING; 
        }
    }
    function getUserRatings($userIDs) 
    {
        foreach ($userIDs as $userID) {
            $returnArray[$userID] = $this->getUserRating($userID);
        }            
        return $returnArray;
    } 
    function getTeamRating($teamID,$playerRatings) 
    {
            // First, lets see if there is already a rating for this team
            $this->recursive = -1;
            $rating = $this->find(
                'first', array('order'=>array('Ratinghistory.id'=>'DESC'), 'conditions'=>array(
                'model'=>'Team',
                'team_id'=>$teamID))
            );
            if (!$rating) {
                if (count($playerRatings) == 0) {
                    return INITIAL_TEAM_RATING; 
                }
                $averageTeammatesRating = $this->getTeammatesRating($playerRatings);
                if (($averageTeammatesRating * PLAYER_TO_TEAM_RATING_MULT) > INITIAL_TEAM_RATING) { 
                    return ($averageTeammatesRating * PLAYER_TO_TEAM_RATING_MULT); 
                }
                elseif ($averageTeammatesRating < INITIAL_TEAM_RATING)
                return $averageTeammatesRating;
                else {
                    return INITIAL_TEAM_RATING; 
                }
            } 
            else { return $rating['Ratinghistory']['after']; 
            }
    }   
    function getTeammatesRating($playerRatings) 
    {
        if (count($playerRatings) == 0) {
            return INITIAL_PLAYER_RATING; 
        }
            $count = 0;
            $totalRating = 0;
        foreach ($playerRatings as $playerRating) {
            $count++;
            $totalRating += $playerRating;
        }
            return $totalRating / $count; 
    }
}

?>
