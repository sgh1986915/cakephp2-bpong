<?php
class Status extends AppModel
{

    var $name = 'Status';

    var $validate = array
    (

         'name'      => array(
                             'rule'       => array('notEmpty')
                             ,'allowEmpty' => false
                             ,'message'    => 'Name should contain only letters and numbers'
                           )
    );

    //The Associations below have been created with all possible keys, those that are not needed can be removed
    var $belongsTo = array(
    'Group' => array('className'  => 'Group',
                             'foreignKey' => 'group_id',
                             'conditions' => '',
                             'fields'     => '',
                             'order'      => ''
    )
    );


    function afterDelete()
    {
        $query = "DELETE FROM access WHERE status_id = ".$this->id;
        $this->query($query);

    }
    /**
 * Returns only  default statuses
 * @return unknown_type
 */
    function getStatusesLists() 
    {
        $result = array();
        $this->Group->unbindModel(array('hasMany' => array('Status')));
        $statuses = $this->Group->find('all', array('conditions'=>array('Group.id <>'=>VISITOR_GROUP),'order'=>'Group.id'));
        
        foreach ($statuses as $s) {
            $result[$s['Status']['id']] = $s['Group']['name']."/".$s['Status']['name'];
        }
        return $result;
    }

}
?>
