<?php
class PhonesController extends AppController
{

    var $name = 'Phones';
    var $helpers = array('Html', 'Form');
    var $types   = array('Home'=>'Home','Work'=>'Work','Cell'=>'Cell','Other'=>'Other');

    /**
 * AJAX view all Phones
 * @author vovivh
 * @param string                                                                                                    $modelname - name of the model for wich will be added new phone
 * @param int                                                                                                       $modelID   - ID of the model for which new phone will be added
 * @param int ownerID            - ID of the  Model owner (for the security) FE: for the venues will be venue owner  
 */
    function view($modelName="User",$modelID=null,$ownerID = null) 
    {
        Configure::write('debug', 0);
        $this->layout = false;         
        $conditions = array('Phone.model'=>$modelName,'Phone.model_id'=>$modelID,'Phone.is_deleted'=>"0", 'LENGTH(Phone.phone) >' => "0"); 
        $this->request->data = $this->Phone->find('all', array("conditions"=>$conditions));
        
        $cnt = $this->Phone->find('count', array("conditions"=>$conditions));
        if ($cnt > 0) {
            $this->request->data['Phone']['cnt'] = $cnt;    
        }
         
        $this->set('modelName', $modelName);
        $this->set('modelID', $modelID);
        $this->set('ownerID', $ownerID);
        $this->render();
        
    }
    
    
    
    /**
 * Add new phone 
 * @author vovivh
 * @param string                                                                                                    $modelname - name of the model for wich will be added new phone
 * @param int                                                                                                       $modelID   - ID of the model for which new phone will be added
 * @param int ownerID            - ID of the  Model owner (for the security) FE: for the venues will be venue owner  
 */
    function add($modelName="User",$modelID=null,$ownerID = null) 
    {
        
        Configure::write('debug', 0);
        $this->layout = false; 
        
        if (!$this->RequestHandler->isAjax() || !$this->Access->getAccess('Phone', 'c', $ownerID) || !$ownerID || !$modelID ) {
            $this->Session->setFlash('This action is not permitted for you.', 'flash_error');
            $this->redirect($_SERVER['HTTP_REFERER']);      
        }        
        
         /*Storing data*/
        if (!empty($this->request->data)) {
            $this->request->data['Phone']['model']      =$modelName;
            $this->request->data['Phone']['model_id'] = $modelID;
            $this->Phone->create();
            if ($this->Phone->save($this->request->data)) {
                exit();
            } else {
                $this->logErr('error occured while storing the phone');
                exit("Error");
            }
        }
        /*EOF storing data*/
        
        $this->set('modelName', $modelName);
        $this->set('modelID', $modelID);
        $this->set('ownerID', $ownerID);
        $this->set('types', $this->types);
    }
    /**
 *  AJAX Edit phone 
 * @author vovivh
 * @param string                                                                                                    $modelname - name of the model for wich will be added new phone
 * @param int                                                                                                       $modelID   - ID of the model for which new phone will be added
 * @param int ownerID            - ID of the  Model owner (for the security) FE: for the venues will be venue owner
 * @param int                                                                                                       $phoneID   - phone ID  
 */    
    function edit($modelName="User",$modelID=null,$ownerID = null, $phoneID=null) 
    {
        
        Configure::write('debug', 0);
        $this->layout = false; 
        
        if (!$this->RequestHandler->isAjax() || !$this->Access->getAccess('Phone', 'u', $ownerID) || !$phoneID  || !$modelID) {
            $this->Session->setFlash('This action is not permitted for you.', 'flash_error');
            exit('this action is not permitted for you');      
        }     
         
         /* Storing data */
        if (!empty($this->request->data)) {
            $this->request->data['Phone']['id'] = $phoneID;
            if ($this->Phone->save($this->request->data)) {
                exit();
            } else {
                $this->logErr('error occured while storing the phone');
                exit("Error");
            }
        }
        /* EOF storing */
        if (empty($this->request->data)) {
            $this->request->data = $this->Phone->read(null, $phoneID);
        }
        
        $this->set('modelName', $modelName);
        $this->set('modelID', $modelID);
        $this->set('ownerID', $ownerID);        
        $this->set('phoneID', $phoneID);
        $this->set('types', $this->types);
    
    }
    /**
 *  AJAX delete phone 
 * @author vovivh
 * @param int ownerID            - ID of the  Model owner (for the security) FE: for the venues will be venue owner
 * @param int                                                                                                       $phoneID - phone ID  
 */
    function delete($ownerID = null, $phoneID=null) 
    {
        Configure::write('debug', 0);
        $this->layout = false; 
         
        if (!$this->RequestHandler->isAjax() || !$this->Access->getAccess('Phone', 'd', $ownerID) || !$ownerID || !$phoneID ) {
             exit('This action is not permitted for you.');
        }    

        $this->request->data['Phone']['id']                = $phoneID;
        $this->request->data['Phone']['is_deleted']  = 1;
        $this->request->data['Phone']['deleted']      = date('Y-m-d H:i:s');
        if ($this->Phone->save($this->request->data, false)) {
            exit();
        } else {
            exit ("Error while deleting");
        }
    }
    
}
?>