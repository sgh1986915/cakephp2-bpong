<?php
class ForumbranchesController extends AppController
{

    var $name = 'Forumbranches';
    var $helpers = array ('Html', 'Form', 'Time', 'Forumlinks' );

    var $uses = array ("Forumbranch", "Forumtopic");

    var $paginate = array ('limit' => 10, 'order' => array ('Forumbranch.order' => 'asc' ) );

    function beforeFilter() 
    {
        parent::beforeFilter();
        $this->set("meta_description", "Go ahead, talk trash. Just make sure you back it up at the World Series of Beer Pong. The BPONG online beer pong forum is a community where beer pong players across the globe can exchange ideas, tips, tricks, announce games, and tournaments.");
    }

    /**
     * View all forumbranches
     * @author Povstyanoy
     */
    /*
    function views() {
    $this->Access->checkAccess ( 'forumbranches', 'r' );




    $this->Forumbranch->recursive = 2;

    $this->paginate ['contain'] = array ('Lastpost' => array ('User', 'Forumtopic' ), 'User' );
    $this->set ( 'forumbranches', $this->paginate ( 'Forumbranch', array ('Forumbranch.parent_id IS NULL', 'Forumbranch.is_deleted <> 1' ) ) );


    $this->set ( 'userID', $this->Access->getLoggedUserID () );
    $this->set ( 'Updated', $this->Access->returnAccess ( 'forumbranches', 'u' ) );
    $this->set ( 'Created', $this->Access->getAccess ( 'forumbranches', 'c' ) );
    $this->set ( 'Deleted', $this->Access->returnAccess ( 'forumbranches', 'd' ) );
    }
    */


