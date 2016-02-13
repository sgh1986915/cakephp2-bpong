<?php
class Softwarereg extends AppModel
{
    var $name = 'Softwarereg';
    var $recursive = -1;
    var $belongsTo = array(
      'User' => array('className' => 'User',
          'foreignKey' => 'user_id',
          'conditions' => '',
          'fields' => '',
          'order' => ''
      )         
    );
    function convertMacAddress($macAddress) 
    {
        // This takes a mac address, strips the dashes, and inverts the number
        $data = preg_replace('/[^A-Za-z0-9\s\s+]/', '', $macAddress);
        // This reverses the order of the string, hashes it, and keeps the first 12 letters
        return substr(md5($this->invertString($data)), 0, 12);
    }
    function convertMacAddressWSOBP($macAddress) 
    {
         // This takes a mac address, strips the dashes, and inverts the number
        $data = preg_replace('/[^A-Za-z0-9\s\s+]/', '', $macAddress);
        // This reverses the order of the string, 'inverts' each character, hashes it, and keeps the first 12 letters
        return substr(md5($this->reverseString($data)), 0, 12);

    }      
    function reverseString($data) 
    {
        if (strlen($data) < 2) {
            return $data; 
        }
        return $this->reverseString(substr($data, 1)).substr($data, 0, 1);   
    }
    function invertString($data) 
    {
        if (strlen($data) < 2) { 
            return $this->getInverseOfCharacter(substr($data, 0, 1)); 
        }
        return $this->getInverseOfCharacter(substr($data, 0, 1)).$this->invertString(substr($data, 1));
    }
    function getInverseOfCharacter($char) 
    {
        switch ($char) {
        case '0': 
            return 'F'; break;
        case '1': 
            return 'E'; break;
        case '2': 
            return 'D'; break;
        case '3': 
            return 'C'; break;
        case '4': 
            return 'B'; break;
        case '5': 
            return 'A'; break;
        case '6': 
            return '9'; break;
        case '7': 
            return '8'; break;
        case '8': 
            return '7'; break;
        case '9': 
            return '6'; break;
        case 'A': 
            return '5'; break;
        case 'B': 
            return '4'; break;
        case 'C': 
            return '3'; break;
        case 'D': 
            return '2'; break;
        case 'E': 
            return '1'; break;
        case 'F': 
            return '0'; break; 
        }
    }
}
?>
