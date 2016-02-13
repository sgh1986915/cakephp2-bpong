<?php

class ManagersController extends AppController
{
    var $name = 'Managers';

    var $uses = array( 'Manager','User', 'Event');

    /**
     * Assign manager
     * @author vovich
     * @param string $model
     * @param string $email
     * @param int    $modelID
     */
    function assignManager($model=null,$email=null,$modelID=null) 
    {
        if ($model && $email && $modelID) {
            /**
* 
 * Getting UserID 
*/
            $this->User->recursive = -1;
            $user = $this->User->find('first', array('conditions' => array('email' => urldecode($email), 'is_deleted' => 0)));

            if (empty($user)) {
                exit;
                $this->Session->setFlash('Can not find user.', 'flash_error');
                $this->logErr('error occured: Can\'t find such user.');
                return $this->redirect($_SERVER['HTTP_REFERER']);
            }

            $conditions = array('model'=>$model,'user_id'=>$user['User']['id'],'model_id'=>$modelID);
            $this->Manager->recursive = -1;
            $manager = $this->Manager->find('all', array('conditions'=>$conditions));

            if (empty($manager)) {
                $this->request->data['Manager']['user_id']  = $user['User']['id'];
                $this->request->data['Manager']['model']    = $model;
                $this->request->data['Manager']['model_id'] = $modelID;

                $this->Manager->create();
                if ($this->Manager->save($this->request->data['Manager'])) {
                    //Sending Activation code
                    switch ($model){
                    case 'Event':
                        $event = $this->Event->read(null, $modelID);    
                        $result = $this->sendMailMessage(
                            'Activation' . $model . 'Manager', array(
                            '{FNAME}'         => $user['User']['firstname'],
                            '{LNAME}'         => $user['User']['lastname'],
                            '{EMAIL}'         => $user['User']['email'],
                            '{EVENT_NAME}'              => $event['Event']['name'],
                            '{LINK}'              => "http://{$_SERVER['HTTP_HOST']}/events/view/" . $event['Event']['slug'],
                            '{ACTIVATION_LINK}'          => "http://{$_SERVER['HTTP_HOST']}/managers/activation/{$model}/{$modelID}/" . urlencode($email)
                                     ),
                            $email
                        );                            
                        break;    
                    }

                    if (!$result) {
                        //$this->logErr('error occured while sendinig password change email');
                    }
                }

                //$this->Session->setFlash('User has been assigned.');
                //$this->redirect($_SERVER['HTTP_REFERER']);
                exit;

            }else{
                //$this->logErr('error occured: User already assigned.');
                //$this->Session->setFlash('Such user already assigned.');
                //$this->redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->Session->setFlash('Error assigning Manager.', 'flash_error');
            $this->logErr('error occured: Error with assign Manager.');
            return $this->redirect($_SERVER['HTTP_REFERER']);
        }
    }
    /**
     * Activation manager
     * @author vovich
     * @param string $model
     * @param int    $modelID
     * @param string $activationCode
     */
    function activation($model=null, $modelID=null, $email)
    {
        if ($model && $email) {
            $this->User->recursive = -1;
            $userInfo = $this->User->find('first', array('conditions' => array('email' => urldecode($email), 'is_deleted' => 0)));
            if (!empty($userInfo)) {
                $conditions = array('model'=>$model,'user_id'=>$userInfo['User']['id'],'model_id'=>$modelID);
                   $this->Manager->recursive = -1;
                   $manager = $this->Manager->find('first', array('conditions'=>$conditions));
                if (!empty($manager)) {
                    $manager['Manager']['is_confirmed'] = 1;
                    $this->Manager->save($manager);
                    $this->Session->setFlash('You have been activated.', 'flash_success');
                    $this->redirect("/");
                } else {
                    $this->Session->setFlash('Can not find the Manager.', 'flash_error');
                    $this->logErr('error occured: Can not fins the Manager.');
                    $this->redirect("/");
                }
            }


        } else {
            $this->Session->setFlash('Error with parameters.', 'flash_error');
            $this->logErr('error occured: Error with parameters.');
            $this->redirect("/");
        }

    }
     /**
     * AJAX Find managers by email, lastname, or lgn
     * @author vovich     *
     */
    function find()
    {
        Configure::write('debug', '0');
        $this->layout = false;
        
        if ($this->RequestHandler->isAjax()  
            && ($this->request->data['Manager']['email'] || $this->request->data['Manager']['lastname'] 
            || $this->request->data['Manager']['lgn'])
        ) {
            $conditions = array('is_deleted'=>0);
            if ($this->request->data['Manager']['email']) {
                $conditions['email'] =$this->request->data['Manager']['email']; 
            }
            if ($this->request->data['Manager']['lgn']) {
                $conditions['lgn'] =$this->request->data['Manager']['lgn']; 
            }
            if ($this->request->data['Manager']['lastname']) {
                $conditions['lastname'] =$this->request->data['Manager']['lastname']; 
            }
            
            $managers   = $this->Manager->User->find('first', array( 'contain' => array(),'conditions'=>$conditions));

            $assignmodel = $this->request->data['Manager']['model'];
            $modelID = $this->request->data['Manager']['model_id'];

            $this->set(compact('managers'));
            $this->set('assignmodel', $assignmodel);
            $this->set('modelID', $modelID);
        } else {
            exit();
        }

    }

    /**
     * AJAX Find managers by email
     * @author vovich     *
     */
    function findByEmail()
    {
        Configure::write('debug', '0');
        $this->layout = false;
        
        if ($this->RequestHandler->isAjax() && $this->request->data['Manager']['email']) {

            $conditions = array('email' => $this->request->data['Manager']['email'],'is_deleted'=>0) ;
            $managers   = $this->Manager->User->find('first', array( 'contain' => array(),'conditions'=>$conditions));

            $assignmodel = $this->request->data['Manager']['model'];
            $modelID = $this->request->data['Manager']['model_id'];

            $this->set(compact('managers'));
            $this->set('assignmodel', $assignmodel);
            $this->set('modelID', $modelID);
        } else {
            exit();
        }

    }
    /**
     * remove user form the manager
     * @author vovich
     * @param string $model
     * @param int    $modelID
     */
    function remove( $model = null, $model_id = null, $userID=null )
    {
        if ($model && $model_id && $userID) {
            $this->Access->checkAccess('Manager', 'd', $userID);
            $this->Manager->deleteAll(array('Manager.model'=>$model,'Manager.model_id'=>$model_id,'Manager.user_id'=>$userID));
            exit();
            $this->Session->setFlash('Manager has been deleted.', 'flash_success');
            //$this->redirect("/tournaments/edit/".$model_id);
            //Modified by Povstyanoy
            return $this->redirect($_SERVER['HTTP_REFERER']);

        } else {
            $this->Session->setFlash('Can not find the Manager.', 'flash_error');
              $this->logErr('error occured: Can not find the Manager.');
              $this->redirect("/");
        }

    }

}

?>
