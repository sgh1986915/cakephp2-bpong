<?php
class Banneddevice extends AppModel
{
    
     var $name = 'Banneddevice';
     
    function isBanned($deviceID) 
    {
        return $this->find(
            'first', array('conditions'=>array(
            'deviceid'=>$deviceID))
        );     
    }
}
?>