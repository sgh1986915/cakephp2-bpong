<?php
class VideosController extends AppController
{

    var $name = 'Videos';
    var $uses = array('Video');
    var $helpers = array('Youtube');

    /**
     * Add videos from submissions
     * @author Oleg D.
     */
    function submissionsAdd() 
    {
        $userID = $this->getUserID();
        if (isset($this->request->data['Video'])) {
            $albumID  = $this->request->data['Video']['album_id'];
            if (!$albumID) {
                $albumID = $this->Video->Album->getJunkId($userID);
            }
            App::import('Vendor', 'YouTube', array('file' => 'class.YouTube.php'));
            $VideoTube = new YouTube();
            $youTubeID = $VideoTube->getEmbedID($this->request->data['Video']['code']);  // save ID!!!
            if (!$youTubeID) {
                //$this->Session->setFlash('YouTube Video error!');
                //$this->redirect('/submit/');
            }
            $video = array();
            $video['deleted'] = null;
            if ($youTubeID) {
                $video['youtube_id'] = $youTubeID;
            } else {
                $video['is_processed'] = 1;

            }
            $video['user_id'] = $userID;
            $video['is_deleted'] = 0;
            $video['model_id'] = $albumID;
            $video['model'] = 'Album';
            $video['description'] = $this->request->data['Video']['description'];
            $video['code'] = $this->request->data['Video']['code'];
            $this->Video->create();
            $this->Video->save($video);
            $videoID = $this->Video->getLastInsertID();
            //if ($this->Video->Album->field('cover_video_id', array('id' => $albumID))) {
            $this->Video->Album->save(array('id' => $albumID, 'cover_video_id' => $videoID));
            //}
            Cache::delete('last_videos');
            $this->Video->Album->changeFilesNum($albumID, 1);
        }
        $this->redirect('/submissions/finish/' . $albumID . '/video');
    }

       /**
     * Show Videos
     * @author Oleg D.
     */
    function show($id) 
    {
        $userID = $this->getUserID();

        $this->Video->changeViews($id);
        $this->Video->contain('User', 'Tag');
        $video = $this->Video->find('first', array('conditions' => array('Video.id' => $id)));

        if (empty($video['Video']['id']) || $video['Video']['is_deleted']) {
            $this->Session->setFlash('Access Error', 'flash_error');
            return $this->redirect('/');
        }

        $isAuthor = 0;
        if ($userID == $video['Video']['user_id']) {
            $isAuthor = 1;
        }

        $allowUpdate = $this->Access->getAccess('Video', 'u', $video['Video']['user_id']);
        $allowDelete = $this->Access->getAccess('Video', 'd', $video['Video']['user_id']);

        $album = $this->Video->Album->find('first', array('conditions' => array('Album.id' => $video['Video']['model_id'])));
        if (!empty($album['Album']['content_type']) && $album['Album']['content_type'] == 'junk') {
            $isAlbumJunk = 1;
        } else {
            $isAlbumJunk = 0;
        }
        if ($album['Album']['files_num'] > 0) {
            $videoPaging = $this->Video->albumVideoPaging($id, $video['Video']['model_id']);
        } else {
            $videoPaging['count'] = $album['Album']['files_num'];
            $videoPaging['nextID'] = 0;
            $videoPaging['pageNum'] = 1;
        }

        // VOTES
        $videoVotes = $this->Video->Vote->getVotes('Video', $id, $this->getUserID());
        $this->set('videoVotes', $videoVotes);
        $this->set('canVoteSubmissions', $this->Access->returnAccess('Vote_Submissions', 'c'));
        // EOF VOTES

        // COMMENTS
        $this->set('comments', $this->Video->Comment->getCommentsTree('Video', $id));
        $this->set('commentVotes', $this->Video->Vote->getCommentVotes('Video', $id, $this->getUserID()));
        $this->set('canVoteComment', $this->Access->returnAccess('Vote_Comment', 'c'));
        $this->set('canComment', $this->Access->returnAccess('Comment_Blogpost', 'c'));
        $this->set('canDeleteComment', $this->Access->returnAccess('Comment', 'd'));
        // EOF COMMENTS

        $this->set('rels_images', array($this->Video->getVideoImage($video['Video']['youtube_id'])));
        if ($video['Video']) {
            $this->set('meta_description', $video['Video']['description']);
        }
        $this->set('videoPaging', $videoPaging);
        $this->set('album', $album);
        $this->set('allowDelete', $allowDelete);
        $this->set('allowUpdate', $allowUpdate);
        $this->set('isAuthor', $isAuthor);
        $this->set('video', $video);
        $this->set('isAlbumJunk', $isAlbumJunk);
    }

       /**
     * Delete Video
     * @author Oleg D.
     */
    function delete($id = null)
    {
        //Configure::write('debug', '1');
        $userID = $this->getUserID();
        $video = $this->Video->read(null, $id);
        $this->Access->checkAccess('Video', 'd', $video['Video']['user_id']);
        if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']) {
            $back_url = $_SERVER['HTTP_REFERER'];
        } else {
            $back_url = '/';
        }

