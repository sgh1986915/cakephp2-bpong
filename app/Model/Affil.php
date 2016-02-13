<?php
class Affil extends AppModel
{
    var $name     = 'Affil';
    var $useTable = false;
    var $affil_models = array('School','Greek','City','Organization');
    var $affil_types = array('School','Greek','Current','Hometown','Organization');
    
    var $types_and_models= array(
        'School'=>'School',
        'Greek'=>'Greek',
        'Hometown'=>'City',
        'Current'=>'City',
        'Organization'=>'Organization'
    ); 
    var $models_and_types = array(
        'School'=>'School',
        'Greek'=>'Greek',
        'City'=>array('Hometown','Current'),
        'Organization'=>'Organization'
    );
    function getTypeFromModel($modelName) 
    {
        if (isset($this->models_and_types[$modelName])) {
            return $this->models_and_types[$modelName]; 
        }
        else {
            return false; 
        }
    }                  
}
    
?>
