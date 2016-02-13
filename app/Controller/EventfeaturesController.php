<?php
class EventfeaturesController extends AppController
{

    var $name = 'Eventfeatures';

    /**
     * Show All Eventfeature
     * @author vovich
     */
    function index() 
    {
        $this->Access->checkAccess('Eventfeature', 'r');
        $this->Eventfeature->recursive = 0;
        $this->set('eventfeatures', $this->paginate());
    }

    /**
     * Add new Eventfeature
     * @author vovich
     */
    function add() 
    {
        $this->Access->checkAccess('Eventfeature', 'c');
        if (!empty($this->request->data)) {
            $this->Eventfeature->create();
            if ($this->Eventfeature->save($this->request->data)) {
                $this->Session->setFlash('The Eventfeature has been saved', 'flash_success');
                return $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash('The Eventfeature could not be saved. Please, try again.', 'flash_error');
                $this->logErr('error occured: The Eventfeature could not be saved. Please, try again.');
            }
        }
        $events = $this->Eventfeature->Event->find('list');
        $this->set(compact('events'));
    }
    /**
     * Edit Eventfeature
     * @author vovich
     * @param int $id
     */
    function edit($id = null) 
    {
        $this->Access->checkAccess('Eventfeature', 'u');
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash('Invalid Eventfeature', 'flash_error');
            $this->logErr('error occured: Invalid Eventfeature.');
            return $this->redirect(array('action'=>'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->Eventfeature->save($this->request->data)) {
                $this->Session->setFlash('The Eventfeature has been saved', 'flash_error');
                return $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash('The Eventfeature could not be saved. Please, try again.', 'flash_error');
                $this->logErr('error occured: The Eventfeature could not be saved. Please, try again.');
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Eventfeature->read(null, $id);
        }
        $events = $this->Eventfeature->Event->find('list');
        $this->set(compact('events'));
    }
    /**
    * Delete Eventfeature
    * @author vovich
    * @param int $id
    */
    function delete($id = null) 
    {
        $this->Access->checkAccess('Eventfeature', 'd');
        if (!$id) {
            $this->Session->setFlash('Invalid id for Eventfeature', 'flash_error');
            $this->logErr('error occured: Invalid id for Eventfeature.');
            return $this->redirect(array('action'=>'index'));
        }
        if ($this->Eventfeature->del($id)) {
            $this->Session->setFlash('Eventfeature deleted', 'flash_success');
            return $this->redirect(array('action'=>'index'));
        }
    }

}
?>
