<?php
class PromocodesController extends AppController
{

    var $name = 'Promocodes';
    var $helpers = array('Html', 'Form');
    var $uses    = array('Promocode');
    var $components = array('Time', 'Csv');
    var $models = array(
    'All' => 'All',
    'Event' => 'Event',
    'StoreCategory' => 'Store Category',
        'StoreSlot' => 'Store Slot',
        'StoreProduct' => 'Store Product',
    );
    var $model_ids = array('-1'=>'All','0'=>'Custom');

    /**
  *view all promocodes by the logged user
  * @author vovich
  */
    function index() 
    {

        $this->Access->checkAccess('PromocodesViewAll', 'r');
        $conditions = array();

        /* filter Getting data from the session or from the form*/
        if(!empty($this->request->data['PromocodeFilter'])) {
            $this->Session->write('PromocodeFilter', $this->request->data['PromocodeFilter']);
        }elseif($this->Session->check('PromocodeFilter')) {
            $this->request->data['PromocodeFilter']=$this->Session->read('PromocodeFilter');
        }

        //Prepare data for the filter
        if (!empty( $this->request->data['PromocodeFilter']['type'])) {
            $conditions['Promocode.type'] = $this->request->data['PromocodeFilter']['type']; 
        }
        if (!empty( $this->request->data['PromocodeFilter']['code'])) {
            $conditions['Promocode.code'] = $this->request->data['PromocodeFilter']['code']; 
        }
        if (!empty( $this->request->data['PromocodeFilter']['value'])) {
            $conditions['Promocode.value'] = $this->request->data['PromocodeFilter']['value']; 
        }

        if (!empty( $this->request->data['PromocodeFilter']['number_of_uses'])) {
            $conditions['Promocode.number_of_uses'] = $this->request->data['PromocodeFilter']['number_of_uses']; 
        }
        if (!empty( $this->request->data['PromocodeFilter']['uses_count'])) {
            $conditions['Promocode.uses_count'] = $this->request->data['PromocodeFilter']['uses_count']; 
        }
        if (isset( $this->request->data['PromocodeFilter']['status']) && $this->request->data['PromocodeFilter']['status']!=-1) {
            $conditions['Promocode.is_deleted'] = $this->request->data['PromocodeFilter']['status']; 
        }
        if (!empty( $this->request->data['PromocodeFilter']['assigned_user']) && $this->request->data['PromocodeFilter']['assigned_user']) {
            $conditions['Promocode.assign_user_id > '] = '0' ; 
        }

        $this->paginate = array(
               'limit' => 10,
               'order' => array('Promocode.id' => 'DESC' ),
                     'recursive'=>1,
                'conditions' => $conditions

        );

        $promocodes = $this->paginate('Promocode');
        //pr($promocodes);
        //exit;

        $this->set('promocodes', $promocodes);
        $this->set('types', array(''=>' All ','Percent'=>'Percent','Amount'=>'Amount','Free'=>'Free'));
        $this->set('statuses', array('-1'=>' All ','0'=>'Active','1'=>'Deleted'));
    }

    /**
   * Add new promocode
   * @author vovich
   */
    function add()
    {
        $this->Access->checkAccess('Promocodes', 'c');

        if (!empty($this->request->data)) {
            $this->Promocode->create();

            if(!empty($this->request->data['Promocode']['expiration_date'])) {
                $this->request->data['Promocode']['expiration_date'] = $this->Time->calendarToSql($this->request->data['Promocode']['expiration_date']);
            } else {
                $this->request->data['Promocode']['expiration_date']=null;
            }

            if ($this->Promocode->save($this->request->data)) {
                //assign promocode
                $this->request->data['PromocodeAssigment']['promocode_id'] = $this->Promocode->getLastInsertID();
                $this->Promocode->PromocodesAssigment->newAssigment($this->request->data['PromocodeAssigment']);
                //EOF ASSIGMENT
                $this->Session->setFlash('The Promocode has been saved', 'flash_success');
                $this->redirect(array('action'=>'index'));
            } else {
                if(isset($this->request->data['Promocode']['expiration_date']) && !empty($this->request->data['Promocode']['expiration_date'])) {
                    $this->request->data['Promocode']['expiration_date'] = $this->Time->sqlToCalendar($this->request->data['Promocode']['expiration_date']); 
                }
                $this->Session->setFlash('The Promocode could not be saved. Please, try again.', 'flash_error');
                $this->logErr('error occured The Tournament could not be saved.');
            }
        }

        $this->set('types', array(''=>' Select one ','Percent'=>'Percent','Amount'=>'Amount','Free'=>'Free', 'Shipping-Handling'=>'Shipping-Handling'));
        $this->set('models', $this->models);
        $this->request->data['PromocodeAssigment']['model_id'] =-1;
        $this->set('model_ids', $this->model_ids);
        $this->set('exact_model_ids', array());
    }

