<?php
class MailtemplatesController extends AppController
{

    var $name = 'Mailtemplates';
    var $helpers = array('Html', 'Form');

    function index() 
    {
        $this->Access->checkAccess('Mailtemplates', 'r');
        $this->Mailtemplate->recursive = 0;
        $this->paginate = array('order' => array('id' => 'desc'));
        $this->set('mailtemplates', $this->paginate());
    }

    function view($id = null) 
    {
        $this->Access->checkAccess('Mailtemplates', 'r');
        if (!$id) {
            $this->Session->setFlash('Invalid Mailtemplate.', 'flash_error');
            return $this->redirect(array('action'=>'index'));
        }
        $this->set('mailtemplate', $this->Mailtemplate->read(null, $id));
    }

    function add() 
    {
        $this->Access->checkAccess('Mailtemplates', 'c');
        if (!empty($this->request->data)) {
            $this->Mailtemplate->create();
            if ($this->Mailtemplate->save($this->request->data)) {
                $this->Session->setFlash('The Mailtemplate has been saved', 'flash_success');
                return $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash('The Mailtemplate could not be saved. Please, try again.', 'flash_error');
            }
        }
    }

    function edit($id = null) 
    {
        $this->Access->checkAccess('Mailtemplates', 'c');
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash('Invalid Mailtemplate', 'flash_error');
            return $this->redirect(array('action'=>'index'));
        }
        if (!empty($this->request->data)) {
            if ($id) {
                $this->request->data['Mailtemplate']['id'] = $id;
            }
            if ($this->Mailtemplate->save($this->request->data)) {
                $this->Session->setFlash('The Mailtemplate has been saved', 'flash_success');
                return $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash('The Mailtemplate could not be saved. Please, try again.', 'flash_error');
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Mailtemplate->read(null, $id);
        }
    }

    function delete($id = null) 
    {
        $this->Access->checkAccess('Mailtemplates', 'd');
        if (!$id) {
            $this->Session->setFlash('Invalid id for Mailtemplate', 'flash_error');
            return $this->redirect(array('action'=>'index'));
        }
        if ($this->Mailtemplate->del($id)) {
            $this->Session->setFlash('Mailtemplate deleted', 'flash_error');
            return $this->redirect(array('action'=>'index'));
        }
    }

}
?>
