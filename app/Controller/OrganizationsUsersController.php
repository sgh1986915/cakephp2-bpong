<?php

class OrganizationsUsersController extends AppController
{

    var $name    = 'OrganizationsUsers';
    //var $uses = array('User');

    function org_list($slug = null) 
    {
        
        
        if (empty($slug)) {
            $this->Session->setFlash('There is no Organization with such name.', 'flash_error');
            return $this->redirect('/');
        }

        $organization = $this->OrganizationsUser->Organization->find('first', array('conditions' => array('Organization.slug' => $slug, 'Organization.is_deleted' => 0)));
        
        if ($this->Access->getAccess('OrganizationsUser', 'u', $this->OrganizationsUser->getManagers($organization['Organization']['id']))) {
            $isManager = 1;
        } else {
            $isManager = 0;
        }
                
        if (empty($organization)) {
            $this->Session->setFlash('There is no Organization with such name.', 'flash_error');
            return $this->redirect('/');
        }            
        
        $orgUser = $this->OrganizationsUser->getOrgUser($organization['Organization']['id']);
        $this->set('orgUser',  $orgUser);
        
        $this->paginate = array(
         'conditions' => array('organization_id' => $organization['Organization']['id'], 'OrganizationsUser.status' => 'accepted', 'User.is_deleted <>' => 1 ), 
         'contain' => array('User')
        );
        $members = $this->paginate('OrganizationsUser');
        
        $pendingMembers = $this->OrganizationsUser->find('all', array('contain' => array('User'), 'conditions' => array('organization_id' => $organization['Organization']['id'], 'OrganizationsUser.status' => 'pending')));
        $declinedMembers = $this->OrganizationsUser->find('all', array('contain' => array('User'), 'conditions' => array('organization_id' => $organization['Organization']['id'], 'OrganizationsUser.status' => 'declined')));
        $invitedMembers = $this->OrganizationsUser->find('all', array('contain' => array('User'), 'conditions' => array('organization_id' => $organization['Organization']['id'], 'OrganizationsUser.status' => 'invited')));
        
        
        $this->pageTitle = $organization['Organization']['name'] . ' :: ' . 'Members';        
        $this->set('organizationsMenu',  1);            
    
        $this->set(compact('organization', 'members', 'pendingMembers', 'declinedMembers', 'invitedMembers', 'isManager'));
    }
    /**
     * Join user to organization
     * @return unknown_type
     */
    function joinUser($slug = null, $allow = 0, $redirect = 'organization') 
    {
        $userID = $this->getUserID();
        if (empty($slug)) {
            $this->Session->setFlash('There is no Organization with such name.', 'flash_error');
            return $this->redirect($backUrl);
        }
        
        
        $organization = $this->OrganizationsUser->Organization->find('first', array('conditions' => array('Organization.slug' => $slug, 'Organization.is_deleted' => 0), 'contain' => array('Creator')));        
        if ($redirect == 'organization') {
            $backUrl = '/o/' . $organization['Organization']['slug'];    
        } elseif ($redirect == 'profile') {
            $backUrl = '/u/' . $this->getUserLogin();            
        }
        
        if (empty($organization)) {
            $this->Session->setFlash('There is no Organization with such name.', 'flash_error');
            return $this->redirect($backUrl);
        }

        $orgUser = $this->OrganizationsUser->getOrgUser($organization['Organization']['id']);
        $this->set('orgUser',  $orgUser);
        
        
        
        if (!empty($orgUser['id'])) {
            if ($orgUser['status'] == 'invited') {
                return $this->redirect('/organizations_users/accept_invitation/' . $orgUser['id']);                    
            }
            $this->Session->setFlash('You have already joined this organization.', 'flash_error');
            return $this->redirect($backUrl);                
        }        
        
        if ($allow) {
            if (!$this->isLoggined()) {
                $this->Session->setFlash('Please log in to join the group.', 'flash_error');
                return $this->redirect($backUrl);                
            }
            $this->OrganizationsUser->create();    
            $this->OrganizationsUser->save(array('organization_id' => $organization['Organization']['id'], 'user_id' => $userID, 'role' => 'member', 'status' => 'pending'));
            $this->sendMailMessage(
                'OrganizationMemberJoinRequest', array(    
                     '{FNAME}'     => $organization['Creator']['firstname'],
                     '{LNAME}'     => $organization['Creator']['lastname'],
                     '{ORG_NAME}'     => $organization['Organization']['name'],        
                     '{MEMBERS_LINK}'      => MAIN_SERVER . '/o_members/' . $organization['Organization']['slug']),
                $organization['Creator']['email']
            );                
            
            $this->Session->setFlash('Your request to join this Organization has been received. The Organization Manager must now validate it.', 'flash_success');            
            return $this->redirect($backUrl);
        }    
        $this->pageTitle = $organization['Organization']['name'] . ' :: ' . 'Join';        
        $this->set('organizationsMenu',  1);
        $this->set(compact('organization'));        
        $this->set('isLoggined',  $this->isLoggined());
        
        
        
    }
    /**
     * Accept member
     * @author Oleg D. 
     */
    function accept($id) 
    {
        $organizationsUser = $this->OrganizationsUser->find('first', array('conditions' => array('OrganizationsUser.id' => $id), 'contain' => array('Organization', 'User')));    
        
        $this->Access->checkAccess('OrganizationsUser', 'u', $this->OrganizationsUser->getManagers($organizationsUser['Organization']['id']));
        if ($this->OrganizationsUser->save(array('id' => $id, 'status' => 'accepted'))) {
            $this->OrganizationsUser->recalculateMembersCount($organizationsUser['OrganizationsUser']['organization_id']);
            
            $this->sendMailMessage(
                'OrganizationMemberAccept', array(    
                     '{FNAME}'     => $organizationsUser['User']['firstname'],
                     '{LNAME}'     => $organizationsUser['User']['lastname'],
                     '{ORG_NAME}'     => $organizationsUser['Organization']['name'],        
                     '{ORG_LINK}'      => MAIN_SERVER . '/o/' . $organizationsUser['Organization']['slug']),
                $organizationsUser['User']['email']
            );            
            $this->Session->setFlash("User's status has been changed", 'flash_success');        
        }
        $this->goBack();        
    }
    