    /**
     * View list of topics and subbranches
     *
     * @author Povstyanoy
     *
     * @param int $forumbranch_id
     */
    function index() 
    {

        $parameters = $this->Forumbranch->findIdBySlug2($this->request->params['pass']);
        
        /*
        //experimental
        $to_procedure = implode('/',$this->request->params['pass']) . "/";
        $this->log("run procedure", LOG_DEBUG);
        $res_query = $this->Forumbranch->query("CALL get_forum_path( '" . $to_procedure . "' );");
        $this->log( $res_query, LOG_DEBUG );
        */    
        //die;		
        
        
        $forumbranch_id = $parameters['Forum'];
        
        if (empty($forumbranch_id) ) {
            $this->set("is_main_page", true);
        } else {
            $this->set("is_main_page", false);
        }
        
        $this->set("slug", implode("/", $this->request->params['pass']));

        $back_slug = "/";
        if (!empty($this->request->params['pass'])) {
            $back_slug = $this->request->params['pass'];
            unset($back_slug[count($back_slug)-1]);
            $back_slug = implode("/", $back_slug);
        }
        $this->set("back_slug", $back_slug);

        $this->set("forum_id", $forumbranch_id);

        /*
        if ($forumbranch_id['type'] == 'forum') {
        } else {
        return $this->redirect(array("controller" => "forumposts", "action" => "view", "/" . implode("/", $this->request->params['pass'])));
        }
        */
        $this->Access->checkAccess('forumtopics', 'r');

        $this->pageTitle = "BeerPong Forumtopics";

        /*if((int)$forumbranch_id == null) {
        return $this->redirect(array('controller'=> 'forumbranches', 'action'=>'index'));
        }*/


        //$this->set("optionlist", $forum_tree );
        // eof forum tree



        $this->Forumbranch->recursive = 2;

        if (!empty ($forumbranch_id) ) {
            //$conditions = array( 'Forumbranch.parent_id' => $forumbranch_id, 'Forumbranch.is_deleted <> 1' );
            $conditions = " Forumbranch.parent_id = $forumbranch_id AND Forumbranch.is_deleted <> 1";
        } else {
            $conditions = " Forumbranch.parent_id IS NULL AND Forumbranch.is_deleted <> 1";
        }
        /*
        $subbranches = $this->Forumbranch->find('all', array( 'conditions' => $conditions
															, 'contain' => array( 'Lastpost' => array( 'User', 'Forumtopic' ) )
																)
        );
        */    

        $subbranches = $this->Forumbranch->getBranches($conditions);
        
        
        $accessToMoveForum = $this->Access->returnAccess('forumbranches', 'u');
        
        // Remove subbranch and their childrens from selectbox for Admin (move branch)
        $all_trees = array();
        
        if ($accessToMoveForum == 'ALL') {
            //Generate forum tree for admin to move a forum or topic
            $tree_spacer = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            $forum_tree = $this->Forumbranch->generatetreelist(array('Forumbranch.is_deleted <> 1'), null, null, $tree_spacer);
            
            foreach ( $subbranches as $key => $value) {
                $current_tree = $forum_tree;
                $childrens = $this->Forumbranch->children($value['Forumbranch']['id']);
                unset( $current_tree[$value['Forumbranch']['id']] );
                foreach ($childrens as $child) {
                    if (array_key_exists($child['Forumbranch']['id'], $current_tree)) {
                        unset( $current_tree[$child['Forumbranch']['id']] );
                    }
                }
    
                $all_trees[$value['Forumbranch']['id']] = $current_tree;
            }
        
            $all_trees["All"] = $forum_tree;
        }
        
        $this->set("alltrees", $all_trees);

        //$this->Forumtopic->recursive = 2;
        //$this->paginate['contain']=array('Lastpost'=> array('User'), 'User', 'Forumbranch');
        $this->paginate['order'] = array( 'Lastpost.id' => 'desc');
        // @todo Code, listing below, generate too many sql queries!
        //		$this->paginate = array();
        /*		$forumtopics = $this->paginate('Forumtopic', array(   'Forumtopic.forumbranch_id' => (int)$forumbranch_id
															, 'Forumtopic.is_deleted <> 1' ));
        */
        $forumtopics = $this->paginate('Forumtopic', "Forumtopic.forumbranch_id = " . (int)$forumbranch_id . " AND Forumtopic.is_deleted <> 1", null, null, 10);
        // eof todo

        $this->set('forumtopics', $forumtopics);
        $this->set('subbranches', $subbranches);
        $this->request->data['Forumbranches']['id'] = $forumbranch_id;

        $this->set('userID', $this->Access->getLoggedUserID());
        //check ACCESS for Update and delete links
        $this->set('UpdatedForum', $accessToMoveForum);
        $this->set('UpdatedTopic',  $this->Access->returnAccess('forumtopics', 'u'));
        $this->set('DeletedForum', $this->Access->returnAccess('forumbranches', 'd'));
        $this->set('DeletedTopic',   $this->Access->returnAccess('forumtopics', 'd'));
        //$this->set('MoveTopic',       $this->Access->returnAccess('moveforumtopics','r'));
        //Check access for the buttons
        $this->set('CreatedForum', $this->Access->getAccess('forumbranches', 'c'));
        $this->set('CreatedTopic', $this->Access->getAccess('forumtopics', 'c'));

    }

    /**
     * Add new branch
     *
     * @author Povstyanoy
     *
     * @param int $parent_id
     */


