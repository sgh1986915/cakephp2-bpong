<?php
/**
 * Posts controller
 * @author Povstyanoy
 */
class ForumpostsController extends AppController
{

    var $name = 'Forumposts';
    var $helpers = array('Html', 'Form', 'Time', 'Forumlinks', 'Bbcode');
    var $uses = array('Forumpost', 'Forumbranch', 'Forumtopic');
    var $post_per_page = 10;

    function beforeFilter() 
    {
        parent::beforeFilter();
        $this->set("meta_description", "Go ahead, talk trash. Just make sure you back it up at the World Series of Beer Pong. The BPONG online beer pong forum is a community where beer pong players across the globe can exchange ideas, tips, tricks, announce games, and tournaments.");
    }

    /**
     * Display all posts of the topic
     *
     * @author Povstyanoy
     * @param  int $forumbranch_id ID of the forum branch
     * @param  int $forumtopic_id  ID of the forum topic
     */
    function index() 
    {
        $topic_slug = $this->request->params['pass'][count($this->request->params['pass']) -1 ];

        $topic = $this->Forumtopic->findTopicIdBySlugForPost($topic_slug);

        $forumtopic_id = $topic[0]['Forumtopic']['id'];

        $this->set("topicname", $topic[0]['Forumtopic']['name']);
        $this->set("branchleft", $topic[0]['Forumbranch']['lft']);
        $this->set("branchright", $topic[0]['Forumbranch']['rght']);

        $slug = implode("/", $this->request->params['pass']);
        $this->set("slug", $slug);

        $back_slug = "/";
        if (!empty($this->request->params['pass'])) {
            $back_slug = $this->request->params['pass'];
            unset($back_slug[count($back_slug)-1]);
            $back_slug = implode("/", $back_slug);
        }
        $this->set("back_slug", $back_slug);

        /*Security*/
        $this->Access->checkAccess('forumposts', 'r');
        $this->set('userID', $this->Access->getLoggedUserID());
        $this->set('Updated', $this->Access->returnAccess('forumposts', 'u'));
        $this->set('Created', $this->Access->getAccess('forumposts', 'c'));
        $this->set('Deleted', $this->Access->returnAccess('forumposts', 'd'));

        /**
         * Check for branch and topic exists
         */
        /*$this->Forumbranch->recursive = -1;
        $existbranch = $this->Forumbranch->find( 'all',array('conditions' => array ('Forumbranch.id' => (int)$forumbranch_id, 'Forumbranch.deleted IS NULL'))  );
        $this->Forumtopic->recursive = -1;
        $existtopic  = $this->Forumtopic->find( 'all', array('conditions' => array ('Forumtopic.id' => (int)$forumtopic_id, 'Forumtopic.deleted IS NULL'))  );
        */
        /*if (empty($forumbranch_id) && empty($forumtopic_id)){
        return $this->redirect('/forumbranches');
        } elseif (empty($forumtopic_id) && $forumbranch_id) {
        return $this->redirect('/forumbranches/index/' . w);
        }*/

        /**
         * EOF Check
        */

        $criteria = array( 'Forumpost.forumtopic_id' => $forumtopic_id, 'Forumpost.is_deleted <> 1');

        $firstpostId = $this->Forumpost->field('id', $criteria, 'Forumpost.id ASC');
        $this->set('firstPostId', $firstpostId);

        //$this->Forumpost->recursive = 0;
        $forumposts = $this->paginate('Forumpost', "Forumpost.forumtopic_id = $forumtopic_id AND Forumpost.is_deleted <> 1");
        
        $usersInfo = array();
        foreach ($forumposts as $forumKey => $forumpost) {
            if (!isset($usersInfo[$forumpost['User']['id']])) {
                $usersInfo[$forumpost['User']['id']] =     $this->Forumpost->User->Team->getPlayerStats($forumpost['User']['id']);        
            }
            $forumposts[$forumKey]['User']['gameInfo'] = $usersInfo[$forumpost['User']['id']];            
        }
        
        $this->set('forumposts', $forumposts);

        //$this->request->data['Forumtopic']['forumbranch_id'] = $forumbranch_id;
        //$this->request->data['Forumtopic']['forumtopic_id'] = $forumtopic_id;

        //increment view counter current topic
        $this->Forumpost->incrementViewCounter($forumtopic_id);

    }