    /**
   * Edit promocode
   * @author vovich
   */
    function edit($id=null)
    {
        $this->Access->checkAccess('Promocodes', 'c');

        if (!empty($this->request->data)) {

            if(!empty($this->request->data['Promocode']['expiration_date'])) {
                $this->request->data['Promocode']['expiration_date'] = $this->Time->calendarToSql($this->request->data['Promocode']['expiration_date']);
            } else {
                $this->request->data['Promocode']['expiration_date']=null;
            }

            if ($this->request->data['Promocode']['code'] == $this->request->data['Promocode']['old_code']) {
                unset($this->Promocode->validate['code']['isunique']);
            }

            if ($this->Promocode->save($this->request->data)) {
                $this->Session->setFlash('The Promocode has been saved', 'flash_success');
                $this->redirect(array('action'=>'index'));
            } else {
                if(!empty($this->request->data['Promocode']['expiration_date'])) {
                    $this->request->data['Promocode']['expiration_date'] = $this->Time->sqlToCalendar($this->request->data['Promocode']['expiration_date']); 
                }
                $this->Session->setFlash('The Tournament could not be saved. Please, try again.', 'flash_error');
                $this->logErr('error occured The Tournament could not be saved.');
            }
        } else {
            $this->request->data = $this->Promocode->find('first', array('conditions'=>array('Promocode.id'=>$id)));
            if(isset($this->request->data['Promocode']['expiration_date']) && !empty($this->request->data['Promocode']['expiration_date'])) {
                $this->request->data['Promocode']['expiration_date'] = $this->Time->sqlToCalendar($this->request->data['Promocode']['expiration_date']); 
            }
            $this->request->data['Promocode']['old_code'] = $this->request->data['Promocode']['code'];
        }

        $this->set('types', array(''=>' Select one ','Percent'=>'Percent','Amount'=>'Amount','Free'=>'Free', 'Shipping-Handling'=>'Shipping-Handling'));
        $this->set('models', $this->models);
        $this->set('model_ids', $this->model_ids);
        $this->set('exact_model_ids', array());
    }

    /**
   * Delete promocode
   * @author vovich
   * @param int $id
   */
    function delete($id = null) 
    {
        $this->Access->checkAccess('Promocodes', 'd');
        if (!$id) {
            $this->Session->setFlash('Invalid id for Promocode', 'flash_error');
            $this->logErr('error occured: Invalid id for promocode.');
            $this->redirect(array('action'=>'index'));
        }

        $this->request->data['Promocode']['id']         = $id;
        $this->request->data['Promocode']['is_deleted'] = 1;
        $this->request->data['Promocode']['deleted']    = date('Y-m-d H:i:s');

        if ($this->Promocode->save($this->request->data, false)) {
            $this->Session->setFlash('Promocode has been deleted', 'flash_success');
            $this->redirect(array('action'=>'index'));
        }
    }



    /**
   * generate unic code
   * @author vovich
   */
    function generateCode() 
    {
        Configure::write('debug', '0');

        if ($this->RequestHandler->isAjax()) {

            $code = $this->ActivationCode(20);
            while ($this->Promocode->find('count', array('conditions'=>array('code'=>$code)))>0) {
                 $code = $this->ActivationCode(20);
            }

            exit($this->Json->encode($code));
        } else {
            exit();
        }
    }

    /**
   * Autocomplete for models
   * @author vovich
   */