    function add() 
    {

        $parameters = $this->Forumbranch->findIdBySlug2($this->request->params['pass']);

        $parent_id = $parameters['Forum'];

        $slug = implode("/", $this->request->params['pass']);
        $this->set("slug", $slug);
        $back_slug = "/";
        if (!empty($this->request->params['pass'])) {
            $back_slug = $this->request->params['pass'];
            unset($back_slug[count($back_slug)-1]);
            $back_slug = implode("/", $back_slug);
        }
        $this->set("back_slug", $back_slug);

        if (! empty ( $this->request->data )) {
            $captcha = $this->Session->read('captcha_text');
            if ($captcha == md5(strtolower($this->request->data['Captcha']['text']))) {
                $this->Forumbranch->create();
                $this->request->data ['Forumbranch'] ['user_id'] = $this->Session->read('loggedUser.id');
                $this->request->data ['Forumbranch'] ['name'] = trim(htmlentities($this->request->data ['Forumbranch'] ['name']));
                $this->request->data ['Forumbranch'] ['description'] = trim(htmlentities($this->request->data ['Forumbranch'] ['description']));
                if (!empty ( $parent_id )) {
                    $this->request->data ['Forumbranch'] ['parent_id'] = $parent_id;
                }
    
                if ($this->Forumbranch->save($this->request->data)) {
                    Cache::delete('forumtopics');    
                    $this->Session->setFlash('The Forumbranch has been saved', 'flash_success');
                    if (! empty ( $slug )) {
                        $this->redirect(array ( 'action' => 'index',  $slug));
                    } else {
                        $this->redirect(array ( 'action' => 'index' ));
                    }
                } else {
                    $this->Session->setFlash('The Forumbranch could not be saved. Please, try again.', 'flash_error');
                }
            } else {
                $this->Session->setFlash('Please retype blue letters.', 'flash_error');
            }            
            
            
        }
    }

    /**
     * Edit branch
     *
     * @author Povstyanoy
     *
     * @param int $id
     */
    function edit() 
    {

        $parameters = $this->Forumbranch->findIdBySlug2($this->request->params['pass']);

        $id = $parameters['Forum'];
        $slug = implode("/", $this->request->params['pass']);
        $this->set("slug", $slug);
        $back_slug = "/";
        if (!empty($this->request->params['pass'])) {
            $back_slug = $this->request->params['pass'];
            unset($back_slug[count($back_slug)-1]);
            $back_slug = implode("/", $back_slug);
        }
        $this->set("back_slug", $back_slug);

        $this->Forumbranch->id = $id;
        $this->Forumbranch->contain();
        $forumbranch = $this->Forumbranch->read(array('user_id', 'parent_id' ));
        $this->Access->checkAccess('forumbranches', 'u', $forumbranch ['Forumbranch'] ['user_id']);
        $this->set('Deleted', $this->Access->getAccess('forumbranches', 'd', $forumbranch ['Forumbranch'] ['user_id']));


        /* for redirect button on page*/
        if (!empty ( $slug )) {
            $togo = array ( 'action' => 'index', $back_slug );
        } else {
            $togo = array ( 'action' => 'index' );
        }

        $this->set('togo', $togo);


        if (! $id && empty ( $this->request->data )) {
            $this->Session->setFlash('Invalid Forumbranch', 'flash_error');
            $this->redirect(array ('action' => 'index' ));
        }
        if (! empty ( $this->request->data )) {

            $this->request->data ['Forumbranch'] ['name'] = trim(htmlentities($this->request->data ['Forumbranch'] ['name']));
            $this->request->data ['Forumbranch'] ['description'] = trim(htmlentities($this->request->data ['Forumbranch'] ['description']));

            if ($this->Forumbranch->save($this->request->data)) {
                Cache::delete('forumtopics');    
                $this->Session->setFlash('The Forumbranch has been saved', 'flash_success');

                if (empty ( $forumbranch ['Forumbranch'] ['parent_id'] )) {
                    $this->redirect($togo);
                } else {
                    $this->redirect($togo);
                }
                exit ();
            } else {
                $this->Session->setFlash('The Forumbranch could not be saved. Please, try again.', 'flash_error');
            }
        }
        if (empty ( $this->request->data )) {
            $this->request->data = $this->Forumbranch->read(null, $id);
        }
    }

