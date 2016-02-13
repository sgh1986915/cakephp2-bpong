<?php
class RankingsController extends AppController
{
    var $name = 'Rankings';
    var $uses    = array('Rating', 'Game', 'Ratinghistory', 'Ranking', 'Rankinghistory', 'Provincestate', 'User', 'Team');
        
    function testGetRanking($userid) 
    {
        return $this->Ranking->getUserRank($userid);
    }
    function take_a_snapshot() 
    {
        $this->Access->checkAccess('RankingsSnapshot', 'c');
        $this->saveRankingsSnapshot();
        $this->Session->setFlash('Snapshot saved.');
        $this->redirect('/pages/update_stats');
    }
    /*
    This saves a snapshot of the Rankings. $dateToUse is a timestamp
    */
    function saveRankingsSnapshot($dateToUse = null) 
    {
        if (!$this->Access->getAccess('RankingsSnapshot', 'c')) {
            return 'Access Denied.'; 
        }
        //First, lets place a record in the rankingshistories table  
        if (!$dateToUse) {
            $mysqldate = date('Y-m-d H:i:s'); 
        } 
        else {
            $mysqldate = date('Y-m-d H:i:s', $dateToUse); 
        } 
        $newRankingHistory['Rankinghistory']['date'] = $mysqldate;
        $this->Rankinghistory->create();
        if (!$this->Rankinghistory->save($newRankingHistory)) {
            return array('problem',$newRankingHistory); 
        } 
        $id = $this->Rankinghistory->getLastInsertID();            
                                                       
        $array['conditions'] = array('rating >'=>0);
        $array['order'] = array('rating'=>'DESC');   
        $array['contain'] = array();
        $array['fields'] = array('id','rating');
        $this->Team->recursive = -1;
        $ratings = $this->Team->find('all', $array);
        $currentRank = 1;
        $ctr = 1;
        $currentRating = 0;
            
        $newRank['model'] = 'Team';
        $newRank['history_id'] = $id;
        $this->Ranking->recursive = -1;
        foreach ($ratings as $rating) {
            $newRank['model_id'] = $rating['Team']['id'];
            $newRank['rating'] = $rating['Team']['rating']; 
            if ($currentRating != $newRank['rating']) {
                $currentRank = $ctr;                    
            }
            $newRank['rank'] = $currentRank;
            unset($newRank['id']);                
                
            $this->Ranking->create($newRank);
            // $newRank['id'] = $this->Ranking->getLastInsertID();
            if (!$this->Ranking->save($newRank)) {
                return array('problem',$newRank); 
            }
            $currentRating = $newRank['rating'];    
            $ctr++;        
        } 
        $numteams = $ctr-1;
        //now do Users
        $this->User->recursive = -1;
        $ratings = $this->User->find('all', $array);
        $currentRank = 1;
        $ctr = 1;
        $currentRating = 0;
            
        $newRank['model'] = 'User';
        $newRank['history_id'] = $id;
        $this->Ranking->recursive = -1;
        foreach ($ratings as $rating) {
            $newRank['model_id'] = $rating['User']['id'];
            $newRank['rating'] = $rating['User']['rating']; 
            if ($currentRating != $newRank['rating']) {
                $currentRank = $ctr;                    
            }
            $newRank['rank'] = $currentRank;
            unset($newRank['id']);                
                
            $this->Ranking->create($newRank);
            //$newRank['id'] = $this->Ranking->getLastInsertID();
            if (!$this->Ranking->save($newRank)) {
                return array('problem',$newRank); 
            }
            $currentRating = $newRank['rating'];    
            $ctr++;        
        } 
        $numusers = $ctr - 1;
        $newRankingHistory['Rankinghistory']['numteams'] = $numteams;
        $newRankingHistory['Rankinghistory']['numusers'] = $numusers;
        $this->Rankinghistory->save($newRankingHistory); 
        return "ok";
    }
    function allteams() 
    {   
              
        if (!$this->isLoggined()) {
             $this->redirect('/');
        }
        $user = $this->Session->read('loggedUser');
            
        //for right now, lets just get the most recent....worry about dates later
        $mostRecentHistory = $this->Rankinghistory->find('first', array('order'=>array('date'=>'DESC')));
        if (!$mostRecentHistory) {
            $this->Session->setFlash('Problem', 'flash_error');
            $this->redirect('/');
        }
            
            
        $rankingPaginate['conditions'] = array('Ranking.model'=>'Team','history_id'=>$mostRecentHistory['Rankinghistory']['id']);
        $rankingPaginate['order'] = array('rank' => 'ASC');
        $rankingPaginate['contain'] = array('Team');
            
        //   $this->Ranking->recursive = -1;
        //  return $this->Ranking->find('all',$rankingPaginate);
            
        $this->paginate = array('Ranking'=>$rankingPaginate);
        $this->set('teamRankings', $this->paginate('Ranking')); 
              
    } 
    /*
    If ($ajax), then this is delivered through Ajax. If not, its delivered through requestAction, so
    make sure to render
    */
    function allusersajax($ajax=1) 
    {  
        //   $this->layout = false;
        $this->pageTitle = 'Official Beer Pong Rankings';
        if (isset($_REQUEST['gender'])) {
            $gender= trim($_REQUEST['gender']);
            $firstname = trim($_REQUEST['firstname']);
            $lastname = trim($_REQUEST['lastname']);
            if ($gender != 'Both') {
                $conditions['User.gender'] = $gender; 
            }
            if (strlen($firstname) > 0) {
                $conditions['User.firstname LIKE'] = '%'.$firstname.'%'; 
            }
            if (strlen($lastname) > 0) {
                $conditions['User.lastname LIKE'] = '%'.$lastname.'%'; 
            }                                        
        }   
        $mostRecentHistory = $this->Rankinghistory->getLatestHistory();        
        $conditions['Ranking.model'] = 'User';
        $conditions['Ranking.history_id'] =  $mostRecentHistory['Rankinghistory']['id'];
            
        $rankingPaginate['conditions'] = $conditions;
        $rankingPaginate['order'] = array('rank' => 'ASC');
        $rankingPaginate['contain'] = array('User'=>array('Address'));
        $rankingPaginate['fields'] = array('User.id','User.firstname','User.lastname','User.lgn','Ranking.rank','Ranking.rating');
           
           // $rankingPaginate['contain'] = array('User'=>array('Address','conditions'=>array(
            //    'Address.provincestate_id'=>$this->request->data['RankingFilter']['provincestate_id'])));
           // $conditions['Address.provincestate_id'] = $this->request->data['Ranking']['provincestate_id'];
            
           
        $this->paginate = array('Ranking'=>$rankingPaginate);
        $this->set('userRankings', $this->paginate('Ranking'));  
        $this->set('numusers', $mostRecentHistory['Rankinghistory']['numusers']); 
            
        $states = $this->Provincestate->find('list', array('country_id'=>array(1,2)));
            
        if(!empty($states)) {
            $states=array('0'=>"Select one")+$states; 
        }
        else {
            $states=array('0'=>"Select one"); 
        }
        $this->set('states', $states);    
        if (!$ajax) { 
            $this->render(); 
        }  
    }
        
