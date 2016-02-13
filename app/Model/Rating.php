<?php
class Rating extends AppModel
{
    var $name = 'Rating';   
    var $useTable = false;
    /* These are all obsolete, but hold onto them */
    function getAllUserIDs() 
    {
        //This returns all userids that have rating>0
        $query = "SELECT users.id FROM users WHERE users.rating > 0";
        //$query = "SELECT ratings.model_id FROM ratings WHERE ratings.model='User'";
        $results = $this->query($query); 
        $userIDs = Set::extract($results, '{n}.users.id');
        return $userIDs;
    }
    function getRatingsForCSV($model) 
    {
        if (!($model == 'Team' || $model == 'User')) { return 1; 
        }
        $query = "SELECT * FROM 'ratings' WHERE  'model'='".$model."' ORDER BY 'rating' DESC";
        return $this->query($query);
    }  
    function getNumberOfObjectsAhead($rating,$model = 'User') 
    {
        $query = "SELECT count(*) as c FROM `ratinghistories` WHERE `model`='".$model."' AND `rating` > ".$rating;
        $result = $this->query($query);      
        return $result[0][0][c];     
    }
    function getUserRank($userID) 
    {
        $this->recursive = -1;
        $userRating = $this->find('first', array('conditions'=>array('model'=>'User','model_id'=>$userID)));
        return $this->getUserRankByRating($userRating['Rating']['rating']);
    }
    function getUserRankByRating($rating) 
    {
        return $this->getNumberOfObjectsAhead($rating) + 1;
    }

    /*   function setRating($model,$modelID,$rating) {
      $this->recursive = -1;
      $cr = $this->find('first',array('conditions'=>array(
          'model'=>$model,
          'model_id'=>$modelID)));
      if ($cr) {
          $cr['Rating']['rating'] = $rating;
          return $this->save($cr);
      }
      else {
          $this->create();
          $newRating['model'] = $model;
          $newRating['model_id'] = $modelID;
          $newRating['rating'] = $rating;
          return $this->save($newRating);
      }
    } */
}
?>
