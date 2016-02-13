<?php
class RatinghistoriesController extends AppController
{
      var $name = 'Ratinghistories';
      var $uses    = array('Rating','Ratinghistory','Game','User','Team','Teammate','Ranking','Rankinghistory',
      'Affil','Affilspoint','UsersAffil');
        
    function team($teamID) 
    {
        if (!$this->isLoggined()) {
             $this->redirect('/');
        }
          $user = $this->Session->read('loggedUser');
          //only allow superadmins to view right now
        if (!$this->canUserViewStats($user['id'])) {
            $this->redirect('/'); 
        }
            
            
            $ratingPaginate['conditions'] = array(
            'Ratinghistory.model'=>'Team',
            'Ratinghistory.team_id'=>$teamID);
            $ratingPaginate['order'] = array('Ratinghistory.id'=>'DESC');
            $ratingPaginate['contain'] = array('Game'=>(
            array(
                'Team1',
                'Team2',
                'Ratinghistory',
                'Event'=>array('fields'=>array('name','shortname','id','slug')))));
            
            //            return $this->Ratinghistory->find('all',$ratingPaginate);
            $this->paginate = array('Ratinghistory' => $ratingPaginate);
            $this->set('ratingChanges', $this->paginate('Ratinghistory'));
            
            $this->Team->recursive = -1;
            $team = $this->Team->find('first', array('conditions'=>array('id'=>$teamID)));
            $this->set('team', $team);
    }
    function user($userID) 
    {
        if (!$this->isLoggined()) {
             $this->redirect('/');
        }
        $user = $this->Session->read('loggedUser');
        //only allow superadmins to view right now
        if (!$this->canUserViewStats($user['id'])) {
            $this->redirect('/'); 
        }
            
        $ratingPaginate['conditions'] = array(
            'Ratinghistory.model'=>'User',
            'Ratinghistory.user_id'=>$userID);
        $ratingPaginate['order'] = array('Ratinghistory.id'=>'DESC');
        $ratingPaginate['contain'] = array('Team','Game'=>(
            array(
                'Team1',
                'Team2',
                'Ratinghistory',
            'Event'=>array('fields'=>array('name','shortname','id','slug')))));
            
        $this->paginate = array('Ratinghistory' => $ratingPaginate);
        $this->set('ratingChanges', $this->paginate('Ratinghistory'));
            
        $this->User->recursive = -1;
        $user = $this->User->find('first', array('conditions'=>array('id'=>$userID)));
        $this->set('user', $user);
    }
        /* From the old ratings_controller....might want to put this in rankings
        var $components = array('Csv');
       
        function exportRatingsCSV() {
          $results = $this->Rating->getRatingsForCSV();
          return $results;
          if ($results) {
              $this->Csv->addGrid($results);
          } 

          $this->Csv->setFilename("ratings");
          echo $this->Csv->render1();
        }      
        */
        
        
        /* 
        function updateRatingsFromHistory() {
           $ratings = $this->Rating->find('all',array('contain'=>array()));
           foreach ($ratings as $rating) {
               $model = $rating['Rating']['model'];
               $model_id = $rating['Rating']['model_id'];
               if ($model == 'Team') {
                   $ratingHistory = $this->RatingHistory->find('first',array(
                       'conditions'=>array(
                           'model'=>'Team',
                           'team_id'=>$model_id),
                       'contain'=>array(),
                       'order'=>array('id'=>'DESC')));
               }
               else {
                   $ratingHistory = $this->RatingHistory->find('first',array(
                       'conditions'=>array(
                           'model'=>'User',
                           'user_id'=>$model_id),
                       'contain'=>array(),
                       'order'=>array('id'=>'DESC')));
               }
               $rating['Rating']['rating'] = $ratingHistory['RatingHistory']['after'];
               $this->RatingHistory->save($rating);
           }
           return "ok";
       }
       */
}
?>