    function allusers() 
    {
        $this->pageTitle = 'Official Beer Pong Rankings';
        $mostRecentHistory = $this->Rankinghistory->getLatestHistory(); 
        $this->set('numusers', $mostRecentHistory['Rankinghistory']['numusers']);  
    }
    function calculateRatingChange()
    {
        Configure::write('debug', '0');
        $this->layout = false;
        
        if ($this->RequestHandler->isAjax()) { // && $this->request->data['Manager']['email']){
            $playerRating = $this->request->data['Ranking']['player_rating'];
            $opponentsAverageRating = $this->request->data['Ranking']['opponents_average_rating'];
            $winner = $this->request->data['Ranking']['winner'];
            $cupdif = $this->request->data['Ranking']['cupdif'];
            $error = 1;
            if ($playerRating < 1) { 
                $result = 'Player Rating must be above 0'; 
            }
            else if ($opponentsAverageRating < 1) { 
                $result = 'Opponents Average Rating must be above 0'; 
            }
            else if ($winner < 0) { 
                $result= "You must decide whether player's team won or lost."; 
            }
            else {
                if ($winner > 0) {
                    $calculatedRatingChange = 100 * $this->Ratinghistory->getRatingChange($playerRating, $opponentsAverageRating, $cupdif);
                } else {
                    $calculatedRatingChange = -100 * $this->Ratinghistory->getRatingChange($opponentsAverageRating, $playerRating, $cupdif);                                                
                }
                $calculatedRatingChange = round($calculatedRatingChange, 0);
                $result = 'Calculated Rating Change: '.$calculatedRatingChange;                     
            }

            $this->set('result', $result);
        } else {
            exit();
        }
    }
    function testRatingChange() 
    {
        return $this->Ratinghistory->getRatingChange(5000, 5000, 1);
    }
    function explanation() 
    {
        $this->pageTitle = 'Official Beer Pong Rankings';
        $possibleCupDifs = array(
            1=>'1 or 2',3=>'3',4=>'4',5=>'5+');
        $this->set('possibleCupDifs', $possibleCupDifs);
    }
    function m_getPlayerRankingByRating($user_id, $amf = 0) 
    {
        if (isset($this->request->params['form']['user_id'])) {
            $user_id = mysql_real_escape_string($this->request->params['user_id']['start']);
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = mysql_real_escape_string($this->request->params['form']['amf']);
        } 
          
        //first get the users rating
        $this->User->recursive = -1;
        $user = $this->User->find('first', array('conditions'=>array('id'=>$user_id)));
        if (!$user) {
            return $this->returnMobileResult('User not found', $amf);
        }
        $userRating = $user['User']['rating'];
          
        //Get the number of users rated higher than the user
        $usersAbove = $this->User->find(
            'count', array(
            'conditions'=>array('is_deleted'=>0,'rating >'=>$userRating))
        );
        
        $totalUsers = $this->User->find(
            'count', array(
            'conditions'=>array('is_deleted'=>0,'rating >'=>0))
        );
            
        $start = 10 * floor(($usersAbove)/10) + 1;          
        $closePlayers = $this->m_getPlayerLeaderBoardByRating($start, 10, $amf);
        return array('rank'=>$usersAbove+1,'numplayerstotal'=>$totalUsers,'leaderboard'=>$closePlayers);
        
    } 
    function m_getPlayerLeaderBoardByRating($start = 1, $limit = 10, $amf = 0) 
    {
        if (isset($this->request->params['form']['start'])) {
            $email = mysql_real_escape_string($this->request->params['form']['start']);
        }
        if (isset($this->request->params['form']['limit'])) {
            $pass = mysql_real_escape_string($this->request->params['form']['limit']);
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = mysql_real_escape_string($this->request->params['form']['amf']);
        } 
        $limitStart = $start - 1;
        $users = $this->User->find(
            'all', array(
            'contain'=>array('UsersAffil'=>array('School','Greek','City','Hometown')),
            'order'=>array('User.rating'=>'DESC'),
            'limit'=>$limitStart.','.$limit)
        );  
        foreach ($users as &$user) {  
            foreach ($user['UsersAffil'] as $key => $val) {
                //unset the b.s. affils
                if ($val['model'] != 'City') {
                    unset($user['UsersAffil'][$key]['City']);
                }
                if ($val['model'] != 'Hometown') {
                    unset($user['UsersAffil'][$key]['Hometown']);
                }
                if ($val['model'] != 'Greek') {
                    unset($user['UsersAffil'][$key]['Greek']);
                }
                if ($val['model'] != 'School') {
                    unset($user['UsersAffil'][$key]['School']);
                }
            }
        }
        return $this->returnMobileResult($users, $amf);
    }
        
