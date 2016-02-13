<?php
class PaymentsController extends AppController
{

    var $name = 'Payments';
    var $helpers = array('Html', 'Form');
    var $uses     = array('Payment','Promocode');

    /**
    *view all apyments by the logged user
    * @author vovich
    */
    function index() 
    {
        $this->Access->checkAccess('PaymentsViewAll', 'r');
        $conditions = array();

        /* filter Getting data from the session or from the form*/
        if(!empty($this->request->data['PaymentFilter'])) {
            $this->Session->write('PaymentFilter', $this->request->data['PaymentFilter']);
        }elseif($this->Session->check('PaymentFilter')) {
            $this->request->data['PaymentFilter']=$this->Session->read('PaymentFilter');
        }

        //Prepare data for the filter
        if (!empty( $this->request->data['PaymentFilter']['user_id'])) {
            $conditions['User.id'] = $this->request->data['PaymentFilter']['user_id']; 
        }

        if (!empty( $this->request->data['PaymentFilter']['user_email'])) {
            $conditions['User.email'] = $this->request->data['PaymentFilter']['user_email']; 
        }

        if (!empty( $this->request->data['PaymentFilter']['user_lastname'])) {
            $conditions['User.lastname LIKE'] = $this->request->data['PaymentFilter']['user_lastname']; 
        }

        if (!empty( $this->request->data['PaymentFilter']['user_login'])) {
            $conditions['User.lgn LIKE'] = $this->request->data['PaymentFilter']['user_login']; 
        }

        if (!empty( $this->request->data['PaymentFilter']['model'])) {
            $conditions['Payment.model'] = $this->request->data['PaymentFilter']['model']; 
        }

        if (!empty( $this->request->data['PaymentFilter']['status'])) {
            $conditions['Payment.status'] = $this->request->data['PaymentFilter']['status']; 
        }
        if (!empty( $this->request->data['PaymentFilter']['promocode'])) {
            $promocodeId = $this->Promocode->field('id', array('code' => $this->request->data['PaymentFilter']['promocode']));
            if (!empty($promocodeId)) {
                $ids = $this->Payment->PaymentsPromocode->find('list', array('fields'=>array('payment_id','payment_id'),'conditions'=>array('promocode_id'=>$promocodeId)));
                $conditions['Payment.id'] = $ids;
            } else {
                $conditions['Payment.id'] = 0; 
            }
        }

        $this->paginate = array(
         'limit' => 10,
         'order' => array('Payment.id' => 'DESC' ),
         'recursive'=>1,
         'conditions' => $conditions,
        );

        $payments = $this->paginate('Payment');

        foreach ($payments as &$payment){
            if (!empty($payment['Signup']['model'])) {
                $payment['Signup']['slug'] = $this->Payment->Signup->$payment['Signup']['model']->field('slug', 'id='.$payment['Signup']['model_id']);
                if ($payment['Signup']['model']=="Tournament") {
                    $payment['Signup']['link'] = "/tournaments/view/".$payment['Signup']['slug'];
                }
            }
        }
        
        $this->Payment->recursive =0;
        $total = $this->Payment->find('all', array('fields'=>'SUM(amount) as total','conditions'=>$conditions));
        $this->set('total', $total[0][0]['total']);

        $this->set('payments', $payments);
        $this->set('statuses', array(''=>' All ','Approved'=>'Approved','Declined'=>'Declined','Error'=>'Error'));
        $this->set('models', array(''=>' All ','Signup'=>'Signup','Order'=>'Order'));
    }


    /**
    *view all apyments by the logged user
    * @author vovich
    */
    function myPayments() 
    {
        $this->Access->checkAccess('Payment', 'r');
        if ($this->isLoggined()) {
             $userSession = $this->Session->read('loggedUser');
        }

        $condition = array('Payment.user_id = '.$userSession['id']);


        $this->paginate = array(
                     'limit' => 10,
                     'order' => array('Payment.id' => 'DESC' ),
                     'recursive'=>1,
                     'contain' =>array(),
                     'conditions' => $condition

        );

        $payments = $this->paginate('Payment');

        $this->set('payments', $payments);

    }


    /**
    * view payments by the logged user for the current model
    * @author vovich
    * @param char $modelName - name of the model
    * @param int  $modelID   - id of the model
    */
    function view($modelName = null, $modelID=null) 
    {
        $this->Access->checkAccess('Payment', 'r');
        if ($this->isLoggined()) {
             $userSession = $this->Session->read('loggedUser');
        }

        $modelData =  $this->Payment->$modelName->find('first', array('conditions'=>array($modelName.'.id'=>$modelID)));
        if (empty($modelData)) {
            $this->Session->setFlash('Error while getting parameters.', 'flash_error');
            $this->redirect('/');
        }

        $this->set('modelData', $modelData);

        $condition = array('Payment.user_id = '.$userSession['id'],'Payment.model'=>$modelName,'Payment.model_id'=>$modelID);

        $this->paginate = array(
                     'limit' => 10,
                     'order' => array('Payment.id' => 'DESC' ),
                     'recursive'=>1,
                      'conditions' => $condition

        );

        $payments = $this->paginate('Payment');

        foreach ($payments as &$payment){
            if (!empty($payment['Signup']['model'])) {
                $payment['Signup']['slug'] = $this->Payment->Signup->$payment['Signup']['model']->field('slug', 'id='.$payment['Signup']['model_id']);
                if ($payment['Signup']['model']=="Tournament") {
                    $payment['Signup']['link'] = "/tournaments/view/".$payment['Signup']['slug'];
                }
            }
        }

        $this->set('payments', $payments);

    }
    /**
       * remake payments info - from payments to payments_promocodes
       * @author Oleg D.
       */
    function remakePromoTable() 
    {
        $this->layout = false;
        $this->Payment->recursive = -1;
        $insertText = '';
        $payments = $this->Payment->find('all', array('conditions' => array('promocode_id > 0'), 'fields' => array('promocode_id', 'id')));
        foreach ( $payments as $payment) {
            $paymentID = $payment['Payment']['id']; 
            $promocodeID = $payment['Payment']['promocode_id'];
            $insertText .= "($paymentID, $promocodeID),";

            
        }
        $insertText = substr($insertText, 0, -1);
        
        echo $insert = "INSERT INTO payments_promocodes (payment_id, promocode_id )
		VALUES $insertText ;";
            
        
        exit;
    }

}
?>
