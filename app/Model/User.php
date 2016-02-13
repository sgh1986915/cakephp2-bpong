<?php
class User extends AppModel
{

    var $name         = 'User';
    var $actsAs       = array('Containable','ExtendAssociations');
    var $cacheQueries = false;
    var $usePaginate  = "";
    var $order        = 'User.id';
    var $compare      = 'AND';

    var $validate = array(
         'lgn' => array(
                 'alphanumeric' => array(
                           'rule'       => array('notEmpty')
                           ,'required'   => true
                          ,'allowEmpty' => false
                          ,'message'    => 'Please enter your Nickname.'
                 )
                           ,'isunique' => array(
                           'rule'       => array('isUnique', 'lgn')
                          ,'message'    => 'Such Nickname already exist.'
                           )
                           ,'maxLength' => array(
                           'rule'       => array('maxLength', 20)
                           ,'message'    => 'Nickname can not be greater then 20 symbols.'
                           )
                           //isunque
          )//login
         ,'email' => array(
                    'email' => array(
                      'rule' => 'email'
                    ,'allowEmpty' => false
                    ,'required'   => true
                    ,'message'    => 'emailAlphaNumeric'
                    )//email rule
                    ,'isunique' => array(
                           'rule'       => array('isUnique', 'email')
                          ,'message'    => 'A User with that email already exist.'
                    )//isunque rule
         )//array
         ,'pwd' => array(
                 'rule' => array('notEmpty')
                ,'allowEmpty' => false
         ,'required'   => true
                ,'message'    => 'password can not be empty'
         )//password

    );//validate





    

    //The Associations below have been created with all possible keys, those that are not needed can be removed
    var $hasAndBelongsToMany = array(
    'Status' => array('className' => 'Status',
                        'joinTable' => '',
                        'with' => "UsersStatus",
                        'foreignKey' => 'user_id',
                        'associationForeignKey' => 'status_id',
                        'unique' => true,
                        'conditions' => '',
                        'fields' => '',
                        'order' => '',
                        'limit' => '',
                        'offset' => '',
                        'finderQuery' => '',
                        'deleteQuery' => '',
                        'insertQuery' => ''
    ),
    'Team' => array('className' => 'Team',
                        'joinTable' => '',
                        'with' => "Teammate",
                        'foreignKey' => 'user_id',
                        'associationForeignKey' => 'team_id',
                        'unique' => true,
                        'conditions' => array('Teammate.status'=>array('Creator','Accepted','Pending')),
                        'fields' => '',
                        'order' => '',
                        'limit' => '',
                        'offset' => '',
                        'finderQuery' => '',
                        'deleteQuery' => '',
                        'insertQuery' => ''
    ),
    'Mailinglist' => array('className' => 'Mailinglist',
            'joinTable' => 'mailinglist_user',
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'mailinglist_id',
            'unique' => true,
          )

    );
    /*	var $hasOne = array(
    'Ranking'=>array('className'=>'Ranking',
						'foreignKey'=>'model_id',
						'conditions'=>array('Ranking.model'=>'User'),
						'dependent'=>true));
    */
    var $hasMany = array(
    'Address' => array('className' => 'Address',
                                'foreignKey' => 'model_id',
                                'dependent' => true,
                                'conditions' => array('Address.model'=>'User','Address.is_deleted'=>0),
                                'fields' => '',
                                'order' => ''
    ),
    'Phone' => array('className' => 'Phone',
                                'foreignKey' => 'model_id',
                                'dependent' => true,
                                'conditions' => array('Phone.model'=>'User','Phone.is_deleted'=>0),
                                'fields' => '',
                                'order' => ''
    ),
            'UsersAffil'=>array('className'=>'UsersAffil',
                            'foreignKey'=>'user_id',
                            'conditions'=>array('UsersAffil.is_deleted'=>0)),
            'Teammate'=>array('className'=>'Teammate',
                                'foreignKey'=>'user_id',
                                'conditions'=>array('Teammate.status <>'=>'Deleted')),
            'VenuesUser'=>array('className'=>'VenuesUser',
                                'foreignKey'=>'user_id')
    );
    
    var $belongsTo = array(
    'Timezone' => array('className' => 'Timezone',
                                'foreignKey' => 'timezone_id',
                                'dependent' => true,
                                'conditions' => '',
                                'fields' => '',
                                'order' => ''
    )
    );



