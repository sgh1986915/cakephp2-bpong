<?php
class Promocode extends AppModel
{
    var $name      = 'Promocode';
    var $actsAs    = array('Containable');

     var $validate = array(
    'type' =>'notEmpty'
        ,'number_of_uses'=>'notEmpty',
         'code' => array(
            'rule' => array('isUnique'),
            'message' => 'Code already taken.'
         )
        );


     var $hasMany = array(
     'PromocodesAssigment' => array( 'className' => 'PromocodesAssigment',
                                            'foreignKey' => 'promocode_id',
                                            'fields' => '',
                                            'order' => ''
     ),
     'PaymentsPromocode' => array( 'className' => 'PaymentsPromocode',
                                            'foreignKey' => 'promocode_id',
                                            'fields' => '',
                                            'order' => ''
                                                        )

     );
     var $belongsTo = array(
     'User' => array(
                                    'className'    => 'User',
                                    'foreignKey'    => 'assign_user_id'
            )
        );

     /**
     *  Check coupon correctionand return results
     * @author vovich
     * @param char $coupon
     * @param char $model   (can be Event or Tournament)
     * @param int  $modelID
     * @param int  $userID
     * @return array if allright else string error;
     */
     function checkCoupon($coupon=null, $model=null,$modelID=null,$userID=null) 
     {
         $errors = array();
         $errors[1] = "Such coupon does not exist.";
         $errors[2] = "This coupon has already been used.";
         $errors[3] = "This coupon has expired.";
         $errors[4] = "Wrong input data.";
         $errors[5] = "This coupon can not be used for this action.";
         $errors[6] = "This kind of discount is not supported for this action.";
         $errors[7] = "You have already used the promo code";

         if ($coupon && $model && $modelID) {
             $conditions = array('code'=>$coupon,'Promocode.is_deleted'=>0);
             $this->recursive = -1;
             $couponInfo = $this->find('first', array('conditions'=>$conditions));
             if (empty($couponInfo)) {
                 return $errors[1];
             }

                if (intval($couponInfo['Promocode']['uses_count'])==intval($couponInfo['Promocode']['number_of_uses'])) {
                    return $errors[2];
                }

                if (!empty($couponInfo['Promocode']['expiration_date'])) {
                    $conditions['expiration_date >'] = date('Y-m-d H:i:s');
                    $this->recursive = -1;
                    $couponInfo = $this->find('first', array('conditions'=>$conditions));
                    if (empty($couponInfo)) {
                        return $errors[3];
                    }
                }

                //Check assigments
                $checking = $this->PromocodesAssigment->find('first', array('conditions'=>array('promocode_id'=>$couponInfo['Promocode']['id'],'model'=>'All')));
                if (empty($checking)) {
                    $checking = $this->PromocodesAssigment->find('first', array('conditions'=>array('promocode_id'=>$couponInfo['Promocode']['id'],'model'=>$model,'model_id'=>-1)));
                    if (empty($checking)) {
                        $checking = $this->PromocodesAssigment->find('first', array('conditions'=>array('promocode_id'=>$couponInfo['Promocode']['id'],'model'=>$model,'model_id'=>$modelID)));
                        if (empty($checking)) {
                            return $errors[5];
                        }
                    }
                }
                //Checking if user already used thispromo code.
                // Currently user can not use promocode only once

                if ($userID) {
                    $Payment = ClassRegistry::init('Payment');
                    //$Payment->recursive = -1;
                    //$paymentCnt = $Payment->find('count',array('conditions'=>array('Payment.promocode_id'=>$couponInfo['Promocode']['id'],'Payment.user_id'=>$userID,'Payment.status'=>'Approved')));
                    $paymentCnt = $this->PaymentsPromocode->find('count', array('conditions'=>array('PaymentsPromocode.promocode_id' => $couponInfo['Promocode']['id'],'Payment.user_id' => $userID,'Payment.status'=>'Approved')));

                    if ($paymentCnt>0) {
                        return $errors[7];
                    }
                    unset($Payment);
                }

                //All right return
                return $couponInfo['Promocode'];

         } else {
                return $errors[4];
         }

        }

        /**
     * Getting array and format them
     */
        function formatDiscount($input="")
        {

            if (is_array($input)) {

                switch ($input['type']) {
                case 'Free':
                    $response = 'You can choose the cheapest package for free!!';
                    break;
                case 'Amount':
                    $response = 'You have a discount $'.$input['value'];
                    break;
                case 'Percent':
                    $response = 'You have a discount '.$input['value'].'%';
                    break;
                }

            } else {
                $response = $input;
            }

            return $response;
        }

        /**
     * Calculating discount
     *  @author vovich
     */
        function calculateDiscountAmount($discountInformation = array(),$amount = 0) 
        {

            $discountAmount = 0;

            switch ($discountInformation['type']) {
            case 'Amount':
                $discountAmount = floatval($discountInformation['value']);
                break;
            case 'Percent':
                if ($discountInformation['value']>=0) {
                    $discountAmount = floatval($amount)*floatval($discountInformation['value'])/100;
                } else {
                    $discountAmount = 1;
                }
                break;
            }

            $discountAmount = sprintf("%01.2f", $discountAmount);

            return $discountAmount;

        }

        /**
     * Functionality for use promocode - change count of use
     * @author vovich
     * @param int id Promocode ID
     */
        function usePromoCode($id=null) 
        {
            if (!$id) {
                return false;
            }
            $this->query("UPDATE promocodes SET uses_count = uses_count+1 WHERE id ='$id'");

            return true;

        }

}
?>