<?php

class TestsController extends AppController
{

    var $name    = 'Tests';
    var $helpers = array('Html', 'Form');
    var $uses = array('User');
    //var $components = array('Ups');
    
    
    function index() 
    {        
        Configure::write('debug', 1);
        $MongoConnect = new Mongo();
        $SignupLogs = $MongoConnect->signup_logs;
        
        exit;
        exit(ROOT);    
    }
    
    function test_cache($write = null) 
    {
        Configure::write('debug', 1);
        if (!empty($write)) {
            Cache::write('test_cache', $write);                
        }
        Cache::read('test_cache');
        echo "Cache value: " . Cache::read('test_cache') . "<br/>";    
        exit('Server root: ' . ROOT);            
    }
    function svn_test($prop = null, $prop2 = 0) 
    {
        Configure::write('debug', 1);
        exit('===OK===');
        if ($prop == 'global_check') {
            echo '<br/><b>=========================Test Memcache (example - slider):</b><br/>';
            print_r(Cache::read('slides', 'full_time'));
            echo '<br/><b>=========================Test Session (example - user):</b><br/>';
            print_r($_SESSION['loggedUser']);    
            echo '<br/><b>=========================Test Master/Slave (example - store_offers):</b><br/>';            
            $StoreOffer = ClassRegistry::init("StoreOffer");
            /*
            if (!$prop2) {
            $StoreOffer->create();
            $StoreOffer->save(array('name' => 'Test is OK'));
            $prop2 = $StoreOffer->getLastInsertID();
            echo "<br/><b>Last ID</b>: " . $prop2;
            }
            */
            //sleep(5);
            //$StoreOffer->setDataSource('slave1');
            //unset($_SESSION['database']['master_switched']);
            echo "<br/><b>Saved Record Slave</b>:<br/>";
            Configure::write('Database.use_master', 0);
            print_r($StoreOffer->read(null, 242));
            echo "<br/><b>Saved Record Master</b>:<br/>";        
            Configure::write('Database.use_master', 1);
            print_r($StoreOffer->read(null, 242));            
            //$StoreOffer->delete($id);
            
        }
        exit();    
    }
    function facebook_wall() 
    {
        exit;
        Configure::write('debug', 1);
        if (!$this->Session->check('facebook_session_admin')) {
            $this->Session->write('previous_url', $_SERVER['REQUEST_URI']);
            return $this->redirect('/users/fb_connect/0/admin');
        }
        $facebook_session_admin = $this->Session->read('facebook_session_admin');

        App::import('Vendor', 'facebook');
        $Facebook = new Facebook(array('appId' => FACEBOOK_API_KEY, 'secret' => FACEBOOK_SECRET_KEY, 'cookie'=>true));
        $wallPost = array('access_token' => $facebook_session_admin['access_token'], 'message' => 'new testing for bpong.com');
        $Facebook->api('/' . $facebook_session_admin['uid'] . '/feed/', 'post', $wallPost);
        exit('ok');
    }

    function facebook_event() 
    {
        exit;
        Configure::write('debug', 1);
        if (!$this->Session->check('facebook_session_admin')) {
            $this->Session->write('previous_url', $_SERVER['REQUEST_URI']);
            return $this->redirect('/users/fb_connect/0/admin');
            exit;
        }
        $facebook_session_admin = $this->Session->read('facebook_session_admin');

        App::import('Vendor', 'facebook');
        $Facebook = new Facebook(array('appId' => FACEBOOK_API_KEY, 'secret' => FACEBOOK_SECRET_KEY, 'cookie'=>true));
        $data = array(
        'access_token' => $facebook_session_admin['access_token'],
        'name' => 'Testing Event from bpong.com',
        'description' => 'It is testing Event from bpong.com',
        'location' => 'USA',
        'street' => '',
        'city' => '',
        'page_id' => '95752737260',
        'privacy_type' => 'OPEN', // OPEN, CLOSED, SECRET
        'start_time' => date('Y-m-d H:i:s', time()), // timezone info is stripped
        'end_time' => '2011-12-21 20:00',
        );
        $Facebook->api('/' . $facebook_session_admin['uid'] . '/events/', 'post', $data);
        pr($data);
        exit('ok');
    }

    function test_cloud_files() 
    {
        exit();
        error_reporting(E_ALL);
        ini_set("display_errors", "1");
        Configure::write('debug', 1);
        
        $Image = ClassRegistry::init('Image');
        // Add file 
        // echo $filename = $Image->saveOnCloudHosting('img_albums', TMP_DIR . '4yz50oj0fk_2_ad_merch.gif', '4yz50oj0fk_2_ad_merch.gif');
        
        // Delete file
        echo $Image->deleteFromCloudHosting('img_albums', 'big_2_3rd_not_even_close.jpg');
        
        exit();
    }
    
    function deleteNullDirs($dirs) 
    {
        exit();
        foreach ($dirs as $key => $val) {
            if ($val == '.' || $val == '..' || $val == '...' || $val == '.svn') {
                unset($dirs[$key]);    
            }
        }
        return $dirs;    
    }
    
