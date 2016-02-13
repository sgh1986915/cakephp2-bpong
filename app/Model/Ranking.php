<?php
class Ranking extends AppModel
{
    var $name = 'Ranking';
    var $actsAs = array('Containable');    
    var $belongsTo = array(
      'User' => array('className' => 'User',
          'foreignKey' => 'model_id',
    //      'conditions' => array('Ranking.model' =>'User'),
      ),
      'Team' =>array('className' =>'Team',
          'foreignKey'=>'model_id',
      //     'conditions'=>array('Ranking.model'=>'Team'),
          ),
      'Rankinghistory' =>array('className'=>'Rankinghistory',
          'foreignKey'=>'history_id',)
            
    );
    /**
    * Generall, model="user",model_id = userid
    * 
    * This returns the total number of 
    * 
    * @param mixed $model
    * @param mixed $model_id
    */
    function getUserRank($userid) 
    {
        
        $history = $this->Rankinghistory->getLatestHistory();
        $rank = $this->find(
            'first', array(
            'conditions'=>array(
                'model'=>'User',
                'model_id'=>$userid,
                'history_id'=>$history['Rankinghistory']['id']),
                )
        );
        if (!$rank) {
            return 'Not ranked'; 
        }
         return array(
            'rating'=>$rank['Ranking']['rating'],
            'rank'=>$rank['Ranking']['rank'],
            'totalusers'=>$history['Rankinghistory']['numusers']
            );
    }
}
?>
