<?php
class AddressesController extends AppController
{

    var $name    = 'Addresses';
    var $helpers = array('Html', 'Form');
    var $labels   = array('Home'=>'Home','Business'=>'Business','School'=>'School','Billing'=>'Billing','Additional'=>'Additional','Shipping'=>'Shipping');

    /**
     * AJAX view all addreses
     * @author vovivh
     * @param string                                                                                                    $modelname - name of the model for wich will be added new address
     * @param int                                                                                                       $modelID   - ID of the model for which new address will be added
     * @param int ownerID            - ID of the  Model owner (for the security) FE: for the venues will be venue owner
     * @param varchar                                                                                                   $label     - if !empty then working only with such label
     */
    function view($modelName="User",$modelID=null,$ownerID = null,$label=null) 
    {

        Configure::write('debug', 0);
        $this->layout = false;
         $conditions = array('Address.model'=>$modelName,'Address.model_id'=>$modelID,'Address.is_deleted'=>"0");
        if ($label) {
            $conditions['Address.label'] = $label; 
        }
        $this->request->data = $this->Address->find('all', array("conditions"=>$conditions));
        $conditions['Address.label'] = "Home";
        $homeCount = $this->Address->find('count', array("conditions"=>$conditions));

        $this->set('homeCount', $homeCount);
        $this->set('modelName', $modelName);
        $this->set('modelID', $modelID);
        $this->set('ownerID', $ownerID);
        $this->set('label', $label);

        /*pass to the view countries and states*/
        $countries_states = $this->Address->setCountryStates();

        $this->set('countries', $countries_states['countries']);
        $this->set('states', $countries_states['states']);
        $this->render();
    }

    /**
     * Add new address
     * @author vovivh
     * @param string                                                                                                    $modelname - name of the model for wich will be added new address
     * @param int                                                                                                       $modelID   - ID of the model for which new address will be added
     * @param int ownerID            - ID of the  Model owner (for the security) FE: for the venues will be venue owner
     * @param varchar                                                                                                   $label     - if !empty then working only with such label
     */
    function add($modelName="User",$modelID=null,$ownerID = null,$label=null) 
    {

        Configure::write('debug', 0);
        $this->layout = false;

        if (!$this->RequestHandler->isAjax() || !$this->Access->getAccess('Address', 'c', $ownerID) || !$ownerID || !$modelID ) {
            $this->Session->setFlash('This action is not permitted for you.', 'flash_error');
            $this->redirect($_SERVER['HTTP_REFERER']);
        }

         /*Storing data*/
        if (!empty($this->request->data)) {
            if ($label) {
                $this->request->data['Address']['label']   = $label; 
            }
            $this->request->data['Address']['model']       = $modelName;
            $this->request->data['Address']['model_id']    = $modelID;
            $this->Address->create();
            if ($this->Address->save($this->request->data)) {
                exit();
            } else {
                $this->logErr('error occured while storing the address');
                exit("Error");
            }
        }
        /*EOF storing data*/

        $this->set('modelName', $modelName);
        $this->set('modelID',  $modelID);
        $this->set('ownerID',  $ownerID);
        $this->set('labels',   $this->labels);
        $this->set('label',    $label);

        /*pass to the view countries and states*/
        $countries_states = $this->Address->setCountryStates();

        $this->set('countries', $countries_states['countries']);
        $this->set('states', $countries_states['states']);
    }