    /**
     * Add new post
     *
     * @author Povstyanoy
     *
     * @param int $forumbranch_id ID of the forum branch
     * @param int $forumtopic_id  ID of the forum topic
     */
    function add() 
    {
        $url_slug = $this->request->params['pass'];
        $post_id_to_quote = $this->_getpostID($url_slug);
        // slug contain ID of post
        if ((int) $post_id_to_quote > 0 ) {
            unset($url_slug[ count($url_slug) - 1 ]);
        }
        //eof

        //remove postID from slug

        $parameters = $this->Forumtopic->findIdBySlug2($url_slug);

        $forumbranch_id = $parameters['Forum'];
        $forumtopic_id = $parameters['Topic'];
        $slug = implode("/", $url_slug);
        $this->set("slug", $slug);

        $back_slug = "/";
        if (!empty($url_slug)) {
            $back_slug = $url_slug;
            unset($back_slug[count($back_slug)-1]);
            $back_slug = implode("/", $back_slug);
        }
        $this->set("back_slug", $back_slug);

        $this->Access->checkAccess('forumposts', 'c');

        if (!empty( $this->request->data ) ) {
            $captcha = $this->Session->read('captcha_text');
            if ($captcha == md5(strtolower($this->request->data['Captcha']['text']))) {
                
                $this->Forumpost->create();
                $this->request->data['Forumpost']['user_id'] = $this->Session->read('loggedUser.id');
                $this->request->data['Forumpost']['ip'] = $_SERVER['REMOTE_ADDR'];
                $this->request->data['Forumpost']['text'] = strip_tags($this->request->data['Forumpost']['text']);
                $this->request->data['Forumpost']['text'] = htmlentities($this->request->data['Forumpost']['text'], ENT_QUOTES);
                $this->request->data['Forumpost']['text'] = $this->cut_long_words_from_post($this->request->data['Forumpost']['text']);
    
                $this->request->data['Forumtopic']['forumbranch_id'] = $forumbranch_id;
                $this->request->data['Forumpost']['forumtopic_id'] = $forumtopic_id;
    
                if ($this->Forumpost->save($this->request->data)) {
                    Cache::delete('forumtopics');    
                    $post_id = $this->Forumpost->getLastInsertID();
                    $pagenum = $this->_post_on_page($post_id);
                    //			$this->tempSendMessageToSkinny($this->request->data);
                    return $this->redirect(array('action'=>'index', $slug, "page:" . $pagenum['page'], "#post_" . $pagenum['id']));
                    
                } else {
                    $this->Session->setFlash('Post could not be saved. Please, try again.', 'flash_error');
                    $this->tempSendMessageToSkinny($this->request->data);
                }
        
        
            } else {
                $this->Session->setFlash('Please retype blue letters.', 'flash_error');
            }    
        }    
    
        /**
             * Check for branch and topic exists
             */
    
        $existbranch = $this->Forumbranch->read(null, (int)$forumbranch_id);
        $existtopic = $this->Forumtopic->read(null, (int)$forumtopic_id);
    
        /*
        if ($existbranch == null && $existtopic == null){
        return $this->redirect('/forumbranches');
        } elseif ($existtopic == null && $existbranch) {
        return $this->redirect('/forumbranches/index/' . $forumbranch_id);
        }
        **
        * EOF Check
        */
    
        if ((int)$post_id_to_quote != null) {
            $post_to_add = $this->Forumpost->read(null, $post_id_to_quote);
            $this->request->data['Forumpost']['text'] =
            '[quote="' . $post_to_add['User']['lgn'] . '"]' . html_entity_decode(html_entity_decode($post_to_add['Forumpost']['text'], ENT_QUOTES), ENT_QUOTES) . '[/quote]';
        }
        $this->set("topic_name", $existtopic ['Forumtopic'] ['name']);
    
        $this->request->data['Forumtopic']['forumbranch_id'] = $forumbranch_id;
        $this->request->data['Forumpost']['forumtopic_id'] = $forumtopic_id;
    }
    function tempSendMessageToSkinny($array) 
    {
        App::import('Vendor', 'PHPMailer', array('file' => 'mailer.class.php'));
         $mailer = new PHPMailer();
         //$mailer->From = 'no-reply@bpong.com';
         
         $emailto = 'skinny@bpong.com';
         $emailto = trim($emailto);
         $mailer->AddAddress($emailto, $emailto);             
         $mailer->CharSet = 'utf-8';
         $mailer->Subject = 'BPONG dump of email message';
         $mailer->Body    =  implode($array);
        //	 $mailer->Body = 'sdafsdfa';
         $mailer->From = 'no-reply@bpong.com';
             $mailer->FromName = 'BPONG';
        $mailer->ContentType = 'text/html';        
         return $mailer->Send();
    }

