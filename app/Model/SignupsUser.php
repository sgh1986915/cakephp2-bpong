<?php
class SignupsUser extends AppModel
{

    var $name = 'SignupsUser';
    var $recursive = -1;
    var $actsAs = array ('Containable');
    
    var $belongsTo = array(
    'Signup' => array(
                'className' => 'Signup',
                'foreignKey' => 'signup_id'
    ),
    'User' => array(
                'className'    => 'User',
                'foreignKey'    => 'user_id'
            )
        );
}
?>