    function devideDirFiles($dirPath, $allFiles) 
    {
        exit();
        $files = array();
        $dirs = array();
        foreach ($allFiles as $fileName) {
            if (is_dir($dirPath . DS . $fileName)) {
                $dirs[$fileName] = $fileName;            
            } else {
                $files[$fileName] = $fileName;                
            }            
        }

        return array('files' => $files, 'dirs' => $dirs);
    }
    
    
    function migrate_albums() 
    {
        exit();
        Configure::write('debug', 1);
        ini_set('mysql.connect_timeout', '300');
        set_time_limit(300);
        
        $rootDir = WWW_ROOT . 'img' . DS . 'albums';
        $migrationDir = WWW_ROOT . 'img' . DS . 'migration_dir'; // CREATE IT !!!!!
        
        if (!is_dir($migrationDir)) {
            exit('Create migration DIR!');    
        }
        
        $rootDirs = $this->deleteNullDirs(scandir($rootDir));
        if (empty($rootDirs)) {
            exit("Empty Dir!!!");
        }

        foreach ($rootDirs as $dirName) {
            
            $dirPath = $rootDir . DS . $dirName;
            $allFiles = $this->devideDirFiles($dirPath, $this->deleteNullDirs(scandir($dirPath)));
            
            if (!empty($allFiles['files'])) {                
                foreach ($allFiles['files'] as $copyFileName) {                    
                    // copy big file!
                    if (is_file($dirPath . DS . $copyFileName)) {
                        copy($dirPath . DS . $copyFileName, $migrationDir . DS . $copyFileName);
                    }
                    
                    foreach ($allFiles['dirs'] as $copyDir) {                        
                        // copy version file!					
                        if (is_file($dirPath . DS . $copyDir . DS . $copyFileName)) {
                            copy($dirPath . DS . $copyDir . DS . $copyFileName, $migrationDir . DS . $copyDir . '_' . $copyFileName);
                        }
                    }        
                }
            }                
        }
        
        exit("<br/>==OK==<br/>");
    }
    
    function migrate_models() 
    {
        exit();
        Configure::write('debug', 1);
        ini_set('mysql.connect_timeout', '300');
        set_time_limit(300);
        
        $rootDir = WWW_ROOT . 'img' . DS . 'Team';
        $migrationDir = WWW_ROOT . 'img' . DS . 'migration_dir'; // CREATE IT !!!!!
        
        if (!is_dir($migrationDir)) {
            exit('Create migration DIR!');    
        }
        
        //$dirPath = $rootDir . DS . $dirName;
        $allFiles = $this->devideDirFiles($rootDir, $this->deleteNullDirs(scandir($rootDir)));
        if (!empty($allFiles['files'])) {                
            foreach ($allFiles['files'] as $copyFileName) {                    
                // copy big file!
                if (is_file($rootDir . DS . $copyFileName)) {
                    copy($rootDir . DS . $copyFileName, $migrationDir . DS . $copyFileName);
                }    
            }
        }
        if (!empty($allFiles['dirs'])) {                
            foreach ($allFiles['dirs'] as $dirName) {
                $allDirFiles = $this->devideDirFiles($rootDir . DS . $dirName, $this->deleteNullDirs(scandir($rootDir . DS . $dirName)));
                foreach ($allDirFiles['files'] as $copyFileName) {
                    // copy version file!
                    if (is_file($rootDir  . DS . $dirName . DS . $copyFileName)) {
                        copy($rootDir  . DS . $dirName . DS . $copyFileName, $migrationDir . DS . $dirName . '_' . $copyFileName);
                    }                        
                }                    
            }
        }        
        

        
        
        exit("<br/>==OK==<br/>");
    }
    function copy_migrations_to_hosting() 
    {
        exit();
        Configure::write('debug', 1);
        ini_set('mysql.connect_timeout', '300');
        set_time_limit(300);
        
        // Comfiguration !!!		
        $migrationDir = WWW_ROOT . 'img' . DS . 'migration_dir';
        $container = 'img_models';
        $buferCount = 100;
        // EOF migration !!!
        
        $Image = ClassRegistry::init('Image');        
        $migrationFiles = $this->deleteNullDirs(scandir($migrationDir));
        if (empty($migrationFiles)) {
            exit("I've Finished!!!!");
        } 
        
        $i=0;
        foreach ($migrationFiles as $fileName) {
            if ($i > $buferCount) {
                return $this->redirect('/tests/copy_migrations_to_hosting');
            }
            $Image->saveOnCloudHosting($container, $migrationDir . DS . $fileName, $fileName);
            $i++;    
        }       
        
        exit("====OK====");
                       
    }
    
    function session_gc() 
    {
        //$this->Session->__gc();
        //echo "ok";
        //exit;
    }
    
    function test_mail($email) 
    {
        Configure::write('debug', 1);
        $this->sendMailMessage('TestEmail', array('{USER_EMAIL}' => $email), $email);    
        exit('sent');
    }    

}
?>
