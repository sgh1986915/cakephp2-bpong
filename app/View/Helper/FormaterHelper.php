<?php

class FormaterHelper extends Helper {
	var $helpers = array('Html');
	/**
	 * Escape string for URL
	 * @author Oleg D.
	 */
	function escapeURL($string,$nums=0){
	        $string = str_replace(" ","_",$string);
	        $string = str_replace("&amp;","and", $string);
	        $string = str_replace("&","and", $string);
	        $string = eregi_replace('[^a-zA-Z0-9]', '_', $string);
	//        $string = iconv("UTF-8", "ISO-8859-1", $string);
			$string = str_replace("___","_",$string);
		    $string = str_replace("__","_",$string);
			if($nums)
				$string=substr($string,0, $nums);
			$string=strtolower($string);

	        return $string;
	}
	
	/**
	 * Make Cut text
	 * @author Oleg D.
	 */
	function stringCut($string, $nums, $cut = '...', $stripTags = 1){
		if ($stripTags) {
        	$string = strip_tags(str_replace('&amp;', ' ', str_replace('&nbsp;', ' ', $string)));
		}
        if (mb_strlen(strip_tags($string)) > $nums) {
        	$string = mb_substr($string, 0, $nums) . $cut;
        }
	   return $string;
	}
	
	/**
	 * Show user name for submissions
	 * @author Oleg D.
	 */
	function authorsName ($user, $full = 1, $me = 0){
		if ($me) {
			$name = 'My';
		} else {
			if ($full) {
				$name = $this->userName($user, 1) . "'s";				
			} else {
				$name = $this->userName($user, 0) . "'s";						
			}		
		}
		return $name;						
	}
	/**
	 * Show user name
	 * @author Oleg D.
	 */
	function userName ($user, $full = 1){
		$name = $user['firstname']; 
		if ($full) {
			if ($name && $user['lastname']) {
				$name = $name . ' ' . substr($user['lastname'], 0, 1) . '.';	
			}					
		}
		if (!$name) {
			$name = $user['lgn']; 
		}
		return $name;							
	}	
	/**
	 * Get Album Link
	 * @author Oleg D.
	 */			
    function getAlbumLink ($album) {
        return '/Albums/show_' . $album['content_type'] . '/' . $album['id'];
    }
    function showTag($itemTag, $model) {
    	return $this->Html->link($this->stringCut($itemTag['tag'], 12), '/tag/' . $itemTag['id'] . '/' . $model, array('class' => 'tag_name')) . "<span class='underline'>(" . $this->Html->link($itemTag['counter'], '/tag/' . $itemTag['id'] . '/' . $model) . ")</span>";    	
    } 	
    
    function getHomeAddress($addresses) {
    	if (!empty($addresses)) {
    		$adrCount = count($addresses);
    		$i=1;
    		foreach ($addresses as $address) {
    			if ($address['label'] == 'Homeq' || $i == $adrCount) {
    				$result = $address;	
    				break;
    			}
    			$i++;
    		}
    	}
    	return $result;
    		
    }   
}
?>