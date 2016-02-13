<?php
class KnowledgeTopicsController extends AppController
{

    var $name = 'KnowledgeTopics';
    var $helpers = array('Html', 'Form','Tree');
    var $actsAs = array('Tree');

    /**
 * Show topic tree for the users
 * @param $slug 
 * @return unknown_type
 */    
    function show($slug = null) 
    {
        $topicId = null;
        $parentsId = array();
        $showHidden = $this->Access->getAccess('KnowledgeHidden', 'r');
        $conditions = array();
        if (!$showHidden) {
            $conditions = array('KnowledgeTopic.is_hidden' => 0);
        }
        
        if ($slug) {            
            $topic = $this->KnowledgeTopic->find('first', array('conditions'=>$conditions + array('slug'=>$slug)));
            if (empty($topic)) {
                $this->Session->setFlash(__('Invalid Knowledge Topic'), 'flash_error');
                return $this->redirect(array('action'=>'index'));
            }
            
            if (!$topicId && !empty($topic)) {
                $topicId = $topic['KnowledgeTopic']['id'];
            }
            
            $parents = $this->KnowledgeTopic->getpath($topicId);
            if (!empty($parents)) {
                $topic['KnowledgeTopic']['lft']  = $parents[0]['KnowledgeTopic']['lft'];
                $topic['KnowledgeTopic']['rght'] = $parents[0]['KnowledgeTopic']['rght'];
                
                foreach ($parents as $parent) {
                    $parentsId[] = $parent['KnowledgeTopic']['id'];
                }
            }
            
            $topics = $this->KnowledgeTopic->find(
                'threaded', array('order'=>"lft", 
                                         'conditions' => $conditions + array(                                            
                                         'lft >='  => $topic['KnowledgeTopic']['lft'],
                                         'rght <=' => $topic['KnowledgeTopic']['rght']
                                         )
                                         )
            );
        } else {
            $topics = $this->KnowledgeTopic->find('threaded', array('order'=>"lft",'conditions'=>$conditions));
            if (!$topicId && !empty($topics)) {
                $topicId = $topics[0]['KnowledgeTopic']['id'];
            }
        }

        $this->set(compact('topics', 'parentsId', 'topicId'));
        
        
        if ($this->RequestHandler->isAjax()) {
            Configure::write('debug', 0);
            $this->render('tree');
        }
        
    }    
    
    /**
 * Show topic tree
 * @param $id 
 * @return unknown_type
 */    
    function index($id = null) 
    {
        //configure::write('debug', 2);
        //$this->Access->checkAccess('Knowledgebase','l');
        $showHidden = $this->Access->getAccess('KnowledgeHidden', 'r');
        
        $conditions = array();
        if (!$showHidden) {
            $conditions = array('KnowledgeTopic.is_hidden <>' => 1);
        }
        
        $topics = $this->KnowledgeTopic->find('threaded', array('order'=>"lft",'conditions'=>$conditions));
        
        if (!$id && !empty($topics)) {
            $id = $topics[0]['KnowledgeTopic']['id'];
        }       
        $this->set(compact('topics', 'topic'));
        $this->set('topicId', $id);
        
        if ($this->RequestHandler->isAjax()) {
            Configure::write('debug', 0);
            $this->render('tree');
        }
        $this->set('isAccess', $this->Access->getAccess('Knowledgebase', 'l'));
        
    }

    /**
 * View knowledge topic questions and answers
 * @param $id
 * @return unknown_type
 */    
    function view($id = null) 
    {
        if ($this->RequestHandler->isAjax()) {
            Configure::write('debug', 0);           
        }
        $this->set('isAccess', $this->Access->getAccess('Knowledgebase', 'l'));
        if (!$id) {
            $this->Session->setFlash(__('Invalid KnowledgeTopic'), 'flash_error');
            $this->redirect(array('action'=>'index'));
        }
        $showHidden = $this->Access->getAccess('KnowledgeHidden', 'r');
        $conditions = array();
        if (!$showHidden) {
            $conditions = array('KnowledgeTopic.is_hidden' => 0);
        }
        
        $knowledgeTopic = $this->KnowledgeTopic->find('first', array('conditions'=>$conditions+array('id'=>$id),'contain'=>array('KnowledgeQuestion.KnowledgeAnswer')));
        $this->set(compact('knowledgeTopic'));
    }
    /**
 * add ne topic/subtopic
 * @param $parentId
 * @return unknown_type
 */
    function add($parentId = null) 
    {
        $this->Access->checkAccess('Knowledgebase', 'c'); 
        if (!empty($this->request->data)) {
            if (empty($this->request->data['KnowledgeTopic']['parent_id'])) {
                $this->request->data['KnowledgeTopic']['parent_id'] = null;
            }
            $this->KnowledgeTopic->create();
            if ($this->KnowledgeTopic->save($this->request->data)) {
                $this->Session->setFlash(__('KnowledgeTopic saved.'), 'flash_success');
                return $this->redirect(array('action'=>'index'));
            } else {
            }
        }
        $this->set(compact('parentId'));
    }
    /**
 * Edit topic/subtopic
 * @param $id
 * @return unknown_type
 */
    function edit($id = null) 
    {
        $this->Access->checkAccess('Knowledgebase', 'u'); 
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid KnowledgeTopic'), 'flash_error');
            return $this->redirect(array('action'=>'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->KnowledgeTopic->save($this->request->data)) {
                $this->Session->setFlash(__('The KnowledgeTopic has been saved.'), 'flash_success');
                return $this->redirect(array('action'=>'index'));
            } else {
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->KnowledgeTopic->read(null, $id);
        }
    }
    /**
 * Delete topic /subtopic
 * @param $id
 * @return unknown_type
 */
    function delete($id = null) 
    {
        $this->Access->checkAccess('Knowledgebase', 'd'); 
        if (!$id) {
            $this->Session->setFlash(__('Invalid KnowledgeTopic'), 'flash_error');
            return $this->redirect(array('action'=>'index'));
        }
        if ($this->KnowledgeTopic->del($id)) {
            $this->Session->setFlash(__('KnowledgeTopic deleted'), 'flash_success');
            return $this->redirect(array('action'=>'index'));
        }
    }
    /**
 * AJAX change parrent
 * @return unknown_type
 */
    /*	function ajax_changeParent() {
        Configure::write('debug', 2);
        if ($this->RequestHandler->isAjax() && !empty($this->request->data)) {
            $this->KnowledgeTopic->id = $this->request->data['id'];
            $this->KnowledgeTopic->saveField('parent_id', $this->request->data['parent_id']);
        }
        exit();
    }//eof*/
    /**
 * moving in the node
 * @param $id
 * @param $direction
 * @return unknown_type
 */    
    function sort($id, $direction) 
    {
        $this->Access->checkAccess('Knowledgebase', 'u'); 
        Configure::write('debug', 0);
        $this->layout = false;
        $this->KnowledgeTopic->sort($id, $direction);
        exit();

    }
    /**
 * 
 * @param $id
 * @param $newParentId
 * @return unknown_type
 */
    function setParent($id, $newParentId) 
    {
        $this->Access->checkAccess('Knowledgebase', 'u'); 
        $this->layout = false;
        Configure::write('debug', 1);
        
        $result = $this->KnowledgeTopic->setParent($id, $newParentId);
        
        exit($result);
    }
    
}