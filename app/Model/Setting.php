<?php

class Setting extends AppModel
{

    var $name = 'Setting';

    var $validate = array
    (
         'type'           => array(
                              'rule'       => array('custom', '/^BOOL|STRING|FLOAT|INT$/')
                             ,'allowEmpty' => false
                             ,'message'    => 'This type is not allowed'
                          )

         ,'description' => array(
                             'rule'       => array('custom', '/^[a-zA-Z0-9.\' ]*$/')
                            ,'allowEmpty' => false
                            ,'message'    => 'Only letters and numbers are allowed'
                          )

         ,'name'        => array(
                              'rule'       => array('custom', '/^[a-zA-Z0-9.: ]*$/')
                             ,'allowEmpty' => false
                             ,'message'    => 'Name must contain only letters and numbers'
                          )

    );


    function beforeValidate($options = array()) 
    {

        switch ($this->data['Setting']['type']) {

        case 'INT':
            $this->validate['value'] = array(
                    array('rule'    => array('custom','/^[0-9]{1,5}$/')
                         ,'message' => 'The value should be integer')
                   ,array('rule'    => array('comparison', '>=', 0)
                         ,'message' => 'The value should be between 0 and 32768')
                   ,array('rule'    => array('comparison', '<=', 32768)
                         ,'message' => 'The value should be between 0 and 32768'));
            break;

        case 'FLOAT':
            $this->validate['value'] = array(
                 array('rule'    => array('custom','/^[0-9]{1,5}\.?[0-9]{0,5}$/')
                         ,'message' => 'The value should be float')
                   ,array('rule'    => array('comparison', '>=', 0)
                         ,'message' => 'The value should be between 0 and 32768')
                   ,array('rule'    => array('comparison', '<=', 32768)
                         ,'message' => 'The value should be between 0 and 32768'));
            break;

        case 'BOOL':
            $this->validate['value'] = array(
                 array('rule'    => array('custom','/^true|false|0|1$/')
                         ,'message' => 'The following values are allowed for BOOL type: true,false, 0, 1'));
            break;

        default: //STRING type is validated by default
            $this->validate['value'] = array(
                 array('rule'    => array('custom',"/^[a-zA-Z0-9'@. ]*$/")
                         ,'message' => 'The value mustn\'t contain special characters'));
            break;
        }

        return true;
    }

}
?>