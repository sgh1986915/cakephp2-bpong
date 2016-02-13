<?php
class Question extends AppModel
{
    var $name = 'Question';

    var $hasMany = array(
    'Option' => array('className' => 'Option',
                                'foreignKey' => 'question_id',
                                'dependent' => true,
                                'conditions' => array(),
                                'fields' => '',
                                'order' => 'Option.priority'
    ));


}
?>