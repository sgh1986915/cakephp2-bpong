<?php

class ControlsHelper extends AppHelper {

    var $helpers = array('Html', 'Getcontent');

    
    /**
     * Edit icon
     *
     * @return string
     */
    
    function editImg() {
    	return $this->Html->image('layout/icons/accessories-text-editor.png', array('alt' => 'edit', 'title' => 'edit'));
    }

    
    /**
     * Delete icon
     *
     * @return string
     */
    
    function deleteImg() {
    	return $this->Html->image('layout/icons/edit-delete.png', array('alt' => 'delete', 'title' => 'delete'));
    }


    /**
     * Search icon
     *
     * @return string
     */
    
    function searchImg() {
    	return $this->Html->image('layout/icons/system-search.png', array('alt' => 'search', 'title' => 'search'));
    }
    
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $id
	 * @return unknown
	 */

    function edit($id = null, $url = null, $label = null) {
    	if (empty($id) && empty($url)) {
    		$url = array('action' => 'edit');
    	} elseif (!empty($id) && empty($url)) {
    		$url = array('action' => 'edit', $id);
    	} 

		return $this->Html->link(
			 'Edit'
    		,$url
    		,array('title' => 'Edit')
    		,null
    		,false
    	);//link
    }//eof edit

    
    function delete($id = null, $url = null, $label = null) {
    	if (empty($id) && empty($url)) {
    		$url = array('action' => 'delete');
    	} elseif (!empty($id) && empty($url)) {
    		$url = array('action' => 'delete', $id);
    	}
    	 
		return $this->Html->link(
        		 'Delete'
        		,$url
        		,array('title' => 'Delete')
        		,'Are you shure?'
        		,false
        );//link
    }//eof delete

    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    
    function search() {
    	return $this->Html->link(
    		"Search"//$this->Getimage->show('search', null, 0)
    	   ,$this->Html->url()
    	   ,array('id' => 'showfilter')
    	   ,null
    	   ,false
    	);
    }
    
}
