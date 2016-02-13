<?php

  App::import('Model', 'Setting');
  App::import('Model', 'Language');
  App::import('Model', 'Content');
  App::import('Model', 'Contentgroup');

class AppController extends Controller
{

    var $helpTopic = 'default';

    var $helpers = array(
        'Html',
        'Form',
        'Text',
        'Time',
        'Formater',
        'Js' => array('jquery'),
        'Getcontent',
        'Getmetatags',
        'Language',
        'Paginator',
        'Session',
        'Access',
        'Controls',
        'Bbcode',
        'Image',
    'Youtube'
    );

    var $components = array(
        'Session',
        'Access',
        'Json',
        'RequestHandler',
        'Cookie',
        'Logger',
        'DebugKit.Toolbar'
    );

    var $paginate = array(
         'limit'        => 10
        ,'page'         => 1
    );
    var $uses = array('User');

    public $theme = "Bpong";

    public function __construct($request = null, $response = null)
    {
        parent::__construct($request, $response);
        if ($this->name == 'CakeError') {
               $this->constructClasses();
               $this->beforeFilter();
        }
    }



    function beforeFilter() 
    {
        /*
        $points = array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9');
        echo count($points);
        pr($points);		
    	    	
        exit();
        */
        /*
        //working with cookie
        if (!$this->Session->check('loggedUser') && $userSession=$this->Cookie->read('loggedUser')) {
    	      $userSession = unserialize($userSession);
    	      $this->Session->write('loggedUser',$userSession);
        }
        */
        //EOF working    
        $this->__startSession();
        $this->__tournament();
        //$this->storecategoriesMenu();

        $userSession = array();
        if ($this->Session->check('loggedUser')) {
            $userSession = $this->Session->read('loggedUser');
        }


        if($_SERVER['SERVER_PORT']==443 && ($this->name == 'Pages')) {
            $this->redirect('http://'.ltrim($_SERVER['SERVER_NAME'], 'secure.').$_SERVER['REQUEST_URI'], 301);
        }
    }//eof beforeFilter

    function beforeRender() 
    {
        // default title
        if (empty($this->pageTitle)) {
            $this->pageTitle =  str_replace('_', ' ', __(Inflector::humanize(!empty($this->request->params['controller'])?$this->request->params['controller']:$this->name)));
        }
        $this->set('title_for_layout', $this->pageTitle);
    }
    function afterRender() 
    {
        parent::afterRender();
    }



    /**
    * Initialize session data - user and language
    * @author Changed by Vovich
    */
    function __startSession() 
    {

        /*Check If user logged*/
        if (!$this->Session->check('loggedUser')) {
            // changed by Oleg D.
            //$this->Access->loggining(VISITOR_USER);
            $this->Session->write('loggedUser', Configure::read('VisitorSession'));
        }

        if ($this->Session->check('loggedUser')) {
            $this->set('userSession', $this->Session->read('loggedUser'));
        }

        //Menues
        $LoggedMenu = false;
        //    $LoggedMenu = $this->Access->getAccess('LoggedMenu');
        $userID = $this->getUserID();
        $userLogin = $this->getUserLogin();
        
        if (empty($userLogin) || !$userID || $userID == VISITOR_USER) {
            $LoggedMenu = false;    
        }
    
        $this->set('LoggedMenu', $LoggedMenu);
    
        //ADMIN menu
        $AdminMenu = false;
        if($LoggedMenu) {
            //      $AdminMenu = $this->Access->getAccess('AdminMenu');
            $this->set('AdminMenu', $AdminMenu);
        }
        // Customer Service Panel Menu
        $CSpanel = 0;
        //    $CSpanel = $this->Access->getAccess('CustomerShowServices');
        $this->set('CSpanel', $CSpanel);

        //EOF MENU

        //        $objLang = new Language();
        /* set user's language if changed */
        //        if( !empty($this->request->data['Language']['id']) ) {
        //            $lang = $objLang->read('id, code', $this->request->data['Language']['id']);
        //            if ( !empty($lang) ) {
        //                $this->Session->write('User.Lang', $lang['Language']);
        //            }
        //        }
        //
        //        //check and write the default language to session
        //        if (!$this->Session->check('User.Lang')) {
        //          $lang = $objLang->read('id, code', DEFAULT_LANG_ID);
        //            if ( !empty($lang) ) {
        //                $this->Session->write('User.Lang', $lang['Language']);
        //            }
        //        }
        //
        //        unset($objLang);
    }


    /**
     * Enter description here...
     */

    function getsetting($name = null) 
    {
        if (!$name) {
            return false;
        }
        $objSetting = new Setting();
        $setting = $objSetting->find(
            'first', array('conditions' => array(
            'parent_id' => '<> 0'
            ,'name'      => $name
            ))
        );
        if (empty($setting)) {
            $defaultGroup = $objSetting->find(
                'first', array(
                'conditions' => array(
                'parent_id' => 0
                ,'name'      => 'Global'
                )//array
                ,'fields' => 'id'
                )
            );
            if (empty($defaultGroup)) {
                $objSetting->create(array('parent_id' => 0, 'name' => 'Global'));
                $objSetting->save()
                or $this->logErr('cannot create default settings group');
                $gid = $objSetting->id;
            } else {
                $gid = $defaultGroup['Setting']['id'];
            }
            $objSetting->create(array('parent_id' => $gid, 'name' => $name));
            $objSetting->save();
            //or $this->logErr('cannot create a new setting');
        } else {
            return $setting['Setting']['value'];
        }
    }//eof getsetting


