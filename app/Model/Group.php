<?php
class Group extends AppModel
{

    var $name = 'Group';

    var $actsAs = array ('Containable');

    var $validate = array
    (

         'name'      => array(
                            'rule'       => array('notEmpty')
                             ,'allowEmpty' => false
                             ,'message'    => 'Name should contain only letters and numbers'
                           )
    );

    //The Associations below have been created with all possible keys, those that are not needed can be removed
    var $hasMany = array(
    'Status' => array('className' => 'Status',
                                'foreignKey' => 'group_id',
                                'dependent' => false,
                                'conditions' => '',
                                'fields' => '',
                                'order' => '',
                                'limit' => '',
                                'offset' => '',
                                'exclusive' => '',
                                'finderQuery' => '',
                                'counterQuery' => ''
    )
    );

    /*Associations with default status*/
    var $belongsTo = array(
    'Status' => array('className' => 'Status',
                                'foreignKey' => 'defstats_id',
                                'conditions' => '',
                                'fields' => '',
                                'order' => ''
    )
    );


}
?>
