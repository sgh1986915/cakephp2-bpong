<?php
class CitiesController extends AppController
{
    var $name = 'Cities';
    var $uses = array('City','Provincestate','Country', 'Team', 'Game');
      
    /**
     * Show City
     * @author Oleg D.
     */
    function show($id) 
    {
        $modelName = 'City';
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
      
      
      
    function custom_array_unique($array) 
    {
        foreach ($array as $data) {
            $result[$data] = $data;
        }
        return $result;
    }
    function m_getCitiesByGeo($lat='',$lng='',$radius='',$limit = 10,$amf =0) 
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
        if (!$lat || !$lng || !$radius) {
              return $this->returnMobileResult(array(), $amf); 
        }
        if ($radius > 200) {
            $radius = 200; 
        }
        $results = $this->City->returnGeo($lat, $lng, $radius, 'cities', $limit);
        $state_ids = Set::extract($results, '{n}.provincestate_id');
        $state_ids = $this->custom_array_unique($state_ids);
        
        $country_ids = Set::extract($results, '{n}.country_id');
        $country_ids = $this->custom_array_unique($country_ids);
        //return $results;
        $this->Country->recursive = -1;
        $countriesresult = $this->Country->find('all', array('conditions'=>array('id'=>$country_ids)));
        foreach ($countriesresult as $countryresult) {
            $countries[$countryresult['Country']['id']] = $countryresult['Country']['iso2'];
        }
        
        $this->Provincestate->recursive = -1;
        
        $stateresults = $this->Provincestate->find('all', array('conditions'=>array('id'=>$state_ids)));
        foreach ($stateresults as $stateresult) {
            $states[$stateresult['Provincestate']['id']] = $stateresult['Provincestate']['shortname'];
        }
        $ctr = 0;
        foreach ($results as &$result) {
            $results[$ctr]['state'] = $states[$result['provincestate_id']];
            $results[$ctr]['country'] = $countries[$result['country_id']];
            $ctr++;
        }
        return $this->returnMobileResult($results, $amf);
    }       
    function m_findCities($name=null,$state_id=null, $country_id=null, $limit = 20,$mustHaveUsers = 0,$amf = 0) 
    {
        if (isset($this->request->params['form']['name'])) {
            $name = $this->request->params['form']['name']; 
        }
        if (isset($this->request->params['form']['state_id'])) {
            $state_id = $this->request->params['form']['state_id']; 
        }
        if (isset($this->request->params['form']['country_id'])) {
            $country_id = $this->request->params['form']['country_id']; 
        }
        if (isset($this->request->params['form']['limit'])) {
            $limit = $this->request->params['form']['limit']; 
        }
        if (isset($this->request->params['form']['mustHaveUsers'])) {
            $mustHaveUsers = $this->request->params['form']['mustHaveUsers']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }
        if ($limit == 0) {
            $limit = 20; 
        }
        $conditions = array();
        $orderBy = array();
        if (strlen($name) >0) {
            $conditions['City.name LIKE'] = '%'.$name.'%'; 
        }
        if ($state_id > 0) {
            $conditions['City.provincestate_id'] = $state_id; 
        }
        if ($country_id > 0) {
            $conditions['City.country_id'] = $country_id; 
        }
        if ($mustHaveUsers) {
            $conditions['City.userscount >'] = 0;           
            $orderBy['City.userscount'] = 'DESC';
        }
        $cities = $this->City->find(
            'all', array(
            'conditions'=>$conditions,
            'limit'=>$limit,
            'order'=>$orderBy,
            'contain'=>array('Provincestate','Country'))
        );
        foreach ($cities as &$city) {
            $city['City']['country'] = ($city['Country']['name'] ? $city['Country']['name'] : "");
            $city['City']['state'] = ($city['Provincestate']['name'] ? $city['Provincestate']['name'] : "");
            unset($city['Country']);
            unset($city['Provincestate']);
        }
        return $this->returnMobileResult($cities, $amf);
    }   
    function m_getCityByID($id,$amf = 0) 
    {
        if (isset($this->request->params['form']['id'])) {
            $id = $this->request->params['form']['id']; 
        }
        if (isset($this->request->params['form']['amf'])) {
            $amf = $this->request->params['form']['amf']; 
        }
        if (!$id) {
            return $this->returnMobileResult(array(), $amf); 
        }
        $this->City->recursive = -1;
        $result = $this->City->find('first', array('conditions'=>array('id'=>$id)));
        return $this->returnMobileResult($result, $amf);
    }
      
    /**
     * Cities autocompleter
     * @author Oleg D.
     */
    function autocomplete() 
    {
        Configure::write('debug', 0);
        
        if (isset($_REQUEST["q"]) && $_REQUEST["q"]) {
            $q = strtolower($_REQUEST["q"]);
            
            $countryID = intval(@$_REQUEST["country_id"]); 
            $stateID = intval(@$_REQUEST["state_id"]); 
            
            $conditions['name LIKE '] = trim($q) . '%';
            if ($countryID) { 
                $conditions['country_id'] = $countryID;         
            }
            if ($stateID) {
                $conditions['provincestate_id'] = $stateID;
            }   
                       
            $results = $this->City->find('all', array('conditions' => $conditions, 'fields' => array('name')));
            if (!empty($results)) {
                foreach ($results as $result) {
                    echo $result['City']['name'] . "|\n";
                }                  
            } 
        }
        exit;    
    }
    
    /**
     * Show city select
     * @author Oleg D.
     */
    function getcities()
    {
            $this->layout = false;
        //switch off debug information
              Configure::write('debug', '0');

        if(isset($_REQUEST['countryID']) && !empty($_REQUEST['countryID'])) {
            $countryID = $_REQUEST['countryID'];
            $conditions['country_id'] = $countryID;
        } else {
            $countryID=0;
        }
        if(isset($_REQUEST['stateID']) && !empty($_REQUEST['stateID'])) {
            $stateID = $_REQUEST['stateID'];
            $conditions['provincestate_id'] = $stateID;
        } else {
            $stateID = 0;
        }
                       
            $params = array('conditions' => $conditions, 'fields' => array('id', 'name'), 'recursive' => -1, 'order' => 'name ASC');

            $cities = $this->City->find('list', $params);
        if(!empty($cities)) {
            $cities = array('0'=>"Select one") + $cities; 
        }
        else {
            $cities = array('0'=>"Select one"); 
        }

            /*Showing*/
            $response = "";
        foreach ($cities as $key => $val){
                     $response.='<option value="' . $key . '">'.$val.'</option>';
        }
           exit($this->Json->encode($response));
    }                                 
}
?>
