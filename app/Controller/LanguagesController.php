<?php

class LanguagesController extends AppController
{

    var $name = 'Languages';

    /**
     * Displays language list
     */

    function index() 
    {
        $this->set('langs', $this->Language->find('all'));
    }//eof index

    
    //    /**
    //     * Make a language visible
    //     *
    //     * @param integer $id
    //     */
    //
    //    function visible($id = null) {
    //        $this->Access->onlyAdmin();
    //		Configure::write('debug', '0');
    //		if ( $this->RequestHandler->isAjax() && $id ) {
    //			$this->Language->id = $id;
    //			$this->Language->saveField('visible', 1);
    //		}//if			
    //        exit();
    //    }//eof visible

    
    //    /**
    //     * Make a language invisible
    //     *
    //     * @param integer $id
    //     */
    //
    //    function invisible($id = null) {
    //        $this->Access->onlyAdmin();
    //		Configure::write('debug', '0');
    //		if ( $this->RequestHandler->isAjax() && $id ) {
    //			$this->Language->id = $id;
    //			$this->Language->saveField('visible', 0);
    //		}//if			
    //        exit();
    //    }//eof invisible

    
    //    /**
    //     * Make a language editable
    //     *
    //     * @param int $id
    //     */
    //
    //    function editable($id) {
    //        $this->Access->onlyAdmin();
    //		Configure::write('debug', '0');
    //		if ( !$this->RequestHandler->isAjax() || !$id ) {
    //			exit();
    //		}
    //		$this->Language->id = $id;
    //		$this->Language->saveField('editable', 1);
    //        exit();
    //    }//eof editable

    
    //    /**
    //     * Make a language not editable
    //     *
    //     * @param int $id
    //     */
    //
    //    function noteditable($id) {
    //        $this->Access->onlyAdmin();
    //		Configure::write('debug', '0');
    //		if ( !$this->RequestHandler->isAjax() || !$id ) {
    //			exit();
    //		}
    //		$this->Language->id = $id;
    //		$this->Language->saveField('editable', 0);
    //        exit();
    //    }//eof noteditable

    
    //    /**
    //     * Create a new language
    //     *
    //     */
    //
    //    function addnew() {
    //
    //        $this->Access->onlyAdmin();
    //
    //		$this->Language->data['Language'] = array(
    //            'name' => 'NewLanguage'
    //           ,'nationalname' => 'NewLanguage'
    //           ,'code' => 'oo'
    //           ,'flag' => ''
    //           ,'visible' => '0'
    //           ,'editable' => '0'
    //		);//array
    //		$this->Language->save();
    //		return $this->redirect('/languages/index');
    //		exit();
    //    }//eof addnew

    
    //    /**
    //     * Delete language
    //     *
    //     * @param int $id
    //     */
    //
    //    function delete($id) {
    //        $this->Access->onlyAdmin();
    //		$this->Language->delete($id);
    //		return $this->redirect('/languages/index');
    //		exit();
    //    }//eof addnew

    
    /**
     * Edit language
     *
     * @param int $id
     */

    function edit($id) 
    {
        if (!empty($this->request->data['Language'])) {
            if ($id) {              
                $this->Language->id = $id;              
                $this->Language->set($this->request->data);
            } else {
                $this->Language->create($this->request->data);               
            }
            if ($this->Language->save()) {
                $this->redirect('/languages/index', null, true);//
                //exit();
            }
        } elseif ($id) {
            $this->request->data = $this->Language->read(null, $id); 
        }
    }//eof edit


    //    /**
    //     * [AJAX] Upload flag image.
    //     *
    //     * @return mixed JSON encoded structure
    //     */
    //
    //    function uploadflag() {
    //        $this->Access->onlyAdmin();
    //        Configure::write('debug', '0');
    //		if ( !$this->RequestHandler->isPost() ) {
    //			exit();
    //		}
    //		$response = array('error' => '', 'msg' => '');
    //
    //        vendor('upload.class');
    //
    //        $objFile = new upload($this->request->data['Language']['flagfile']);
    //        $objFile->file_auto_rename = true;
    //
    //        if ($objFile->uploaded) {
    //            $newName = 'flag' . time();
    //            $newExt = end(explode('.',$this->request->data['Language']['flagfile']['name']));
    //            $objFile->file_new_name_ext = $newExt;
    //            $objFile->image_resize = true;
    //            $objFile->file_safe_name = true;
    //            $objFile->mime_magic_check = true;
    //            $objFile->file_new_name_body = $newName;
    //            $objFile->allowed = array('image/jpeg', 'image/pjpeg', 'image/gif', 'image/png');
    //            $objFile->image_y = 30;
    //            $objFile->image_x = 60;
    //            $objFile->process('img/flags/');
    //            if ($objFile->processed) {
    //                $response['msg'] = $newName . '.' . $newExt;
    //                $objFile->clean();
    //            } else {
    //                $response['error'] = $objFile->error;
    //            }//if
    //        }//if
    //        
    //        exit($this->Json->encode($response));        
    //    }//eof uploadflag


}