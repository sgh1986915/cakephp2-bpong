<?php
class PackagesController extends AppController
{

    var $name = 'Packages';
    var $helpers = array('Html', 'Form');
    var $uses      = array('Package','Manager');

    /**
     * AJAX view all Packages
     * @author vovivh
     * @param string $modelname - name of the model for wich will be added new package
     * @param int    $modelID   - ID of the model for which new package will be added
     */
    function view($modelName="Event",$modelID=null) 
    {
        Configure::write('debug', 0);
        $this->layout = false;
        //Getting owners for this Model
        $owners = $this->__getModelManagersID($modelName, $modelID);
        if (!$modelID ) {
            $this->Session->setFlash('This action is not permitted for you.', 'flash_error');
            echo "This action is not permitted for you.";
            exit();
            //$this->redirect($_SERVER['HTTP_REFERER']);
        }

        $this->request->data = $this->Package->find('all', array('recursive' =>1,"order"=>"is_hidden ASC","conditions"=>array('Package.model'=>$modelName,'Package.model_id'=>$modelID,'Package.is_deleted'=>"0")));
        $this->set('modelName', $modelName);
        $this->set('modelID', $modelID);
        $this->render();
    }



    /**
     * Add new package
     * @author vovivh
     * @param string $modelname - name of the model for wich will be added new package
     * @param int    $modelID   - ID of the model for which new package will be added
     */
    function add($modelName="Event",$modelID=null) 
    {

        Configure::write('debug', 0);
        $this->layout = false;

        //Getting owners for this Model
        $owners = $this->__getModelManagersID($modelName, $modelID);
        $ModelObject = ClassRegistry::init($modelName);        
        $model = $ModelObject->read(null, $modelID);
        
        if (!$this->RequestHandler->isAjax() || !$this->Access->getAccess('Package', 'c', $owners)  || !$modelID ) {
            $this->Session->setFlash('This action is not permitted for you.', 'flash_error');
            echo "This action is not permitted for you.";
            exit();
        }

         /*Storing data*/
        if (!empty($this->request->data)) {
            $this->request->data['Package']['model']      =$modelName;
            $this->request->data['Package']['model_id'] = $modelID;
            if (empty($this->request->data['Package']['people_in_room']) || intval($this->request->data['Package']['people_in_room']) < 1) {
                $this->request->data['Package']['people_in_room'] = 0;    
            }
                        
            $this->Package->create();
                        
            if ($this->Package->save($this->request->data['Package'])) {
                $id = $this->Package->getLastInsertID();
                if (!empty($this->request->data['Packagedetail']['price']) || !empty($this->request->data['Packagedetail']['price_team'])) {
                    $this->request->data['Packagedetail']['package_id'] = $id;
                    $this->request->data['Packagedetail']['start_date'] = date('Y-m-d');
                    if (!empty($model[$modelName]['finish_signup_date'])) {
                        $this->request->data['Packagedetail']['end_date'] = date('Y-m-d', strtotime($model[$modelName]['finish_signup_date']));
                    }
                    $this->Package->Packagedetail->save($this->request->data['Packagedetail']);            
                }
                exit();
            } else {
                $this->logErr('error occured while storing the package');
                exit("Error");
            }
        }


        /*EOF storing data*/
        $this->set('model', $model);
        $this->set('modelName', $modelName);
        $this->set('modelID', $modelID);

    }

    /**
     *  AJAX Edit package
     * @author vovivh
     * @param string $modelname - name of the model for wich will be added new package
     * @param int    $modelID   - ID of the model for which new package will be added
     * @param int    $packageID - package ID
     */
    function edit($modelName="Event",$modelID=null, $packageID=null) 
    {

        Configure::write('debug', 0);
         $this->layout = false;
        //Getting owners for this Model
        $owners = $this->__getModelManagersID($modelName, $modelID);
        $ModelObject = ClassRegistry::init($modelName);        
        $model = $ModelObject->read(null, $modelID);
        if (!$this->RequestHandler->isAjax() || !$this->Access->getAccess('Package', 'u', $owners) || !$packageID  || !$modelID) {
              $this->Session->setFlash('This action is not permitted for you.', 'flash_error');
              echo "This action is not permitted for you.";
              exit();
        }

         /* Storing data */
        if (!empty($this->request->data)) {
            $this->request->data['Package']['id'] = $packageID;
            
            if (empty($this->request->data['Package']['people_in_room']) || intval($this->request->data['Package']['people_in_room']) < 1) {
                $this->request->data['Package']['people_in_room'] = 0;    
            }
                        
            
            if ($this->Package->save($this->request->data)) {
                exit();
            } else {
                $this->logErr('error occured while storing the package');
                exit("Error");
            }
        }
        /* EOF storing */
        if (empty($this->request->data)) {
            $this->request->data = $this->Package->read(null, $packageID);
        }
        $this->set('model', $model);
        $this->set('modelName', $modelName);
        $this->set('modelID', $modelID);
        $this->set('packageID', $packageID);

    }
    /**
     *  AJAX delete package
     * @author vovivh
     * @param string $modelname - name of the model for wich will be added new package
     * @param int    $modelID   - ID of the model for which new package will be added
     * @param int    $packageID - package ID
     */
    function delete($modelName="Event", $modelID=null, $packageID=null) 
    {
        Configure::write('debug', 0);
        $this->layout = false;

         //Getting owners for this Model
        $owners = $this->__getModelManagersID($modelName, $modelID);

        if (!$this->RequestHandler->isAjax() || !$this->Access->getAccess('Package', 'd', $owners) || !$modelID || !$packageID ) {
             exit('This action is not permitted for you.');
        }

        $this->request->data['Package']['id']                = $packageID;
        $this->request->data['Package']['is_deleted']  = 1;
        $this->request->data['Package']['deleted']      = date('Y-m-d H:i:s');
        if ($this->Package->save($this->request->data, false)) {
            exit();
        } else {
            exit ("Error while deleting");
        }
    }

