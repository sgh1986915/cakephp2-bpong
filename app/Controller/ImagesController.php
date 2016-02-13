<?php class ImagesController extends AppController
{

    var $name    = 'Images';
    var $helpers = array('Html', 'Form');
    var $uses     = array('Image');
    /*
    var $albumsImages = array(
	    'User' => array(
        	'thumbs' => array('create'=>true,'width'=>'120','height'=>'120','bgcolor'=>'#FFFFFF'),
        	'versions' => array(
        	            'middle'=>array('width'=>'260','height'=>'260','bgcolor'=>'#FFFFFF')
                        )
            )
    );
    */

    /**
* 
     * Edit Image
     * @author Oleg D.
     */
    function edit($id = null)
    {

        $this->Image->recursive = -1;
        $image = $this->Image->find('first', array('conditions' => array('id' => $id)));
        $modelName = $image['Image']['model'];
        $this->Access->checkAccess('Images' . $modelName, 'u');
        $show_attributes = $this->Access->getAccess('ImageAttributes' . $modelName, 'u');

        if (empty($this->request->data)) {
            if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']) {
                $back_url = $_SERVER['HTTP_REFERER'];
            } else {
                $back_url = '/';
            }
            $this->request->data = $image;
        } else {
            $back_url = $this->request->data['Form']['back_url'];

            // If upload file
            if ($this->request->data['Image'][$id]['size'] > 0) {
                $ImagesModel = ClassRegistry::init($modelName);
                $this->request->data[$modelName]['id'] = $image['Image']['model_id'];
                if ($ImagesModel->save($this->request->data)) {
                    $this->Session->setFlash(__('Image has been updated'), 'flash_success');
                }
            } else {
                $img['Image']['id'] = $id;
                $img['Image']['title'] = $this->request->data['Image'][$id]['title'];
                $img['Image']['alt'] = $this->request->data['Image'][$id]['alt'];
                $img['Image']['description'] = $this->request->data['Image'][$id]['description'];

                if ($this->Image->save($img)) {

                    $this->Session->setFlash(__('Image has been updated'), 'flash_success');
                }
            }

            return $this->redirect($back_url);
        }
        //$this->Access->checkAccess('Image' . $modelName, 'u' );

        $this->set('show_attributes', $show_attributes);
        $this->set('back_url', $back_url);
    }
    /**
* 
     * Delete Image
     * @author Oleg D.
     */
    function delete($id = null)
    {

        $this->Image->recursive = -1;
        $image = $this->Image->find('first', array('conditions' => array('id' => $id)));
        $modelName = $image['Image']['model'];
        $this->Access->checkAccess('Images' . $modelName, 'd');

        if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']) {
            $back_url = $_SERVER['HTTP_REFERER'];
        } else {
            $back_url = '/';
        }

        //$this->request->data['Image']['id']         = $id;
        //$this->request->data['Image']['deleted']    = date('Y-m-d H:i:s');
        //$this->request->data['Image']['is_deleted'] = 1;

        if ($this->Image->delete($id)) {
            $this->Session->setFlash(__('Image has been deleted'), 'flash_success');
        }
        if ($modelName == 'Album' || $modelName == 'Image') {
            $this->goHome();
        } else {
            return $this->redirect($back_url);
        }
        exit;
    }

    /**
     * Add Images to album
     * @author Oleg D.
     */
    function albumAdd($albumID = null)
    {
        $userID = $this->getUserID();

        $album = $this->Image->Album->find('first', array('conditions' => array('id' => $albumID)));
        $album = $album['Album'];

        $model = $album['model'];
        $modelID = $album['model_id'];

        //check access
        if (!$this->Image->Album->getAlbumUploadAccess($userID, $album['model'], $album['model_id'])) {
            $this->Session->setFlash('Access Error', 'flash_error');
            $this->redirect('/');
        }
        $imagesList = '';
        if (!empty($this->request->data)) {
            foreach ($this->request->data['Image'] as $image) {
                if ($image['name'] && $image['size']) {
                    $imageID = $this->Image->saveToAlbum($image, $albumID, $userID, 0, 1, $model, $album['model']);
                    $imagesList .= $imageID . '_';
                }
            }
            Cache::delete('last_images');
            if ($imagesList) {
                $imagesList = substr($imagesList, 0, -1);
                $this->redirect('/Images/albumAddFinish/' . $albumID . '/' . $imagesList);
            } else {
                $this->Session->setFlash('Please select Image', 'flash_error');
            }
        }

        $this->set('album', $album);
    }

    /**
     * Finish Add Images to album
     * @author Oleg D.
     */
    function albumAddFinish($albumID, $imagesList = '')
    {
        $userID = $this->getUserID();
        if (!empty($this->request->data)) {

            $coverImageID = 0;
            if (isset($this->request->data['cover'])) {
                $coverImageID = $this->request->data['cover'];
            }
            $i=0;
            foreach ($this->request->data['descriptions'] as $imageID => $description) {
                $image = array();
                $image['id'] = $imageID;
                $image['is_deleted'] = 0;
                $image['description'] = trim($description);
                $this->Image->save($image);
                $i++;
            }

            $this->Image->Album->changeFilesNum($albumID, $i);
            if ($coverImageID) {
                $saveAlbum['id'] = $albumID;
                $saveAlbum['cover_image_id'] = $coverImageID;
                $this->Image->Album->save($saveAlbum);
            }
            Cache::delete('last_images');
            $this->redirect('/Albums/show_image/' . $albumID);
            exit;
        } else {
            $images = explode('_', $imagesList);
            if (!empty($images)) {

                $album = $this->Image->Album->find('first', array('conditions' => array('id' => $albumID)));
                if (!$this->Image->Album->getAlbumUploadAccess($userID, $album['Album']['model'], $album['Album']['model_id'])) {
                    $this->Session->setFlash('Access Error', 'flash_error');
                    $this->redirect('/');
                }
                $conditions['model'] =  'Album';
                $conditions['model_id'] =  $albumID;
                $conditions['id'] =  $images;

                $images = $this->Image->find('all', array('conditions' => $conditions));
                $this->set('album', $album);
                $this->set('images', $images);
            } else {
                exit;
            }
        }
    }

    /**
     * Show Image  from album
     * @author Oleg D.
     */
    function albumShow($id = null) 
    {

        $userID = $this->getUserID();
        $this->Image->changeViews($id);
        $this->Image->contain('Tag', 'User');
        $image = $this->Image->find('first', array('conditions' => array('Image.id' => $id)));

        $isAuthor = 0;
        if ($userID == $image['Image']['user_id']) {
            $isAuthor = 1;
        }

        $allowUpdate = $this->Access->getAccess('Image', 'u', $image['Image']['user_id']);
        $allowDelete = $this->Access->getAccess('Image', 'd', $image['Image']['user_id']);

        $album = $this->Image->Album->find('first', array('conditions' => array('Album.id' => $image['Image']['model_id'])));
        if (!empty($album['Album']['content_type']) && $album['Album']['content_type'] == 'junk') {
            $isAlbumJunk = 1;
        } else {
            $isAlbumJunk = 0;
        }
        $nextImage = 0;
        if ($album['Album']['files_num'] > 0) {
            $imagePaging = $this->Image->albumImagePaging($id, $image['Image']['model_id']);
        } else {
            $imagePaging['count'] = $album['Album']['files_num'];
            $imagePaging['nextID'] = 0;
            $imagePaging['pageNum'] = 1;
        }
        // VOTES
        $imageVotes = $this->Image->Vote->getVotes('Image', $id, $this->getUserID());
        $this->set('imageVotes', $imageVotes);
        $this->set('canVoteSubmissions', $this->Access->returnAccess('Vote_Submissions', 'c'));
        // EOF VOTES

        // COMMENTS
        $this->set('comments', $this->Image->Comment->getCommentsTree('Image', $id));
        $this->set('commentVotes', $this->Image->Vote->getCommentVotes('Image', $id, $this->getUserID()));
        $this->set('canVoteComment', $this->Access->returnAccess('Vote_Comment', 'c'));
        $this->set('canComment', $this->Access->returnAccess('Comment_Blogpost', 'c'));
        $this->set('canDeleteComment', $this->Access->returnAccess('Comment', 'd'));
        // EOF COMMENTS

        $this->set('rels_images', array(STATIC_BPONG.'/img/albums/' . $image['Image']['user_id']. '/thumb/' . $image['Image']['filename']));
        if ($image['Image']['description']) {
            $this->set('meta_description', $image['Image']['description']);
        }
                
        $this->set('isAuthor', $isAuthor);
        $this->set('imagePaging', $imagePaging);
        $this->set('allowDelete', $allowDelete);
        $this->set('allowUpdate', $allowUpdate);
        $this->set('album', $album);
        $this->set('img', $image);
        $this->set('isAlbumJunk', $isAlbumJunk);

    }

    /**
     * Edit Image from album
     * @author Oleg D.
     */
    function albumEdit($id = null) 
    {
        $this->Image->contain('Tag');
        $image = $this->Image->find('first', array('conditions' => array('id' => $id)));
        $albumID = $image['Image']['model_id'];
        $albumCoverID = $this->Image->Album->field('cover_image_id', array('id' => $albumID));
        $this->Access->checkAccess('Image', 'u', $image['Image']['user_id']);

        if (!empty($this->request->data)) {
            if (isset($this->request->data['Image']['is_cover']) && $this->request->data['Image']['is_cover']) {
                    $this->Image->Album->save(array('cover_image_id' => $id, 'id' => $albumID));
            } else {
                if ($albumCoverID == $id) {
                    $this->Image->Album->save(array('cover_image_id' => 0, 'id' => $albumID));
                }
            }
            $this->request->data['Image']['id'] = $id;
            $this->Image->save($this->request->data);
            Cache::delete('last_images');

            $this->Session->setFlash('Image has been updated', 'flash_success');
            $this->redirect('/Images/albumShow/' . $id);
        }
        $isCover = false;
        if ($albumCoverID == $id) {
            $isCover = 'checked';
        }
        $this->request->data = $image;
        $this->set('isCover', $isCover);
    }

    /**
     * Delete Image from album
     * @author Oleg D.
     */
    function albumDelete($id = null) 
    {
        $image = $this->Image->find('first', array('conditions' => array('id' => $id)));

        if (empty($image['Image']['id']) || $image['Image']['is_deleted']) {
            $this->Session->setFlash('Access Error', 'flash_error');
            return $this->redirect('/');
        }

        $this->Access->checkAccess('Image', 'd', $image['Image']['user_id']);

        $this->Image->delete($id);
        Cache::delete('last_images');
        $this->Image->Album->changeFilesNum($image['Image']['model_id'], -1);
        // if is album cover delete it from album cover
        $albumCoverID = $this->Image->Album->field('cover_image_id', array('id' => $image['Image']['model_id']));
        if ($albumCoverID == $id) {
            $newCoverID = intval($this->Image->field('id', array('model' => 'Album', 'model_id' => $image['Image']['model_id'], 'is_deleted' => 0)));
            $this->Image->Album->save(array('cover_image_id' => $newCoverID, 'id' => $image['Image']['model_id']));
        }
        $this->Session->setFlash('Image has been deleted', 'flash_success');
        if ($image['Image']['model_id']) {
            return $this->redirect('/Albums/show_image/' . $image['Image']['model_id']);
        }
        $this->goHome();

    }

    /**
     * show albums tags
     * @author Oleg D.
     */
    function Tag($tag) 
    {
        $modelName = 'Image';

        $tagID = $this->Image->Tag->field('id', array('Tag.tag' => $tag, 'Tag.model' => $modelName));
        $this->Image->ModelsTag->bindModel(
            array('belongsTo' => array($modelName => array('className' => $modelName,
                                'foreignKey' => 'model_id',
                                'conditions' => '',
                                'fields' => '',
                                'order' => ''
            ))), false
        );
        $contain = array($modelName, /*$modelName . '.Tag',*/ $modelName . '.User');
        $conditions = array('ModelsTag.tag_id' => $tagID, 'ModelsTag.model' => $modelName);

        $this->paginate = array(
            'limit' => 10,
            'contain' => $contain,
            'order' => array('ModelsTag.created' => 'DESC'),
            'conditions' => $conditions
        );

        $objects = $this->paginate('ModelsTag');
        //pr($objects); exit();

        $this->set('tag', $tag);
        $this->set('objects', $objects);
        $this->set('modelName', $modelName);

    }
    /**
     *
     * @author Oleg D.
     */
    function saveFromSubmissions() 
    {
        $this->layout = false;
        Configure::write('debug', 0);
        $albumID = intval($_REQUEST['param2']);
        $imageID = 0;
        $userID = $_REQUEST['param1'];
        $image = $_FILES['Filedata'];
        if ($image['name'] && $image['size'] ) {
            if ($this->request->data['User']['avatar']['size'] > 1100000) {
                exit('er1');
            } else {
                $imageID = $this->Image->saveToAlbum($image, $albumID, $userID, 0, 1);
            }
        }
        if (!$imageID) {
            $imageID = 0;
        }
        exit($imageID);
    }
    /**
     * Submit Images from submissions
     * @author Oleg D.
     */
    function submissionsAdd() 
    {
        $userID = $this->getUserID();
        if (isset($this->request->data['Images'])) {
            $forJunk = 0;
            $albumID  = $this->request->data['Images']['album_id'];
            $images  = $this->request->data['all_images'];
            $coverImageID = 0;
            if (isset($this->request->data['Images']['cover_id'])) {
                $coverImageID = $this->request->data['Images']['cover_id'];
            }
            if (!$albumID) {
                $albumID = $this->Image->Album->getJunkId($userID);
                $forJunk = 1;
            }
            $imageNum = 0;
            foreach ($images as $imageID => $image) {
                $saveImage = array();
                $saveImage['id'] = $imageID;
                $saveImage['deleted'] = null;
                $saveImage['is_deleted'] = 0;
                $saveImage['model_id'] = $albumID;
                $saveImage['name'] = $image['name'];
                $saveImage['description'] = $image['description'];
                $saveImage['tags'] = $image['tags'];
                $this->Image->save($saveImage);
                if ($coverImageID) {
                    $saveAlbum['id'] = $albumID;
                    $saveAlbum['cover_image_id'] = $coverImageID;
                    $this->Image->Album->save($saveAlbum);
                }
                $imageNum++;
            }
            if (!$forJunk && !$coverImageID && !$this->Image->Album->field('cover_image_id', array('Album.id' => $albumID))) {
                $this->Image->Album->save(array('id' => $albumID, 'cover_image_id' => $imageID));
            }
            $this->Image->Album->changeFilesNum($albumID, $imageNum);
        }
        Cache::delete('last_images');
        $this->redirect('/submissions/finish/' . $albumID);
    }

    /**
     * Show all images of user
     * @author Oleg D.
     */
    function users_all($userID) 
    {
        $model = 'Image';
        $isAuthor = 0;
        if ($userID == $this->getUserID()) {
            $isAuthor = 1;
        }
        $user = $this->Image->User->read(null, $userID);
        $limit = 10;

        $this->paginate = array(
            'limit' => $limit,
            'contain' => array('User', 'Album'),
            'order' => array($model . '.created' => 'DESC'),
            'conditions' => array($model . '.user_id' => $userID, $model . '.is_deleted' => 0, $model . '.model' => 'Album')
        );

        $items = $this->paginate($model);
        $itemIDs = Set::combine($items, '{n}.' . $model . '.id', '{n}.' . $model . '.id');

        $this->Image->ModelsTag->contain(array('Tag'));
        $allTags = $this->Image->ModelsTag->find('all', array('conditions' => array('ModelsTag.model' => $model, 'ModelsTag.model_id' => $itemIDs)));
        $itemsTags = array();
        foreach ($allTags as $allTag) {
            $itemsTags[$allTag['ModelsTag']['model_id']][] = $allTag['Tag'];
        }

        $votes = $this->Image->Vote->getVotes($model, $itemIDs, $this->getUserID());

        $this->set('itemsTags', $itemsTags);
        $this->set('canVoteSubmissions', $this->Access->returnAccess('Vote_Submissions', 'c'));
        $this->set('votes', $votes);
        $this->set('model', $model);
        $this->set('limit', $limit);
        $this->set('items', $items);
        $this->set('user', $user);
        $this->set('isAuthor', $isAuthor);
    }
    
    /**
     * AJAX change images order
     * @author Oleg D.
     */
    function ajaxChangeOrder() 
    {
        Configure::write('debug', 0);
        $images = explode(',', trim($_POST['images_order']));

        $i = 1;

        foreach ($images as $key => $value) {
            $id = intval(str_replace('img_', '', $value));
            if ($id) {
                $image= array();
                $image['id'] = $id;
                $image['order_view'] = $i;
                $this->Image->save($image);

                $i++;
            }
        }
        exit();
    }
}
?>