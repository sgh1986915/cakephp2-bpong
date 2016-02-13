<?php
class KnowledgeQuestion extends AppModel
{

    var $name = 'KnowledgeQuestion';

    var $actsAs = array ('Containable','Sluggable' => array('separator' =>  '-',
                                                            'label'         => 'question',
                                                            'slug'          => 'slug',
                                                            'unique'         => true,
                                                            'length'       => 100,
                                                            'overwrite'  =>  true)
    );

    var $validate = array ('question' =>
                    array ('rule' => 'notEmpty', 'required' => true, 'message' => 'Value not empty' ),
                    );


    var $hasMany = array ('KnowledgeAnswer' => array (
                                  'className' => 'KnowledgeAnswer'
                                , 'foreignKey' => 'question_id'
                                , 'dependent' => true
                                , 'conditions' => ''
                                , 'fields' => ''
                                , 'order' => ''
                                , 'limit' => ''
                                , 'offset' => ''
                                , 'exclusive' => ''
                                , 'finderQuery' => ''
                                , 'counterQuery' => '' )

                        );
    var $belongsTo = array ('KnowledgeTopic' => array (
                                  'className' => 'KnowledgeTopic'
                                , 'foreignKey' => 'topic_id'
                                , 'conditions' => ''
                                , 'fields' => ''
                                , 'order' => '' )
                            );


}

?>