        if ($this->Video->find('count', array('conditions' => array('id' => $id, 'is_deleted' => 0)))) {
            $this->Video->delete($id);
            $video = $this->Video->read(null, $id);
            $albumID = $video['Video']['model_id'];
            $this->Video->Album->changeFilesNum($albumID, -1);
            if ($video['Video']['is_file']) {
                App::import('Vendor', 'YouTube', array('file' => 'class.YouTube.php'));
                $VideoTube = new YouTube();
                $result = $VideoTube->deleteVideo($video['Video']['youtube_id']);
            }

            // if is album cover delete it from album cover
            $albumCoverID = $this->Video->Album->field('cover_video_id', array('id' => $video['Video']['model_id']));
            if ($albumCoverID == $id) {
                $newCoverID = intval($this->Video->field('id', array('model' => 'Album', 'model_id' => $video['Video']['model_id'], 'is_deleted' => 0)));
                $this->Video->Album->save(array('cover_video_id' => $newCoverID, 'id' => $video['Video']['model_id']));
            }
            Cache::delete('last_videos');
            $this->Session->setFlash('Video has been deleted', 'flash_success');
            if ($video['Video']['model_id']) {
                   return $this->redirect('/Albums/show_video/' . $video['Video']['model_id']);
            }
        } else {
            $this->Session->setFlash(__('There is no such video'), 'flash_error');
        }

