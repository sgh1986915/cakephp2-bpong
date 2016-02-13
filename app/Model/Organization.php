<?php
class Organization extends AppModel
{

    var $name = 'Organization';    
    var $recursive = -1;
    var $actsAs = array(
        'Containable',
        'SoftDeletable',
        'Image'=>array(
    'thumbs' => array('create'=>true,'width'=>'120','height'=>'120'),
    'versions'=>array(
                'middle'=>array('width'=>'350','height'=>'300')
            )    
         )
    );
    var $validate = array(
    'name' => array('rule' => array('notEmpty'), 'allowEmpty' => false,'message'    => 'Name can not be empty.'),
    'slug' => array(
                'alphaNumericDashUnderscore' => array('rule' => 'alphaNumericDashUnderscore', 'message' => 'Only letters, numbers and underscore.'), 
                'isUnique' => array('rule' => 'isUnique', 'message' => 'Such slug already in use, try another.'),
                'notEmpty' => array('rule' => 'notEmpty', 'message' => 'Please, specify slug.'))                              
    );
    var $belongsTo = array(
    'Creator' => array('className' => 'User',
                                  'foreignKey' => 'user_id',
                                  'dependent' => false,
                                  'conditions' => array(),
                                  'fields' => '',
                                  'order' => ''
    )                                          
    );        
    var $hasOne = array(
    'Address' => array('className' => 'Address',
                                'foreignKey' => 'model_id',
                                'dependent' => true,
                                'conditions' => array('Address.model' => 'Organization', 'Address.is_deleted'=>0),
                                'fields' => '',
                                'order' => ''
    ),
    'Image' => array('className' => 'Image',
                                'foreignKey' => 'model_id',
                                'dependent' => true,
                                'conditions' => array('Image.model' => 'Organization', 'Image.is_deleted'=>0),
                                'fields' => '',
                                'order' => ''
    )            
            
    );
    
    var $hasAndBelongsToMany = array(
    'User' => array('className' => 'User',
                        'joinTable' => '',
                        'with' => "OrganizationsUser",
                        'foreignKey' => 'organization_id',
                        'associationForeignKey' => 'user_id',
                        'unique' => true,
                        'conditions' => array('User.is_deleted' => 0),
                        'fields' => '',
                        'order' => '',
                        'limit' => '',
                        'offset' => '',
                        'finderQuery' => '',
                        'deleteQuery' => '',
                        'insertQuery' => ''
    )


    );
    var $hasMany = array(
                    'OrganizationNews' => array(
                        'className' => 'OrganizationNews',
                        'foreignKey' => 'organization_id',
                        'dependent' => true,
                        'fields' => '',
                        'order' => 'Vote.created ASC',
                        'limit' => '',
                        'offset' => '',
                        'exclusive' => '',
                        'finderQuery' => ''),
                    'Affilspoint' => array(
                        'className' => 'Affilspoint',
                        'foreignKey'=>'model_id',
                        'conditions'=>array('Affilspoint.model'=>'Organization'))
         
    );                                                      
    
    function alphaNumericDashUnderscore($check) 
    {
        // $data array is passed using the form field name as the key
        // have to extract the value to make the function generic
        $value = array_shift($check);
        return preg_match('|^[0-9a-zA-Z_-]*$|', $value);
    }
    
    /**
     * check access to albums for this model id
     * @author Oleg D.
     */
    function getAlbumUploadAccess($userID, $modelID, $Access, $getAll) 
    {
        if ($Access->getAccess('EventAlbums', 'c') || $this->OrganizationsUser->find('count', array('conditions' => array('organization_id' => $modelID, 'user_id' => $userID, 'status' => 'accepted')))) {            
            return true;
        } else {            
            return false;
        }
    }
    function recalculatePoints($orgID) 
    {
        $allPoints = $this->find(
            'first', array(
            'conditions'=>array('Organization.id'=>$orgID),
            'contain'=>array('Affilspoint'=>array('conditions'=>array('status'=>'Active'))))
        );
        $eachPoint = Set::extract($allPoints['Affilspoint'], '{n}.points');
        $eachWin = Set::extract($allPoints['Affilspoint'], '{n}.win');
        $eachLoss = Set::extract($allPoints['Affilspoint'], '{n}.loss');
        $eachcupdif= Set::extract($allPoints['Affilspoint'], '{n}.cupdif');
        return $this->save(
            array(
            'id'=>$orgID,
            'points'=>array_sum($eachPoint),
            'total_wins'=>array_sum($eachWin),
            'total_losses'=>array_sum($eachLoss),
            'total_cupdif'=>array_sum($eachcupdif))
        );
    }
    
}
?>