    function modelsAutocomplete()
    {
        Configure::write('debug', '0');
        $this->layout = false;

        if(isset($_REQUEST['model']) && !empty($_REQUEST['model'])) {
            $model = $_REQUEST['model']; 
        }
        else {
            $model = ''; 
        }

         $conditions = array('conditions' => array('start_date >' => date('Y-m-d H:i:s')),
                'fields' => array('id', 'name'),
                'recursive' => -1
                );

          $models = $this->Promocode->PromocodesAssigment->$model->find('list', $conditions);
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

    /**
   * check coupon correction and return coupon information
   * @author vovich
   */
    function checkCoupon() 
    {
        Configure::write('debug', '0');
        $this->layout = false;

        $userID = 0;
        if ($this->isLoggined()) {
            $userSession = $this->Session->read('loggedUser');
        }
        $userID = $userSession['id'];

        if ($this->RequestHandler->isAjax() && !empty($_REQUEST['coupon']) && !empty($_REQUEST['model']) && !empty($_REQUEST['model_id'])) {
            $result   = $this->Promocode->checkCoupon($_REQUEST['coupon'], $_REQUEST['model'], $_REQUEST['model_id'], $userID);
            $response = $this->Promocode->formatDiscount($result);

            exit($this->Json->encode($response));
        } else {
            exit($this->Json->encode("Error while getting coupon information"));
        }
    }

    /**
   *  Export promocodes
   *  @author vovich
   * @param array filterdata
   */
    function export() 
    {
        Configure::write('debug', 0);
        $this->layout = false;
        $this->Access->checkAccess('PromocodesViewAll', 'r');
        $conditions = array();

        /* filter Getting data from the session or from the form*/
        if($this->Session->check('PromocodeFilter')) {
            $this->request->data['PromocodeFilter']=$this->Session->read('PromocodeFilter');
        }

        //Prepare data for the filter
        if (!empty( $this->request->data['PromocodeFilter']['type'])) {
            $conditions['Promocode.type'] = $this->request->data['PromocodeFilter']['type']; 
        }
        if (!empty( $this->request->data['PromocodeFilter']['code'])) {
            $conditions['Promocode.code'] = $this->request->data['PromocodeFilter']['code']; 
        }
        if (!empty( $this->request->data['PromocodeFilter']['value'])) {
            $conditions['Promocode.value'] = $this->request->data['PromocodeFilter']['value']; 
        }

        if (!empty( $this->request->data['PromocodeFilter']['number_of_uses'])) {
            $conditions['Promocode.number_of_uses'] = $this->request->data['PromocodeFilter']['number_of_uses']; 
        }
        if (!empty( $this->request->data['PromocodeFilter']['uses_count'])) {
            $conditions['Promocode.uses_count'] = $this->request->data['PromocodeFilter']['uses_count']; 
        }
        if (isset( $this->request->data['PromocodeFilter']['status']) && $this->request->data['PromocodeFilter']['status']!=-1) {
            $conditions['Promocode.is_deleted'] = $this->request->data['PromocodeFilter']['status']; 
        }

        $this->Promocode->recursive = -1;
        $promocodes = $this->Promocode->find('all', array('conditions'=>$conditions,'contain'=>array()));

        $result = array();
        foreach ($promocodes as $key =>$promocode) {
            $result[$key]['ID']                         = $promocode['Promocode']['id'];
            $result[$key]['type']                     = $promocode['Promocode']['type'];
            $result[$key]['code']                    = $promocode['Promocode']['code'];
            $result[$key]['description']             = $promocode['Promocode']['description'];
            $result[$key]['value']                     = $promocode['Promocode']['value'];
            $result[$key]['number of uses']     = $promocode['Promocode']['number_of_uses'];
            $result[$key]['uses count']             = $promocode['Promocode']['uses_count'];
            $result[$key]['expiration date']     = empty($promocode['Promocode']['expiration_date'])?"":$this->Time->niceDate($promocode['Promocode']['expiration_date']);
            $result[$key]['deleted']                 = empty($promocode['Promocode']['deleted'])?"No":"Yes";
            $result[$key]['assigments']         = "";
            //'PromocodesAssigment','PromocodesAssigment.Tournament','PromocodesAssigment.Event'
            $assigments = array();
            $assigments = $this->Promocode->PromocodesAssigment->find('all', array('conditions'=>array('promocode_id'=>$promocode['Promocode']['id'])));
            if (!empty($assigments)) {
                foreach ($assigments as $assigment) {
                    if ($assigment['PromocodesAssigment']['model']=="All") {
                        $result[$key]['assigments'] .="All Tournaments and Events; ";
                    } elseif ($assigment['PromocodesAssigment']['model_id']==-1) {
                        $result[$key]['assigments'] .="All ".$assigment['PromocodesAssigment']['model']."s; ";
                    } else {
                        $result[$key]['assigments'] .=$assigment['PromocodesAssigment']['model']." - ".$assigment[$assigment['PromocodesAssigment']['model']]['name']."; ";
                    }
                }
            }
        }
        unset($promocodes);

        $this->Csv->addGrid($result);
        $this->Csv->setFilename("Promocodes");
        echo $this->Csv->render1();

        exit();
    }
    /**
     * AJAX Find user by email
     * @author Oleg D.     *
     */
    function findByEmail()
    {
        Configure::write('debug', '1');
        $this->layout = false;

        if ($this->RequestHandler->isAjax() && $this->request->data['User']['email']) {

            $conditions = array('User.email' => $this->request->data['User']['email'],'User.is_deleted'=>0) ;
            $members   = $this->Promocode->User->find('first', array('conditions'=>$conditions));

            if(!empty($members)) {
                $this->set(compact('members'));
            } else {
                exit();
            }
        } else {
            exit();
        }

    }

    /**
   * show promocodes, assigned for current user
   * @author vovich
   */
    function showAssigned() 
    {
        $user_id = $this->getUserID();
        $this->Promocode->recursive = -1;
        $promocodes = $this->Promocode->find('all', array('conditions' => array('assign_user_id' => $user_id)));
        $this->set('promocodes', $promocodes);
    }
}
?>
