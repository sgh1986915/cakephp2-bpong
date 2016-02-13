<?php 
    if(!empty($users)) {  
    foreach($users as $user) {  
        $key = ife(empty($isEmail),$user['User']['lgn'],$user['User']['email']);
        $value = ife(empty($isEmail),$user['User']['lgn'],$user['User']['email']."  [ ".$user['User']['lgn']." ]");
		echo "$key|$value\n";        
     }  
    }  
    else {  
    echo 'No results';  
   }  
   ?>