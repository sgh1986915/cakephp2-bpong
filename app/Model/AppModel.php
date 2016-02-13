<?php
App::uses('CakeNumber', 'Utility');
App::uses('Model', 'Model');
class AppModel extends Model
{
    
    
    /**
     * Switch Associated models to Parents model DB source
     *
     * @author Oleg D.
     */
    function switchAssocToParentSource() 
    {
        $associations = $this->getAssociated();
        // loop through all association models and switch to master
        foreach ($associations as $assocModel => $assoc) {
            $this->{$assocModel}->useDbConfig = $this->useDbConfig;

            // loop through all association models 2-nd level and switch to master
            $associations2 = $this->{$assocModel}->getAssociated();
            foreach ($associations2 as $assocModel2 => $assoc2) {
                $this->{$assocModel}->{$assocModel2}->useDbConfig = $this->useDbConfig;
            }
        }
    }

    function implodeCond($conditions = Array(),$join = 'AND') 
    {
        $output = '';
        if (empty($conditions)) {
            return $output; 
        }

        $j = "AND"; /*FIRST ALWAYS should be AND*/
        foreach($conditions as $key => $value) {
            //$output .= ' '.$join.' '.$key.ife(strpos($key, 'LIKE') !== false, '', ' = ').ife(is_int($value), $value, ' "'.Sanitize::escape($value).'"');
            $output .= ' '.$join.' ';
            if (empty(strpos($key, 'LIKE') !== false))
                $output .= $key . ' = ';

            if (!empty(is_int($value)))
                $output .= $value;
            else
                $output .= ' "'.Sanitize::escape($value).'"';

            $j = $join;
        }

        return $output;
    }

    /*
    This take a raw gameObject i.e. $game['Game'] and returns the effective cupdif....this is used in a lot of places...
    */
    function getEffectiveCupDif($gameObject) 
    {
        if ($gameObject['isforfeit']) {
            return 3; 
        }
        if ($gameObject['numots'] > 0) {
            return 1; 
        }
        return $gameObject['cupdif'];
    }
    /*
    http://janmatuschek.de/LatitudeLongitudeBoundingCoordinates
    * Need to adjust for negatives and cross the polls
    */
    function returnGeo($lat, $lng, $radius,$tableName,$limit = 10) 
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
        // return array($lat,$lng,$radius,$tableName,$limit);
        if ($tableName == 'cities') {
            $query = "SELECT *, 3956 * 2 * ASIN(SQRT(POWER(SIN(abs(".$lat." - latitude) * pi()/180 /2),2)+ ".
            "(COS(".$lat."* pi()/180) * COS(abs(latitude) * pi()/180) * ".
            "POWER(SIN(abs(".$lng." - longitude) * pi()/180 / 2),2)) )) ".
            " as Distance FROM ".$tableName.
            " WHERE latitude < ".$maxLat." AND latitude > ".$minLat.
            " AND longitude < ".$maxLon." AND longitude > ".$minLon.
            " having Distance < ".$radius.
            " ORDER BY population DESC, Distance ASC LIMIT ".$limit.";";
        }
        else {
            $query = "SELECT *, 3956 * 2 * ASIN(SQRT(POWER(SIN(abs(".$lat." - latitude) * pi()/180 /2),2)+ ".
            "(COS(".$lat."* pi()/180) * COS(abs(latitude) * pi()/180) * ".
            "POWER(SIN(abs(".$lng." - longitude) * pi()/180 / 2),2)) )) ".
            " as Distance FROM ".$tableName.
            " WHERE latitude < ".$maxLat." AND latitude > ".$minLat.
            " AND longitude < ".$maxLon." AND longitude > ".$minLon.
            "having Distance < ".$radius.
            " ORDER BY Distance ASC LIMIT ".$limit.";";
        }
         //return $query;
         $results = $this->query($query);
         //if (!$results) return array('problem'=>$query);
        foreach ($results as $key => $value) {
            $results[$key] = $value[$tableName];
            $results[$key]['distance'] = $value[0]['Distance'];
        }

         return $results;

    }
     /*
    I have no idea why the native array_unique function doesn't work, but it doesnt.
    */                                                                                
    function custom_array_unique($array) 
    {
        if (!is_array($array)) {
            return false; 
        }
        $result = array();
        foreach ($array as $data) {
            $result[$data] = $data;
        }
        return $result;
    }
    /**
     *    CURL replacement for file_get_contents
     * @author Oleg D.
     */
    function file_get_contents_curl($url) 
    {
        $ch = curl_init();
         
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
        curl_setopt($ch, CURLOPT_URL, $url);
         
        $data = curl_exec($ch);
        curl_close($ch);
         
        return $data;
    } 
}
?>