    /**
     * Delete branch
     *
     * @author Povstyanoy
     *
     * @param int $id
     */
    function delete() 
    {

        $parameters = $this->Forumbranch->findIdBySlug2($this->request->params['pass']);

        $id = $parameters['Forum'];
        $slug = implode("/", $this->request->params['pass']);

        $back_slug = "/";
        if (!empty($this->request->params['pass'])) {
            $back_slug = $this->request->params['pass'];
            unset($back_slug[count($back_slug)-1]);
            $back_slug = implode("/", $back_slug);
        }


        if (! $id) {
            $this->Session->setFlash('Invalid id for Forumbranch', 'flash_error');
            $this->redirect(array ('action' => 'index' ));
            exit();
        }

        /*Check access*/
        $this->Forumbranch->id = $id;
        $this->Forumbranch->contain();
        $forumbranch = $this->Forumbranch->read(array ('user_id', 'parent_id' ));
        $this->Access->checkAccess('forumbranches', 'd', $forumbranch ['Forumbranch'] ['user_id']);
        /*EOF Check access*/

        $this->Forumbranch->branchdelete($id);
        Cache::delete('forumtopics');    

        $this->Session->setFlash('Forumbranch deleted', 'flash_success');

        $this->redirect(array ( 'action' => 'index', $back_slug ));
        exit();
    }

