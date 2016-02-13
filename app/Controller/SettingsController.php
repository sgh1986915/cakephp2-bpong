<?php
class SettingsController extends AppController
{

    var $name = 'Settings';
    var $helpers = array('Html', 'Form');

    function index() 
    {
        $this->Access->checkAccess('Settings', 'r');
        $this->Setting->recursive = 0;
        $this->set('settings', $this->paginate());
    }

    function add() 
    {
        $this->Access->checkAccess('Settings', 'c');
        if (!empty($this->request->data)) {
            $this->Setting->create();
            if ($this->Setting->save($this->request->data)) {
                $this->Session->setFlash('The Setting has been saved', 'flash_success');
                return $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash('The Setting could not be saved. Please, try again.', 'flash_error');
            }
        }
    }

    function edit($id = null) 
    {
        $this->Access->checkAccess('Settings', 'u');
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash('Invalid Setting', 'flash_error');
            return $this->redirect(array('action'=>'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->Setting->save($this->request->data)) {
                $this->Session->setFlash('The Setting has been saved', 'flash_success');
                return $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash('The Setting could not be saved. Please, try again.', 'flash_error');
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Setting->read(null, $id);
        }
    }

    function delete($id = null) 
    {
        $this->Access->checkAccess('Settings', 'd');
        if (!$id) {
            $this->Session->setFlash('Invalid id for Setting', 'flash_error');
            return $this->redirect(array('action'=>'index'));
        }
        if ($this->Setting->del($id)) {
            $this->Session->setFlash('Setting deleted', 'flash_success');
            return $this->redirect(array('action'=>'index'));
        }
    }

}
?>
