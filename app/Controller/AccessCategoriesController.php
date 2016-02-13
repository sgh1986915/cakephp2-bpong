<?php 
class AccessCategoriesController extends AppController
{

    var $name = 'AccessCategories';
    
    /**
     * Add new Category
     * @author vovich
     */
    function add() 
    {
        $this->Access->checkAccess('Accesscategories', 'c');
        if (!empty($this->request->data)) {
            $this->AccessCategory->create();
            if ($this->AccessCategory->save($this->request->data)) {
                $this->Session->setFlash('New category has been added', 'flash_success');
                return $this->redirect(array('controller'=>'accessions','action'=>'index'));
            } else {
                $this->Session->setFlash('New category can not be added. Please, try again.', 'flash_error');
            }
        }
        
    }
    /**
     * edit Category
     * @author vovich
     * @param int $id
     */
    function edit($id = null) 
    {
        $this->Access->checkAccess('Accesscategories', 'u');
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash('Invalid id category', 'flash_error');
            return $this->redirect(array('controller'=>'accessions','action'=>'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->AccessCategory->save($this->request->data)) {
                $this->Session->setFlash('Category has been saved', 'flash_success');
                return $this->redirect(array('controller'=>'accessions','action'=>'index'));
            } else {
                $this->Session->setFlash('The Category can not be saved. Please, try again.', 'flash_error');
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->AccessCategory->read(null, $id);
        }
        
    }

    /**
     * delete access category
     * @author vovich
     * @param $id status id
     * @return unknown_type
     */
    function delete($id = null) 
    {
        $this->Access->checkAccess('UserGroup', 'd');
        if (!$id) {
            $this->Session->setFlash('Invalid Category id', 'flash_error');
            return $this->redirect(array('controller'=>'accessions','action'=>'index'));
        }
        if ($this->AccessCategory->GetObjectsCnt($id)>0) {
            $this->Session->setFlash('This Category contain security objects, please remove them at first.', 'flash_error');
            $this->redirect(array('controller'=>'accessions','action'=>'index'));
        }
        if ($this->AccessCategory->del($id)) {
            $this->Session->setFlash('Access category deleted', 'flash_success');
            return $this->redirect(array('controller'=>'accessions','action'=>'index'));
        }
                 
    }

        
}
?>
