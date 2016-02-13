<?php
class Venuetype extends AppModel
{

    var $name = 'Venuetype';
    var $validate = array(
    'name' => array('alphanumeric')
    );

    var $hasOne = array(
    'Venue' => array('className' => 'Venue',
                                'foreignKey' => 'venuetype_id',
                                'dependent' => false,
                                'conditions' => '',
                                'fields' => '',
                                'order' => ''
    )
    );

}
?>
