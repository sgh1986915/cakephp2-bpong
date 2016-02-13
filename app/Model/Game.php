<?php
/**
 * This is the model for a 'game' object, which represents one beer pong game
 * @author Skinny
 */
class Game extends AppModel
{

    var $name     = 'Game';
    var $recursive = -1;
    var $actsAs = array('Containable');
        
    var $belongsTo = array(
            'Event' => array('className' => 'Event',
                                'foreignKey' => 'event_id',
            ),
            'Brackettype' => array('className'=>'Brackettype',
                                  'foreignKey'=>'brackettype_id'
            ),
            'Team1' => array('className'=>'Team',
                                  'foreignKey'=>'team1_id'
            ),
            'Team2' => array('className'=>'Team',
                                  'foreignKey'=>'team2_id'
            )                      
    );
    var $hasMany = array(
            'Ratinghistory'=>array('className'=>'Ratinghistory',
                            'foreignKey'=>'game_id')
    );
    /**
     * Get teams opponents
     * @author Oleg D. 
     */
    function getTeamsOpponents($teamID, $teams = array(), $eventID = null) 
    {
        if (empty($teams)) {
            $conditions = array('OR' => array('team1_id' => $teamID, 'team2_id' => $teamID), 'AND' => array('Game.status' => 'Completed'));
            if ($eventID) {
                $conditions['AND']['event_id'] = $eventID;    
            }
            $games = $this->find(
                'all', 
                array(
                'contain' => array(),
                'fields' => array('DISTINCT(CONCAT(team1_id, team2_id))', 'team1_id', 'team2_id'), 
                'conditions' => $conditions
                )
            );
            $opponents = array();
            foreach ($games as $game) {
                $opponentID = 0;
                if ($teamID != $game['Game']['team1_id']) {
                    $opponentID = $game['Game']['team1_id'];
                } else {
                    $opponentID = $game['Game']['team2_id'];                
                }
                $opponents[$opponentID] = $this->Team1->field('name', array('id' => $opponentID));                
            }
        } else {
            
            $conditions = array('OR' => array('team1_id' => $teams, 'team2_id' => $teams), 'AND' => array('Game.status' => 'Completed'));
            if ($eventID) {
                $conditions['AND']['event_id'] = $eventID;    
            }       
                
            $games = $this->find(
                'all', 
                array(
                'contain' => array(),
                'fields' => array('DISTINCT(CONCAT(team1_id, team2_id))', 'team1_id', 'team2_id'), 
                'conditions' => $conditions
                )
            );
            $opponents = array();
            foreach ($games as $game) {
                $opponentID = 0;
                if (!isset($teams[$game['Game']['team1_id']])) {
                    $opponentID = $game['Game']['team1_id'];
                } else {
                    $opponentID = $game['Game']['team2_id'];                
                }
                $opponents[$opponentID] = $this->Team1->field('name', array('id' => $opponentID));                
            }            
        }
        return $opponents;        
    }
}
?>
