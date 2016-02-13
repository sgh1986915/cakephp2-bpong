<?php
class LoggerComponent extends Component
{
    /**
     *  get array of the file names
     * @param $type string of log direcroty, like store
     * @param $year logs year
     * @param $month logs month
     * @author Oleg D.
     */
    function getLogFiles($type, $year, $month) 
    {

        $logFiles = array();
        if(!isset($type)) {
            echo 'Set Type';
            exit;
        }
        if ($handle = @opendir(ROOT . DS . 'app' .DS . 'logs' . DS . $type . DS . $year . DS . $month . DS)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    $logFiles[] = $file;
                }
            }
                @closedir($handle);
        }
        $logFiles = array_reverse($logFiles);

        return $logFiles;
    }
    /**
     * get logs of the log file
     * @param $type string of log direcroty, like store
     * @param $logFile - log files name like 12.27.2009.xml
     * @param $userID
     * @author Oleg D.
     */
    function getLogs($type, $logFile, $userID = null) 
    {
        $logFile.='.xml';
        $date = explode('.', $logFile);
        $year = $date[2];
        $month = $date[0]; ;

        App::import('Vendor', 'XmlParser', array('file' => 'xmlparser.class.php'));
        $XmlParser = new XmlParser();
        $file = file_get_contents(ROOT . DS . 'app' .DS . 'logs' . DS . $type . DS . $year . DS . $month . DS . $logFile);
        $file = str_replace('&', 'and', $file);
        if(!$file) {
            echo 'file error';
            exit;
        }
          $file = '<All>' . $file . '</All>';
          $logsParse =  $XmlParser->xml2array($file);

          $logs = array();
        if(isset($logsParse['All']['Log'])) {
            $logs = $logsParse['All']['Log'];
        }
        if ($userID) {
            $myLogs = array();
            foreach ($logs as $log) {
                if($log['Uid'] == $userID) {
                    $myLogs[] = $log;
                }
            }
        } else {
            $myLogs = $logs;
        }

           $myLogs = array_reverse($myLogs);
           //echo "<pre>";
           //print_r($myLogs);
           return $myLogs;
    }

    /**
     *  write xml log to log file
     *     @param $type - log type : store, signup etc.
     * @param $attributes - log attributes array('userID', 'modelName', 'modelID', 'title', 'description', 'array');
     * @author Oleg D.
     */
    function write($type, $title = '' , $attributes = array()) 
    {

        $logTime = date('m.d.Y H:i:s');
        $year = date('Y');
        $month = date('m');
        $day = date('d');

        $dirName = ROOT . DS . 'app' .DS . 'logs' . DS . $type . DS . $year . DS . $month . DS    ;
        $fileName = $month . '.' . $day . '.' . $year . '.xml';

        // check path
        if(!file_exists($dirName)) {
            if(!file_exists(ROOT . DS . 'app' .DS . 'logs' . DS . $type . DS . $year)) {
                @mkdir(ROOT . DS . 'app' .DS . 'logs' . DS . $type . DS . $year, '0755');
                @chmod(ROOT . DS . 'app' .DS . 'logs' . DS . $type . DS . $year, 0777);
            }
            @mkdir(ROOT . DS . 'app' .DS . 'logs' . DS . $type . DS . $year . DS . $month, '0755');
            @chmod(ROOT . DS . 'app' .DS . 'logs' . DS . $type . DS . $year . DS . $month, 0777);
        }
        // EOF check path

        $modelName = $modelID = $userID = $description = $array = '';
        if (isset($attributes['modelName'])) {
            $modelName = $attributes['modelName'];
        }
        if (isset($attributes['modelID'])) {
            $modelID = htmlspecialchars($attributes['modelID']);
        }
        if (isset($attributes['userID'])) {
            $userID = htmlspecialchars($attributes['userID']);
        }
        if (isset($attributes['description'])) {
            $description = htmlspecialchars($attributes['description']);
        }
        if (isset($attributes['array'])) {
            $array = serialize($attributes['array']);
            $array = str_replace('<', '|', $array);
            $array = str_replace('>', '|', $array);
        }
        $title = htmlspecialchars($title);

        $file = @fopen($dirName . $fileName, "a+");
        $newLog = "<Log><Tm>" . $logTime . "</Tm><Uid>" . $userID  . "</Uid><Mdl>" . $modelName  . "</Mdl><Mdlid>" . $modelID  . "</Mdlid><Tle>" . $title  . "</Tle><Dn>" . $description  . "</Dn><Ary>" . $array  . "</Ary></Log>\n";

        $success = @fwrite($file, $newLog);

        return $success;
    }
    
}






















?>
