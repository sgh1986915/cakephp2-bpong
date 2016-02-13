<?php
class Address extends AppModel
{

    var $name = 'Address';

    var $belongsTo = array(
            'Country' => array('className' => 'Country',
                                'foreignKey' => 'country_id',
                                'dependent' => true,
                                'conditions' => '',
                                'fields' => '',
                                'order' => ''
            ),
            'Provincestate' => array('className' => 'Provincestate',
                                'foreignKey' => 'provincestate_id',
                                'dependent' => true,
                                'conditions' => '',
                                'fields' => '',
                                'order' => ''
            )
    );
    var $actsAs= array('Containable');


    /**
     * Make address information in upper case
     */
    function beforeSave($options = array())
    {
        if (isset($this->data['Address']['address'])) {
            $this->data['Address']['address']    = strtoupper($this->data['Address']['address']);
        }
        if (isset($this->data['Address']['address2'])) {
            $this->data['Address']['address2']   = strtoupper($this->data['Address']['address2']);
        }
        if (isset($this->data['Address']['address3'])) {
            $this->data['Address']['address3']   = strtoupper($this->data['Address']['address3']);
        }
        if (isset($this->data['Address']['city'])) {
            $this->data['Address']['city']       = strtoupper($this->data['Address']['city']);
        }
        if (isset($this->data['Address']['city'])) {
            $this->data['Address']['postalcode'] = strtoupper($this->data['Address']['postalcode']);
        }
        return true;
    }


    /**
    * Set arrays for the countries and states to the view
    * @author Oleg D.
    */
    function setCountryStates($formName='Address', $countryID = 0) 
    {

        $contriesID = $this->Provincestate->find('all', array('fields'=> array('DISTINCT Provincestate.country_id'),'recursive' => -1,'contains' => array(),'conditions'=> array()));
        $contriesIDs = Set::extract($contriesID, '{n}.Provincestate.country_id');

        /*Countries*/
        $countries = $this->Country->find('list', array('conditions'=>array('Country.id' => $contriesIDs), 'order' => array('rank' => 'DESC', 'name' => 'ASC')));
        $countries = array('0'=>"Select one") + $countries;

        if (!$countryID) {
            if (empty($this->data[$formName]['country_id'])) {
                $countryID = 0;
            } else {
                $countryID = $this->data[$formName]['country_id'];
            }
        }
        $conditions = array('conditions' => array('country_id' => $countryID),
                            'fields' => array('id', 'name'),
        'order' => array('name' => 'asc'),
                            'recursive' => -1
        );
        $states = $this->Provincestate->find('list', $conditions);
        //print_r($states);
        if(!empty($states)) {
            $states = array('0' => "Select one") + $states;
        }else{
            $states = array('0' => "Select one");
        }

        $countries_states['countries'] = $countries;
        $countries_states['states']    = $states;

        return $countries_states;

    }
    /**
     * Get lat lon by address
     * 
     * modified by skinny....$state can be array('id'=>$id) and $country can be array('id'=>$id)
     * 
     * @author Oleg D.
     */
    function getLatLon($address = null, $city=null, $state=null, $country = null) 
    {
        if (is_array($state)) {
            $stateid = $state['id'];
            $provinceState = $this->Provincestate->find('first', array('id'=>$stateid));
            $state = $provinceState['Provincestate']['name'];
        }
        if (is_array($country)) {
            $countryid = $country['id'];
            $country = $this->Country->find('first', array('id'=>$countryid));
            $country = $country['Country']['name'];
        }
        
        App::import('Vendor', 'GoogleMapAPI', array('file' => 'class.GoogleMapAPI.php'));
        $map = new GoogleMapAPI();
        $map->setAPIKey(GOOGLE_MAP_KEY);
        return $map->getCoordsByAddress($address, $city, $state, $country);
    }
    /**
     * Get addresses of venues within a certain radius of a coordinate
     */
    /*	function getVenueAddressesWithinRadius($lat,$lng,$radius,$limit = 10) {
    $query = "SELECT *, 3956 * 2 * ASIN(SQRT(POWER(SIN(abs(".$lat." - latitude) * pi()/180 /2),2)+ ".
            "(COS(".$lat."* pi()/180) * COS(abs(latitude) * pi()/180) * ".
            "POWER(SIN(abs(".$lng." - longitude) * pi()/180 / 2),2)) )) ".
    " as Distance FROM addresses WHERE model = 'Venue' having Distance < ".$radius.
            " ORDER BY Distance LIMIT ".$limit.";";
        $query2 = "SELECT * FROM addresses WHERE ";
     	$results = $this->query($query);
     	return $results;
    }*/
    function getModelAddressesWithinRadius($modelName,$lat,$lng,$radius,$limit = 10) 
    {
        $r = $radius / 3956;
        $latRadians = $lat * pi() / 180;
        $maxLat = 180 * ($latRadians + $r) / pi();
        $minLat = 180 * ($latRadians - $r) / pi(); 
        
        $lonRadians = $lng * pi() / 180;
        $deltaLon = asin(sin($r)/cos($latRadians));
        //return $deltaLon;
        $maxLon = 180 * (($lonRadians + $deltaLon) / pi());
        $minLon = 180 * (($lonRadians - $deltaLon) / pi());
        
        $query = "SELECT *, 3956 * 2 * ASIN(SQRT(POWER(SIN(abs(".$lat." - latitude) * pi()/180 /2),2)+ ".
            "(COS(".$lat."* pi()/180) * COS(abs(latitude) * pi()/180) * ".
            "POWER(SIN(abs(".$lng." - longitude) * pi()/180 / 2),2)) )) ".
            " as Distance FROM addresses WHERE model = '".$modelName.
            "' AND latitude < ".$maxLat." AND latitude > ".$minLat.
            " AND longitude < ".$maxLon." AND longitude > ".$minLon.
            " having Distance < ".$radius.
            " ORDER BY Distance LIMIT ".$limit.";";
         $results = $this->query($query);
         return $results;
    }

}
?>
