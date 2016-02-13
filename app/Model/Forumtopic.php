<?php
class Forumtopic extends AppModel
{

    var $name = 'Forumtopic';
    var $uses = array('Forumbranch', 'Forumtopic');
    var $actsAs = array('Containable', 'Sluggable' => array('separator' =>  '-',
                                                            'label'         => 'name',
                                                            'slug'          => 'slug',
                                                            'unique'         => true,
                                                            'length'       => 100,
                                                            'overwrite'  =>  true)
    );
    var $validate = array(
    'name' => array (
                  'rule' => 'notEmpty'
                , 'message' => 'Must be not empty'
        )
        ,'description' => array(
            'notEmpty'
    ,'length' => array(
                  'rule' => array('between', 0, 254),
                  'message' => 'Description must be less than 255 characters in length')
           )
    );

    //The Associations below have been created with all possible keys, those that are not needed can be removed
    var $belongsTo = array(
    'Forumbranch' => array('className' => 'Forumbranch',
                                'foreignKey' => 'forumbranch_id',
                                'conditions' => 'Forumbranch.is_deleted <> 1',
                                'fields' => '',
                                'order' => ''
    ),
    'User' => array('className' => 'User',
                                'foreignKey' => 'user_id',
                                'conditions' => '',
                                'fields' => '',
                                'order' => ''
    ),
    'Lastpost' => array('className' => 'Forumpost',
                                'foreignKey' => 'lastpost_id',
                                'conditions' => 'Lastpost.is_deleted <> 1',
                                'fields' => '',
                                'order' => ''
    )
    );

    var $hasMany = array(
    'Forumpost' => array('className' => 'Forumpost',
                                'foreignKey' => 'forumtopic_id',
                                'dependent' => true,
                                'conditions' => 'Forumpost.is_deleted <> 1',
                                'fields' => '',
                                'order' => '',
                                'limit' => '',
                                'offset' => '',
                                'exclusive' => '',
                                'finderQuery' => '',
                                'counterQuery' => ''
    )
    );

    function beforeSave($options = array())
    {

        if (isset($this->data['Forum']['actiontype']) && $this->data['Forum']['actiontype'] == "add") {
            if(!class_exists('Forumbranch')) {
                App::import('Model', 'Forumbranch');
            }

            $forumbranch = new Forumbranch();

            $forumbranch->update_branch_parents(
                $this->data['Forumtopic']['forumbranch_id'], '-1' 
            );
        }

        return true;

    }

    function beforeDelete($cascade = true)
    {

        $result = $this->read(null);

        $posts = $this->query(
            "
							SELECT 	count(id) AS posts
							FROM 	forumposts
							WHERE	forumtopic_id = " . $this->id
        );

        $posts_count = $posts[0][0]['posts'];

        if(!class_exists('Forumbranch')) {
            App::import('Model', 'Forumbranch');
        }

        $forumbranch = new Forumbranch();

        $forumbranch->update_branch_parents(
            $result['Forumtopic']['forumbranch_id'], 1, $posts_count 
        );

        return true;

    }