        $this->goHome();
        exit;
    }

    /**
     * Edit Video
     * @author Oleg D.
     */
    function edit($id = null)
    {
        $userID = $this->getUserID();
        $video = $this->Video->read(null, $id);
        $this->Access->checkAccess('Video', 'u', $video['Video']['user_id']);

        if (!empty($this->request->data)) {
            if (!empty($this->request->data['Video']['code'])) {
                App::import('Vendor', 'YouTube', array('file' => 'class.YouTube.php'));
                $VideoTube = new YouTube();
                $this->request->data['Video']['youtube_id'] = $VideoTube->getEmbedID($this->request->data['Video']['code']);  // save ID!!!
            }
            $this->Video->save($this->request->data);
            Cache::delete('last_videos');
            $this->redirect('/videos/show/' . $this->request->data['Video']['id']);
        } else {
            $this->Video->contain('Tag');
            $this->request->data = $video;
        }


    }

    function ajaxGetYoutubeUrl() 
    {
        Configure::write('debug', 0);
        $this->layout   = false;
        $userID = $this->getUserID();
        App::import('Vendor', 'YouTube', array('file' => 'class.YouTube.php'));
        $VideoTube = new YouTube();
        $title = $description = '';

        if (!empty($_REQUEST['title'])) {
            $title = trim($_REQUEST['title']);
        }
        if (!empty($_REQUEST['description'])) {
            $description = trim($_REQUEST['description']);
        }

        $youTubeUrl = $VideoTube->getUploadUrl($title, $description);

        $albumID = intval($_REQUEST['album_id']);
        if (!$albumID) {
            $albumID = $this->Video->Album->getJunkId($userID);
        }

        $newVideo = array();
        $newVideo['model'] = 'Album';
        $newVideo['model_id'] = $albumID;
        $newVideo['user_id'] = $this->getUserID();;
        $newVideo['title'] = $title;
        $newVideo['description'] = $description;
        $newVideo['is_processed'] = 0;
        $newVideo['is_deleted'] = 1;
        $newVideo['is_file'] = 1;
        $newVideo['tags'] = $_REQUEST['tags'];

        if (!empty($youTubeUrl)) {
            $this->Video->create();
            $this->Video->save($newVideo);
            $id = $this->Video->getLastInsertID();
            $youTubeUrl['url'] .= '?nexturl=http://' . $_SERVER['HTTP_HOST'] . '/videos/youTubeUploaderBack/' . $id . '/';
            $youTubeUrl['id'] = $id;
            exit($this->Json->encode($youTubeUrl));

        } else {
            exit;
        }
    }

    /**
     * Back url for youtube uploader
     * @author Oleg D.
     */
    function youTubeUploaderBack($videoID) 
    {
        $youTubeStatus = $youTubeID = '';
        if (!empty($_REQUEST['status'])) {
            $youTubeStatus = $_REQUEST['status'];
        }
        if (!empty($_REQUEST['id'])) {
            $youTubeID = $_REQUEST['id'];
        }

        $video['id'] = $videoID;
        $video['youtube_id'] = $youTubeID;
        $video['uploaded'] = date("Y-m-d H:i:s");
        if ($youTubeID) {
            $video['is_deleted'] = 0;
        }

        $albumID = $this->Video->field('model_id', array('id' => $videoID));
        if (empty($youTubeID)) {
            $this->Video->save(array('id' => $videoID, 'is_deleted' => 1));
            $this->Session->setFlash('Some error occurred on the YouTube side', 'flash_error');
            $this->redirect('/');
        }
        $this->Video->save($video);
        $this->Video->Album->changeFilesNum($albumID, 1);
        $this->Video->Album->save(array('id' => $albumID, 'cover_video_id' => $videoID));

        $this->redirect('/submissions/finish/' . $albumID . '/video');
    }

    /**
     * CRON update video process status
     * @author Oleg D.
     */
    function cronUpdateVideoStatus() 
    {
        Configure::write('debug', 1);
        // set is_processed for all videos who uploaded mor then 15 minutes ago
        $sql = "UPDATE videos SET  is_processed = 1 WHERE uploaded AND is_processed = 0  AND uploaded < date_add(now(), interval -25 minute)";
        $this->Video->query($sql);

        App::import('Vendor', 'XmlParser', array('file' => 'xmlparser.class.php'));
        $XmlParser = new XmlParser();
        $videos = $this->Video->find('all', array('conditions' => array('is_processed' => 0, 'is_deleted' => 0, 'is_file' => 1, 'youtube_id is not null')));
        //pr($videos);
        foreach ($videos as $video) {
            if ($video['Video']['youtube_id']) {
                $content = $this->file_get_contents_curl('http://gdata.youtube.com/feeds/api/videos/' . $video['Video']['youtube_id']);
                $ansverArray = $XmlParser->xml2array($content);

                if (!empty($ansverArray['entry'])) {
                    if (!empty($ansverArray['entry']['app:control']['yt:state_attr'])) {
                        //echo "processing";
                        echo $video['Video']['youtube_id'] . ' - ' . 'processing<br/>';
                    } else {
                        //echo "processing finished";
                        $this->Video->save(array('id' => $video['Video']['id'], 'is_processed' => 1));
                        echo $video['Video']['youtube_id'] . ' - ' . 'processed<br/>';
                    }
                }
            }
        }
        Cache::delete('last_videos');
        exit;
    }
    /**
     * CRON upload video to archive
     * @author Oleg D.
     */
    function cronUploadVideosToArchive() 
    {
        Configure::write('debug', 1);
        ini_set('mysql.connect_timeout', '6000');
        set_time_limit(6000);

        App::import('Vendor', 'YouTube', array('file' => 'class.YouTube.php'));
        $VideoTube = new YouTube();

        $videos = $this->Video->find('all', array('conditions' => array('is_downloaded' => '0', 'is_file' => 1, 'is_processed' => 1, 'is_deleted' => 0), 'limit' => 2));
        if (!empty($videos)) {
            foreach ($videos as $video) {
                if ($video['Video']['youtube_id']) {
                    $filename = '../webroot/video_archive/' . $video['Video']['youtube_id'];
                    $VideoTube->download_video($video['Video']['youtube_id'], $filename);
                    if (file_exists($filename . '.flv')) {
                        $this->Video->save(array('id' => $video['Video']['id'], 'is_downloaded' => 1));

                        echo $video['Video']['youtube_id'] . " - downloaded";
                        echo "<br/>";
                    }
                }
            }
        }
        exit;
    }
    function test() 
    {
            App::import('Vendor', 'YouTube', array('file' => 'class.YouTube.php'));
            $VideoTube = new YouTube();
            echo $youTubeID = $VideoTube->getEmbedID($this->Video->field('code', array('id' => 60)));  // save ID!!!
            exit;
    }

    /**
     * Show all videos of user
     * @author Oleg D.
     */
    function users_all($userID) 
    {
        $model = 'Video';
        $isAuthor = 0;
        if ($userID == $this->getUserID()) {
            $isAuthor = 1;
        }
        $user = $this->Video->User->read(null, $userID);
        $limit = 10;

        $this->paginate = array(
            'limit' => $limit,
            'contain' => array('User', 'Album'),
            'order' => array($model . '.created' => 'DESC'),
        'fields' => array('*', 'youtube_id as filename'),
            'conditions' => array($model . '.user_id' => $userID, $model . '.is_deleted' => 0, $model . '.model' => 'Album')
        );

        $items = $this->paginate($model);
        $itemIDs = Set::combine($items, '{n}.' . $model . '.id', '{n}.' . $model . '.id');

        $this->Video->ModelsTag->contain(array('Tag'));
        $allTags = $this->Video->ModelsTag->find('all', array('conditions' => array('ModelsTag.model' => $model, 'ModelsTag.model_id' => $itemIDs)));
        $itemsTags = array();
        foreach ($allTags as $allTag) {
            $itemsTags[$allTag['ModelsTag']['model_id']][] = $allTag['Tag'];
        }
        $votes = $this->Video->Vote->getVotes($model, $itemIDs, $this->getUserID());

        $this->set('itemsTags', $itemsTags);
        $this->set('canVoteSubmissions', $this->Access->returnAccess('Vote_Submissions', 'c'));
        $this->set('votes', $votes);
        $this->set('model', $model);
        $this->set('limit', $limit);
        $this->set('items', $items);
        $this->set('user', $user);
        $this->set('isAuthor', $isAuthor);
    }
}
?>
