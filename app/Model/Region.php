<?php
class Region extends AppModel
{
    
     var $name = 'Region';
                                                  /*
     function returnGeo($lat, $lng, $radius,$limit) {
     
         $query = "SELECT region.id,region.name, 3956 * 2 * ASIN(SQRT(POWER(SIN((".$lat." - abs(region.lat)) * pi()/180 /2),2)+ COS(".$lat."* pi()/180) * COS(abs(region.lat) * pi()/180) * POWER(SIN((".$lng." - region.lng) * pi()/180 / 2),2) )) as distance FROM regions region having distance < ".$radius." ORDER BY distance limit ".$limit.";";
 
         $results = $this->query($query);
         
         return $results;
         
     }                                              */
}
?>
