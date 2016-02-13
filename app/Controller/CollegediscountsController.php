<?php 
class CollegediscountsController extends AppController
{

    var $name = 'Collegediscounts';                  
    var $uses = array('User','Package','PackagesUser');
    
    var $possibleSchools = array('unlv','asu','arizona');

    var $schoolInfo = array(
     'unlv'=>array( 
         'displayName'=>'UNLV',
         'landingPage'=>'/UNLV',
         'includesHotel'=>0,
         'price'=>100,
         'packageID'=>61,
         'an'=>0,
         'regExp'=>'/(u|U)(n|N)(l|L)(v|V)/'),
     'asu'=> array(
          'displayName'=>'ASU',
         'landingPage'=>'/Tempe12',
         'includesHotel'=>1,
         'price'=>250,
         'packageID'=>73,
         'an'=>1,
         'regExp'=>'/(a|A)(s|S)(u|U)/'),
      'arizona'=>array(
         'displayName'=>'University of Arizona',
         'landingPage'=>'/Tuscon12',
         'includesHotel'=>1,
         'price'=>250,
         'packageID'=>74,
         'an'=>0,
         'regExp'=>'/(a|A)(r|R)(i|I)(z|Z)(o|O)(n|N)(a|A)/'));   
 
    function index() 
    {
        
    }
    function landingpage($school,$source = '') 
    {
        $this->set('source', $source);
        $this->set('school', $school);
        $this->set('price', $this->schoolInfo[$school]['price']);
        $this->set('schooldisplayname', $this->schoolInfo[$school]['displayName']);
        $this->set('includesHotel', $this->schoolInfo[$school]['displayName']);
        $this->set('an', $this->schoolInfo[$school]['an']);
    }
    