    /**
     * Players stats page
     * @author Oleg D.
     */
    function players_stats($param1 = null) 
    {
        $modelName = 'User';
        if ($param1 == 's') {
            if(isset($this->request->data['Search']['q'])) {
                $this->Session->write('stats_search', $this->request->data['Search']['q']);
            } elseif ($this->Session->check('stats_search')) {
                $this->request->data['Search']['q'] = $this->Session->read('stats_search');
            }
        } else {
            $this->request->data['Search']['q'] = '';
        }
        
        $mostRecentHistory = $this->Rankinghistory->getLatestHistory();        
        $conditions['Ranking.model'] = $modelName;
        $conditions['Ranking.history_id'] =  $mostRecentHistory['Rankinghistory']['id'];
        
        if (!empty($this->request->data['Search']['q'])) {
            $conditions['OR'][$modelName . '.lgn LIKE'] = '%' . $this->request->data['Search']['q'] . '%'; 
            $conditions['OR'][$modelName . '.lastname LIKE'] = '%' . $this->request->data['Search']['q'] . '%'; 
        }
        
        $rankingPaginate = array(
        'limit' => 50,
        'conditions' => $conditions, 
        'order' => array('rank' => 'ASC'),
        'contain' => array($modelName => array('Address' => array('Provincestate', 'Country', 'conditions' => array('Address.label' => 'Home'))))
        );
        
        $this->paginate = array('Ranking'=>$rankingPaginate);
        $rankings = $this->paginate('Ranking');
        //echo "<pre/>";print_r($rankings);exit;
        $this->set(compact('rankings', 'modelName'));      
    }
    /**
     * AJAX Players stats page
     * @author Oleg D.
     */
    function ajax_players_stats($param1 = null) 
    {
        $modelName = 'User';
        if ($param1 == 's') {
            if (isset($_REQUEST['search'])) {
                $this->request->data['Search']['q'] = $_REQUEST['search'];    
            }
            if(isset($this->request->data['Search']['q'])) {
                $this->Session->write('ajax_stats_search', $this->request->data['Search']['q']);
            } elseif ($this->Session->check('ajax_stats_search')) {
                $this->request->data['Search']['q'] = $this->Session->read('ajax_stats_search');
            }
        } else {
            $this->request->data['Search']['q'] = '';
        }

        $conditions = [];
        //		$mostRecentHistory = $this->Rankinghistory->getLatestHistory();
        //		$conditions['Ranking.model'] = $modelName;
        //		$conditions['Ranking.history_id'] =  $mostRecentHistory['Rankinghistory']['id'];
        
        if (!empty($this->request->data['Search']['q'])) {
            $conditions['OR'][$modelName . '.lgn LIKE'] = '%' . $this->request->data['Search']['q'] . '%'; 
            $conditions['OR'][$modelName . '.lastname LIKE'] = '%' . $this->request->data['Search']['q'] . '%'; 
        }

        $rankingPaginate = array(
        'limit' => 20,
        'conditions' => $conditions, 
        'order' => array('rank' => 'ASC'),
        'contain' => array($modelName => array('Address' => array('Provincestate', 'Country', 'conditions' => array('Address.label' => 'Home'))))
        );
        
        $this->paginate = array('Ranking'=>$rankingPaginate);
        $rankings = $this->paginate('Ranking');
        //echo "<pre/>";print_r($rankings);exit;
        $this->set(compact('rankings', 'modelName'));
                
        $this->layout = false;        
        $this->render();    
    }
    
