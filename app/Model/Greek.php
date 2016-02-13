<?php
class Greek extends AppModel
{
    var $name = 'Greek';
    var $actsAs = array('Containable');
    var $recursive = -1;



    function getGreekByID($id) 
    {
        $this->recursive = -1;
        $result =  $this->find('first', array('conditions'=>array('id'=>$id)));
        return $result;
    }
}
?>
