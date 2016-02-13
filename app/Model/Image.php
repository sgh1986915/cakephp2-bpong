<?php
App::import('Vendor', 'example', array('file' => 'class.upload.php'));

class Image extends AppModel
{

    var $name = 'Image';
    var $recursive = -1;
    var $actsAs= array(
        'Tag', 'SoftDeletable' // Use Behavior Tag only with Behavior SoftDeletable !!!! Because of increase/decrease Tag's counter !!!
        ,'Containable'
    );
    var $albumSettings = array(
                    'thumb'=>array('create'=>true, 'width'=>'167', 'height'=>'120', 'bgcolor'=> false),
                    'versions'=>array(
                                'small'=>array('width'=>'70', 'height'=>'81', 'bgcolor'=>false),
                                'big'=>array('width'=>'689', 'height'=>'492', 'bgcolor'=>false)
                            )
    );
    var $hasAndBelongsToMany = array(
    'Tag' => array('className' => 'Tag',
                        'joinTable' => '',
                        'with'=>'ModelsTag',
                        'foreignKey' => 'model_id',
                        'associationForeignKey' => 'tag_id',
                        'unique' => true,
                        'conditions' => array('ModelsTag.model' => 'Image'),
                        'order' => '',
                        'limit' => ''
    )    
    );
    var $hasMany = array(
                             'Vote' => array(
                                                        'className' => 'Vote',
                                                        'foreignKey' => 'model_id',
                                                        'dependent' => true,
                                                        'conditions' => array('Vote.model' => 'Image'),
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
                                                        'conditions' => array('Comment.model' => 'Image'),
                                                        'fields' => '',
                                                        'order' => 'Comment.id ASC',
                                                        'limit' => '',
                                                        'offset' => '',
                                                        'exclusive' => '',
                                                        'finderQuery' => '')
         
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
                'conditions' => array('Image.model' => 'Album'),
                'fields' => '',
                'order' => ''
    )        
    );
    /**
     * Show list of the Images for this object
     * @author Oleg D.
     */
    function myImages($model_name, $model_id, $prop = null)
    {
        $result = false;
        $conditions = array();
        $conditions["model"]    = $model_name;
        $conditions["model_id"] = $model_id;
        $conditions["is_deleted"] = ' <> 1';
        if ($prop) {
            $conditions["prop"] = $prop;
        }
        $result=$this->find('all', array('conditions' => $conditions));

        return $result;
    }
    
      /**
       * escape name of the File
       * @author Oleg
       */
    function escapeFile($string, $nums=0)
    {
            $string = str_replace(" ", "_", $string);
            $string = str_replace("&amp;", "and", $string);
            $string = str_replace("&", "and", $string);
            $string = eregi_replace('[^a-zA-Z0-9]', '_', $string);
        $string = str_replace("___", "_", $string);
          $string = str_replace("__", "_", $string);
        if($nums) {
            $string=substr($string, 0, $nums);
        }  
        $string=strtolower($string);
    
        return $string;
    }    
    /**
     * Save Image to album
     * @author Oleg D.
     */
    function saveToAlbum($image, $albumID, $userID, $imageID = 0, $is_deleted = 0) 
    {
          $foo = new Upload($image);            
        if ($albumID) {
            $album = $this->Album->find('first', array('conditions' => array('Album.id' => $albumID)));
            $modelName = $album['Album']['model'];
        } else {
            $modelName = null;    
        }
          
             $albumSettings = $this->albumSettings; 
        if ($modelName) {
            $ModelObject = ClassRegistry::init($modelName);
            if (isset($ModelObject->actsAs['Image']['versions'])) {
                foreach ($ModelObject->actsAs['Image']['versions'] as $versionName => $version) {
                    if (!isset($albumSettings['versions'][$versionName])) {
                        $albumSettings['versions'][$versionName] = $version;
                    } else {
                        $albumSettings['versions'][$modelName . '_' . $versionName] = $version;                          
                    }    
                }                  
            }
        }
       
             $filename = $userID . '_' . $this->generateRandomFilename($image['name']);
             $filenameTemporary = $this->generateRandomFilename();
          
             $image['image_src_x'] = $foo->image_src_x;
          $image['image_src_y'] = $foo->image_src_y;
        if ($foo->uploaded) { 
            $foo->file_new_name_body = $filenameTemporary;                                 
              $foo->Process(TMP_DIR); 
              // Move file to cloud hosting           
              $filename = $this->saveOnCloudHosting('img_albums', TMP_DIR . $foo->file_dst_name, $filename);

            if($albumSettings['thumb']['create']) {
                $foo->image_resize = true;
                $foo->image_ratio = true;                 
                    
                if(isset($albumSettings['thumb']['height'])) {
                    $foo->image_y = $albumSettings['thumb']['height'];
                } else {
                    $foo->image_y = $image['image_src_y'];                    
                }
                if(isset($albumSettings['thumb']['width']) && $image['image_src_x'] > $albumSettings['thumb']['width']) {
                    $foo->image_x = $albumSettings['thumb']['width'];
                } else {
                    $foo->image_x = $image['image_src_x'];                        
                }
                    
                if(isset($albumSettings['thumb']['bgcolor']) && $albumSettings['thumb']['bgcolor']) {
                    $foo->image_background_color = $albumSettings['thumb']['bgcolor'];
                }else{
                    //$foo->image_background_color = '#FFFFFF';
                }
                    
                    $foo->file_new_name_body = $filenameTemporary;                         
                    $foo->Process(TMP_DIR);
                    // Move file to cloud hosting   
                    $this->saveOnCloudHosting('img_albums', TMP_DIR . $foo->file_dst_name, 'thumb_' . $filename);
                    
                    // save image to DB
                    $dbImage['filename'] = $filename;                
                    $dbImage['user_id']  = $userID;
                    $dbImage['height'] = $image['image_src_y'];
                    $dbImage['width'] = $image['image_src_x'];
                    $dbImage['model'] = 'Album';
                    $dbImage['model_id'] = $albumID;
                if ($is_deleted) {
                    $dbImage['is_deleted'] = 1;   
                    $dbImage['deleted'] = date("Y-m-d H:i:s");                           
                }
                    // eof saving image to DB
                    
                    $this->create();
                    $this->save($dbImage);
                    $imageID = $this->getLastInsertID();

                                        
            }

            if(isset($albumSettings['versions']) && !empty($albumSettings['versions'])) {    
                foreach($albumSettings['versions'] as $version_name=>$version){
                       //$foo->image_convert = 'jpg';
                    if((isset($version['width']) &&  $image['image_src_x'] > $version['width']) || (isset($version['height']) &&  $image['image_src_y'] > $version['height'])) {
                        $foo->image_resize          = true;
                    } else {
                        $foo->image_resize          = false;                               
                    }
                            
                       $foo->image_ratio = true;
                    if(isset($version['height']) &&  $image['image_src_y'] > $version['height']) {
                        $foo->image_y = $version['height'];
                    } else {
                        $foo->image_y = $image['image_src_y'];                               
                    }
                    if(isset($version['width']) &&  $image['image_src_x'] > $version['width']) {
                        $foo->image_x = $version['width'];
                    } else {
                        $foo->image_x = $version['image_src_x'];                              
                    }
                    if(isset($version['bgcolor']) && $version['bgcolor']) {
                        $foo->image_ratio_fill      = 'C';
                        $foo->image_background_color = $version['bgcolor'];
                    }else{
                        //$foo->image_background_color = '#FFFFFF';
                    }

                        $foo->file_new_name_body = $filenameTemporary;                                 
                        $foo->Process(TMP_DIR); 
                        $this->saveOnCloudHosting('img_albums', TMP_DIR . $foo->file_dst_name, $version_name . '_' . $filename);
                }
            }
        }
            return $imageID;
    }
    /**
     * Migrate Images to some album to album
     * @author Oleg D.
     */
    function migrateToAlbum($imageName, $imageRoot, $albumID, $userID, $modelName = '') 
    {
        $imageID = 0;
          $foo = new Upload($imageRoot . $imageName);            
          
          $baseDir = WWW_ROOT . 'img' . DS . 'albums' . DS . $userID . DS;
          $thumbsDir = WWW_ROOT . 'img' . DS . 'albums' . DS . $userID . DS .'thumb'.DS;
          
          $albumSettings = $this->albumSettings; 
                    
        if ($modelName) {
            $ModelObject = ClassRegistry::init($modelName);
            if (isset($ModelObject->actsAs['Image']['versions'])) {
                foreach ($ModelObject->actsAs['Image']['versions'] as $versionName => $version) {
                    if (!isset($albumSettings['versions'][$versionName])) {
                        $albumSettings['versions'][$versionName] = $version;
                    } else {
                        $albumSettings['versions'][$modelName . '_' . $versionName] = $version;                          
                    }    
                }                  
            }
        }
       
          $filename = $this->makeFilename($imageName);
          $image['image_src_x'] = $foo->image_src_x;
          $image['image_src_y'] = $foo->image_src_y;
        if ($foo->uploaded) { 
            $foo->file_new_name_body = $filename;                                 
              $foo->Process($baseDir);            
            if($albumSettings['thumb']['create']) {
                //$foo->image_convert = 'jpg';
                $foo->image_resize = true;
                $foo->image_ratio = true;                 
                //$foo->image_ratio_fill      = 'C';
                    
                if(isset($albumSettings['thumb']['height'])) {
                    $foo->image_y               = $albumSettings['thumb']['height']; 
                }
                if(isset($albumSettings['thumb']['width']) && $image['image_src_x'] > $albumSettings['thumb']['width']) {
                    $foo->image_x               = $albumSettings['thumb']['width'];
                }
                if(isset($albumSettings['thumb']['bgcolor']) && $albumSettings['thumb']['bgcolor']) {
                    $foo->image_background_color = $albumSettings['thumb']['bgcolor'];
                }else{
                    //$foo->image_background_color = '#FFFFFF';
                }
                    
                $foo->file_new_name_body = $filename;                         
                $foo->Process($thumbsDir);
                echo $foo->error;
                // save image to DB
                $dbImage['filename'] = $foo->file_dst_name;                
                $dbImage['user_id']  = $userID;
                $dbImage['height'] = $image['image_src_y'];
                $dbImage['width'] = $image['image_src_x'];
                $dbImage['model'] = 'Album';
                $dbImage['model_id'] = $albumID;

                    
                $this->create();
                $this->save($dbImage);
                $imageID = $this->getLastInsertID();

                $filename = $foo->file_dst_name_body;
                                        
            }
    
            if(isset($albumSettings['versions']) && !empty($albumSettings['versions'])) {    
                foreach($albumSettings['versions'] as $version_name=>$version){
                          //$foo->image_convert = 'jpg';
                    if(isset($version['width']) &&  $image['image_src_x'] > $version['width']) {
                        $foo->image_resize          = true;
                    } else {
                          $foo->image_resize          = false;                               
                    }
                            $foo->image_ratio = true;
                    if(isset($version['height'])) {
                        $foo->image_y               = $version['height']; 
                    }
                    if(isset($version['width']) &&  $image['image_src_x'] > $version['width']) {
                        $foo->image_x               = $version['width'];
                    }
                    if(isset($version['bgcolor']) && $version['bgcolor']) {
                        $foo->image_ratio_fill      = 'C';
                        $foo->image_background_color = $version['bgcolor'];
                    }else{
                        //$foo->image_background_color = '#FFFFFF';
                    }
                            $foo->file_new_name_body = $filename;
                            $foo->Process($baseDir . $version_name . DS);
                }
            }
        }
            return $imageID;
    }    
    /**
   * Make water mark
   * @author Oleg
   */
    function watermark($base_image, $logo ) 
    {
          $size = getimagesize($base_image);
          $IMAGE_WIDTH  = $size[0];
          $IMAGE_HEIGHT = $size[1];

          //Load and resize the image
          $uploaded = imagecreatefromjpeg($base_image);
          $image = imagecreatetruecolor($IMAGE_WIDTH, $IMAGE_HEIGHT);
          imagecopyresampled($image, $uploaded, 0, 0, 0, 0, $IMAGE_WIDTH, $IMAGE_HEIGHT, imagesx($uploaded), imagesy($uploaded));
          imagealphablending($image, true); //allows us to apply a 24-bit watermark over $image

          $size2 = getimagesize($logo);
          $SOLD_WIDTH  = $size2[0];
          $SOLD_HEIGHT = $size2[1];

          //Load the sold watermark
          $sold_band = imagecreatefrompng($logo);
          imagealphablending($sold_band, true);

          //Apply watermark and save
          //$image = image_overlap($image, $sold_band);
          imagecopy($image, $sold_band, $IMAGE_WIDTH - $SOLD_WIDTH, $IMAGE_HEIGHT - $SOLD_HEIGHT, 0, 0, $SOLD_WIDTH, $SOLD_HEIGHT);
          imagedestroy($uploaded);
          imagedestroy($sold_band);
          return $image;
    }
 
    /**
     * Get array of fast image paging / next if  click on mage
     * @author Oleg D.
     */    
    function albumImagePaging($imageID, $albumID) 
    {
        $results['count'] = 0;
        $results['nextID'] = 0;
        $results['pageNum'] = 1;
        
        $this->recursive = -1;        
        $images = $this->find('all', array('conditions' => array('Image.model_id' => $albumID, 'Image.model' => 'Album', 'Image.is_deleted' => 0), 'fields' => array('Image.id'), 'order' => array('id' => 'DESC')));    
        $results['count'] = count($images);
        if ($results['count'] > 1) {
            $thisImageKey = 0;
            foreach ($images as $imageKey => $image) {
                if ($image['Image']['id'] == $imageID) {
                    $thisImageKey = $imageKey;
                    break;
                }                                   
            }
            $results['pageNum'] = $thisImageKey+1;
            if (isset($images[$thisImageKey+1])) {
                $results['nextID'] = $images[$thisImageKey+1]['Image']['id'];                                 
            } else {
                $results['nextID'] = $images[0]['Image']['id'];                
            }
        }    
        return $results;
    }    
    
    /**
    * Increase in views 1
     * @author Oleg D.
     */    
    function changeViews($id) 
    {
        return $this->query('UPDATE images SET views = views + 1 WHERE id = ' . $id); 
    }
    
    
    /**
     * Get store slots images
     * @author Oleg D.
     */
    function getStoreImages($slotID) 
    {
        $images= array();
        
        $this->Album->recursive = -1;
        $albums = $this->Album->find('list', array('fields' => array('id', 'id'), 'conditions' => array('model' => 'StoreSlot', 'model_id' => $slotID, 'is_deleted' => 0)));
        
        $this->recursive = -1;        
        $images = $this->find('all', array('fields' => array('*', 'if (order_view = 0, 10000, order_view) as custom_order'), 'order' => array('custom_order' => 'ASC', 'Image.id' => 'ASC'),'conditions' => array('model' => 'Album', 'model_id' => $albums, 'is_deleted' => 0)));

        return $images;            
    }
    
    
    function createRackspaceConnection() 
    {
        if (!isset($this->RackspaceConnection)) {
            include_once '../Vendor/rackspace_cloudfiles/cloudfiles.php';
            // Connect to Rackspace
            $Auth = new CF_Authentication(RACKSPACE_CLOUDFILE_USERNAME, RACKSPACE_CLOUDFILE_APIKEY);
            $Auth->authenticate();
            $this->RackspaceConnection = new CF_Connection($Auth);    
            //echo "Create Connection<br/>";		
        }
        
        return $this->RackspaceConnection;
    }
    
    function createRackspaceContainer($containerName) 
    {
        $this->createRackspaceConnection();
        if (!isset($this->RackspaceContainer) || $this->RackspaceContainer->name != $containerName) {
            $this->RackspaceContainer = $this->RackspaceConnection->get_container($containerName);    
            //echo "Create Container<br/>";		
        }

        return $this->RackspaceContainer;
    }    
    /**
     * Save file on cloud hosting
     * $containerName = 'img_albums';
     * $filePathName =  '../webroot/img/banner.jpg';
     * $cloudFileName = 'banner.jpg';
     *  
     * @author Oleg D.
     */
    
    function saveOnCloudHosting($containerName, $filePathName, $cloudFileName, $unlink = true) 
    {                    
        // Get the container we want to use
        $this->createRackspaceContainer($containerName);

        // check for exists file
        $isUniq = false;
        while(!$isUniq) {
            $CheckExistObject = new CF_Object($this->RackspaceContainer, $cloudFileName);
            if ($CheckExistObject->exists()) {    
                $explName = explode('.', $cloudFileName);
                $cloudFileName = $explName[0]  . '_'. '.' . $explName[1];
                
            } else {
                $isUniq = true;        
            }
        }
        // save file
        $Object = $this->RackspaceContainer->create_object($cloudFileName);
        //echo 'move - ' . $filePathName . ' =====>' . $cloudFileName . '<br/>';
        if ($Object->load_from_filename($filePathName)) {        
            if ($unlink) {
                @unlink($filePathName);
            }
        }
        unset($Object);
        
        return $cloudFileName;        
    }
    /**
     * Delete file from cloud hosting
     * $containerName = 'img_albums';
     * $cloudFileName = 'banner.jpg';
     *  
     * @author Oleg D.
     */
    function deleteFromCloudHosting($containerName, $cloudFileName) 
    {
                
        include_once '../vendors/rackspace_cloudfiles/cloudfiles.php';    
        // Connect to Rackspace
        $Auth = new CF_Authentication(RACKSPACE_CLOUDFILE_USERNAME, RACKSPACE_CLOUDFILE_APIKEY);
        $Auth->authenticate();
        $Connection = new CF_Connection($Auth);
        
        // Get the container we want to use
        $Container = $Connection->get_container($containerName);
                
        // check for exists file
        $CheckExistObject = new CF_Object($Container, $cloudFileName);
        if (!$CheckExistObject->exists()) {    
            return false;                
        }
        // delete file
        return $Container->delete_object($cloudFileName);        
    }
    
    
    
    function generateRandomFilename($filename = null, $length = 20) 
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';            
        $string = '';

        for ($p = 0; $p < $length; $p++) {
            $string .= $characters[mt_rand(0, strlen($characters) -1)];
        }
        if ($filename) {
            $explodeFilename = explode(".", $filename);
            if (count($explodeFilename) > 0 ) {
                $newFilename = $explodeFilename[0];
                $filename = strtolower($this->escapeFile($newFilename, 50));
            
                $string = $string . '.' . $explodeFilename[count($explodeFilename)-1];
            }
        }
          
        return $string;            
    }
    
}
                  

?>