    /**
     * Schools stats page
     * @author Oleg D.
     */
    function teams_stats($param1 = null) 
    {
        $modelName = 'Team';
        if ($param1 == 's') {
            if(isset($this->request->data['Search']['q'])) {
                $this->Session->write('stats_search', $this->request->data['Search']['q']);
            } elseif ($this->Session->check('stats_search')) {
                $this->request->data['Search']['q'] = $this->Session->read('stats_search');
            }
        } else {
            $this->request->data['Search']['q'] = '';
        }
        $conditions = array();
        $conditions['Team.status <>'] = 'Deleted';
        $conditions['Team.people_in_team'] = 2;
        $conditions['Team.total_wins >'] = 0;
        if (!empty($this->request->data['Search']['q'])) {
            $conditions[$modelName . '.name LIKE'] = '%' . $this->request->data['Search']['q'] . '%'; 
        }
        $rankingPaginate = array('limit'=>50,
            'conditions'=>$conditions,
            'order'=>array('Team.rating'=>'DESC'),
            'contain'=>array());
        $this->paginate = array('Team'=>$rankingPaginate);
        $rankings = $this->paginate('Team');
        $this->set('rankings', $rankings);     
    }
    /**
     * AJAX Teams stats page
     * @author Oleg D.
     */
    function ajax_teams_stats($param1 = null) 
    {
        $modelName = 'Team';
        if ($param1 == 's') {
            if (isset($_REQUEST['search'])) {
                $this->request->data['Search']['q'] = $_REQUEST['search'];    
            }
            if(isset($this->request->data['Search']['q'])) {
                $this->Session->write('ajax_stats_search', $this->request->data['Search']['q']);
            } elseif ($this->Session->check('ajax_stats_search')) {
                $this->request->data['Search']['q'] = $this->Session->read('ajax_stats_search');
            }
        } else {
            $this->request->data['Search']['q'] = '';
        }
        $conditions = array();
        $conditions['Team.status <>'] = 'Deleted';
        $conditions['Team.people_in_team'] = 2;
        $conditions['Team.total_wins >'] = 0;
        
        if (!empty($this->request->data['Search']['q'])) {
            $conditions[$modelName . '.name LIKE'] = '%' . $this->request->data['Search']['q'] . '%'; 
        }       
        $rankingPaginate = array('limit'=>50,
            'conditions'=>$conditions,
            'order'=>array('Team.rating'=>'DESC'),
            'contain'=>array());
        $this->paginate = array('Team'=>$rankingPaginate);
        $rankings = $this->paginate('Team');
        $this->set('rankings', $rankings); 
        
        $this->layout = false;        
        $this->render();    
    }
    
