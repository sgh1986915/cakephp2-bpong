<?php
class Packagedetail extends AppModel
{

    var $name = 'Packagedetail';
    var $recursive = -1;

        var $belongsTo = array(
    'Package' => array('className' => 'Package',
                                  'foreignKey' => 'package_id',
                                  'dependent' => false,
                                  'conditions' => array(),
                                  'fields' => '',
                                  'order' => ''
    )
        );

}
?>