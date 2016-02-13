<?php
/* SVN FILE: $Id: languages_controller.php 557 2008-09-21 12:28:51Z ykharchenko $ */
/*
 * @version $Revision: 557 $
 * @modifiedby $LastChangedBy: ykharchenko $
 * @lastmodified $Date: 2008-09-21 15:28:51 +0300 (Вск, 21 Сен 2008) $
 */
class Teammate extends AppModel
{

    var $name = 'Teammate';
    var $recursive = 1;
    var $actsAs = array('Containable');


    //The Associations below have been created with all possible keys, those that are not needed can be removed
    var $belongsTo = array(
    'User' => array('className' => 'User',
                                'foreignKey' => 'user_id',
                                'conditions' => array('User.is_deleted' => 0),
                                'fields' => '',
                                'order' => ''
    ),
    'Team' => array('className' => 'Team',
                                'foreignKey' => 'team_id',
                                'conditions' => array('Team.is_deleted' => 0),
                                'fields' => '',
                                'order' => ''
    )
    );
    /**
     * Get Users Teams List
     * @author Oleg D.
     */
    function getUserTeamsIDs($userID) 
    {
        return $this->find('list', array('conditions' => array('user_id' => $userID,'status <>'=>'Deleted'), 'fields' => array('team_id', 'team_id')));            
    }
    /**
     * Get Affils Teams List
     * @author Oleg D.
     */
    function getAffilActiveTeamsIDs($modelName, $modelID) 
    {
        $teams = array();
        $sql = '
			SELECT teammates.user_id, teammates.team_id FROM users_affils
			LEFT JOIN teammates ON teammates.user_id = users_affils.user_id AND teammates.status <> "deleted"
			LEFT JOIN teams ON teams.id = teammates.team_id
			WHERE users_affils.model_id = ' . intval($modelID) . ' AND users_affils.is_deleted = 0 AND users_affils.model = "' . $modelName . '" AND (teams.total_wins + teams.total_losses) > 0';
        $queryResults = $this->query($sql);
        if (!empty($queryResults)) {
            $teams = Set::combine($queryResults, '{n}.teammates.team_id', '{n}.teammates.team_id');            
        }
        return $teams;        
    }
    /**
     * Check completed address info for signup
     * @author Oleg D.
     */
    function isAddressCompleted($userID) 
    {
        $homeCount = $this->User->Address->find('count', array('conditions' => array('Address.model' => 'User', 'Address.model_id' => $userID, 'Address.is_deleted' => '0', 'Address.label' => 'Home')));
        $user = $this->User->find('first', array('conditions' => array('User.id' => $userID)));
        if ($homeCount && $user['User']['birthdate'] && $user['User']['firstname'] && $user['User']['lastname'] && $user['User']['gender']) {
            return 1;
        } else {
            return 0;
        }
    }
    
    /**
     * Get teammates user_id for the completet teams for some model, model_id
     * @author Oleg D.
     */
    function temmatesOfCompletetTeams($model, $modelID) 
    {
        
        $sql = "SELECT DISTINCT(tm.user_id)
		FROM teams t
		INNER JOIN teammates tm ON t.id = tm.team_id
		INNER JOIN teams_objects tob ON t.id = tob.team_id
		WHERE tob.model = '" . $model . "' AND tob.model_id = " . $modelID . " AND tob.status = 'created' AND tm.status IN ('Creator', 'Accepted') AND t.status IN ('Completed','Created')";
        
        $results = $this->query($sql);
        $usersIDs = Set::combine($results, '{n}.tm.user_id', '{n}.tm.user_id');
        return $usersIDs;
    }
        /**
     * Get teammates user_id for the teams for some model, model_id
     * @author Oleg D.
     */
    function temmatesOfCompletedOrPendingTeams($model, $modelID) 
    {
        
        $sql = "SELECT DISTINCT(tm.user_id)
        FROM teams t
        INNER JOIN teammates tm ON t.id = tm.team_id
        INNER JOIN teams_objects tob ON t.id = tob.team_id
        WHERE tob.model = '" . $model . "' AND tob.model_id = " . $modelID . " AND tob.status = 'created' AND tm.status IN ('Creator', 'Accepted','Pending') AND t.status IN ('Completed','Created','Pending')";
        
        $results = $this->query($sql);
        $usersIDs = Set::combine($results, '{n}.tm.user_id', '{n}.tm.user_id');
        return $usersIDs;
    }
}
?>