    /**
     * Schools stats page
     * @author Oleg D.
     */
    function schools_stats($param1 = null) 
    {
        $modelName = 'School';
        if ($param1 == 's') {
            if(isset($this->request->data['Search']['q'])) {
                $this->Session->write('stats_search', $this->request->data['Search']['q']);
            } elseif ($this->Session->check('stats_search')) {
                $this->request->data['Search']['q'] = $this->Session->read('stats_search');
            }
        } else {
            $this->request->data['Search']['q'] = '';
        }
        $ModelObject = ClassRegistry::init($modelName);       
        
        $conditions = array();
        if (!empty($this->request->data['Search']['q'])) {
            $conditions[$modelName . '.name LIKE'] = '%' . $this->request->data['Search']['q'] . '%'; // Make changes for each model
        }else { //if theres no searchterm, show all with users>0
            $conditions['School.userscount >'] = 0;
        }

        $rankingPaginate = array(
        'limit' => 50,
        'conditions' => $conditions, 
        'order' => array($modelName . '.points' => 'DESC', $modelName . '.id' => 'ASC'),
        'contain' => array('City', 'Provincestate', 'Country')
        );
        
        $this->paginate = array($modelName => $rankingPaginate);
        $rankings = $this->paginate($ModelObject);
        //echo "<pre/>";print_r($rankings);exit;
        $this->set(compact('rankings', 'modelName'));          
    }
    
