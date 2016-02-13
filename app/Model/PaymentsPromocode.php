<?php
class PaymentsPromocode extends AppModel
{
    var $name      = 'PaymentsPromocode';

        var $belongsTo = array(
    'Promocode' => array('className' => 'Promocode',
                                  'foreignKey' => 'promocode_id',
                                  'dependent' => false,
                                  'fields' => '',
                                  'order' => ''
    ),
    'Payment' => array('className' => 'Payment',
                                  'foreignKey' => 'payment_id',
                                  'dependent' => false,
                                  'fields' => '',
                                  'order' => ''
    )
        );
}
?>