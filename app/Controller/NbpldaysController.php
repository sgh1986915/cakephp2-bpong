<?php

class NbpldaysController extends AppController
{

    var $name    = 'Nbpldays';
    var $helpers = array('Html', 'Form');
    var $uses = array('Nbplday');
    
    /**
     * Show new day block
     * @author Oleg D.
     */
    function showNew($number) 
    {
        $this->set(compact('number'));
    }
    function delete($id) 
    {
        $this->Access->checkAccess('ApproveVenue', 'c');
        $this->Nbplday->delete($id);
        $this->goBack();
    }


}