    /**
     * AJAX Schools stats page
     * @author Oleg D.
     */
    function ajax_schools_stats($param1 = null) 
    {
        $modelName = 'School';
        if ($param1 == 's') {
            if (isset($_REQUEST['search'])) {
                $this->request->data['Search']['q'] = $_REQUEST['search'];    
            }
            if(isset($this->request->data['Search']['q'])) {
                $this->Session->write('ajax_stats_search', $this->request->data['Search']['q']);
            } elseif ($this->Session->check('ajax_stats_search')) {
                $this->request->data['Search']['q'] = $this->Session->read('ajax_stats_search');
            }
        } else {
            $this->request->data['Search']['q'] = '';
        }
        $ModelObject = ClassRegistry::init($modelName);       
        
        $conditions = array();
        if (!empty($this->request->data['Search']['q'])) {
            $conditions[$modelName . '.name LIKE'] = '%' . $this->request->data['Search']['q'] . '%'; // Make changes for each model
        }
        else { //if theres no searchterm, show all with users>0
            $conditions['School.userscount >'] = 0;
        }

        $rankingPaginate = array(
        'limit' => 50,
        'conditions' => $conditions, 
        'order' => array($modelName . '.points' => 'DESC', $modelName . '.id' => 'ASC'),
        'contain' => array('City', 'Provincestate', 'Country')
        );
        
        $this->paginate = array($modelName => $rankingPaginate);
        $rankings = $this->paginate($ModelObject);
        //echo "<pre/>";print_r($rankings);exit;
        $this->set(compact('rankings', 'modelName')); 
        
        $this->layout = false;        
        $this->render();    
    }
    
    /**
     * Greeks stats page
     * @author Oleg D.
     */
    function greeks_stats($param1 = null) 
    {
        $modelName = 'Greek';
        if ($param1 == 's') {
            if(isset($this->request->data['Search']['q'])) {
                $this->Session->write('stats_search', $this->request->data['Search']['q']);
            } elseif ($this->Session->check('stats_search')) {
                $this->request->data['Search']['q'] = $this->Session->read('stats_search');
            }
        } else {
            $this->request->data['Search']['q'] = '';
        }
        $ModelObject = ClassRegistry::init($modelName);       
        
        $conditions = array();
        if (!empty($this->request->data['Search']['q'])) {
            $conditions[$modelName . '.name LIKE'] = '%' . $this->request->data['Search']['q'] . '%'; // Make changes for each model
        }
        else {
            $conditions['Greek.userscount >'] = 0;
        }

        $rankingPaginate = array(
        'limit' => 50,
        'conditions' => $conditions, 
        'order' => array($modelName . '.points' => 'DESC', $modelName . '.id' => 'ASC')
        );
        
        $this->paginate = array($modelName => $rankingPaginate);
        $rankings = $this->paginate($ModelObject);
        //echo "<pre/>";print_r($rankings);exit;
        $this->set(compact('rankings', 'modelName'));        
    }
    
    /**
     * AJAX Greeks stats page
     * @author Oleg D.
     */
    function ajax_greeks_stats($param1 = null) 
    {
        $modelName = 'Greek';
        if ($param1 == 's') {
            if (isset($_REQUEST['search'])) {
                $this->request->data['Search']['q'] = $_REQUEST['search'];    
            }
            if(isset($this->request->data['Search']['q'])) {
                $this->Session->write('ajax_stats_search', $this->request->data['Search']['q']);
            } elseif ($this->Session->check('ajax_stats_search')) {
                $this->request->data['Search']['q'] = $this->Session->read('ajax_stats_search');
            }
        } else {
            $this->request->data['Search']['q'] = '';
        }
        $ModelObject = ClassRegistry::init($modelName);       
        
        $conditions = array();
        if (!empty($this->request->data['Search']['q'])) {
            $conditions[$modelName . '.name LIKE'] = '%' . $this->request->data['Search']['q'] . '%'; // Make changes for each model
        }
        else {
            $conditions['Greek.userscount >'] = 0;
        }

        $rankingPaginate = array(
        'limit' => 50,
        'conditions' => $conditions, 
        'order' => array($modelName . '.points' => 'DESC', $modelName . '.id' => 'ASC')
        );
        
        $this->paginate = array($modelName => $rankingPaginate);
        $rankings = $this->paginate($ModelObject);
        //echo "<pre/>";print_r($rankings);exit;
        $this->set(compact('rankings', 'modelName'));
        
        $this->layout = false;        
        $this->render();    
    }
    
