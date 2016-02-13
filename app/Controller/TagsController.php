<?php

class TagsController extends AppController
{
    var $name = 'Tags';
    var $uses    = array('Tag', 'ModelsTag', 'Image');
    /**
     * Tags autocompleter
     * @author Oleg D.
     */
    function autocomplete($model = '') 
    {
        Configure::write('debug', 0);
        
        if (isset($_REQUEST["q"]) && $_REQUEST["q"] && $model) {
            $q = strtolower($_REQUEST["q"]);
            
            $conditions['tag LIKE '] = trim(strtolower($q)) . '%';
            $conditions['model '] = $model;
            
            $tags = $this->Tag->find('all', array('conditions' => $conditions, 'fields' => array('tag')));
            if (!empty($tags)) {
                foreach ($tags as $tag) {
                    echo $tag['Tag']['tag'] . "|\n";
                }                  
            } 
        }
        exit;    
    }
    
    /**
     * Show tags by categories
     * @author Oleg D.
     */
    function index() 
    {
        $this->Access->checkAccess('TagsAmin', 'r');
        $models = $this->ModelsTag->find('all', array('conditions' => array(), 'fields' => array('DISTINCT(ModelsTag.model)')));   
         
        $this->set('models', $models);
    }
    
    /**
     * Show all tags of the model
     * @author Oleg D.
     */
    function modelShow($model = '') 
    {
        $this->Access->checkAccess('TagsAmin', 'r');
        $tags = $this->Tag->find('all', array('conditions' => array('Tag.model' => $model)));
        $this->paginate = array(
            'limit' => 50,
            'order' => array('Tag.id' => 'DESC'),
            'conditions' => array('Tag.model' => $model)       
        );
        $this->Tag->recursive = 1;
        $tags = $this->paginate('Tag');    
                
        $this->set('tags', $tags);       
        $this->set('model', $model);
    }
    
    /**
     * Show tag
     * @author Oleg D.
     */
    function show($tagID, $model = null, $search = null) 
    {
        
        // MODEL OPTIONS
        // For default model
        $modelOptions[$model] = array(
            'search_fileds' => array('description'),
            'join' => array('User' => true),                          
            'calumns' => array(   
                'Title & Description' => $model . '.name',
                'Upranks' => $model . '.votes_plus',
                'Downranks' => $model . '.votes_minus',
                'Comments' => $model . '.comments',
                'Submitted By' => 'User.lgn',
                'Submitted' => $model . '.created'       
                )
        );
        // For Image, Video Album, Link
        $modelOptions['Video'] = $modelOptions['Album'] = $modelOptions['Link'] =
        array(
            'search_fileds' => array('title', 'description'), 
            'join' => array('User' => true),                      
            'calumns' => array(
                'Title & Description' => $model . '.title',
                'Upranks' => $model . '.votes_plus',
                'Downranks' => $model . '.votes_minus',
                'Comments' => $model . '.comments',
                'Submitted By' => 'User.lgn',
                'Submitted' => $model . '.created'       
                )
        );
        $modelOptions['Album']['search_fileds'] = array('name', 'description'); 
        $modelOptions['Image'] =        
        array(
            'search_fileds' => array('name', 'description'), 
            'join' => array('User' => true),                      
            'calumns' => array(
                'Title & Description' => $model . '.name',
                'Upranks' => $model . '.votes_plus',
                'Downranks' => $model . '.votes_minus',
                'Comments' => $model . '.comments',
                'Submitted By' => 'User.lgn',
                'Submitted' => $model . '.created'       
                )
        );
        // EOF MODEL OPTIONS
                
        $searchQuery = '';                
        $tag = $this->Tag->read(null, $tagID);
        
        if ($search) {
            if (!empty($this->request->data['Tag']['search'])) {
                $searchQuery = $this->request->data['Tag']['search'];
                $this->Session->write('tag_search', $searchQuery); 
            } elseif ($this->Session->read('tag_search')) {
                $searchQuery = $this->Session->read('tag_search');                
            }                                    
            $this->request->data['Tag']['search'] = $searchQuery;                    
        }

        // Custom Pagination
        $limit = 10;
        $this->paginate = array(
            'limit' => $limit,
            'extra' => $this->ModelsTag->getShowTagQueries($model, $tagID, $searchQuery, $modelOptions[$model]['search_fileds']) + array('params' => $this->request->params),
            'order' => array($model . '.created' => 'DESC')         
        ); 
        $items = $this->paginate('ModelsTag'); 
        // EOF Custom Pagination
        
        $itemIDs = Set::combine($items, '{n}.' . $model . '.id', '{n}.' . $model . '.id');
       
        $this->ModelsTag->contain(array('Tag'));
        $allTags = $this->ModelsTag->find('all', array('conditions' => array('ModelsTag.model' => $model, 'ModelsTag.model_id' => $itemIDs)));    
        $itemsTags = array();
        foreach ($allTags as $allTag) {
            $itemsTags[$allTag['ModelsTag']['model_id']][] = $allTag['Tag'];           
        }
       
        // VOTES
        $modelVotes = $this->Image->Vote->getVotes($model, $itemIDs, $this->getUserID());
        $this->set('modelVotes', $modelVotes);        
        $this->set('canVoteSubmissions', $this->Access->returnAccess('Vote_Submissions', 'c'));      
        // EOF VOTES
        
        $this->request->data['Tag']['model'] = $model; 
      
        $this->set('itemsTags', $itemsTags);          
        $this->set('modelOptions', $modelOptions);    
        $this->set('tag', $tag);       
        $this->set('model', $model);
        $this->set('items', $items);
        $this->set('limit', $limit);      
    }   
    
