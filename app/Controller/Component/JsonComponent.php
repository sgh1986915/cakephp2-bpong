<?php

/**
 * @name JsonComponent
 * @author Yuriy K.
 *
 * JSON encode wrapper.
 * Uses built-in json_encode php function if available and Services_JSON class if not.
 *
 * Usage in controllers: $this->Json->encode($variable);
 */

class JsonComponent extends Component
{

    /**
     * Encode data to JSON format
     *
     * @param  mixed $var
     * @return string json encoded data
     */

    function encode($var = null) 
    {
        if (phpversion('json')) {
            return json_encode($var);
        } else {

            App::import('Vendor', 'Services_JSON', array('file' => 'json.class.php'));
            $json = new Services_JSON();
            return $json->encode($var);
        }
    }

}
?>