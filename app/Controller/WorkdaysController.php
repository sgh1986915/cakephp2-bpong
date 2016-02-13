<?php
class WorkdaysController extends AppController
{

    var $name = 'Workdays';
    var $helpers = array('Html', 'Form');

    function index() 
    {
        $this->Workday->recursive = 0;
        $this->set('workdays', $this->paginate());
    }

    function view($id = null) 
    {
        if (!$id) {
            $this->flash(__('Invalid Workday'), array('action'=>'index'));
        }
        $this->set('workday', $this->Workday->read(null, $id));
    }

    function add() 
    {
        if (!empty($this->request->data)) {
            $this->Workday->create();
            if ($this->Workday->save($this->request->data)) {
                $this->flash(__('Workday saved.'), array('action'=>'index'));
            } else {
            }
        }
        $venues = $this->Workday->Venue->find('list');
        $this->set(compact('venues'));
    }

    function edit($id = null) 
    {
        if (!$id && empty($this->request->data)) {
            $this->flash(__('Invalid Workday'), array('action'=>'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->Workday->save($this->request->data)) {
                $this->flash(__('The Workday has been saved.'), array('action'=>'index'));
            } else {
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Workday->read(null, $id);
        }
        $venues = $this->Workday->Venue->find('list');
        $this->set(compact('venues'));
    }

    function delete($id = null) 
    {
        if (!$id) {
            $this->flash(__('Invalid Workday'), array('action'=>'index'));
        }
        if ($this->Workday->del($id)) {
            $this->flash(__('Workday deleted'), array('action'=>'index'));
        }
    }

}
?>
