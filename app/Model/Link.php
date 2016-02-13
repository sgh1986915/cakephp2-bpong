<?php

class Link extends AppModel
{

    var $name = 'Link';
    var $recursive = -1;

    var $actsAs= array(
    'Tag', 'SoftDeletable' // Use Behavior Tag only with Behavior SoftDeletable !!!! Because of increase/decrease Tag's counter !!!
    ,'Containable');

    //The Associations below have been created with all possible keys, those that are not needed can be removed
    var $hasAndBelongsToMany = array(
    'Tag' => array('className' => 'Tag',
                        'joinTable' => '',
                        'with'=>'ModelsTag',
                        'foreignKey' => 'model_id',
                        'associationForeignKey' => 'tag_id',
                        'unique' => true,
                        'conditions' => array('ModelsTag.model' => 'Link'),
                        'order' => '',
                        'limit' => ''
    )
    );

    var $belongsTo = array(
    'User' => array('className' => 'User',
                                'foreignKey' => 'user_id',
                                'conditions' => '',
                                'fields' => '',
                                'order' => ''
    )
    );
    var $hasMany = array(
                             'Vote' => array(
                                                        'className' => 'Vote',
                                                        'foreignKey' => 'model_id',
                                                        'dependent' => true,
                                                        'conditions' => array('Vote.model' => 'Link'),
                                                        'fields' => '',
                                                        'order' => 'Vote.created ASC',
                                                        'limit' => '',
                                                        'offset' => '',
                                                        'exclusive' => '',
                                                        'finderQuery' => ''),
                             'Comment' => array(
                                                        'className' => 'Comment',
                                                        'foreignKey' => 'model_id',
                                                        'dependent' => true,
                                                        'conditions' => array('Comment.model' => 'Link'),
                                                        'fields' => '',
                                                        'order' => 'Comment.id ASC',
                                                        'limit' => '',
                                                        'offset' => '',
                                                        'exclusive' => '',
                                                        'finderQuery' => '')

    );
    //var $actsAs= array('Tag', 'Containable');
       /**
       * Overridden paginate
       * @author Oleg D.
       */
    function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) 
    {
        if (isset($extra['extra']['query'])) {
            $sqlOrder = '';
            if (!empty($order)) {
                $orders = "";
                foreach ($order AS $key=>$val) {
                    $orders .= str_replace('Link.', '', $key)." ".$val;  // ONLY FOR SUBMISSION USE str_replace !!!!!!!!!!
                }
                $sqlOrder = " ORDER BY {$orders} ";
            }

            if ($page>1) {
                $sqlLimit = ' LIMIT ' . (($page-1)*$limit) . ', ' .$limit;
            } else {
                $sqlLimit = ' LIMIT ' . $limit;
            }
            $query = $extra['extra']['query'] . $sqlOrder . $sqlLimit;
            return $this->query($query);
        } else {
            return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive'));
        }
    }

    /**
     * Overridden paginateCount method
       * @author Oleg D.
     */
    function paginateCount($conditions = null, $recursive = 0, $extra = array()) 
    {
        if (isset($extra['extra']['count_query'])) {
            //echo $extra['extra']['count_query'];
            $res = $this->query($extra['extra']['count_query']);
            $cnt = $res[0][0]['cnt'];
            return $cnt;
        } else {
            return $this->find('count', array('conditions' => $conditions));
        }
    }
    /**
    * Increase in views 1
     * @author Oleg D.
     */
    function changeViews($id) 
    {
        return $this->query('UPDATE links SET views = views + 1 WHERE id = ' . $id);
    }
}


?>
