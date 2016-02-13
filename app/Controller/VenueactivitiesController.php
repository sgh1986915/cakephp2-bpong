<?php
class VenueactivitiesController extends AppController
{

    var $name = 'Venueactivities';
    var $helpers = array('Html', 'Form');

    function index() 
    {
        $this->Venueactivity->recursive = 0;
        $this->set('venueactivities', $this->paginate());
    }

    function view($id = null) 
    {
        if (!$id) {
            $this->flash(__('Invalid Venueactivity'), array('action'=>'index'));
        }
        $this->set('venueactivity', $this->Venueactivity->read(null, $id));
    }

    function add() 
    {
        if (!empty($this->request->data)) {
            $this->Venueactivity->create();
            if ($this->Venueactivity->save($this->request->data)) {
                $this->flash(__('Venueactivity saved.'), array('action'=>'index'));
            } else {
            }
        }
        $venues = $this->Venueactivity->Venue->find('list');
        $this->set(compact('venues'));
    }

    function edit($id = null) 
    {
        if (!$id && empty($this->request->data)) {
            $this->flash(__('Invalid Venueactivity'), array('action'=>'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->Venueactivity->save($this->request->data)) {
                $this->flash(__('The Venueactivity has been saved.'), array('action'=>'index'));
            } else {
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Venueactivity->read(null, $id);
        }
        $venues = $this->Venueactivity->Venue->find('list');
        $this->set(compact('venues'));
    }

    function delete($id = null) 
    {
        if (!$id) {
            $this->flash(__('Invalid Venueactivity'), array('action'=>'index'));
        }
        if ($this->Venueactivity->del($id)) {
            $this->flash(__('Venueactivity deleted'), array('action'=>'index'));
        }
    }

}
?>
