<?php

class Language extends AppModel
{

    var $name = 'Language';

    var $validate = array
    (
         'code'           => array(
                              'rule'       => array('custom', '/^[a-zA-Z]{2}$/')
                             ,'allowEmpty' => false
                             ,'message'    => 'Language code should consist of 2 letters'
                          )

         ,'name'        => array(
                             'rule'       => array('alphaNumeric')
                            ,'allowEmpty' => false
                            ,'message'    => 'Only letters and numbers are allowed for name'
                           )

         ,'nationalname' => array(
                              'rule'       => array('alphaNumeric')
                             ,'allowEmpty' => false
                             ,'message'    => 'Natinal name should contain only letters and numbers'
                           )
    );


    var $hasMany = array(

        'Content' => array(
             'className'  => 'Content'
            ,'order'      => 'Content.token ASC'
            ,'foreignKey' => 'language_id'
            ,'dependent'  => 'true'
        )//array

        ,'Metatag' => array(
             'className'  => 'Metatag'
            ,'order'      => 'Metatag.name ASC'
            ,'foreignKey' => 'language_id'
            ,'dependent'  => 'true'
        )//array

    );//array

}
?>