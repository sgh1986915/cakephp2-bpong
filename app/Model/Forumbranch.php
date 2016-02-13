<?php
class Forumbranch extends AppModel
{

    var $name = 'Forumbranch';

    var $actsAs = array ('Containable', 'Tree', 'Sluggable' => array('separator' =>  '-',
                                                            'label'         => 'name',
                                                            'slug'          => 'slug',
                                                            'unique'         => true,
                                                            'length'       => 100,
                                                            'overwrite'  =>  true)
    );

    var $validate = array ('name' => array ('rule' => 'notEmpty', 'required' => true, 'message' => 'Value not empty' ), 'description' => array ('notEmpty', 'length' => array ('rule' => array ('between', 0, 254 ), 'message' => 'Description must be less than 255 characters in length' ) ) );



    //The Associations below have been created with all possible keys, those that are not needed can be removed
    var $belongsTo = array ('User' => array (
                                  'className' => 'User'
                                , 'foreignKey' => 'user_id'
                                , 'conditions' => ''
                                , 'fields' => ''
                                , 'order' => '' )
                            , 'Lastpost' => array (
                                  'className' => 'Forumpost'
                                , 'foreignKey' => 'lastpost_id'
                                , 'conditions' => 'Lastpost.is_deleted <> 1'
                                , 'limit' => ''
                                , 'fields' => ''
                                , 'order' => '' ) );

    var $hasMany = array ('Forumtopic' => array (
                                  'className' => 'Forumtopic'
                                , 'foreignKey' => 'forumbranch_id'
                                , 'dependent' => false
                                , 'conditions' => 'Forumtopic.is_deleted <> 1'
                                , 'fields' => ''
                                , 'order' => ''
                                , 'limit' => ''
                                , 'offset' => ''
                                , 'exclusive' => ''
                                , 'finderQuery' => ''
                                , 'counterQuery' => '' )
                            , 'Subbranch' => array (
                                  'className' => 'Forumbranch'
                                , 'foreignKey' => 'parent_id'
                                , 'dependent' => false
                                , 'conditions' => 'Subbranch.is_deleted <> 1'
                                , 'fields' => ''
                                , 'order' => ''
                                , 'limit' => ''
                                , 'offset' => ''
                                , 'exclusive' => ''
                                , 'finderQuery' => ''
                                , 'counterQuery' => '' )
                        );

    function branchdelete($id) 
    {

        $id = ( int ) $id;

        if (! $id) {
            return false;
        }

        $current_lastpost_id = $this->field('lastpost_id', array('Forumbranch.id' => $id));

        $result = $this->children($id);
        $all_subforums = array ($id );

        foreach ( $result as $value ) {
            if (empty($value['Forumbranch']['deleted'])) {
                array_push($all_subforums, $value ['Forumbranch'] ['id']);
            }
        }

        // Working with topics
        App::import('Model', 'Forumtopic');
        $forumtopic = new Forumtopic();
        $topic_unbind = array ("belongsTo" => array ("Forumbranch", "User", "Lastpost" ), "hasMany" => array ("Forumpost" ) );
        $topic_bind = array ('hasMany' => array ('Post' => array (
                                                                  'className' => 'Forumpost'
                                                                , 'foreignKey' => 'forumtopic_id'
                                                                , 'fields' => array ('id' )
                                                                , 'conditions' => 'Post.is_deleted <> 1' ) ) );
        $forumtopic->unbindModel($topic_unbind);
        $forumtopic->bindModel($topic_bind);

        $all_subforums = implode(",", $all_subforums);
        $topics = $forumtopic->find(
            'all', array ('conditions' => array("forumbranch_id IN ($all_subforums)" )
                                                                        , "is_deleted <> 1") 
        );

        $topics_count = count($topics);
        $posts_count = 0;
        $topics_to_delete = array ();
        $posts_to_delete = array ();

        foreach ( $topics as $value ) {
            array_push($topics_to_delete, $value ['Forumtopic'] ['id']);
            foreach ( $value ['Post'] as $value2 ) {
                array_push($posts_to_delete, $value2 ['id']);
            }
            $posts_count += count($value ['Post']);
        }

        $topics_to_delete = implode(",", $topics_to_delete);
        $posts_to_delete = implode(",", $posts_to_delete);

        $time_now = date("Y-m-d H:i:s", time());
        if (! empty ( $posts_to_delete )) {
            //$forumtopic->Forumpost->query("DELETE FROM forumposts WHERE forumposts.id IN ($posts_to_delete)");
            $forumtopic->Forumpost->query("UPDATE forumposts SET deleted = '$time_now',is_deleted = 1 WHERE forumposts.id IN ($posts_to_delete)");
        }

        if (! empty ( $topics_to_delete )) {
            //$forumtopic->query("DELETE FROM forumtopics WHERE forumtopics.id IN ($topics_to_delete)");
            $forumtopic->query("UPDATE forumtopics SET deleted = '$time_now',is_deleted = 1 WHERE forumtopics.id IN ($topics_to_delete)");
        }

        if (! empty ( $all_subforums )) {
            //$this->del($all_subforums, false);
            $this->updateAll(
                array(  'deleted' => "'".$time_now."'"
                , 'is_deleted' => 1), array ("Forumbranch.id IN ($all_subforums)" )
            );
        }

        $this->update_branch_parents(
            $id, $topics_count, $posts_count, null        , true 
        );

        $this->update_lastpostID(
            $id, $current_lastpost_id
        );

        return true;
    }

