<?php
class Mailinglist extends AppModel
{
    var $name = 'Mailinglist';

        var $hasAndBelongsToMany = array(
        
    'User' => array('className' => 'User',
            'joinTable' => 'mailinglist_user',
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'mailinglist_id',
            'unique' => true,           
          )

        );
    
        /**
 * Return 0 or 1 
 * @param $userId
 * @param $listId
 * @return 0 or 1
 */    
        function isUserInList($userId = null, $listId = null) 
        {
            if ($userId && $listId) {
                $subscribeId = $this->MailinglistUser->field('id', array('mailinglist_id' => $listId, 'user_id'=> $userId));
                if ($subscribeId) {
                    return 1;
                 } else {
                    return 0;
                    }
            } else {
                return 0;
            }
 
        } 
 
        function subscribe($userId = null, $listId = null, $email = null, $params = array()) 
        {
     
            App::import('Vendor', 'MCAPI', array('file'   => 'MCAPI.class.php'));
            $MCapi = new MCAPI(MCLOGIN, MCPASSWORD);
            $mcListId = $this->field('chimpid', array('id'=>$listId));
            if (!$mcListId) {
                return false;
            }
            $params['IP_Address'] = $_SERVER['REMOTE_ADDR'];
            $this->MailinglistUser->deleteAll("mailinglist_id = $listId AND user_id = $userId");
            $this->MailinglistUser->create();
            $this->MailinglistUser->save(array("mailinglist_id" => $listId , "user_id" => $userId));
            $result =  $MCapi->listSubscribe(
                $mcListId,
                $email,
                $params,
                'html', // email_type
                false,  // double_optin
                true,   // update_existing 
                true,   // replace_interests
                false   // send_welcome
            );
            //pr($MCapi->errorCode);
            return $result;
        }
    
        function unSubscribe($userId = null, $listId = null, $email = null )
        {

            App::import('Vendor', 'MCAPI', array('file'   => 'MCAPI.class.php'));
            $MCapi = new MCAPI(MCLOGIN, MCPASSWORD);
            $mcListId = $this->field('chimpid', array('id'=>$listId));
            if (!$mcListId) {
                return false;
            }
     
            $this->MailinglistUser->deleteAll("mailinglist_id = $listId AND user_id = $userId");
            /*Remove from the list*/ 
            $result =  $MCapi->listUnsubscribe(
                $mcListId, 
                $email, 
                true,  // delete member
                true, // send goodbye
                false  // send notify
            ); 
            //pr($MCapi->errorCode);
        }
}
