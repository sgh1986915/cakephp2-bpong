<?php
class Provincestate extends AppModel
{
    var $name = 'Provincestate';

    var $belongsTo = array('Country' =>
                           array('className'  => 'Country',
                                 'conditions' => '',
                                 'order'      => '',
                                 'foreignKey' => 'country_id'
                           )
                     );
}
?>