    function count_branch_subnodes( $id) 
    {
        $id = ( int ) $id;

        if (! $id) {
            return false;
        }

        //$current_lastpost_id = $this->field('lastpost_id', array('Forumbranch.id' => $id) );

        $result = $this->children($id);
        $all_subforums = array ($id );

        foreach ( $result as $value ) {
            if (empty($value['Forumbranch']['is_deleted'])) {
                array_push($all_subforums, $value ['Forumbranch'] ['id']);
            }
        }

        // Working with topics
        App::import('Model', 'Forumtopic');
        $forumtopic = new Forumtopic();
        $topic_unbind = array ("belongsTo" => array ("Forumbranch", "User", "Lastpost" ), "hasMany" => array ("Forumpost" ) );
        $topic_bind = array ('hasMany' => array ('Post' => array (
                                                                  'className' => 'Forumpost'
                                                                , 'foreignKey' => 'forumtopic_id'
                                                                , 'fields' => array ('id' )
                                                                , 'conditions' => 'Post.is_deleted <> 1' ) ) );
        $forumtopic->unbindModel($topic_unbind);
        $forumtopic->bindModel($topic_bind);

        $all_subforums = implode(",", $all_subforums);
        $topics = $forumtopic->find(
            'all', array ('conditions' => array("forumbranch_id IN ($all_subforums)" )
                                                                        , "is_deleted <> 1") 
        );

        $topics_count = count($topics);
        $posts_count = 0;
        $topics_to_delete = array ();
        $posts_to_delete = array ();

        foreach ( $topics as $value ) {
            array_push($topics_to_delete, $value ['Forumtopic'] ['id']);
            foreach ( $value ['Post'] as $value2 ) {
                array_push($posts_to_delete, $value2 ['id']);
            }
            $posts_count += count($value ['Post']);
        }

        $result_data ['topicsCount'] = $topics_count;
        $result_data ['postsCount'] = $posts_count;
        $result_data ['SubbranchesID'] = $all_subforums;
        $result_data ['TopicsID'] = $topics_to_delete;
        $result_data ['PostsID'] = $posts_to_delete;

        return $result_data;
    }


