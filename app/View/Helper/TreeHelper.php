<?php 
class TreeHelper extends AppHelper {
/**
 * name property
 *
 * @var string 'Tree'
 * @access public
 */
	var $name = 'Tree';
	
/**
 * 
 * @param $elements
 * @param $pkPath
 * @param $namePath
 * @param $addChildUrl
 * @param $moveUrl
 * @param $editUrl
 * @param $deleteUrl
 * @param $sortUrl
 * @param $topicId
 * @param $hide
 * @param $parentsId
 * @return unknown_type
 */	
function multiTree($elements, $pkPath="", $namePath="", $addChildUrl="", $moveUrl="", $editUrl="", $deleteUrl="", $sortUrl = null,$topicId = NULL,$hide=true,$parentsId = array()) {
           
        if ($hide) {
		    $return = array('<ul style="display:none;">');
        } else {
            $return = array('<ul>');
        }   
 
		foreach ($elements as $element) {
           
		    $pk = Set::extract($pkPath, $element);
			$pk = $pk[0];
			$name = Set::extract($namePath, $element);
			$name = $name[0];
			$class = "";
			if ($topicId && $topicId == $pk) {
			    $class = "current";
			    $topicId = NULL;
			}
			
			
		    $return[] = '<li>';		    
            $return[] = '<span>';
            			
			if (!empty($element['children'])) {
			     if (!empty($parentsId) ) {
			        if (in_array($pk,$parentsId)) {
			            $hide = false;
			        } else {
			            $hide = true;
			        }
			     } else {
			         $hide = true;
			     }			 
			        
			    if (!$hide) {
			        $_class = " open";
			    } else { 
			        $_class ="";
			    }
			    
			    $return[] = '<em class="marker '.$_class.'"></em>
			    			<a onclick="javascript:nodeClick(this,'.$pk.')" class = "mainNode '.$class.'">'.$name.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			} else {
			    $return[] = '<a onclick="javascript:nodeClick(this,'.$pk.')" class = "'.$class.'">'.$name.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			
			if ($addChildUrl) {
    			$args = "{$addChildUrl}/{$pk}";
    			$return[] = '<a class="navigation" href="'.$args.'"><img src="/img/tree/add.gif" title="add child" /></a>';
			}
			if ($sortUrl) {
			    $args = "'{$sortUrl}/{$pk}/up', reloadOnSuccessJsFunction";
				$return[] = '<img src="/img/tree/arrow_up.gif" title="up" onclick="$.get('.$args.');" />';

				$args = "'{$sortUrl}/{$pk}/down', reloadOnSuccessJsFunction";
				$return[] = '<img src="/img/tree/arrow_down.gif" title="down" onclick="$.get('.$args.');" />';
			}
			if ($moveUrl) {
    			$args = "$pk, '$name', '$moveUrl'";
    			$return[] = '<img src="/img/tree/move.gif" title="move" onclick="moveTreeElement('.$args.');" />';
			}
			if ($editUrl) {
    			$args = "{$editUrl}/{$pk}";
    			$return[] = '<a class="navigation" href="'.$args.'"><img src="/img/tree/edit.gif" title="edit" /></a>';
			}
			
			if ($deleteUrl) {
                $args = "{$deleteUrl}/{$pk}";
    			$return[] = '<a class="navigation" href="'.$args.'"><img src="/img/tree/delete.gif" title="delete" /></a>';
			}
			$return[] = '</a>';
			$return[] = '</span>';
			if (!empty($element['children'])) {
				$return[] = $this->multiTree($element['children'], $pkPath, $namePath, $addChildUrl, $moveUrl, $editUrl, $deleteUrl, $sortUrl,$topicId,$hide,$parentsId);
			}
			$return[] = '</li>';
		}
		$return[] = '</ul>';
		return implode("\n", $return);
	}


}
?>