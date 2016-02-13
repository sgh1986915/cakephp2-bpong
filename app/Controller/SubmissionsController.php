<?php
class SubmissionsController extends AppController
{

    var $name = 'Submissions';
    var $uses = array('Submission', 'Album', 'Image', 'Video','Link');

    
    /**
     * Submission Index Page
     * @author Oleg D.
     */
    function index($contentType = null, $arg1 = null, $arg2 = null) 
    {
        $this->noCache();        $userID = $this->getUserID();
        
        $this->Access->checkAccess('Submission', 'c');  
              
        switch ($contentType) {
        case 'image' : 
            if ($arg1 == 'album') {
                $userID = $this->getUserID();
                //$albums = $this->Album->find('list', array('conditions' => array('user_id' => $userID, 'is_deleted' => 0, 'content_type' => $contentType), 'fields' => array('id', 'name')));          
                $album = $this->Album->read(null, $arg2);
                $albums[$album['Album']['id']] = $album['Album']['name'];
                    
                if (!$this->Album->getAlbumUploadAccess($userID, $album['Album']['model'], $album['Album']['model_id'], $this->Access)) {
                    $this->Session->setFlash('Access Error', 'flash_error');
                    return $this->redirect('/');                          
                }
                                        
                $this->set('album_id', $arg2);                 
                $this->set('albums', $albums);  
                if ($album['Album']['content_type'] == 'junk') {
                    $this->request->data['albumType'] = 'itself';
                } else {
                    $this->request->data['albumType'] = 'existing';
                }                  
                $this->set('album', $albums);                    
            }            
            break;    
        case 'video' : 
            if ($arg1 == 'album') {
                $userID = $this->getUserID();
                //$albums = $this->Album->find('list', array('conditions' => array('user_id' => $userID, 'is_deleted' => 0, 'content_type' => $contentType), 'fields' => array('id', 'name')));
                $album = $this->Album->read(null, $arg2);
                $albums[$album['Album']['id']] = $album['Album']['name'];
                                      
                if (!$this->Album->getAlbumUploadAccess($userID, $album['Album']['model'], $album['Album']['model_id'], $this->Access)) {
                    $this->Session->setFlash('Access Error', 'flash_error');
                    return $this->redirect('/');                          
                }
                                        
                if ($album['Album']['content_type'] == 'junk') {
                    $this->request->data['albumType'] = 'itself';
                } else {
                    $this->request->data['albumType'] = 'existing';
                }       
                $this->set('album_id', $arg2);                 
                $this->set('albums', $albums);                                                  
                $this->set('album_id', $arg2);                 
                $this->set('albums', $albums);                                  
                $this->set('album', $albums);                    
            }            
            break;              
        }        
        $this->set('contentType', $contentType);            
    }
    /**
     * Image Upload
     * @author Oleg D.
     */
    function upload_image() 
    {
        $this->layout = false;
        Configure::write('debug', 0);

    }
    
    /**
     * Link Upload
     * @author Oleg D.
     */
    function upload_link() 
    {
        $this->layout = false;
        Configure::write('debug', 0);

    }
    /**
     * Video Upload
     * @author Oleg D.
     */
    function upload_video() 
    {
        $this->layout = false;
        Configure::write('debug', 0);

    }
    
    /**
     * Show List of the albums
     * @author Oleg D.
     */
    function album_list($type) 
    {
        $this->layout = false;
        Configure::write('debug', 0);
        $userID = $this->getUserID();
        $albums = $this->Album->find('list', array('conditions' => array('user_id' => $userID, 'is_deleted' => 0, 'content_type' => $type), 'fields' => array('id', 'name')));
        $this->set('albums', $albums);
    }
    /**
     * Create image
     * @author Oleg D.
     */
    function getCreatedImage($imageID = 0, $albumID = 0) 
    {
        $this->layout = false;
        Configure::write('debug', 0);
        //$imageSave['id'] = intval($imageID);
        //$imageSave['model_id'] = $albumID;             
        //$this->Image->save($imageSave);  
        $img = $this->Image->find('first', array('conditions' => array('id' => $imageID)));       
        //$album = $this->Album->find('first', array('conditions' => array('id' => $albumID)));      
            
        $this->set('imageID', $imageID);       
        $this->set('img', $img);
        //$this->set('album', $album);
        $this->set('albumID', $albumID);
    }
    
