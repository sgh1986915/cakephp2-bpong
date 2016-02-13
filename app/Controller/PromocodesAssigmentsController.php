<?php
class PromocodesAssigmentsController extends AppController
{

    var $name = 'PromocodesAssigments';
    var $helpers = array('Html', 'Form');
    var $uses    = array('PromocodesAssigment');
    var $components = array('Time');
    var $models = array(
          'All' => 'All', 
          'Tournament' => 'Tournament', 
          'Event' => 'Event', 
          'StoreCategory' => 'Store Category',
            'StoreSlot' => 'Store Slot',
            'StoreProduct' => 'Store Product',  
    );
    var $model_ids = array('-1'=>'All','0'=>'Custom');


    /**
     * Assign promocode
     * @author vovich
     */
    function add($promocodeID = null)
    {
        Configure::write('debug', 0);
        $this->layout = false;

        if (!$this->RequestHandler->isAjax() || !$this->Access->getAccess('PromocodesAssigment', 'c') || !$promocodeID ) {
            $this->Session->setFlash('This action is not permitted for you.', 'flash_error');
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
        if (!empty( $this->request->data)) {
            $this->request->data['PromocodeAssigment']['promocode_id'] = $promocodeID;
            $result = $this->PromocodesAssigment->newAssigment($this->request->data['PromocodeAssigment']);
            exit($result);
        }


        $this->request->data['PromocodeAssigment']['model_id'] =-1;
        $this->set('model_ids', $this->model_ids);
        $this->set('promocodeID', $promocodeID);
        $this->set('models', $this->models);
        $this->set('exact_model_ids', array());

    }

    /**
     * show all assigments by current promocode
     * @param $promocodeID paramname
     * @author vovich
     */
    function view($promocodeID=null)
    {
        Configure::write('debug', 0);
        $this->layout = false;

        $this->Access->checkAccess('PromocodesAssigment', 'c');

        if (!$this->RequestHandler->isAjax() || !$this->Access->getAccess('PromocodesAssigment', 'c') || !$promocodeID ) {
             $this->Session->setFlash('This action is not permitted for you.', 'flash_error');
             $this->redirect($_SERVER['HTTP_REFERER']);
        }

        $assigments = $this->PromocodesAssigment->find('all', array('conditions'=>array('promocode_id'=>$promocodeID)));

        $this->set('promocodeID', $promocodeID);
        $this->set('assigments', $assigments);
        $this->set('models', $this->models);

    }

    /**
     * AJAX delete assigment
     * @author vovivh
     */
    function delete($assigmentID=null) 
    {
        Configure::write('debug', 0);
        $this->layout = false;

        if (!$this->RequestHandler->isAjax() || !$this->Access->getAccess('PromocodesAssigment', 'd')) {
             exit('This action is not permitted for you.');
        }

        $this->PromocodesAssigment->del($assigmentID);
        exit();

    }

    /**
     * Autocomplete for models
     * @author vovich
     */

    function modelsAutocomplete()
    {
        Configure::write('debug', '0');
        $this->layout = false;

        if (isset($_REQUEST['model']) && !empty($_REQUEST['model'])) {
            $model = $_REQUEST['model'];
        } else {
            $model = '';
        }
        if ($_REQUEST['model'] == 'StoreSlot' || $_REQUEST['model'] == 'StoreCategory' || $_REQUEST['model'] == 'StoreProduct') {
            $conditions = array('fields' => array('id', 'name'),
              'conditions' => array('is_deleted <> 1'),
            'recursive' => -1
            );
        } elseif ($_REQUEST['model'] == 'Event') {
            $conditions = array('conditions' => array('start_date >' => date('Y-m-d H:i:s'), 'is_deleted' => 0, 'signup_required' => 1),
            'fields' => array('id', 'name'),
            'recursive' => -1
            );           
        }
           $models = $this->PromocodesAssigment->$model->find('list', $conditions);
        if(!empty($models)) {
            $models=array('0'=>"Select one")+$models; 
        }
        else {
            $models=array('0'=>"Select one"); 
        }

            /*Showing*/
            $response = "";
        foreach ($models as $key => $val){
            $response.='<option value="' . $key . '">'.$val.'</option>';
        }
            exit($this->Json->encode($response));


    }


}
?>