    /**
     * Method to update topic, post and lastpost fields in Forumbranch table
     *
     * @author Povstyanoy
     * @param  int     $branch_id   This is a branch id from which to update
     * @param  int     $topics      Number of topics to sub
     * @param  int     $posts       Number of posts to sub
     * @param  int     $lastpost_id Id of lastpost to update
     * @param  boolean $excluded    Included or excluded $branch_id updating
     *      *      
*/
    function update_branch_parents(   $branch_id
        , $topics = null
        , $posts = null
        , $lastpost_id = null
        , $excluded = false 
    ) {

        $update_branches = $this->getpath($branch_id);

        $to_update = array ();

        foreach ( $update_branches as $value ) {
            if ($value['Forumbranch']['deleted'] == "" ) {
                array_push($to_update, $value ['Forumbranch'] ['id']);
            }
        }
        if ($excluded ) {
            $key = array_search($branch_id, $to_update);
            if ($key !== false ) {
                unset($to_update[$key]);
            }
        }

        $conditions = array();

        $topics = (int)$topics;
        $posts = (int)$posts;
        $lastpost_id = (int)$lastpost_id;

        if ($topics != null) {
            $conditions['topiccounter'] = "topiccounter - $topics";
        }
        if ($posts != null) {
            $conditions['postcounter'] = "postcounter - $posts";
        }
        if ($lastpost_id != null) {
            $conditions['lastpost_id'] = $lastpost_id;
        }

        if (!empty( $to_update ) && !empty( $conditions )) {
            $to_update = implode(",", $to_update);
            $this->updateAll(
                $conditions, array ("Forumbranch.id IN ($to_update)" )
            );
        }

        unset( $update_branches );
        unset( $to_update );
        unset( $conditions );

    }
    /**
     * Method to update lastpost fields in Forumbranch table
     *
     * @author Povstyanoy
     * @param  int     $branch_id                This is a branch id from which to update
     * @param  int     $topics                   Number of topics to sub
     * @param  int     $posts                    Number of posts to sub
     * @param  int     $lastpost_id              Id of lastpost to update
     * @param  boolean $excluded                 Included or excluded $branch_id updating
     * @param  int     $lastpost_id_not_equal_to Id of the post not equal to lastpost_id in branches field *       *      
     *      *      
*/
    function update_lastpostID(       $branch_id
        , $modded_lastpost_id = null 
    ) {

        $update_branches = $this->getpath($branch_id);
        $update_branches = array_reverse($update_branches);
        foreach ( $update_branches as $value ) {
            if (!empty( $modded_lastpost_id) && empty($value ['Forumbranch'] ['deleted'])) {
                if ($value ['Forumbranch'] ['lastpost_id'] == $modded_lastpost_id ) {

                    $lpid = $this->_getLastpostId($value ['Forumbranch'] ['id'], $modded_lastpost_id);
                    $tempid = $this->id;
                    $this->id =  $value ['Forumbranch'] ['id'];
                    $this->saveField('lastpost_id', $lpid);
                    $this->id = $tempid;
                    unset( $tempid );
                }
            }
        }

    }

    function updatetopic_lastpostID(       $branch_id
        , $modded_lastpost_id = null 
    ) {

        $update_branches = $this->getpath($branch_id);
        $update_branches = array_reverse($update_branches);

        foreach ( $update_branches as $value ) {
            if (!empty( $modded_lastpost_id) && empty($value ['Forumbranch'] ['deleted'])) {
                if ($modded_lastpost_id > $value ['Forumbranch'] ['lastpost_id'] ) {

                    //$lpid = $this->_getLastpostId( $value ['Forumbranch'] ['id'], $modded_lastpost_id );
                    $tempid = $this->id;
                    $this->id =  $value ['Forumbranch'] ['id'];
                    $this->saveField('lastpost_id', $modded_lastpost_id);
                    $this->id = $tempid;
                    unset( $tempid );
                }
            }
        }

    }

    function _getLastpostId( $branch_id, $deletedpost_id ) 
    {
        //$this->log( "branch".$branch_id, LOG_DEBUG );
        $branch_childrens = $this->children($branch_id, true);
        //$this->log( $branch_childrens, LOG_DEBUG );
        $topic_lastpost = $this->query(
            "SELECT max(FT.lastpost_id) as newlastpost_id
										FROM forumtopics AS FT
										WHERE FT.forumbranch_id = $branch_id
												AND FT.is_deleted <> 1
										"
        );

        $topic_lastpost = $topic_lastpost[0][0]['newlastpost_id'];
        //$this->log( "topic=".$topic_lastpost, LOG_DEBUG );
        foreach ($branch_childrens as $child) {
            if (((int)$child['Forumbranch']['lastpost_id'] > (int)$topic_lastpost)
                && ($child ['Forumbranch'] ['lastpost_id'] != $deletedpost_id)
                && empty( $child['Forumbranch']['deleted'] )
            ) {
                $topic_lastpost = $child['Forumbranch']['lastpost_id'];
            }
        }
        if (empty($topic_lastpost)) {
            $topic_lastpost = null;
        }
        //$this->log( "newlasppost=".$topic_lastpost, LOG_DEBUG );
        return $topic_lastpost;
    }

    /**
     * Enter description here...
     *
     * @author Povstyanoy
     * @param  array $slugs Array of action unnamed parameters array["pass"];
     * @return array $out
     *
     *         "forum" => id
     *         "topic" => id
     *         "post" => id
     */

