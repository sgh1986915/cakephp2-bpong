<?php
class Payment extends AppModel
{
 
    var $name      = 'Payment';

        var $belongsTo = array(
    'Signup' => array('className' => 'Signup',
                                  'foreignKey' => 'model_id',
                                  'dependent' => false,
                                  'conditions' => array('Payment.model'=>'Signup'),
                                  'fields' => '',
                                  'order' => ''
    ),
    'User' => array(
            'className'    => 'User',
            'foreignKey'    => 'user_id'
            )

        );
        
        var $hasMany = array(
        'PaymentsPromocode' => array('className' => 'PaymentsPromocode',
        'foreignKey' => 'payment_id',
        'fields' => '',
        'order' => ''
        )
        );

        var $hasAndBelongsToMany = array(
        'Promocode' => array(
        'className' => 'Promocode',
        'fields' => '',
        'order' => ''
        )
        );

        /**
* Save payments_promocodes
* @param $promocode
* @param $paymentId
* @author vovich
*/
        function savePaymentPromocodes($promocodeId = null, $paymentId = null)
        {
            $promoPayment = array();
            if (!empty($paymentId) && !empty($promocodeId)) {
                $promoPayment['payment_id']   = $paymentId;
                $promoPayment['promocode_id'] = $promocodeId;
                $this->PaymentsPromocode->create();
                $this->PaymentsPromocode->save($promoPayment);
            }
        }
    
    
    
}
?>