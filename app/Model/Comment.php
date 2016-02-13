<?php
class Comment extends AppModel
{

    var $name = 'Comment';
    var $validate = array(
    'comment'       => array('notempty')
    );
    var $actsAs = array ('Tree','Containable');
    

    //The Associations below have been created with all possible keys, those that are not needed can be removed
    var $belongsTo = array(
    'User' => array('className' => 'User',
                                'foreignKey' => 'user_id',
                                'conditions' => '',
                                'fields' => '',
                                'order' => ''
    ),
    'Blogpost' => array('className' => 'Blogpost',
                                  'foreignKey' => 'model_id',
                                  'dependent' => false,
                                  'conditions' => array('Comment.model'=>'Blogpost'),
                                  'fields' => '',
                                  'order' => ''
    ),
    'Video' => array('className' => 'Video',
                                  'foreignKey' => 'model_id',
                                  'dependent' => false,
                                  'conditions' => array('Comment.model'=>'Video'),
                                  'fields' => '',
                                  'order' => ''
    ),
    'Album' => array('className' => 'Album',
                                  'foreignKey' => 'model_id',
                                  'dependent' => false,
                                  'conditions' => array('Comment.model'=>'Album'),
                                  'fields' => '',
                                  'order' => ''
    ),            
    'Image' => array('className' => 'Image',
                                  'foreignKey' => 'model_id',
                                  'dependent' => false,
                                  'conditions' => array('Comment.model'=>'Image'),
                                  'fields' => '',
                                  'order' => ''
    ),
    'Link' => array('className' => 'Link',
                                  'foreignKey' => 'model_id',
                                  'dependent' => false,
                                  'conditions' => array('Comment.model'=>'Link'),
                                  'fields' => '',
                                  'order' => ''
    ),                                    
    'Event' => array('className' => 'Event',
                                  'foreignKey' => 'model_id',
                                  'dependent' => false,
                                  'conditions' => array('Comment.model'=>'Event'),
                                  'fields' => '',
                                  'order' => ''
    )
    );
    var $hasMany = array(
                         'Vote' => array(
                                                    'className' => 'Vote',
                                                    'foreignKey' => 'model_id',
                                                    'dependent' => true,
                                                    'conditions' => array('Vote.model' => 'Comment'),
                                                    'fields' => '',
                                                    'order' => 'Vote.created ASC',
                                                    'limit' => '',
                                                    'offset' => '',
                                                    'exclusive' => '',
                                                    'finderQuery' => '')
     
    );
     
    
    /**
 * Adding new comment
 * @param $data
 * @author vovich
 * @return unknown_type
 */    
    function addNewComent($data = array()) 
    {      
        $data['Comment']['comment'] = strip_tags($data['Comment']['comment'], "<p><i><u><strike><><ul><li>");
        if (empty($data['Comment']['parent_id'])) {
            unset ($data['Comment']['parent_id']);
        }
         $this->set($data);
        if (!$this->validates()) {
            return false;
        }
          $this->create();      
             $result =  $this->save($data['Comment']);      
             $commentId = $this->getLastInsertID(); 
             $model_info = $this->$data['Comment']['model']->find('first', array('fields'=>array('id','comments'),'contains'=> array(),'recursive' => -1, 'conditions'=>array($data['Comment']['model'].'.id'=>$data['Comment']['model_id'])));
             $model_info[$data['Comment']['model']]['comments'] = $model_info[$data['Comment']['model']]['comments'] + 1;
             $model_info[$data['Comment']['model']]['modified'] = date('Y-m-d H:i:s');
             $this->$data['Comment']['model']->save($model_info, false);
         return $commentId;    
    }
    /**
     *  generate  data for the comment tree
     * @param $model
     * @param $modelId
     * @return unknown_type
     */
    function getCommentsTree($model = null, $modelId = null, $limit = false) 
    {
        $comments = array();
        $conditions = array('Comment.model'=>$model,'Comment.model_id'=>$modelId,'Comment.is_deleted <>'=>1);
        //$comments = $this->generatetreelist(array(), '{n}.Comment.id', '{n}.Comment.comment', '_',1);
        $comments = $this->find(
            'threaded', array('order'=>"lft", 
                                         'limit' => $limit,
                                         'conditions' => $conditions,
                                         'contain'=>array('User')
                                         )
        ); 
        
        return $comments;        
    }
    /**
 * mark comment as deleted
 * @param unknown_type $commentId
 * @param unknown_type $model
 * @param unknown_type $modelId
 * @author vovich
 * @return unknown_type
 */    
    function deleteComment($commentId = null, $model= null, $modelId = null) 
    {
           $newComments = 0; 
           $comment['id'] = $commentId;
           $comment['is_deleted'] = 1;
           $comment['deleted'] = date('Y-m-d-h'); 
        if ($this->save($comment, false)) {
            $childCount = $this->childcount($commentId);
            if ($childCount > 0) { //Hide all child comments 
                    $children =  $this->children($commentId);
                foreach ( $children as $child ) {
                    if ($child['Comment']['is_deleted'] != 1) { 
                        $this->save(
                            array('id' => $child['Comment']['id'],
                                            'is_deleted' => 1,
                                            'deleted' => date('Y-m-d-h') )
                        );
                    } else {
                        $childCount--;
                    }
                }
            }
            $this->$model->recursive = -1;
            $modelInfo = $this->$model->find('first', array('fields'=>array($model.".id",$model.".comments"),'contain'=>array(),'conditions'=>array($model.".id"=>$modelId)));
            if (!empty($modelInfo[$model]['comments'])) {
                $newComments = $modelInfo[$model]['comments'] = $modelInfo[$model]['comments'] -1 - $childCount;
                $this->$model->save($modelInfo, false);
            }
        }
           return $newComments;
    }
}