<?php
class KnowledgeAnswersController extends AppController
{

    var $name = 'KnowledgeAnswers';
    var $helpers = array('Html', 'Form');
    /**
 * add new answer
 * @return unknown_type
 */
    function add($questionId = null) 
    {
        $this->Access->checkAccess('Knowledgebase', 'c'); 
        
        if (!empty($this->request->data)) {
            $topicId = $this->KnowledgeAnswer->KnowledgeQuestion->field('topic_id', array('id'=>$this->request->data['KnowledgeAnswer']['question_id']));
            $this->KnowledgeAnswer->create();            
            if ($this->KnowledgeAnswer->save($this->request->data)) {
                $this->Session->setFlash(__('KnowledgeAnswer saved.'), 'flash_success');
                return $this->redirect(array('controller'=>'knowledge_topics','action'=>'index',$topicId));
            } 
        }
        if (empty($this->request->data['KnowledgeAnswer']['question_id'])) {
            $this->request->data['KnowledgeAnswer']['question_id'] = $questionId;
        }
        
    }
    /**
 * Edit answer
 * @param $id
 * @return unknown_type
 */
    function edit($id = null) 
    {
        $this->Access->checkAccess('Knowledgebase', 'u'); 
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid KnowledgeAnswer'), 'flash_error');
            return $this->redirect(array('controller'=>'knowledge_topics','action'=>'index'));
        }
        if (!empty($this->request->data)) {
            $topicId = $this->KnowledgeAnswer->KnowledgeQuestion->field('topic_id', array('id'=>$this->request->data['KnowledgeAnswer']['question_id']));
            if ($this->KnowledgeAnswer->save($this->request->data)) {
                $this->Session->setFlash(__('The KnowledgeAnswer has been saved.'), 'flash_success');
                return $this->redirect(array('controller'=>'knowledge_topics','action'=>'index',$topicId));
            } else {
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->KnowledgeAnswer->read(null, $id);
        }
    
    }
    /**
 * Delete answer
 * @param $id
 * @return unknown_type
 */
    function delete($id = null) 
    {
        $this->Access->checkAccess('Knowledgebase', 'd'); 
        if (!$id) {
            $this->Session->setFlash(__('Invalid KnowledgeAnswer'), 'flash_error');
            return $this->redirect(array('controller'=>'knowledge_topics','action'=>'index'));
        }
        
        $questionId = $this->KnowledgeAnswer->field('question_id', array('id'=>$id));
        $topicId = $this->KnowledgeAnswer->KnowledgeQuestion->field('topic_id', array('id'=>$questionId));
        
        if ($this->KnowledgeAnswer->del($id)) {
            $this->Session->setFlash(__('KnowledgeAnswer deleted'), 'flash_success');
            return $this->redirect(array('controller'=>'knowledge_topics','action'=>'index',$topicId));
        }
    }

}
?>