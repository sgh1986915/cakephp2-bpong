<?php
class Vote extends AppModel
{

    var $name = 'Vote';
    var $actsAs = array ('Containable');
    
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    var $belongsTo = array(
    'User' => array('className' => 'User',
                                'foreignKey' => 'user_id',
                                'conditions' => '',
                                'fields' => '',
                                'order' => ''
    ),
    'Comment' => array('className' => 'comment',
                                  'foreignKey' => 'model_id',
                                  'dependent' => false,
                                  'conditions' => array('Vote.model'=>'Comment'),
                                  'fields' => '',
                                  'order' => ''
    ),
    'Blogpost' => array('className' => 'Blogpost',
                                  'foreignKey' => 'model_id',
                                  'dependent' => false,
                                  'conditions' => array('Vote.model'=>'Blogpost'),
                                  'fields' => '',
                                  'order' => ''
    ),
    'Album' => array('className' => 'Album',
                                  'foreignKey' => 'model_id',
                                  'dependent' => false,
                                  'conditions' => array('Vote.model'=>'Album'),
                                  'fields' => '',
                                  'order' => ''
    ),    
    'Link' => array('className' => 'Link',
                                  'foreignKey' => 'model_id',
                                  'dependent' => false,
                                  'conditions' => array('Vote.model'=>'Link'),
                                  'fields' => '',
                                  'order' => ''
    ),    
    'Video' => array('className' => 'Video',
                                  'foreignKey' => 'model_id',
                                  'dependent' => false,
                                  'conditions' => array('Vote.model'=>'Video'),
                                  'fields' => '',
                                  'order' => ''
    ),    
    'Image' => array('className' => 'Image',
                                  'foreignKey' => 'model_id',
                                  'dependent' => false,
                                  'conditions' => array('Vote.model'=>'Image'),
                                  'fields' => '',
                                  'order' => ''
    ),                                            
    'Event' => array('className' => 'Event',
                                  'foreignKey' => 'model_id',
                                  'dependent' => false,
                                  'conditions' => array('Vote.model'=>'Event'),
                                  'fields' => '',
                                  'order' => ''
    )
    );

    
    /**
 * check if current user can vote
 * @author vovich
 * @param unknown_type $model
 * @param unknown_type $modelId
 * @param unknown_type $userId
 * @return if error then text
 */    
    function canVote($model,$modelId,$userId)
    {
            $result = "";
            $cnt = $this->find('count', array('contains'=>array(),'conditions'=>array('Vote.model'=>$model,'Vote.model_id'=>$modelId, 'Vote.user_id'=>$userId)));
        if ($cnt>0) {
            $result = "You have already voted for this ".$model;
        } else {
            $modelUserId = $this->$model->field('user_id', array('id'=>$modelId));
            if ($modelUserId == $userId) {
                $result = "You can not vote for your own ".$model;
           }     
        }
            return $result;
    }
    /**
 * Adding new vote
 * @author vovich
 * @param $data
 * @return unknown_type
 */    
    function add($data = array()) 
    {
      
        $this->set($data);     
        $this->create();      
         $result =  $this->save($data);
         $model = $data['model'];
      
         $model_info = $this->$model->find('first', array('contains'=> array(),'recursive' => -1, 'conditions'=>array($model.'.id'=>$data['model_id'])));
      
        if (!empty($model_info)) {
             $minfo = $model_info[$model];
           
            if ($data['delta'] >= 0 ) {
                $minfo['votes_plus'] = $minfo['votes_plus'] + 1;
                $minfo['modified'] = date('Y-m-d H:i:s');
            } else {
                $minfo['votes_minus'] = $minfo['votes_minus'] + 1;
            }
               
                $minfo['last_user_id'] = $data['user_id'];
                $minfo['last_voted'] = date('Y-m-d H:i:s');
                $this->$model->save($minfo, false);
                return $minfo;          
        }
      
         return 0;
    
    }

    /**
 * Get voted list
 * @author vovich
 * @param char $model
 * @param int  $modelId
 * @param int  $userId
 * @return array(commentId=>point)
 */    
    function getVotes($model = null, $modelId = null, $userId = null ) 
    {
            //$delta = $this->field('delta', array('model' =>$model,'model_id'=>$modelId,'user_id'=>$userId));
            //return array($modelId =>$delta );
            $result = array();
            $points = $this->find('all', array('contain'=>array(),'conditions'=>array('Vote.model' =>$model,'Vote.model_id'=>$modelId,'Vote.user_id'=>$userId)));
        if (!empty($points)) {
            foreach ($points as $point) {
                    $result[$point['Vote']['model_id']] = $point['Vote']['delta'];
            }            
        }
            return $result;
    }
    /**
 *  Get  votes for comment
 *  @author vovich
 * @param char $model
 * @param int  $modelId
 * @param int  $userId
 * @return array(commentId=>point)    
 */
    function getCommentVotes($model = null, $modelId = null, $userId = null) 
    {
        $votes = array();
        $rows = $this->find('all', array('contain'=>array('Comment'), 'conditions' => array('Comment.model' => $model, 'Comment.model_id' => $modelId,'Vote.user_id'=>$userId)));
        if (!empty($rows)) {
            foreach ($rows as $r) {
                    $votes[$r['Comment']['id']] =$r['Vote']['delta'];
            }
        }
            return $votes;        
    }
}