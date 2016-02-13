<?php

/**
 *  Usage in controller:
 *
 *      $this->Filter->setField('User', 'name');
 *      $criteria += $this->Filter->getFilter();
 *        $this->paginate('Image', $criteria));
 */



class FilterComponent extends Component
{

    var $components = array('Session');

    /**
     * @var array
     */
    var $filterCondition = array();


    /**
     * Startup component
     *
     * @param  object $controller Instantiating controller
     * @access public
     */

    function startup(&$controller) 
    {
        $this->controller = &$controller;
    }


    /**
     * Set filter for a field of a model
     * Usage in controller: $this->Filter->set('Model', 'field');
     *
     * @param string $model model name
     * @param string $field field name
     */

    function setField($model = null, $field = null, $condition = 'LIKE') 
    {
        //name session key like Filter.Model.Field
        $sessionKey = implode('.', array('Filter', $model, $field));
        //name post key like ModelField
        $postKey = $model . ucfirst($field);
        //shortcut for Model.field naming
        $modelField = $model . '.' . $field;
        //shortcut for post data
        $data = $this->controller->data['Filter'][$postKey];
        //filter expression
        $expression = '';

        //check post data first
        if (isset($data) ) {
            //trim and get the filter value
            $expression = $data = trim($data);
            //store it in session
            $this->Session->write($sessionKey, $data);
            //check session for the filter
        } elseif ($this->Session->check($sessionKey)) {
            //get from session if exists
            $expression = $this->Session->read($sessionKey);
        }//if

            //if the filter expression is not empty
        if (strlen($expression)) {
            //append the filter to the global filter conditions
            $condArray = array();
            switch ((string)$condition) {
            case '=':
                $condArray = array($modelField => $expression);
                break;
            default:
                  $condArray = array($modelField => 'LIKE %' . $expression . '%');
            }
            $this->filterCondition = am($this->filterCondition, $condArray);
        }
    }//eof set



    /**
     * Enter description here...
     *
     * @param unknown_type $model
     * @param unknown_type $fields
     */

    function setFields($model = null, $fields = array()) 
    {
        foreach ($fields as $field => $condition) {
            $this->setField($model, $field, $condition);
        }
    }


    /**
     * Enter description here...
     *
     * @param  unknown_type $type
     * @return unknown
     */

    function getFilter($type = 'or') 
    {
        if (!empty($this->filterCondition)) {
            return array($type => $this->filterCondition);
        }
        return array();
    }//eof getFilter

}
?>