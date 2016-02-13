<?php
class OrganizationNews extends AppModel
{

    var $name = 'OrganizationNews';
    var $table = 'organizations_news';
    var $recursive = -1;
    var $actsAs = array(
        'Containable',
        'SoftDeletable',
        'Image'=>array(
    'thumbs' => array('create'=>true,'width'=>'60','height'=>'60'),
    'versions'=>array(
                'little'=>array('width'=>'150','height'=>'150'),
                'middle'=>array('width'=>'350','height'=>'300')
            )    
         )
    );
    
    var $belongsTo = array(
    'Organization' => array('className' => 'Organization',
                                  'foreignKey' => 'organization_id',
                                  'dependent' => false,
                                  'conditions' => array(),
                                  'fields' => '',
                                  'order' => ''
    ),
    'User' => array('className' => 'User',
                                  'foreignKey' => 'user_id',
                                  'dependent' => false,
                                  'conditions' => array(),
                                  'fields' => '',
                                  'order' => ''
    )
    );   
    
    var $hasOne = array(
    'Image' => array('className' => 'Image',
                                'foreignKey' => 'model_id',
                                'dependent' => true,
                                'conditions' => array('Image.model' => 'OrganizationNews', 'Image.is_deleted'=>0),
                                'fields' => '',
                                'order' => ''
    )            
            
    );   

}
?>