    /**
     * Decline member
     * @author Oleg D. 
     */
    function decline($id) 
    {
        $organizationsUser = $this->OrganizationsUser->find('first', array('conditions' => array('OrganizationsUser.id' => $id), 'contain' => array('Organization', 'User')));
        
        $this->Access->checkAccess('OrganizationsUser', 'u', $this->OrganizationsUser->getManagers($organizationsUser['Organization']['id']));        
        
        if ($this->OrganizationsUser->save(array('id' => $id, 'status' => 'declined'))) {
            $this->OrganizationsUser->recalculateMembersCount($organizationsUser['OrganizationsUser']['organization_id']);
            
            $this->sendMailMessage(
                'OrganizationMemberDecline', array(    
                     '{FNAME}'     => $organizationsUser['User']['firstname'],
                     '{LNAME}'     => $organizationsUser['User']['lastname'],
                     '{ORG_NAME}'     => $organizationsUser['Organization']['name'],        
                     '{ORG_LINK}'      => MAIN_SERVER . '/o/' . $organizationsUser['Organization']['slug']),
                $organizationsUser['User']['email']
            );                    
            $this->Session->setFlash("User's status has been changed", 'flash_success');        
        }
        $this->goBack();                
    }    
    
    /**
     * Manage member
     * @author Oleg D. 
     */
    function manage($id) 
    {
        $organizationsUser = $this->OrganizationsUser->find('first', array('conditions' => array('OrganizationsUser.id' => $id), 'contain' => array('User', 'Organization')));            
        
        $this->Access->checkAccess('OrganizationsUser', 'u', $this->OrganizationsUser->getManagers($organizationsUser['Organization']['id']));
        
        $organization['Organization'] = $organizationsUser['Organization'];
        
        if (empty($organization)) {
            $this->Session->setFlash('There is no Organization with such name.', 'flash_error');
            return $this->redirect('/');
        }        
        
        if (!empty($this->request->data)) {        
            if ($this->OrganizationsUser->save($this->request->data)) {
                $this->OrganizationsUser->recalculateMembersCount($organizationsUser['OrganizationsUser']['organization_id']);
                $this->Session->setFlash("Organization's member has been changed", 'flash_success');        
            }
            return $this->redirect('/o_members/' . $organization['Organization']['slug']);        
        } else {
            $this->request->data = $organizationsUser;
        }
        
        
        $this->pageTitle = $organization['Organization']['name'];        
        $this->set('organizationsMenu',  1);
        $this->set(compact('organization', 'organizationsUser', 'id'));                
    }
    
