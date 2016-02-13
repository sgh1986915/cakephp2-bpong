<?php class SignupsController extends AppController
{

    var $name    = 'Signups';
    var $helpers = array('Html', 'Form');
    var $uses     = array('Signup', 'SignupRoom', 'SignupRoommate','Package','Address','Payment','Question','Answer','Promocode', 'SignupsUser', 'Address', 'Phone');
    var $components = array('Time');
    var $paginate = array('order'=>array('Signup.id' => 'desc'));


    /**
* @author vovivh
* @param string $modelname - name of the model for wich will be added new signup
* @param int    $modelID   - ID of the model for which new signup will be added
*/
    function step1($modelName="Event",$slug=null) 
    {

        Configure::write('debug', '0');
        $this->Access->checkAccess('Signup', 'c', null, SECURE_SERVER);

        if ($this->Session->check('loggedUser')) {
            $userSession = $this->Session->read('loggedUser');
        } else{
            $this->redirect('/');
        }
    
        $modelInfo = $this->Signup->$modelName->find('first', array('conditions'=>array('slug'=>$slug)));
        if (empty($modelInfo)) {
            $this->Session->setFlash('Event does not exist.', 'flash_error');
            $this->redirect(MAIN_SERVER);
        }

        $gotoStep = null;
        if ($this->Session->read('signup_payment_error')) {
            $gotoStep = 4;            
        } else {
             
            //Creating session for the last step
            $step1['modelName'] = $modelName;
            $step1['slug']             = $slug;
            $this->Session->write('SignupStep1', $step1);

            /*check does we have any packages and package details*/
            $id   = $this->Signup->$modelName->field('id', 'slug="'.$slug.'"');
            $name = $this->Signup->$modelName->field('name', 'id='.$id);
            $agreement = $this->Signup->$modelName->field('agreement', 'id='.$id);
            $this->set('agreement', $agreement);
            $finish_signup_date = $this->Signup->$modelName->field('finish_signup_date', 'id='.$id);


            if ((!empty($finish_signup_date) && $this->Time->fromString($finish_signup_date)<strtotime(date("Y-m-d")) )) {
                 $this->Session->setFlash('Signup is not available anymore.', 'flash_error');
                 $this->redirect(MAIN_SERVER);
           }

            $this->pageTitle = "Signup to the ".$name.".";

            //check if already signed up
            $this->Signup->recursive= -1;
            $singups = $this->Signup->find('first', array('conditions'=>array('model'=>$modelName,'model_id'=>$id,'user_id'=>$userSession['id'],'status'=>array('partly paid','paid'))));
            if(!empty($singups)) {
                return $this->redirect('/signups/signupDetails/'.$singups['Signup']['id']);
            } else {
                $SignupsUser = ClassRegistry::init('SignupsUser');
                $SignupsUser->contain('Signup');
                $signupUser =  $SignupsUser->find('first', array('conditions' => array('Signup.model_id' => $id, 'Signup.model' => $modelName,'Signup.status'=>array('partly paid','paid'), 'SignupsUser.user_id' => $userSession['id'])));
                pr($signupUser);
                if (!empty($signupUser)) {      
                    return $this->redirect('/signups/signupDetailsTeammate/'.$signupUser['Signup']['id']);
                } 
            }
            //EOF checking


             $packages  =  $this->Package->find(
                 'all', array('conditions'=>array('model'  =>$modelName,
                                                                        'model_id'  =>$id,
                                      'is_deleted'=>0
                 ))
             );
            if (!empty ($packages) &&  $this->Package->cntPackagesDetails($modelName, $id)==0) {
                $this->render('error');
            }
            /*EOF*/
        }
        $this->set('gotoStep', $gotoStep);
        $this->set('modelName', $modelName);
        $this->set('slug', $slug);


    }

    /**
* @author vovivh
* @param string $modelname - name of the model for wich will be added new signup
* @param int    $modelID   - ID of the model for which new signup will be added
*/
    function step2( $modelName = "Event", $slug = null ) 
    {
        $this->Logger->write('signup', 'Step2', array('userID' => $this->getUserID(), 'modelName' => $modelName, 'modelID' =>'' , 'description' => 'Step2', 'array' => $_SESSION));
         $userSession = array();
         Configure::write('debug', '0');

        //if (!$this->RequestHandler->isAjax() || !$this->Access->getAccess('Signup','c')  || !$modelName || !$slug){
        if (!$this->Access->getAccess('Signup', 'c')  || !$modelName || !$slug) {
     
             $this->Session->setFlash('This action is not permitted for you. Error 190', 'flash_error');
             echo "This action is not permitted for you. Error 190";
             exit();
        }

        if ($this->Session->check('loggedUser')) {
            $userSession = $this->Session->read('loggedUser');
        }

        /* Storing data */
        if (!empty($this->request->data)) {
            if (!empty($this->request->data['Address'])) {
                $this->request->data['Address']['model']       = 'User';
                $this->request->data['Address']['model_id']    = $this->getUserID();
                $this->Address->create();
                $this->Address->save($this->request->data['Address']);
                unset($this->request->data['Address']);
            }
            $this->request->data['User']['id'] = $userSession['id'];
               //remove validations
             unset($this->Signup->User->validate);
            if (empty($this->request->data['User']['subscribed'])) {
                unset($this->request->data['User']['subscribed']);
                unset($this->request->data['User']['old_subscribed']);
            }
            if ($this->Signup->User->save($this->request->data)) {
                exit();
            } else {
                exit("Error");
            }
        }
        /*EOF storing*/

        if (!empty($userSession)) {
             $this->Signup->User->recursive = 0;
             $this->request->data = $this->Signup->User->read(null, $userSession['id']);
             $this->request->data['User']['old_subscribed'] = $this->Signup->User->Mailinglist->isUserInList($this->request->data['User']['id'], LISTID);
             $this->request->data['User']['subscribed']     = 1;
        }
        /*check does we have any packages*/
        $modelInfo = $this->Signup->$modelName->find('first', array('conditions'=>array('slug'=>$slug)));
        if (empty($modelInfo)) {
            exit('Some errors while loading: '.$slug . ' and '.$modelName);
        }
        /*sTUPID HACK  for B by Vovich*/
        $maxAge = 21;
        if (!empty($modelInfo[$modelName]['venue_id'])) {
            $country_id = $this->Address->field('country_id', array('model'=>'Venue','model_id'=>$modelInfo[$modelName]['venue_id'],'is_deleted <>'=>1));
            if ($country_id == 2) {/*CANADA*/
                $maxAge = 18;
            }
        }
        $this->set('maxAge', $maxAge);
        /*EOF Stupid hack*/
        $id = $modelInfo[$modelName]['id'];
        $packages  =  $this->Package->find('list', array('conditions'=>array('model' => $modelName, 'model_id'  =>$id, 'is_deleted'=>0)));
    
        $isFreeEvent  =  $this->Package->isFreeEvent($this->Access->getLoggedUserID(), $modelName, $id);

    
    
        if(empty($this->request->data['User']['birthdate'])) {
            $this->request->data['User']['birthdate'] = '';
        }

        $homeCount = $this->Address->find('count', array('conditions' => array('Address.model' => 'User', 'Address.model_id' => $this->getUserID(), 'Address.is_deleted' => '0', 'Address.label' => 'Home')));
        if (!$homeCount) {
            /*pass to the view countries and states*/
            $countries_states = $this->Address->setCountryStates();
    
            $this->set('countries', $countries_states['countries']);
            $this->set('states', $countries_states['states']);        
        }
    
        $this->set('genders', array(''=>'Select one','M'=>'Male','F'=>'Female'));
        $this->set('homeCount', $homeCount);
        $this->set('packages', $packages);  
        $this->set('isFreeEvent', $isFreeEvent);      
        $this->set('modelName', $modelName);
        $this->set('slug', $slug);
        $this->set('modelInfo', $modelInfo[$modelName]);
    }

    /**
   * @author vovivh
   * @param string $modelname - name of the model for wich will be added new signup
   * @param int    $modelID   - ID of the model for which new signup will be added
   */
    function step3($modelName="Event",$slug=null) 
    {
        $this->Logger->write('signup', 'Step3', array('userID' => $this->getUserID(), 'modelName' => $modelName, 'modelID' =>'' , 'description' => 'Step3', 'array' => $_SESSION));
         $userSession = array();
         Configure::write('debug', '0');

         $id = $this->Signup->$modelName->field('id', 'slug="'.$slug.'"');
        if (!$this->RequestHandler->isAjax() || !$this->Access->getAccess('Signup', 'c')  || !$modelName || !$slug || !$id) {
             $this->Session->setFlash('This action is not permitted for you. Error 20', 'flash_error');
             exit("This action is not permitted for you. Error 20");
        }
        /*     if ($this->Session->check('loggedUser')) {
         $userSession = $this->Session->read('loggedUser');
        }
        */
        /* Storing data */
        if (!empty($this->request->data) ) {
            //Getting package information
            $packageInfor = $this->Package->packagDetails($this->request->data['Package']['id']);

            if (empty($packageInfor)) {
                exit("Error while getting package details");
            }
        
            if (!empty($this->request->data['Signup']['for_team']) && $this->request->data['Signup']['for_team'] == 1) {
                $this->request->data['Package']['price'] = $packageInfor['packagedetails']['price_team'];
                $this->request->data['Package']['totalprice']        = $packageInfor['packagedetails']['price_team'];
                $this->request->data['Package']['deposit']            = $packageInfor['packagedetails']['deposit'] * $packageInfor['packages']['people_in_room'];        
            } else {
                $this->request->data['Package']['price']             = $packageInfor['packagedetails']['price'];
                $this->request->data['Package']['totalprice']        = $packageInfor['packagedetails']['price'];
                $this->request->data['Package']['deposit']            = $packageInfor['packagedetails']['deposit'];          
            }       
            $this->request->data['Package']['packagedetails_id'] = $packageInfor['packagedetails']['id'];
            //Create session

            $this->Session->write('SignupStep3', $this->request->data + array('modelName' => $modelName, 'slug' => $slug));
            exit();
        }
        /*EOF storing*/
        $this->request->data['Package']['id'] = '0';
        if ($this->Session->check('SignupStep3')) {
            $this->request->data = $this->Session->read('SignupStep3');
        }

        $packages  =  $this->Package->packagesList($this->Access->getLoggedUserID(), $modelName, $id);
        //echo "<pre/>";
        //print_r($packages);

        if (empty($this->request->data['Package']['id'])) {
            $this->request->data['Package']['id'] =  key($packages);
        }

        //working with questions
        $questions = $this->Question->find('all', array('conditions'=>array('model'=>$modelName,'model_id'=>$id)));
        $this->set('questions', $questions);

        //working with questions
        $this->set('packages', $packages);
        $this->set('modelName', $modelName);
        $this->set('slug', $slug);
        $this->set('id', $id);
    }


    /**
   * @author vovivh
   * @param string $modelname - name of the model for wich will be added new signup
   * @param int    $modelID   - ID of the model for which new signup will be added
   */
    function step4($modelName="Event",$slug=null) 
    {
        $userID = $this->getUserID();
        $email = $this->Signup->User->field('email', array('User.id' => $userID));     
    
        $this->Logger->write('signup', 'Step4', array('userID' => $userID, 'modelName' => $modelName, 'modelID' =>'' , 'description' => 'Step4', 'array' => $_SESSION));

        $discount = array();

        $userSession     = array();
        $oldPriceText    = "";
        $oldDepositText  = "";
        $discountAmount  = 0;
        $discountID = 0;
        $oldPrice = $oldDeposit = 0;
        $discountInformation = array();

        $id = $this->Signup->$modelName->field('id', 'slug="'.$slug.'"');//getting model ID

        if (!$this->RequestHandler->isAjax() || !$this->Access->getAccess('Signup', 'c')  || !$modelName || !$slug || !$id || !$this->Session->check('SignupStep3') || !$this->Session->check('loggedUser')) {
             $this->Session->setFlash('This action is not permitted for you. Error 21', 'flash_error');
             exit( "This action is not permitted for you. Error 21");
        }

        if ($this->Session->check('loggedUser')) {
            $userSession = $this->Session->read('loggedUser');
        }
        if ($this->Session->check('SignupStep3')) {
            $step3 = $this->Session->read('SignupStep3');
        }

        $packageInfor = $this->Package->packagDetails($step3['Package']['id']);

        if (empty($packageInfor)) {
            exit("Error while getting package details");
        }
        
        if (!empty($step3['Signup']['for_team']) && $step3['Signup']['for_team'] == 1) {
             $step3['Package']['price'] = $packageInfor['packagedetails']['price_team'];
             $step3['Package']['totalprice']        = $packageInfor['packagedetails']['price_team'];
             $step3['Package']['deposit']            = $packageInfor['packagedetails']['deposit'] * $packageInfor['packages']['people_in_room'];        
        } else {
             $step3['Package']['price']             = $packageInfor['packagedetails']['price'];
             $step3['Package']['totalprice']        = $packageInfor['packagedetails']['price'];
             $step3['Package']['deposit']            = $packageInfor['packagedetails']['deposit'];          
        }         
    
        $this->Signup->User->recursive =0;
        $this->request->data = $this->Signup->User->read(null, $userID);

        /*Countries*/
        /*$countries = $this->Address->Country->find('list');
        $countries = array('0'=>"Select one") + $countries;
        $this->set('countries',$countries );*/
        /*pass to the view countries and states*/
        $countries_states = $this->Address->setCountryStates();
        $this->set('countries', $countries_states['countries']);
        $this->set('states', $countries_states['states']);

        //Getting  address
        $this->Address->recursive = -1;
        $addresses = $this->Address->find('list', array('fields' => array('id', 'address'),'conditions'=>array('model'=>'User','model_id'=>$userSession['id'],'is_deleted <>'=>1),'order'=>'id DESC'));
        $addresses = array('0'=>"Custom address") + $addresses;
        //states
        $conditions = array('conditions' => array('country_id' => 0),
                'fields' => array('id', 'name'),
                'recursive' => -1
        );

        /*    $states = $this->Address->Provincestate->find('list',$conditions);
        $states = array('0'=>"Select one") + $states;
        $this->set('states',$states);*/
        //EOF states
        //working with Discounts
        if (!empty($step3['Package']['promocode'])) {
            $discountInformation = $this->Promocode->checkCoupon($step3['Package']['promocode'], $modelName, $id, $userSession['id']);
            $discountID = $discountInformation['id'];
            //echo "<pre/>";
            //print_r($step3);
          
            if (!is_array($discountInformation)) {
                //error with discount
                $this->set('discountError', $discountInformation);
            } else {
                //if this is free discount and it's  cheepest package then show new view
                if ($discountInformation['type'] == "Free") {
                    $cheepestpackage = $this->Package->getCheepesPackage($modelName, $id);

                    if ($step3['Package']['packagedetails_id'] == $cheepestpackage['packagedetails']['id'] && $step3['Signup']['for_team'] != 1) {
                        //This is free package
                        $discountAmount    = floatval($cheepestpackage['packagedetails']['price']);

                        $this->Session->write('SignupDiscount', array('discountAmount'=>$discountAmount,'discountInformation'=>$discountInformation));
                        $this->set('modelName', $modelName);
                        $this->set('slug', $slug);
                        $this->render('free_package');
                    } else {
                        //This is free discount but not cheepest - then discount = price of the cheepest package
                        $discount = 'You have a discount $'.$cheepestpackage['packagedetails']['price'];

                        $oldPrice                = $step3['Package']['price'];
                        $oldDeposit            = $step3['Package']['deposit'];
                        $discountAmount    = floatval($cheepestpackage['packagedetails']['price']);
                    }

                } else {
                    $discount       = $this->Promocode->formatDiscount($discountInformation);
                    $oldPrice          = $step3['Package']['price'];
                    $oldDeposit      = $step3['Package']['deposit'];
                    $discountAmount = $this->Promocode->calculateDiscountAmount($discountInformation, $packageInfor['packagedetails']['price']);
                }

                if ($discountAmount>0) {
                    //calculate new  price
                    $step3['Package']['price'] = sprintf("%.2f", floatval($step3['Package']['price']) -  floatval($discountAmount));
                    //deposit can not be greater then price
                    if (floatval($step3['Package']['price']) < floatval($step3['Package']['deposit'])) {
                        $step3['Package']['deposit'] = $step3['Package']['price']; 
                    }
                    if ($oldPrice != $step3['Package']['price']) {
                        $oldPriceText   = '<strike>$'.$oldPrice.'</strike> '; 
                    }
                    if ($oldDeposit != $step3['Package']['deposit']) {
                        $oldDepositText = '<strike>$'.$oldDeposit.'</strike> '; 
                    }
                }

                //Creation new session with discount information
                $this->Session->write('SignupDiscount', array('discountAmount'=>$discountAmount,'discountInformation'=>$discountInformation));
                $this->Session->write('SignupStep3', $step3);
            }
        }
        //this is for the radiobuttons
        if (floatval($step3['Package']['deposit'])==floatval($step3['Package']['price']) || floatval($step3['Package']['deposit']) == 0 || empty($step3['Package']['deposit']) ) {
            $amounts = array(sprintf("%01.2f", $step3['Package']['price'])   => "Full price ".$oldPriceText."$" . sprintf("%01.2f", $step3['Package']['price']));
        } else {
            $amounts = array(sprintf("%01.2f", $step3['Package']['deposit']) => "Deposit ".$oldDepositText."$".sprintf("%01.2f", $step3['Package']['deposit']),
                 sprintf("%01.2f", $step3['Package']['price'])   => "Full price ".$oldPriceText."$".sprintf("%01.2f", $step3['Package']['price']),
                 'custom'  => "any amount between the deposit amount and the full price. ");
        }

        // Authorize.net DPM installation ===========================================================================================

        //App::import('Vendor', 'AuthorizeNet', array('file' => 'anet_php_sdk'. DS .'AuthorizeNet.php'));
        include_once '../vendors/anet_php_sdk/AuthorizeNet.php';

        $authorizeNetProperties = array(
            //'x_amount'        => $amount,
            //'x_fp_sequence'   => $fp_sequence,
            //'x_fp_timestamp'  => $time,
            'x_relay_response'=> "TRUE",
            'x_merchant_email'=> ADMIN_EMAIL,
            //'x_relay_url'     => SECURE_SERVER . '/checkouts/payment_callback',
            'x_relay_url'     => SECURE_SERVER . '/signups/payment_callback',
            'x_delim_data'  => "TRUE",
            'x_delim_char'  => ","
        );


        if (SIGNUP_AUTH_NET_TEST_MODE) {
            $authLogin = SIGNUP_AUTH_NET_TEST_LOGIN_ID;
            $authKey = SIGNUP_AUTH_NET_TEST_TRAN_KEY;
            $authorizeNetProperties['x_test_request'] = 'TRUE';
            $authorizeNetURL = AuthorizeNetDPM::SANDBOX_URL;
        } else {
               $authLogin = SIGNUP_AUTH_NET_LOGIN_ID;
               $authKey = SIGNUP_AUTH_NET_TRAN_KEY;
            $authorizeNetProperties['x_test_request'] = 'FALSE';
            $authorizeNetURL = AuthorizeNetDPM::LIVE_URL;
        }

        $authorizeNetProperties['x_freight'] = '0';
        //$authorizeNetProperties['x_po_numz'] = $signupID; /// ! after
    
        //additional customer data
        $authorizeNetProperties['x_cust_id'] = $this->getUserID();
        $authorizeNetProperties['x_customer_ip'] = $_SERVER['REMOTE_ADDR'];

        $authorizeNetProperties['x_merchant_email'] = 'no-reply@bpong.com';
        //$authorizeNetProperties['x_invoice_num'] = $signupID; /// ! after
        //$authorizeNetProperties['x_description'] = 'BPONG.COM Signup.' . $signupID; /// ! after
        $authorizeNetProperties['x_login'] = $authLogin;
        $authorizeNetProperties['x_email'] = $email;

        foreach ($authorizeNetProperties as $key => $value) {
            $authorizeNetProperties[$key] = addslashes($value);
        }
        
        $sim = new AuthorizeNetSIM_Form($authorizeNetProperties);
        $authorizeNetHiddens = $sim->getHiddenFieldString();

        // EOF Authorize Net configuration
        
        if ($this->Session->check('signup_payment_error')) {
            $payment_error = $this->Session->read('signup_payment_error');
            $this->Session->delete('signup_payment_error');
        } else {
            $payment_error = '';
        }

        $this->set('authorizeNetHiddens', $authorizeNetHiddens);
        $this->set('authorizeNetURL', $authorizeNetURL);   
        $this->set('payment_error', $payment_error); 
           
        // EOF Authorize.net DPM installation ==========================================================================
        if ($this->Session->check('signup_payment_error')) {
            $payment_error = $this->Session->read('signup_payment_error');
            $this->Session->delete('signup_payment_error');
        } else {
            $payment_error = '';
        }
        if ($this->Session->check('last_payment_id')) {
            $last_payment_id = $this->Session->read('last_payment_id');
            $payment = $this->Payment->find('first', array('conditions' => array('Payment.id' => $last_payment_id)));
            $addressID = $payment['Payment']['address_id'];
            $phone = $this->Phone->field('phone', array('id' => $payment['Payment']['phone_id']));
            
        } else {
            $addressID = 0;
            $phone = '';          
        }
           
        $this->set(compact('amounts', 'userID', 'step3', 'modelName', 'slug', 'discount', 'discountID', 'payment_error', 'addressID', 'phone'));

        $this->set('cardtypes', array('Visa'=>'Visa','MasterCard'=>'MasterCard', 'AmericanExpress' => 'American Express', 'Discover' => 'Discover'));
        $this->set('addressesIds', $addresses);

        if (!empty($oldPrice) && $oldPrice == $discountAmount) {
            //This is free package
            $this->render('free_package');
        }
    }
  
    /**
   * Payment callback for authorize.net
   * @author Oleg D.
   */
    function payment_callback() 
    {
        Configure::write('debug', '0');
        $this->layout = false;
        //Configure::write('debug', 1);
        include_once '../vendors/anet_php_sdk/AuthorizeNet.php';
                
        if (SIGNUP_AUTH_NET_TEST_MODE) {
            $authLogin = SIGNUP_AUTH_NET_TEST_LOGIN_ID;
            $authSetting = AUTHORIZENET_MD5_SETTING;
        } else {
            $authLogin = SIGNUP_AUTH_NET_LOGIN_ID;
            $authSetting = AUTHORIZENET_MD5_SETTING;
        }    
        
        $response = new AuthorizeNetSIM($authLogin, $authSetting);

        $signupID = intval($_POST['data']['Addition']['sd']);
        $userID = intval($_POST['data']['Addition']['ud']);    
        $amount = $_POST['x_amount'];
        $discountID = intval($_POST['data']['Addition']['dd']);
        
        // 1 - first time payment, 2 - complete payment after partly paid
        $payment_process_num = intval($_POST['data']['Addition']['payment_process_num']);

        
        $signupStatus['Signup']['id'] = $signupID;
        $this->Signup->recursive = -1;
        $signup = $this->Signup->find('first', array('conditions' => array('Signup.id' => $signupID)));
        
        if (empty($signup['Signup']['id'])) {
            exit('Signup ID error!');
        }
        
        if ($response->isAuthorizeNet()) {
                  
            $address_id = $this->_storeBillingAddress($userID, $_POST['x_address'], $_POST['data']['Addition']['address2'], $_POST['x_city'], $_POST['data']['Addition']['state_id'], $_POST['x_zip'], $_POST['data']['Addition']['country_id']);                            
            $phoneID = $this->Phone->addPhone($_POST['x_phone'], $userID);    
            //store payment
                  $payment = array();
                  $payment['model']        = "Signup";
                  $payment['model_id']     = $signupID;
                  $payment['user_id']      = $userID;
                  $payment['payment_date'] = date('Y-m-d H:i:s');
            if ($response->approved) {
                      $payment['status']       = 'Approved';
            } else {
                $payment['status']       = 'Declined';    
            }
                  $payment['amount']       = $amount;
                  $payment['reason']       = $_POST['x_response_reason_text'];
                  $payment['description']  = $_POST['x_description'];
                  $payment['information']  = serialize($_POST);
                  $payment['address_id']   = $address_id;
                  $payment['promocode_id'] = $discountID;
                  $payment['phone_id'] = $phoneID;
    
                  $this->Payment->create();
                  $this->Payment->save($payment);
                  $paymentId = $this->Payment->getLastInsertID();
                  $this->Payment->savePaymentPromocodes($payment['promocode_id'], $paymentId);
                
            if ($response->approved) {
                if (floatval($signup['Signup']['total']) - floatval($signup['Signup']['discount']) == (floatval($signup['Signup']['paid']) + floatval($payment['amount']))) {
                          $signupStatus['Signup']['status'] = "paid";
                          $signupStatus['Signup']['paid']   = floatval($signup['Signup']['paid'] + $payment['amount']);
                          $this->Promocode->usePromoCode($payment['promocode_id']);//updatecount of use
                } else {
                      $signupStatus['Signup']['status'] ="partly paid";
                      $signupStatus['Signup']['paid']   = floatval($signup['Signup']['paid'] + $payment['amount']);
                      $this->Promocode->usePromoCode($payment['promocode_id']);//updatecount of use
                }                
                if ($payment_process_num == 1) {
                      $return_url = SECURE_SERVER . '/signups/thankyou/' . $paymentId;                        
                } elseif ($payment_process_num == 2) {
                      $return_url = SECURE_SERVER . '/signups/complete_payment_redirect/' . $paymentId;
                }
                  
            } else {
                if ($signup['Signup']['paid'] > 0) {
                    $signupStatus['Signup']['status'] = "partly paid";    
                } else {
                    $signupStatus['Signup']['status'] ="not paid";                              
              }
                if ($payment_process_num == 1) {
                    $return_url = SECURE_SERVER . '/signups/payment_error_redirect/' . $paymentId . '/?error=' . htmlspecialchars($response->response_reason_text);                        
                } elseif ($payment_process_num == 2) {
                    $return_url = SECURE_SERVER . '/signups/complete_payment_error_redirect/' . $signupID . '/' . $paymentId . '/?error=' . htmlspecialchars($response->response_reason_text);
                }                               
                        
            }                  
                 
        } else {
            if ($signup['Signup']['paid'] > 0) {
                $signupStatus['Signup']['status'] = "partly paid";    
            } else {
                $signupStatus['Signup']['status'] ="not paid";                              
            }
            //echo "MD5 Hash failed. Check to make sure your MD5 Setting matches the one in config.php";
            if ($payment_process_num == 1) {
                $return_url = SECURE_SERVER . '/signups/payment_error_redirect/' . $paymentId . '/?error=' . htmlspecialchars('MD5 Hash failed');                        
            } elseif ($payment_process_num == 2) {
                $return_url = SECURE_SERVER . '/signups/complete_payment_error_redirect/' . $signupID . '/' . $paymentId . '/?error=' . htmlspecialchars('MD5 Hash failed');
            }                    
        }
        $this->Signup->save($signupStatus);
        
        echo AuthorizeNetDPM::getRelayResponseSnippet($return_url);
        exit();
    }
    
    /**
     * Redirect user if error payment
     * @author Oleg D.
     */
    function payment_error_redirect($paymentID) 
    {
        Configure::write('debug', 0);

        $error = $_GET['error'];
        $step3 = $this->Session->read('SignupStep3');
        $this->Session->write('last_payment_id', $paymentID);
                               
        $this->Session->write('signup_payment_error', $error);
        return $this->redirect(SECURE_SERVER . '/signups/' . $step3['modelName'] . '/' . $step3['slug']);
        exit;    
    }
    
    /**
     * Redirect user if error payment while completing payment
     * @author Oleg D.
     */
    function complete_payment_error_redirect($signupID, $paymentID) 
    {
        Configure::write('debug', 0);
        $error = $_GET['error'];

        $this->Session->write('last_payment_id', $paymentID);
        $this->Session->write('signup_payment_error', $error);
        return $this->redirect(SECURE_SERVER . '/signups/signupDetails/' . $signupID);
        exit;    
    }
    /**
     * Redirect user to event avter comleted payment
     * @author Oleg D.
     */
    function complete_payment_redirect($paymentID) 
    {
        Configure::write('debug', '1');    
        $payment = $this->Payment->find('first', array('conditions' => array('Payment.id' => $paymentID)));
        $signupID = $payment['Payment']['model_id'];
        $signupDetails = $this->_getSignupDetails($signupID);
                
        $result = $this->sendMailMessage(
            'CompletePayment', array(
            '{USERNAME}'  => $signupDetails['User']['lgn'],
            '{FNAME}'     => $signupDetails['User']['firstname'],
            '{LNAME}'     => $signupDetails['User']['lastname'],
            '{MODEL}'     => strtolower($signupDetails['Signup']['model']),
            '{MNAME}'     => $signupDetails[$signupDetails['Signup']['model']]['name'],
            '{TOTAL}'     => '$'.sprintf("%.2f", $signupDetails['Signup']['total']),
            '{REMAINING}' => 0,
            '{PAID}'      => '$'.sprintf("%.2f", $payment['Payment']['amount']),
            '{LINK}'      => MAIN_SERVER."/events/my"),
            $signupDetails['User']['email']
        );
         $this->Session->del('last_payment_id');
         $this->Session->setFlash('Thank You For Your Payment.', 'flash_success');
         $this->redirect(SECURE_SERVER . '/signups/signupDetails/' . $signupID);

    }
        
    /**
   * Create FP hash code by Ajax
   * @author Oleg D.
   */    
    function ajaxCreateFpHash() 
    { 
        Configure::write('debug', '0');    
        if ($this->RequestHandler->isAjax() && !empty($_REQUEST['amt'])) {
            $result = array();
        
            if (STORE_AUTH_NET_TEST_MODE) {
                $authLogin = SIGNUP_AUTH_NET_TEST_LOGIN_ID;
                $authKey = SIGNUP_AUTH_NET_TEST_TRAN_KEY;
            } else {
                   $authLogin = SIGNUP_AUTH_NET_LOGIN_ID;
                   $authKey = SIGNUP_AUTH_NET_TRAN_KEY;
            }  
            include_once '../vendors/anet_php_sdk/AuthorizeNet.php'; 
        
            $result['x_amount'] = sprintf("%01.2f", $_REQUEST['amt']);
            $result['x_fp_timestamp'] = $result['x_fp_sequence'] = time();          
            $result['x_fp_hash'] = AuthorizeNetDPM::getFingerprint($authLogin, $authKey, $result['x_amount'], $result['x_fp_sequence'], $result['x_fp_timestamp']);
        
            exit($this->Json->encode($result));
        }
    }
    /**
   * Save signup after click "pay" and before payment process
   */
    function ajaxSaveSignup() 
    {
        Configure::write('debug', '0');
        if ($this->RequestHandler->isAjax() && !empty($_REQUEST['amt'])) {

            $result = array();
            $signupID = 0;
            $userID = $this->getUserID();
            $step3 = $this->Session->read('SignupStep3');
           
            $modelName = $step3['modelName'];
            $slug = $step3['slug'];
          
            $discount = 0;
            $this->Signup->$modelName->recursive = -1;
            $model    = $this->Signup->$modelName->find('first', array('conditions'=>array('slug'=> $slug)));
            $id            = $model[$modelName]['id'];
        
            $SignupDiscount = array();
            $amount = $_REQUEST['amt'];
        
            //Calculate Promocode
            if (isset($step3['Package']['promocode']) && !empty($step3['Package']['promocode']) && $this->Session->check('SignupDiscount')) {
                $SignupDiscount = $this->Session->read('SignupDiscount');
            }          
          
            //Store Signup
            $signup = array();
            $signup['user_id']              = $userID;
            $signup['model']                = $modelName;
            $signup['model_id']             = $id;
            $signup['signup_date']          = date('Y-m-d H:i:s');
            $signup['packagedetails_id']    = $step3['Package']['packagedetails_id'];
            $signup['total']                   = $step3['Package']['totalprice'];
            $signup['for_team']               = intval($step3['Signup']['for_team']);
            $signup['discount']             = empty($SignupDiscount['discountAmount'])?0:$SignupDiscount['discountAmount'];

            //searching if such sign up already exists
            $tnhis->Signup->recursive = -1;
            $signups = $this->Signup->find('first', array('conditions'=>array('model' => $modelName, 'model_id' => $id,'user_id' => $userID, 'status'=>array('partly paid', 'paid'))));

        
            if (!empty($signups)) {
                exit ('You have already signed up.');
            }
            unset($signups);
       
            $signupID  = $this->Signup->createSignup($modelName, $id, $userID, $signup);

            if (!$signupID) {
                exit ('Error while storing sign up');
            }
            $this->Session->write('signupID', $signupID);
            //Storing answers
            if (!empty($step3['Question'])) {
                $this->__storeAnswers($modelName, $id, $userID, $step3);
            }                  
          
            $result['x_po_numz'] = $signupID;
            $result['x_invoice_num'] = $signupID;          
            $result['x_description'] = $model[$modelName]['name'];
            $result['signup_id'] = $signupID;
                  
        }
        exit($this->Json->encode($result));    
    }  
    
    /**
 * Congratilation step with sending email
  * @author vovivh
  */
    function thankyou($paymentID = 0) 
    {
         $this->Access->checkAccess('Signup', 'c');

        if (!$this->isLoggined() || !$this->Session->check('signupID')) {
            $this->Session->setFlash('Error: You are not logged in.', 'flash_error');
            $this->redirect(MAIN_SERVER);
        }
       
         $signupId = $this->Session->read('signupID');
         $address =  $this->Address->find('first', array('conditions' => array('Address.id' => $signupId)));
       
        if ($paymentID) {
            $payment = $this->Payment->find('first', array('conditions' => array('Payment.id' => $paymentID)));
            $amount = $payment['Payment']['amount'];
        } else {
            $amount = 0;
            $payment['Payment']['amount'] = 0;    
        } 
              
        if ($this->Session->check('SignupStep4')) {
            $signupStep4 = $this->Session->read('SignupStep4');
        } else {
             $signupStep4['amount'] = 0;
        }

        $this->Signup->recursive = -1;
        $signupInformation  = $this->Signup->find('first', array('conditions'=>array('Signup.id' => $signupId, 'Signup.user_id' => $this->getUserID())));
        if (empty($signupInformation['Signup']['status'])) {
            exit('Signup Error s.u.1');    
        }
        $isFreeSignup = 0;
        if ($signupInformation['Signup']['status'] == 'paid' && ($signupInformation['Signup']['total'] + $signupInformation['Signup']['discount']) == 0) {
            $isFreeSignup = 1;    
        }
    
        if (empty($signupInformation)) {
            $this->sendErrorMessage("Can not get signup information");
        }

        $modelName = $signupInformation['Signup']['model'];
        $modelId       = $signupInformation['Signup']['model_id'];

        if ($this->Session->check('loggedUser')) {
            $userSession = $this->Session->read('loggedUser');
        }
        if ($this->Session->check('SignupStep3')) {
            $SignupStep3 = $this->Session->read('SignupStep3');
        } else {
            $SignupStep3 = array();
            $SignupStep3['Package']['price']       = 0;
            $SignupStep3['Package']['deposit']    = 0;
            $SignupStep3['Package']['totalprice'] = 0;
        }

        $this->Signup->recursive = 0;
        $modelInfo = $this->Signup->{$modelName}->find('first', array('conditions'=>array($modelName.'.id'=>$modelId)));
        if (empty($modelInfo)) {
               $this->sendErrorMessage("Can not get package information for the model" . $modelName . " with modelId: " . $modelId);
        }
        $slug = $modelInfo[$modelName]['slug'];
        $this->set('modelInfo', $modelInfo[$modelName]);

        $this->Signup->User->recursive = -1;
        $userInfo = $this->Signup->User->find('first', array('conditions'=>array('id'=>$userSession['id'])));

        if (!empty($SignupStep3['Package']['id'])) {
            $this->Package->recursive = -1;
            $packageInfor = $this->Package->find('first', array('conditions'=>array('id'=>$SignupStep3['Package']['id'])));
            if (empty($packageInfor)) {
                 $this->sendErrorMessage("Can not get package information");
            }
        } else {
            $packageInfor['Package']['name']        = "";
            $packageInfor['Package']['description'] = "";
        }
        //sending congratulation email
        if (!empty($userInfo['User']['firstname'])) {
            $username = $userInfo['User']['firstname']." ".$userInfo['User']['lastname'];
        } else {
            $username = $userInfo['User']['lgn'];
        }
        if ($isFreeSignup) {
            $messageName = 'NewFreeSignup';    
        } else {
            $messageName = 'NewSignup';            
        }
        $result = $this->sendMailMessage(
            $messageName, array(
                     '{USERNAME}'      => $username,
                     '{FNAME}'             => $userInfo['User']['firstname'],
                     '{LNAME}'             => $userInfo['User']['lastname'],
                     '{MODEL}'             => strtolower($modelName),
                     '{MNAME}'            => $modelInfo[$modelName]['name'],
                     '{PNAME}'             => $packageInfor['Package']['name'],
                     '{PDESCRIPTION}'  => $packageInfor['Package']['description'],
                     '{PRICE}'               => $signupInformation['Signup']['total'],
                     '{DEPOSIT}'           => $SignupStep3['Package']['deposit'],
                     '{DISCOUNT}'        => $signupInformation['Signup']['discount'],
                     '{AMOUNT}'           => $amount,
                     '{LINK}'                 => MAIN_SERVER."/signups/mySignups"),
            $userInfo['User']['email']
        );

        if (!$result) {
             $this->Session->setFlash('Error: while sending email.', 'flash_error');
            //$this->redirect(MAIN_SERVER);
        }
        //EOF sending

        $this->Session->del('SignupStep3');
        $this->Session->del('SignupStep1');
        $this->Session->del('SignupStep2');
        $this->Session->del('SignupDiscount');
        $this->Session->del('last_payment_id');

        $this->set('modelName', $modelName);
        $this->set('slug', $slug);
        $this->set('signupID', $signupInformation['Signup']['id']);
        $this->set('totalprice', $SignupStep3['Package']['totalprice']);

        // Variables for  e-commerce tracking
        if (!empty($signupId) && !empty($modelInfo)) {
            $eTracking = array(
            'OrderID'            => $signupId,
            'Affiliation'           => 'BEERPONG.COM',
            'Total'                    => $payment['Payment']['amount'],
            'Tax'                    => 0,
            'Shipping'            => 0,
            'Address'                => $address['Address']['address'],
            'Zip'                    => $address['Address']['postalcode'],
            'City'                   => $address['Address']['city'],
            'State'                    => $address['Provincestate']['name'],
            'Country'                => $address['Country']['name'],
            'SKU'                      => 'SIGNUP',
            'ProductName'   => $modelInfo[$modelName]['name'].'-'.$packageInfor['Package']['name'],
            'Category'            => 'Signup',
            'Price'                     => $payment['Payment']['amount'],
            'Quantity'                 => 1
            );
            $this->set('eTracking', $eTracking);
        }
    }

    /**
   * payment process
   * @author vovivh
   * @param string $modelname - name of the model for wich will be added new signup
   * @param int $modelID           - ID of the model for which new signup will be added
   */
    /*
    function payment($modelName="Event",$slug=NULL) {
     Configure::write('debug', '0');
     $this->layout = false;

     $discount = 0;
     $this->Signup->$modelName->recursive = -1;
     $model    = $this->Signup->$modelName->find('first',array('conditions'=>array('slug'=>$slug)));
     $id       	 = $model[$modelName]['id'];
     $SignupDiscount = array();

      if ($this->Session->check('SignupStep3')) {
        $step3 = $this->Session->read('SignupStep3');
      }

     if (!$this->RequestHandler->isAjax() || !$this->Access->getAccess('Signup','c')  || !$modelName || !$slug || !$id || !isset($step3)){
           $this->Session->setFlash('This action is not permitted for you.');
           exit('This action is not permitted for you.');
       }

       if ($this->Session->check('loggedUser')) {
         $userSession = $this->Session->read('loggedUser');
     } else {
        exit("You are not logged!");
     }


          if ( !empty($this->request->data) ) {

              if ($this->request->data['Payment']['amount']=='price') {
                  $amount = $step3['Package']['price'];
              } elseif ($this->request->data['Payment']['amount']=='deposit') {
                  $amount = $step3['Package']['deposit'];
              } elseif (!empty($this->request->data['Payment']['amountvalue']) && $this->request->data['Payment']['amountvalue']>=$this->request->data['Payment']['deposit'] && $this->request->data['Payment']['amountvalue']<=$this->request->data['Payment']['amount']) {
                  $amount = $this->request->data['Payment']['amountvalue'];
              } else {
                  exit ("Wrong amount value");
              }

        //Calculate Promocode
        if (isset($step3['Package']['promocode']) && !empty($step3['Package']['promocode']) && $this->Session->check('SignupDiscount')) {
          $SignupDiscount = $this->Session->read('SignupDiscount');
        }

              //Store Signup
              $signup = array();
              $signup['user_id']              = $userSession['id'];
              $signup['model']                = $modelName;
              $signup['model_id']             = $id;
              $signup['signup_date']          = date('Y-m-d H:i:s');
              $signup['packagedetails_id']    = $step3['Package']['packagedetails_id'];
              $signup['total']   			  = $step3['Package']['totalprice'];
              $signup['for_team']   			  = intval($step3['Signup']['for_team']);
              $signup['discount']             = empty($SignupDiscount['discountAmount'])?0:$SignupDiscount['discountAmount'];

              //searching if such sign up already exists
               $tnhis->Signup->recursive = -1;
               $signups = $this->Signup->find('first',array('conditions'=>array('model'=>$modelName,'model_id'=>$id,'user_id'=>$userSession['id'],'status'=>array('partly paid','paid'))));

               if (!empty($signups)) {
                   exit ('You have already signed up.');
               }
               unset($signups);

               $signupID  = $this->Signup->createSignup($modelName,$id,$userSession['id'],$signup);

               if (!$signupID)
                   exit ('Error while storing sign up');
               $this->Session->write('signupID',$signupID);
            //Storing answers
              if (!empty($step3['Question'])) {
                $this->__storeAnswers($modelName,$id,$userSession['id'],$step3);
              }

              $user = array();
              $user['firstName'] = $this->request->data['User']['firstname'];
              $user['lastName']  = $this->request->data['User']['lastname'];
              //Working with BILLING address
              $address_id = $this->_storeBillingAddress($userSession['id'],$this->request->data['Address']['address'],$this->request->data['Address']['address2'],$this->request->data['Address']['city'],$this->request->data['Address']['provincestate_id'],$this->request->data['Address']['postalcode'],$this->request->data['Address']['country_id']);

              $billingaddress                   = $this->Address->find('first',array('conditions'=>array('Address.id'=>$address_id)));
              $user['address']                  = $billingaddress['Address']['address'];
              $user['city']                     = $billingaddress['Address']['city'];
              $user['zip']                      = $billingaddress['Address']['postalcode'];
              $user['country']                  = $billingaddress['Country']['shortname'];
              $user['state']                    = $billingaddress['Provincestate']['name'];
              $user['id']                       = $userSession['id'];
              $user['invoice_num']              = $signupID;
              $user['invoice_description']      = $model[$modelName]['name'];

              $card = array();
              $card['ccnumber'] = $this->request->data['Card']['number'];
              $card['valid']    = $this->request->data['Card']['expiryday']['month'].$this->request->data['Card']['expiryyear']['year'];
              $card['cvn']      = $this->request->data['Card']['cvv'];

              $payment_result   = $this->_pay($user,$card,$amount);

              //store payment
              $payment = array();
              $payment['model']        = "Signup";
              $payment['model_id']     = $signupID;
              $payment['user_id']      = $userSession['id'];
              $payment['payment_date'] = date('Y-m-d H:i:s');
              $payment['amount']       = $payment_result['amount'];
              $payment['status']       = $payment_result['payment_status'];
              $payment['reason']       = $payment_result['reason'];
              $payment['description']  = $payment_result['description'];
              $payment['information']  = serialize($payment_result['information']);
              $payment['address_id']   = $address_id;
              $payment['promocode_id'] = empty($SignupDiscount['discountInformation']['id'])?0:$SignupDiscount['discountInformation']['id'];

              $this->Payment->create();
              if (!$this->Payment->save($payment)) {
                  exit('Can not store payments in the DB');
              }
               $paymentId = $this->Payment->getLastInsertID();
               $this->Payment->savePaymentPromocodes($payment['promocode_id'],$paymentId);
              //EOF store paymentelect address
              //Update signup status
                  $signupStatus['Signup']['id'] = $signupID;
                  if ( floatval($signup['total'])-floatval($signup['discount']) == floatval($payment['amount']) && $payment_result['payment_status']=="Approved") {
                      $signupStatus['Signup']['status'] ="paid";
                      $signupStatus['Signup']['paid']   = $payment['amount'];
                      $this->Promocode->usePromoCode($payment['promocode_id']);//updatecount of use
                  } elseif(floatval($signup['total'])-floatval($signup['discount'])!= floatval($payment['amount']) && $payment_result['payment_status']=="Approved") {
                      $signupStatus['Signup']['status'] ="partly paid";
                      $signupStatus['Signup']['paid']   = $payment['amount'];
                      $this->Promocode->usePromoCode($payment['promocode_id']);//updatecount of use
                  }else{
                      $signupStatus['Signup']['status'] ="not paid";
                  }
              $this->Signup->save($signupStatus);

               //EOF storing signup

              if ($payment_result['payment_status']!="Approved") {
                  exit($payment_result['payment_status'].': '.$payment_result['reason']);
              } else{
                  unset($payment['information']);
                    $this->Session->write('SignupStep4',$user+$payment);
                  exit();
              }
              exit();
          }
        exit("error - empty data set");
    }
    */
    function saveFreeSignup($modelName, $slug) 
    {
        $modelID = $this->Signup->$modelName->field('id', 'slug="' . $slug . '"');
        $package  =  $this->Package->find('first', array('conditions' => array('model' => $modelName, 'model_id'  => $modelID, 'is_deleted' => 0)));
        $packagedetail = $this->Package->Packagedetail->find('first', array('conditions' => array('package_id' => $package['Package']['id'], 'is_deleted' => 0), 'order' => array('price' => 'asc')));
        if ($packagedetail['Packagedetail']['price'] >0) {
            exit('Signup error f.s.1');
        }
        //Store Signup
          $signup = array();
          $signup['user_id']             = $this->getUserID();
          $signup['model']               = $modelName;
          $signup['model_id']            = $modelID;
          $signup['signup_date']         = date('Y-m-d H:i:s');
          $signup['packagedetails_id']   = $packagedetail['Packagedetail']['id'];
          $signup['paid']                = 0;
          $signup['total']                = 0;
          $signup['discount']            = 0;
          $signup['status']              = "paid";
          $signup['for_team']              = 1;
          
           //searching if such sign up already exists
        if ($this->Signup->find('count', array('conditions' => array('model' => $modelName, 'model_id'=> $modelID, 'user_id' => $this->getUserID(), 'status' => array('partly paid','paid'))))) {
              exit ('You already signed up.');
        }

        $signupID  = $this->Signup->createSignup($modelName, $modelID, $this->getUserID(), $signup);
        $this->Session->write('signupID', $signupID);
        return $this->redirect(SECURE_SERVER . '/signups/thankyou');
    }
    
    
    /**
   * Free payment process store signup and create payments
   * @author vovivh
   * @param string $modelname - name of the model for wich will be added new signup
   * @param int    $modelID   - ID of the model for which new signup will be added
   */
    function freepayment($modelName="Event",$slug=null)
    {
        Configure::write('debug', '0');
        $this->layout = false;

        $model    = $this->Signup->$modelName->find('first', array('conditions'=>array('slug'=>$slug)));
        $id       = $model[$modelName]['id'];

        if ($this->Session->check('SignupStep3')) {
            $step3 = $this->Session->read('SignupStep3');
        }
        if (!$this->RequestHandler->isAjax() || !$this->Access->getAccess('Signup', 'c')  || !$modelName || !$slug || !$id || !isset($step3)) {
             $this->Session->setFlash('This action is not permitted for you. Error 22', 'flash_error');
             exit('This action is not permitted for you. Error 22');
        }

        if ($this->Session->check('loggedUser')) {
            $userSession = $this->Session->read('loggedUser');
        } else {
            exit("You are not logged2!");
        }

        //Calculate Promocode
        if (isset($step3['Package']['promocode']) && !empty($step3['Package']['promocode']) && $this->Session->check('SignupDiscount')) {
            $SignupDiscount = $this->Session->read('SignupDiscount');
        } else {
            exit ('Payment can bee only by discount coupon');
        }


        //Store Signup
          $signup = array();
          $signup['user_id']             = $userSession['id'];
          $signup['model']               = $modelName;
          $signup['model_id']            = $id;
          $signup['signup_date']         = date('Y-m-d H:i:s');
          $signup['packagedetails_id']   = $step3['Package']['packagedetails_id'];
          $signup['paid']                = 0;
          $signup['total']                = $step3['Package']['totalprice'];
          $signup['discount']            = empty($SignupDiscount['discountAmount'])?0:$SignupDiscount['discountAmount'];
          $signup['status']              = "paid";         

           //searching if such sign up already exists
           $singups = $this->Signup->find('first', array('conditions'=>array('model'=>$modelName,'model_id'=>$id,'user_id'=>$userSession['id'],'status'=>array('partly paid','paid'))));
        if (!empty($signups)) {
              exit ('You already signed up.');
        }
           unset($signups);

           $signupID  = $this->Signup->createSignup($modelName, $id, $userSession['id'], $signup);

        if (!$signupID) {
            exit ('Error while storing sign up'); 
        }

           $this->Session->write('signupID', $signupID);
         //Storing answers
        if (!empty($step3['Question'])) {
            $this->__storeAnswers($modelName, $id, $userSession['id'], $step3);
        }

        //store payment
              $payment = array();
              $payment['model']        = "Signup";
              $payment['model_id']     = $signupID;
              $payment['user_id']      = $userSession['id'];
              $payment['payment_date'] = date('Y-m-d H:i:s');
              $payment['amount']       = 0;
              $payment['address_id']   = 0;
              $payment['status']       = "Approved";

        if ($SignupDiscount['discountInformation']['type'] == "Free") {
            $payment['reason']       = 'Free discount';
            $payment['information']  = 'Free discount';
        } else {
            $payment['reason']        = 'Discount the same as total $'.sprintf("%.2f", $SignupDiscount['discountAmount']);
            $payment['information']   = 'Discount the same as total $'.sprintf("%.2f", $SignupDiscount['discountAmount']);
        }

              $payment['description']  = $model[$modelName]['name'];
              $payment['promocode_id'] = empty($SignupDiscount['discountInformation']['id'])?0:$SignupDiscount['discountInformation']['id'];

              $this->Payment->create();
        if (!$this->Payment->save($payment)) {
            exit('Can not store payments in the DB');
        }

              $paymentId = $this->Payment->getLastInsertID();
              $this->Payment->savePaymentPromocodes($payment['promocode_id'], $paymentId);

             $this->Promocode->usePromoCode($payment['promocode_id']);
        exit();
    }


    /**
   * pay through authorize.net
   *
   * @param array $user - user information
   *                         ['firstName']
     *                         ['lastName']
     *                         ['address']
     *                         ['city']
     *                         ['state']
     *                         ['zip']
     *                         ['country']
     *                         ['id']
     *                         ['invoice_num']
     *                         ['invoice_description']
   *
   * @param  array $card - credit card information
   *                         ['ccnumber'] - credit card number
   *                         ['valid']         - expiry date MM/YYYY
   *                         ['cvn']           - card verification number
   * @param  float $amount
   * @return array ('result', 'resultText', 'billingSaveResult', 'md5Equal', 'billingId')
   */
    /*
    function _pay($user = array(), $card= array(),$amount = 0) {

    $amount = sprintf("%.2f", ((float)$amount));

    $authTransactionId = 0; //stub

    App::import('Vendor','AuthorizeNet',array('file'=>'authorizenet.class.php'));
    $a = new AuthorizeNet;

    if (SIGNUP_AUTH_NET_TEST_MODE) {
      $a->add_field('x_login', SIGNUP_AUTH_NET_TEST_LOGIN_ID);
      $a->add_field('x_tran_key', SIGNUP_AUTH_NET_TEST_TRAN_KEY);
      $a->gateway_url = "https://test.authorize.net/gateway/transact.dll";
      $a->add_field('x_test_request', 'TRUE');
      $stringToMd5 = SIGNUP_AUTH_NET_TEST_MD5HASH . SIGNUP_AUTH_NET_TEST_LOGIN_ID . $authTransactionId;
    } else {
      $a->add_field('x_login', SIGNUP_AUTH_NET_LOGIN_ID);
      $a->add_field('x_tran_key', SIGNUP_AUTH_NET_TRAN_KEY);
      $a->add_field('x_test_request', 'FALSE');
      $stringToMd5 = SIGNUP_AUTH_NET_MD5HASH . SIGNUP_AUTH_NET_LOGIN_ID . $authTransactionId;
    }


    //$a->add_field('x_password', 'CHANGE THIS TO YOUR PASSWORD');

    $a->add_field('x_version', '3.1');
    $a->add_field('x_type', 'AUTH_CAPTURE');
    $a->add_field('x_relay_response', 'FALSE');
    $a->add_field('x_delim_data', 'TRUE');
    $a->add_field('x_delim_char', '|');
    $a->add_field('x_encap_char', '');
    $a->add_field('x_duplicate_window', '3');

    $a->add_field('x_first_name',   $user['firstName']);
    $a->add_field('x_last_name',   $user['lastName']);
        //address
        if ( !empty( $user['address'] ) )
        $a->add_field('x_address', $user['address']);
        if ( !empty( $user['city'] ) )
        $a->add_field('x_city', $user['city']);
        if ( !empty( $user['state'] ) )
        $a->add_field('x_state',$user['state']);
      if ( !empty( $user['zip'] ) )
        $a->add_field('x_zip',$user['zip']);
      if ( !empty( $user['country'] ) )
        $a->add_field('x_country',$user['country']);



    //additional customer data
    $a->add_field('x_cust_id',         $user['id']);
    $a->add_field('x_customer_ip', $_SERVER['REMOTE_ADDR']);

    //emails
    $a->add_field('x_email_customer', false);
    $a->add_field('x_merchant_email', 'no-reply@bpong.com');

    $a->add_field('x_invoice_num',     $user['invoice_num']);
    $a->add_field('x_description',     $user['invoice_description']);
    $a->add_field('x_method', 'CC');
    $a->add_field('x_card_num',        $card['ccnumber']);
    $a->add_field('x_amount',          $amount);
    $a->add_field('x_exp_date',        $card['valid']);
    $a->add_field('x_card_code',       $card['cvn']);    // Card CAVV Security code

    //Process the payment
    $result = $a->process();

    switch ($result) {
      case 1:
        $paymentStatus = 'Approved';
        break;
      case 2:
        $paymentStatus = 'Declined';
        break;
      case 3:
        $paymentStatus = 'Error';
        break;
    }

    $stringToMd5 .= $amount;
    $myMd5 = md5($stringToMd5);
    $md5Equal = (strtoupper($myMd5) == strtoupper($a->response['MD5 Hash'])) ? true : false;

    $billing = array();
    $billing['payment_status']    = $paymentStatus;
    $billing['reason']            = $a->response['Response Reason Text'];
    $billing['avs_result']        = $a->response['AVS Result Code'];
    $billing['cvn_result']        = $a->response['Card Code (CVV2/CVC2/CID) Response Code'];
    $billing['cardholder_result'] = $a->response['Cardholder Authentication Verification Value (CAVV) Response Code'];
    $billing['md5_equal']         = $md5Equal ? 1 : 0;
    $billing['amount']            = $a->response['Amount'];
    $billing['description']       = $a->response['Description'];
    $billing['information']       = $a->response;

    return $billing;
    }
    */    
    /**
   * Saving questions
   * @author vovich
   */
    function __storeAnswers($modelName=null,$modelID=null,$userID=null,$answers = array()) 
    {
        //Storing answers
              $this->Question->recurcive = -1;
              $questions = $this->Question->find('all', array('conditions'=>array('model'=>$modelName,'model_id'=>$modelID)));
            $this->Answer->deleteAll(array('model'=>$modelName,'model_id'=>$modelID,'user_id'=>$userID));

        foreach ($questions as $question) {
            if (!empty($answers['Question']['Option'.$question['Question']['id']])) {
                foreach ($answers['Question']['Option'.$question['Question']['id']] as $option) {
                     $answer = array();
                     $answer['model']     = $modelName;
                     $answer['model_id']  = $modelID;
                     $answer['user_id']   = $userID;
                     $answer['option_id'] = $option;
                    if (!empty($answers['Question']['input_'.$option])) {
                        $answer['text'] = $answers['Question']['input_'.$option];
                    }
                     $this->Answer->create();
                     $this->Answer->save($answer);
                }
            }
        }
        return true;
    }

    /**
   *  checking if such address already exist and if not then sore
   *  @author vovich
   */
    function _storeBillingAddress($user_id=null,$address="",$address2="",$city="",$provincestate_id = 0 ,$postalcode="",$country_id=0)
    {

            $addressID = $this->Address->field('id', array('model_id'=>$user_id,'model'=>"User",'label'=>"Billing",'address'=>$address,'IFNULL(address2,"")'=>$address2,'city'=>$city,'provincestate_id'=>$provincestate_id ,'postalcode'=>$postalcode,'country_id'=>$country_id));

            $addr['model']            = "User";
            $addr['label']            = "Billing";
            $addr['model_id']         = $user_id;
            $addr['address']          = $address;
            $addr['address2']         = $address2;
            $addr['city']             = $city;
            $addr['provincestate_id'] = $provincestate_id ;
            $addr['postalcode']       = $postalcode;
            $addr['country_id']       = $country_id;

        if (!$addressID) {
            $this->Address->create();
            $this->Address->save($addr);
            $addressID = $this->Address->getLastInsertID();
        }

        return $addressID;
    }

    /**
   * Show already signed up functionality
   * @author vovich
   */
    function alreadysigned($model='Event')
    {
        $this->set('model', $model);
    }

    /**
   * Show all  signups by current user
   * @author vovich
   */
    function mySignups() 
    {

        if ($this->isLoggined()) {
            $userSession = $this->Session->read('loggedUser');
        } else {
            $this->Session->setFlash('You are not logged in.', 'flash_error');
            $this->redirect('/');
        }

        $this->paginate = array(
        'SignupsUser' => array(
            'conditions' => array('SignupsUser.user_id' => $userSession['id'], 'Signup.status' => array('paid', 'partly paid')),
            'order' => array('Signup.id' => 'DESC'),
            'contain' => array('Signup' => array('Event'))          
        ));
    
        $testResult = $this->SignupsUser->find(
            'all', array(
            'conditions' => array('SignupsUser.user_id' => $userSession['id'], 'Signup.status' => array('paid', 'partly paid')),
            'order' => array('Signup.id' => 'DESC'),
            'contain' => array('Signup' => array('Event')))
        );          
    
        //return $this->returnJSONResult($testResult);

        $signups = $this->paginate('SignupsUser');

        $this->set('signups', $signups);

    }
    function testWTF() 
    {
        $this->Signup->recursive = -1;
        $signup = $this->Signup->find('first', array('conditions'=>array('id'=>4484)));
        $Team = ClassRegistry::init('Team');
        $teamInfoForSignup = $Team->testWTF(8439, 'Event', 4484, $signup);
        return $this->returnJSONResult($teamInfoForSignup);
    }
  
    /**
   *show signup details
   * @author vovich, Alex
   * @param int $signupId
   */
    function signupDetails($signupId = null, $activeTab = 'tab-payment') 
    {
        $userSession = $this->Session->read('loggedUser');
        $signupDetails = $this->_getSignupDetails($signupId);
        $isFreeSignup = 0;
        if ($signupDetails['Signup']['status'] == 'paid' && ($signupDetails['Signup']['total'] + $signupDetails['Signup']['discount'] + $signupDetails['Signup']['2pay']) == 0) {
            $isFreeSignup = 1;    
        }

        $userID = $this->getUserID();
        
        $signupUsers = $this->SignupsUser->find('all', array('conditions' => array('signup_id' => $signupId), 'contain' => 'User'));

        $signupUserIDs = Set::combine($signupUsers, '{n}.SignupsUser.user_id', '{n}.SignupsUser.user_id');
        $signupUsers = Set::combine($signupUsers, '{n}.SignupsUser.user_id', '{n}');

        $this->Access->checkAccess('Signup', 'u', $signupDetails['Signup']['user_id']);
    
        //pr($signupDetails);
    
        //Getting packages
        if (!empty($signupDetails['Packagedetails']['package_id'])) {
            $this->Package->recursive = -1;
            $packageInformation = $this->Package->find('first', array('conditions'=>array('Package.id'=>$signupDetails['Packagedetails']['package_id'])));
            $signupDetails['Package'] = $packageInformation['Package'];
        }
    
        //get the number of people in team
        if ($signupDetails['Signup']['model'] == 'Event') {
            $Event = ClassRegistry::init('Event');
            $Event->recursive = -1;
            $event = $Event->find('first', array('conditions'=>array('id'=>$signupDetails['Signup']['model_id'])));
            $peopleinteam = $event['Event']['people_team'];
        }
        else {
            $peopleinteam = 2;
        }
        //        return $this->returnJSONResult($event);
        // PAYMENT BLOCK
    
        //Getting payments
        $this->Payment->recursive = 1;
        $payments = $this->Payment->find('all', array('conditions'=>array('Payment.user_id' => $signupDetails['Signup']['user_id'],'Payment.model'=>'Signup','Payment.model_id'=>$signupDetails['Signup']['id'])));
        if (intval($signupDetails['Signup']['2pay']) > 0) {

            // Authorize.net DPM installation ===========================================================================================
            include_once '../vendors/anet_php_sdk/AuthorizeNet.php';
            $amount = $signupDetails['Signup']['2pay'];
            $time = time();
               $fp_sequence = $time;
               $authorizeNetProperties = array(
            'x_amount'        => $amount,
            'x_fp_sequence'   => $fp_sequence,
            'x_fp_timestamp'  => $time,
            'x_relay_response'=> "TRUE",
            'x_merchant_email'=> ADMIN_EMAIL,
            'x_relay_url'     => SECURE_SERVER . '/signups/payment_callback',
            'x_delim_data'  => "TRUE",
            'x_delim_char'  => ","
               );

               if (SIGNUP_AUTH_NET_TEST_MODE) {
                   $authLogin = SIGNUP_AUTH_NET_TEST_LOGIN_ID;
                   $authKey = SIGNUP_AUTH_NET_TEST_TRAN_KEY;
                   $authorizeNetProperties['x_test_request'] = 'TRUE';
                   $authorizeNetURL = AuthorizeNetDPM::SANDBOX_URL;
               } else {
                       $authLogin = SIGNUP_AUTH_NET_LOGIN_ID;
                       $authKey = SIGNUP_AUTH_NET_TRAN_KEY;
                    $authorizeNetProperties['x_test_request'] = 'FALSE';
                    $authorizeNetURL = AuthorizeNetDPM::LIVE_URL;
               }

                $authorizeNetProperties['x_freight'] = '0';
                $authorizeNetProperties['x_po_numz'] = $signupId;

                //additional customer data
                $authorizeNetProperties['x_cust_id'] = $this->getUserID();
                $authorizeNetProperties['x_customer_ip'] = $_SERVER['REMOTE_ADDR'];

                $authorizeNetProperties['x_merchant_email'] = 'no-reply@bpong.com';
                $authorizeNetProperties['x_invoice_num'] = $signupId;
                $authorizeNetProperties['x_description'] = $signupDetails[$signupDetails['Signup']['model']]['name'];
                $authorizeNetProperties['x_login'] = $authLogin;
                $authorizeNetProperties['x_email'] = $signupDetails['User']['email'];
        
                $fp = AuthorizeNetDPM::getFingerprint($authLogin, $authKey, $amount, $fp_sequence, $time);
                $authorizeNetProperties['x_fp_hash'] = $fp;
        

                foreach ($authorizeNetProperties as $key => $value) {
                    $authorizeNetProperties[$key] = addslashes($value);
                }

                $sim = new AuthorizeNetSIM_Form($authorizeNetProperties);
                $authorizeNetHiddens = $sim->getHiddenFieldString();

                // EOF Authorize Net configuration

                if ($this->Session->check('signup_payment_error')) {
                    $payment_error = $this->Session->read('signup_payment_error');
                    $this->Session->delete('signup_payment_error');
                } else {
                    $payment_error = '';
                }
                if ($this->Session->check('last_payment_id')) {
                        $last_payment_id = $this->Session->read('last_payment_id');
                        $payment = $this->Payment->find('first', array('conditions' => array('Payment.id' => $last_payment_id)));
                        $addressID = $payment['Payment']['address_id'];
                        $phone = $this->Phone->field('phone', array('id' => $payment['Payment']['phone_id']));
            
                } else {
                    $addressID = 0;
                    $phone = '';          
                }
                $this->set('authorizeNetHiddens', $authorizeNetHiddens);
                $this->set('authorizeNetURL', $authorizeNetURL);
                $this->set('payment_error', $payment_error);

                // EOF Authorize.net DPM installation ==========================================================================		
        
        
        
        
        }
    
        // EOF PAYMENT BLOCK
    
        //Checking Team and Teammates
        $isteamAssigned    = false;
        $roomIsCompleted = false;

        $Team = ClassRegistry::init('Team');
        $assigned = array();
        //Changed by Skinny. We need to include the Pending teams, otherwise it's confusing. If the user selects this team,
        //it automatically accepts their participation on the team
        $teams = $Team->getAllUserTeamsIncludingPending($signupDetails['Signup']['user_id'], " Team.* ", $signupDetails[$signupDetails['Signup']['model']]['people_team']);
        if (empty($teams)) {

        } else {
             //Getting assigned teams
             $assigned = $Team->getUserAssignedTeams($signupDetails['Signup']['user_id'], $signupDetails['Signup']['model'], $signupDetails['Signup']['model_id']);
            if (!empty($assigned)) {
                $isteamAssigned = true;
            }
        }
        $teamInfoForSignup = array();
        $teammates = array();

        if (!empty($assigned[0]['Team']['id'])) {
            $teamInfoForSignup = $Team->teamInfoForSignup($assigned[0]['Team']['id'], $signupDetails['Signup']['model'], $signupDetails['Signup']['model_id'], $signupDetails);
           
            $teammates = $Team->Teammate->find('all', array('conditions' => array('team_id' => $assigned[0]['Team']['id'], 'status' => array('Accepted', 'Creator', 'Pending')), 'contain' => array('User')));         
        }

        //Added by Skinny: If this user is Pending on a team that is assigned to the event, we need to give him the opportunity here
        //to accept this.
        $userIsPendingOnTeam = false;
        if ($isteamAssigned) {
            if(empty($teamInfoForSignup['waiting_for_signup']) && empty($teamInfoForSignup['waiting_for_accept'])) {
                $teamIsCompleted = true;
            }
            else {
                $teamIsCompleted = false;
                foreach ($teamInfoForSignup['waiting_for_accept'] as $userWaitingForAccept) {
                    if ($userWaitingForAccept['id'] == $userID) {
                         $userIsPendingOnTeam = $userWaitingForAccept; 
                    }
                }
            }    
        } else {
            $teamIsCompleted = false;       
        } 
   
        $new_created_team_id = 0;
        if ($this->Session->check('new_created_team_id')) {
            $new_created_team_id = $this->Session->read('new_created_team_id');
            $this->Session->delete('new_created_team_id');
        }

        //EOF checking  
   
        // ROOMS BLOCK	
        $roomsCnt = 0;
        if ($signupDetails[$signupDetails['Signup']['model']]['is_room']==0 || !$signupDetails['Package']['people_in_room']) {
            $roomIsCompleted = true;                
        } else{
             $roomsCnt = $this->SignupRoommate->getCountRooms($signupDetails['Signup']['model'], $signupDetails['Signup']['model_id'], $signupDetails['Signup']['user_id']); 
            
             $roomsStatus = 'incompleted';
            if ($signupDetails['Signup']['for_team']) {
                $neededRooms = $signupDetails[$signupDetails['Signup']['model']]['people_team'] / $signupDetails['Package']['people_in_room'];               
            } else {
                $neededRooms = 1;
            }                    

             $rooms = $this->SignupRoom->getSignupRooms($signupUserIDs, $signupDetails['Signup']['model'], $signupDetails['Signup']['model_id']);

             $roomInfo = array();
            
            $roomIsCompleted = false;
            $roomIsPending = false;
              // CREATE ROOM BLOCK
              $showCreateRoomBlock = true;
            if ($neededRooms <= count($rooms)) {
                $roomIsCompleted = true;
                $showCreateRoomBlock = false;    
            }
            $showFindInviters = false;

            foreach ($rooms as $room) {
                if (isset($room['users'][$signupDetails['Signup']['user_id']])) {
                    $showCreateRoomBlock = false;
                    if ($room['status'] == 'Pending') {                            
                        $roomIsCompleted = false;
                        $roomIsPending = true;
                    }                            
                }    
                if ($room['people_in_room'] > count($room['roommates'])) {
                    $showFindInviters = true;
                    $roomIsCompleted = false;    
                }
            }
            
            if ($signupDetails['Signup']['for_team'] && !$showCreateRoomBlock && !$roomIsCompleted) {
                $waitingForTemmatesRoom = true;
            } else {
                $waitingForTemmatesRoom = false;                
            }
            
            if ($showCreateRoomBlock) {
                //working with questions
                $questions = $this->Question->find('all', array('conditions'=>array('model' => 'Room_for_' . strtolower($signupDetails['Signup']['model']), 'model_id' => $signupDetails['Signup']['model_id'])));
                $this->set('questions', $questions);
            }
                      
            $this->set('showFindInviters', $showFindInviters);
            $this->set('waitingForTemmatesRoom', $waitingForTemmatesRoom);
            $this->set('showCreateRoomBlock', $showCreateRoomBlock);       
            // EOF CREATE ROOM BLOCK
                        
            $this->set('rooms', $rooms);
        }
        // EOF ROOMS BLOCK
    
        if (!empty($signupDetails[$signupDetails['Signup']['model']]['signup_required'])) {
            $this->set('cheepestPackage', $this->Package->getCheepesPackage($signupDetails['Signup']['model'], $signupDetails['Signup']['model_id']));
        }
    
        if ($signupDetails['Signup']['status']!='paid') {
            /*pass to the view countries and states*/
            $countries_states = $this->Address->setCountryStates();
            $this->set('countries', $countries_states['countries']);
            $this->set('states', $countries_states['states']);
    
            //Getting  address
            $this->Address->recursive = -1;
            $addresses = $this->Address->find('list', array('fields' => array('id', 'address'),'conditions'=>array('model'=>'User','model_id'=>$userSession['id'],'is_deleted <>'=>1),'order'=>'id DESC'));
            $addresses=array('0'=>"Custom address")+$addresses;

            $this->set('addressesIds', $addresses);       
        }  
        if ($userID == 2) {
            //Configure::write('debug', '1');
            //echo $neededRooms;
            //pr($signupDetails);	
        
        }
        
        $this->set('peopleinteam', $peopleinteam);
        $teams = Set::combine($teams, '{n}.Team.id', '{n}.Team.name');
        $this->set('cardtypes', array('Visa'=>'Visa','MasterCard'=>'MasterCard'));     
        $this->set('team', $assigned);    
        $this->set('userIsPendingOnTeam', $userIsPendingOnTeam);

        //Checking accession for the changing packages
        $this->set('canChangePackage', $this->Access->getAccess('SignupChangePackage', 'r', $signupDetails['Signup']['user_id']));
        $this->set('canUpgradePackage', $this->Access->getAccess('SignupUpgradePackage', 'r', $signupDetails['Signup']['user_id']));
        $this->set('userRole', 'creator');
        $this->set('roomsCnt', $roomsCnt); 
        $this->set('isFreeSignup', $isFreeSignup);   
        $this->set('new_created_team_id', $new_created_team_id);
        $this->set(
            compact(
                'roomsCnt', 'roomIsCompleted', 'roomIsPending', 'activeTab', 'teamIsCompleted', 'isteamAssigned', 'teamInfoForSignup', 
                'signupUsers', 'userID', 'payments', 'signupDetails', 'signupId', 'teams', 'signupDetails', 'phone', 'addressID', 'teammates'
            )
        );   
    }
  
    /**
   * Signup Details page for Temmates
   * @author Oleg D.
   */    
    function signupDetailsTeammate($signupId = null, $activeTab = null) 
    {
        $signupDetails = $this->_getSignupDetails($signupId);

        $userID = $this->getUserID();
        $userSession = $this->Session->read('loggedUser');
    
        $modelName = $signupDetails['Signup']['model'];
        $modelID = $signupDetails['Signup']['model_id'];
        
        $modelInfo = $this->Signup->{$modelName}->find('first', array('conditions' => array('id' => $modelID)));
        $modelInfo = $modelInfo[$modelName];
        $agreement = $modelInfo['agreement'];

        $signupUsers = $this->SignupsUser->find('all', array('conditions' => array('signup_id' => $signupId)));
        $signupUserIDs = Set::combine($signupUsers, '{n}.SignupsUser.user_id', '{n}.SignupsUser.user_id');
        $signupUsers = Set::combine($signupUsers, '{n}.SignupsUser.user_id', '{n}.SignupsUser');            
        $usersAccess = $signupUserIDs;
        unset($usersAccess[$signupDetails['Signup']['user_id']]);
        $this->Access->checkAccess('Signup', 'r', $usersAccess);
    
    
        //Getting packages
        if (!empty($signupDetails['Packagedetails']['package_id'])) {
            $this->Package->recursive = -1;
            $packageInformation = $this->Package->find('first', array('conditions'=>array('Package.id'=>$signupDetails['Packagedetails']['package_id'])));
            $signupDetails['Package'] = $packageInformation['Package'];
        }
        if (isset($signupUsers[$userID])) {
            $signupUser = $signupUsers[$userID];        
        } else {
            $signupUser = array();    
        }
    
        // Address info block
        $this->Signup->User->recursive = 0;
        $this->request->data = $this->Signup->User->read(null, $userID);
        $this->request->data['User']['old_subscribed'] = $this->Signup->User->Mailinglist->isUserInList($this->request->data['User']['id'], LISTID);
        $this->request->data['User']['subscribed']     = 1;

        /*check does we have any packages*/
        if (empty($modelInfo)) {
            exit('Some errors while loading');
        }
        /*sTUPID HACK  for B by Vovich*/
        $maxAge = 21;
        if (!empty($modelInfo[$modelName]['venue_id'])) {
            $country_id = $this->Address->field('country_id', array('model'=>'Venue','model_id'=>$modelInfo['venue_id'],'is_deleted <>'=>1));
            if ($country_id == 2) {/*CANADA*/
                $maxAge = 18;
            }
        }
        $this->set('maxAge', $maxAge);
        /*EOF Stupid hack*/
        $packages  =  $this->Package->find('list', array('conditions'=>array('model' => $modelName, 'model_id'  => $modelID, 'is_deleted'=>0)));

        if(empty($this->request->data['User']['birthdate'])) {
            $this->request->data['User']['birthdate'] = '';
        }

        $homeCount = $this->Address->find('count', array('conditions' => array('Address.model' => 'User', 'Address.model_id' => $this->getUserID(), 'Address.is_deleted' => '0', 'Address.label' => 'Home')));
        if (!$homeCount) {
            /*pass to the view countries and states*/
            $countries_states = $this->Address->setCountryStates();
    
            $this->set('countries', $countries_states['countries']);
            $this->set('states', $countries_states['states']);        
        }
    
        $this->set('genders', array(''=>'Select one','M'=>'Male','F'=>'Female'));
        $this->set('homeCount', $homeCount);
        $this->set('packages', $packages);        
        // EOF Address info block

        // Rooms block
        $this->Package->recursive = -1;
        $packageInformation = $this->Package->find('first', array('conditions'=>array('Package.id' => $signupDetails['Packagedetails']['package_id'])));
      
        if ($signupDetails[$signupDetails['Signup']['model']]['people_team'] > $packageInformation['Package']['people_in_room'] && $signupDetails[$signupDetails['Signup']['model']]['is_room']>0 && $signupDetails['Package']['people_in_room'] > 0) {            
            $showRoomsBlock = true;
            $rooms = $this->SignupRoom->getSignupRooms($userID, $modelName, $modelID);

            if (empty($rooms)) {
                $isRoomsCompleted = false;    
            } else {
                $isRoomsCompleted = true;
            }            
            $this->set('rooms', $rooms);
        } else {
            $showRoomsBlock = false;
            $isRoomsCompleted = true;        
        }
    
        if ($signupDetails['Package']['people_in_room'] < 2 && empty($rooms)) {
            $showCreateRoomBlock = true;    
        } else {
            $showCreateRoomBlock = false;           
        }

        if ($showCreateRoomBlock) {
            //working with questions
            $questions = $this->Question->find('all', array('conditions'=>array(      'model' => 'Room_for_' . strtolower($signupDetails['Signup']['model']), 'model_id' => $signupDetails['Signup']['model_id'])));
            $this->set('questions', $questions);
        }
    
        if (!$showCreateRoomBlock &&  !$this->SignupRoommate->isMyRoomFilled($userID, $signupDetails['Package']['people_in_room'], $signupDetails['Signup']['model'], $signupDetails['Signup']['model_id']) ) {
            $showFindInviters = true;    
        } else {
            $showFindInviters = false;               
        }            
        $this->set('isteamAssigned', true);
        $this->set('showFindInviters', $showFindInviters);
        $this->set('showCreateRoomBlock', $showCreateRoomBlock); 
    
        // EOF Rooms block
    
    
        $Teammate = ClassRegistry::init('Teammate');
        $isAddressCompleted = $Teammate->isAddressCompleted($userID);

        if (!$activeTab) {
            if (!$signupUser['agreement_accepted'] || $isAddressCompleted) {
                $activeTab = 'tab-agreement';    
            } else {
                $activeTab = 'tab-address';
            }    
        }
        if ($signupUser['agreement_accepted'] && $isAddressCompleted && $isRoomsCompleted) {
            $isRegistrationCompleted = true;     
        } else {
            $isRegistrationCompleted = false;       
        }
        $this->set('userRole', 'teammate');
        $this->set(compact('activeTab', 'signupUser', 'signupDetails', 'signupId', 'agreement', 'modelName', 'modelID', 'modelInfo', 'isAddressCompleted', 'showRoomsBlock', 'isRoomsCompleted', 'isRegistrationCompleted'));     
  
    }

  
    /**
   * Accept agreement for signup for teammates
   *
   * @author Oleg D.
   * 
   * Edited by Skinny: When a user accepts the agreement, its safe to assume the accept any team or room assignments that 
   * they are attached to for this event.
   */    
    function accept_agreement($signupID = null) 
    {
        $userID = $this->getUserID();
        $signupsUser = $this->SignupsUser->find(
            'first', array('conditions' => array('SignupsUser.signup_id' => $signupID, 'SignupsUser.user_id' => $userID),
            'contain'=>array('Signup'))
        );    
        $this->SignupsUser->save(array('id' => $signupsUser['SignupsUser']['id'], 'agreement_accepted' => 1));
    
        //added by skinny - when the user accepts the agreement we should accept the team assignment as well
        //First, look for all teammates that the user is on
        $Teammate = ClassRegistry::init('Teammate');
        $Teammate->recursive = -1;
        $teammates = $Teammate->find(
            'all', array('conditions'=>array(
            'user_id'=>$userID,
            'status'=>array('Creator','Accepted','Pending')))
        );
        $teamIDs = Set::extract($teammates, '{n}.Teammate.team_id');
        //Now look for TeamObjects where one of those teams is linked to the signup
        $TeamsObject = ClassRegistry::init('TeamsObject');
        $TeamsObject->recursive = -1;
        $teamsObject = $TeamsObject->find(
            'first', array('conditions'=>array(
            'TeamsObject.status <>'=>'Deleted',
            'TeamsObject.model'=>$signupsUser['Signup']['model'],
            'TeamsObject.model_id'=>$signupsUser['Signup']['model_id'],
            'TeamsObject.team_id'=>$teamIDs))
        );         
        if ($teamsObject) {
            //get the team
            $Team = ClassRegistry::init('Team');
            $team = $Team->find(
                'first', array('conditions'=>array(
                'Team.id'=>$teamsObject['TeamsObject']['team_id'],
                'Team.status <>'=>'Deleted'),
                'contain'=>array('User'))
            );
            $teamIsCompleted = true;
            if ($team) {
                foreach ($team['User'] as &$teammate) {
                    if ($teammate['id'] == $userID) {
                        $teammate['Teammate']['status'] = 'Accepted';
                        $Teammate->save($teammate['Teammate']);                  
                    }
                    else {
                        if ($teammate['status'] == 'Pending') {
                            $teamIsCompleted = false;
                        }
                    }
                }
                if ($teamIsCompleted) {
                    $team['Team']['status'] = 'Completed';
                    $Team->save($team['Team']);
                }
            }
        }                              
    
    
        //Now take care of the room
        $this->SignupRoommate->acceptRoomInvitation(
            $userID, $signupsUser['Signup']['model'],
            $signupsUser['Signup']['model_id']
        );
    
        $this->redirect('/signups/signupDetailsTeammate/' . $signupID);
        exit;      
    }

  
    /**
   * Accept agreement for signup for teammates
   * @author Oleg D.
   */    
    function update_address($signupID = null) 
    {
        /* Storing data */
        if (!empty($this->request->data)) {
            if (!empty($this->request->data['Address'])) {
                $this->request->data['Address']['model']       = 'User';
                $this->request->data['Address']['model_id']    = $this->getUserID();
                $this->Address->create();
                $this->Address->save($this->request->data['Address']);
                unset($this->request->data['Address']);
            }
            $this->request->data['User']['id'] = $this->getUserID();
               //remove validations
             unset($this->Signup->User->validate);
            if (empty($this->request->data['User']['subscribed'])) {
                unset($this->request->data['User']['subscribed']);
                unset($this->request->data['User']['old_subscribed']);
            }
             $this->Signup->User->save($this->request->data);

        }    
    
        return $this->redirect('/signups/signupDetailsTeammate/' . $signupID);
        exit;      
    }

    /**
   * function for calculating price
   * @author vovich
   */
    function calculatePricebyDiscount()
    {
        Configure::write('debug', '0');
        $this->layout = false;
        $response['discountInformation'] = "Can not find such coupon.";
        $response['priceInformation']    = "";
        $response['isrefund']              = 0 ;
        $discount                        = 0;

        if ($this->Session->check('loggedUser')) {
            $userSession = $this->Session->read('loggedUser');
        } else {
            $this->Json->encode("You are not logged in.");
        }

        if ($this->RequestHandler->isAjax() && !empty($_REQUEST['coupon']) && !empty($_REQUEST['model']) && !empty($_REQUEST['model_id'])) {
            $result   = $this->Promocode->checkCoupon($_REQUEST['coupon'], $_REQUEST['model'], $_REQUEST['model_id'], $userSession['id']);
            $response['discountInformation'] = $this->Promocode->formatDiscount($result);
            //getting new price
            if (!empty($_REQUEST['signup_id'])) {
                $this->Signup->recursive = -1;
                $signupDetails = $this->Signup->find('first', array('conditions'=>array('Signup.id'=>$_REQUEST['signup_id'])));

                if (!empty($signupDetails) && is_array($result)) {
                    $oldPrice = floatval($signupDetails['Signup']['total']) - floatval($signupDetails['Signup']['discount']) - floatval($signupDetails['Signup']['paid']);
                    //Getting discount from 2pay
                    if ($result['type']=='Free') {
                        $cheepestpackage = $this->Package->getCheepesPackage($signupDetails['Signup']['model'], $signupDetails['Signup']['model_id'], $signupDetails['Signup']['signup_date']);
                        if (!empty($cheepestpackage)) {
                            $discount = floatval($cheepestpackage['packagedetails']['price']);
                            $response['discountInformation'] = 'You have a discount $'.$discount;
                        }
                    } else {
                        $discount = $this->Promocode->calculateDiscountAmount($result, $signupDetails['Signup']['total']);
                    }

                    $topay    = $oldPrice- floatval($discount);
                    if ($topay<0) {
                        $topay = min(abs($topay), floatval($signupDetails['Signup']['paid']));
                        $response['priceInformation'] = 'We will refund $'.abs($topay);
                        $response['isrefund']           = 1;
                    } elseif($oldPrice != $topay) {
                        $response['priceInformation'] = 'Old price: $<strike>'.$oldPrice.'</strike>; New price $'.abs($topay);
                    } else {
                        $response['priceInformation'] = '$'.sprintf("%.2f", $topay);
                    }
                    if ($topay == 0) {
                        $response['isrefund'] = 1;
                    }
                }
            }
            //EOF getting new price
            exit($this->Json->encode($response));
        } else {
            //empty coupon
            $this->Signup->recursive = -1;
            $signupDetails = $this->Signup->find('first', array('conditions'=>array('Signup.id'=>$_REQUEST['signup_id'])));
            $amount = floatval($signupDetails['Signup']['total']) - floatval($signupDetails['Signup']['discount']) - floatval($signupDetails['Signup']['paid']);
            $response['discountInformation'] = "";
            $response['priceInformation'] = '$'.sprintf("%.2f", $amount);

            exit($this->Json->encode($response));
        }

    }

    /**
   *  her znaet zachem
   * @author Alex
   */
    function _getSignupDetails( $singupId = null, $isBoolean = false, $recursive = 1 ) 
    {
        if (!$singupId ) {
            if (!$isBoolean) {
                $this->Session->setFlash('Incorrect ID', 'flash_error');
                $this->redirect('/');
                exit();
            } else {
                return false;
            }
        }

        $this->Signup->recursive = $recursive;
        $signupDetails = $this->Signup->find('first', array('conditions' => array( 'Signup.id' => $singupId )));
        $signupDetails['Signup']['2pay'] =sprintf("%.2f", floatval($signupDetails['Signup']['total']) - floatval($signupDetails['Signup']['discount']) - floatval($signupDetails['Signup']['paid']));
    

        if (empty($signupDetails)) {
            if (!$isBoolean ) {
                $this->Session->setFlash('Can not find such signup.', 'flash_error');
                $this->redirect('/');
                exit();
            } else {
                return false;
            }
        }

        return $signupDetails;
    }

    /**
   * payment process for complete signup
   * @author vovivh
   * @param int $signupID
   */
    /*  
    * 
  															SHOULD BE DELETED !!!!!!!!!!
    function makeCompletePayment($signupID = NULL) {
     Configure::write('debug', '0');
     $this->layout = false;

     $address_id = NULL;
     $discount = 0;
     $SignupDiscount = array();

     if (!$this->RequestHandler->isAjax() || !$this->Access->getAccess('Signup','c')  || !$signupID){
           $this->Session->setFlash('This action is not permitted for you.');
           exit('This action is not permitted for you.');
       }

       if ($this->Session->check('loggedUser')) {
      $userSession = $this->Session->read('loggedUser');
     } else {
        exit("You are not logged!");
     }

     $this->Signup->recursive = 0;
     $signupDetails = $this->Signup->find('first',array('conditions'=>array('Signup.id'=>$signupID)));
     if (empty($signupDetails)) {
       exit('can not find signup.');
     }

     // getting amount
     $amount = floatval($signupDetails['Signup']['total']) - floatval($signupDetails['Signup']['discount']) - floatval($signupDetails['Signup']['paid']);

          if ( !empty($this->request->data) ) {
               //Calculate Promocode
                if (!empty($this->request->data['Payment']['promocode'])) {
                    $couponInformation = $this->Promocode->checkCoupon($this->request->data['Payment']['promocode'],$signupDetails['Signup']['model'],$signupDetails['Signup']['model_id'],$userSession['id']);
                    if (!is_array($couponInformation)) {
                        exit($couponInformation);
                    }

                    if ($couponInformation['type']=='Free') {
                        $cheepestpackage = $this->Package->getCheepesPackage($signupDetails['Signup']['model'],$signupDetails['Signup']['model_id'], $signupDetails['Signup']['signup_date']);
                        if (!empty($cheepestpackage)) {
                            $discount = floatval($cheepestpackage['packagedetails']['price']);
                            $response['discountInformation'] = 'You have a discount $'.$discount;
                        }
                    } else {
                        $discount = $this->Promocode->calculateDiscountAmount($couponInformation,$signupDetails['Signup']['total']);
                    }
                    $amount = floatval($amount) - floatval($discount);
                }
               //EOF calculation
              $user = array();
              $user['firstName'] = $signupDetails['User']['firstname'];
              $user['lastName']  = $signupDetails['User']['lastname'];
              if (!empty($this->request->data['Address'])) {
                  //Working with BILLING address
                  $address_id = $this->_storeBillingAddress($userSession['id'],$this->request->data['Address']['address'],$this->request->data['Address']['address2'],$this->request->data['Address']['city'],$this->request->data['Address']['provincestate_id'],$this->request->data['Address']['postalcode'],$this->request->data['Address']['country_id']);
                  $billingaddress              = $this->Address->find('first',array('conditions'=>array('Address.id'=>$address_id)));
                  $user['address']             = $billingaddress['Address']['address'];
                  $user['city']                = $billingaddress['Address']['city'];
                  $user['zip']                 = $billingaddress['Address']['postalcode'];
                  $user['country']             = $billingaddress['Country']['shortname'];
                  $user['state']               = $billingaddress['Provincestate']['name'];
                  $user['id']                  = $userSession['id'];
                  $user['invoice_num']         = $signupID;
                  $user['invoice_description'] = $signupDetails[$signupDetails['Signup']['model']]['name'];
              }
              if ($amount>0) {
                          if (empty($this->request->data['Card'])){
                              exit ("Can not find card parameters");
                          }
                          $card = array();
                          $card['ccnumber'] = $this->request->data['Card']['number'];
                          $card['valid']    = $this->request->data['Card']['expiryday']['month'].$this->request->data['Card']['expiryyear']['year'];
                          $card['cvn']      = $this->request->data['Card']['cvv'];
                          $payment_result   = $this->_pay($user,$card,$amount);

              } else {
                  $payment_result = array();
                  $payment_result['amount']         = 0;
                  $payment_result['payment_status'] = "Approved";
                  $payment_result['reason']         = "Free payment";
                  $payment_result['description']    = "Free payment";
                  $payment_result['information']    = "Free payment";
              }
              /////////////store payment/////////////////////////////////
              $payment = array();
              $payment['model']        = "Signup";
              $payment['model_id']     = $signupID;
              $payment['user_id']      = $userSession['id'];
              $payment['payment_date'] = date('Y-m-d H:i:s');
              $payment['amount']       = $payment_result['amount'];
              $payment['status']       = $payment_result['payment_status'];
              $payment['reason']       = $payment_result['reason'];
              $payment['description']  = $payment_result['description'];
              $payment['information']  = serialize($payment_result['information']);
              $payment['address_id']   = $address_id;
              $payment['promocode_id'] = isset($couponInformation['id'])?$couponInformation['id']:0;

              $this->Payment->create();
              if (!$this->Payment->save($payment)) {
                  exit('Can not store payments in the DB');
              }
              $paymentId = $this->Payment->getLastInsertID();
              $this->Payment->savePaymentPromocodes($payment['promocode_id'],$paymentId);
        ////////////// EOF storing payment/////////////////////////////
              //EOF store paymentelect address
              //Update signup status
                  $signupStatus['Signup']['id'] = $signupID;
                  if ($payment_result['payment_status']=="Approved") {
                      $signupStatus['Signup']['status']   = "paid";
                      $signupStatus['Signup']['paid']     = floatval($signupDetails['Signup']['paid']) + floatval( $payment_result['amount']);
                      if ($discount>0)
                          $signupStatus['Signup']['discount'] = floatval($signupDetails['Signup']['discount']) + floatval($discount);
                       else
                          $signupStatus['Signup']['discount'] = floatval($signupDetails['Signup']['discount']);

                      if (!empty($payment['promocode_id']))
                          $this->Promocode->usePromoCode($payment['promocode_id']);//updatecount of use
                  }

                  $this->Signup->save($signupStatus);
               //EOF storing signup

              if ($payment_result['payment_status']!="Approved") {
                  exit($payment_result['payment_status'].': '.$payment_result['reason']);
              } else{
                  //all right sending email to the User
                  $rest = floatval($signupDetails['Signup']['total'])-floatval($signupStatus['Signup']['discount'])-floatval($signupStatus['Signup']['paid']);
                  if ($rest<0){
                      $rest = min(abs($rest),floatval($signupStatus['Signup']['paid']));
                      if ($rest>0)
                          $rest = -1*$rest;
                  }
                  $rest = $rest>=0?'$'.sprintf("%.2f",$rest):' we will refund -$'.sprintf("%.2f",abs($rest));

                  $result = $this->sendMailMessage('CompletePayment', array(
                 	 '{USERNAME}'  => $signupDetails['User']['lgn'],
                     '{FNAME}'     => $signupDetails['User']['firstname'],
                     '{LNAME}'     => $signupDetails['User']['lastname'],
                     '{MODEL}'     => strtolower($signupDetails['Signup']['model']),
                     '{MNAME}'     => $signupDetails[$signupDetails['Signup']['model']]['name'],
                     '{TOTAL}'     => '$'.sprintf("%.2f",$signupDetails['Signup']['total']),
                     '{REMAINING}' => $rest,
                     '{PAID}'      => '$'.sprintf("%.2f",$payment_result['amount']),
                     '{LINK}'      => MAIN_SERVER."/events/my"),
                    $signupDetails['User']['email']
                  );
           	      $this->Session->setFlash('Thank You For Your Payment.');
                  if (!$result) {
                         exit('Error: while sending email.');
                    //$this->redirect(MAIN_SERVER);
                  }
            //EOF sending


                  unset($payment['information']);
                  exit();
              }
              exit();
          }
        exit("error - empty data set");
    }
    */ 
    /**
   * Use promocode for signup
   * @author vovich
   * @param int $signupId
   */
    function usePromocode($signupId=null) 
    {

        $this->Signup->recursive = 1;
        $signupDetails = $this->Signup->find('first', array('conditions'=>array('Signup.id'=>$signupId)));
        $discount      = 0;

        if (empty($signupDetails) || !$this->Session->check('loggedUser')) {
            $this->Session->setFlash('Can not find such signup.', 'flash_error');
            $this->redirect(MAIN_SERVER);
        }

        if ($this->Session->check('loggedUser')) {
            $userSession = $this->Session->read('loggedUser');
        }


        $this->Access->checkAccess('Signup', 'u', $signupDetails['Signup']['user_id']);
        //Have to pay
        $signupDetails['Signup']['2pay'] =sprintf("%.2f", floatval($signupDetails['Signup']['total']) - floatval($signupDetails['Signup']['discount']) - floatval($signupDetails['Signup']['paid']));

        if ($signupDetails['Signup']['status']!='paid' || (!empty($signupDetails[$signupDetails['Signup']['model']]['finish_signup_date']) && $this->Time->fromString($signupDetails[$signupDetails['Signup']['model']]['finish_signup_date'])<strtotime(date("Y-m-d")) )) {
            $this->Session->setFlash('This signup can not be changed.', 'flash_error');
            $this->redirect(MAIN_SERVER);
        }
        //Getting packages
        if (!empty($signupDetails['Packagedetails']['package_id'])) {
            $this->Package->recursive= -1;
            $packageInformation = $this->Package->find('first', array('conditions'=>array('Package.id'=>$signupDetails['Packagedetails']['package_id'])));
            $signupDetails['Package'] = $packageInformation['Package'];
        }
        //Use promocode
        if (!empty($this->request->data)) {
            if (empty($this->request->data['Payment']['promocode'])) {
                $this->Session->setFlash('Promocode can not be empty', 'flash_error');
                $this->redirect(MAIN_SERVER.'/signups/mySignups');
            }

            //Calculate Promocode
            if (!empty($this->request->data['Payment']['promocode'])) {
                $couponInformation = $this->Promocode->checkCoupon($this->request->data['Payment']['promocode'], $signupDetails['Signup']['model'], $signupDetails['Signup']['model_id'], $userSession['id']);
                if (!is_array($couponInformation)) {
                    $this->Session->setFlash($couponInformation, 'flash_success');
                    $this->redirect('/');
                }

                if ($couponInformation['type']=='Free') {
                    $cheepestpackage = $this->Package->getCheepesPackage($signupDetails['Signup']['model'], $signupDetails['Signup']['model_id'], $signupDetails['Signup']['signup_date']);
                    if (!empty($cheepestpackage)) {
                        $discount = floatval($cheepestpackage['packagedetails']['price']);
                    }
                } else {
                    $discount = $this->Promocode->calculateDiscountAmount($couponInformation, $signupDetails['Signup']['total']);
                }
                // Update signup
                $signup['Signup']['discount'] = floatval($signupDetails['Signup']['discount']) + floatval($discount);
                $signup['Signup']['id'] = $signupDetails['Signup']['id'];
                $this->Signup->save($signup);

                //store payment
                $payment = array();
                $payment['model']        = 'Signup';
                $payment['model_id']     = $signupDetails['Signup']['id'];
                $payment['user_id']      = $signupDetails['Signup']['user_id'];
                $payment['payment_date'] = date('Y-m-d H:i:s');
                $payment['amount']       = 0;
                $payment['status']       = "Approved";
                $payment['reason']       = "Used promocode";
                $payment['description']  = "Used promocode";
                $payment['information']  = "Used promocode";
                $payment['promocode_id'] = $couponInformation['id'];

                $this->Payment->create();
                if (!$this->Payment->save($payment)) {
                    $this->Session->setFlash('Error while storing payment', 'flash_error');
                    $this->redirect(MAIN_SERVER);
                }
                $paymentId = $this->Payment->getLastInsertID();
                $this->Payment->savePaymentPromocodes($payment['promocode_id'], $paymentId);


                $this->Session->setFlash('Promocode has been used.', 'flash_success');
                $this->redirect(MAIN_SERVER.'/signups/mySignups');
            }

        }

        $this->set('signupDetails', $signupDetails);
    }
    /**
* Show all signups of all users
* @author vovich
*/
    function showAllSignups() 
    {

        $this->Access->checkAccess('showAllSignups', 'r');
        $conditions = array();

        /* filter Getting data from the session or from the form*/
        if(!empty($this->request->data['SignupFilter'])) {
            $this->Session->write('SignupFilter', $this->request->data['SignupFilter']);
        }elseif($this->Session->check('SignupFilter')) {
            $this->request->data['SignupFilter']=$this->Session->read('SignupFilter');
        }
        // we're searching by SignupsUser...in most cases, we only want to look at those records where SignupsUser.user_id = Signup.user_id
        // if we're searching by name this isn't true
        $onlySearchRequester = true;

        //Prepare data for the filter
        if (!empty( $this->request->data['SignupFilter']['model'])) {
            $conditions['Signup.model'] = $this->request->data['SignupFilter']['model']; 
        }
        if (!empty( $this->request->data['SignupFilter']['user_id'])) {
            $conditions['User.id'] = $this->request->data['SignupFilter']['user_id']; 
        }
        if (!empty( $this->request->data['SignupFilter']['user_email'])) {
            $conditions['User.email'] = $this->request->data['SignupFilter']['user_email'];
            $onlySearchRequester = false;
        }
        if (!empty( $this->request->data['SignupFilter']['user_lastname'])) {
            $conditions['User.lastname LIKE'] = $this->request->data['SignupFilter']['user_lastname'];
            $onlySearchRequester = false;
        }
        if (!empty( $this->request->data['SignupFilter']['user_login'])) {
            $conditions['User.lgn LIKE'] = $this->request->data['SignupFilter']['user_login'];
            $onlySearchRequester = false;
        }
        if (!empty( $this->request->data['SignupFilter']['eventid'])) {
            $conditions['Signup.model_id'] = $this->request->data['SignupFilter']['eventid']; 
        }
        
        if (!empty($this->request->data['SignupFilter']['for_team']) && $this->request->data['SignupFilter']['for_team']!='all') {
            $conditions['Signup.for_team'] = $this->request->data['SignupFilter']['for_team'];
        
        }
        

        if (!empty( $this->request->data['SignupFilter']['status'])) {
            if ($this->request->data['SignupFilter']['status']=='refund') {
                $conditions["(Signup.total-Signup.discount-Signup.paid) < "]=0;
                $conditions["Signup.paid > "] = 0;
            } else {
                $conditions['Signup.status'] = $this->request->data['SignupFilter']['status'];
            }
        }
        if (!empty( $this->request->data['SignupFilter']['searchby']) && $this->request->data['SignupFilter']['searchby'] == "OR") {
            $conditions = array('OR'=>$conditions);
        }
    
        $paginationArray['conditions'] = $conditions;
        $paginationArray['contain'] = array('User','Signup'=>array('Event','User'));
        $paginationArray['order'] = array('Signup.id'=>'DESC');
    
        $this->paginate = array('SignupsUser'=>$paginationArray);

        $signups = $this->paginate('SignupsUser');
    
    
        //$this->Signup->recursive = 0;
        //   $signups = $this->paginate('SignupsUser',$paginationArray);
        $this->set('signups', $signups);

        $this->set('statuses', array(''=>' All ','new'=>'new','partly paid'=>'partly paid','paid'=>'paid','not paid'=>'not paid','cancelled'=>'cancelled','refund'=>'refund'));
        $this->set('models',  array(''=>' All ','Event'=>'Event'));

    }

    /**
   * Function for cancelling signup
   * @author vovich
   */
    function cancell($signupId=null,$setAction = null) 
    {
        $this->Signup->recursive = 1;
        $signupDetails = $this->Signup->find('first', array('contain'=>array('Event'),'conditions'=>array('Signup.id'=>$signupId)));

        $this->Access->checkAccess('Signup', 'd', $signupDetails['Signup']['user_id']);
        if (empty($signupDetails)) {
            $this->Session->setFlash('Can not find such signup', 'flash_error');
            $this->redirect(MAIN_SERVER);
        }
        $signupDetails['Signup']['status'] = 'cancelled';
        if ($this->Signup->save($signupDetails)) {

            $TeamObject = ClassRegistry::init('TeamsObject');
            $teams = $TeamObject->removeAssigment($signupDetails['Signup']['user_id'], $signupDetails['Signup']['model'], $signupDetails['Signup']['model_id']);

            //Sending email to the teammates that team Assigment has been removed
            $Teammate = ClassRegistry::init('Teammate');
            foreach ($teams as $team) {
                $Team->recursive = 1;
                $teammmateInformation = $Teammate->find('all', array('contain'=>array('User','Team'),'conditions'=>array('Teammate.team_id'=>$team['Team']['id'],'Teammate.status'=>array('Creator','Accepted') )));
                if (!empty($teammmateInformation['User']['email'])) :
                    $result = $this->sendMailMessage(
                        'DeleteTeamObject', array(
                        '{MODELNAME}'  => $signupDetails[$signupDetails['Signup']['model']]['name'],
                        '{MODEL}'  => $signupDetails['Signup']['model'],
                        '{TEAMNAME}'  => @$teammmateInformation['Team']['name'],
                        '{TEAMDESCRIPTION}'  => @$teammmateInformation['Team']['description'],
                        '{FNAME}'         => @$teammmateInformation['User']['firstname'],
                        '{LNAME}'         => @$teammmateInformation['User']['lastname']
                        ),
                        $teammmateInformation['User']['email']
                    );
                endif;
            }

            //Delete Room
            $rooms = $this->SignupRoommate->find('all', array('fields' => array('SignupRoommate.room_id'),'conditions'=>array('SignupRoommate.user_id'=>$signupDetails['Signup']['user_id'],'Room.model'=>$signupDetails['Signup']['model'],'Room.model_id'=>$signupDetails['Signup']['model_id'],'Room.status'=>array('Created','Pending','Approved','Confirmed'))));
            if (!empty($rooms)) {
                foreach ($rooms as $room) {
                     $this->requestAction("/rooms/delete_room/".$room['SignupRoommate']['room_id'], array('requested' => true, 'return' => true ));
                }
            }

            /*history*/
            $user  = $this->Session->read('loggedUser');
            $History            = ClassRegistry::init('History');
            $historyParams                     = array();
            $historyParams['user_id']          = $user['id'];
            $historyParams['model']            = $signupDetails['Signup']['model'];
            $historyParams['model_id']         = $signupDetails['Signup']['model_id'];
            $historyParams['affected_user_id'] = serialize($signupDetails['Signup']['user_id']);

            $History->signupCancell($signupDetails['Signup']['id'], $historyParams);
            unset($History);
            /*EOF HISTORY*/
            if ($setAction) {
                return;
            } else {
                $this->Session->setFlash('The signup has been cancelled', 'flash_success');
                $this->redirect($_SERVER['HTTP_REFERER']);
            }

        } else {
            if ($setAction) {
                return "Error";
            } else {
                $this->Session->setFlash('Some errors while deleting', 'flash_error');
                $this->redirect(@$_SERVER['HTTP_REFERER']);
            }
        }


    }

    /**
 * make refund and send email
 * @author vovich
 * @param array signupDetails
 * @param float amount
 * @param string description
 */
    function __makeRefund($signupDetails = null, $amount = null,$reason = "Refund signup",$description = "Refund signup") 
    {

          //store payment
              $payment = array();
              $payment['model']               = "Signup";
              $payment['model_id']          = $signupDetails['Signup']['id'];
              $payment['user_id']             = $signupDetails['Signup']['user_id'];
              $payment['payment_date'] = date('Y-m-d H:i:s');
              $payment['amount']            = $amount;
              $payment['status']               = "Approved";
              $payment['reason']              = $reason;
              $payment['description']        = $description;
              $payment['information']       = serialize($signupDetails);

              $this->Payment->create();
        if (!$this->Payment->save($payment)) {
            $this->Session->setFlash('Error while store payment', 'flash_error');
            $this->redirect(MAIN_SERVER);
        }

              $paymentId = $this->Payment->getLastInsertID();
              $this->Payment->savePaymentPromocodes($payment['promocode_id'], $paymentId);

          $this->Signup->id = $signupDetails['Signup']['id'];
          $this->Signup->saveField('paid', floatval($signupDetails['Signup']['paid']) + floatval($amount));


            $result = $this->sendMailMessage(
                'RefundPayment', array(
                     '{USERNAME}'        => $signupDetails['User']['lgn'],
                     '{FNAME}'           => $signupDetails['User']['firstname'],
                     '{LNAME}'           => $signupDetails['User']['lastname'],
                     '{MODEL}'           => strtolower($signupDetails['Signup']['model']),
                     '{MNAME}'           => $signupDetails[$signupDetails['Signup']['model']]['name'],
                     '{REFUND}'          => '$'.sprintf("%.2f", abs($amount)),
                     '{LINK}'            => MAIN_SERVER."/events/my"),
                $signupDetails['User']['email']
            );

            return ;

    }

    /**
   * Refund Signup
   * @author vovich
   */
    function refund($signupId=null) 
    {
        $this->Access->checkAccess('refund', 'r');

        if ($signupId) {
            $this->Signup->recursive = 0;
            $signupDetails = $this->Signup->find('first', array('conditions'=>array('Signup.id'=>$signupId)));

            if (empty($signupDetails)) {
                $this->Session->setFlash('Can not find such signup', 'flash_error');
                $this->redirect($_SERVER['HTTP_REFERER']);
            }


            $rest   = floatval($signupDetails['Signup']['total']) - floatval($signupDetails['Signup']['discount']) - floatval($signupDetails['Signup']['paid']);
            $amount = min(abs($rest), floatval($signupDetails['Signup']['paid']));
            $amount = -1*$amount;
            if ($rest>=0) {
                $this->Session->setFlash('Amount >0, Refund is not needed.', 'flash_error');
                $this->redirect($_SERVER['HTTP_REFERER']);
            }


            $this->__makeRefund($signupDetails, $amount);

            //store payment
              $payment = array();
              $payment['model']        = "Signup";
              $payment['model_id']     = $signupDetails['Signup']['id'];
              $payment['user_id']      = $signupDetails['Signup']['user_id'];
              $payment['payment_date'] = date('Y-m-d H:i:s');
              $payment['amount']       = $amount;
              $payment['status']       = "Approved";
              $payment['reason']       = "Refund signup";
              $payment['description']  = "Refund signup";
              $payment['information']  = "Refund";

              $this->Payment->create();
            if (!$this->Payment->save($payment)) {
                $this->Session->setFlash('Error while store payment', 'flash_error');
                $this->redirect(MAIN_SERVER);
            }

              $paymentId = $this->Payment->getLastInsertID();
              $this->Payment->savePaymentPromocodes($payment['promocode_id'], $paymentId);

              $this->Signup->id = $signupDetails['Signup']['id'];
              $this->Signup->saveField('paid', floatval($signupDetails['Signup']['paid']) + floatval($amount));

             /* $result = $this->sendMailMessage('RefundPayment', array(
             		 '{USERNAME}'      => $signupDetails['User']['lgn'],
                     '{FNAME}'         => $signupDetails['User']['firstname'],
                     '{LNAME}'         => $signupDetails['User']['lastname'],
                     '{MODEL}'         => strtolower($signupDetails['Signup']['model']),
                     '{MNAME}'         => $signupDetails[$signupDetails['Signup']['model']]['name'],
                     '{REFUND}'         => '$'.sprintf("%.2f",abs($amount)),
                     '{LINK}'          => MAIN_SERVER."/events/my"),
            $signupDetails['User']['email']
                  );*/


        }

        $this->redirect($_SERVER['HTTP_REFERER']);

    }

    /**
   * Checking if user have any home address
   * currently doesn't use
   */
    function checkAddress() 
    {
        Configure::write('debug', '0');
        $this->layout = false;

        if (!$this->RequestHandler->isAjax()) {
            $this->Session->setFlash('Only for Ajax.', 'flash_error');
            $this->redirect(MAIN_SERVER);
        }

        if (!$this->Session->check('loggedUser')) {
            exit ("You are not logged.");
        }
        if ($this->Session->check('loggedUser')) {
            $userSession = $this->Session->read('loggedUser');
        }

        $this->Address->recursive = -1;
        $addresses = $this->Address->find('list', array('fields' => array('id', 'address'),'conditions'=>array('model'=>'User','label'=>'Home','model_id'=>$userSession['id'],'is_deleted <>'=>1),'order'=>'id DESC'));

        if (empty($addresses)) {

            /*Send error message*/
            $this->Address->recursive = -1;
            $addresses = $this->Address->find('all', array('conditions'=>array('model'=>'User','model_id'=>$userSession['id']),'order'=>'id DESC'));
            $this->sendErrorMessage("Can not find any Home addresses", "An error is happened while signup", $addresses);
            /*EOF sending error message*/

            exit ('You must have at least one "Home" address');
        }

        exit();
    }




    /**
   *  Change package for the current signup
   *  @author vovich
   *  @param int $signupID
   */
    function changePackage($signupID = null) 
    {
        $this->layout = false;
        $userSession = array();
        $additional     = '';
        $isUpgrade    = empty($this->request->params['isUpgrade'])?false:true;

        if ($this->Session->check('loggedUser')) {
            $userSession = $this->Session->read('loggedUser');
        }
    
        $this->Signup->recursive = 1;
        $signupDetails = $this->Signup->find('first', array('conditions' => array( 'Signup.id' => $signupID )));
        if (empty($signupDetails)) {
            exit('Can not find such signup.');
        }

        if ($isUpgrade) {
            $this->Access->checkAccess('canUpgradePackage', 'r', $signupDetails['Signup']['user_id']);// User Owner Admin ALL
            if ((!empty($signupDetails[$signupDetails['Signup']['model']]['finish_signup_date']) && $this->Time->fromString($signupDetails[$signupDetails['Signup']['model']]['finish_signup_date'])<strtotime(date("Y-m-d")) )) {
                $this->Session->setFlash('This signup can not be changed.', 'flash_error');
                $this->redirect(MAIN_SERVER);
            }
        } else {
            $this->Access->checkAccess('SignupChangePackage', 'u', $signupDetails['Signup']['user_id']);
        }
    
        /*
        $roomsCount = $this->SignupRoommate->getCountRooms($signupDetails['Signup']['model'],$signupDetails['Signup']['model_id'],$signupDetails['Signup']['user_id']);
        if ($roomsCount >0) {
        $this->Session->setFlash('You can not change package such as you in the approved team.');
        $this->redirect(MAIN_SERVER);
        }
        */
        //Getting packages
        if (!empty($signupDetails['Packagedetails']['package_id'])) {
            $this->Package->recursive = -1;
            $conditions = array();
            $conditions['Package.id'] = $signupDetails['Packagedetails']['package_id'];
            $packageInformation       = $this->Package->find('first', array('conditions'=>$conditions));
            $signupDetails['Package'] = $packageInformation['Package'];
            unset ($conditions);
        }

        if ($signupDetails['Signup']['for_team']) {
            $packageType = 'team';
            $priceLimit = $signupDetails['Packagedetails']['price_team'];            
        } else {
            $packageType = 'personal';
            $priceLimit = $signupDetails['Packagedetails']['price'];            
        }
    
        if (!$isUpgrade) {
            $priceLimit = 0;
        }
        $packages  =  $this->Package->packagesList($signupDetails['Signup']['user_id'], $signupDetails['Signup']['model'], $signupDetails['Signup']['model_id'], $signupDetails['Signup']['signup_date'], $priceLimit, $packageType);
    
        //pr($packages);
        //exit;
        if (empty($packages)) {
            exit('There is no package that you can change to.');
        }

    
        //Update package ////////!!!!!!!!!!!!!!!
        if (!empty($this->request->data)) {
            //Getting new price
            $packageInfor = $this->Package->packagDetails($this->request->data['Package']['id'], $signupDetails['Signup']['signup_date']);

            if (empty($packageInfor)) {
                $this->Session->setFlash('Can not get package information.', 'flash_error');
                $this->redirect("/signups/signupDetails/".$signupDetails['Signup']['id']);
            }

            $this->request->data['Package']['price']              = $packageInfor['packagedetails']['price'];
            $this->request->data['Package']['price_team']         = $packageInfor['packagedetails']['price_team'];      
            $this->request->data['Package']['packagedetails_id']  = $packageInfor['packagedetails']['id'];

            //store signup    
            $this->request->data['Signup']['for_team'] = $signupDetails['Signup']['for_team'];
            $newSignup['Signup']                         = $signupDetails['Signup'];
            if ($this->request->data['Signup']['for_team']) {
                $newSignup['Signup']['total']                = $this->request->data['Package']['price_team']  ;       
            } else {
                $newSignup['Signup']['total']                = $this->request->data['Package']['price']  ;         
            }
            $newSignup['Signup']['packagedetails_id']   = $this->request->data['Package']['packagedetails_id'] ;
            $newSignup['Signup']['for_team'] = $this->request->data['Signup']['for_team'];

            $dif = $newSignup['Signup']['total']  - $newSignup['Signup']['discount'] - $newSignup['Signup']['paid'];

            if ($dif <= 0 ) {
                $newSignup['Signup']['status'] = 'paid';
                if ($dif < 0) {
                    $rest = min(abs($dif), floatval($newSignup['Signup']['paid']));
                    $rest = -1*$rest;
                    $additional = ' we will refund  $'.sprintf("%.2f", abs($dif));
                } else {
                    $additional = ' signup paid succesfully.';
                }
            } elseif ($dif >0) {
                $newSignup['Signup']['status'] = 'partly paid';
                $additional = ' you must pay $'.sprintf("%.2f", $dif);
            }

            if (!$this->Signup->save($newSignup['Signup'])) {
                $this->logErr('Error while storing Signup');
                $this->Session->setFlash('Error while storing Signup.', 'flash_error');
                 $this->redirect("/signups/signupDetails/".$signupDetails['Signup']['id']);
            }

            //create new payment
            $payment = array();
            $payment['model']         = "Signup";
            $payment['model_id']      = $signupDetails['Signup']['id'];
            $payment['user_id']       = $signupDetails['Signup']['user_id'];
            $payment['payment_date']  = date('Y-m-d H:i:s');
            $payment['amount']        = 0;
            $payment['status']        = 'Approved';
            $payment['reason']        = 'Change package by '.$userSession['lgn'];
            $payment['description']   = 'Change package by '.$userSession['lgn'].' from package '.$signupDetails['Package']['name'].' price:  $'.$signupDetails['Signup']['total'].' ' .
                                                             ' to the package '.$packageInfor['packages']['name'].' price $'.$newSignup['Signup']['total'];
            $payment['information']   = serialize($signupDetails);
            $payment['address_id']    = 0;
            $payment['promocode_id']  = 0;

            $this->Payment->create();
            if (!$this->Payment->save($payment)) {
                $this->logErr('Error while storing Payment');
                $this->Session->setFlash('Error while storing Payment.', 'flash_error');
                $this->redirect("/signups/signupDetails/".$signupDetails['Signup']['id']);
            }

            $paymentId = $this->Payment->getLastInsertID();

            /*Delete room*/
            $rooms = $this->SignupRoommate->find('all', array('fields' => array('SignupRoommate.room_id'),'conditions'=>array('SignupRoommate.user_id'=>$signupDetails['Signup']['user_id'],'Room.model'=>$signupDetails['Signup']['model'],'Room.model_id'=>$signupDetails['Signup']['model_id'],'Room.status'=>array('Created','Pending','Approved','Confirmed'))));
            if (!empty($rooms)) {
                foreach ($rooms as $room) {
                     $this->requestAction("/rooms/delete_room/".$room['SignupRoommate']['room_id'], array('requested' => true, 'return' => true ));
                }
            }
            $Team = ClassRegistry::init('Team');
            $teams = $Team->getUserAssignedTeams($signupDetails['Signup']['user_id'], $signupDetails['Signup']['model'], $signupDetails['Signup']['model_id']);
            if (!empty($teams)) {
                foreach ($teams as $team) {
                     $this->requestAction("/teams/remove_from_signup/" . $signupDetails['Signup']['id'] . '/' . $team['Team']['id'], array('requested' => true, 'return' => true ));
                }
            }
        
            /*EOF Delete room*/
            //sending an email to the user
             $result = $this->sendMailMessage(
                 'ChangePackage', array(
                 '{FNAME}'            => $signupDetails['User']['firstname'],
                 '{LNAME}'               => $signupDetails['User']['lastname'],
                 '{MODEL}'            => $signupDetails['Signup']['model'],
                 '{MNAME}'            => $signupDetails[$signupDetails['Signup']['model']]['name'],
                 '{OLDPNAME}'         => $signupDetails['Package']['name'],
                 '{OLDPDESCRIPTION}'  => $signupDetails['Package']['description'],
                 '{OLDPRICE}'         => '$'.sprintf("%.2f", $signupDetails['Signup']['total']),
                 '{NEWPNAME}'         => $packageInfor['packages']['name'],
                 '{NEWPDESCRIPTION}'  => $packageInfor['packages']['description'],
                 '{NEWPRICE}'         => '$'.sprintf("%.2f", $newSignup['Signup']['total']),
                 '{TOTAL}'            => '$'.sprintf("%.2f", $newSignup['Signup']['total']),
                 '{DISCOUNT}'         => '$'.sprintf("%.2f", $newSignup['Signup']['discount']),
                 '{PAID}'             => '$'.sprintf("%.2f", $newSignup['Signup']['paid']),
                 '{ADDITIONAL}'       => $additional,
                 '{LINK}'             =>  "<a href='http://{$_SERVER['HTTP_HOST']}/signups/signupDetails/".$signupDetails['Signup']['id']."'>View sign up</a>"
                 ), $signupDetails['User']['email']
             );
            ////////////////////////////============================//////////////////////////////////////////
            //history
            $user  = $this->Session->read('loggedUser');
            $History            = ClassRegistry::init('History');
            $historyParams = array();
            $historyParams['user_id']   = $user['id'];
            $historyParams['model']    = $signupDetails['Signup']['model'];
            $historyParams['model_id'] = $signupDetails['Signup']['model_id'];
            $historyParams['affected_user_id'] = serialize($signupDetails['Signup']['user_id']);

            $History->packageIsChanged($signupDetails['Signup']['id'], $historyParams);
            unset($History);
            //EOF HISTORY
            /////////////////////////////===========================/////////////////////////////////////////////
            $this->Session->setFlash('The package has been changed.', 'flash_success');
            $this->redirect("/signups/signupDetails/".$signupDetails['Signup']['id']);
        }
        //EOF updating package ///////!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    
    
        $this->set('packages', $packages);
        $this->set('signupDetails', $signupDetails);
        $this->set('isUpgrade', $isUpgrade);
        $this->set('priceLimit', $priceLimit);
    
        if (empty($this->request->data['Package']['id'])) {
            $this->request->data['Package']['id'] = $signupDetails['Packagedetails']['package_id'];
        }

    }

    /**
   * Cancell with refund Form
   * @author vovich
   * @param int signupID
   */
    function cancelWithRefundForm($signupID = null) 
    {
        Configure::write('debug', '0');
        $this->layout = false;

        $this->Signup->recursive = 1;
        $signupDetails = $this->Signup->find('first', array('contain'=>array('Event'),'conditions'=>array('Signup.id'=>$signupID)));

        $this->Access->checkAccess('Signup', 'd', $signupDetails['Signup']['user_id']);
        if (empty($signupDetails)) {
            echo "error with signup ID";
        }

        $this->request->data['Signup']['amount'] = $signupDetails['Signup']['paid'];
        $this->set('signupDetails', $signupDetails);

    }

    /**
   * Cancell with refund
   * @author vovich
   * @param int signupID
   */
    function cancelWithRefund($signupID = null) 
    {
        Configure::write('debug', '0');
        $this->layout = false;

        $this->Signup->recursive = 1;
        $signupDetails = $this->Signup->find('first', array('contain'=>array('Event'),'conditions'=>array('Signup.id'=>$signupID)));

        if (!$this->RequestHandler->isAjax()) {
             $this->Session->setFlash('This action is not permitted for you. Error 25', 'flash_error');
        }

        $this->Access->checkAccess('Signup', 'd', $signupDetails['Signup']['user_id']);
        if (empty($signupDetails)) {
            echo "error with signup ID";
        }

        $this->request->data['Signup']['amount'] = -1*floatval($this->request->data['Signup']['amount']);
        $this->__makeRefund($signupDetails, $this->request->data['Signup']['amount'], "Refund signup", $this->request->data['Signup']['reason']);
        $this->setAction("cancell", $signupDetails['Signup']['id'], 'setAction');

        exit();
    }

    /**
     *  transferring signup from one to another user (FORM)
     * @author vovich
     * @param int signupID
     */
    function transferringForm($signupID = null)
    {
        Configure::write('debug', '0');
         $this->layout = false;

        $this->Access->checkAccess('Signup', 'u');

         $this->Signup->recursive = 1;
         $signupDetails = $this->Signup->find('first', array('contain'=>array('Event'),'conditions'=>array('Signup.id'=>$signupID)));
        $this->set('signupDetails', $signupDetails);

    }

    /**
     *  transferring signup from one to another user (processing)
     * @author vovich
     * @param int signupID
     */
    function transferring($signupID = null) 
    {
        Configure::write('debug', '0');
        $this->layout = false;
        $user  = $this->Session->read('loggedUser');

        $signupDetails = $this->Signup->find('first', array('contain'=>array('Event'),'conditions'=>array('Signup.id'=>$signupID)));
        if (empty($signupDetails)) {
            exit("Error can not find such signup".$signupID);
        }
        if ($signupDetails['Signup']['for_team']) {
            $this->Session->setFlash('Can not currently do this for a Team Signup', 'flash_error');
            $this->redirect(MAIN_SERVER.'/signups/showAllSignups');
        }
        $this->Access->checkAccess('Signup', 'u', $signupDetails['Signup']['user_id']);

        if (empty($this->request->data['Signup']['login'])) {
            exit("Error login can not be empty");
        }
        $this->Signup->User->recursive = -1;
        $newUserInformation  = $this->Signup->User->find('first', array('conditions'=>array('User.lgn'=>$this->request->data['Signup']['login'])));

        if (empty($newUserInformation)) {
            exit("Error login can not find such User - ".$this->request->data['Signup']['login']);
        }
        //Checking that user doesn't have such signup
        $signupExist = $this->SignupsUser->find(
            'first', array('fields'=>array('id'),'conditions'=>array(
            'SignupsUser.user_id'=>$newUserInformation,
            'Signup.model'=>$signupDetails['Signup']['model'],
            'Signup.model_id'=>$signupDetails['Signup']['model_id']),
            )
        );
        
        if (!empty($signupExist) ) {
            exit("Error: Such User already has signup");
        }
        //Creation history
        $History            = ClassRegistry::init('History');
        $historyParams = array();
        $historyParams['user_id']   = $user['id'];
        $historyParams['model']     = $signupDetails['Signup']['model'];
        $historyParams['model_id'] = $signupDetails['Signup']['model_id'];
        $historyParams['affected_user_id'] =serialize(array($signupDetails['Signup']['user_id'],$newUserInformation['User']['id']));

        $History->signupTransferring($signupDetails['Signup']['id'], $historyParams);
        unset($History);

        //Delete Room
        $rooms = $this->SignupRoommate->find('all', array('fields' => array('SignupRoommate.room_id'),'conditions'=>array('SignupRoommate.user_id'=>$signupDetails['Signup']['user_id'],'Room.model'=>$signupDetails['Signup']['model'],'Room.model_id'=>$signupDetails['Signup']['model_id'],'Room.status'=>array('Created','Pending','Approved','Confirmed'))));
        if (!empty($rooms)) {
            foreach ($rooms as $room) {
                   $this->requestAction("/rooms/delete_room/".$room['SignupRoommate']['room_id'], array('requested' => true, 'return' => true ));
            }
        }

        //Delete team assigments
        $TeamObject = ClassRegistry::init('TeamsObject');
        $teams = $TeamObject->removeAssigment($signupDetails['Signup']['user_id'], $signupDetails['Signup']['model'], $signupDetails['Signup']['model_id']);

         //Sending email to the teammates that team Assigment has been removed
        $Teammate = ClassRegistry::init('Teammate');
        foreach ($teams as $team) {
            $Team->recursive = 1;
            $teammmateInformation = $Teammate->find('all', array('contain'=>array('User','Team'),'conditions'=>array('Teammate.team_id'=>$team['Team']['id'],'Teammate.status'=>array('Creator','Accepted') )));
            $result = $this->sendMailMessage(
                'DeleteTeamObject', array(
                '{MODELNAME}'  => $signupDetails[$signupDetails['Signup']['model']]['name'],
                '{MODEL}'  => $signupDetails['Signup']['model'],
                '{TEAMNAME}'  => $teammmateInformation['Team']['name'],
                '{TEAMDESCRIPTION}'  => $teammmateInformation['Team']['description'],
                '{FNAME}'         => $teammmateInformation['User']['firstname'],
                '{LNAME}'         => $teammmateInformation['User']['lastname']
                 ),
                $teammmateInformation['User']['email']
            );
        }

        unset($TeamObject);

        /*Transferring payments*/
        $this->Payment->recursive = -1;
        $this->Payment->updateAll(array( 'Payment.user_id' =>$newUserInformation['User']['id']  ), array( 'Payment.user_id' => $signupDetails['Signup']['user_id'],'Payment.model'=>"Signup","Payment.model_id"=>$signupDetails['Signup']['id'] ));
        /*Transferring Signups*/
        $this->Signup->recursive = -1;
        $this->Signup->updateAll(
            array( 'Signup.user_id' =>$newUserInformation['User']['id']  ), 
            array( 'Signup.user_id' => $signupDetails['Signup']['user_id'],
                'Signup.model'=>$signupDetails['Signup']['model'] ,"Signup.model_id"=>$signupDetails['Signup']['model_id'] )
        );
        /*Transferring SignupsUser*/
        $this->SignupsUser->recursive = -1;
        $this->SignupsUser->updateAll(
            array('SignupsUser.user_id' => $newUserInformation['User']['id']),
            array('SignupsUser.signup_id'=>$signupDetails['Signup']['id'],
                'SignupsUser.user_id'=>$signupDetails['Signup']['user_id'])
        );
        
        /*Sending an email*/

        exit();
    }
    /**
 * Show reports
 * @return unknown_type
 */
    function reports() 
    {
            $this->Access->checkAccess('showAllSignups', 'r');
            $conditions = array('Signup.status <>'=>'cancelled');;    
             /* filter Getting data from the session or from the form*/
        if(!empty($this->request->data['SignupFilter'])) {
            $this->Session->write('SignupFilter', $this->request->data['SignupFilter']);
        }elseif($this->Session->check('SignupFilter')) {
            $this->request->data['SignupFilter']=$this->Session->read('SignupFilter');
        }

        if (empty( $this->request->data['SignupFilter']['from'])) {
            $from = date('Y-m-01');
            $this->request->data['SignupFilter']['from'] = $this->Time->sqlToCalendar($from);
        } else {
            $from = $this->Time->calendarToSql(trim($this->request->data['SignupFilter']['from']));
        }

        if (empty( $this->request->data['SignupFilter']['to'])) {
               $to = date('Y-m-d');
               $this->request->data['SignupFilter']['to'] = $this->Time->sqlToCalendar($to);
        } else {
            $to = $this->Time->calendarToSql(trim($this->request->data['SignupFilter']['to']));
        }

        if  ($from == $to) {
            $conditions[] = array('DATE(Signup.signup_date)'=>$from);
        } else  {
            $conditions[] = array('DATE(Signup.signup_date) >='=>$from,'DATE(Signup.signup_date) <='=>$to);
        }


            $fields = array('Event.end_date',
                            'Event.start_date',
                            'Event.name',
                            'Signup.model',
                            'Signup.model_id',
                            'Sum(paid) as paid',
                            'Sum(discount) as discount',
                            'Sum(total) as total',
                            'count(Signup.id) as signups');

            //echo "<pre/>";
            //print_r($conditions);
            $signups = $this->Signup->find('all', array('fields' => $fields,'contain'=>array('Event'),'conditions'=>$conditions,'group'=>array('model','model_id')));

        foreach ($signups as $key => $signup) {
                 $ref = $this->Signup->getCountRefunds($signup['Signup']['model'], $signup['Signup']['model_id'], " AND Signup.signup_date >='$from' AND Signup.signup_date <='$to'");
                 $signups[$key][0]['refunds'] = $ref['refunds'];
                 $signups[$key][0]['refundscnt'] = $ref['cnt'];
        }

            $this->set('signups', $signups);

    }
    function testAvailablPackagesOnLive($userID,$eventID) 
    {
        $array = $this->Package->packagesList($userID, 'Event', $eventID);
        return     Set::combine($array, '{n}.id', '{n}.info');
    }
    function completeSignup($signupId) 
    {
        /**
       * This looks to see if this is a Singles tourney. If so, it makes
       * sure that theres a team ssigned to the tourney. If not, it 
       * creates and assign
     */
        $signupUsers = $this->SignupsUser->find('all', array('conditions' => array('signup_id' => $signupId), 'contain' => 'User'));
        
        $signupWithEvent = $this->Signup->find(
            'first', array(
            'conditions'=>array('Signup.id'=>$signupId),
            'contain'=>array('Event'))
        );
        
        if ($signupWithEvent['Signup']['status'] == 'paid' 
            && $signupWithEvent['Event']['people_team'] == 1  
            && count($signupUsers == 1)
        ) {
            $signupUserID = $signupUsers[0]['SignupsUser']['user_id'];
            
            $Team = ClassRegistry::init('Team');
            $singlesTeam = $Team->getSinglesTeam($signupUserID);  
            $TeamsObject = ClassRegistry::init('TeamsObject');
             //Check to see if Team is already assigned to Event
            $TeamsObject->recursive = -1;
            $isTeamInEvent = $TeamsObject->find(
                'all', array('conditions'=>array(
                'model_id'=>$signupWithEvent['Event']['id'],
                'team_id'=>$singlesTeam['Team']['id'],
                'model'=>'Event',
                'status <>'=>'Deleted'
                ))
            );
             
            if (empty($isTeamInEvent)) {
                $teamObject['model']         = 'Event';
                $teamObject['model_id']     = $signupWithEvent['Event']['id'];
                $teamObject['assigner_id']  = $signupWithEvent['Signup']['user_id'];
                $teamObject['name']          = $singlesTeam['Team']['name'];
                $teamObject['status']          = 'Created';
                $teamObject['team_id']       = $singlesTeam['Team']['id'];
                $teamObject['seed']          = 0;
                $TeamsObject->create();
                $TeamsObject->save($teamObject);             
            }
        }
          $this->redirect(MAIN_SERVER.'/signups/signupDetails/'.$signupId);
    }
           
    
      }
?>
