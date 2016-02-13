<?php

class ProvincestatesController extends AppController
{
    var $name = 'Provincestates';


    /**
     * Show state select
     * @author vovich
     * @param $_REQUEST['countryID']
     */
    function getstates()
    {
            $this->layout = false;
        //switch off debug information
              Configure::write('debug', '0');
        if(isset($_REQUEST['countryID']) && !empty($_REQUEST['countryID'])) {
            $countryID=$_REQUEST['countryID']; 
        }
        else {
            $countryID=0; 
        }

            $conditions = array('conditions' => array('country_id' => $countryID),
                                  'fields' => array('id', 'name'),
                                  'recursive' => -1,
                                  'order' => 'name ASC'
            );

            $states = $this->Provincestate->find('list', $conditions);
        if(!empty($states)) {
            $states=array('0'=>"Select one")+$states; 
        }
        else {
            $states=array('0'=>"Select one"); 
        }

            /*Showing*/
            $response = "";
        foreach ($states as $key => $val){
            $response.='<option value="' . $key . '">'.$val.'</option>';
        }
            exit($this->Json->encode($response));
    }

}
?>
