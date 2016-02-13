<?php
class Forumpost extends AppModel
{

    var $name = 'Forumpost';
    //var $uses = array( 'Forumpost', 'Forumtopic', 'Forumbranch');

    var $actsAs = array('Containable');
    /*
    var $actsAs = array('Containable','Sluggable' => array(	'separator' =>  '_',
												'label'         => 'name',
												'slug'          => 'slug',
												'length'       => 100,
												'overwrite'  =>  true)
    );
    */
    var $validate = array(
    'text' => array(
              'rule' => 'notEmpty'
    , 'message' => 'Must be not empty'
        )

    );

    //The Associations below have been created with all possible keys, those that are not needed can be removed
    var $belongsTo = array(
    'Forumtopic' => array('className' => 'Forumtopic',
                                'foreignKey' => 'forumtopic_id',
                                'conditions' => 'Forumtopic.is_deleted <> 1',
                                'fields' => '',
                                'order' => ''
    ),
    'User' => array('className' => 'User',
                                'foreignKey' => 'user_id',
                                'conditions' => '',
                                'fields' => '',
                                'order' => ''
    )
    );

    /**
     * Increment counters only for add action before save post
     *
     * @return true
     */
    function beforeSave()
    {

        $this->data['Forumpost']['modified'] = date("Y-m-d H:i:s", time());

        if (isset( $this->data['Forum']['actiontype'] ) && $this->data['Forum']['actiontype'] == "add" ) {
            //Increment replies counter
            $this->query(
                "
								UPDATE `forumtopics`
								SET `repliescounter` = `repliescounter` + 1
									, `modified` = '".date("Y-m-d H:i:s", time())."'
								WHERE `forumtopics`.`id` = " . $this->data['Forumpost']['forumtopic_id']
            );
        }

        return true;

    }

    function afterSave() 
    {

        if (isset( $this->data['Forum']['actiontype'] ) && $this->data['Forum']['actiontype'] == "add" ) {

            $id = $this->getLastInsertID();
            $this->query(
                "
								UPDATE `forumtopics`
								SET   `lastpost_id` = " . $id . "
								WHERE `forumtopics`.`id` = " . $this->data['Forumpost']['forumtopic_id']
            );

            if(!class_exists('Forumbranch') ) {
                App::import('Model', 'Forumbranch');
            }

            $forumbranch = new Forumbranch();

            $this->log("asa".$this->data['Forumtopic']['forumbranch_id'], LOG_DEBUG);
            $forumbranch->update_branch_parents(
                $this->data['Forumtopic']['forumbranch_id'], null, '-1', $id 
            );
        }

        return true;

    }

    /**
     * Increment view counter
     *
     * @author Povstyanoy
     *
     * @param  int $topic_id
     * @return bool true
     */
    function incrementViewCounter( $topic_id = null ) 
    {
        if (!empty ( $topic_id ) ) {
            $this->query(
                "
								UPDATE `forumtopics`
								SET `viewcounter` = `viewcounter` + 1
								WHERE `forumtopics`.`id` = ".$topic_id
            );
        }
        return true;
    }

    function _deletePost( $post_id = null)
    {

        $post_id = (int)$post_id;

        if(!$post_id ) {
            return false;
        }

        $time_now = date("Y-m-d H:i:s", time());

        $this->id = $post_id;
        $forumtopic_id = $this->field('forumtopic_id');

        $this->Forumtopic->recursive = 0;

        $result = $this->Forumtopic->read(null, $forumtopic_id);

        if (!$result['Forumtopic']['repliescounter']) {
            $this->Forumtopic->_deleteTopic($forumtopic_id);
            return false;
        }

        $this->query(
            "
							UPDATE `forumposts`
							SET `deleted` = '$time_now', is_deleted = 1
							WHERE `forumposts`.`id` = $post_id"
        );

        $this->query(
            "
							UPDATE `forumtopics`
							SET `repliescounter` = `repliescounter` - 1
							WHERE `forumtopics`.`id` = " . $result['Forumtopic']['id']
        );



        //Change only if topic have this lastpost id
        if($result['Forumtopic']['lastpost_id'] == $post_id ) {

            $lastpost_id  = $this->query(
                "	SELECT max(id) AS lastpost_id
											FROM forumposts
											WHERE 	forumtopic_id = " . $forumtopic_id . "
												AND	is_deleted <> 1 "
            );

            $lastpost_id = $lastpost_id[0][0]['lastpost_id'];

            $this->query(
                "
								UPDATE `forumtopics`
								SET   `lastpost_id` = " . $lastpost_id . "
								WHERE `forumtopics`.`id` = " . $result['Forumtopic']['id']
            );
        }


        if(!class_exists('Forumbranch')) {
            App::import('Model', 'Forumbranch');
        }

        $forumbranch = new Forumbranch();

        $forumbranch->update_branch_parents(
            $result['Forumtopic']['forumbranch_id'], null, 1 
        );

        $forumbranch->update_lastpostID(
            $result['Forumtopic']['forumbranch_id'], $post_id 
        );
        return true;
    }
    function paginate( $conditions = null, $fields = null, $order = null, $limit = null, $page = 1, $recursive = null ) 
    {

        if ($page <= 1) {
            $paging_sql = " LIMIT $limit;";
        } else {
            $paging_sql = " LIMIT " . $limit * ( $page - 1 ) . ", $limit;";
        }

        $query = "
			SELECT
				`Forumpost`.`id`
				, `Forumpost`.`user_id`
				, `Forumpost`.`text`
				, `Forumpost`.`created`
				, `Forumpost`.`modified`
				, `Forumtopic`.`name`
				, `User`.`id`
				, `User`.`lgn`
				, `User`.`avatar`
				, `User`.`birthdate`
				, `User`.`created`
			FROM `forumposts` AS `Forumpost`
			LEFT JOIN `forumtopics` AS `Forumtopic` ON (`Forumtopic`.`is_deleted` <> 1 AND `Forumpost`.`forumtopic_id` = `Forumtopic`.`id`)
			LEFT JOIN `users` AS `User` ON (`Forumpost`.`user_id` = `User`.`id`)
			WHERE " . $conditions[1] . "
			ORDER BY `Forumpost`.`id` ASC
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

}
?>