    /**
     * Cities stats page
     * @author Oleg D.
     */
    function cities_stats($param1 = null) 
    {
        $modelName = 'City';
        if ($param1 == 's') {
            if(isset($this->request->data['Search']['q'])) {
                $this->Session->write('stats_search', $this->request->data['Search']['q']);
            } elseif ($this->Session->check('stats_search')) {
                $this->request->data['Search']['q'] = $this->Session->read('stats_search');
            }
        } else {
            $this->request->data['Search']['q'] = '';
        }

        $ModelObject = ClassRegistry::init($modelName);  
        //print_r($ModelObject->find('first')); 
        //exit;    
        
        $conditions = array();
        if (!empty($this->request->data['Search']['q'])) {
            $conditions[$modelName . '.name LIKE'] = '%' . $this->request->data['Search']['q'] . '%'; // Make changes for each model
        }
        $conditions['total_wins >']=0;

        $rankingPaginate = array(
        'fields' => array($modelName . '.id', $modelName . '.points', $modelName . '.name', $modelName . '.total_wins', $modelName . '.total_losses', $modelName . '.total_cupdif', 'Country.iso2', 'Provincestate.name'),
        'limit' => 50,
        'conditions' => $conditions, 
        'order' => array($modelName . '.points' => 'DESC', $modelName . '.id' => 'ASC'),
        'contain' => array('Provincestate', 'Country')
        );
        
        $this->paginate = array($modelName => $rankingPaginate);
        $rankings = $this->paginate($ModelObject);
        //echo "<pre/>";print_r($rankings);exit;
        $this->set(compact('rankings', 'modelName'));          
    }

    /**
     * AJAX Cities stats page
     * @author Oleg D.
     */
    function ajax_cities_stats($param1 = null) 
    {
        $modelName = 'City';
        if ($param1 == 's') {
            if (isset($_REQUEST['search'])) {
                $this->request->data['Search']['q'] = $_REQUEST['search'];    
            }
            if(isset($this->request->data['Search']['q'])) {
                $this->Session->write('ajax_stats_search', $this->request->data['Search']['q']);
            } elseif ($this->Session->check('ajax_stats_search')) {
                $this->request->data['Search']['q'] = $this->Session->read('ajax_stats_search');
            }
        } else {
            $this->request->data['Search']['q'] = '';
        }

        $ModelObject = ClassRegistry::init($modelName);  
        //print_r($ModelObject->find('first')); 
        //exit;    
        
        $conditions = array();
        if (!empty($this->request->data['Search']['q'])) {
            $conditions[$modelName . '.name LIKE'] = '%' . $this->request->data['Search']['q'] . '%'; // Make changes for each model
        }
        $conditions['total_wins >']=0;

        $rankingPaginate = array(
        'fields' => array($modelName . '.id', $modelName . '.points', $modelName . '.name', $modelName . '.total_wins', $modelName . '.total_losses', $modelName . '.total_cupdif', 'Country.iso2', 'Provincestate.name'),        
        'limit' => 50,
        'conditions' => $conditions, 
        //'order' => array($modelName . '.points' => 'DESC', $modelName . '.id' => 'ASC'),
        'order' => array($modelName . '.points' => 'DESC'),
            'contain' => array('Provincestate', 'Country')
        );
        
        $this->paginate = array($modelName => $rankingPaginate);
        $rankings = $this->paginate($ModelObject);
        //echo "<pre/>";print_r($rankings);exit;
        $this->set(compact('rankings', 'modelName'));
        
        $this->layout = false;
        $this->render();    
    }    
    
    /**
     * Organizations stats page
     * @author Oleg D.
     */
    function organizations_stats($param1 = null) 
    {
        
    }
    /**
     * States stats page
     * @author Oleg D.
     */
    function states_stats($param1 = null) 
    {
        
    }
    /**
     * Countries stats page
     * @author Oleg D.
     */
    function countries_stats($param1 = null) 
    {
        
    }
}
?>
