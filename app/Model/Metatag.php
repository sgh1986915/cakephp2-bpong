<?php

class Metatag extends AppModel
{

    var $name = 'Metatag';

    var $validate = array
    (
    /*
         'content'      => array(
                              'rule'       => array('alphaNumeric')
	                         ,'allowEmpty' => false
        	                 ,'message'    => 'Natinal name should contain only letters and numbers'
        	               )
    */
    );

    var $belongsTo = array(
       'Language' => array(
             'className'  => 'Language'
            ,'foreignKey' => 'language_id'
            ,'fields'     => 'code'
       )//Content
    );//hasMany



}
?>