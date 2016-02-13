<?php
class Venue extends AppModel
{

    var $name = 'Venue';
    var $validate = array(
          'name' => array(
                  'rule' => 'notEmpty'
                , 'required' => true
                , 'message' => 'Value not empty'
          )

    );

    var $actsAs = array (      'Containable'
                            , 'Sluggable' => array( 'separator'     =>  '-',
                                                    'label'         => 'name',
                                                    'slug'          => 'slug',
                                                    'length'           => 100,
                                                    'overwrite'      =>  true)
                    ,'Image'=>array(
                        'thumbs'=>array('create'=>true,'width'=>'120','height'=>'120','bgcolor'=>'#FFFFFF'),
                        'versions'=>array(
                                'middle'=>array('width'=>'215','height'=>'215','bgcolor'=>'#FFFFFF')
                                )
                      )


    );
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    var $belongsTo = array(
    'Venuetype' => array('className' => 'Venuetype',
                                'foreignKey' => 'venuetype_id',
                                'conditions' => '',
                                'fields' => '',
                                'order' => ''
    )
    );

    var $hasOne = array(
    'Address' => array('className' => 'Address',
                                'foreignKey' => 'model_id',
                                'dependent' => true,
                                'conditions' => array('Address.model'=>'Venue','Address.is_deleted'=>0),
                                'fields' => '',
                                'order' => ''
    )
    );

    var $hasMany = array(
    'Phone' => array('className' => 'Phone',
                                'foreignKey' => 'model_id',
                                'dependent' => true,
                                'conditions' => array('Phone.model'=>'Venue','Phone.is_deleted'=>0),
                                'fields' => '',
                                'order' => ''
    ),
            'Checkin'=>array('className'=>'Checkin',
                                'foreignKey'=>'venue_id'),
            
            'Nbplday'=>array('className' => 'Nbplday',
                                'foreignKey'=>'venue_id')            
    );

    var $hasAndBelongsToMany = array(
    'Venueactivity' => array('className' => 'Venueactivity',
                        'joinTable' => 'venues_venueactivities',
                        'foreignKey' => 'venue_id',
                        'associationForeignKey' => 'venueactivity_id',
                        'unique' => true,
                        'conditions' => '',
                        'fields' => '',
                        'order' => '',
                        'limit' => '',
                        'offset' => '',
                        'finderQuery' => '',
                        'deleteQuery' => '',
                        'insertQuery' => ''
    ),

    'Venuefeature' => array('className' => 'Venuefeature',
                        'joinTable' => 'venues_venuefeatures',
                        'foreignKey' => 'venue_id',
                        'associationForeignKey' => 'venuefeature_id',
                        'unique' => true,
                        'conditions' => '',
                        'fields' => '',
                        'order' => '',
                        'limit' => '',
                        'offset' => '',
                        'finderQuery' => '',
                        'deleteQuery' => '',
                        'insertQuery' => ''
    ),

    'Worktime' => array('className' => 'Workday',
                        'joinTable' => 'venues_worktimes',
                        'foreignKey' => 'venue_id',
                        'associationForeignKey' => 'workday_id',
                        'unique' => true,
                        'conditions' => '',
                        'fields' => '',
                        'order' => '',
                        'limit' => '',
                        'offset' => '',
                        'finderQuery' => '',
                        'deleteQuery' => '',
                        'insertQuery' => ''
    ),

    'User' => array('className' => 'User',
                        'joinTable' => '',
                        'with' => "Manager",
                        'foreignKey' => 'model_id',
                        'associationForeignKey' => 'user_id',
                        'unique' => true,
                        'conditions' => array('Manager.model' => 'Venue','User.is_deleted' => 0),
                        'fields' => '',
                        'order' => '',
                        'limit' => '',
                        'offset' => '',
                        'finderQuery' => '',
                        'deleteQuery' => '',
                        'insertQuery' => ''
    )
    );


    function delete_venue($id) 
    {
        $this->Address->recursive = -1;
        $address_to_delete = $this->Address->find('first', array('conditions' => array ("model_id" => $id, "model" => "Venue")));
        $this->Phone->recursive = -1;
        $phone_to_delete = $this->Phone->find('first', array('conditions' => array ("model_id" => $id, "model" => "Venue")));

        $time_now = date("Y-m-d H:i:s", time());
        $this->Address->id = $address_to_delete["Address"]["id"];
        $this->Address->saveField('deleted', $time_now);
        $this->Address->saveField('is_deleted', 1);

        $this->Phone->id = $phone_to_delete["Phone"]["id"];
        $this->Phone->saveField('deleted', $time_now);
        $this->Phone->saveField('is_deleted', 1);

        $this->id = $id;
        $this->saveField('deleted', $time_now);
        $this->saveField('is_deleted', 1);

        return true;
    }

