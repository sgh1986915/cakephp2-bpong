<?php
class PackagedetailsController extends AppController
{

    var $name = 'Packagedetails';
    var $helpers = array('Html', 'Form');
    var $uses      = array('Packagedetail','Manager');
    var $components = array('Time');

    /**
     * Add new packagedetail
     * @author vovivh
     * @param string $modelname - name of the model for wich will be added new packagedetail
     * @param int    $modelID   - ID of the model for which new packagedetail will be added
     * @param int    $packageID - package ID for which new details will be added
     */
    function add($modelName="Event",$modelID=null,$packageID=null) 
    {

        Configure::write('debug', 0);
        $this->layout = false;

        //Getting owners for this Model
        $owners = $this->__getModelManagersID($modelName, $modelID);

        if (!$this->RequestHandler->isAjax() || !$this->Access->getAccess('Packagedetails', 'c', $owners)  || !$modelID || !$packageID) {
            $this->Session->setFlash('This action is not permitted for you. Error 31', 'flash_error');
            echo "This action is not permitted for you. Error 31";
            exit();
        }

         /*Storing data*/
        if (!empty($this->request->data)) {
            $this->request->data['Packagedetail']['package_id']      = $packageID;
            $this->Packagedetail->create();

            $this->request->data['Packagedetail']['start_date'] = $this->Time->calendarToSql($this->request->data['Packagedetail']['start_date']);
            $this->request->data['Packagedetail']['end_date'] = $this->Time->calendarToSql($this->request->data['Packagedetail']['end_date']);

            if ($this->Packagedetail->save($this->request->data)) {
                exit();
            } else {
                $this->logErr('error occured while storing the packagedetail');
                exit("Error");
            }
        }
        /*EOF storing data*/

        $this->set('modelName', $modelName);
        $this->set('modelID', $modelID);
        $this->set('packageID', $packageID);

    }

    /**
     *  AJAX Edit packagedetail
     * @author vovivh
     * @param string $modelname - name of the model for wich will be added new packagedetail
     * @param int    $modelID   - ID of the model for which new packagedetail will be added
     * @param int    $packageID - package ID
     * @param int    $detailID  - package detail ID
     */
    function edit($modelName="Event",$modelID=null, $packageID=null,$detailID=null) 
    {

        Configure::write('debug', 0);
        $this->layout = false;

        //Getting owners for this Model
        $owners = $this->__getModelManagersID($modelName, $modelID);

        if (!$this->RequestHandler->isAjax() || !$this->Access->getAccess('Packagedetails', 'u', $owners) || !$packageID  || !$modelID || !$detailID) {
              $this->Session->setFlash('This action is not permitted for you. Error 32', 'flash_error');
              echo "This action is not permitted for you. Error 32";
              exit();
        }

         /* Storing data */
        if (!empty($this->request->data)) {
            $this->request->data['Packagedetail']['id'] = $detailID;
            $this->request->data['Packagedetail']['start_date'] = $this->Time->calendarToSql($this->request->data['Packagedetail']['start_date']);
            $this->request->data['Packagedetail']['end_date'] = $this->Time->calendarToSql($this->request->data['Packagedetail']['end_date']);
            if ($this->Packagedetail->save($this->request->data)) {
                exit();
            } else {
                $this->logErr('error occured while storing the packagedetail');
                exit("Error");
            }
        }
        /* EOF storing */
        if (empty($this->request->data)) {
            $this->request->data = $this->Packagedetail->read(null, $detailID);
            $this->request->data['Packagedetail']['start_date'] = $this->Time->sqlToCalendar($this->request->data['Packagedetail']['start_date']);
            $this->request->data['Packagedetail']['end_date'] = $this->Time->sqlToCalendar($this->request->data['Packagedetail']['end_date']);
        }
        $this->set('modelName', $modelName);
        $this->set('modelID', $modelID);
        $this->set('packageID', $packageID);
        $this->set('detailID', $detailID);
    }
    /**
     *  AJAX delete packagedetail
     * @author vovivh
     * @param string $modelname - name of the model for wich will be added new packagedetail
     * @param int    $modelID   - ID of the model for which new packagedetail will be added
     * @param int    $detailID  - package detail ID
     */
    function delete($modelName="Event",$modelID=null, $detailID=null) 
    {
        Configure::write('debug', 0);
        $this->layout = false;

         //Getting owners for this Model
        $owners = $this->__getModelManagersID($modelName, $modelID);

        if (!$this->RequestHandler->isAjax() || !$this->Access->getAccess('Packagedetails', 'd', $owners) || !$modelID || !$detailID ) {
             exit('This action is not permitted for you. Error 33');
        }

        $this->request->data['Packagedetail']['id']                = $detailID;
        $this->request->data['Packagedetail']['is_deleted']  = 1;
        $this->request->data['Packagedetail']['deleted']      = date('Y-m-d H:i:s');
        if ($this->Packagedetail->save($this->request->data, false)) {
            exit();
        } else {
            exit ("Error while deleting");
        }
    }

    /**
     *   getting managers for the Model for security
     *   @author vovich
     *   @param string $modelname - name of the model for wich will be added new packagedetail
     *   @param int    $modelID   - ID of the model for which new packagedetail will be added
     *   @return array managersID
     */
    function __getModelManagersID($modelName="User",$modelID=null) 
    {

            $managers = array();
            $managers = $this->Manager->find('list', array('conditions'=>array( 'model'=>$modelName,'model_id'=>$modelID ), 'fields'=>'user_id' ,'recursive'=>-1));

            return      $managers;

    }

}
?>
