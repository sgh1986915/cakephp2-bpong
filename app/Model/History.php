<?php
class History extends AppModel
{

    var $name = 'History';

    var $belongsTo = array(
                                          'User' => array(
                                                            'className'    => 'User',
                                                            'foreignKey'    => 'user_id'
                                              )
      );


     /**
     * delete/add assigment to the tournEvent
     * @author vovich
     * @param string type - must be 'add' or 'delete'
     * @param int                                     $teamID
     * @param array inputData
     *                  ['user_id']
     *                  ['model']
     *                     ['model_id']
     *                    ['affected_user_id'] -
     */
    function teamAssigment($type='add',$teamID = null, $inputData = array()) 
    {

        $Team           = ClassRegistry::init('Team');
        $Team->recursive = -1;
        $teamInformation = $Team->find('first', array('conditions'=>array('Team.id'=>$teamID)));
        unset($Team);

        if (empty($teamInformation)) {
            return false;
        }

        $data['History'] = $inputData;

        if ($type == 'add') {
            $data['History']['type']            =  "Team Add Assigment";
            $data['History']['link']              =  Router::url(array('controller'=>'teams','action'=>'view',$teamInformation['Team']['slug'],$teamID));
            $data['History']['description']  =  "Team Add Assigment";
        } else {
            $data['History']['type']            =  "Team Remove Assigment";
            $data['History']['link']              =  Router::url(array('controller'=>'teams','action'=>'view',$teamInformation['Team']['slug'],$teamID));
            $data['History']['description']  =  "Team Remove Assigment";
        }

        $this->create();

        return $this->save($data);

    }

     /**
     * Delete/Change room option/Completed Room
     * @author vovich
     * @param string type 'delete','completed','changed'
     * @param int                                        $roomID
     * @param array inputData
     *                  ['user_id']
     *                  ['model']
     *                     ['model_id']
     *                    ['affected_user_id'] -
     */
    function rooms($type='delete',$roomID = null, $inputData = array()) 
    {
        $data['History'] = $inputData;
        $data['History']['link']              =  Router::url(array('controller'=>'rooms','action'=>'view',$roomID));

        switch ($type) {
        case "delete":
            $data['History']['type']            =  "Room deleted";
            $data['History']['description']  =  "Room has been deleted";
            break;
        case "completed":
            $data['History']['type']            =  "Room completed";
            $data['History']['description']  =  "Room has been completed";
            break;
        case "changed":
            $data['History']['type']            =  "Room changed";
            $data['History']['description']  =  "Room parameters has been changed";
            break;
        }

        $this->create();

        return $this->save($data);

    }


     /**
     * Package is changed
     * @author vovich
     * @param int             $signupID
     * @param array inputData
     *                  ['user_id']
     *                  ['model']
     *                     ['model_id']
     *                    ['affected_user_id'] -
     */
    function packageIsChanged($signupID = null, $inputData = array()) 
    {
        $data['History'] = $inputData;
        $data['History']['type']            =  "Changed Package";
        $data['History']['link']              =  Router::url(array('controller'=>'signups','action'=>'signupDetails',$signupID));
        $data['History']['description']  =  "Package has been changed";
        $this->create();

        return $this->save($data);

    }

    /**
     * Cancell Signup
     * @author vovich
     * @param int             $signupID
     * @param array inputData
     *                  ['user_id']
     *                  ['model']
     *                     ['model_id']
     *                    ['affected_user_id'] -
     */
    function signupCancell($signupID = null, $inputData = array()) 
    {

        $data['History'] = $inputData;
        $data['History']['type']            =  "Cancell Signup";
        $data['History']['link']              =  Router::url(array('controller'=>'signups','action'=>'signupDetails',$signupID));
        $data['History']['description']  =  "Signup has been cancelled";

        $this->create();

        return $this->save($data);

    }
    
        /**
     *  Signup transferring
     * @author vovich
     * @param int             $signupID
     * @param array inputData
     *                  ['user_id']
     *                  ['model']
     *                     ['model_id']
     *                    ['affected_user_id'] -
     */
    function signupTransferring($signupID = null, $inputData = array()) 
    {

        $data['History'] = $inputData;
        $data['History']['type']            =  "Signup Transferring";
        $data['History']['link']              =  Router::url(array('controller'=>'signups','action'=>'signupDetails',$signupID));
        $data['History']['description']  =  "Signup has been transferred to another user";

        $this->create();

        return $this->save($data);

    }
}
?>