    /**
     * Invite member
     * @author Oleg D. 
     */
    function invite($orgID, $userID) 
    {
        
        if (!$this->OrganizationsUser->find('count', array('conditions' => array('organization_id' => $orgID, 'user_id' => $userID)))) {
            $this->OrganizationsUser->create();
            $this->OrganizationsUser->save(array('organization_id' => $orgID, 'user_id' => $userID, 'role' => 'member', 'status' => 'invited'));    
            
            $this->Session->setFlash('User has been invited.', 'flash_error');
            
            $id = $this->OrganizationsUser->getLastInsertID();
            $organizationsUser = $this->OrganizationsUser->find('first', array('conditions' => array('OrganizationsUser.id' => $id), 'contain' => array('Organization', 'User')));    
                
            $this->sendMailMessage(
                'OrganizationMemberInvite', array(    
                     '{FNAME}'     => $organizationsUser['User']['firstname'],
                     '{LNAME}'     => $organizationsUser['User']['lastname'],
                     '{ORG_NAME}'     => $organizationsUser['Organization']['name'],        
                     '{ACCEPT_LINK}' => MAIN_SERVER . '/organizations_users/accept_invitation/' . $id,
                     '{DECLINE_LINK}' => MAIN_SERVER . '/organizations_users/decline_invitation/' . $id),
                $organizationsUser['User']['email']
            );
                                
        } else {
            $this->Session->setFlash('This User has been already invited.', 'flash_error');                
        }                
        $this->goBack();                    
    }
    
    /**
     * Accept invintation to the organization
     * @author Oleg D.
     */
    function accept_invitation($id) 
    {
        $this->Access->checkAccess('LoggedMenu');
        $organizationsUser = $this->OrganizationsUser->find('first', array('conditions' => array('OrganizationsUser.id' => $id, 'OrganizationsUser.user_id' => $this->getUserID()), 'contain' => array('Organization', 'User')));
        if (!empty($organizationsUser) && $organizationsUser['OrganizationsUser']['status'] == 'invited') {
            $this->OrganizationsUser->save(array('id' => $id, 'status' => 'accepted'));    
            $this->Session->setFlash('Invitation has been accepted.', 'flash_error');                        
        } else {
            $this->Session->setFlash('This invitation is deprecated or not for you.', 'flash_error');
            return $this->redirect('/');        
        }
        $this->OrganizationsUser->recalculateMembersCount($organizationsUser['OrganizationsUser']['organization_id']);    
        return $this->redirect('/o/' . $organizationsUser['Organization']['slug']);    
    }
    
    /**
     * Decline invintation to the organization
     * @author Oleg D.
     */
    function decline_invitation() 
    {
        $this->Access->checkAccess('LoggedMenu');
         $organizationsUser = $this->OrganizationsUser->find('first', array('conditions' => array('OrganizationsUser.id' => $id, 'OrganizationsUser.user_id' => $this->getUserID()), 'contain' => array('Organization', 'User')));
        if (!empty($organizationsUser) && $organizationsUser['OrganizationsUser']['status'] == 'invited') {
            $this->OrganizationsUser->delete($id);    
            $this->Session->setFlash('Invitation has been declined.', 'flash_error');                        
        } else {
            $this->Session->setFlash('This invitation is deprecated or not for you.', 'flash_error');
            return $this->redirect('/');    
        }
        $this->OrganizationsUser->recalculateMembersCount($organizationsUser['OrganizationsUser']['organization_id']);
        return $this->redirect('/o/' . $organizationsUser['Organization']['slug']);    
    }    
        
    /**
     * Find users to invite
     * @author Oleg D.
     */
    function find_user() 
    {
        
        Configure::write('debug', 0);
        $conditions = array();
        if (!empty($_POST['lgn'])) {
            $lgn = trim($_POST['lgn']);
            $conditions['User.lgn'] = $lgn;
        }
        if (!empty($_POST['email'])) {
            $email = trim($_POST['email']);
            $conditions['User.email'] = $email;
        }
        
        $objects = array();
        if (!empty($conditions)) {
            $objects = $this->OrganizationsUser->User->find('all', array('conditions' => $conditions));
        }
        $this->set('objects', $objects);    
        $this->set('organizationID', intval($_POST['organization_id']));    
    }    
    function test($id = 2,$amf = 0) 
    {
                
        $OrganizationsUser = ClassRegistry::init('OrganizationsUser');
        $test1 = $OrganizationsUser;
        $result1 = $OrganizationsUser->find(
            'all', 
            array('conditions' => array('OrganizationsUser.id' => $id,'User.is_deleted=0'), 
                'contain' => array('Organization', 'User'))
        );    
        
        
        //$result4 = $OrganizationsUser->find('first',array('conditions'=>array('id'=>$id),
        //   'contain'=>array()));
        $result2 = $OrganizationsUser->save(
            array(
            'id'=>$id,
            'organization_id'=>2,
            'user_id'=>19572,
            'role'=>'creator',
            'status'=>'accepted')
        );
        
        $result3 = $OrganizationsUser->find(
            'all', 
            array('conditions' => array('OrganizationsUser.id' => $id,'User.is_deleted=0'), 
                'contain' => array('Organization', 'User'))
        );    

        
        $resultArray = array('FindQuery1'=>$result1,'SaveQuery'=>$result2,'FindQuery2'=>$result3);
        
        return $resultArray;
    }
}
?>