    /**
     * AJAX Edit address
     * @author vovivh
     * @param string                                                                                                    $modelname - name of the model for wich will be added new address
     * @param int                                                                                                       $modelID   - ID of the model for which new address will be added
     * @param int ownerID            - ID of the  Model owner (for the security) FE: for the venues will be venue owner
     * @param int                                                                                                       $addressID - address ID
     * @param varchar                                                                                                   $label     - if !empty then working only with such label
     */
    function edit($modelName="User",$modelID=null,$ownerID = null, $addressID=null, $label=null) 
    {

        Configure::write('debug', 0);
         $this->layout = false;

        if (!$this->RequestHandler->isAjax() || !$this->Access->getAccess('Address', 'u', $ownerID) || !$addressID  || !$modelID) {
            $this->Session->setFlash('This action is not permitted for you.', 'flash_error');
            $this->redirect($_SERVER['HTTP_REFERER']);
        }

         /* Storing data */
        if (!empty($this->request->data['Address'])) {

              //mark old address as deleted and create new one
            $address['Address']['id']         = $addressID;
            $address['Address']['is_deleted'] = 1;
            $address['Address']['deleted']    = date('Y-m-d H:i:s');
            $this->Address->save($address, false, array('is_deleted','deleted'));
            
            //create new address
            $this->request->data['Address']['model']    = $modelName;
            $this->request->data['Address']['model_id'] = $modelID;
            $this->Address->create();
            $this->Address->save($this->request->data);

        }
        /* EOF storing */

        if (empty($this->request->data)) {
            $this->request->data = $this->Address->read(null, $addressID);
        }
        $this->set('modelName', $modelName);
        $this->set('modelID', $modelID);
        $this->set('ownerID', $ownerID);
        $this->set('addressID', $addressID);
        $this->set('labels', $this->labels);
        $this->set('label', $label);
        /*pass to the view countries and states*/
        $countries_states=$this->Address->setCountryStates();

        $this->set('countries', $countries_states['countries']);
        $this->set('states', $countries_states['states']);
    }
    /**
     * AJAX delete address if address is home and it's the latest address then alert - can not remove the latest address
     * @author vovivh
     * @param int ownerID            - ID of the  Model owner (for the security) FE: for the venues will be venue owner
     * @param int                                                                                                       $addressID - address ID
     */
    function delete($ownerID = null, $addressID=null) 
    {
        Configure::write('debug', 0);
        $this->layout = false;

        if (!$this->RequestHandler->isAjax() || !$this->Access->getAccess('Address', 'd', $ownerID) || !$ownerID || !$addressID ) {
             exit('This action is not permitted for you.');
        }

        $this->Address->recursive = -1;
        $address = $this->Address->find('first', array('conditions'=>array('Address.id'=>$addressID)));
        if (empty($address)) {
            exit('Can not find such address.');
        }

        $this->Address->recursive = -1;
        if ($address['Address']['label'] == 'Home'  && $address['Address']['model']=='User' && $this->Address->find('count', array('conditions'=>array('Address.label'=>'Home','Address.model'=>'User','Address.model_id'=>$ownerID,'Address.is_deleted <>'=>1)))<=1) {
            exit('You can not remove this address such as must be at least one Home address.');
        }

        $this->request->data['Address']['id']          = $addressID;
        $this->request->data['Address']['is_deleted']  = 1;
        $this->request->data['Address']['deleted']     = date('Y-m-d H:i:s');
        $this->Address->save($address, false, array('is_deleted','deleted'));
        if ($this->Address->save($this->request->data, false)) {
            exit();
        } else {
            exit ("Error while deleting");
        }
    }
    /**
     * Set arrays for the countries and states to the view
     * @author vovich
     */
    function __setCountryStates()
    {

        $contriesID = $this->Address->Provincestate->find('all', array('fields'=> array('DISTINCT Provincestate.country_id'),'recursive' => -1,'contains' => array(),'conditions'=> array()));
        $contriesIDs = Set::extract($contriesID, '{n}.Provincestate.country_id'); 

        /*Countries*/
        $countries = $this->Address->Country->find('list', array('conditions'=>array('Country.id' => $contriesIDs)));
        $countries = array('0'=>"Select one") + $countries;
        $this->set('countries', $countries);

        if (empty($this->request->data['Address']['country_id'])) {
            $countryID = 0;
        } else {
            $countryID = $this->request->data['Address']['country_id'];
        }
        $conditions = array('conditions' => array('country_id' => $countryID),
                              'fields' => array('id', 'name'),
                              'recursive' => -1
        );

        $states = $this->Address->Provincestate->find('list', $conditions);
        if(!empty($states)) {
            $states=array('0'=>"Select one")+$states; 
        }
        else {
            $states=array('0'=>"Select one"); 
        }
        $this->set('states', $states);

    }

    /**
     *    Getting address information
     *  @author vovich
     *  @param string $modelname - name of the model for wich will be added new address
     *  @param int    $modelID   - ID of the model for which new address will be added
     *  @param int    $addressID
     *  @return JSON address information
     */
    function getAddressJson($modelName="User",$modelID=null,$addressID=null)
    {
        Configure::write('debug', 0);
         $this->layout = false;

        if (!$this->RequestHandler->isAjax() || !$addressID  || !$modelID) {
            exit('Error');
        }

        $conditions = array('model'=>$modelName,'model_id'=>$modelID,'id'=>$addressID);

        $this->Address->recursive = -1;
        $address = $this->Address->find('first', array('conditions'=>$conditions));
        exit($this->Json->encode($address));

    }
}
?>