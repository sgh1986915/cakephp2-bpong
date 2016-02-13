<?php

App::import('Model', 'Content');

class GetcontentHelper extends AppHelper {

    var $helpers = array('Session', 'Html');


    /**
     * Enter description here...
     *
     * @param string $token
     * @param integer $editMode
     * @return string
     */

    function show($token = "",$editMode = true) {

    	if ( empty($token) ) {
            return '';
        }
		$out = "";
        $id  = "";

        $ses_LNG = $this->Session->read('User.Lang');
        $langId  = $ses_LNG['id'];

        $objContent = new Content();

		$content = $objContent->find('first', array(
						             'contain' => array()
									,'conditions' => array('token' => $token, 'language_id' => $langId)
								));

        if ( !empty($content)) {
            $out = $content['Content']['content'];
            $id = $content['Content']['id'];

        } else {
            //Get content for default Language
            $content = $objContent->find('first', array(
				             'contain' => array()
							,'conditions' => array('token' => $token, 'language_id' => DEFAULT_LANG_ID)
						));
            if (!empty($content) ) {
                $out = $content['Content']['content'];
            	$id = $content['Content']['id'];
            } else {
                /*Create New content*/
                $defaultContent = array( 'Content' => array(
                     'token'       => $token
                    ,'language_id' => DEFAULT_LANG_ID
                    ,'title'       => ''
                    ,'content'     => $token
                ));//array
                $objContent->save($defaultContent);
                $id  = $objContent->getLastInsertID();
                $out = $token;
            }
        }//else

        if ( $editMode ) {
            $out = '<span class="token">'
                  .   '<span class="data" id="token' . $id . '" >'
                  .     $out
                  .   '</span>'
                  .   $this->Html->link(
                  	  	 $this->Html->image('editable.gif', array('alt' => 'edit'))
                  	  	,"/contents/edittoken/{$id}?KeepThis=true&amp;TB_iframe=true&amp;height=500&amp;width=680"
                  	  	,array('class' => 'thickbox', 'title' => 'edit')
                  	  	,null
                  	  	,false
                  	  )
                  .'</span>';
        }//if

        return $out;

    }

}