    function findIdBySlug( $slug = null ) 
    {
        if (empty( $slug ) ) {
            return false;
        }

        $this->contain();
        $id = $this->find('first', array('conditions' => array('slug' => $slug)));
        if (!empty( $id )) {
            return $id['Venue']['id'];
        }
        return false;
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

        if (!isset($conditions[0]) || $conditions[0] == null) {
            $conditions[0] = "1=1";
        }

        if ($order != null) { foreach ($order as $field => $direction) {
                $limited = ' ORDER BY '.$field.' '.$direction.' ';
        } 
        } else { $limited = ' '; 
        }

        $query = "
			SELECT `Venue`.`id`
				, `Venue`.`name`
				, `Venue`.`phone`
				, `Venue`.`slug`
                , `Venue`.`nbpltype`
				, `Venuetype`.`name`
				, `Address`.`city`
				, `Provincestate`.`name`
			FROM `venues` AS `Venue`
			LEFT JOIN `venuetypes` AS `Venuetype` ON (`Venuetype`.`id` = `Venue`.`venuetype_id`)
			LEFT JOIN `addresses` AS `Address` ON (`Address`.`model` = 'Venue' AND `Address`.`model_id` = `Venue`.`id`)
			LEFT JOIN `provincestates` AS `Provincestate` ON (`Provincestate`.`id` = `Address`.`provincestate_id`)
			WHERE " . $conditions[0] . " " . $limited . "
			$paging_sql
			";
        return $this->query($query);
    }

    /**
     * Custom paginateCount method
     */
    function paginateCount( $conditions = null ) 
    {
        $this->contain('Address');
        return $this->find('count', array('conditions' => $conditions));
    }
    /**
 * add\edit venue
 * @param unknown_type $data
 * @author vovich
 * @return false or Venue Id
 */
    function storeVenue($data = array())
    {

        if (empty($data)) {
                return false;
        }

            $data['name']  = trim(htmlentities(strip_tags($data['name'])));
        //Save Venue info
        if (!empty($data['id'])) {
                $this->create();
        }
        $venue['Venue'] = $data;
        $venue['Image'] = $venue['Venue']['Image'];
        unset($venue['Venue']['Address']);
        unset($venue['Venue']['Phone']);
        unset($venue['Venue']['Image']);
        unset($venue['Venue']['Venueactivity']);
        $venue['Venueactivity'] = $data['Venueactivity'];

        $this->save($venue);
        if (empty($data['id'])) {
            $data['id'] = $this->getLastInsertID();
        }

        /*Store address*/
        $data ['Address']['model']    = "Venue";
        $data ['Address']['model_id'] = $data['id'];
        if (empty($data ['Address']['id'])) {
            $data ['Address']['id']   = $this->Address->field('Address.id', array('Address.model' => "Venue",'Address.model_id' => $data['id']));
        }
        if (empty($data ['Address']['id'])) {
            $data ['Address']['id']   = $this->Address->field('Address.id', array('Address.model' => "Venue",'Address.model_id' => $data['id']));
        }
        $this->Address->save($data['Address']);

        /*Save Manager info*/
        $data['Manager']['user_id']        =    $_SESSION['loggedUser']['id'];
        $data['Manager']['model']        =    "Venue";
        $data['Manager']['model_id']    =    $data['id'];
        $data['Manager']['is_confirmed']=    0;
        $this->Manager->create();
        $this->Manager->save($data['Manager']);

        /*Save Phone*/
        if (!empty($data['Phone']['phone']) ) {
             $data['Phone']['phone']     = trim(htmlentities(strip_tags($data['Phone']['phone'])));
            $phone['Phone']                = $data['Phone'];
             $phone['Phone']['model']    = "Venue";
             $phone['Phone']['model_id']    = $data['id'];
            if (empty($data ['Phone']['id'])) {
                $data ['Phone']['id']   = $this->Phone->field('Phone.id', array('Phone.model' => "Venue",'Phone.model_id' => $data['id']));
            }
                $this->Phone->save($phone);
                unset( $phone );
        }

        return $data['id'];
    }
    function updateUsersCount($venueID) 
    {
        $this->Checkin->recursive = -1;
        $checkinCount = $this->Checkin->find(
            'count', array('conditions'=>array(
            'venue_id'=>$venueID,
            'checkedout'=>0))
        );
        $this->recursive = -1;
        $venue = $this->find('first', array('conditions'=>array('id'=>$venueID)));
        if (!$venue) { 
            return false; 
        }
        $venue['Venue']['userscount'] = $checkinCount;
        $this->save($venue);
        return true;
    }

}
?>
