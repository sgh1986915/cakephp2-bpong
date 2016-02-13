<?php
class KnowledgeQuestionsController extends AppController
{

    var $name = 'KnowledgeQuestions';
    var $helpers = array('Html', 'Form','Tree');


    function view($slug = null) 
    {
        if (!$slug) {
            $this->Session->setFlash(__('Invalid KnowledgeQuestion'), 'flash_error');
            return $this->redirect(array('action'=>'index'));
        }
        $showHidden = $this->Access->getAccess('KnowledgeHidden', 'r');
        $conditions = array();
        if (!$showHidden) {
            $conditions = array('KnowledgeQuestion.is_hidden' => 0);
        }
        
        $knowledgeQuestion = $this->KnowledgeQuestion->find('first', array('conditions'=>$conditions+array('KnowledgeQuestion.slug'=>$slug)));
        if (empty($knowledgeQuestion)) {
            $this->Session->setFlash(__('Invalid Knowledge question'), 'flash_error');
            return $this->redirect(array('action'=>'index'));
        }        
        
        $topicId = $knowledgeQuestion['KnowledgeQuestion']['topic_id'];
        
        $parents = $this->KnowledgeQuestion->KnowledgeTopic->getpath($topicId);
        if (!empty($parents)) {
            $topic['KnowledgeTopic']['lft']  = $parents[0]['KnowledgeTopic']['lft'];
            $topic['KnowledgeTopic']['rght'] = $parents[0]['KnowledgeTopic']['rght'];
            
            foreach ($parents as $parent) {
                $parentsId[] = $parent['KnowledgeTopic']['id'];
            }
        }
        if (!$showHidden) {
            $conditions = array('KnowledgeTopic.is_hidden' => 0);
        }
        $topics = $this->KnowledgeQuestion->KnowledgeTopic->find(
            'threaded', array('order'=>"lft", 
                                     'conditions' => array(
                                     'lft >='  => $topic['KnowledgeTopic']['lft'],
                                     'rght <=' => $topic['KnowledgeTopic']['rght']
                                     ))
        );
        
        
        
        $this->set(compact('knowledgeQuestion', 'topics', 'parentsId', 'topicId'));
    }
    /**
 * add new topic question
 * @param $topicId
 * @return unknown_type
 */
    function add($topicId = null) 
    {
        $this->Access->checkAccess('Knowledgebase', 'c'); 
        if (!empty($this->request->data)) {
            $this->KnowledgeQuestion->create();
            if ($this->KnowledgeQuestion->save($this->request->data)) {
                $this->Session->setFlash(__('KnowledgeQuestion saved.'), 'flash_success');
                return $this->redirect(array('controller'=>'knowledge_topics','action'=>'index',$this->request->data['KnowledgeQuestion']['topic_id']));
            } else {
            }
        }
        if (empty($this->request->data['KnowledgeQuestion']['topic_id'])) {
            $this->request->data['KnowledgeQuestion']['topic_id'] = $topicId;
        }
        if (empty($this->request->data['KnowledgeQuestion']['ord'])) {
            $this->request->data['KnowledgeQuestion']['ord'] = 1;
        }    
    }

    /**
 * edit topic question
 * @param $id
 * @return unknown_type
 */    
    function edit($id = null) 
    {
        $this->Access->checkAccess('Knowledgebase', 'u'); 
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid KnowledgeQuestion'), 'flash_error');
            return $this->redirect(array('controller'=>'knowledge_topics','action'=>'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->KnowledgeQuestion->save($this->request->data)) {
                $this->Session->setFlash(__('The KnowledgeQuestion has been saved.'), 'flash_success');
                return $this->redirect(array('controller'=>'knowledge_topics','action'=>'index',$this->request->data['KnowledgeQuestion']['topic_id']));
            } else {
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->KnowledgeQuestion->read(null, $id);
        }    
        if (empty($this->request->data['KnowledgeQuestion']['ord'])) {
            $this->request->data['KnowledgeQuestion']['ord'] = 1;
        }        
    }
    /**
 * Delete knowledge question
 * @param $id
 * @return unknown_type
 */
    function delete($id = null) 
    {
        if (!$id) {
            $this->Session->setFlash(__('Invalid KnowledgeQuestion'), 'flash_error');
            return $this->redirect(array('controller'=>'knowledge_topics','action'=>'index'));
        }
        $this->Access->checkAccess('Knowledgebase', 'd'); 
        $topicId = $this->KnowledgeQuestion->field('topic_id', array('id'=>$id));
        if ($this->KnowledgeQuestion->del($id)) {
            $this->Session->setFlash(__('KnowledgeQuestion deleted'), 'flash_success');
            return $this->redirect(array('controller'=>'knowledge_topics','action'=>'index',$topicId));
        }
    }

}
?>