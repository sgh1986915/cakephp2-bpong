<?php
class Rankinghistory extends AppModel
{
        var $name = 'Rankinghistory';
        var $actsAs = array('Containable'); 
        var $hasMany = array(
          'Ranking'=>array(
              'className'=>'Ranking',
              'foreignKey'=>'history_id',
              'dependent'=>false)
          );
        function getLatestHistory() 
        {
            return $this->find('first', array('order'=>array('date'=>'DESC')));
        } 
        function getLatestHistoryID() 
        {
            $result = $this->getLatestHistory();
            return $result['Rankinghistory']['id'];
        }
}
?>