    /**
     *   getting managers for the Model for security
     *   @author vovich
     *   @param string $modelname - name of the model for wich will be added new package
     *   @param int    $modelID   - ID of the model for which new package will be added
     *   @return array managersID
     */
    function __getModelManagersID($modelName="User",$modelID=null) 
    {

            $managers = array();
            $managers = $this->Manager->find('list', array('conditions'=>array( 'model'=>$modelName,'model_id'=>$modelID ), 'fields'=>'user_id' ,'recursive'=>-1));

            return      $managers;

    }

    /**
     *  function for showing package detail for signup
     *   @author vovich
     *   @param int $packageID - ID of the package
     */
    function showpackagedetails($packageID=null) 
    {
          Configure::write('debug', 0);
          $this->layout = false;
        if (!$this->RequestHandler->isAjax() || !$packageID ) {
            exit('This action is not permitted for you.');
        }

         $package = $this->Package->find('first', array('conditions'=>array('id'=>$packageID,'is_deleted'=>0)));
         $details   = $this->Package->Packagedetail->find(
             'first', array('conditions'=>array('package_id'=>$packageID,
             ))
         );

         $this->request->data['Package']['id']           = $package['Package']['id'];
         $this->request->data['Package']['description']  = $package['Package']['description'];
         $this->request->data['Package']['deposit_id']   = $details['Packagedetail']['id'];
         $this->request->data['Package']['price']        = $details['Packagedetail']['price'];
         $this->request->data['Package']['deposit']      = $details['Packagedetail']['deposit'];

    }
    /**
 * For hidden packages - remove  user from the package
 * @param string    $modelname - name of the model for wich will be added new package
 * @param int       $modelID   - ID of the model for which new package will be added
 * @param $packageId
 * @param $userId
 * @return unknown_type
 */
    function removeUser($modelName = "Event", $modelID = null, $packageId = null, $userId = null) 
    {
        //Getting owners for this Model
        $owners = $this->__getModelManagersID($modelName, $modelID);
        $this->Access->checkAccess('Package', 'u', $owners);

        $this->Package->PackagesUser->deleteAll("package_id = $packageId AND user_id = $userId");


        $this->redirect($_SERVER['HTTP_REFERER']);
    }
    /**
 *
 * @param $modelName
 * @param $modelID
 * @param $packageId
 * @return unknown_type
 */
    function assignUser($modelName = "Event", $modelID = null, $packageID = null) 
    {
         Configure::write('debug', 0);
         $this->layout = false;

        //Getting owners for this Model
        $owners = $this->__getModelManagersID($modelName, $modelID);

        if (!$this->RequestHandler->isAjax() || !$this->Access->getAccess('Package', 'u', $owners) || !$packageID  || !$modelID) {
              $this->Session->setFlash('This action is not permitted for you.', 'flash_error');
              echo "This action is not permitted for you.";
              exit();
        }

         /* Storing data */
        if (!empty($this->request->data['User']['email'])) {
            $userId = $this->Package->User->field('id', array('email' => $this->request->data['User']['email']));
            if (!$userId) {
                exit("Error");
            }
            $data['user_id']    = $userId;
            $data['package_id'] = $packageID;
            $this->Package->PackagesUser->deleteAll("package_id = $packageID AND user_id = $userId");
            $this->Package->PackagesUser->create();
            if ($this->Package->PackagesUser->save($data)) {
                exit();
            } else {
                $this->logErr('error occured while storing the package');
                exit("Error");
            }
        }
        /* EOF storing */
        if (empty($this->request->data)) {
            $this->request->data = $this->Package->read(null, $packageID);
        }
        $this->set('modelName', $modelName);
        $this->set('modelID', $modelID);
        $this->set('packageID', $packageID);


    }
    /**
     * 
     * @return unknown_type
     */
    function selectType($packageID) 
    {
        Configure::write('debug', 0);
        $package = $this->Package->packagDetails($packageID);
        
        $this->set('package', $package);
        $this->set('packageID', $packageID);        
    }
}
?>