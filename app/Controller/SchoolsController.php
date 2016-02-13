<?php
class SchoolsController extends AppController
{
    var $name = 'Schools';
    var $uses = array('School', 'Team', 'Game');

    /**
     * Show School
     * @author Oleg D.
     */
    function show($id) 
    {
        $modelName = 'School';    
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
      
    function m_getSchoolsByGeo($lat,$lng,$radius,$limit = 10,$amf = 0) 
    {
        if (isset($this->request->params['form']['lat'])) {
            $lat = $this->request->params['form']['lat']; 
        }
        if (isset($this->request->params['form']['lng'])) {
            $lng = $this->request->params['form']['lng']; 
        }
        if (isset($this->request->params['form']['radius'])) {
            $radius = $this->request->params['form']['radius']; 
        }
        if (isset($this->request->params['form']['limit'])) {
            $limit = $this->request->params['form']['limit']; 
        }
        if (isset($this->request->params['form']['amf'])) {
              $amf= $this->request->params['form']['amf']; 
        }
        $result = $this->School->returnGeo($lat, $lng, $radius, 'schools', $limit);
        return $this->returnMobileResult($result, $amf);
    }
    function m_getSchool($id,$amf = 0) 
    {
        if (isset($this->request->params['form']['id'])) {
            $id = $this->request->params['form']['id']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        } 
        $result =$this->School->find(
            'first', array(
            'conditions'=>array('School.id'=>$id),
            'contain'=>array('Provincestate'))
        );
        return $this->returnMobileResult($result, $amf);
    }
      
    function m_findSchools($nameToSearch=null,$country_id=0,$state_id=0,$city_id=0,$limit = 20,$mustHaveUsers=0,$amf = 0) 
    {
        if (isset($this->request->params['form']['nameToSearch'])) {
            $nameToSearch = $this->request->params['form']['nameToSearch']; 
        }
        if (isset($this->request->params['form']['country_id'])) {
            $country_id = $this->request->params['form']['country_id']; 
        }
        if (isset($this->request->params['form']['state_id'])) {
            $state_id = $this->request->params['form']['state_id']; 
        }
        if (isset($this->request->params['form']['city_id'])) {
            $city_id = $this->request->params['form']['city_id']; 
        }
        if (isset($this->request->params['form']['amf'])) {
              $amf= $this->request->params['form']['amf']; 
        }
        if (isset($this->request->params['form']['limit'])) {
              $limit= $this->request->params['form']['limit']; 
        }
        if (isset($this->request->params['form']['mustHaveUsers'])) {
              $mustHaveUsers= $this->request->params['form']['mustHaveUsers']; 
        }              
        $conditions = array();
        $orderBy = array();
        if ($nameToSearch != '') {
            $conditions['School.name LIKE'] = '%'.$nameToSearch.'%'; 
        }
        if ($country_id > 0) { 
            $conditions['School.country_id'] = $country_id; 
        }
        if ($state_id > 0) {
            $conditions['School.provincestate_id'] = $state_id; 
        }
        if ($city_id > 0) {
            $conditions['School.city_id'] = $city_id; 
        }
        if ($mustHaveUsers) {
            $conditions['School.userscount >'] = 0;
            $orderBy['School.userscount'] = 'DESC';
        }
        if (!($country_id > 0) && !($state_id > 0) && !($city_id > 0) && ($nameToSearch == '')) { 
            return $this->returnMobileResult(array(), $amf); 
        }
        //     return $conditions;    
        //$this->School->recursive = 0;     
        $schools = $this->School->find(
            'all', array(
            'conditions'=>$conditions,
            'contain'=>array('City','Country','Provincestate'),
            'order'=>$orderBy,
            'limit'=>$limit)
        );
        foreach ($schools as &$school) {
            $school['School']['city'] = $school['City']['name'];
            $school['School']['country'] = $school['Country']['name'];
            $school['School']['state'] = $school['Provincestate']['name'];
            unset($school['City']);
            unset($school['Provincestate']);
            unset($school['Country']);
        }
        return $this->returnMobileResult($schools, $amf); 
             
    }
      
    /**
     * Schools autocompleter
     * @author Oleg D.
     */
    function autocomplete() 
    {
        Configure::write('debug', 0);
        
        if (isset($_REQUEST["q"]) && $_REQUEST["q"]) {
            $q = strtolower($_REQUEST["q"]);
            
            $countryID = intval(@$_REQUEST["country_id"]); 
            $stateID = intval(@$_REQUEST["state_id"]); 
            $city = trim(ucwords(strtolower(@$_REQUEST["city"])));
                        
            $conditions['name LIKE '] = trim($q) . '%';
            if ($countryID) { 
                $conditions['country_id'] = $countryID;         
            }
            if ($stateID) {
                $conditions['provincestate_id'] = $stateID;
            }   
            if ($stateID && $countryID && $city) {
                $cityID = $this->School->City->field('id', array('name' => $city, 'country_id' => $countryID, 'provincestate_id' => $stateID));    
                if ($cityID) {
                    $conditions['city_id'] = $cityID;    
                }
            }
                       
            $results = $this->School->find('all', array('conditions' => $conditions, 'fields' => array('name')));
            if (!empty($results)) {
                foreach ($results as $result) {
                    echo $result['School']['name'] . "|\n";
                }                  
            } 
        }
        exit;    
    } 
}
?>
