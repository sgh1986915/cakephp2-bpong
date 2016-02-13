<?php
class Slide extends AppModel
{
    var $name = 'Slide';
    var $recursive = -1;
    
    var $actsAs = array(
    'Image' => array(
    'thumbs' => array('create'=>true, 'width' => '120', 'height' => '52'),
    'versions'=>array(
                'slider'=>array('create' => true, 'width' => '960', 'height' => '410')
            )),     
            'Containable');                
}
?>