<?php
class Pongtable extends AppModel
{

    var $name = 'Pongtable';

    var $validate = array(
          'title' => array(
                  'rule' => 'notEmpty'
                , 'required' => true
                , 'message' => 'Value not empty'
          )
          , 'description' => array(
                  'rule' => 'notEmpty'
                , 'required' => true
                , 'message' => 'Value not empty'
          )
    );

    var $actsAs = array (      'Containable'
                            ,  'Image'=>array( 'thumbs' => array(     'create'    =>    true
                                                                    ,'width'    =>    '120'
                                                                    ,'height'    =>    '120'
                                                                    ,'bgcolor'    =>    '#FFFFFF')
                                          , 'required' => true
                                          , 'watermark' => array(
                                                                                    'image' => 'img/bpong_watermark.png'
                                                                                    )
                                        )


    );

    var $hasOne = array(
    'Address' => array('className' => 'Address',
                                'foreignKey' => 'model_id',
                                'dependent' => true,
                                'conditions' => array('Address.model'=>'Pongtable','Address.is_deleted <> 1'),
                                'fields' => '',
                                'order' => ''
    )
    );

    var $hasMany = array('Tableimage' =>
                         array(        'className'     => 'Image',
                                    'conditions'    => "Tableimage.model = 'Pongtable' AND Tableimage.is_deleted <> 1",
                                    'order'         => '',
                                    'limit'         => '',
                                    'foreignKey'    => 'model_id',
                                    'dependent'     => false,
                                    'exclusive'     => false,
                                    'finderQuery'   => ''
                         )
    );

    var $belongsTo = array(
    'User' => array(
            'className'    => 'User',
            'foreignKey'    => 'user_id'
            )
    );

    function getRandomTable() 
    {
        $sql = "SELECT
					Tableimage.filename
					, Pongtable.description
				FROM images AS Tableimage
				LEFT JOIN pongtables AS Pongtable ON ( Tableimage.model_id = Pongtable.id )
				WHERE Tableimage.model = 'Pongtable' AND Tableimage.is_deleted <> 1
				ORDER BY RAND()
				LIMIT 1;
		";
        $result = $this->query($sql);
        return  $result[0];
    }

}
?>
