<?php
class VenuetypesController extends AppController
{

    var $name = 'Venuetypes';
    var $helpers = array('Html', 'Form');

    function index() 
    {
        $this->Venuetype->recursive = 0;
        $this->set('venuetypes', $this->paginate());
    }

    function view($id = null) 
    {
        if (!$id) {
            $this->flash(__('Invalid Venuetype'), array('action'=>'index'));
        }
        $this->set('venuetype', $this->Venuetype->read(null, $id));
    }

    function add() 
    {
        if (!empty($this->request->data)) {
            $this->Venuetype->create();
            if ($this->Venuetype->save($this->request->data)) {
                $this->flash(__('Venuetype saved.'), array('action'=>'index'));
            } else {
            }
        }
    }

    function edit($id = null) 
    {
        if (!$id && empty($this->request->data)) {
            $this->flash(__('Invalid Venuetype'), array('action'=>'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->Venuetype->save($this->request->data)) {
                $this->flash(__('The Venuetype has been saved.'), array('action'=>'index'));
            } else {
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Venuetype->read(null, $id);
        }
    }

    function delete($id = null) 
    {
        if (!$id) {
            $this->flash(__('Invalid Venuetype'), array('action'=>'index'));
        }
        if ($this->Venuetype->del($id)) {
            $this->flash(__('Venuetype deleted'), array('action'=>'index'));
        }
    }

}
?>