    /**
     * Enter description here...
     *
     * @param  unknown_type $template
     * @param  unknown_type $replaceData
     * @return unknown
     */

    function getMailMessage($template, $replaceData = array())
    {
        App::import('Model', 'Mailtemplate');
        $bcc=array();
        $search = array_keys($replaceData);
        $replace = array_values($replaceData);
        $objMailtemplate = new Mailtemplate();
        $objMailtemplate->unbindModel(array('belongsTo' => array('Language')));
        $tpl = $objMailtemplate->find(
            array(
              'code' => $template
             ,'language_id' => $this->Session->read('User.Lang.id')
            ), array('subject','from', 'body','bcc'), null, 0
        );//find
        unset($objMailtemplate);
        $tpl = $tpl['Mailtemplate'];


        if (empty($tpl)) {
            $this->logErr('Could not find email template: ' . $template);
            return false;
        } else {

            if (!empty($tpl['bcc'])) {
                $bcc = split(';', $tpl['bcc']);

            }

            return array(
             'body'    => str_replace($search, $replace, $tpl['body'])
            ,'subject' => str_replace($search, $replace, $tpl['subject'])
            ,'from' =>  $tpl['from']
            ,'bcc'=>$bcc
            );//array
        }
    }//eof getMailMessage

    /**
     *  Sending email message
     * @author vovich
     */
    function sendMailMessage($template=null, $replaceData=array(),$emailto)
    {

         App::import('Vendor', 'PHPMailer', array('file' => 'mailer.class.php'));
         $mailer = new PHPMailer();
         //$mailer->From = 'no-reply@bpong.com';

         $emailto = str_replace(',', ';', $emailto);
        $emailto = str_replace(' ', '', $emailto);
        $emailto = explode(';', $emailto);

        if (is_array($emailto)) {
            foreach ($emailto as $email) {
                $email = trim($email);
                $mailer->AddAddress($email, $email);
            }
        } else {
            $emailto = trim($emailto);
            $mailer->AddAddress($emailto, $emailto);
        }

         //$mailer->AddAddress("dikusarv@mail.ru", "dikusarv@mail.ru");
         $template = $this->getMailMessage($template, $replaceData);
        $mailer->CharSet = 'utf-8';
         $mailer->Subject = $template['subject'];
         $mailer->Body    = $template['body'];
         $mailer->From = $template['from']!=''?$template['from']:'no-reply@beerpong.com';
             $mailer->FromName = $mailer->From;
        if (!empty($template['bcc'])) {
            foreach ($template['bcc'] as $bcc){
                $mailer->AddBCC($bcc, $bcc);
            }

        }

         /*$debug = Configure::read('debug');*/
         /* $fp = fopen('email'.time().'.html',"w+");
          fwrite($fp,"Emailto:".$emailto."<BR>Subject:".$template['subject']."<BR> Body:".$template['body']);
          fclose($fp);*/
          //echo "Emailto:".$emailto."<BR>Subject:".$template['subject']."<BR> Body:".$template['body'];
        $mailer->ContentType = 'text/html';

         return $mailer->Send();

    }


    /**
     * Enter description here...
     *
     * @param  unknown_type $token
     * @return unknown
     */

    function getcontent($token = null) 
    {
        if (empty($token) ) {
            return '';
        }

        $langId = $this->Session->read('User.Lang.id');

        $objContent = new Content();

        $content = $objContent->find(
            'first', array(
            'contain'   => array()
            ,'conditions' => array('token' => $token, 'language_id' => $langId)
            )
        );//find

        if (!empty($content)) {
            $out = $content['Content']['content'];
            $id = $content['Content']['id'];
        } else {
            //is there any tokens with this name
            $content = $objContent->findByToken($token);
            if (!empty($content) ) {
                $groupId = $content['Content']['contentgroup_id'];
            } else {
                $objGroup = new Contentgroup();
                $group = $objGroup->findByName('General');
                unset($objGroup);
                $groupId = $group['Contentgroup']['id'];
            }//if
            $defaultContent = $objContent->find(
                array(
                 'token'       => $token
                ,'language_id' => $this->Session->read('DefaultLang.id')
                )
            );//find
            if (empty($defaultContent) ) {
                $defaultContent = array( 'Content' => array(
                     'token'       => $token
                    ,'language_id' => $this->Session->read('DefaultLang.id')
                    ,'title'       => ''
                    ,'content'     => '<:empty:>'
                    ,'contentgroup_id' => $groupId
                ));//array
                $objContent->save($defaultContent);
                $id = $objContent->getLastInsertID();
            } else {
                $id = $defaultContent['Content']['id'];
            }//if
            $out = $defaultContent['Content']['content'];
        }//if

        return $out;

    }//eof getcontent

    function ActivationCode($length = 10)
    {

        $alphabeth = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZhijklmnopqrstuvwxyz";
        $alphabeth = str_shuffle($alphabeth);
        $code = "";

        for($i=0;$i<$length;$i++) {
            $code.=$alphabeth[mt_rand(0, strlen($alphabeth)-1)];
        }


        return     $code.time();

    }
    /**
     *  working with  session for tournaments
     *  @author vovich
     */
    function __tournament()
    {

        if ($this->Session->check('Tournament')) {
            $tournament = $this->Session->read('Tournament');

            if (empty($tournament) || empty($tournament['remain_to_signup']) || $tournament['date']!=date('Y-m-d-h') ) {
                $this->updateTtournamentMenu();
            }

        } else {
            $this->updateTtournamentMenu();
        }

    }

