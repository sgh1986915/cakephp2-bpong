<?php

class MetatagsController extends AppController
{

    var $name = 'Metatags';

    /**
     * List metatags
     */

    function index() 
    {
        $this->Filter->setFields(
            'Metatag', array(
            'keywords'     => 'LIKE'
            ,'author'         => 'LIKE'
            ,'description' => 'LIKE'
            ,'title'       => 'LIKE'
            ,'url'         => 'LIKE'
            ,'name'        => 'LIKE'
            ,'language_id' => '='
            )
        );

        $filter = $this->Filter->getFilter();

        $this->Paginator->init(
            'Metatag', array(
                'id'
               ,'url'
               ,'name'
               ,'action'
               ,'code'
               ,'title'
               ,'keywords'
               ,'author'
               ,'description'
             )//array
            , array('filter' => $filter)
        );//init

        $this->set('metatags', $this->paginate('Metatag', $filter));
    }//eof index


    /**
     * Edit metatag
     *
     * @param integer $id
     */

    function edit($id = null) 
    {
        $this->Metatag->id = $id;
        $this->request->data = $this->Metatag->read();
    }//eof edit


    /**
     * Save metatag
     */

    function save() 
    {
        if (!empty($this->request->data['Metatag']) ) {
            $this->Metatag->unbindModel(array('belongsTo' => array('Language')));
            $exists = $this->Metatag->find(
                'count', array('conditions' => array(
                    'language_id' => $this->request->data['Metatag']['language_id']
                   ,'name'        => $this->request->data['Metatag']['name']
                   ,'action'      => $this->request->data['Metatag']['action']
                ))
            );//findCount
            if (!$exists ) {
                unset($this->request->data['Metatag']['id']);
            }//if
            if (!$this->Metatag->save($this->request->data['Metatag']) ) {
                die('data cannot be saved');
                // TODO add a flash message
            }//if
        }//if
        $this->redirect('/metatags/index');
        exit();
    }//eof save


    /**
     * Delete metatag
     *
     * @param integer $id
     */

    function delete($id = null) 
    {
        $this->Metatag->delete($id);
        $this->redirect('/metatags/index');
        exit();
    }//eof delete

}//eof class MetatagsController
?>