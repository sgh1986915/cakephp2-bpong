<?php
class PromocodesAssigment extends AppModel
{
    var $name      = 'PromocodesAssigment';
    var $actsAs = array('Containable');

    var $belongsTo = array(
    'Event' => array('className' => 'Event',
                                'foreignKey' => 'model_id',
                                'conditions' => array('PromocodesAssigment.model' => 'Event', 'PromocodesAssigment.model_id !='=>-1),
                                'fields' => '',
                                'order' => ''
    )
    ,
    'StoreCategory' => array('className' => 'StoreCategory',
                                'foreignKey' => 'model_id',
                                'conditions' => array('PromocodesAssigment.model' => 'StoreCategory', 'PromocodesAssigment.model_id !='=>-1),
                                'fields' => '',
                                'order' => ''
    )
    ,
    'StoreSlot' => array('className' => 'StoreSlot',
                                'foreignKey' => 'model_id',
                                'conditions' => array('PromocodesAssigment.model' => 'StoreSlot', 'PromocodesAssigment.model_id !='=>-1),
                                'fields' => '',
                                'order' => ''
    )
    ,
    'StoreProduct' => array('className' => 'StoreProduct',
                                'foreignKey' => 'model_id',
                                'conditions' => array('PromocodesAssigment.model' => 'StoreProduct', 'PromocodesAssigment.model_id !='=>-1),
                                'fields' => '',
                                'order' => ''
    ),
            'Promocode'=>array('className'=>'Promocode',
                                'foreignKey'=>'promocode_id')
    );

    /**
     *  Add new assigment
     *  @author vovich
     *    @param array $input
     *        $input[promocode_id]
     *        $input[model]
     *        $input[model_id]
     *        $input[exact_model_id]
     *    @return Error;
     */
    function newAssigment($input=array())
    {

        if ($input['model_id']>=0 && $input['exact_model_id']>0) {
            $input['model_id'] = $input['exact_model_id'];
        } else {
            $input['model_id'] = -1;
        }
        //checking assigments
        $checking = $this->find('first', array('conditions'=>array('promocode_id'=>$input['promocode_id'],'model'=>'All')));
        if (!empty($checking)) {
            return "This assigment conflicted with another assigment!!";
        } else {
            $checking = $this->find('first', array('conditions'=>array('promocode_id'=>$input['promocode_id'],'model'=>$input['model'],'model_id'=>-1)));
            if (!empty($checking)) {
                return "This assigment conflicted with another assigment!!";
            } else {
                $checking = $this->find('first', array('conditions'=>array('promocode_id'=>$input['promocode_id'],'model'=>$input['model'],'model_id'=>$input['model_id'])));
                if (!empty($checking)) {
                    return "Such assigment assigment already exist!!";
                } else {
                    $this->create();
                        $this->save($input);


                }


            }

        }


    }


}
?>