    /**
    * getting last tournament and create session
    * @author vovich
    */
    function updateTtournamentMenu()
    {

        unset($_SESSION['Tournament']);
        //       $_date = -1;
        //       $event = Cache::read('turnament', 'turnament');
        //		if (empty($event)) {
        //           App::import('Model', 'Event');
        //           $objEvent = new Event();
        //           $conditions = array('shown_on_front'=>1,'is_deleted'=>0, 'end_date >'=>date('Y-m-d-h'));
        //           $event = $objEvent->find('first',array("conditions"=>$conditions,'fields'=>'slug,id,name,shortname,url,start_date,end_date, finish_signup_date, start_date, end_date' ,'order'=>"start_date ASC"));
        //           if (empty($event)) {
        //               Cache::write('turnament', 'no_event', 'turnament');
        //           } else {
        //               Cache::write('turnament', $event, 'turnament');
        //           }
        //	    }
        //        if ($event == 'no_event') {
        //            $event = array();
        //        }
        //       //$event = $objEvent->find('first',array("conditions"=>$conditions,'fields'=>'slug,id,name,shortname,url,start_date,end_date, TO_DAYS(Event.finish_signup_date)-TO_DAYS(NOW()) as remain_to_signup, TO_DAYS(Event.start_date)-TO_DAYS(NOW()) as remain_to_start,TO_DAYS(Event.end_date)-TO_DAYS(NOW()) as remain_to_end' ,'order'=>"start_date ASC"));
        //       $now = date("Y-m-d H:i:s");
        //       if (!empty($event)) {
        //           $event['Event']['remain_to_start'] = intval((strtotime($event['Event']['start_date']) - strtotime($now))/86400);
        //           $event['Event']['remain_to_end'] = intval((strtotime($event['Event']['end_date']) - strtotime($now))/86400);
        //           $event['Event']['remain_to_signup'] = intval((strtotime($event['Event']['finish_signup_date']) - strtotime($now))/86400);
        //
        //           $event['Event']['date'] =date('Y-m-d');
        //           //$event['Event']['remain_to_start']  =  $event[0]['remain_to_start'];
        //           //$event['Event']['remain_to_end']    =  $event[0]['remain_to_end'];
        //           //$event['Event']['remain_to_signup'] =  $event[0]['remain_to_signup'];
        //
        //             if (!empty($event)  && $event['Event']['remain_to_signup'] >= 0) {
        //                 $_date = $event['Event']['remain_to_signup'];
        //              } elseif (!empty($event)  && $event['Event']['remain_to_start']>0) {
        //                 $_date = $event['Event']['remain_to_start'];
        //              } elseif($event['Event']['remain_to_end']>0) {
        //                  $_date = $event['Event']['remain_to_end'];
        //              }
        //
        //              if ($_date >= 0) {
        //                  $_date = sprintf("%03d",$_date);
        //                  $_remaining = array();
        //                  for ($i=0; $i<strlen($_date);$i++){
        //                         $_remaining[]=$_date[$i];
        //                  }
        //                  $event['Event']['remaining'] = $_remaining;
        //                  $this->Session->write('Tournament',$event['Event']);
        //              }
        //       }
    }
    function logErr($msg) 
    {
        $this->log("\nController: {$this->name}\nMessage: {$msg}\n");
    }

