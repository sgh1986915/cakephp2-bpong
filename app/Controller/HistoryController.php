<?php
class HistoryController extends AppController
{

    var $name = 'History';
    var $helpers = array('Html', 'Form');

    /**
   * Show all history
   */
    function index() 
    {
        $this->Access->checkAccess('History', 'l');

        $conditions = array();

        /* filter Getting data from the session or from the form*/
        if(!empty($this->request->data['HistoryFilter'])) {
            $this->Session->write('HistoryFilter', $this->request->data['HistoryFilter']);
        }elseif($this->Session->check('HistoryFilter')) {
            $this->request->data['HistoryFilter']=$this->Session->read('HistoryFilter');
        }

        //Prepare data for the filter

        if (!empty( $this->request->data['HistoryFilter']['status'])) {
            $conditions['Team.type'] = $this->request->data['HistoryFilter']['type'];
        }

        $history = $this->paginate('History', $conditions);
        $types = $this->History->find('list', array('fields'=>array('History.type','History.type')));
        $types = array(''=>'select one') + $types;
        $this->set('history', $history);
        $this->set('types', $types);
    }


}
?>