    function _deleteTopic( $topic_id = null ) 
    {

        $this->id = $topic_id;
        $result = $this->read(null);

        $time_now = date("Y-m-d H:i:s", time());
        //Delete topic
        $this->saveField('deleted', $time_now);
        $this->saveField('is_deleted', 1);

        //Count posts of a topic
        $posts = $this->query(
            "
							SELECT 	count(id) AS posts
							FROM 	forumposts
							WHERE	forumtopic_id = " . $this->id . "
									AND forumposts.is_deleted <> 1
						"
        );

        $posts_count = $posts[0][0]['posts'];

        //Delete posts
        $posts = $this->query(
            "
							UPDATE 	forumposts
							SET		deleted = '$time_now', is_deleted = 1
							WHERE	forumtopic_id = " . $this->id . "
									AND forumposts.is_deleted <> 1
						"
        );

        if(!class_exists('Forumbranch')) {
            App::import('Model', 'Forumbranch');
        }

        $forumbranch = new Forumbranch();

        $forumbranch->update_branch_parents(
            $result['Forumtopic']['forumbranch_id'], 1, $posts_count 
        );

        $forumbranch->update_lastpostID(
            $result['Forumtopic']['forumbranch_id'], $result['Forumtopic']['lastpost_id'] 
        );

        return true;

    }
    function findIdBySlug2( $slugs ) 
    {
        App::import('Model', 'Forumbranch');
        $branch = new Forumbranch();

        $out = array(      "Forum" => null
         , "Topic" => null
        );

        if (empty( $slugs ) ) {
            return $out;
        }
        $topic_slug = $slugs[ count($slugs) - 1 ];
        $this->contain();
        $topic_id = $this->find(
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
        $branch->contain();
        $forumbranch = $branch->find(
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

    function findTopicIdBySlugForPost( $slug ) 
    {
        if (empty( $slug ) ) {
            return "";
        }
        $sql = "
			SELECT 	  Forumtopic.id
				, Forumtopic.name
				, Forumbranch.lft
				, Forumbranch.rght
			FROM `forumtopics` AS `Forumtopic`
			LEFT JOIN forumbranches AS Forumbranch ON ( Forumtopic.forumbranch_id = Forumbranch.id )
			WHERE `Forumtopic`.`is_deleted` <> 1 AND `Forumtopic`.`slug` = '$slug'
			LIMIT 1;
		";
        return $this->query($sql);
    }

    function getLastTopicsForNationPage() 
    {
        $sql = "
			SELECT 	Forumtopic.id
				, Forumtopic.name
				, Forumtopic.viewcounter
				, Forumtopic.repliescounter
				, Forumtopic.slug
				, Forumbranch.name
				, Forumbranch.slug
				, Forumbranch.lft
				, Forumbranch.rght
				, User.lgn
				, Lastpost.id
				, Lastpostuser.lgn
				, Lastpost.created
			FROM forumtopics AS Forumtopic
			LEFT JOIN `forumbranches` AS `Forumbranch` ON (`Forumtopic`.`forumbranch_id` = `Forumbranch`.`id`)
			LEFT JOIN users AS User ON (Forumtopic.user_id = User.id)
			LEFT JOIN forumposts AS Lastpost ON (Lastpost.is_deleted <> 1 AND Forumtopic.lastpost_id = Lastpost.id)
			LEFT JOIN users AS Lastpostuser ON (Lastpost.user_id = Lastpostuser.id)
			WHERE Forumtopic.is_deleted <> 1
			ORDER BY Forumtopic.lastpost_id DESC
			LIMIT 10;
		";
        return $this->query($sql);
    }

    function getLastTopicsForHomePage() 
    {
        $sql = "
			SELECT 	Forumtopic.id
				, Forumtopic.name
				, Forumtopic.viewcounter
				, Forumtopic.repliescounter
				, Forumtopic.slug
				, Forumbranch.name
				, Forumbranch.slug
				, Forumbranch.lft
				, Forumbranch.rght
				, User.lgn
				, Lastpost.id
				, Lastpostuser.lgn
				, Lastpost.created
			FROM forumtopics AS Forumtopic
			LEFT JOIN 'forumbranches' AS 'Forumbranch' ON ('Forumtopic'.'forumbranch_id' = 'Forumbranch'.'id')
			LEFT JOIN users AS User ON (Forumtopic.user_id = User.id)
			LEFT JOIN forumposts AS Lastpost ON (Lastpost.is_deleted <> 1 AND Forumtopic.lastpost_id = Lastpost.id)
			LEFT JOIN users AS Lastpostuser ON (Lastpost.user_id = Lastpostuser.id)
			WHERE Forumtopic.is_deleted <> 1
			ORDER BY Forumtopic.lastpost_id DESC
			LIMIT 16;
		";


        return $this->query($sql);
    }

    /**
    * Custom paginate method
    */

    function paginate( $conditions = null, $fields = null, $order = null, $limit = null, $page = 1, $recursive = null ) 
    {

        if ($page <= 1) {
            $paging_sql = " LIMIT $limit;";
        } else {
            $paging_sql = " LIMIT " . $limit * ( $page - 1 ) . ", $limit;";
        }

        $query = "
			SELECT `Forumtopic`.`id`
				, `Forumtopic`.`user_id`
				, `Forumtopic`.`forumbranch_id`
				, `Forumtopic`.`name`
				, `Forumtopic`.`description`
				, `Forumtopic`.`viewcounter`
				, `Forumtopic`.`repliescounter`
				, `Forumtopic`.`slug`
				, `User`.`lgn`
				, `Lastpost`.`id`
				, `Lastpost`.`modified`
				, Lastpostuser.lgn
			FROM `forumtopics` AS `Forumtopic`
			LEFT JOIN `users` AS `User` ON (`Forumtopic`.`user_id` = `User`.`id`)
			LEFT JOIN `forumposts` AS `Lastpost` ON (`Lastpost`.`is_deleted` <> 1 AND `Forumtopic`.`lastpost_id` = `Lastpost`.`id`)
			LEFT JOIN `users` AS `Lastpostuser` ON (`Lastpostuser`.`id` = `Lastpost`.`user_id`)
			WHERE " . $conditions[1] . "
			ORDER BY `Lastpost`.`id` desc
			$paging_sql
			";
        return $this->query($query);
    }

    /**
     * Custom paginateCount method
     */

    function paginateCount( $conditions = null ) 
    {
        $this->contain();
        return $this->find('count', array('conditions' => $conditions));
    }

    function generate_last_post_url_for_branch($forumtopic_slug, $lft, $rght) 
    {

        $forum_path = $this->Forumbranch->getBranchSlugTree($lft, $rght);

        if (empty($forum_path)) {
            return ""; 
        }

        $slugs = array();

        foreach($forum_path as $index => $branch) {
            $slugs[ $index ] = $branch['Forumbranch']['slug'];
        }

        array_push($slugs, $forumtopic_slug);

        return implode("/", $slugs);
    }
}
?>