    //School is in ('unlv','asu','arizona'
    function submitemail($school = 'unlv',$source='') 
    {
        $useremail = $this->request->data['User']['useremail'];

            //add this to the unlvemailaddresses table
        $addressTable = ClassRegistry::init('Collegediscounts');
        $addressTable->recursive = -1;
        $addressTable->create();
        $newAddress['email'] = $useremail;
        $newAddress['school'] = $school;
        $newAddress['source'] = $source;
        $result = $addressTable->save($newAddress);        
        
        $schoolDisplayName = $this->schoolInfo[$school]['displayName'];
             
        //Check to see if email is valid unlv address
        $regExp = $this->schoolInfo[$school]['regExp'];
                                                       /*
        if (!preg_match($regExp,$useremail)) {
            $this->Session->setFlash('Email is not a valid '.$schoolDisplayName.' email address', 'flash_error');
            $this->redirect($this->schoolInfo[$school]['landingPage']);
            exit(1);
        }                                                */

        //First of all, does this user have an account? If so, assign that account to the package
        $this->User->recursive = -1;
        $doesUserExist = $this->User->find('first', array('conditions'=>array('email'=>$useremail,'is_deleted'=>0)));
        if ($doesUserExist) {
            //User exists....
            //1. Assign that account to the UNLV package
            $this->assignUserToPackage($doesUserExist['User']['id'], $school);
            //2. If for some reason the user was logged in as someone else, kick em out
            $_SESSION = array();
            $this->Cookie->del('loggedUser');
            //3. Redirect to Login Page.
            $this->redirect('/collegediscounts/accountexists/'.$school);
        }
        else {
            //User does not exist...do the following
            // 1. Create an account for them
            $newUserData = $this->createAccount($useremail, $school);
            // 2. Assign that account to the UNLV package.
            $this->assignUserToPackage($newUserData['id'], $school);
            // 3. Push towaards an 'account created, please activate'
            $this->redirect('/collegediscounts/accountcreated/'.$school);
            
            //Still need to re-order pacakges
            
        }    
    }
    function accountcreated($school) 
    {
        $this->set('schooldisplayname', $this->schoolInfo[$school]['displayName']);    
    }
    function accountexists($school) 
    {
        $this->set('price', $this->schoolInfo[$school]['price']);
        $this->set('schooldisplayname', $this->schoolInfo[$school]['displayName']);
    }
    function createAccount($email,$school) 
    {
        //we know that a) the email is valid and b) no account exists
      
      
        // if no lgn was provided, set it equal to the
        //username portion of the email address
        $exp_array = explode("@", $email);
        $lgn = $exp_array[0];
        //strip the login of non alphanumerics
        $lgn = ereg_replace("[^A-Za-z0-9]", "", $lgn);
        $new_nick = $my_nick = $lgn;
      
        $i=1;
        $nick_unfree=1;
        while ($nick_unfree==1) {
            $this->User->recursive = -1;
            $unfree_user=$this->User->find('count', array('conditions'=>array('lgn'=>$new_nick)));
            if(!$unfree_user) {
                $nick_unfree=0;
                break;
            }else{
                $new_nick=$my_nick.$i;
            }
            $i++;
        }
        // Generate password
        $new_pwd=substr(uniqid(), -6);

        unset($newUser);
        $newUser['User']['email'] = $email;
        $newUser['User']['lgn']=$new_nick;
        $newUser['User']['pwd']=$new_pwd;
        $newUser['User']['firstname']='';
        $newUser['User']['lastname']='';
        /*Storing*/
        $newUser['User']['pwd'] = md5($newUser['User']['pwd']);
        $newUser['User']['activation_code'] = $this->ActivationCode(20);

        $is_exist = $this->User->find('first', array('fields'=>array('id'),'conditions'=>array('activation_code'=>$newUser['User']['activation_code'] )));
        while (!empty($is_exist) ){
            $newUser = $this->ActivationCode(20);
            $is_exist = $this->User->find('first', array('fields'=>array('id'),'conditions'=>array('activation_code'=>$newUser['User']['activation_code'] )));
        }

        $this->User->recursive = -1;
        $this->User->create();

        if ($this->User->save($newUser)) {
            $id = $this->User->getLastInsertID();
            $this->User->habtmAdd('Status', $id, REGISTRY_STATUS_ID);
            $newUser['User']['id'] = $id;
            // 3. Send them an activation email that heads back here
            $this->sendMailMessage(
                'UNLVAccountAdded', array(
                      '{LOGIN}'         => $newUser['User']['lgn'],
                      '{PASSWORD}'         => $new_pwd,
                      '{EMAIL}'         => $newUser['User']['email'],
                '{SCHOOL}'        => $this->schoolInfo[$school]['displayName'],
                      '{LINK}'          => "http://{$_SERVER['HTTP_HOST']}/collegediscounts/activate/{$newUser['User']['activation_code']}"

                ),
                $newUser['User']['email']
            );
            return $newUser['User'];
        }
        else {
            return 'could not save data'; 
        }
    }
    function assignUserToPackage($userID,$school) 
    {
        //Specifically, WSOBP VII and the UNLV package
          
        //66 is on bpongskinny
        //61 on bpong
        $packageID = $this->schoolInfo[$school]['packageID'];
        //  $packageID = 61;
                   
        $this->PackagesUser->recursive = -1;
        $result = $this->PackagesUser->find(
            'all', array('conditions'=>array(
            'package_id'=>$packageID,
            'user_id'=>$userID))
        );
        if ($result) {
            return true; 
        }
        $newPackagesUser['package_id'] = $packageID;
        $newPackagesUser['user_id'] = $userID;
        $this->PackagesUser->create();
        $this->PackagesUser->save($newPackagesUser);
        return true;
    }    
    function activate($actCode = "") 
    {
        $this->Access->checkaccess('Activation');
        $userInfo = array();
        if (!empty($this->request->data['User']['activation_code'])) {
            $actCode = $this->request->data['User']['activation_code'];
        } elseif (!empty($actCode)) {
            $this->request->data['User']['activation_code'] = $actCode;
        }
        if (!empty($actCode)) {
            $this->User->recursive = -1;
            $conditions = array('activation_code' => $actCode);
            $userInfo   = $this->User->find($conditions, array(), null, -1);
            if (!empty($userInfo)) {
                /*Change status*/
                $sql = "DELETE FROM users_statuses WHERE status_id = " . REGISTRY_STATUS_ID . " AND user_id=".$userInfo['User']['id'];
                $this->User->query($sql);
                $this->User->habtmAdd('Status', $userInfo['User']['id'], ACTIVE_STATUS_ID);
                //Now redirect to signups
              
                $this->redirect('/collegediscounts/activated');
            
            } else {
                $this->Session->setFlash('Error: Incorrect Activation code', 'flash_error');
                $this->redirect('/');
            }
        }
    }
    function activated() 
    {
          
    }
}   
?>
