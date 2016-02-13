<?php

class Content extends AppModel
{

    var $name = 'Content';

    var $belongsTo = array(
       'Language' => array(
             'className'  => 'Language'
            ,'foreignKey' => 'language_id'
            ,'fields'     => 'code'
       )//Content
    );//hasMany



}
