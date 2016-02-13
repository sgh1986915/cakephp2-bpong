<?php
class KnowledgeAnswer extends AppModel
{
    
    var $name = 'KnowledgeAnswer';
    
    var $actsAs = array ('Containable','Sluggable' => array('separator' =>  '-', 
                                                            'label'         => 'name',
                                                            'slug'          => 'slug',
                                                            'unique'         => true,
                                                            'length'       => 100,
                                                            'overwrite'  =>  true)
    );
     
    
    
    var $belongsTo = array ('KnowledgeQuestion' => array (
                                  'className' => 'KnowledgeQuestion'
                                , 'foreignKey' => 'question_id'
                                , 'conditions' => ''
                                , 'fields' => ''
                                , 'order' => '' )
                            );                        

    
}

?>
