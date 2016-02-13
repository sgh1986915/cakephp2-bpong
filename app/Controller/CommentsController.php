<?php
class CommentsController extends AppController
{

    var $name = 'Comments';
    var $helpers = array('Bbcode');
    
    /**
 * Preview comment
 * @author vovich
 * @return unknown_type
 */    
    function preview() 
    {
        Configure::write('debug', 0);
         $this->layout = false;
        if (!$this->RequestHandler->isAjax()) {
            $this->redirect($_SERVER['HTTP_REFERER']);
        }       
        if (!empty($_POST['comment'])) {
            $this->set('comment', $_POST['comment']);
        }   else {
            exit("Comment can not be empty");
        }   
        
    }
    /**
 * Adding new comment
 * @author vovich
 * @return unknown_type
 */    
    function add() 
    {
        Configure::write('debug', 0);
         $this->layout = false;
        if (!$this->RequestHandler->isAjax()) {
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
        if (!$this->Access->getAccess('Comment_'.$this->request->data['Comment']['model'], 'c')) {
            exit(__("You are not permitted for this action."));
        }        
        if (!empty($this->request->data)) {
            if (empty($this->request->data['Comment']['comment'])) {
                exit(__('The comment could not be empty.'));
            }
            $userId = $this->Access->getLoggedUserID();    
            $this->request->data['Comment']['user_id'] = $userId;
            $this->request->data['Comment']['id'] = $this->Comment->addNewComent($this->request->data);

            if ($this->request->data['Comment']['model'] == 'Image') {
                Cache::delete('last_images');    
            } elseif ($this->request->data['Comment']['model'] == 'Video') {
                Cache::delete('last_images');                
            }            
            if ($this->request->data['Comment']['id']) {
                /*SENDING EMAILS*/
                if (empty($this->request->data['Comment']['parent_id'])) {
                    $this->__sendNewComment($this->request->data['Comment']);
                } else {
                    $this->__sendNewReply($this->request->data['Comment']);
                }
                
                exit();
            } else {                
                exit(__('The comment could not be saved. Please, try again.'));                
            }
        } else {
            exit("empty data");
        }
        
    }
    /**
 * show model ID
 * @param unknown_type $model
 * @param unknown_type $modelId
 * @return unknown_type
 */    
    function show($model = null, $modelId = null) 
    {
         Configure::write('debug', 0);
         $this->layout = false;
        if (!$this->RequestHandler->isAjax()) {
            $this->redirect($_SERVER['HTTP_REFERER']);
        }        
        
        $userId = $this->Access->getLoggedUserID();    
        $this->set('comments', $this->Comment->getCommentsTree($model, $modelId));
        $this->set('commentVotes', $this->Comment->Vote->getCommentVotes($model, $modelId, $userId));
        
        $this->set('canVoteComment', $this->Access->returnAccess('Vote_Comment', 'c'));
        $this->set('canComment', $this->Access->returnAccess('Comment_'.$model, 'c'));
        $this->set('canDeleteComment', $this->Access->returnAccess('Comment', 'd'));
        
        $this->set(compact('model', 'modelId'));       
    }


    /**
 * Delete comment
 * @author vovich
 * @param $id
 * @return unknown_type
 */
    function delete($id = null) 
    {
        $result['error'] = "";
        $result['comments'] = 0;
        if (!$id) {
            $result['error'] = 'Invalid Comment ID';
        }                
        Configure::write('debug', 0);
         $this->layout = false;
        if (!$this->RequestHandler->isAjax()) {
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
         
        $commentInfo = $this->Comment->find('first', array('contain'=> array(),'fields' =>array('id','user_id','model','model_id') ,'conditions'=>array('id'=>$id)));
        if (empty($commentInfo)) {
            $result['error'] = 'can not find comment';
        } 
        if (!$this->Access->getAccess('Comment', 'd', $commentInfo['Comment']['user_id'])) {
            $result['error'] = "You are not permitted for this action." . $this->Access->getAccess('Comment', 'd', $commentInfo['Comment']['user_id']);
        }      
        if (empty($result['error']) && !$commentInfo['Comment']['is_deleted']) {
            $result['comments'] = $this->Comment->deleteComment($id, $commentInfo['Comment']['model'], $commentInfo['Comment']['model_id']);
        }
         
        exit($this->Json->encode($result));         
    }
    /**
 * Send new comment email to the owner 
 * @author vovich
 * @param unknown_type $data array(model, model_id, id,url,user_id, comment)
 * @return unknown_type
 */    
    function __sendNewComment($data = array()) 
    {        
        $model_url    = "http://{$_SERVER['HTTP_HOST']}".$data['url'];
        $modelIfo     = $this->Comment->$data['model']->find('first', array('contain'=>array('User'),'conditions'=>array($data['model'].'.id'=>$data['model_id'])));
        $authorInfo   = $modelIfo['User'];
        $modelIfo     = $modelIfo[$data['model']];
        $respondent = $this->Comment->User->find('first', array('contain'=>array(),'conditions'=>array('User.id'=>$data['user_id'])));
        $respondent = $respondent['User'];
        if (isset($modelIfo['user_id']) && $modelIfo['user_id'] == $this->getUserID()) {
            return true;
        }
        if (!empty($modelIfo['title'])) {
            $title = $modelIfo['title'];
        } elseif (!empty($modelIfo['name'])) {
            $title = $modelIfo['name'];                        
        } else {
            $title =  $data['model'];
        }
        $result = $this->sendMailMessage(
            'NewComment', array(
                             '{MODEL}'              => $data['model']
                             ,'{MODEL_ID}'          => $data['model_id']
                           ,'{MODEL_URL}'      => $model_url
                           ,'{RESPONDENT_LGN}'               => $respondent['lgn']
                           ,'{RESPONDENT_USER_LINK}'  => "<a href='http://".$_SERVER['HTTP_HOST']."/users/view/".$respondent['lgn']."'>".$respondent['lgn']."</a>"
                           ,'{REPLY}'                 =>  html_entity_decode($this->convert_bbcode($data['comment']), ENT_QUOTES, 'UTF-8')
                           ,'{MODEL_LINK}'        => "<a href='".$model_url."'>".$title."</a>"
                           ,'{REPLY_LINK}'        => "<a href='".$model_url."?reply_to=".$data['id']."#comment_".$data['id']."'>Reply on this comment</a>"
                           ,'{COMMENT_URL}' => "{$model_url}#comment_".$data['model_id']
                              ),
            $authorInfo['email']
        );
        
    }
    /**
 * Send you have new reply to the comment owner
 * @author vovich
 * @param unknown_type $data array(model, model_id, id,url,user_id, comment)
 * @return unknown_type
 */
    function __sendNewReply($data) 
    {
        $model_url    = "http://{$_SERVER['HTTP_HOST']}".$data['url'];
        $modelIfo     = $this->Comment->$data['model']->find('first', array('contain'=>array('User'),'conditions'=>array($data['model'].'.id'=>$data['model_id'])));
        $authorInfo   = $modelIfo['User'];
        $modelIfo     = $modelIfo[$data['model']];
        $respondent = $this->Comment->User->find('first', array('contain'=>array(),'conditions'=>array('User.id'=>$data['user_id'])));
        $respondent = $respondent['User'];
        $comment    = $this->Comment->find('first', array('contain'=>array('User'),'conditions'=>array('Comment.id'=>$data['parent_id'])));
        
        if (!empty($modelIfo['title'])) {
            $title = $modelIfo['title'];
        } elseif (!empty($modelIfo['name'])) {
            $title = $modelIfo['name'];                        
        } else {
            $title =  $data['model'];
        }
        
        $result = $this->sendMailMessage(
            'NewCommentReply', array(
                         '{MODEL}'              => $data['model']
                         ,'{MODEL_ID}'          => $data['model_id']
                       ,'{MODEL_URL}'      => $model_url
                       ,'{RESPONDENT_LGN}'               => $respondent['lgn']
                       ,'{RESPONDENT_USER_LINK}'  => "<a href='http://".$_SERVER['HTTP_HOST']."/users/view/".$respondent['lgn']."'>".$respondent['lgn']."</a>"
                       ,'{COMMENT}'             =>  html_entity_decode($this->convert_bbcode($comment['Comment']['comment']), ENT_QUOTES, 'UTF-8')
                       ,'{REPLY}'                 =>  html_entity_decode($this->convert_bbcode($data['comment']), ENT_QUOTES, 'UTF-8')
                       ,'{MODEL_LINK}'        => "<a href='".$model_url."'>".$title."</a>"
                       ,'{REPLY_LINK}'        => "<a href='".$model_url."?reply_to=".$data['id']."#comment_".$data['id']."'>Reply on this comment</a>"
                       ,'{COMMENT_URL}' => "{$model_url}#comment_".$data['parent_id']
                       ,'{COMMENT_LINK}'=> "<a href='".$model_url."#comment_".$data['parent_id']."'>Comment</a>"
                          ),
            $comment['User']['email']
        );
        
    }

}
?>