<?php
class ForumtopicsController extends AppController
{

    var $name = 'Forumtopics';
    var $helpers = array('Html', 'Form', 'Time', 'Forumlinks');
    var $uses = array('Forumpost', 'Forumtopic', 'Forumbranch');

    var $components = array('Cookie');

    function beforeFilter() 
    {
        parent::beforeFilter();
        $this->set("meta_description", "Go ahead, talk trash. Just make sure you back it up at the World Series of Beer Pong. The BPONG online beer pong forum is a community where beer pong players across the globe can exchange ideas, tips, tricks, announce games, and tournaments.");
    }


    /**
     * View list of topics and subbranches
     *
     * @author Povstyanoy
     *
     * @param int $forumbranch_id
     */
    /*	function index( $forumbranch_id = null ) {
    $this->Access->checkAccess('forumtopics','r');

    $this->pageTitle = "BeerPong Forumtopics";

    if((int)$forumbranch_id == null) {
    return $this->redirect(array('controller'=> 'forumbranches', 'action'=>'index'));
    }

    //Generate forum tree for admin to move a forum or topic
    $tree_spacer = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    $forum_tree = $this->Forumtopic->Forumbranch->generatetreelist(array('Forumbranch.is_deleted <> 1'), null, null, $tree_spacer);

    //$this->set("optionlist", $forum_tree );
    // eof forum tree



    $this->Forumtopic->Forumbranch->recursive = 2;
    $subbranches = $this->Forumtopic->Forumbranch->find('all', array('conditions' => array(
																						  'Forumbranch.parent_id' => (int)$forumbranch_id
																						, 'Forumbranch.is_deleted <> 1' )
																		, 'contain' => array( 'Lastpost' => array( 'User', 'Forumtopic' ) )
																		)
															);
    // Remove subbranch and their childrens from selectbox for Admin (move branch)
    $all_trees = array();
    foreach ( $subbranches as $key => $value) {
    $current_tree = $forum_tree;
    $childrens = $this->Forumtopic->Forumbranch->children( $value['Forumbranch']['id'] );
    unset( $current_tree[$value['Forumbranch']['id']] );
    foreach ($childrens as $child) {
				if (array_key_exists($child['Forumbranch']['id'],$current_tree)) {
					unset( $current_tree[$child['Forumbranch']['id']] );
				}
    }

    $all_trees[$value['Forumbranch']['id']] = $current_tree;
    }

    $all_trees["All"] = $forum_tree;

    $this->set("alltrees", $all_trees );

    $this->Forumtopic->recursive = 2;
    $this->paginate['contain']=array('Lastpost'=>array('User'),'User');
    $forumtopics = $this->paginate('Forumtopic', array(   'Forumtopic.forumbranch_id' => (int)$forumbranch_id
															, 'Forumtopic.is_deleted <> 1' ));

    $this->set('forumtopics', $forumtopics);
    $this->set('subbranches', $subbranches);
    $this->request->data['Forumbranches']['id'] = $forumbranch_id;

    $this->set('userID', $this->Access->getLoggedUserID());
    //check ACCESS for Update and delete links
    $this->set('UpdatedForum',$this->Access->returnAccess('forumbranches','u'));
    $this->set('UpdatedTopic',  $this->Access->returnAccess('forumtopics','u'));
    $this->set('DeletedForum', $this->Access->returnAccess('forumbranches','d'));
    $this->set('DeletedTopic',   $this->Access->returnAccess('forumtopics','d'));
    $this->set('MoveTopic',       $this->Access->returnAccess('moveforumtopics','r'));
    //Check access for the buttons
    $this->set('CreatedForum',$this->Access->getAccess('forumbranches','c'));
    $this->set('CreatedTopic',$this->Access->getAccess('forumtopics','c'));
    }
    */
    /**
     * Add forum topic
     *
     * @author Povstyanoy
     *
     * @param int $forumbranch_id
     */
    function add() 
    {

        $parameters = $this->Forumbranch->findIdBySlug2($this->request->params['pass']);

        $forumbranch_id = $parameters['Forum'];

        $slug = implode("/", $this->request->params['pass']);
        $this->set("slug", $slug);

        $back_slug = "/";
        if (!empty($this->request->params['pass'])) {
            $back_slug = $this->request->params['pass'];
            unset($back_slug[count($back_slug)-1]);
            $back_slug = implode("/", $back_slug);
        }
        $this->set("back_slug", $back_slug);


        $this->pageTitle = "Add topic";
        $this->Access->checkAccess('forumtopics', 'c');
        if (!empty($this->request->data)) {
            $captcha = $this->Session->read('captcha_text');
            if ($captcha == md5(strtolower($this->request->data['Captcha']['text']))) {
                $user_id = $this->Session->read('loggedUser.id');
                $this->Forumtopic->create();
                $this->request->data['Forumtopic']['user_id'] = $user_id;
                $this->request->data['Forumtopic']['forumbranch_id'] = $forumbranch_id;
                $this->request->data['Forumtopic']['name'] = htmlentities($this->request->data['Forumtopic']['name']);
                $this->request->data['Forumtopic']['description'] = htmlentities($this->request->data['Forumtopic']['description']);
    
                if ($this->Forumtopic->save($this->request->data)) {
                    Cache::delete('forumtopics');                
                    /**
                     * @var $temp create temporary post data to save
                     */
                    $temp['Forumpost']['forumtopic_id'] = $this->Forumtopic->getLastInsertID();
                    $temp['Forum']['actiontype'] = 'add';
                    $temp['Forumpost']['user_id'] = $user_id;
                    $temp['Forumpost']['ip'] = $_SERVER['REMOTE_ADDR'];
                    $temp['Forumpost']['text'] = $this->request->data['Forumpost']['text'];
    
                    //This var used in model
                    $temp['Forumtopic']['forumbranch_id'] = $forumbranch_id;
    
                    if ($this->Forumpost->save($temp)) {
                        Cache::delete('forumtopics');
                        $this->Session->setFlash('The Forumtopic has been saved', 'flash_success');
                    } else {
                        $this->Forumtopic->del($temp['Forumpost']['forumtopic_id']);
                        $this->Session->setFlash('The Forumtopic could not be saved. Please, try again later.', 'flash_error');
                    }
                    return $this->redirect(array( 'controller' => 'forumbranches', 'action'=>'index', $slug ));
                    exit();
    
                } else {
                    $this->Session->setFlash('The Forumtopic could not be saved. Please, try again.', 'flash_error');
                }
                        
            } else {
                $this->Session->setFlash('Please retype blue letters.', 'flash_error');
            }                        
        }
        if ((int)$forumbranch_id == null) {
            return $this->redirect(array('controller' => 'forumbranches'));
        }

        //
    }

