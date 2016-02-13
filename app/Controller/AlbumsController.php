<?php class AlbumsController extends AppController
{

    var $name    = 'Albums';
    var $uses     = array('Album', 'Image', 'Video', 'Event', 'StoreSlot', 'Organization');

    /**
     * Add new album
     * @author Oleg D.
     */
    function add($type, $albumModel, $albumModelID) 
    {

        $referer = '/';
        $userID = $this->getUserID();

        if (!$this->Album->getAlbumUploadAccess($userID, $albumModel, $albumModelID, $this->Access)) {
            $this->Session->setFlash('Access Error', 'flash_error');
            $this->redirect('/');
        }

        if (!empty($this->request->data)) {
            $referer = $this->request->data['Album']['referer'];
            unset($this->request->data['Album']['referer']);

            $this->request->data['Album']['user_id'] = $this->getUserID();
            $this->request->data['Album']['model'] = $albumModel;
            $this->request->data['Album']['model_id'] = $albumModelID;
            $this->request->data['Album']['content_type'] = $type;

            $this->Album->create();
            $this->Album->save($this->request->data['Album']);
            $albumID = $this->Album->getLastInsertID();
            //$this->Session->setFlash('New album has been saved', 'flash_success');
            if ($type == 'image') {
                $this->redirect('/submit/image/album/' . $albumID);
            } elseif ($type = 'video') {
                $this->redirect('/submit/video/album/' . $albumID);
            } else {
                return $this->redirect($referer);
            }
            /*
            if ($type == 'video') {
	            $this->redirect('/Albums/show_video/' . $albumID);
            }*/

        } else {
            if (!isset($_SERVER['HTTP_REFERER']) || !$_SERVER['HTTP_REFERER']) {
                $referer = '/';
            } else {
                $referer = $_SERVER['HTTP_REFERER'];
            }
        }
        $this->set('type', $type);
        $this->set('referer', $referer);
        $this->set('albumModel', $albumModel);
        $this->set('albumModelID', $albumModelID);
    }

    /**
     * Edit album
     * @author Oleg D.
     */
    function edit($id) 
    {
        $referer = '/';
        $userID = $this->getUserID();

        $this->Album->contain('Tag');
        $album = $this->Album->find('first', array('conditions' => array('id' => $id)));

        $this->Access->checkAccess('Album', 'u', $album['Album']['user_id']);

        if (!empty($this->request->data)) {
            $referer = $this->request->data['Album']['referer'];
            unset($this->request->data['Album']['referer']);

            $this->request->data['Album']['id'] = $id;
            $this->Album->save($this->request->data['Album']);

            $type = $this->Album->field('content_type', array('id' => $id));

            $this->Session->setFlash('Album has been saved', 'flash_success');
            //$this->redirect('/Albums/show_' . $type . '/' . $id);
            $this->redirect($referer);

        } else {
            if (!isset($_SERVER['HTTP_REFERER']) || !$_SERVER['HTTP_REFERER']) {
                $referer = '/';
            } else {
                $referer = $_SERVER['HTTP_REFERER'];
            }
        }
        $this->request->data = $album;
        $this->set('referer', $referer);
        $this->set('id', $id);
    }

    /**
     * Show images album
     * @author Oleg D.
     */
    function show_image($id) 
    {
        $userID = $this->getUserID();
        $this->Album->recursive = 1;
        $this->Album->contain('Tag', 'User');
        $album = $this->Album->find('first', array('conditions' => array('Album.id' => $id)));

        if (empty($album['Album']['id']) || $album['Album']['is_deleted']) {
            $this->Session->setFlash('Access Error', 'flash_error');
            return $this->redirect('/');
        }

        $myProfile = 0;
        if ($userID == $album['Album']['user_id']) {
            $myProfile = 1;
        }
        //pr($album);
        $allowChange = 0;
        $canUpload = $this->Album->getAlbumUploadAccess($userID, $album['Album']['model'], $album['Album']['model_id'], $this->Access);
        $allowChange = $this->Access->getAccess('Album', 'u', $album['Album']['user_id']);

        //$images = $this->Image->find('all', array('conditions' => array('Image.model_id' => $id, 'Image.model' => 'Album', 'Image.is_deleted' => 0), 'order' => array('id' => 'desc')));

        $imagePaginate['limit'] = 12;
        $imagePaginate['conditions'] = array('Image.model_id' => $id, 'Image.model' => 'Album', 'Image.is_deleted' => 0);
        $imagePaginate['order'] = array('id' => 'desc');

        $this->paginate = array('Image' => $imagePaginate);
        $images = $this->paginate('Image');

        if ($album['Album']['model'] != 'User') {
            $model = $this->{$album['Album']['model']}->find('first', array('conditions' => array('id' => $album['Album']['model_id'])));
        } else {
            $model['User'] = $album['User'];
        }

        $this->set('model', $model);

        // VOTES
        $imageAlbumVotes = $this->Album->Vote->getVotes('Album', $id, $this->getUserID());
           $this->set('imageAlbumVotes', $imageAlbumVotes);
        $this->set('canVoteSubmissions', $this->Access->returnAccess('Vote_Submissions', 'c'));
        // EOF VOTES

        // COMMENTS
        $this->set('comments', $this->Album->Comment->getCommentsTree('Album', $id));
        $this->set('commentVotes', $this->Image->Vote->getCommentVotes('Album', $id, $this->getUserID()));
           $this->set('canVoteComment', $this->Access->returnAccess('Vote_Comment', 'c'));
           $this->set('canComment', $this->Access->returnAccess('Comment_Blogpost', 'c'));
           $this->set('canDeleteComment', $this->Access->returnAccess('Comment', 'd'));
        // EOF COMMENTS

           $this->set('allowChange', $allowChange);
           $this->set('canUpload', $canUpload);
        $this->set('album', $album);
           $this->set('images', $images);
           $this->set('myProfile', $myProfile);

    }

    /**
     * Show videos album
     * @author Oleg D.
     */
    function show_video($id) 
    {
        $userID = $this->getUserID();
        $this->Album->recursive = 1;
        $this->Album->contain('Tag', 'User');
        $album = $this->Album->find('first', array('conditions' => array('Album.id' => $id)));

        if (empty($album['Album']['id']) || $album['Album']['is_deleted']) {
            $this->Session->setFlash('Access Error', 'flash_error');
            return $this->redirect('/');
        }

        $myProfile = 0;
        if ($userID == $album['Album']['user_id']) {
            $myProfile = 1;
        }
        //pr($album);
        $allowChange = 0;
        $canUpload = $this->Album->getAlbumUploadAccess($userID, $album['Album']['model'], $album['Album']['model_id'], $this->Access);
        $allowChange = $this->Access->getAccess('Album', 'u', $album['Album']['user_id']);

        $videoPaginate['limit'] = 12;
        $videoPaginate['conditions'] = array('Video.model_id' => $id, 'Video.model' => 'Album', 'Video.is_deleted' => 0);
        $videoPaginate['order'] = array('id' => 'desc');

        $this->paginate = array('Video' => $videoPaginate);
        $videos = $this->paginate('Video');

        if ($album['Album']['model'] != 'User') {
            $model = $this->{$album['Album']['model']}->find('first', array('conditions' => array('id' => $album['Album']['model_id'])));
        } else {
            $model['User'] = $album['User'];
        }

        $this->set('model', $model);

        // VOTES
        $videoAlbumVotes = $this->Album->Vote->getVotes('Album', $id, $this->getUserID());
           $this->set('videoAlbumVotes', $videoAlbumVotes);
        $this->set('canVoteSubmissions', $this->Access->returnAccess('Vote_Submissions', 'c'));
        // EOF VOTES

        // COMMENTS
        $this->set('comments', $this->Album->Comment->getCommentsTree('Album', $id));
        $this->set('commentVotes', $this->Image->Vote->getCommentVotes('Album', $id, $this->getUserID()));
           $this->set('canVoteComment', $this->Access->returnAccess('Vote_Comment', 'c'));
           $this->set('canComment', $this->Access->returnAccess('Comment_Blogpost', 'c'));
           $this->set('canDeleteComment', $this->Access->returnAccess('Comment_Blogpost', 'd'));
        // EOF COMMENTS
           $this->set('myProfile', $myProfile);
           $this->set('allowChange', $allowChange);
           $this->set('canUpload', $canUpload);
        $this->set('album', $album);
           $this->set('videos', $videos);
    }

    /**
     * Delete album
     * @author Oleg D.
     */
    function delete($id) 
    {
        if (!isset($_SERVER['HTTP_REFERER']) || !$_SERVER['HTTP_REFERER']) {
            $referer = '/';
        } else {
            $referer = $_SERVER['HTTP_REFERER'];
        }

           $userID = $this->getUserID();
           $album = $this->Album->find('first', array('conditions' => array('Album.id' => $id)));
           $this->Access->checkAccess('Album', 'd', $album['Album']['user_id']);
        $this->Album->deleteAlbum($id);
           $this->Session->setFlash('Album has been deleted', 'flash_success');

        $this->goHome();
    }
    /**
     * Create new Album from submissions page
     * @author Oleg D.
     */
    function create_from_submissions($type) 
    {
        $this->layout = false;
        Configure::write('debug', 0);
        $userID = $this->getUserID();
        $this->set('type', $type);

    }
    /**
     * Save new Album from submissions page
     * @author Oleg D.
     */
    function save_from_submissions($type) 
    {
        $this->layout = false;
        Configure::write('debug', 0);
        $userID = $this->getUserID();
        $album = $this->request->data['Album'];
        $album['model'] = 'User';
        $album['model_id'] = $userID;
        $album['content_type'] = $type;
        $album['user_id'] = $userID;

        $this->Album->save($album);
        $id = $this->Album->getLastInsertID();
        $this->set('id', $id);
        $this->set('album', $album);
    }

    /**
   * Show all user albums
   * $getAll - show all users albums
   * @author Oleg D.
   */
    function image_albums_list($model, $modelID, $getAll = 0) 
    {
        $loginUserID = $this->getUserID();
        $limit = 4;
        $allowChange = 0;
        $allowCreate = 0;

        if ($getAll && $modelID == $loginUserID && $model = 'User') {
            $allowCreate = 1;
        } else {
            $allowCreate = $this->Album->getAlbumUploadAccess($loginUserID, $model, $modelID, $this->Access, $getAll);
        }
        if ($allowCreate) {
            $limit = 3;
        }

        if ($this->Access->getAccess('Album', 'u')) {
            $allowChange = 1;
        }

        $conditions = array('Album.content_type' => 'image', 'Album.is_deleted' => 0);
        if (!$getAll) {
            $conditions['Album.model'] = $model;
            $conditions['Album.model_id'] = $modelID;
        } elseif ($model = 'User') {
            $conditions['Album.user_id'] = $modelID;
        } else {
            exit;
        }

        $this->Album->recursive = 1;
        $this->paginate = array(
        'Album' => array(
            'limit' => $limit,
         'contain' => array('CoverImage'),
            'order' => array('Album.created' => 'DESC'),
        'conditions' => $conditions
        ));
        $albums = $this->paginate('Album');

        // VOTES
        $albumsIDs = Set::combine($albums, '{n}.Album.id', '{n}.Album.id');
        $imageAlbumVotes = $this->Album->Vote->getVotes('Album', $albumsIDs, $this->getUserID());
        $this->set('imageAlbumVotes', $imageAlbumVotes);
        $this->set('canVoteSubmissions', $this->Access->returnAccess('Vote_Submissions', 'c'));
        // EOF VOTES

        $this->set('albums', $albums);
        $this->set('model', $model);
        $this->set('modelID', $modelID);
        $this->set('getAll', $getAll);
        $this->set('allowChange', $allowChange);
        $this->set('allowCreate', $allowCreate);
        $this->set('loginUserID', $loginUserID);
        $this->render();
    }
    /**
   * Show all user albums
   * @author Oleg D.
   */
    function video_albums_list($model, $modelID, $getAll = 0) 
    {
        $loginUserID = $this->getUserID();
        $limit = 4;
        $allowChange = 0;
        $allowCreate = 0;

        if ($getAll && $modelID == $loginUserID && $model = 'User') {
            $allowCreate = 1;
        } else {
            $allowCreate = $this->Album->getAlbumUploadAccess($loginUserID, $model, $modelID, $this->Access, $getAll);
        }
        if ($allowCreate) {
            $limit = 3;
        }

        if ($this->Access->getAccess('Album', 'u')) {
            $allowChange = 1;
        }

        $conditions = array('Album.content_type' => 'video', 'Album.is_deleted' => 0);
        if (!$getAll) {
            $conditions['Album.model'] = $model;
            $conditions['Album.model_id'] = $modelID;
        } elseif ($model = 'User') {
            $conditions['Album.user_id'] = $modelID;
        } else {
            exit;
        }

        $this->Album->recursive = 1;
        $this->paginate = array(
        'Album' => array(
            'limit' => $limit,
         'contain' => array('CoverVideo'),
            'order' => array('Album.created' => 'DESC'),
        'conditions' => $conditions
        ));
        $albums = $this->paginate('Album');

        // VOTES
        $albumsIDs = Set::combine($albums, '{n}.Album.id', '{n}.Album.id');
        $videoAlbumVotes = $this->Album->Vote->getVotes('Album', $albumsIDs, $this->getUserID());
        $this->set('videoAlbumVotes', $videoAlbumVotes);
        $this->set('canVoteSubmissions', $this->Access->returnAccess('Vote_Submissions', 'c'));
        // EOF VOTES

        $this->set('albums', $albums);
        $this->set('model', $model);
        $this->set('modelID', $modelID);
        $this->set('getAll', $getAll);
        $this->set('allowChange', $allowChange);
        $this->set('allowCreate', $allowCreate);
        $this->set('loginUserID', $loginUserID);
        $this->render();
    }
    /**
     * Enter description here ...
     * @param $albumID
     * @param $imageID
     */
    function change_cover($albumID, $imageID) 
    {
        $album = $this->Album->find('first', array('conditions' => array('id' => $albumID)));
        $image = $this->Image->find('first', array('conditions' => array('id' => $imageID)));

        $this->Access->checkAccess('Album', 'u', $album['Album']['user_id']);
        if ($image['Image']['model'] = 'Album' && $image['Image']['model_id'] == $albumID) {
                $this->Album->save(array('id' => $albumID, 'cover_image_id' => $imageID));
                $this->Session->setFlash('Albums cover has been changed', 'flash_success');
        }

        return $this->redirect('/Images/albumShow/' . $imageID);
    }


}
?>