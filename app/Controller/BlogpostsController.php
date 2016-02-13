<?php
class BlogpostsController extends AppController
{

    var $name = 'Blogposts';

    var $paginate = array(
         'limit'        => 10,
         'page'         => 1,
         'order'        => array('Blogpost.created' => 'DESC')
     );

    var $helpers = array('Html', 'Form', 'Time', 'Bbcode','Image');
    var $uses = array('Blogpost','Image');
    /**
     * Set title and metatag description for blog before each action
     */
    function beforeFilter() 
    {
        parent::beforeFilter();
        $this->pageTitle = 'Beer Pong Blog | Read About News, Events, and Beer Pong  Tournaments | BPONG.COM';
        $this->set('meta_description', 'Get your Beer Pong information straight from the source - BPONG.COM. Our Beer Pong blog covers the latest in beer pong related news, stories, and other fun shit.  Read on, be smarter (not really).');
    }
    /**
     * Index action: dislays first n posts
     * @author Edward
     */
    function index() 
    {
        $this->components [] = 'RequestHandler';
        $this->Access->checkAccess('blogposts', 'r');
        $this->Blogpost->recursive = 0;
        
        $userId = $this->Access->getLoggedUserID();
         
        if($this->RequestHandler->isRss() ) {
            Configure::write('debug', 0);
            $blogposts = $this->Blogpost->find(
                'all',
                array(
                'conditions' => array(
                'Blogpost.is_deleted' => '0'
                ),
                'limit' => 20,
                'order' => 'Blogpost.created DESC'
                )
            );
            $this->set(compact('blogposts'));
        } else {
            $blogposts =  $this->paginate('Blogpost', array("Blogpost.is_deleted = '0'"));
        
            App::import('Model', 'Comment');
            $Comment = new Comment();
            foreach ($blogposts as $key => $blogpost) {
                $blogposts[$key]['Comments'] = $Comment->getCommentsTree('Blogpost', $blogpost['Blogpost']['id'], 3);
                $blogposts[$key]['commentVotes'] = $Comment->Vote->getCommentVotes('Blogpost', $blogpost['Blogpost']['id'], $userId);            
            }
            //pr($blogposts);
            //exit;
            $this->set('blogposts', $blogposts);

            //$this->set('comments', $Comment->getCommentsTree('Blogpost',$this->request->data['Blogpost']['id']));
        
        
            $userId = $this->Access->getLoggedUserID();
            $blogpostIds = Set::extract($blogposts, '/Blogpost/id');
            $votes = $Comment->Vote->getVotes('Blogpost', $blogpostIds, $userId);
            $this->set('votes', $votes);
            //security
            $this->set('canEdit', $this->Access->getAccess('blogposts', 'u'));
            $this->set('canAdd', $this->Access->getAccess('blogposts', 'c'));
            $this->set('canDelete', $this->Access->getAccess('blogposts', 'd'));
            $this->set('canVoteBlogpost', $this->Access->returnAccess('Vote_Blogpost', 'c'));
            $this->set('canVoteComment', $this->Access->returnAccess('Vote_Comment', 'c'));
            $this->set('canComment', $this->Access->returnAccess('Comment_Blogpost', 'c'));
            $this->set('canDeleteComment', $this->Access->returnAccess('Comment', 'd'));        
        }
    }

    /**
     * Add new blog post
     * @author Edward
     */
    function add() 
    {
        $this->Access->checkAccess('blogposts', 'c');

        if (!empty($this->request->data)) {
            $this->Blogpost->create();
            $this->request->data['Blogpost']['user_id'] = $this->Access->getLoggedUserID();
            if ($this->Blogpost->save($this->request->data)) {
                Cache::delete('last_blogposts');
                $this->Session->setFlash('Post has been saved', 'flash_success');
                return $this->redirect(Router::url(array('action'=>'index')));
            } else {
            }
        }

        //$users = $this->Blogpost->User->find('list');
        //$this->set(compact('users'));
    }

    /**
     * Edit post
     *
     * @param  id - blog post id
     * @author Edward
     */
    function edit($id = null) 
    {
        $this->Access->checkAccess('blogposts', 'u');

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash('Invalid post', 'flash_error');
            return $this->redirect(Router::url(array('action'=>'index')));
        }
        if (!empty($this->request->data)) {

            //$this->request->data['Blogpost']['user_id'] = $this->Access->getLoggedUserID();
            if ($this->Blogpost->save($this->request->data)) {
                Cache::delete('last_blogposts');
                $this->Session->setFlash('Post has been saved', 'flash_success');
            } else {
                $this->Session->setFlash('Post could not be saved. Please, try again.', 'flash_error');
            }

            return $this->redirect(Router::url(array('action'=>'index')));
        }

        if (empty($this->request->data)) {
            $this->request->data = $this->Blogpost->read(null, $id);
        }
         $images = $this->Image->myImages('Blogpost', $id);

         $this->set('images', $images);
    }
    /**
 * View post
 * @param unknown_type $slug
 * @return unknown_type
 */
    function view($slug = null) 
    {
        if (!$slug) {
            $this->Session->setFlash('Invalid post', 'flash_error');
            return $this->redirect(Router::url(array('action'=>'index')));
        }

        $this->request->data = $this->Blogpost->find('first', array('contain'=>array(),'conditions'=>array('slug'=>$slug)));

        if (empty($this->request->data)) {
            $this->Session->setFlash('Invalid post', 'flash_error');
            return $this->redirect(Router::url(array('action'=>'index')));
        }

        App::import('Model', 'Comment');
        $Comment = new Comment();
        $userId = $this->Access->getLoggedUserID();
        $this->set('comments', $Comment->getCommentsTree('Blogpost', $this->request->data['Blogpost']['id']));
        $this->set('commentVotes', $Comment->Vote->getCommentVotes('Blogpost', $this->request->data['Blogpost']['id'], $userId));
        $this->set('votes', $Comment->Vote->getVotes('Blogpost', $this->request->data['Blogpost']['id'], $userId));
        $this->set('canVoteBlogpost', $this->Access->returnAccess('Vote_Blogpost', 'c'));
        $this->set('canVoteComment', $this->Access->returnAccess('Vote_Comment', 'c'));
        $this->set('canComment', $this->Access->returnAccess('Comment_Blogpost', 'c'));
        $this->set('canDeleteComment', $this->Access->returnAccess('Comment', 'd'));
         //$images = $this->Image->myImages('Blogpost', $id);
         //$this->set('images',$images);
    }
    /**
     * Deleted blog post
     *
     * @param  id of the post
     * @author Edward
     */
    function delete($id = null) 
    {
        $this->Access->checkAccess('blogposts', 'd');
        if (!$id) {
            $this->Session->setFlash('Invalid post', 'flash_error');
        }

        $this->request->data['Blogpost']['id']         = $id;
        $this->request->data['Blogpost']['is_deleted'] = 1;
        $this->request->data['Blogpost']['deleted']    = date('Y-m-d H:i:s');

        if ($this->Blogpost->save($this->request->data)) {
              Cache::delete('last_blogposts');
            $this->Session->setFlash('Post deleted', 'flash_success');
        }

        $this->redirect(Router::url(array('action'=>'index')));
    }


}
?>
