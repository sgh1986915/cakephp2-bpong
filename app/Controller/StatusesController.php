<?php 
class StatusesController extends AppController
{

    var $name = 'Statuses';
    var $helpers = array('Html', 'Form');

    function index() 
    {
        $this->Access->checkAccess('UserGroup', 'u');
        $this->Status->recursive = 0;
        $this->set('statuses', $this->paginate());
    }

    /**
     * Add new status
     * @author vovich
     */
    function add() 
    {
        $this->Access->checkAccess('UserGroup', 'c');
        if (!empty($this->request->data)) {
            $this->Status->create();
            if ($this->Status->save($this->request->data)) {
                $this->Session->setFlash('The Status has been saved', 'flash_success');
                return $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash('The Status could not be saved. Please, try again.', 'flash_error');
            }
        }
        $groups = $this->Status->Group->find('list');
        $this->set(compact('groups'));
    }
    /**
     * edit status
     * @author vovich
     * @param int $id
     */
    function edit($id = null) 
    {
        $this->Access->checkAccess('UserGroup', 'u');
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash('Invalid Status', 'flash_error');
            return $this->redirect(array('action'=>'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->Status->save($this->request->data)) {
                $this->Session->setFlash('The Status has been saved', 'flash_success');
                return $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash('The Status could not be saved. Please, try again.', 'flash_error');
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Status->read(null, $id);
        }
        $groups = $this->Status->Group->find('list');
        $this->set(compact('groups'));
    }

    function delete($id = null) 
    {
        $this->Access->checkAccess('UserGroup', 'd');
        if (!$id) {
            $this->Session->setFlash('Invalid id for Status', 'flash_error');
            return $this->redirect(array('action'=>'index'));
        }
        if ($this->Status->del($id)) {
            $this->Session->setFlash('Status deleted', 'flash_error');
            return $this->redirect(array('action'=>'index'));
        }
                 
    }
    function t1() 
    {
        $this->layout = false;
        $this->Session->setFlash('Incorrect login or password', 'flash_error');
        return $this->redirect('/statuses/add');
        exit; 
    }
  
}
?>