    /**
 * Send to the MC
 */
    function afterSave($created, $options = array())
    {

        if (!isset($this->data['User']['id'])) {
            $this->data['User']['id'] = $this->getLastInsertID();
        }

        if (isset($this->data['User']['subscribed']) && isset($this->data['User']['id'])) {

              App::import(
                  'Vendor', 'MCAPI', array(
                                                    'file'   => 'MCAPI.class.php'
                                                )
              );

               $MCapi = new MCAPI(MCLOGIN, MCPASSWORD);

              if (!isset($this->data['User']['old_subscribed']) || $this->data['User']['subscribed'] > $this->data['User']['old_subscribed']) {
                  /*adding to the list*/
                  $this->Mailinglist->subscribe($this->data['User']['id'], LISTID, $this->data['User']['email'], $this->data['User']);

              } elseif ($this->data['User']['subscribed'] < $this->data['User']['old_subscribed']) {
                    /*Remove from the list*/
                    $this->Mailinglist->unSubscribe($this->data['User']['id'], LISTID, $this->data['User']['email']);
                     //pr($MCapi->errorCode);
              }

        }



        return true;
    }
    /**
 *
 * @param $model
 * @param $model_id
 * @return unknown_type
 */
    function notPaidUsers( $model = null, $model_id = null ) 
    {
        if($model === null && $model_id === null) {
            return false;
        }

        $sql ="
			SELECT u.email, u.lgn, s.for_team
			FROM users u
			INNER JOIN signups_users su ON su.user_id = u.id
			INNER JOIN signups s ON s.id = su.signup_id 
			WHERE model = '$model' AND model_id = $model_id AND status = 'partly paid'
		";
        return $this->query($sql);
    }

    /**
     * check access to albums for this model id
     * @author Oleg D.
     */
    function getAlbumUploadAccess($userID, $modelID, $Access, $getAll) 
    {
        if ($userID == $modelID) {
            return true;
        } else {
            return false;
        }
    }


    /**
    * Overridden paginateCount method
    */
    function paginateCount($conditions = null, $recursive = 0, $extra = array()) 
    {
             $cnt = 0;

        if ($this->usePaginate == "custom") {
            if (!empty($conditions['Address.city']) || !empty($conditions['Address.provincestate_id'])) {
                $addressCond = "";
                if (!empty($conditions['Address.city'])) {
                    $addressCond .= ' AND city LIKE "'.$conditions['Address.city'].'"';
                    unset($conditions['Address.city']);
                }
                if (!empty($conditions['Address.provincestate_id'])) {
                    $addressCond .= ' AND provincestate_id = '.$conditions['Address.provincestate_id'];
                    unset($conditions['Address.provincestate_id']);
                }
                $cond = $this->implodeCond($conditions, $this->compare);
                if (strtolower($this->compare) == 'and' || (empty($cond) && empty($addressCond))) {
                    $cond = ' 1 = 1'. $cond;
                } else {
                    $cond = ' 1 = 2'. $cond;
                }

                $sql =  'SELECT COUNT(*) as cnt FROM users AS User
                  		  WHERE '.$cond.' '.$this->compare.' User.id IN (SELECT DISTINCT  model_id FROM addresses WHERE is_deleted <>1 AND model="User" AND label="Home" '.$addressCond.')';
            } else { /*WITHOUT address*/

                $cond = $this->implodeCond($conditions, $this->compare);
                if (strtolower($this->compare) == 'and' || empty($cond)) {
                    $cond = ' 1 = 1'. $cond;
                } else {
                    $cond = ' 1 = 2'. $cond;
                  }
                $sql  =  'SELECT COUNT(*) as cnt FROM users AS User
                  		  WHERE '.$cond;
          }

            $res = $this->query($sql);
            if (empty($res)) {
                  $cnt = 0;
            } else {
                  $cnt = $res[0][0]['cnt'];
            }

        } else { /*NOT custom*/
            if (empty($conditions) || empty($conditions['I18n__title.content LIKE'])) {
                return $this->find('count', array('conditions' => $conditions));

            } else {
                  return $this->find('count', array('conditions' => $conditions));
            }
        }
             return $cnt;
    }