    /**
     * Edit forum topic
     *
     * @author Povstyanoy
     *
     * @param int $id
     */
    function edit() 
    {

        $parameters = $this->Forumtopic->findIdBySlug2($this->request->params['pass']);

        $id = $parameters['Topic'];
        $slug = implode("/", $this->request->params['pass']);
        $this->set("slug", $slug);
        $back_slug = "/";
        if (!empty($this->request->params['pass'])) {
            $back_slug = $this->request->params['pass'];
            unset($back_slug[count($back_slug)-1]);
            $back_slug = implode("/", $back_slug);
        }
        $this->set("back_slug", $back_slug);


        $this->pageTitle = "Edit topic";

        $this->Forumtopic->contain();

        $forumtopic = $this->Forumtopic->read(null, $id);

        if (empty( $forumtopic ) && empty( $this->request->data) ) {

            $this->Session->setFlash('Invalid Forumtopic', 'flash_error');

            return $this->redirect(array( 'controller' => 'forumbranches', 'action' => 'index' ));

        }

        //Security
        $this->Access->checkAccess('forumtopics', 'u', $forumtopic['Forumtopic']['user_id']);
        $this->set('Deleted', $this->Access->getAccess('forumtopics', 'd', $forumtopic['Forumtopic']['user_id']));

        if (!empty($this->request->data)) {

            $this->request->data['Forumtopic']['name'] = htmlentities($this->request->data['Forumtopic']['name']);
            $this->request->data['Forumtopic']['description'] = htmlentities($this->request->data['Forumtopic']['description']);

            if ($this->Forumtopic->save($this->request->data)) {
                Cache::delete('forumtopics');
                $this->Session->setFlash('The Forumtopic has been saved', 'flash_success');
            } else {
                $this->Session->setFlash('The Forumtopic could not be saved. Please, try again.', 'flash_error');
            }

            return $this->redirect(array('controller' => 'forumbranches', 'action'=>'index', $back_slug));
            exit();
        }
        if (empty($this->request->data)) {
            $this->request->data = $forumtopic;
        }
    }

    /**
     * Delete forum topic
     *
     * @author Povstyanoy
     *
     * @param int $id
     */
    function delete() 
    {

        $parameters = $this->Forumtopic->findIdBySlug2($this->request->params['pass']);


        $id = $parameters['Topic'];
        $slug = implode("/", $this->request->params['pass']);
        $this->set("slug", $slug);

        $back_slug = "/";
        if (!empty($this->request->params['pass'])) {
            $back_slug = $this->request->params['pass'];
            unset($back_slug[count($back_slug)-1]);
            $back_slug = implode("/", $back_slug);
        }

        if (!$id) {
            $this->Session->setFlash('Invalid id for Topic', 'flash_error');
            return $this->redirect(array('controller' => 'forumbranches', 'action'=>'index'));
            exit();
        }
        //Security
        $this->Forumtopic->contain();
        $forumtopic = $this->Forumtopic->read(null, (int)$id);
        $this->Access->checkAccess('forumtopics', 'd', $forumtopic['Forumtopic']['user_id']);

        $forum_id = $forumtopic['Forumtopic']['forumbranch_id'];

        if ($this->Forumtopic->_deleteTopic($id)) {
            Cache::delete('forumtopics');    
            $this->Session->setFlash('Topic deleted', 'flash_success');
            return $this->redirect(array( 'controller' => 'forumbranches', 'action'=>'index', $back_slug ));
            exit();
        }

        $this->Session->setFlash('Topic not deleted', 'flash_error');
        return $this->redirect(array('controller' => 'forumbranches', 'action'=>'index'));
        exit();

    }

    function generateslug( $password = "") 
    {

        if ($password != "may_become_hot") {
            echo "Password is invalid.";
            die;
        }

        $this->Forumtopic->contain();
        $forums = $this->Forumtopic->find('all', array("conditions" => array("Forumtopic.is_deleted <> 1")));

        foreach ( $forums as $forum ) {
            $newforums['Forumtopic']['id'] = $forum['Forumtopic']['id'];
            $newforums['Forumtopic']['modified'] = $forum['Forumtopic']['modified'];
            $newforums['Forumtopic']['name'] = $forum['Forumtopic']['name'];
            $this->Forumtopic->save($newforums, false);
            Cache::delete('forumtopics');
            unset($newforums);
        }
        echo "Completed";
                die;

    }
}
?>
