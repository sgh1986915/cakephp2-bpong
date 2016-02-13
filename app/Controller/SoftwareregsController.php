<?php
class SoftwareregsController extends AppController
{
    var $name = 'Softwareregs';
    public function getCurrentDateForExpiration_api() 
    {
        return getdate();          
    }  
    public function togglePremium($id) 
    {
        $this->Access->checkAccess('ShowAllSoftwareRegs', 'u'); 
        if (!($id > 0)) {
            $this->Session->setFlash('Invalid id', 'flash_error');
            $this->redirect(array('action'=>'index'));
        }
        $this->Softwarereg->recursive = -1;
        $reg = $this->Softwarereg->find(
            'first', array('conditions'=>array(
            'Softwarereg.id'=>$id))
        );
        if (!$reg) {
            $this->Session->setFlash('Invalid id', 'flash_error');
            $this->redirect(array('action'=>'index'));
        }

        $reg['Softwarereg']['premium'] = 1 - $reg['Softwarereg']['premium'];
        $this->Softwarereg->save($reg);
        $this->Session->setFlash('Premium Setting Toggled');
        $this->redirect(array('action'=>'index'));
    }    
    public function accept($id) 
    {
        $this->Access->checkAccess('ShowAllSoftwareRegs', 'u'); 
        if (!($id > 0)) {
            $this->Session->setFlash('Invalid id', 'flash_error');
            $this->redirect(array('action'=>'index'));
        }
        $this->Softwarereg->recursive = 0;
        $reg = $this->Softwarereg->find(
            'first', array('conditions'=>array(
            'Softwarereg.id'=>$id))
        );
        if (!$reg) {
            $this->Session->setFlash('Invalid id', 'flash_error');
            $this->redirect(array('action'=>'index'));
        }
        $this->sendMailMessage(
            'SoftwareRegistrationAccepted', array(
            '{KEY}'=>$reg['Softwarereg']['key']
             ), $reg['User']['email']
        );         

                 
        $reg['Softwarereg']['accepted'] = 1;
        $this->Softwarereg->save($reg);
        $this->Session->setFlash('Software Registration Accepted', 'flash_success');
        $this->redirect(array('action'=>'index'));
    } 
    public function ban($id) 
    {
        $this->Access->checkAccess('ShowAllSoftwareRegs', 'u'); 
        if (!($id > 0)) {
            $this->Session->setFlash('Invalid id', 'flash_error');
            $this->redirect(array('action'=>'index'));
        }
        $this->Softwarereg->recursive = -1;
        $reg = $this->Softwarereg->find(
            'first', array('conditions'=>array(
            'Softwarereg.id'=>$id))
        );
        if (!$reg) {
            $this->Session->setFlash('Invalid id', 'flash_error');
            $this->redirect(array('action'=>'index'));
        }       
        $reg['Softwarereg']['banned'] = 1;
        $this->Softwarereg->save($reg);
        $this->Session->setFlash('Hardware Address Banned', 'flash_success');
        $this->redirect(array('action'=>'index'));
    }
    public function hide($id) 
    {
        $this->Access->checkAccess('ShowAllSoftwareRegs', 'u'); 
        if (!($id > 0)) {
            $this->Session->setFlash('Invalid id', 'flash_error');
            $this->redirect(array('action'=>'index'));
        }
        $this->Softwarereg->recursive = -1;
        $reg = $this->Softwarereg->find(
            'first', array('conditions'=>array(
            'Softwarereg.id'=>$id))
        );
        if (!$reg) {
            $this->Session->setFlash('Invalid id', 'flash_error');
            $this->redirect(array('action'=>'index'));
        }       
        $reg['Softwarereg']['hidden'] = 1;
        if (!$this->Softwarereg->save($reg)) {
            $this->Session->SetFlash('Could not save', 'flash_error'); 
        }
        else { 
            $this->Session->setFlash('Registration Hidden', 'flash_success'); 
        }
        return $this->redirect(array('action'=>'index'));
    }
    public function unban($id) 
    {
        $this->Access->checkAccess('ShowAllSoftwareRegs', 'u'); 
        if (!($id > 0)) {
            $this->Session->setFlash('Invalid id', 'flash_error');
            $this->redirect(array('action'=>'index'));
        }
        $this->Softwarereg->recursive = -1;
        $reg = $this->Softwarereg->find(
            'first', array('conditions'=>array(
            'Softwarereg.id'=>$id))
        );
        if (!$reg) {
            $this->Session->setFlash('Invalid id', 'flash_error');
            $this->redirect(array('action'=>'index'));
        }       
        $reg['Softwarereg']['banned'] = 0;
        $this->Softwarereg->save($reg);
        $this->Session->setFlash('Hardware Address Un-Banned', 'flash_success');
        $this->redirect(array('action'=>'index'));
    }
    public function index() 
    {
        $this->Access->checkAccess('ShowAllSoftwareRegs', 'r');
        $conditions = array();
        if (!empty($this->request->data['SoftwareregFilter'])) {
            $this->Session->write('SoftwareregFilter', $this->request->data['SoftwareregFilter']);
        } elseif ($this->Session->check('SoftwareregFilter')) {
            $this->request->data['SoftwareregFilter'] = $this->Session->read('SoftwareregFilter');
        }
        $conditions['Softwarereg.hidden'] = 0;
        if (!empty($this->request->data['SoftwareregFilter']['email'])) {
            $conditions['User.email'] = $this->request->data['SoftwareregFilter']['email']; 
        }
        if (!empty($this->request->data['SoftwareregFilter']['lgn'])) {
            $conditions['User.lgn'] = $this->request->data['SoftwareregFilter']['lgn']; 
        }  
          
        $this->paginate = array(
          'limit' => 20,
          'order' => array('Softwarereg.created' =>'DESC'),
          'conditions'=>$conditions
          );
        $this->Softwarereg->recursive = 0;
        $softwareregs = $this->paginate('Softwarereg');
        $this->set('softwareregs', $softwareregs);
            
    }
    /** 
    * This next function lets caller know wther or not user is banneed allow us to ban people. 
    * 
    * @param  mixed $registrationCode
    * @return mixed
    */
    function isRegKeyBanned_api($regKey) 
    { 
        if (empty($regKey)) {
            return "no"; 
        }
        $this->Softwarereg->recursive = -1;
        $keys = $this->Softwarereg->find('all', array('conditions'=>array('Softwarereg.key'=>$regKey)));
        //          return $keys;
        foreach ($keys as $key) {
            if ($key['Softwarereg']['banned'] == 1) {
                return "yes"; 
            }
        }
        return "no"; 
    }
      
    /** 
      * This looks at a registration key to see if this is a premium registration.
      */
    function isRegKeyPremium_api($regKey) 
    {
        if (empty($regKey)) {
            return "no"; 
        }
        $this->Softwarereg->recursive = -1;
         $keys = $this->Softwarereg->find('all', array('conditions'=>array('Softwarereg.key'=>$regKey)));
        foreach ($keys as $key) {
            if ($key['Softwarereg']['premium'] == 1 && $key['Softwarereg']['accepted'] == 1) {
                return "yes"; 
            }
        }
        return "no";
    }
      
    // this provides a distinct list of emails
    function emails() 
    {
        //         $this->redirect('cnn');
        $this->Access->checkAccess('ShowAllSoftwareRegs', 'r');

        $this->Softwarereg->recursive = 0;
          
        $allregs = $this->Softwarereg->find(
            'all', array(
            'fields'=>array('User.email'),
            'contains'=>array('User'),
            'conditions'=>array('banned'=>0))
        );  
        $allEmails = Set::extract($allregs, '{n}.User.email');
        $emails =  array_unique($allEmails);
        $this->set('emails', $emails);
        
    }
}

?>