    /**
     * Ajax function to move branch
     *
     * @author Povstyanoy
     * @param  int nodeID id of a current node
     * @param  int wheretomoveID Id of branch where to move current node
     * @param  string nodetype Type of a node to move (topic or branch)
     */
    function ajaxmovebranch() 
    {
        Configure::write('debug', '0');
        if ($this->RequestHandler->isAjax()) {
            $this->layout = false; 
        }
        else {
            $this->layout = false; 
        }

        $data_array = array();

        $this->request->data ['wheretomoveID'] = (int) $this->request->data ['wheretomoveID'];
        $this->request->data ['nodeID'] = (int) $this->request->data ['nodeID'];

        if (!empty( $this->request->data ['wheretomoveID'])) { $data_array['wheretomoveID'] = $this->request->data ['wheretomoveID']; 
        }
        if (!empty($this->request->data ['nodetype'])) { $data_array['nodetype'] = $this->request->data ['nodetype']; 
        }
        if (!empty($this->request->data ['nodeID'])) { $data_array['nodeID'] = $this->request->data ['nodeID']; 
        }

        if (!empty( $data_array ) && count($data_array) == 3) {
            if($data_array ['nodetype'] == 'branch') {

                //Generate forum tree for admin to move a forum
                $forum_tree = $this->Forumbranch->generatetreelist(array('Forumbranch.is_deleted <> 1'));

                $subnodes = $this->Forumbranch->count_branch_subnodes($data_array ['nodeID']);

                //print_r($subnodes);

                //$all_keys = array_keys ( $forum_tree );

                if (isset ( $forum_tree [ $data_array ['nodeID'] ])) {
                    list ( $current_position ) = array_keys(array_keys($forum_tree), $data_array ['nodeID']);
                }

                if (isset ( $forum_tree [ $data_array ['wheretomoveID'] ])) {
                    list ( $needed_position ) = array_keys(array_keys($forum_tree), $data_array ['wheretomoveID']);
                }

                $this->Forumbranch->id = $data_array ['nodeID'];
                $oldbranch = $this->Forumbranch->read(null);

                $delta = abs($current_position - $needed_position);

                if ($current_position > $needed_position) {
                    echo "moveUp";
                    $this->Forumbranch->moveUp($data_array ['nodeID'], $delta);
                } else {
                    echo "moveDown";
                    $this->Forumbranch->moveDown($data_array ['nodeID'], $delta);
                }

                //print_r($delta);

                $this->Forumbranch->id = $data_array ['nodeID'];
                $this->Forumbranch->saveField("parent_id", $data_array ['wheretomoveID']);

                //Old forumbranch update fields lastpost_id, topiccount, postscount
                $this->Forumbranch->update_branch_parents(
                    $oldbranch['Forumbranch']['parent_id'], $subnodes['topicsCount'], $subnodes['postsCount'] 
                );

                $this->Forumbranch->update_lastpostID(
                    $oldbranch['Forumbranch']['parent_id'], $oldbranch['Forumbranch']['lastpost_id'] 
                );

                //New forumbranch update fields lastpost_id, topiccount, postscount
                $this->Forumbranch->update_branch_parents(
                    $data_array ['wheretomoveID'], "-" . $subnodes['topicsCount'], "-" . $subnodes['postsCount'] 
                );

                $this->Forumbranch->updatetopic_lastpostID(
                    $data_array ['wheretomoveID'], $oldbranch['Forumbranch']['lastpost_id'] 
                );

            } else {
                //echo "topic";
                $this->Forumbranch->Forumtopic->id = $data_array ['nodeID'];
                $oldtopic = $this->Forumbranch->Forumtopic->read(null);

                $postscount = $this->Forumbranch->query(
                    "
											SELECT count(posts.id) AS postscount
											FROM forumposts as posts
											WHERE posts.is_deleted <> 1
												AND posts.forumtopic_id = " . $data_array ['nodeID']
                );

                $postscount = $postscount [0] [0] ['postscount'];
                //echo $postscount;
                $this->Forumbranch->Forumtopic->saveField("forumbranch_id", $data_array ['wheretomoveID']);

                //Old forumbranch update fields lastpost_id, topiccount, postscount
                $this->Forumbranch->update_branch_parents(
                    $oldtopic['Forumtopic']['forumbranch_id'], 1, $postscount 
                );

                $this->Forumbranch->update_lastpostID(
                    $oldtopic['Forumtopic']['forumbranch_id'], $oldtopic['Forumtopic']['lastpost_id'] 
                );

                //New forumbranch update fields lastpost_id, topiccount, postscount
                $this->Forumbranch->update_branch_parents(
                    $data_array ['wheretomoveID'], '-1', "-$postscount" 
                );

                $this->Forumbranch->updatetopic_lastpostID(
                    $data_array ['wheretomoveID'], $oldtopic['Forumtopic']['lastpost_id'] 
                );
            }

        }
        exit();
    }
    /*
    function findIdBySlug($slugs = array()) {

    $slug = "/" . implode("/", $slugs);
    $out = array();

    $forumbranch = $this->Forumbranch->find('first',array('conditions' => array(
																	  'Forumbranch.deleted IS NULL'
																	, 'Forumbranch.slug' 	=> 	$slug
																	)
													)
    );
    if ( empty($$forumbranch) ) {
    //$counter = count($slugs);
    $topic = $slugs[ count($slugs) - 1 ];

    unset( $slugs[ count($slugs) - 1 ] );

    $slug = "/" . implode("/", $slugs);

    $forum_id = $this->Forumbranch->find('first',array('conditions' => array(
																		  'Forumbranch.deleted IS NULL'
																		, 'Forumbranch.slug' 	=> 	$slug
																		)
														)
    );

    $topic_id = $this->Forumtopic->find('first',array('conditions' => array(
																		  'Forumtopic.deleted IS NULL'
																		, 'Forumtopic.parent_id' => $forum_id
																		, 'Forumtopic.slug' 	=> 	$topic
																		)
														)
    );
    if ($topic_id != null) {
				$out['type'] = 'topic';
				$out['id'] = $topic_id['Forumtopic']['id'];
    }
    } else {
    $out['type'] = 'forum';
    $out['id'] = $forumbranch['Forumbranch']['id'];
    }

    return $out;
    }
    */
    function generateslug( $password = "") 
    {
        
        if ($password != "may_become_hot") {
            echo "Password is invalid.";
            die;
        }

        $this->Forumbranch->contain();
        $forums = $this->Forumbranch->find('all', array("conditions" => array("Forumbranch.is_deleted <> 1")));

        foreach ( $forums as $forum ) {
            /*
            $id = $forum['Forumbranch']['id'];
            $update_branches = $this->Forumbranch->getpath ( $id );
            $new_slug = "";

            foreach ( $update_branches as $value ) {
            $string = strip_tags( $value ['Forumbranch'] ['name'] );
            $string = eregi_replace("&([a-z])[a-z0-9]{3,};", "", $string);
            $string = str_replace( " ", "_", $string );

            $new_slug .= "/" . $string;
            unset($string);
            }
            */
            $this->Forumbranch->id = $forum['Forumbranch']['id'];
            $this->Forumbranch->saveField('name', $forum['Forumbranch']['name']);

        }
        echo "Slugs was generated";
        die;

    }

}
?>
