<?php
class UsersAffil extends AppModel
{
    var $name = 'UsersAffil';
    var $actsAs = array('Containable', 'SoftDeletable');
    var $recursive = -1;
      
    var $belongsTo = array(
      'User' => array('className' => 'User',
          'foreignKey' => 'user_id',
          'conditions' => array('User.is_deleted' => 0)),     
      'Greek' => array('className' => 'Greek',
          'foreignKey' => 'model_id',
          'conditions' =>array()),
        
      'City' => array('className' => 'City',
          'foreignKey' => 'model_id',
          'conditions' => array()),
        
      'Hometown' => array('className' => 'City',
          'foreignKey' => 'model_id',
          'conditions' => array()),
      'School'=> array('className' => 'School',
          'foreignKey' => 'model_id',
          'conditions'=> array()),           
       );
            
    function getModel($modelName) 
    {
        if ($modelName == 'Hometown') {
            return $this->Hometown; 
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
        
    function getAffilIDsFromPlayersIDs($model,$playerIDs) 
    {
        $result = $this->getAffilsFromPlayerIDs($model, $playerIDs);
        $ids = Set::extract($result, '{n}.UsersAffil.model_id');
        return $this->custom_array_unique($ids);
        //return $this->custom_array_unique(Set::extract($result,'{n}.UsersAffil.affil_id'));
    }   
        //Should not be used for $model=='Organization'
    function getAffilsFromPlayerIDs($model,$playerIDs) 
        {
        $Model = $this->getModel($model);       
                
        $type = $model;
        if ($type == 'City') {
            $type = array('Hometown','City'); 
        }
            
        //return $model;
        $usersAffils = $this->find(
            'all', array(
            'conditions'=>array(
                'UsersAffil.user_id'=>$playerIDs,
                'UsersAffil.is_deleted'=>0,
                'UsersAffil.model'=>$type),
            'contain'=>array())
        );
            //'contain'=>array($model)));
        return $usersAffils;
    }
        
    function updateUserCountForAffil($model,$model_id)
    {
        $Model = $this->getModel($model);
        if (!$Model) {
            return false;
        }
        if ($model == 'City' || $model == 'Hometown') {
            $modelToSearch = array('City','Hometown');
        }
        else {
            $modelToSearch = $model;
        }      
                
        //get the users_affil objects
        $this->recursive = -1;
        $usersAffil = $this->find(
            'all', array('conditions'=>array(
            'model_id'=>$model_id,
            'model'=>$modelToSearch,
            'is_deleted'=>0))
        );    
        //get the user ids
        $userids = Set::extract($usersAffil, '{n}.UsersAffil.user_id');
        $userids = $this->custom_array_unique($userids);
            
        //Now, save the model
        if ($Model->save(array('id'=>$model_id,'userscount'=>count($userids)))) {
            return true;
        }
        else {
            return false;
        }
    }
    function getUsersFromAffil($model,$modelid) 
    {
        $type = $model;
        if ($type == 'City') {
            $type = array('Hometown','City'); 
        }
        //get the users_affil object
        $usersAffil = $this->find(
            'all', array('conditions'=>array(
                'UsersAffil.model_id'=>$modelid,
                'UsersAffil.model'=>$type,
                'UsersAffil.is_deleted'=>0),
            'contain'=>array('User'))
        );                                                                     
        return $usersAffil;          
    }
 
}
?>
