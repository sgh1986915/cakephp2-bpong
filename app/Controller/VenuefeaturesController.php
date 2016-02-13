<?php
class VenuefeaturesController extends AppController
{

    var $name = 'Venuefeatures';
    var $helpers = array('Html', 'Form');

    function index() 
    {
        $this->Venuefeature->recursive = 0;
        $this->set('venuefeatures', $this->paginate());
    }

    function view($id = null) 
    {
        if (!$id) {
            $this->flash(__('Invalid Venuefeature'), array('action'=>'index'));
        }
        $this->set('venuefeature', $this->Venuefeature->read(null, $id));
    }

    function add() 
    {
        if (!empty($this->request->data)) {
            $this->Venuefeature->create();
            if ($this->Venuefeature->save($this->request->data)) {
                $this->flash(__('Venuefeature saved.'), array('action'=>'index'));
            } else {
            }
        }
        $venues = $this->Venuefeature->Venue->find('list');
        $this->set(compact('venues'));
    }

    function edit($id = null) 
    {
        if (!$id && empty($this->request->data)) {
            $this->flash(__('Invalid Venuefeature'), array('action'=>'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->Venuefeature->save($this->request->data)) {
                $this->flash(__('The Venuefeature has been saved.'), array('action'=>'index'));
            } else {
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Venuefeature->read(null, $id);
        }
        $venues = $this->Venuefeature->Venue->find('list');
        $this->set(compact('venues'));
    }

    function delete($id = null) 
    {
        if (!$id) {
            $this->flash(__('Invalid Venuefeature'), array('action'=>'index'));
        }
        if ($this->Venuefeature->del($id)) {
            $this->flash(__('Venuefeature deleted'), array('action'=>'index'));
        }
    }

}
?>
