<?php
class AccessionsController extends AppController
{

    var $name = 'Accessions';
    var $uses    = array('Accession','Group','Status','Obj','AccessCategory');
    var $accessLevels = array('0'=>'Deny','2'=>'Allow','1'=>'Owner');
    var $accessTypes  = array('c'=>'Create','u'=>'Update','r'=>'Read','d'=>'Delete','l'=>'List');

    
    /**
 * show all acess categories
 * @author vovich
 * @return view
 */    
    function index()
    {
         $this->Access->checkAccess('Accessions', 'u');
     
         $categories = $this->AccessCategory->getAccessCategories();
         $this->set('categories', $categories);
        
         unset($AccessCategory);
    }

    /**
 * Show access by current category
 * @param $accessCategoryID
 * @return view
 */    
    function show($accessCategoryID = null) 
    {
    
        $this->Access->checkAccess('Accessions', 'u');

        /*Getting statuses*/
         $this->Status->recursive = 0;
         $statuses = $this->Status->find('all', array('order'=>'Status.group_id'));
        
         /*Getting objects*/
         $objects = $this->Obj->find('all', array('conditions'=>array('category_id'=>$accessCategoryID),'order'=>'name'));
    
         /*Getting permissions*/
         $permissions = $this->Access->getAllPermissons();    

         /*Getting categories*/
         $categories = $this->AccessCategory->find('list', array('fields'=>array('id','name'),'order'=>'name'));
           
         $this->set(compact('statuses', 'objects', 'permissions', 'categories'));
         $this->set('accessTypes', $this->accessTypes);
         $this->set('accessLevels', $this->accessLevels);

    }       
    

    /**
 * AJAX call for changing access
 * @author vovich
 * @param $_POST['obj_id']
 * @param $_POST['status_id']
 * @param $_POST['access_type'] c/u/r/d
 * @param $_POST['access'] true/false
 */
    function changeAccess()
    {
        if ($this->Access->getAccess('Accessions', 'u')) {
            Configure::write('debug', '0');
            $this->layout = false;
            if (empty($_POST['access'])) {
                $_POST['access'] = 0; 
            }

            $sql = "UPDATE ".$this->Group->tablePrefix."access SET ".$_POST['access_type']."=".$_POST['access']." WHERE object_id=".$_POST['obj_id']." AND status_id=".$_POST['status_id'];

            $this->Accession->query($sql);
            $this->Access->loadobjToCache();
        }
        exit();
    }


    /**
 * AJAX call for changing category
 * @author vovich
 * @param $_POST['obj_id']
 * @param $_POST['category_id']
 */
    function changeCategory() 
    {
    
        if ($this->Access->getAccess('Accessions', 'u')) {
            Configure::write('debug', '0');
            $this->layout = false;
            if (empty($_POST['access'])) {
                $_POST['access'] = 0; 
            }

            $sql = "UPDATE ".$this->Group->tablePrefix."objects SET category_id =".$_POST['category_id']." WHERE id=".$_POST['obj_id'];

            $this->Accession->query($sql);
        
        }
        exit($sql);
    
    
    }

    /**
 * AJAX call for changing status
 * @author vovich
 * @param $_POST['obj_id']
 * @param $_POST['status_id']
 * @param $_POST['as_default'] true or false
 */
    function sameAsDefault() 
    {
        Configure::write('debug', '0');
        $this->layout = false;
        if ($this->Access->getAccess('Accessions', 'u')) {
            if ($_POST['as_default']=="true") {
                /*If as default then remove data from the table*/
                $sql = "DELETE FROM ".$this->Group->tablePrefix."access WHERE object_id = ".$_POST['obj_id']." AND status_id =".$_POST['status_id'];
                $this->Accession->query($sql);

            } else {
                $sql = "DELETE FROM ".$this->Group->tablePrefix."access WHERE object_id = ".$_POST['obj_id']." AND status_id =".$_POST['status_id'];
                $this->Accession->query($sql);
                $sql = "INSERT INTO ".$this->Group->tablePrefix."access (object_id,status_id) VALUES (".$_POST['obj_id'].",".$_POST['status_id'].")";
                $this->Accession->query($sql);
            }
        }
        $this->Access->loadobjToCache();
        exit();
    }


    function clear_cache() 
    {
        //$this->Access->checkAccess('Accessions','u');
        $this->layout = false;
        $this->autoRender = false;
        $cachePaths = array('models', 'js', 'css', 'menus', 'views', 'persistent');
        foreach($cachePaths as $config) {
            clearCache(null, $config);
            echo $config . ' - cache deleted<br/>';
        }
        Cache::clear();
        Cache::clear(false, "memcache");
        exit;
        
    } 
}
?>