    /**
     * Submissions finish page
     * @author Oleg D.
     */
    function finish($albumID = 0, $type = 'image') 
    {
        $userID = $this->getUserID();
        $toJunk = 0; 
        if ($albumID == $this->Album->getJunkId($userID)) {
            $toJunk = 1;            
        }
        $this->set('albumID', $albumID);  
        $this->set('toJunk', $toJunk);   
        $this->set('type', $type);      
    }
    /**
     * Show list of all users submissions
     * @author Oleg D.
     */    
    
    function submits_list($userID = 0) 
    {
        $isAdmin = 0;    
        $myProfile = 0;  
        if ($userID == $this->getUserID()) {
            $isAdmin = 1;    
            $myProfile = 1; 
        }

        
        $limit = 10;
        $this->paginate = array(
        'Link' => array(
            'limit' => $limit,
            'order' => array('Link.created' => 'DESC'),
            'extra' => $this->Submission->getSubmissionsSql($userID)        
        ));
        
        
        
        $submits = $this->paginate('Link'); 
        $imageIDs = $linkIDs = $videoIDs = array();
        foreach ($submits as $submit) {
            if ($submit[0]['model'] == 'Image') {
                $imageIDs[] = $submit[0]['id'];                
            } elseif ($submit[0]['model'] == 'Video') {
                $videoIDs[] = $submit[0]['id'];                              
            } elseif ($submit[0]['model'] == 'Link') {
                $linkIDs[] = $submit[0]['id'];                             
            }   
        }
        
         $itemsTags = array();        
        // VOTES	
        $votes['Video'] = $votes['Link'] = $votes['Image'] = array();
        if (!empty($linkIDs)) {
            $votes['Link'] = $this->Link->Vote->getVotes('Link', $linkIDs, $this->getUserID());
            
            $this->Image->ModelsTag->contain(array('Tag'));
            $allTags = $this->Image->ModelsTag->find('all', array('conditions' => array('ModelsTag.model' => 'Link', 'ModelsTag.model_id' => $linkIDs)));            
            foreach ($allTags as $allTag) {
                $itemsTags['Link'][$allTag['ModelsTag']['model_id']][] = $allTag['Tag'];           
            } 
        }
        if (!empty($videoIDs)) {
            $votes['Video'] = $this->Video->Vote->getVotes('Video', $videoIDs, $this->getUserID());
            
            $this->Image->ModelsTag->contain(array('Tag'));
            $allTags = $this->Image->ModelsTag->find('all', array('conditions' => array('ModelsTag.model' => 'Video', 'ModelsTag.model_id' => $videoIDs)));            
            foreach ($allTags as $allTag) {
                $itemsTags['Video'][$allTag['ModelsTag']['model_id']][] = $allTag['Tag'];           
            }             
        }
        if (!empty($imageIDs)) {
            $votes['Image'] = $this->Image->Vote->getVotes('Image', $imageIDs, $this->getUserID());
            
            $this->Image->ModelsTag->contain(array('Tag'));
            $allTags = $this->Image->ModelsTag->find('all', array('conditions' => array('ModelsTag.model' => 'Image', 'ModelsTag.model_id' => $imageIDs)));            
            foreach ($allTags as $allTag) {
                $itemsTags['Image'][$allTag['ModelsTag']['model_id']][] = $allTag['Tag'];           
            }             
        }
      
        
        
        
        $this->set('canVoteSubmissions', $this->Access->returnAccess('Vote_Submissions', 'c'));      
        $this->set('votes', $votes);     
        
        // EOF VOTES

        $this->set('itemsTags', $itemsTags); 
        $this->set('myProfile', $myProfile); 
        $this->set('isAdmin', $isAdmin);        
        $this->set('userID', $userID); 
        $this->set('limit', $limit);         
        $this->set('user', $this->Image->User->read(null, $userID));
        $this->set('submits', $submits); 
        $this->render();     
    }
    /**
     * New stuff page
     * @author Oleg D.
     */
    function new_stuff($model) 
    {

        $userID = $this->getUserID();
        $limit = 20;
        
        $modelMix = 0;
        
        $search = '';
        if(!empty($this->request->data['Submissions']['NewStaffSearch'])) {
            $this->Session->write('new_staff_search', $this->request->data['Submissions']['NewStaffSearch']);
            $this->passedArgs['new_staff_search'] = 1;
            $search = $this->request->data['Submissions']['NewStaffSearch'];
        }elseif($this->Session->check('new_staff_search')) {
            if (!empty($this->passedArgs['new_staff_search'])) {
                $this->request->data['Submissions']['NewStaffSearch'] = $this->Session->read('new_staff_search');
                $search = $this->request->data['Submissions']['NewStaffSearch'];               
            } else {
                $this->Session->delete('new_staff_search');    
            }
        }
        
        switch ($model) {
        case 'Image' :            
            $this->paginate = array(
                     'limit' => $limit,
                     'order' => array($model . '.modified' => 'DESC'),
                     'conditions' => array($model . '.model' => 'Album', $model . '.is_deleted' => 0, 'Album.model <>' => 'StoreSlot'),
            'contain' => array('User', 'Tag', 'Album')
            );  
            if ($search) {
                $this->paginate['conditions']['or']['Image.name LIKE'] = '%' . $search . '%';    
                $this->paginate['conditions']['or']['Image.description LIKE'] = '%' . $search . '%';                        
            } 
            $items = $this->paginate($model);
            $votes[$model] = $this->Video->Vote->getVotes($model, Set::combine($items, '{n}.' . $model . '.id', '{n}.' . $model . '.id'), $this->getUserID());                   
            break;    
        case 'Video' :              
            $this->paginate = array(
                     'limit' => $limit,
                     'order' => array($model . '.modified' => 'DESC'),
                     'conditions' => array($model . '.model' => 'Album', $model . '.is_deleted' => 0),
            'contain' => array('User', 'Tag', 'Album')
            ); 
            if ($search) {
                $this->paginate['conditions']['or']['Video.title LIKE'] = '%' . $search . '%';    
                $this->paginate['conditions']['or']['Video.description LIKE'] = '%' . $search . '%';                        
            }
            $items = $this->paginate($model); 
            $votes[$model] = $this->Video->Vote->getVotes($model, Set::combine($items, '{n}.' . $model . '.id', '{n}.' . $model . '.id'), $this->getUserID());                                        
            break; 
        case 'Link' : 
                
            $this->paginate = array(
            'recursive' => 1,    
                     'limit' => $limit,
                     'order' => array($model . '.modified' => 'DESC'),
                     'conditions' => array($model . '.is_deleted' => 0),
            'contain' => array('User', 'Tag')
            ); 
            $this->Link->hasMany = array();
                      
            if ($search) {
                $this->paginate['conditions']['or']['Link.title LIKE'] = '%' . $search . '%';    
                $this->paginate['conditions']['or']['Link.description LIKE'] = '%' . $search . '%';                        
            }  
            $items = $this->paginate($model);
            $votes[$model] = $this->Video->Vote->getVotes($model, Set::combine($items, '{n}.' . $model . '.id', '{n}.' . $model . '.id'), $this->getUserID());              
            break;
        case 'All' : 
            $userIDs = $items = array();
            $sql = $this->Submission->getAllSubmissionsSql($search);
            $this->paginate = array(
            'Link' => array(
            'limit' => $limit,
            'order' => array('Link.modified' => 'DESC'),
            'extra' => $sql          
            ));
            $submits = $this->paginate('Link'); 
            $imageIDs = $linkIDs = $videoIDs = $users = array();
            $i = 0;
            foreach ($submits as $submit) {
                $userIDs[$submit[0]['user_id']] = $submit[0]['user_id'];
                 $items[$i]['model'] = $submit[0]['model'];
                        
                if ($submit[0]['model'] == 'Image') {
                       $imageIDs[] = $submit[0]['id'];
                        
                       $item = array();  
                       $item['id'] = $submit[0]['id']; 
                       $item['views'] = $submit[0]['views']; 
                        $item['created'] = $submit[0]['created'];
                        $item['filename'] = $submit[0]['filename'];
                        $item['name'] = $submit[0]['name'];
                        $item['description'] = $submit[0]['description'];                        
                        $item['votes_plus'] = $submit[0]['votes_plus'];
                        $item['votes_minus'] = $submit[0]['votes_minus'];                        
                        $item['user_id'] = $submit[0]['user_id'];
                        $item['comments'] = $submit[0]['comments'];
                                                
                        $album['id'] = $submit[0]['album_id'];
                        $album['name'] = $submit[0]['album_name'];
                                                                                                
                                     
                    $items[$i][$submit[0]['model']] = $item;
                    $items[$i]['Album'] = $album;
                    
                } elseif ($submit[0]['model'] == 'Video') {                        
                    $videoIDs[] = $submit[0]['id']; 
                        
                    $item = array();          
                       $item['id'] = $submit[0]['id']; 
                       $item['views'] = $submit[0]['views']; 
                        $item['created'] = $submit[0]['created'];
                        $item['youtube_id'] = $submit[0]['filename'];
                        $item['code'] = $submit[0]['code'];
                        $item['title'] = $submit[0]['name'];
                        $item['description'] = $submit[0]['description'];                        
                        $item['votes_plus'] = $submit[0]['votes_plus'];
                        $item['votes_minus'] = $submit[0]['votes_minus'];                        
                        $item['user_id'] = $submit[0]['user_id'];
                        $item['comments'] = $submit[0]['comments'];
                                                
                        $album['id'] = $submit[0]['album_id'];
                        $album['name'] = $submit[0]['album_name'];
                                                                                                
                                     
                    $items[$i][$submit[0]['model']] = $item;
                    $items[$i]['Album'] = $album;
                        
                        
                } elseif ($submit[0]['model'] == 'Link') {
                    $linkIDs[] = $submit[0]['id']; 
                        
                       $item = array();  
                       $item['id'] = $submit[0]['id']; 
                       $item['views'] = $submit[0]['views']; 
                        $item['created'] = $submit[0]['created'];
                        $item['url'] = $submit[0]['code'];
                        $item['title'] = $submit[0]['name'];
                        $item['description'] = $submit[0]['description'];                        
                        $item['votes_plus'] = $submit[0]['votes_plus'];
                        $item['votes_minus'] = $submit[0]['votes_minus'];                        
                        $item['user_id'] = $submit[0]['user_id'];
                        $item['comments'] = $submit[0]['comments'];
                                                
                        $album['id'] = $submit[0]['album_id'];
                        $album['name'] = $submit[0]['album_name'];
                                                                                                
                                     
                    $items[$i][$submit[0]['model']] = $item;
                    $items[$i]['Album'] = $album;                                                    
                }  
                $i++; 
            }
                
            $itemsTags = array();        
            // VOTES	
            $votes['Video'] = $votes['Link'] = $votes['Image'] = array();
            if (!empty($linkIDs)) {
                $votes['Link'] = $this->Link->Vote->getVotes('Link', $linkIDs, $this->getUserID());
                    
                $this->Image->ModelsTag->contain(array('Tag'));
                   $allTags = $this->Image->ModelsTag->find('all', array('conditions' => array('ModelsTag.model' => 'Link', 'ModelsTag.model_id' => $linkIDs)));            
                foreach ($allTags as $allTag) {
                    $itemsTags['Link'][$allTag['ModelsTag']['model_id']][] = $allTag['Tag'];           
                } 
            }
            if (!empty($videoIDs)) {
                $votes['Video'] = $this->Video->Vote->getVotes('Video', $videoIDs, $this->getUserID());
                    
                $this->Image->ModelsTag->contain(array('Tag'));
                $allTags = $this->Image->ModelsTag->find('all', array('conditions' => array('ModelsTag.model' => 'Video', 'ModelsTag.model_id' => $videoIDs)));            
                foreach ($allTags as $allTag) {
                    $itemsTags['Video'][$allTag['ModelsTag']['model_id']][] = $allTag['Tag'];           
                }             
            }
            if (!empty($imageIDs)) {
                $votes['Image'] = $this->Image->Vote->getVotes('Image', $imageIDs, $this->getUserID());
                    
                $this->Image->ModelsTag->contain(array('Tag'));
                $allTags = $this->Image->ModelsTag->find('all', array('conditions' => array('ModelsTag.model' => 'Image', 'ModelsTag.model_id' => $imageIDs)));            
                foreach ($allTags as $allTag) {
                    $itemsTags['Image'][$allTag['ModelsTag']['model_id']][] = $allTag['Tag'];           
                }             
            } 
            $this->Image->User->recursive = -1;
            $users = $this->Image->User->find('all', array('conditions' => array('User.id' => $userIDs)));
                
            $users = Set::combine($users, '{n}.User.id', '{n}');
            foreach ($items as $key => $item) {
                if (!empty($users[$item[$item['model']]['user_id']]['User'])) {
                    $items[$key]['User'] = $users[$item[$item['model']]['user_id']]['User'];                        
                } else {
                    $items[$key]['User'] = array();                        
                }
                if (!empty($itemsTags[$item['model']][$item[$item['model']]['id']])) {
                    $items[$key]['Tag'] = $itemsTags[$item['model']][$item[$item['model']]['id']];
                } else {
                    $items[$key]['Tag']    = array();
                }
            }
            //pr($items);
            //exit;
            $modelMix = 1;
            break;                                    
        } 

        //pr($items);
        //exit;
   

        $this->set('canVoteSubmissions', $this->Access->returnAccess('Vote_Submissions', 'c'));    
        $this->set('votes', $votes);                         
        $this->set('items', $items);   
        $this->set('limit', $limit);  
        $this->set('model', $model);  
        $this->set('search', $search);
        $this->set('modelMix', $modelMix);      
        //exit;	
    }
}
?>