    function findIdBySlug( $slugs ) 
    {

        App::import('Model', 'Forumtopic');
        App::import('Model', 'Forumpost');

        $forumtopic = new Forumtopic();
        $forumpost = new Forumpost();

        //$slug = implode("/", $slugs);
        $out = array(      "Forum" => null
         , "Topic" => null
         , "Post" => null
        );

        if (empty( $slugs ) ) {
            return $out;
        }
        //Find a post
        $post_id = (int) $slugs[ count($slugs) - 1 ];
        // slug contain ID of post
        if ($post_id > 0 ) {

            // delete post from array
            unset ( $slugs[ count($slugs) - 1 ] );
            $post = $forumpost->find(
                'first', array('conditions' => array(
                                                                          'Forumpost.is_deleted <> 1'
                                                                        , 'Forumpost.id'     =>     $post_id
                                                                        )
                                                        )
            );
            if (!empty( $post['Forumpost']['id'] ) ) {
                $out['Post'] = $post['Forumpost']['id'];
            }

        }
        //eof

        //if (!empty($out['Post'])) {
        $topic_slug = $slugs[ count($slugs) - 1 ];
        //} else {
        //	$topic_slug = $slugs[ count($slugs) - 1 ];
        //}
        $forumtopic->contain();
        $topic_id = $forumtopic->find(
            'first', array('conditions' => array(
                                                                      'Forumtopic.is_deleted <> 1'
                                                                    , 'Forumtopic.slug'     =>     $topic_slug
                                                                    )
                                                    )
        );

        if (!empty( $topic_id )) {
            $out['Topic'] = $topic_id['Forumtopic']['id'];
        }

        if (!empty($out['Topic'])) {
            unset( $slugs[ count($slugs) - 1 ] );
        }

        $forum_slug = $slugs[ count($slugs) - 1 ];
        $this->contain();
        $forumbranch = $this->find(
            'first', array('conditions' => array(
                                                                      'Forumbranch.is_deleted <> 1'
                                                                    , 'Forumbranch.slug'     =>     $forum_slug
                                                                    )
                                                    )
        );

        if (!empty( $forumbranch )) {
            $out['Forum'] = $forumbranch['Forumbranch']['id'];
        }

        return $out;
    }


    function findIdBySlug2( $slugs ) 
    {
        $out['Forum'] = null;

        if (empty( $slugs ) ) {
            return $out;
        }
        $forum_slug = $slugs[ count($slugs) - 1 ];
        $this->contain();
        $forumbranch = $this->find(
            'first', array('conditions' => array(
                                                                      'Forumbranch.is_deleted <> 1'
                                                                    , 'Forumbranch.slug'     =>     $forum_slug
                                                                    )
                                                    )
        );

        if (!empty( $forumbranch )) {
            $out['Forum'] = $forumbranch['Forumbranch']['id'];
        }

        return $out;
    }

    function getBranches( $conditions = " 1 = 1" ) 
    {

        $query = "
			SELECT 	`Forumbranch`.`id`
				, `Forumbranch`.`name`
				, `Forumbranch`.`slug`
				, `Forumbranch`.`description`
				, `Forumbranch`.`user_id`
				, `Forumbranch`.`parent_id`
				, `Forumbranch`.`postcounter`
				, `Forumbranch`.`topiccounter`
				, `Lastpost`.`id`
				, `Lastpost`.`modified`
				, Lastuser.lgn
				, Forumtopic.repliescounter
				, Forumtopic.slug
				, Postbranch.lft
				, Postbranch.rght
			FROM `forumbranches` AS `Forumbranch`
			LEFT JOIN `forumposts` AS `Lastpost` ON (`Lastpost`.`is_deleted` <> 1 AND `Forumbranch`.`lastpost_id` = `Lastpost`.`id`)
			LEFT JOIN `users` AS `Lastuser` ON (`Lastpost`.`user_id` = Lastuser.id )
			LEFT JOIN `forumtopics` AS `Forumtopic` ON (`Lastpost`.`forumtopic_id` = Forumtopic.id )
			LEFT JOIN `forumbranches` AS `Postbranch` ON (`Forumtopic`.`forumbranch_id` = Postbranch.id )
			WHERE".$conditions;

        return $this->query($query);

    }

    function getBranchSlugTree( $lft, $rght ) 
    {
        $query = "
			SELECT Forumbranch.slug, Forumbranch.name
			FROM forumbranches AS Forumbranch
			WHERE Forumbranch.lft <= $lft AND Forumbranch.rght >= $rght
			ORDER BY Forumbranch.lft asc
		";
        return $this->query($query);
    }

}

?>
