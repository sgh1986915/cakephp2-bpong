<?php

class Video extends AppModel
{

    var $name = 'Video';
    var $recursive = -1;
    var $actsAs= array(
    'Tag', 'SoftDeletable' // Use Behavior Tag only with Behavior SoftDeletable !!!! Because of increase/decrease Tag's counter !!!
    ,'Containable');

    //The Associations below have been created with all possible keys, those that are not needed can be removed
    var $hasAndBelongsToMany = array(
    'Tag' => array('className' => 'Tag',
                        'joinTable' => '',
                        'with'=>'ModelsTag',
                        'foreignKey' => 'model_id',
                        'associationForeignKey' => 'tag_id',
                        'unique' => true,
                        'conditions' => array('ModelsTag.model' => 'Video'),
                        'order' => '',
                        'limit' => ''
    )    
    );
    
    var $belongsTo = array(
    'User' => array('className' => 'User',
                                'foreignKey' => 'user_id',
                                'conditions' => '',
                                'fields' => '',
                                'order' => ''
    ),
    'Album' => array('className' => 'Album',
                'foreignKey' => 'model_id',
                'conditions' => array('Video.model' => 'Album'),
                'fields' => '',
                'order' => ''
    )                
    );
    
    var $hasMany = array(
                             'Vote' => array(
                                                        'className' => 'Vote',
                                                        'foreignKey' => 'model_id',
                                                        'dependent' => true,
                                                        'conditions' => array('Vote.model' => 'Video'),
                                                        'fields' => '',
                                                        'order' => 'Vote.created ASC',
                                                        'limit' => '',
                                                        'offset' => '',
                                                        'exclusive' => '',
                                                        'finderQuery' => ''),
                             'Comment' => array(
                                                        'className' => 'Comment',
                                                        'foreignKey' => 'model_id',
                                                        'dependent' => true,
                                                        'conditions' => array('Comment.model' => 'Video'),
                                                        'fields' => '',
                                                        'order' => 'Comment.id ASC',
                                                        'limit' => '',
                                                        'offset' => '',
                                                        'exclusive' => '',
                                                        'finderQuery' => '')
         
    );
    /**
    * Increase in views 1
     * @author Oleg D.
     */    
    function changeViews($id) 
    {
        return $this->query('UPDATE videos SET views = views + 1 WHERE id = ' . $id); 
    }
    
    /**
     * Get array of fast image paging / next if  click on mage
     * @author Oleg D.
     */    
    function albumVideoPaging($videoID, $albumID) 
    {
        $results['count'] = 0;
        $results['nextID'] = 0;
        $results['pageNum'] = 1;
        
        $this->recursive = -1;        
        $videos = $this->find('all', array('conditions' => array('Video.model_id' => $albumID, 'Video.model' => 'Album', 'Video.is_deleted' => 0), 'fields' => array('Video.id'), 'order' => array('id' => 'DESC')));    
        $results['count'] = count($videos);
        if ($results['count'] > 1) {
            $thisVideoKey = 0;
            foreach ($videos as $videoKey => $video) {
                if ($video['Video']['id'] == $videoID) {
                    $thisVideoKey = $videoKey;
                    break;
                }                                   
            }
            $results['pageNum'] = $thisVideoKey+1;
            if (isset($videos[$thisVideoKey+1])) {
                $results['nextID'] = $videos[$thisVideoKey+1]['Video']['id'];                                 
            } else {
                $results['nextID'] = $videos[0]['Video']['id'];                
            }
        }    
        return $results;
    }
    /**
     * Return video image by video ID
     * @author Oleg D.
     */
    function getVideoImage($id, $size = 'big') 
    {
        if ($id) {
            return 'http://img.youtube.com/vi/' . $id . '/1.jpg';
        } else {
            if ($size == 'big') {
                return "/img/video-120-90.png";
            } else {
                return "/img/video-75-57.png";                
            }
        }
    }
}


?>