    /**
     * Return UserID - from hidden_user_id    or $userSession['id'];
     * @author Oleg
     */
    function getUserID() 
    {
        $user_id=0;
        $userSession = $this->Session->read('loggedUser');
        $session_user_id=$userSession['id'];
        $hidden_user_id=0;
        if($this->Session->check('hidden_user_id')) {
            $hidden_user_id = $this->Session->read('hidden_user_id');
        }
        /*
        if($session_user_id==VISITOR_USER){
        $session_user_id=0;
        }
        */
        if($hidden_user_id) {
            $user_id=$hidden_user_id;
        }
        if($session_user_id) {
            $user_id=$session_user_id;
        }
        return $user_id;

    }
    /**
     * Checks loggined user or no
     * @author Oleg D.
     */
    function isLoggined() 
    {
        if (!$this->getUserID() || $this->getUserID() == VISITOR_USER) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Checks loggined user or no, if no, redirects to home page
     * @author Oleg D.
     */
    function checkLoggined() 
    {
        if (!$this->isLoggined()) {
            $this->Session->write('URL', $this->request->here);
            $this->Session->setFlash('You are not logged in', 'flash_error');
            return $this->redirect('/login');
        } else {
            return true;
        }
    }
    
    /**
     * Return UserLogin
     * @author Oleg
     */
    function getUserLogin()
    {
        $userSession = $this->Session->read('loggedUser');
        return $userSession['lgn'];
    }
    /**
 * Send error message
 * @author vovich
 * @param $body
 * @param $subject
 * @param $hiddenBody
 * @return unknown_type
 */
    function sendErrorMessage($body="" , $subject="An error is happened",  $hiddenBody="")
    {
         //$this->Session->write('Error',"Error: ".$body);
         $body.="<br>Vars dump<br>";

        if (!empty($this->request->data)) {
            $body.=$this->print_a($this->request->data); 
        }

        if (!empty($_SESSION)) {
            $body.= "SESSION :<br>";
            $body.= $this->print_a($_SESSION);
        }
         $body.= "<br>Time: ". date("D, M jS Y, H:i:s", time());;
         $body.= "<br> URL: ".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
         $body.= "<br>USER AGENT:".$_SERVER["HTTP_USER_AGENT"]."<br>";

        if (!empty($hiddenBody)) {
            $body .= "<br>Hidden Body<br>";
            if (is_array($hiddenBody)) {
                $body .= $this->print_a($hiddenBody);
            } else {
                $body .= $hiddenBody;
            }
        }

         App::import('Vendor', 'PHPMailer', array('file' => 'mailer.class.php'));
         $mail = new PHPMailer();

         $mail->ContentType="text/html";
         $mail->From = 'support@bpong.com';
         $mail->FromName = 'From: '.$_SERVER['SERVER_NAME']." email service";

         $mail->AddAddress('odikusar@shakuro.com', 'odikusar@shakuro.com');
         $mail->Subject = $subject;
         $mail->Body     = $body;
         $mail->Send();

    }

    /**
 * for the error message
 * @author vovich
 * @param unknown_type $array
 * @return unknown_type
 */
    function print_a($array) 
    {
        $result="";
        if (!is_array($array)) {
            $result.='<span allign="left"><pre>';
            $result.=$array;
            $result.='</pre></span>';
            return $result;
        }
        $result.="<table style='font-size:11px'>";
        $keys = array_keys($array);
        foreach( $keys as $oneKey ) {
            $result.="<tr>";
            $result.="<td bgcolor='#99ccff' style='border:1px solid black'>";
            $result.="<B>" . $oneKey . "</B>";
            $result.="</td>";
            $result.="<td bgcolor='#99ff99' style='border:1px solid black'>";
            if (is_array($array[$oneKey]) ) {
                $result.=$this->print_a($array[$oneKey]); 
            }
            else {
                $result.=$array[$oneKey]; 
            }
            $result.="</td>";
            $result.="</tr>";
        }
        $result.="</table>";
        return $result;
    }

    function convert_bbcode($text) 
    {
        App::import('Vendor', 'BbcodeLib', array('file' => 'bbcode' . DS . 'bbcode.lib.php'));
        $bb = new bbcode($text);
        // convert BBCode to HTML
        return $bb->get_html();
    }
    /**
     * no cache function
     * @author Oleg D.
     * */
    function noCache() 
    {
         header("Cache-Control: no-store, no-cache, must-revalidate");
         header("Expires: " . date("r"));
    }
    /**
     * get random string
     * @author Oleg D.
     * */
    function genRandomString($length = 10, $content = null) 
    {
        if (!$content) {
               $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        } elseif ($content == 'digits') {
            $characters = '0123456789';
        } else {
               $characters = 'abcdefghijklmnopqrstuvwxyz';
        }


        $string = '';

        for ($p = 0; $p < $length; $p++) {
            $string .= $characters[mt_rand(0, strlen($characters) -1)];
        }
        return $string;
    }

    function goBack($default = '/') 
    {
        if ($_SERVER['HTTP_REFERER']) {
            $this->redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->redirect($default);
        }
    }
    function goHome() 
    {
        $userLogin = $this->getUserLogin();
        if ($userLogin) {
            return $this->redirect('/u/' . $userLogin);
        } else {
            return $this->redirect('/');
        }
    }

    function escapeURL($string,$nums=0)
    {
            $string = str_replace(" ", "_", $string);
            $string = str_replace("&amp;", "and", $string);
            $string = str_replace("&", "and", $string);
            $string = eregi_replace('[^a-zA-Z0-9]', '_', $string);
        //        $string = iconv("UTF-8", "ISO-8859-1", $string);
        $string = str_replace("___", "_", $string);
         $string = str_replace("__", "_", $string);
        if($nums) {
            $string=substr($string, 0, $nums); 
        }
        $string=strtolower($string);

            return $string;
    }
    protected function returnMobileResult($result,$amf) 
    {
        //If $amf, just return, else return JSON
        if ($amf) {
            return $result; 
        }
        else {
            return $this->returnJSONResult($result); 
        }
    }
    protected function returnJSONResult($result) 
    {
        Configure::write('debug', 0);
        $this->set('result', $result);

        $this->view = 'Json';
        $this->set('json', 'result');
        return $result;
    }
    function __isTimeOver( $checkTime ) 
    {
        $signuptime = strtotime($checkTime);//$signupDetails[$signupDetails['Signup']['model']]['finish_signup_date']
        if (!empty( $signuptime ) && strtotime(date("Y-m-d")) > $signuptime ) {
            return true;
        }
        return false;
    }
    function canUserViewStats($userID) 
    {
        return ($this->isUserSuperAdmin($userID) || $userID == 33180);
    }
    function isUserSuperAdmin($userID = null) 
    {
        if (!$userID) {
            if (!$this->isLoggined()) {
                 return false;
            }
            $user = $this->Session->read('loggedUser');
            $userID = $user['id'];
        }
        //Oleg, Skinny, Skinny2, Duncan, Billy, Kerry
        return ($userID == 2 || $userID == 25 || $userID == 44139 || $userID == 6 || $userID == 17 || $userID == 61275
        || $userID == 60545);
    }
    /**
    * This function returns the user if it exists. If it does not exist, it creates a new account and returns it. It
    * will probably be used in a couple of different controllers, so I decided to place it here
    * @author: skinny
    */
    
    protected function getPlayerFromEmailAndPassword($email,$md5Pass) 
    {
      
        if ($email == '') { return "bad email"; 
        }
        $validEmail = '/\\A(?:^([a-z0-9][a-z0-9_\\-\\.\\+]*)@([a-z0-9][a-z0-9\\.\\-]{0,63}\\.(com|org|net|biz|info|name|net|pro|aero|coop|museum|[a-z]{2,4}))$)\\z/i';
        if (!preg_match($validEmail, $email)) { return 'bad email'; 
        }
        if (!is_string($md5Pass) || strlen($md5Pass) < 5) {
            return array('bad password'=>$md5Pass); 
        }
    
        $this->User->recursive = -1;
        $user = $this->User->find(
            'first', array('conditions'=>array(
            'email'=>$email,
            'is_deleted'=>0),'recursive'=>-1)
        );
        if ($user) {
            if ($user['User']['pwd'] == $md5Pass) {
                return $user; 
            }
            else {
                return 'bad password'; 
            }
        }
        else 
        {                    //create a new player from this data
            $exp_array = explode("@", $email);
            $new_nick=$my_nick=$exp_array['0'];
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
            
            $this->request->data['User']['email'] = $email;
            $this->request->data['User']['lgn']=$new_nick;
            $this->request->data['User']['pwd']=$md5Pass;
            $this->request->data['User']['firstname']='';
            $this->request->data['User']['lastname']='';
            $this->request->data['User']['activation_code'] = $this->ActivationCode(20);

            $is_exist = $this->User->find('first', array('fields'=>array('id'),'conditions'=>array('activation_code'=>$this->request->data['User']['activation_code'] )));
            while (!empty($is_exist) ){
                $this->request->data['User']['activation_code'] = $this->ActivationCode(20);
                $is_exist = $this->User->find('first', array('fields'=>array('id'),'conditions'=>array('activation_code'=>$this->request->data['User']['activation_code'] )));
            }

            $this->User->create();

            if ($this->User->save($this->request->data)) {
                $id = $this->User->getLastInsertID();
                $this->User->habtmAdd('Status', $id, REGISTRY_STATUS_ID);
                $this->request->data['User']['id'] = $id;
                //Generate QR Code
                $mysqldate = date('Y-m-d H:i:s');   // needs semicolon between day & time or will break     (duncan)
                $qr_togen = "0;".$this->request->data['User']['id'].";".$this->request->data['User']['lgn'].";".$this->request->data['User']['email'].";0;".$mysqldate; 
                $this->User->generate_and_save_new_qr($qr_togen, $this->request->data['User']['email'], $mysqldate);
                $userInfo = $this->User->find('first', array('conditions'=>array('email'=>$this->request->data['User']['email'])));
                $qrimagelink = MAIN_SERVER.'/img/'.$userInfo['User']['qr_image'];
            
                $this->sendMailMessage(
                    'ActivationEmail', array(
                                 '{LINK}'          => "http://{$_SERVER['HTTP_HOST']}/activation/{$this->request->data['User']['activation_code']}",  
                                 '{QRIMAGE}' => $qrimagelink
                         ),
                    $this->request->data['User']['email']
                ); 
                return $this->User->find(
                    'first', array('conditions'=>array(
                    'email'=>$email,
                    'is_deleted'=>0),'recursive'=>-1)
                );
                
                
            }
            else {
                return array("Could not save data."); 
            }
        }
    }
    /**
    * This function returns the user if it exists. If it does not exist, it creates a new account with a randomly 
    * generated password and returns it. It will probably be used in a couple of different controllers, so I decided to place it here
    * 
    * If the password != 0, it must be the same as the password in the d.b.
    * If the $timestamp > 0, it must be at least as recent as the qr_generated_date
    * 
    * @author: skinny
    */
    protected function getOrCreateUser($email,$md5Pass,$timestamp = 0) 
    {
      
        if ($email == '') { return "bad email"; 
        }
        $validEmail = '/\\A(?:^([a-z0-9][a-z0-9_\\-\\.\\+]*)@([a-z0-9][a-z0-9\\.\\-]{0,63}\\.(com|org|net|biz|info|name|net|pro|aero|coop|museum|[a-z]{2,4}))$)\\z/i';
        if (!preg_match($validEmail, $email)) { return 'bad email'; 
        }
        if ($md5Pass != 0 && (!is_string($md5Pass) || strlen($md5Pass) < 5)) {
            return array('bad password'=>$md5Pass); 
        }
    
        $this->User->recursive = -1;
        $user = $this->User->find(
            'first', array('conditions'=>array(
            'email'=>$email,
            'is_deleted'=>0),'recursive'=>-1)
        );
        if ($user) {
            // Need at least a password or a timestamp
            if ((!is_string($md5Pass) || $md5Pass=="0")  && $timestamp == "0") {
                return "Need password or timestamp";
            }
            //If the password is provided, needs to be correct
            if (is_string($md5Pass) && $md5Pass != "0") {
                if ($user['User']['pwd'] != $md5Pass) {
                    return "Bad Password";
                }
            }
            //If a timestamp is provided, needs to be current
            if ($timestamp != "0") {
                if ($user['User']['qr_generated'] > $timestamp) {
                    return "Expired Timestamp";
                }
            }                                    
            //If we've gotten here, we're good.
            return $user;                  
        }
        else 
        {                    //create a new player from this data
            $exp_array = explode("@", $email);
            $new_nick=$my_nick=$exp_array['0'];
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
            $this->request->data['User']['pwd']=$new_pwd;
            $this->request->data['User']['pwd'] = md5($this->request->data['User']['pwd']);        
            $this->request->data['User']['email'] = $email;
            $this->request->data['User']['lgn']=$new_nick;
            $this->request->data['User']['firstname']='';
            $this->request->data['User']['lastname']='';
            $this->request->data['User']['activation_code'] = $this->ActivationCode(20);

            $is_exist = $this->User->find('first', array('fields'=>array('id'),'conditions'=>array('activation_code'=>$this->request->data['User']['activation_code'] )));
            while (!empty($is_exist) ){
                $this->request->data['User']['activation_code'] = $this->ActivationCode(20);
                $is_exist = $this->User->find('first', array('fields'=>array('id'),'conditions'=>array('activation_code'=>$this->request->data['User']['activation_code'] )));
            }

            $this->User->create();

            if ($this->User->save($this->request->data)) {
                $id = $this->User->getLastInsertID();
                $this->User->habtmAdd('Status', $id, REGISTRY_STATUS_ID);
                $this->request->data['User']['id'] = $id;
                //Generate QR Code
                $mysqldate = date('Y-m-d H:i:s');   // needs semicolon between day & time or will break     (duncan)
                //If there is a timestamp, lets use that, because theres already a qr code in circulation
                if ($timestamp > 0) {    
                    $mysqldate = $timestamp;
                }
                $qr_togen = "0;".$this->request->data['User']['id'].";".$this->request->data['User']['lgn'].";".$this->request->data['User']['email'].";0;".$mysqldate;
                $this->User->generate_and_save_new_qr($qr_togen, $this->request->data['User']['email'], $mysqldate);
                $userInfo = $this->User->find('first', array('conditions'=>array('email'=>$this->request->data['User']['email'])));
                $qrimagelink = MAIN_SERVER.'/img/'.$userInfo['User']['qr_image'];
            
                /*     $this->sendMailMessage('ActivationEmail', array(
                                 '{LINK}'          => "http://{$_SERVER['HTTP_HOST']}/activation/{$this->request->data['User']['activation_code']}"  
                         ),
                      $this->request->data['User']['email']
                            );    */
                $this->sendMailMessage(
                    'NewPlayerAdded', array(
                         '{LOGIN}'         => $this->request->data['User']['lgn'],
                         '{PASSWORD}'         => $new_pwd,
                         '{EMAIL}'         => $this->request->data['User']['email'],
                         '{LINK}'          => "http://{$_SERVER['HTTP_HOST']}/activation/{$this->request->data['User']['activation_code']}",
                         '{QRIMAGE}' => $qrimagelink             
                    ),
                    $this->request->data['User']['email']
                );
                
                 return $this->User->find(
                     'first', array('conditions'=>array(
                     'email'=>$email,
                     'is_deleted'=>0),'recursive'=>-1)
                 );     
            }
            else {
                return array("Could not save data."); 
            }
        }
    }
    //C2DM Authentication functions
    // author duncan@bpong.com
    function c2dm_authenticate($username, $password, $source="BPONG-SCOREKEEPER-1.0", $service="ac2dm") 
    {
      
        $alreadyAuthed = $this->Session->check('c2dm_auth');
        if ($alreadyAuthed) {
            return $this->Session->read('c2dm_auth');
        }
      
        session_start();
        if(isset($_SESSION['google_auth_id']) && $_SESSION['google_auth_id'] != null) {
            return $_SESSION['google_auth_id']; 
        }

        // get an authorization token
        $ch = curl_init();
        if(!$ch) {
            return false;
        }

        curl_setopt($ch, CURLOPT_URL, "https://www.google.com/accounts/ClientLogin");
        $post_fields = "accountType=" . urlencode('HOSTED_OR_GOOGLE')
        . "&Email=" . urlencode($username)
        . "&Passwd=" . urlencode($password)
        . "&source=" . urlencode($source)
        . "&service=" . urlencode($service);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);    
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // for debugging the request
        //curl_setopt($ch, CURLINFO_HEADER_OUT, true); // for debugging the request

        $response = curl_exec($ch);

        // var_dump(curl_getinfo($ch)); //for debugging the request
        // var_dump($response);

        curl_close($ch);

        if (strpos($response, '200 OK') === false) {
            return false;
        }

        // find the auth code
        preg_match("/(Auth=)([\w|-]+)/", $response, $matches);
    
        if (empty($matches[2])) {
            return false;
        }

        $_SESSION['google_auth_id'] = $matches[2];
        return $matches[2];
    }
    function sendMessageToPhone($auth_token, $registrationId, $msgType, $command, $data) 
    {

        $messageUrl = "https://android.apis.google.com/c2dm/send";
        $collapseKey = $msgType;
        $data = array('data.COMMAND'=>$command, 'data.DATA' => $data); //The content of the message
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $messageUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    
        $header = array("Authorization: GoogleLogin auth=".$auth_token); //Set the header with the Google Auth Token
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    
        $postFields = array("registration_id" => $registrationId, "collapse_key" => $collapseKey);
        $postData = array_merge($postFields, $data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    
        $response = curl_exec($ch);

        curl_close($ch);
        
    }
    
    
    function c2dm_sendMessageToUser() 
    {
        Configure::write('debug', 0);
        
        $to = "duncan.carroll2@gmail.com";
        $data     = 'We hear you like ducks.  A lot.';    // this is the version number
        
        $command = 'NOTICE';
        //$command = 'UPDATE';
        //$data     = '12';    // this is the NEW version number
        $messageType = "com.bpong.scorekeeper.client.";
        
        
        $authCode = $this->c2dm_authenticate("duncan@bpong.com", "parvard1gar"); // er....

        $deviceIDs = $this->User->getUsersDeviceID($to); 
        $count = 0;
        foreach ($deviceIDs as $deviceID) {
            $collapse_key = $messageType . $count;    // make the collapse key unique-ish
            $response = $this->sendMessageToPhone($authCode, $deviceID['users']['c2dm_key'], $collapse_key, $command, $data);
            $count++;
        }
        
        $result = "{ \"result\" : \"sent to "+ $count + " clients\"}";
        
        $this->set('content_for_layout', $result);
        $this->render('../layouts/json');
        
    }
    
    function c2dm_sendMessageToAllUsers() 
    {
        Configure::write('debug', 0);
        
        //$to = "duncan.carroll@gmail.com";
        $data     = 'Welcome to the terrordome.';    // this is the version number
        
        $command = 'NOTICE';
        $messageType = "com.bpong.scorekeeper.client.";
        
        
        $authCode = $this->c2dm_authenticate("duncan@bpong.com", "parvard1gar"); // er....

        $deviceIDs = $this->User->getAllAndroidDeviceIDs(); 
        $count = 0;
        
        foreach ($deviceIDs as $deviceID) {
            $collapse_key = $messageType . $count;    // make the collapse key unique-ish
            $response = $this->sendMessageToPhone($authCode, $deviceID['users']['c2dm_key'], $collapse_key, $command, $data);
            $count++;
        }
        
        $result = "{ \"result\" : \"sent to "+ $count + " clients\"}";
        
        $this->set('content_for_layout', $result);
        $this->render('../layouts/json');
        
    }       
    function c2dm_updateAllAndroidClientsToLatestVersion() 
    {
        Configure::write('debug', 0);
        $messageType = "com.bpong.scorekeeper.client.";
        $command = 'UPDATE';
        $data     = '13';    // this is the NEW version number
        
        $authCode = $this->c2dm_authenticate("duncan@bpong.com", "parvard1gar"); // er....
        $deviceIDs = $this->User->getAllAndroidDeviceIDs(); 

        $count = 0;
        foreach ($deviceIDs as $deviceID) {
            $collapse_key = $messageType . $count;    // make the collapse key unique-ish
            $response = $this->sendMessageToPhone($authCode, $deviceID['users']['c2dm_key'], $collapse_key, $command, $data);
            $count++;
        }
        
        $result = "{ \"result\" : \"sent to "+ $count + " clients\"}";
        
        $this->set('content_for_layout', $result);
        $this->render('../layouts/json');
        
    }
    /*
    I have no idea why the native array_unique function doesn't work, but it doesnt.
    */                                                                                
    function custom_array_unique($array) 
    {
        if (!is_array($array)) {
            return false; 
        }
        $result = array();
        foreach ($array as $data) {
            $result[$data] = $data;
        }
        return $result;
    }
    //We're going to need this in at least two controllers, so I'm placing it here'''
    protected function mergeTwoTeams($teamIDToDelete, $teamIDToMergeInto) 
    {
        /**                
         * Objects to consider: 
         * Teamates: Will delete the teammates of the old team
         * Personal Image: Will use the image of the new team only - should delete old
         * Games: Transfer to new team
         * Teams_Object: Will transfer to new team
         * Team: Add Wins/Losses/CupDif, and delete
         * Ratinghistory: Transfer to new team
         */
        // For right now, only allow skinny to do this
        if (!$this->Session->check('loggedUser')) {
            return "You are not logged in.";
        }
        $Teammate = ClassRegistry::init('Teammate');
        $Teammate->recursive = -1;
        $loggedUserID = $this->getUserID();
        
        //first, either this user must be on both teams, or this must be a superadmin
        $isUserOnTeamConditions = array('user_id'=>$this->getUserID(),'team_id'=>$teamIDToDelete,'status'=>array('Creator','Accepted','Pending'));
        $isUserOnFirstTeam = $Teammate->find('first', array('conditions'=>$isUserOnTeamConditions));
        $isUserOnTeamConditions['team_id'] = $teamIDToMergeInto;
        $isUserOnSecondTeam = $Teammate->find('first', array('conditions'=>$isUserOnTeamConditions));
        if (!$this->isUserSuperAdmin() && (!$isUserOnFirstTeam || !$isUserOnSecondTeam)) {
            return "Access Denied";
        }
        //return 'qeq';
        //Team
        $Team = ClassRegistry::init('Team');
        $Team->recursive = -1;
        $teamToDelete = $Team->find(
            'first', array('conditions'=>array('id'=>$teamIDToDelete,
            'status <> '=>'Deleted'))
        );
        if (!$teamToDelete) { return "Team to delete does not exist"; 
        }
        $teamToMergeInto = $Team->find(
            'first', array('conditions'=>array('id'=>$teamIDToMergeInto,
            'status <>'=>'Deleted'))
        );
        if (!$teamToMergeInto) { return "Team to merge into does not exist"; 
        }
        $newTeammates = $Teammate->find(
            'all', array('conditions'=>array('team_id'=>$teamIDToMergeInto,
            'status'=>array('Creator','Pending','Accepted')))
        );
        $oldTeammates = $Teammate->find(
            'all', array('conditions'=>array('team_id'=>$teamIDToDelete,
            'status'=>array('Creator','Pending','Accepted')))
        );
        if (count($newTeammates) != count($oldTeammates)) {
            return array(count($newTeammates),count($oldTeammates),$teamIDToMergeInto); //"Teams do not have the same number of players";
        }        foreach ($newTeammates as $newTeammate) {
            $teammateFound = false;
            foreach ($oldTeammates as $oldTeammate) {
                if ($oldTeammate['Teammate']['user_id'] == $newTeammate['Teammate']['user_id']) {
                    $teammateFound = true;
                }
            }
            if (!$teammateFound) {
                return "Teammates do not match";
            }
        }    
        // Delete the team
        $teamToDelete['Team']['status'] = 'Deleted';
        $teamToDelete['Team']['is_deleted'] = 1;
        $Team->save($teamToDelete);
        //Transfer over all non-deleted games
        $Game = ClassRegistry::init('Game');
        $Game->recursive = -1;
        $games = $Game->find(
            'all', array('conditions'=>array('status <> '=>'Deleted',
            'OR'=>array(
                'team1_id'=>$teamIDToDelete,
                'team2_id'=>$teamIDToDelete,
                'winningteam_id'=>$teamIDToDelete)))
        );
        foreach ($games as $game) {
            if ($game['Game']['team1_id'] == $teamIDToDelete) {
                $game['Game']['team1_id'] = $teamIDToMergeInto; 
            }
            if ($game['Game']['team2_id'] == $teamIDToDelete) {
                $game['Game']['team2_id'] = $teamIDToMergeInto; 
            }
            if ($game['Game']['winningteam_id'] == $teamIDToDelete) {
                $game['Game']['winningteam_id'] = $teamIDToMergeInto; 
            }    
            $Game->save($game);        
        }   
        //Ratinghistory
        $Ratinghistory = ClassRegistry::init('Ratinghistory');
        $Ratinghistory->recursive = -1;
        $ratinghistories = $Ratinghistory->find('all', array('conditions'=>array('team_id'=>$teamIDToDelete)));
        foreach ($ratinghistories as $ratinghistory) {
            $ratinghistory['Ratinghistory']['team_id'] = $teamIDToMergeInto;
            $Ratinghistory->save($ratinghistory);
        }     
        
        //For each old teammate, find the corresponding new teammate, if the old teammate was not pending, 
        //update the new teammate
        unset($oldTeammate);
        unset($newTeammate);
        foreach ($oldTeammates as $oldTeammate) {
            foreach ($newTeammates as &$newTeammate) {
                if ($newTeammate['Teammate']['user_id'] == $oldTeammate['Teammate']['user_id']) {
                    if ($oldTeammate['Teammate']['status'] != 'Pending' && $newTeammate['Teammate']['status']=='Pending') {
                        $newTeammate['Teammate']['status'] = $oldTeammate['Teammate']['status'];
                        $Teammate->save($newTeammate['Teammate']);
                    }
                }
            }
            $oldTeammate['Teammate']['status'] = 'Deleted';
            $Teammate->save($oldTeammate['Teammate']);
        }
        //If the statuses of the new teammates are non-pending, set the status of the old team to 'Completed'
        unset($newTeammate);
        $teamIsComplete = true;
        foreach ($newTeammates as $newTeammate) {
            if ($newTeammate['Teammate']['status'] == 'Pending') {
                $teamIsComplete = false; 
            }
        }
        if ($teamIsComplete) {
            $teamToMergeInto['Team']['status'] = 'Completed';
        }
        else {
            $teamToMergeInto['Team']['status'] = 'Pending';            
        }
        $Team->save($teamToMergeInto);
        
        //TeamsObject
        $TeamsObject = ClassRegistry::init('TeamsObject');
        $TeamsObject->recursive = -1;
        $teamsObjects = $TeamsObject->find('all', array('conditions'=>array('team_id'=>$teamIDToDelete,'status <>' =>'Deleted' )));
        foreach ($teamsObjects as $teamsObject) {
            //check to see if this one already exists
            $doesTeamObjectAlreadyExist = $TeamsObject->find(
                'first', array('conditions'=>array(
                'team_id'=>$teamIDToMergeInto,
                'model_id'=>$teamsObject['TeamsObject']['model_id'],
                'model'=>$teamsObject['TeamsObject']['model']))
            );
            if ($doesTeamObjectAlreadyExist) {
                $teamsObject['TeamsObject']['status'] = 'Deleted';   
            }
            else { 
                $teamsObject['TeamsObject']['team_id'] = $teamIDToMergeInto;
            }
            $TeamsObject->save($teamsObject);
        }
        
        //Image
        $Image = ClassRegistry::init('Image');
        $Image->recursive = -1;
        $images = $Image->find('all', array('conditions'=>array('model'=>'Team','model_id'=>$teamIDToDelete)));
        foreach ($images as $image) {
            $Image->delete($image['Image']['id']);
        }
        
        //finally, update stats for teams
        $Team->updateStatsForTeam($teamIDToMergeInto, 1);
        return "ok";
        
    }
    /**
     *    CURL replacement for file_get_contents
     * @author Oleg D.
     */
    function file_get_contents_curl($url) 
    {
        $ch = curl_init();
         
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
        curl_setopt($ch, CURLOPT_URL, $url);
         
        $data = curl_exec($ch);
        curl_close($ch);
         
        return $data;
    }  
  
}