    /**
     * Edit Post with $id
     *
     * @author Povstyanoy
     *
     * @param int $id Post ID
     */


    function edit() 
    {

        $url_slug = $this->request->params['pass'];
        $id = $this->_getpostID($url_slug);
        // slug contain ID of post
        if ((int) $id <= 0 ) {
            $this->Session->setFlash('Post ID is not exist or invalid.', 'flash_error');
            return $this->redirect("/");
            exit();
        }
        //eof

        $this->set("slug", implode("/", $this->request->params['pass']));

        $back_slug = "/";
        if (!empty($this->request->params['pass'])) {

            $back_slug = $this->request->params['pass'];
            unset($back_slug[count($back_slug)-1]);

            $back_back_slug = $back_slug;
            unset( $back_back_slug[ count($back_back_slug) - 1 ] );

            $back_slug = implode("/", $back_slug);

            $back_back_slug = implode("/", $back_back_slug);
        }
        $this->set("back_slug", $back_slug);
        $this->set("back_back_slug", $back_back_slug);

        $forumpost = $this->Forumpost->read(null, (int)$id);
        if (empty($forumpost) && empty($this->request->data)) {
            $this->Session->setFlash('Invalid Forumpost', 'flash_error');
            return $this->redirect(array('action'=>'index'));
        }

        $this->Access->checkAccess('forumposts', 'u', $forumpost['Forumpost']['user_id']);
        $this->set('Deleted', $this->Access->getAccess('forumposts', 'd', $forumpost['Forumpost']['user_id']));

        if (!empty($this->request->data)) {

            $this->request->data['Forumpost']['text'] = strip_tags($this->request->data['Forumpost']['text']);
            $this->request->data['Forumpost']['text'] = htmlentities($this->request->data['Forumpost']['text'], ENT_QUOTES);
            $this->request->data['Forumpost']['text'] = $this->cut_long_words_from_post($this->request->data['Forumpost']['text']);
            
            if ($this->Forumpost->save($this->request->data)) {
                Cache::delete('forumtopics');    
                $this->Session->setFlash('Post has been edited', 'flash_success');
            } else {
                $this->Session->setFlash('Post could not be edited. Please, try again.', 'flash_error');
            }

            $pagenum = $this->_post_on_page($id);

            return $this->redirect(array('action'=>'index', $back_slug,"page:" . $pagenum['page'], "#post_" . $pagenum['id']));
        }

        $this->request->data = $forumpost;
        $this->request->data['Forumpost']['text'] = html_entity_decode($this->request->data['Forumpost']['text']);
    }

    /**
     * Delete post
     *
     * @author Povstyanoy
     *
     * @param int $id Post ID
     */
    function delete() 
    {
        /*Security*/

        $url_slug = $this->request->params['pass'];
        $id = $this->_getpostID($url_slug);
        // slug contain ID of post
        if ((int) $id <= 0 ) {
            $this->Session->setFlash('Post ID is not exist or invalid.', 'flash_error');
            return $this->redirect("/");
            exit();
        }
        //eof
        unset( $url_slug [ count($url_slug) - 1 ] );
        $slug = implode("/", $url_slug);
        unset($url_slug);


        $forumpost = $this->Forumpost->read(null, (int)$id);
        if (empty($forumpost) && empty($this->request->data)) {
            $this->Session->setFlash('Invalid Post', 'flash_error');
            return $this->redirect(array('action'=>'index'));
        }
        $this->Access->checkAccess('forumposts', 'd', $forumpost['Forumpost']['user_id']);
        /*EOF SEcurity*/

        if (!$id) {
            $this->Session->setFlash('Invalid id for Post', 'flash_error');
            return $this->redirect(array('action'=>'index'));
        }

        $forumpost = $this->Forumpost->read(null, $id);

        $pagenum = $this->_post_on_page($id, 'delete');

        if ($this->Forumpost->_deletePost($id)) {
            Cache::delete('forumtopics');    
            $this->Session->setFlash('Post deleted', 'flash_success');
        } else {
            $this->Session->setFlash('Forum Topic has been deleted', 'flash_success');
            return $this->redirect('/nation/beer-pong-forum');
        }

        return $this->redirect(array('action'=>'index', $slug,"page:" . $pagenum['page'], "#post_" . $pagenum['id']));
    }

