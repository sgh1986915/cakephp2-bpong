<?php
class OrganizationsUser extends AppModel
{

    var $name = 'OrganizationsUser';
    var $recursive = -1;
    var $actsAs = array('Containable');

    var $belongsTo = array(
    'Organization' => array('className' => 'Organization',
                                  'foreignKey' => 'organization_id',
                                  'dependent' => false,
                                  'conditions' => array(),
                                  'fields' => '',
                                  'order' => ''
    ),
    'User' => array('className' => 'User',
                                  'foreignKey' => 'user_id',
                                  'dependent' => false,
                                  'conditions' => array(),
                                  'fields' => '',
                                  'order' => ''
    )            
    ); 
    
    /**
     * Get my role for current organization
     * @author Oleg D.
     */
    function getOrgUser($orgID) 
    {
        $userID = intval($_SESSION['loggedUser']['id']);
        $findUser = $this->find('first', array('conditions' => array('user_id' => $userID, 'organization_id' => $orgID)));    
        if (empty($findUser)) {
            return array();
        } else {
            return $findUser['OrganizationsUser'];
        }        
    }
    
    /**
     * Get my role for current organization
     * @author Oleg D.
     */
    function isOrgManager($orgID) 
    {
        $userID = intval($_SESSION['loggedUser']['id']);
        $findUser = $this->find('first', array('conditions' => array('user_id' => $userID, 'organization_id' => $orgID)));    
        if (empty($findUser)) {
            return array();
        } else {
            return $findUser['OrganizationsUser'];
        }        
    }
    
    /**
     * Recalculate members count of organization
     * @author Oleg D.
     */
    function recalculateMembersCount($orgID) 
    {        
        $countUsers = $this->find(
            'count', array(
            'conditions' => array('organization_id' => $orgID, 'OrganizationsUser.status' => 'accepted', 'User.is_deleted <>' => 1, 'User.id >' => 0 ), 
            'contain' => array('User')
            )
        );
        $this->Organization->save(array('id' => $orgID, 'count_users' => $countUsers));
        
        return $countUsers;
    }
    /**
     * Get organizations managers
     * @author Oleg D.
     */
    function getManagers($orgID) 
    {
        return $this->find('list', array('fields' => array('user_id', 'user_id'), 'conditions' => array('organization_id' => $orgID, 'status' => 'accepted', 'role' => array('creator', 'manager'))));        
    }
    
    function getOrgIDsFromPlayersIDs($playerIDs) 
    {
        $result = $this->getOrgsFromPlayerIDs($playerIDs);
        return array_unique(Set::extract($result, '{n}.OrganizationsUser.organization_id'));
    } 
    function getOrgsFromPlayerIDs($playerIDs) 
    {  
        $orgsUsers= $this->find(
            'all', array(
            'conditions'=>array(
                'OrganizationsUser.user_id'=>$playerIDs,
                'OrganizationsUser.status <> '=>'deleted'),
            'contain'=>array('Organization'))
        );
        
        return $orgsUsers;
    }
    
}
?>
