<?php
class GreeksController extends AppController
{
  
    var $name = 'Greeks';
    var $uses = array('Greek', 'Team', 'Game');
    
    /**
     * Show Greek
     * @author Oleg D.
     */
    function show($id) 
    {
        $modelName = 'Greek';
        $affil = $this->{$modelName}->find('first', array('conditions' => array($modelName . '.id' => $id)));
        if (empty($affil[$modelName]['name'])) {
            return $this->redirect('/');    
        }
        $title = $affil[$modelName]['name'];
        
        $chartInfo = array();
        $teams = $this->Team->Teammate->getAffilActiveTeamsIDs($modelName, $id);
        if (!empty($teams)) {
            $chartInfo = $this->Team->geatTeamsGamesForChart($teams, 15);
        }        
        $this->paginate = array(
         'limit' => 15,
         'fields' => array('Event.name', 'Event.id', 'Team1.name', 'Team1.id', 'Team1.slug', 'Team2.name', 'Team2.id', 'Team2.slug', 'Brackettype.*', 'Game.*'),
         'contain' => array('Event', 'Team1', 'Team2', 'Brackettype'),
         'order' => array('Game.created' => 'DESC', 'Game.id' => 'DESC'),
         'conditions' => array('OR' => array('team1_id' => $teams, 'team2_id' => $teams), 'AND' => array('Game.status' => 'Completed'))
        
        );    
        
        $games = $this->paginate('Game');
        
        $this->set(compact('modelName', 'id', 'affil', 'title', 'title', 'chartInfo'));            
    }    
    
    function m_getGreek($id,$amf= 0) 
    {
        if (isset($this->request->params['form']['id'])) {
            $id = $this->request->params['form']['id']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }
        $result =  $this->Greek->getGreekByID($id);
        return $this->returnJSONResult($result);
    }                 
    //$type can be 'Fraternity'
    function m_findGreeks($name=null,$type=null, $mustHaveUsers = 0,$limit = 10, $amf = 0) 
    {
        if (isset($this->request->params['form']['name'])) {
            $name = $this->request->params['form']['name']; 
        }
        if (isset($this->request->params['form']['type'])) {
            $type = $this->request->params['form']['type']; 
        }
        if (isset($this->request->params['form']['mustHaveUsers'])) {
            $mustHaveUsers = $this->request->params['form']['mustHaveUsers']; 
        }
        if (isset($this->request->params['form']['limit'])) {
            $limit = $this->request->params['form']['limit']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }
        
        $conditions = array('name LIKE'=>'%'.$name.'%');
        $orderBy = array();
        if ($mustHaveUsers) {
            $conditions['Greek.userscount >'] = 0;
            $orderBy['Greek.userscount'] = 'DESC';
        }
        if ($type == 'Fraternity' || $type == 'Sorority') {
            $conditions['type'] = $type; 
        }
        $result = $this->Greek->find(
            'all', array('conditions' => $conditions,
            'orderBy'=>$orderBy,
            'limit'=>$limit,
            'fields'=>array('id','name','shortname','type'))
        );
        return $this->returnMobileResult($result, $amf);
    }
    
    /**
     * Greeks autocompleter
     * @author Oleg D.
     */
    function autocomplete() 
    {
        Configure::write('debug', 0);
        
        if (isset($_REQUEST["q"]) && $_REQUEST["q"]) {
            $q = strtolower($_REQUEST["q"]);
            
            $type = strtolower(@$_REQUEST["type"]); 

            $conditions['name LIKE '] = trim($q) . '%';
            if ($type) {
                $conditions['type'] = $type;
            }   
                       
            $results = $this->Greek->find('all', array('conditions' => $conditions, 'fields' => array('name')));
            if (!empty($results)) {
                foreach ($results as $result) {
                    echo $result['Greek']['name'] . "|\n";
                }                  
            } 
        }
        exit;    
    }     
}
?>
