<?php
class ModelsTag extends AppModel
{
    var $name = 'ModelsTag';
    var $recursive = -1;
    
    var $actsAs= array('Containable');

    var $belongsTo = array(
    'Tag' => array('className' => 'Tag',
                                'foreignKey' => 'tag_id',
                                'conditions' => '',
                                'fields' => '',
                                'order' => ''
    )
    );

    
    function getShowTagQueries($model, $tagID, $searchQuery = null, $searchFileds) 
    {      
        $this->bindModel(array('belongsTo' => array($model => array('foreignKey' => 'model_id'))), false);
        $table = $this->{$model}->table;
        $search = '';
        
        if ($searchQuery) {
            $searchQuery = addslashes($searchQuery);
            if (!empty($searchFileds)) {
                $i = 0;
                $fields = '';
                foreach ($searchFileds as $field) {  
                    if ($i !=0) {
                        $fields.= ' OR ';                  
                    }  
                    $fields.= $model . '.' . $field . ' = ' . $searchQuery;                    
                    $i++;                     
                } 
                $search = ' AND ( ' . $fields
                 . ')';                
            }       
        }
        $query = "SELECT * FROM models_tags AS ModelsTag
        INNER JOIN $table AS $model ON $model.id = ModelsTag.model_id
        LEFT JOIN users AS User ON User.id = $model.user_id
        WHERE ModelsTag.model = '$model' AND ModelsTag.tag_id = $tagID
		" . $search;        
        
        $count_query = "SELECT count(*) AS cnt FROM models_tags AS ModelsTag
        INNER JOIN $table AS $model ON $model.id = ModelsTag.model_id
        LEFT JOIN users AS User ON User.id = $model.user_id
        WHERE ModelsTag.model = '$model' AND ModelsTag.tag_id = $tagID 
		" . $search;
        return array('query' => $query, 'count_query' => $count_query);
         
    }
    
       /**
       * Overridden paginate
       * @author Oleg D.
       */
    function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array(), $contain = array()) 
    {
        if (isset($extra['extra']['query'])) {
            $sqlOrder = '';
            if (!empty($extra['extra']['params']['named']['sort'])) {
                $order = array($extra['extra']['params']['named']['sort'] => $extra['extra']['params']['named']['direction']);      
            } 
            if (!empty($order)) {             
                $sqlOrder = ' ORDER BY ' . key($order) . ' ' . current($order) . ' ';                                                   
            }
               
            if ($page>1) {
                $sqlLimit = ' LIMIT ' . (($page-1)*$limit) . ', ' .$limit;
            } else {
                $sqlLimit = ' LIMIT ' . $limit;
            }
            return $this->query($extra['extra']['query'] . $sqlOrder . $sqlLimit);
        } else {
            if (!isset($contain['contain'])) {
                $contain['contain'] = array();
            }
            $contain = $contain['contain'];
            return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'contain'));
           }
    }

    /**
     * Overridden paginateCount method
        * @author Oleg D.
     */
    function paginateCount($conditions = null, $recursive = 0, $extra = array()) 
    {
        if (isset($extra['extra']['count_query'])) {
            $res = $this->query($extra['extra']['count_query']);
            $cnt = $res[0][0]['cnt'];
            return $cnt;
        } else {
            return $this->find('count', array('conditions' => $conditions));
        }
    }
    
    /**
     * Delete double tags
        * @author Oleg D.
     */    
    function deleteDubleTags($expTags) 
    {
        foreach ($expTags as $key1 => $tag1) {
            foreach ($expTags as $key2 => $tag2) {
                if (isset($expTags[$key1]) && trim($tag1) == trim($tag2) && $key1 != $key2) {
                    unset($expTags[$key1]);
                }                        
            }
        }
        return $expTags; 
    }
}
?>