    /**
* Overridden paginate method
*/
    function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) 
    {

             $rows = array();
        if (!empty($extra['contain'])) {
                   $contain = $extra['contain'];
        }

        if ($this->usePaginate == "custom") {
            $offset = ($page > 0 ? $page - 1 : $page ) * $limit;

            if (empty($order)) {
                $orders = $this->order;
            } else {
                $orders = "";
                foreach ($order AS $key=>$val) {
                    $orders .= $key." ".$val;
                }
            }

             $extra = "
                       ORDER BY {$orders}
                       LIMIT {$limit} OFFSET {$offset}
                   ";

            if (!empty($conditions['Address.city']) || !empty($conditions['Address.provincestate_id'])) {
                $addressCond = "";
                if (!empty($conditions['Address.city'])) {
                    $addressCond .= ' AND city LIKE "'.$conditions['Address.city'].'"';
                    unset($conditions['Address.city']);
               }
                if (!empty($conditions['Address.provincestate_id'])) {
                    $addressCond .= ' AND provincestate_id = '.$conditions['Address.provincestate_id'];
                    unset($conditions['Address.provincestate_id']);
                }
                $cond = $this->implodeCond($conditions, $this->compare);
                if (strtolower($this->compare) == 'and' || (empty($cond) && empty($addressCond))) {
                    $cond = ' 1 = 1'. $cond;
                } else {
                    $cond = ' 1 = 2'. $cond;
                }

                $sql  = 'SELECT User.* FROM users AS User
                      		  WHERE '.$cond.' '.$this->compare.' User.id IN (SELECT DISTINCT  model_id FROM addresses WHERE is_deleted <>1 AND model="User" AND label="Home" '.$addressCond.') '.$extra;

            } else { /*WITHOUT ADDRESS*/
                $cond = $this->implodeCond($conditions, $this->compare);
                if (strtolower($this->compare) == 'and' || empty($cond)) {
                    $cond = ' 1 = 1'. $cond;
                } else {
                    $cond = ' 1 = 2'. $cond;
                }
                $sql =  'SELECT User.* FROM users AS User WHERE '.$cond.$extra;
            }

            $rows = $this->query($sql);
            foreach ($rows as $key => $row) {
                 $sql = "SELECT `Status`.*,`Group`.*
                         		 FROM statuses AS `Status` INNER JOIN  users_statuses AS US
                         		 ON US.status_id = Status.id AND  US.user_id = ".$row['User']['id']."
                         		 INNER JOIN groups AS `Group` ON Status.group_id = Group.id";
                 $status = $this->query($sql);
                 $rows[$key]['Status'] = $status;
            }


        } else {  /*NOT CUSTOM PAGING*/
             $rows =  $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'contain'));
        }
                return $rows;
    }
    /**
 * Gets admins emails
 * @author vovich
 * @return unknown_type
 */
    function getAdmins() 
    {
        if (LIVE_WEBSITE) {
            $result = $this->query(" SELECT User.email FROM users AS User INNER JOIN users_statuses AS UsersStatus ON User.id = UsersStatus.user_id AND UsersStatus.status_id = 3 ");
        } else {
            $result = $this->query(" SELECT User.email FROM users AS User INNER JOIN users_statuses AS UsersStatus ON User.id = UsersStatus.user_id AND UsersStatus.status_id = 3 WHERE User.email LIKE '%shakuro%'");
        }
        foreach ($result as $email) {
            $emails[] = $email['User']['email'];
        }
        return $emails;
    }
    function correctAvatar($filename, $size) 
    {
        $correct = 1;
        $possible_formats=array(
        'jpg'=>'',
        'jpeg'=>'',
        'gif'=>'',
        'png'=>''
        );
        $explodes=explode('.', $filename);
        $ponts=(count($explodes)-1);
        $thisFormat=$explodes[$ponts];
        $thisFormat=strtolower($thisFormat);
        if(!isset($possible_formats[$thisFormat])) {
            $correct = 0;
        }else{
            $correct = 1;
            if ($size > 1000000) {
                $correct = 0;
            }
        }
        return $correct;
    }
    /**
    * Upload avatar for user
    * @author Oleg D.
    */
    function uploadAvatar($pict, $userID, $oldFile = null)
    {
        ini_set('mysql.connect_timeout', '300');
        set_time_limit(300);
        App::import('Vendor', 'example', array('file' => 'class.upload.php'));
        $Image = ClassRegistry::init('Image');

        $foo = new Upload($pict);
        $saved = 0;

        if ($foo->uploaded) {
            $filename = $userID . '_' . $Image->generateRandomFilename($pict['name']);
            $filenameTemporary = $Image->generateRandomFilename();
            // Size: default
            $foo->file_new_name_body = $filenameTemporary;
            $foo->image_resize          = false;
            $foo->Process(TMP_DIR);       
              
            // Size: 40x40
            $size = 40;
            $foo->file_new_name_body = $filenameTemporary;
            $foo->image_resize          = true;
            $foo->image_ratio_crop      = 'C';
            $foo->image_y               = $size;
            $foo->image_x               = $size;

            $foo->Process(TMP_DIR);
            $Image->saveOnCloudHosting('img_avatars', TMP_DIR . $foo->file_dst_name, $size . '_' . $filename);

            if ($oldFile) {
                $Image->deleteFromCloudHosting('img_avatars', $size . '_' . $oldFile);
            }

            // Size: 185x185
            $size = 185;
            $foo->file_new_name_body = $filenameTemporary;
            $foo->image_resize          = true;
            $foo->image_ratio_crop      = 'C';
            $foo->image_y               = $size;
            $foo->image_x               = $size;

            if ($oldFile) {
                $Image->deleteFromCloudHosting('img_avatars', $size . '_' . $oldFile);
            }

            $foo->Process(TMP_DIR);
            $Image->saveOnCloudHosting('img_avatars', TMP_DIR . $foo->file_dst_name, $size . '_' . $filename);

            // Size: 24x24
            $size = 24;
            $foo->file_new_name_body = $filenameTemporary;
            $foo->image_resize          = true;
            $foo->image_ratio_crop      = 'C';
            $foo->image_y               = $size;
            $foo->image_x               = $size;
            
            if ($oldFile) {
                $Image->deleteFromCloudHosting('img_avatars', $size . '_' . $oldFile);
            }  
                   
            $foo->Process(TMP_DIR);
            if ($foo->processed) {
                $saved = 1;
            }            
            $Image->saveOnCloudHosting('img_avatars', TMP_DIR . $foo->file_dst_name, $size . '_' . $filename);
        }

        return $filename;
    }
    /**
    * Delete avatar of user
    * @author Oleg D.
    */
    function deleteAvatar($userID) 
    {
        ini_set('mysql.connect_timeout', '300');
        set_time_limit(300);
        
        $oldFile = $this->field('avatar', array('User.id' => $userID));
        if ($oldFile) {
            $Image = ClassRegistry::init('Image');
            
            $Image->deleteFromCloudHosting('img_avatars', '185' . '_' . $oldFile);
            $Image->deleteFromCloudHosting('img_avatars', '40' . '_' . $oldFile);
            $Image->deleteFromCloudHosting('img_avatars', '24' . '_' . $oldFile);
            
            $user['User']['id'] = $userID;
            $user['User']['avatar'] = '';
            $this->validate = array();
            $this->save($user);
        }
    }

    /**
     * Get Facebook information
     * @author Oleg D.
     */
    function getFacebookInfo($facebookID) 
    {
        for ($i=0; $i<5; $i++) {
            $info = $this->file_get_contents_curl('http://graph.facebook.com/' . $facebookID);
            $info = json_decode($info, true);
            if (!empty($info['id'])) {
                break;
            }
        }
        return $info;
    }

    /**
     * Get Twitter information
     * @author Oleg D.
     */
    function getTwitterInfo($twitterID) 
    {
        App::import('Vendor', 'XmlParser', array('file' => 'xmlparser.class.php'));
        $XmlParser = new XmlParser();

        for ($i=0; $i<5; $i++) {
            $info = $this->file_get_contents_curl('http://api.twitter.com/1/users/show.xml?user_id=' . $twitterID);
            $info = $XmlParser->xml2array($info);
            if (!empty($info['user']['id'])) {
                break;
            }
        }
        if (!empty ($info['user'])) {
            $info = $info['user'];
        } else {
            return false;
        }
        return $info;
    }
          // author duncan@bpong.com
    function generate_and_save_new_qr($ctext, $email,$mysqldate = null) 
          {
        App::import('Vendor', 'phpqrcode', array('file' => 'phpqrcode'.DS.'qrlib.php'));

          
        // these are constants and they both need to be identical on the client side.
        //  $BPONG_QR_KEY = "85428061B163CE1F"; Now defined in bootstrap
        //  $BPONG_QR_IV  = "1A5F1350C3B4D6A4"; Now defined in bootstrap    

         $iv = AES_ENCRYPT_IV;
         $cleartext = $ctext;
           
         $key128 = AES_ENCRYPT_KEY;
         $cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');

        if (mcrypt_generic_init($cipher, $key128, $iv) != -1) {
            // PHP pads with NULL bytes if $cleartext is not a multiple of the block size..
            $cipherText = mcrypt_generic($cipher, $cleartext);
            mcrypt_generic_deinit($cipher);
            mcrypt_module_close($cipher);
            $hexCipherText = bin2hex($cipherText);
            
            //             // generate qr code
    
            $filename = $this->my_genRandomString(12).".png";   
            $filepathandname = TMP.$filename;
            //$filepathandname = ROOT. "/app/webroot/img/".$filename;
            $qrapp_bindir = ROOT . "/app/vendors/qrforall/bin/"; 
            $errorCorrectionLevel = "L";
            $matrixPointSize = 5;
            
            QRcode::png($hexCipherText, $filepathandname, $errorCorrectionLevel, $matrixPointSize, 2);
            $Image = ClassRegistry::init('Image');
            $filename = $Image->saveOnCloudHosting('old-files-img_qrcodes', $filepathandname, $filename);
                
              
              
            $sql = "UPDATE users SET qr_string='".$hexCipherText."', qr_image='".$filename.
                 "', qr_generated='".$mysqldate."' WHERE email='".$email."'";
            //return $sql;
            return $this->query($sql);
            
            
            // DISABLING CRYPTO UNTIL WE ENABLE MCRYPT
            //            QRcode::png($ctext, $filepathandname, $errorCorrectionLevel, $matrixPointSize, 2);
            //            $sql = "UPDATE users SET qr_string='".$ctext."', qr_image='".$filename."' WHERE email='".$email."'";
           
           
            
            
            
        }

    }
           
    function my_genRandomString($length = 10) 
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $string = '';

        for ($p = 0; $p < $length; $p++) {
            $string .= $characters[mt_rand(0, strlen($characters) -1)];
        }
        return $string;
    }
    function setMobileStatus($id,$status) 
    {
        if (!($status == 'DND' || $status == 'Away' || $status == 'Not Connected' || $status == 'Active')) {
            return 'Invalid Status'; 
        }

        $this->validate = array();
        return $this->save(array('id'=>$id,'mobilestatus'=>$status));
    }
    function setUserRating($userID,$rating) 
    {
        $this->recursive = -1;
        $user = $this->find(
            'first', array(
            'conditions'=>array('id'=>$userID),
            'contain'=>array('Team'))
        );
        if (!$user) {
            return false;
        }
        $user['User']['rating'] = $rating;
        if (!$this->save($user)) {
            return false; 
        }
        foreach ($user['Team'] as $team) {
            $this->Team->updateTeamRating($team['id']);
        }
        return true;
    }
    function updateTeamRatings($userID) 
    {
        $this->recursive = -1;
        $user = $this->find(
            'first', array(
            'conditions'=>array('id'=>$userID),
            'contain'=>array('Team'))
        );
        if (!$user) {
            return false;
        }
        foreach ($user['Team'] as $team) {
            $this->Team->updateTeamRating($team['id']);
        }
        return true;        
    }
    function updateStatsForUsers($startID, $endID) 
    {
        $ctr = $startID;
        while ($ctr <= $endID) {
            $this->updateStatsForUser($ctr);
            $ctr++;
        }
    }
    function updateStatsForUser($userid) 
    {   
        
        //return 1;
        /*$userWithTeams = $this->find('first',array('conditions'=>array('User.id'=>$userid),
            'contain'=>array('Team'=>array(
                'conditions'=>array(
                    'Teammate.status'=>array('Accepted','Creator','Pending'),
                    'Team.is_deleted'=>0)))));
          */
        $userTeams = $this->Teammate->find(
            'all', array('conditions'=>array(
            'Teammate.status'=>array('Accepted','Creator','Pending'),
            'Teammate.user_id'=>$userid),
            'contain'=>array('Team'))
        );       
        //return $userTeams;
        if (!$userTeams) {
            return false; 
        }
        $results['total_wins'] = 0;
        $results['total_losses'] = 0;
        $results['total_cupdif'] = 0;
        foreach ($userTeams as $userTeam) {
            $results['total_wins'] += $userTeam['Team']['total_wins'];
            $results['total_losses'] += $userTeam['Team']['total_losses']; 
            $results['total_cupdif'] += $userTeam['Team']['total_cupdif']; 
        }
        $results['id'] = $userid;
        //return $this->save($results);
        $this->validate = array();
        return $this->save($results);
    }
    function saveVenuesUserStats($userVenueStats) 
    {
        $this->VenuesUser->recursive = -1;
        $existingVenuesUser = $this->VenuesUser->find(
            'first', array('conditions'=>array(
            'venue_id'=>$userVenueStats['venue_id'],
            'user_id'=>$userVenueStats['user_id']))
        );
        if ($existingVenuesUser) {
            $userVenueStats['id'] = $existingVenuesUser['VenuesUser']['id'];
            $this->VenuesUser->save($userVenueStats);
        } else {
            $this->VenuesUser->create();
            $this->VenuesUser->save($userVenueStats);
        }
    }
}
?>
