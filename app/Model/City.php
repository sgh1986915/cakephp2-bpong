<?php
class City extends AppModel
{
    var $name = 'City';
    var $actsAs= array('Containable');
    var $recursive = -1;

    var $belongsTo = array(
        'Provincestate' => array('className' => 'Provincestate',
                                'foreignKey' => 'provincestate_id',
                                'dependent' => true,
                                'conditions' => '',
                                'fields' => '',
                                'order' => ''
            ),
        'Country' => array('className' => 'Country',
                                'foreignKey' => 'country_id',
                                'dependent' => true,
                                'conditions' => '',
                                'fields' => '',
                                'order' => ''
            )          
    );

    
}
?>