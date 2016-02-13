<?php
/**
 * Model for blog posts table
 * @author Edward
 */
class Blogpost extends AppModel
{
    var $name = 'Blogpost';

    var $actsAs = array(
    'Image'=>array(
                    'thumbs'=>array('create'=>true,'width'=>'28','height'=>'31'),
                    'versions' =>array(
                                                'thumbsBig'=>array('create'=>true,'width'=>'85','height'=>'92')
                    )
                ),
    'Sluggable' => array(    'separator' =>  '-',
                                                'label'         => 'title',
                                                'slug'          => 'slug',
                                                'length'       => 100,
                                                'overwrite'  =>  true)
                        );

    var $validate = array(
    'title' => array(
              'rule' => 'notEmpty'
    , 'message' => 'Must be not empty'
        ),

    'description' => array(
              'rule' => 'notEmpty'
    , 'message' => 'Must be not empty'
        ),
    );

    //The Associations below have been created with all possible keys, those that are not needed can be removed
    var $belongsTo = array(
    'User' => array('className' => 'User',
                                'foreignKey' => 'user_id',
                                'conditions' => '',
                                'fields' => '',
                                'order' => ''
    )
    );

}
?>
