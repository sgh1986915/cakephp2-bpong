<?php

App::import('Model', 'Language');


/**
 * Prints language stuff
 *
 */

class LanguageHelper extends AppHelper {

	/**
	 * @var HtmlHelper
	 */
	var $Html;

    var $helpers = array(
    	 'Session'
    	,'Html'
	);//array


    /**
     * read language id from session
     *
     * @return int
     */

    function langID() {
        return $this->Session->read('User.Lang.id');
    }//eof langID


    /**
     * Return language flag image url
     *
     * @param int $id
     * @param mixed $attrs Html attributes of the flag image
     */

    function flag($id = null, $attrs = array()) {
		if (empty($id)) {
			$id = $this->langID();
		}//if
		$objLang = new Language();
		$lang = $objLang->read('flag', $id);
		return $this->Html->image('/img/flags/' . $lang['Language']['flag'], $attrs);
    }//eof flag


    /**
     * Get available languages list
     *
     * @return mixed
     */

    function availableLangs() {
        $objLang = new Language();
        //$conditions = $this->Access->isAdmin() ? array() : array('visible' => 1);
        $conditions = array();
        return $objLang->find('list', array('conditions' => $conditions));
    }//eof availableLangs
    
    // Pluralize text
    function pluralize ($val, $forOne, $forMany) {
        if ($val == 1) {
            return $forOne;        
        } else {
            return $forMany;        
        }            
    }     
}
