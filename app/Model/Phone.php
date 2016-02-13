<?php
class Phone extends AppModel
{

    var $name = 'Phone';

    /**
     * Add phone
     * @author Oleg D.
     */
    function addPhone($phone, $userID, $type= 'Home') 
    {
        $oldPhone = $this->find('first', array('conditions' => array('model' => 'User', 'model_id' => $userID, 'phone' => $phone, 'type' => $type,'is_deleted' => 0)));
        if (!empty($oldPhone['Phone']['id'])) {
            $id = $oldPhone['Phone']['id'];
        } else {
            $this->create();
            $this->save(array('model' => 'User', 'model_id' => $userID, 'phone' => $phone, 'type' => $type));
            $id = $this->getLastInsertID();
        }
        
        return $id;        
    }

}
?>