<?php
class KnowledgeTopic extends AppModel
{

    var $name = 'KnowledgeTopic';

    var $actsAs = array ('Containable', 'Tree', 'Sluggable' => array('separator' =>  '-',
                                                            'label'         => 'name',
                                                            'slug'          => 'slug',
                                                            'unique'         => true,
                                                            'length'       => 100,
                                                            'overwrite'  =>  true)
    );

    var $validate = array ('name' =>
                    array ('rule' => 'notEmpty', 'required' => true, 'message' => 'Value not empty' ),
                    );


    var $hasMany = array ('KnowledgeQuestion' => array (
                                  'className' => 'KnowledgeQuestion'
                                , 'foreignKey' => 'topic_id'
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

    function setParent($id, $newParentId) 
    {
        $el = $this->read(null, $id);
        if ($el['KnowledgeTopic']['parent_id'] == $newParentId) {
            return  ('This element is already in selected group.');
        }

        $this->id = $id;
        $this->saveField('parent_id', $newParentId);

        $this->movedown($id, true);
        return '';
    }

    function sort($id, $direction = "up") 
    {
        $result = false;
        if ($direction == "up") {
            $result = $this->moveup($id, 1);
        } else {
            $result = $this->movedown($id, 1);
        }

        /*        if (!$result) {

            $curparentId = $this->field('parent_id',array('id' => $id));
            if (!empty($curparentId)) {
             if ($direction == "up") {
                $newParentId = $this->getparentnode( $curparentId, array('id'), 0);
                $newParentId = $newParentId['KnowledgeTopic']['id'];
                if (!empty($newParentId)) {
                    $this->id = $id;
                    $this->saveField('parent_id', $newParentId);
                    $result = $this->moveup($id,true);
                }
             } else {
                $result = $this->movedown($id,true);
                $child = $this->children($curparentId,true);
                print_r($child);
             }


            }
        }*/

    }


}
