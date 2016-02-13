<?php 
class AccessHelper extends Helper {

    var $helpers     = Array("Session"); 

    function getLoggeduserId () {
         if ($this->Session->check('loggedUser')) {
    			$userSession = $this->Session->read('loggedUser');
    			$userID = $userSession['id'];
    		} else {
    			$userID = VISITOR_USER;
    		}
    		
    		return $userID;
    }
/**
 * checking access and return true or false
 * @param $accessType
 * @param $permittedUsers
 * @param $authorId
 * @return unknown_type
 */    
    function getAccess ($accessType = 'DENY',$authorId = NULL,$permittedUsers = NULL) {
        $access = false;
        
        if ($accessType == 'ALL') {
            $access = true;
            
        } elseif ($accessType == 'OWNER') {

            if ($this->Session->check('loggedUser')) {
    			$userSession = $this->Session->read('loggedUser');
    			$userID = $userSession['id'];
    		} else {
    			$userID = VISITOR_USER;
    		}
            
            if (!$permittedUsers) {
                if ($userID == $authorId) {
                    $access = true;
                } else {
                    $access = false;
                }
            
            } elseif (is_array($permittedUsers)) {
                if (in_array($userID,$permittedUsers) || $userID == $authorId) {
                    $access = true;
                } else {
                    $access = false;
                }            
            } else {
                if ($userID == $permittedUsers || $userID == $authorId) {
                    $access = true;
                } else {
                    $access = false;
                } 
            }              
            
        } else {
            $access = false;
        }        
        
        return $access;
    }
   
}