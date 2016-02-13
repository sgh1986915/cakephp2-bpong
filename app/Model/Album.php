<?php
class Album extends AppModel
{

    var $name = 'Album';
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
                        'conditions' => array('ModelsTag.model' => 'Album'),
                        'order' => '',
                        'limit' => ''
    )    
    );
    var $belongsTo = array(
    'CoverImage' => array('className' => 'Image',
                                'foreignKey' => 'cover_image_id',
                                'conditions' => array('CoverImage.is_deleted' => 0),
                                'fields' => '',
                                'order' => ''
    ), 'CoverVideo' => array('className' => 'Video',
                                'foreignKey' => 'cover_video_id',
                                'conditions' => array('CoverVideo.is_deleted' => 0),
                                'fields' => '',
                                'order' => ''
    ), 'User' => array('className' => 'User',
                                'foreignKey' => 'user_id',
                                'conditions' => '',
                                'fields' => '',
                                'order' => ''
    )
    );
    var $hasMany = array(
                             'Vote' => array(
                                                        'className' => 'Vote',
                                                        'foreignKey' => 'model_id',
                                                        'dependent' => true,
                                                        'conditions' => array('Vote.model' => 'Album'),
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
                                                        'conditions' => array('Comment.model' => 'Album'),
                                                        'fields' => '',
                                                        'order' => 'Comment.id ASC',
                                                        'limit' => '',
                                                        'offset' => '',
                                                        'exclusive' => '',
                                                        'finderQuery' => '')
         
    );
    
    
    /**
     * get array of albums
     * @author Oleg D.
     */
    
    function getAlbums($model, $modelID) 
    {
        $this->recursive = 1;
        $albums = $this->find('all', array('conditions' => array('Album.model' => $model, 'Album.model_id' => $modelID, 'Album.is_deleted' => 0)));
        return    $albums;
    }
    
    /**
     * Get user Access for album
     * @author Oleg D.
     */
    function getAlbumUploadAccess($userID, $modelName, $modelID, $Access, $getAll = 0) 
    {

        $Model = ClassRegistry::init($modelName);           
        return $Model->getAlbumUploadAccess($userID, $modelID, $Access, $getAll);
        
    }
    /**
     * Change number of the photo
     * @author Oleg D.
     */
    function changeFilesNum($id, $delta = 0) 
    {
        if ($delta > 0) {
            $delta = ' + ' . $delta;    
        }
        $this->query("UPDATE albums SET files_num = files_num " . $delta . ", modified = '" . date("Y-m-d h:i:s") . "' WHERE albums.id = " . $id);   
        
    }
    // delete album	
    function deleteAlbum($id) 
    {
        $this->delete($id);        
        $this->query("UPDATE images SET is_deleted = 1, modified = '" . date("Y-m-d h:i:s") . "', deleted = '" . date("Y-m-d h:i:s") . "' WHERE model = 'Album' AND model_id = " . $id);    
        $this->query("UPDATE videos SET is_deleted = 1, modified = '" . date("Y-m-d h:i:s") . "', deleted = '" . date("Y-m-d h:i:s") . "' WHERE model = 'Album' AND model_id = " . $id);              
        return 1;           
    }
    /**
     * Get junc album id of user     
     * @author Oleg D.
     */
    function getJunkId($userID, $create = 1) 
    {
        $id = $this->field('id', array('user_id' => $userID, 'content_type' => 'junk'));
        if (!$id && $create) {
            $album['id'] = $id;     
            $album['content_type'] = 'junk';  
            $album['model'] = 'User';  
            $album['model_id'] = $userID; 
            $album['user_id'] = $userID;      
            $album['name'] = 'Uploaded By Itself';                                 
            $this->save($album);
            $id = $this->getLastInsertID();
        }
        if (!$id) {
            $id = 0;
        }  
        return $id;        
    }

    
}
?>