    function _post_on_page( $post_id = null , $action = "add_edit") 
    {

        $forumpost = $this->Forumpost->read('forumtopic_id', $post_id);

        $posts_count = $this->Forumpost->find(
            'all', array('conditions' => array('forumtopic_id' => $forumpost['Forumpost']['forumtopic_id']
                                                                                ,'Forumpost.is_deleted <> 1'))
        );
        $postindex = null;
        foreach($posts_count as $index => $value) {
            if (($value['Forumpost']['id'] == $post_id) && ($postindex == null)) {
                if($action == "delete") {
                    $postindex = $index - 1;
                    $new_id = $posts_count[$index - 1 ]['Forumpost']['id'];
                } else {
                    $postindex = $index;
                    $new_id = $post_id;
                }
            }
        }
        if($postindex != null) {
            $pagenum = ceil(($postindex + 1) / $this->post_per_page);
        } else {
            $pagenum = 1;
        }
        return array('id' => $new_id, 'page' => $pagenum);
    }

    function _getpostID( $url_slug ) 
    {

        // slug contain ID of post
        if (preg_match('/^\d+$/', $url_slug[ count($url_slug) - 1 ], $matches) ) {
            $post_id = (int) $matches[0];
            // delete post from array
            unset ( $url_slug[ count($url_slug) - 1 ] );
            $post = $this->Forumpost->find(
                'first', array('conditions' => array(
                                                                          'Forumpost.is_deleted <> 1'
                                                                        , 'Forumpost.id'     =>     $post_id
                                                                        )
                                                        )
            );
            if (!empty( $post['Forumpost']['id'] ) ) {
                return $post['Forumpost']['id'];
            } else {
                return 'Post ID is not exist.';
            }
        } else {
            return 'Post ID is wrong.';
        }
        //eof

        return false;
    }
    

    function unicode_wordwrap($str, $len=50, $break=" ", $cut=false)
    { 
        if(empty($str)) { return ""; 
        } 
        
        $pattern=""; 
        if(!$cut) { 
            $pattern="/(\S{".$len."})/u"; 
        } 
        else { 
            $pattern="/(.{".$len."})/u"; 
        } 
        
        return preg_replace($pattern, "\${1}".$break, $str); 
    } 
    
    function cut_long_words_from_post($texttodecode = "")
    {
        if (empty($texttodecode)) {
            return "";
        }
        $charset = Configure::read("App.encoding");
        
        $how_much_chars = 51;
        preg_match_all('/[^\s]{' . $how_much_chars . ',}/', $texttodecode, $result, PREG_OFFSET_CAPTURE);
        $accumulated_shift = 0;
        
        $string_to_replace = array();
        $string_for_replace = array();
        
        foreach($result[0] as $index => $value) {
            if (!preg_match('%(?:(?:https?|ftp|file)://|www\.|ftp\.)%', $value[0]) ) {
                /*
                $replacing_string = $this->unicode_wordwrap($value[0], $how_much_chars, "<br />", true);
                //$texttodecode = $this->_mb_substr_replace($texttodecode, $replacing_string, $value[1] + $accumulated_shift, mb_strlen($value[0]) );
                $texttodecode = substr_replace($texttodecode, $replacing_string, $value[1] + $accumulated_shift, mb_strlen($value[0], $charset ));
                $accumulated_shift += mb_strlen($replacing_string, $charset) - mb_strlen($value[0], $charset);
                */
                $string_to_replace[] = $value[0];
                $string_for_replace[]= $this->unicode_wordwrap($value[0], $how_much_chars - 1, " ", true);;
            }
        }

        if (!empty($string_to_replace)) {
            $texttodecode = str_replace($string_to_replace, $string_for_replace, $texttodecode);
        }
        
        return $texttodecode;
    }
    
    
    
}
?>
