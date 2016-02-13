<?php
class Manager extends AppModel
{

    var $name = 'Manager';
    var $recursive = -1;
    var $actsAs = array('Containable'); 
    
    
    var $belongsTo = array(
       'User' => array(
             'className'  => 'User'
            ,'foreignKey' => 'user_id'
            ,'conditions' => array('User.is_deleted' => 0)
            ,'fields'     => ''
       )
       ,'Event' => array(
             'className'  => 'Event'
            ,'foreignKey' => 'model_id'
            ,'conditions' => array('Event.is_deleted' => 0,'Manager.model' => "Event")
            ,'fields'     => ''
       )
       ,'Venue' => array(
             'className'  => 'Venue'
            ,'foreignKey' => 'model_id'
            ,'conditions' => array('Venue.is_deleted' => 0,'Manager.model' => "Venue")
            ,'fields'     => ''
       )     
    );

    /**
 *  Create manager  for the model
 * @param unknown_type $model
 * @param unknown_type $modelId
 * @param unknown_type $managerId
 * @return unknown_type
 * @author vovich
 */   
    function createManager($model = null, $modelId = null,$managerId = null)
    {
        if(!$managerId) {
            $managerId = $_SESSION['loggedUser']['id'];
        }
              
        $data['Manager']['user_id']     = $managerId;
        $data['Manager']['model']       = $model;
        $data['Manager']['model_id']    = $modelId;
        $data['Manager']['is_owner']    = 1;
        $data['Manager']['is_confirmed']= 1;
        $this->create();
        $this->save($data['Manager']);
        return $this->getLastInsertID();
       
    }
    /**
     * Check manager user or not
     * @author Oleg D.
     */
    function isManager($userID, $model, $modelID) 
    {
        $this->recursive = -1;
        return $this->find(
            'count', array(
            'contain' => array()
            , 'conditions' => array('Manager.model' => $model, 'Manager.model_id' => $modelID, 'Manager.user_id' => $userID, 'Manager.is_confirmed' => 1)
                        )
        );
    }   
    
    /**
     * Get models ID's for current user ID
     * @author Oleg D.
     */
    function getModelsIDs($userID, $model) 
    {
        $this->recursive = -1;
        return $this->find('list', array('conditions' => array('model' => $model, 'user_id' => $userID), 'fields' => array('model_id', 'model_id')));
    }
}
?>
