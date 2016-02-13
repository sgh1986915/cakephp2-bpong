<?php

class Statistic extends AppModel
{

    var $name     = 'Statistic';
    var $useTable = false;

    function prepareData($statistics = array()) 
    {
      
        foreach($statistics as $index => $value) {
            $result[$index]["User_login"]   = $value['u']['lgn'];
            $result[$index]["First_name"]  = $value['u']['firstname'];
            $result[$index]["Last_Name"]   = $value['u']['lastname'];
            $result[$index]["Sex"]   = $value['u']['gender'];
            $result[$index]["DOB"]        = $value['u']['DOB'];
            $result[$index]["Email"]      = $value['u']['email'];
            $result[$index]["Phone"]      = $value['phones']['phones'];
            $result[$index]["Address1"]   = $value['a']['address'];
            $result[$index]["Address2"]   = $value['a']['address2'];
            $result[$index]["Zip_Code"]    = $value['a']['postalcode'];
            $result[$index]["City"]       = $value['a']['city'];
            $result[$index]["State"]      = $value['p']['state'];
            $result[$index]["Country"]    = $value['c']['country'];
            if (isset($value['team'])) {
                $result[$index]["Team_Id"]     = $value['team']['id'];
                $result[$index]["Team_Name"]   = $value['team']['name'];
            }
            $result[$index]["signup_id"]    = $value['s']['id'];
            $result[$index]["payment_status"]    = $value['s']['status'];        
            if ($value['s']['for_team']) {
                $result[$index]["payment_type"] = 'for team';
            } else {
                $result[$index]["payment_type"] = 'individual';        
            }
            if (isset($value['room_status'])) {
                $result[$index]["Room_Status"]     = $value['room_status'];
            }          
            if (isset($value['team_status'])) {
                $result[$index]["Team_Status"]     = $value['team_status'];
            }             
        
        }
      
      
        return $result;
  
    }
}
