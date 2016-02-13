<?php
class Affilspoint extends AppModel
{
     var $name = 'Affilspoint';
     var $actsAs= array('Containable');  
     var $belongsTo = array(
          'Organization'=>array('className'=>'Organization',
                              'foreignKey'=>'model_id',
                              'dependent'=>false,
          ) ,
          'School'=>array('className'=>'School',
                              'foreignKey'=>'model_id',
                              'dependent'=>false,
          ) ,
          'Greek'=>array('className'=>'Greek',
                              'foreignKey'=>'model_id',
                              'dependent'=>false,
          ) ,
          'City'=>array('className'=>'City',
                              'foreignKey'=>'model_id',
                              'dependent'=>false,
          ) ,
          'Game' => array('className' => 'Game',
                              'foreignKey' => 'game_id',
                              'dependent'=>false
          ) ,
          'Team'=>array('className'=>'Team',
              'foreignKey'=>'team_id')   
     );
     var $pointStructure = array(
      'Winner'=>
          array(1=>5,2=>7,3=>10,4=>12,5=>15),
      'Loser'=>
          array(1=>-3,2=>-4,3=>-6,4=>-7,5=>-9));
            
     function removeAffilPointsForGame($gameID) 
     {
          $this->recursive = -1;
         $results = $this->find(
             'all', array('conditions'=>array(
             'game_id'=>$gameID,
             'status <>' => 'Deleted'))
         );
         foreach ($results as $affilsPoint) {
             $affilsPoint['AffilsPoint']['status'] = 'Deleted';
             $this->save($affilsPoint['AffilsPoint']);
         }            
     }
        //$cupdif is always positive
        function insertAffilsPoint($model,$modelid,$game,$teamID,$isWinner) 
        {
            $newAffilsPoint['model'] = $model;
            $newAffilsPoint['model_id'] = $modelid;
            $newAffilsPoint['game_id'] = $game['id'];
            $newAffilsPoint['team_id'] = $teamID;
            $effectivecupdif = $this->getEffectiveCupDif($game);
            $newAffilsPoint['points'] = $this->getPointsForGame($effectivecupdif, $isWinner);
            $newAffilsPoint['status'] = 'Active';
            $mult = 1;
            if ($isWinner) { $newAffilsPoint['win'] = 1; 
            }
            else {
                $newAffilsPoint['loss'] = 1;
                $mult = -1;
            }
            if ($modelid['numots'] > 0) { 
                $newAffilsPoint['cupdif'] = $game['cupdif']; 
            }
            else { 
                $newAffilsPoint['cupdif'] = 1; 
            }
            $this->create();
            return $this->save($newAffilsPoint);
        }
        function getPointsForGame($cupdif,$isWinner) 
        {
            if ($cupdif > 5) {
                $cupdif = 5; 
            }
            if ($isWinner) {
                return $this->pointStructure['Winner'][$cupdif]; 
            }
            else {
                return $this->pointStructure['Loser'][$cupdif]; 
            }        
        }
           
        function recalculatePointsForAffil($model,$model_id) 
        {
            /*
            Get the Affil
            */
            $Model = $this->getModel($model);
            if (!$Model) {
                return false; 
            }
            $Model->recursive = -1;
            $affil = $Model->find('first', array('conditions'=>array('id'=>$model_id)));
            
            /*
            Get the points
            */
            $this->recursive = -1;
            $allPoints = $this->find(
                'all', array('conditions'=>array(
                'status'=>'active',
                'model'=>$model,
                'model_id'=>$model_id))
            );
                                         
            $eachPoint = Set::extract($allPoints, '{n}.Affilspoint.points');
            $eachWin = Set::extract($allPoints, '{n}.Affilspoint.win');
            $eachLoss = Set::extract($allPoints, '{n}.Affilspoint.loss');
            $eachcupdif= Set::extract($allPoints, '{n}.Affilspoint.cupdif');
            return $Model->save(
                array(
                'id'=>$model_id,
                'points'=>array_sum($eachPoint),
                'total_wins'=>array_sum($eachWin),
                'total_losses'=>array_sum($eachLoss),
                'total_cupdif'=>array_sum($eachcupdif))
            );
        }
        function getModel($modelName) 
        {
            if ($modelName == 'Organization') {
                return $this->Organization; 
            }
            if ($modelName == 'City') {
                return $this->City; 
            }
            if ($modelName == 'School') {
                return $this->School; 
            }
            if ($modelName == 'Greek') {
                return $this->Greek; 
            }
            return null;
        }
              
}
?>
