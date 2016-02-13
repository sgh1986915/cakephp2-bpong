<?php
class LogsController extends AppController
{

    var $name = 'Logs';
    var $helpers = array('Html', 'Form');

    /**
    *  Show all log files
     *  @author Oleg D.
    */
    function index() 
    {  
         set_time_limit(1000); 
         Configure::write('debug', 2);
        // USING
        //$this->Logger->write('store', 'Testing', array('userID' => 1, 'modelName' => 'Order', 'modelID' => 5, 'description' => 'testing description', 'array' => array('cart' => array('12'=>'14'))));    
        if (!isset($_REQUEST['type'])) {
            $type = 'store';  
        } else {
            $type = $_REQUEST['type'];             
        }
        
        if (!isset($_REQUEST['year'])) {
            $year = date('Y');  
        } else {
            $year = $_REQUEST['year'];             
        }
        
        if (!isset($_REQUEST['month'])) {
            $month = date('m');  
        } else {
            $month = $_REQUEST['month'];             
        }
       
        $logFiles  = $this->Logger->getLogFiles($type, $year, $month);

        $this->set('type', $type);       
        $this->set('year', $year);
        $this->set('month', $month);
        $this->set('logFiles', $logFiles);                    
    }
    /**
     *  show logs of the log file
     *  @author Oleg D.
     */
    function showLogs($type, $logFile) 
    {
         //Configure::write('debug', 2);
         set_time_limit(1000);
        if (!isset($_REQUEST['user_id'])) {
            $userID = null;  
        } else {
            $userID = trim($_REQUEST['user_id']);             
        }
        $logs  = $this->Logger->getLogs($type, $logFile, $userID); 
               
        $this->set('userID', $userID);
        $this->set('logs', $logs);       
        $this->set('type', $type);
        $this->set('logFile', $logFile);        
    }
    function testing() 
    {
        Configure::write('debug', 2);
        //chmod('../logs/store/2010/1', 0777);   
        $this->Logger->write('store', 'Testing', array('userID' => 1, 'modelName' => 'Order', 'modelID' => 5, 'description' => 'testing description', 'array' => array('cart' => array('12'=>'14'))));    
        exit;        
    }
    function show_env() 
    {
        Configure::write('debug', 1);
        echo 'CODE_ENVIRONMENT :' . Configure::read('Sandbox.environment');
        exit;
    }
}
?>