    /**
     * Edit tag
     * @author Oleg D.
     */
    function edit($id = 0) 
    {
        if (!$id) {
            $id = $this->request->data['Tag']['id'];
        }
        $this->Access->checkAccess('TagsAmin', 'u');
        $tag = $this->Tag->find('first', array('conditions' => array('Tag.id' => $id)));            
        if (!empty($this->request->data)) {    
            
            $this->Tag->save($this->request->data['Tag']);
            $this->Session->setFlash('Tag has been changed.', 'flash_success');
            return $this->redirect('/tags/modelShow/' . $tag['Tag']['model']);
        }
        $this->request->data = $tag;        
    }
    
    /**
     * Add tag
     * @author Oleg D.
     */
    function add($model) 
    {
        $this->Access->checkAccess('TagsAmin', 'c');        
        if (!empty($this->request->data)) {    
            $this->request->data['Tag']['user_id'] = $this->getUserID();
            $this->Tag->save($this->request->data['Tag']);
            $this->Session->setFlash('Tag has been added', 'flash_success');
            return $this->redirect('/tags/modelShow/' . $this->request->data['Tag']['model']);
        }
        $this->set('model', $model);    
    } 
    /**
     * Delete tag
     * @author Oleg D.
     */
    function delete($id = 0) 
    {

        $this->Access->checkAccess('TagsAmin', 'd');
        $tag = $this->Tag->find('first', array('conditions' => array('Tag.id' => $id)));
        
        $tag = $this->Tag->find('first', array('conditions' => array('Tag.id' => $id)));    
        
        $this->Tag->delete($id);
        $this->ModelsTag->deleteAll(array('tag_id' => $id));
        
        $this->Session->setFlash('Tag has been deleted', 'flash_success');
        return $this->redirect('/tags/modelShow/' . $tag['Tag']['model']);

    }
    
    /**
     * Delete users tag
     * @author Oleg D.
     */
    function deleteUsers($id = 0) 
    {
        if ($this->getUserID() !=VISITOR_USER) {
            if (!isset($_SERVER['HTTP_REFERER']) || !$_SERVER['HTTP_REFERER']) {
                $referer = '/';
            } else {
                $referer = $_SERVER['HTTP_REFERER'];
            }
            $tagID = $this->ModelsTag->field('tag_id', array('id' => $id));
            if ($tagID) {
                $this->ModelsTag->query('UPDATE tags SET counter = counter - 1 WHERE id = ' . $tagID);  
            }
            $this->ModelsTag->deleteAll(array('id' => $id));    
                       
            $this->Session->setFlash('Users Tag has been deleted', 'flash_success');           
            $this->redirect($referer);    
                   
        }
    }
    /**
     * Add user tags
     * @author Oleg D.
     */
    function ajaxAddUsers($modelName = '', $modelID = 0, $authorID = 0) 
    {
        Configure::write('debug', 0);     
        if (!empty($this->request->data)) {
            $newTagsIDs = array();    
            $userID = $this->getUserID();
            $modelID = $this->request->data['UserTag']['modelID'];
            $modelName = $this->request->data['UserTag']['modelName'];
            $dataTags = trim($this->request->data['UserTag']['tags']);
            $authorID = $this->request->data['UserTag']['authorID'];
            
            if ($dataTags) {
                $expTags = explode(',', $dataTags);    
                if(empty($expTags) && trim($dataTags)) {
                    $expTags[0] = $dataTags;
                }  
                if(!trim($expTags[count($expTags) - 1])) {
                    unset($expTags[count($expTags) - 1]);            
                }                
                $expTags = $this->ModelsTag->deleteDubleTags($expTags);
                $tags = array();
                foreach ($expTags as $expTag) {
                    $tag = $this->Tag->parseTag($expTag);               
                    if ($tag) {
                            $thisTagID = $this->Tag->field('id', array('Tag.tag' => $tag));
                            
                        if (!$thisTagID) {
                            $saveTag['tag'] = $tag;
                            $saveTag['user_id'] = $userID;
                                
                            $this->Tag->create();
                            $this->Tag->save($saveTag);  
                            $thisTagID = $this->Tag->getLastInsertID();  
                        }
                            $this->ModelsTag->create();
                            
                        if (!$this->ModelsTag->find('count', array('conditions' => array('tag_id' => $thisTagID, 'model' => $modelName, 'model_id' => $modelID)))) {
                            $newTagsIDs[] = $thisTagID;
                            $saveModelsTag['tag_id'] = $thisTagID;
                            $saveModelsTag['model_id'] = $modelID;
                            $saveModelsTag['model'] = $modelName;
                            $saveModelsTag['user_id'] = $userID;
            
                            $this->ModelsTag->save($saveModelsTag);
                        }
                    }    
                }
            }          
            /// increase tags counter        
            if (!empty($newTagsIDs)) {
                $this->ModelsTag->query('UPDATE tags SET counter = counter + 1 WHERE id = ' . implode(' OR id = ', $newTagsIDs));                   
            }
                 
            $this->Session->setFlash('Tag has been added.', 'flash_success');
            if (!isset($_SERVER['HTTP_REFERER']) || !$_SERVER['HTTP_REFERER']) {
                $referer = '/';
            } else {
                $referer = $_SERVER['HTTP_REFERER'];
            }
            return $this->redirect($referer);
        }
        $this->set('authorID', $authorID);    
        $this->set('modelID', $modelID);    
        $this->set('modelName', $modelName);    
    }
    
    /**
     * show albums tags
     * @author Oleg D.
     */
    function Album($tag) 
    {

        exit('Unser Constraction');
    }   
   
       
}
?>
