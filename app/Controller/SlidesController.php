<?php
class SlidesController extends AppController
{

    var $name = 'Slides';

    var $uses = array('Slide');

    var $helpers = array(
        'Html',
        'Form',
        'Javascript',
    'Cropimage',
        'Number' // Used to show readable filesizes
    );
    var $components = array('JqImgcrop', 'RequestHandler');


    /**
     * Displays a list of uploaded images
     */
    function index() 
    {
        $this->Access->checkAccess('slides', 'l');
        $this->set('slides', $this->Slide->find('all', array('order' => 'ordering ASC')));
    }

    function edit($id) 
    {
        ini_set('mysql.connect_timeout', '300');
        set_time_limit(300);    
        $this->Access->checkAccess('slides', 'u');
        if ($id) {
            if (!empty($this->request->data)) {
                $this->request->data['Slide']['image_url'] = '-';
                if ($this->Slide->save($this->request->data)) {
                    
                    Cache::delete('slides');
                    $this->Session->setFlash(__('The slide has been saved'), 'flash_success');
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(__('The slide could not be saved. Please, try again.'), 'flash_error');
                }
            }
            $this->request->data = $this->Slide->find('first', array('conditions' => array('Slide.id' => $id), 'contain' => array('Image')));
            
            //echo "<pre/>";
            //print_r($this->request->data);
            //exit;
        } else {
              $this->Session->setFlash(__('The slide ID error'), 'flash_error');
              $this->redirect(array('action' => 'index'));                
        }
    }

    /**
     * changes ordering of items
     *
     * @param int    $id        - id of moved element
     * @param string $direction - up, down
     */
    function move($id, $direction = '') 
    {
        $this->Access->checkAccess('slides', 'u');
        $error = false;
        if(empty($id) || !in_array($direction, array('up', 'down'))) {
            $error = true;
        }
        $currentItem = $this->Slide->read(null, $id);
        $currentOrderId = $currentItem['Slide']['ordering'];
        if ($direction == 'up') {
            $newItem = $this->Slide->find(
                'first', array(
                'conditions' => array(
                'ordering <' => $currentOrderId
                ),
                'order' => 'ordering DESC'
                )
            );   
        }
        if ($direction == 'down') {
            $newItem = $this->Slide->find(
                'first', array(
                'conditions' => array(
                'ordering >' => $currentOrderId
                ),
                'order' => 'ordering ASC'
                )
            );
        }
    
        if (empty($currentItem) || empty($newItem)) {
            $error = true;
        }
        $newOrderId = $newItem['Slide']['ordering'];
        $currentItem['Slide']['ordering'] = $newOrderId;
        $newItem['Slide']['ordering'] = $currentOrderId;
        if(!$this->Slide->save($currentItem) || !$this->Slide->save($newItem)) {
            Cache::delete('slides');
            $error = true;
        } // From skinny: need to delete the Cache either way
        else {
            Cache::delete('slides');
        }
    
        if(!$error) {
            $this->Session->setFlash(__('The ordering has been changed'), 'flash_success');
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(__('The ordering could not be changed. Please, try again.'), 'flash_error');
            $this->redirect(array('action' => 'index'));
        }
    }

}
?>