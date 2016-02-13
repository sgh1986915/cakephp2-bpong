<?php
class ContentsController extends AppController
{

    var $name = 'Contents';
    var $helpers = array('Html', 'Form');
    var $uses    = array('Content','Language');

    /**
     * Show contents
     * @author vovich
     */
    function index() 
    {
        $this->Content->recursive = 1;
        $contents = $this->paginate();
        $this->set('contents', $contents);
    }
    /**
     * Edit content from page
     * @author vovichv
     * @param int $id
     */
    function edit($id = null) 
    {
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash('Invalid Content', 'flash_error');
            return $this->redirect(array('action'=>'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->Content->save($this->request->data)) {
                $this->Session->setFlash('The Content has been saved', 'flash_success');
                return $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash('The Content could not be saved. Please, try again.', 'flash_error');
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Content->read(null, $id);
        }
        $languages = $this->Language->find('list');
        $this->set(compact('languages'));
    }
    /**
     * Delete content
     * @author vovich
     */
    function delete($id = null) 
    {
        if (!$id) {
            $this->Session->setFlash('Invalid id for Content', 'flash_error');
            return $this->redirect(array('action'=>'index'));
        }
        if ($this->Content->del($id)) {
            $this->Session->setFlash('Content deleted', 'flash_success');
            return $this->redirect(array('action'=>'index'));
        }
    }
    /**
     * Edit content from the Thickbox
     * @author vovich
     * @param int $id - token ID
     */
    function edittoken($id = 0)
    {
        Configure::write('debug', '0');
        $this->layout = 'thickbox';

        if (empty($this->request->data)) {
            $this->request->data = $this->Content->read(null, $id);
        }
        $languages = $this->Language->find('list');
        $this->set(compact('languages'));


    }
}
?>
