<?php
class Mailtemplate extends AppModel
{

    var $name = 'Mailtemplate';
   
    var $adminEmail = null;

    function getTemplate($name = '', $params = array()) 
    {
        // separate arrays from static values
        $loops = array();
        foreach ($params as $key => &$val) {
            if (is_array($val)) {
                $loops[$key] = $val;
                unset($params[$key]);
            }
        }

        // default values
        $params['DOMAIN_NAME'] = defined('DOMAIN_NAME') ? 
                                 DOMAIN_NAME :
                                 $_SERVER['HTTP_HOST'];

        $keys = array();
        foreach ($loops as $key => $val) {
            if (isset($val[0]) && is_array($val[0])) {
                $keys[] = sprintf(
                    '{LOOP:%s}%s{ENDLOOP}',
                    $key,
                    $this->__implodeKeys($val[0], $key)
                ); 
            }
        }
        
        $paramKeys = $this->__implodeKeys($params, '', ',');
        if ($paramKeys) {
            $keys[] = $paramKeys;
        }
        $keys = implode($keys, ',');

        // read the template data from db
        $this->locale = Configure::read('Config.language');
        $template = $this->findByCode($name);
        if (empty($template['Mailtemplate'])) {
            $template = array(
                'language_id' =>1,
                'code'           => $name,
                'name'          => $name,
                'subject'        => 'no subject',
                'body'           => $keys,
                'keywords'     => $keys,
                'from'            => $this->adminEmail,
                'bcc'                => $template['Mailtemplate']['bcc']    
            );
            $this->create($template);
            $this->save();
            $this->id = $this->getLastInsertId();
        } else {
            $this->id = $template['Mailtemplate']['id']; 
            $template = $template['Mailtemplate'];
        }

        /*update keywords field if changed*/
        if ($keys != $template['keywords']) {
            $this->locale = false;
            $this->saveField('keywords', $keys);
        }
        /*replace all the loops first*/
        foreach ($loops as $key => $item) {
            $regexp = "/\{LOOP:$key\}(.*)\{ENDLOOP\}/";
            @preg_match($regexp, $template['body'], $matches);
            if (isset($matches[1]) && is_array($item)) {
                $pattern = $matches[1];
                unset($matches);
                $text = '';
                foreach ($item as $data) {
                    $text .=  $this->__replaceKeys($pattern, $data, $key);
                }
                $regexp = "/(.*)?\{LOOP:$key\}(.*)\{ENDLOOP\}(.*)?/";
                $template['body'] = preg_replace($regexp, "\$1$text\$3", $template['body']);
            }
        }
        $template['body'] = $this->__replaceKeys($template['body'], $params);
        $template['subject'] = $this->__replaceKeys($template['subject'], $params);

        return $template;
    }//eof


    function __keyStr($key = '', $parentName = '') 
    {
        if (empty($key)) {
            return '';
        }
        if ($parentName) {
            $key = $parentName . '.' . $key;
        }
        return '{' . $key . '}';
    }//eof


    function __implodeKeys($arr = array(), $parentName = '', $separator = '') 
    {
        $keys = array_keys($arr);
        foreach ($keys as &$key) {
            $key = $this->__keyStr($key, $parentName);
        }
        return implode($keys, $separator);
    }//eof


    function __replaceKeys($string = '', $replacements = array(), $parentName = '') 
    {
        foreach ($replacements as $key => $val) {
            $key = $this->__keyStr($key, $parentName);
            $string = str_replace((string)$key, (string)$val, (string)$string);
        }
        return $string;
    }//eof
     
}//class
