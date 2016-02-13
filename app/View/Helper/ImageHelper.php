<?php

class ImageHelper extends AppHelper {

    var $helpers = array('Form', 'Html');

    /**
     * Manage Images for Add/Edit actions
     * @param string $modelName
     * @param array $images
     * @param bool $add_attrs
     * @param int $images_count - how many images can has current object
     * @param array $options
     * @return string
     * @author Oleg D.
     */
	function manageImages($modelName = '', $add_attrs = false, $images = array(), $images_count = 0, $options = array()){
		$out = '';
		$add_new = 0;
		
		if (!isset($options['default_alt'])) {
			$options['default_alt'] = '';			
		}
		if (!isset($options['default_title'])) {
			$options['default_title'] = '';			
		}
		if (!isset($options['default_description'])) {
			$options['default_description'] = '';			
		}				
						
        if (!empty($images)) {
            foreach ($images as $image) {
                $out .= '<a href="' . IMG_MODELS_URL . '/' . $image['Image']['filename'] . '" title="" class="thickbox">' .
                	'<img src="' . IMG_MODELS_URL . '/thumbs_' . $image['Image']['filename'] . '" border="0"></a>';
				$out .= '<div><a href="/Images/delete/' . $image['Image']['id'] . '" onclick="return confirm(\'Are you sure?\')" >Delete</a>  <a href="/Images/edit/' . $image['Image']['id'] . '">Edit</a></div>';		           
    		   //$out .= $this->Form->input('Image.' . $image['Image']['id'],array('type' => 'file','class'=>'file', 'label'=>__('Edit')));
            }
        }	
      	if (!$images_count){
      		$add_new = 1;	
      	} else {
      		if ($images_count > count($images)) {
      			$add_new = 1;	
      		}else {
      			$add_new = 0;	
      		}	
      	}
        if ($add_new){
        	$out .= $this->Form->input('Image.new',array('type' => 'file','class'=>'file','label'=>__('New Image')));	 		
        }
        if ($add_attrs) {
			
	        $out .= $this->Form->input('Image.new.title', array('size' => 100, 'label' => 'Image Title'));
			$out .= $this->Form->input('Image.new.alt', array('size' => 100, 'label' => 'Image Alternative text'));
			$out .= $this->Form->input('Image.new.description', array('label' => 'Image Description'));			
		}else{
									
			if($options['default_alt']) {
				$out .= $this->Form->hidden('Image.new.alt', array('value' => $options['default_alt']));				
			}
			if($options['default_title']) {
				$out .= $this->Form->hidden('Image.new.title', array('value' => $options['default_title']));				
			}	
			if($options['default_description']) {
				$out .= $this->Form->hidden('Image.new.description', array('value' => $options['default_description']));				
			}						
		}
		       
		return $out;
	}
	/**
	 * Show Avatar
	 * @author Oleg D.
	 */
	function avatar ($avatarValue, $showDefault = true, $size = 40, $attr = array()){
	    if ($avatarValue) {
	        return $this->Html->image(IMG_AVATARS_URL . '/' . $size . '_' . $avatarValue, $attr);
	    } else {
	        if ($showDefault) {
	            return $this->Html->image(IMG_AVATARS_URL . '/default_' . $size . '.gif', $attr);
	        } else {
	            return false;
	        }
	    }
	    
	    return false;
	}
}
