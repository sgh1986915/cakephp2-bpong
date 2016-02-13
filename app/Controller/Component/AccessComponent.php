<?php
class AccessComponent extends Component
{

    var $components = array('Session','Cookie');

    var $controller = true;

    var $model      = false;
    var $obj      = false;
    /**
     * @var SessionComponent
     */
    var $Session;

    /**
     * Startup method
     * Calls the method process if and only if the method _continue returns true
     * @author vovich
     * @access public
     * @param Controller $controller
     * @return void result not used
    */
    function startup(Controller $controller) 
    {
        $this->controller = $controller;
        $this->Session = $controller->Session;
        $this->__initModel();
    }

    /**
     * Check access for an object
     * @author vovich
     * @param string $objName
     * @param string $accessType
     * @param null   $authorID
     * @return true or false
     */
    function getAccess($objName = "", $accessType = "r", $authorID=null) 
    {

        $objectID    = 0;
        $isAccess = true;
        /*Getting group*/
        if (!empty($this->Session->check('userGroup')) && $this->Session->check('userGroup')) {
            $userGroup = $this->Session->read('userGroup');
        } else {
            $userGroup = null;
        }
        /*Getting User ID*/
        if ($this->Session->check('loggedUser')) {
            $userSession = $this->Session->read('loggedUser');
            $userID = $userSession['id'];
            if (!$userID || $userID < 1) {
                $userID = VISITOR_USER;
            }
        } else {
            $userID = VISITOR_USER;
        }

        /*Check if Object is exist
        * 0 - deny;
        * 1 - allow only for author;
        * 2	- allow for ALL;
        */
        $isAccess = $this->__returnAccess($objName, $accessType);

        if ($isAccess==2) {
            $isAccess = true;
        } else {
            /*Check author id*/
            if ($authorID && is_array($authorID) && in_array($userID, $authorID)) {
                $isAccess = true;
            } elseif($userID==$authorID) {
                  $isAccess = true;
            } else {
                $isAccess = false;
            }
            /*EOF Checking author id*/
        } 
        return $isAccess;
    }

    /**
     * check access and redirect
     * @author vovich
     * @param varchar $objName
     * @param char    $accessType
     * @return redirect
     */
    function checkAccess($objName = "",$accessType = "r",$authorID = null,$server = MAIN_SERVER,$url = "/login") 
    {

        $url = MAIN_SERVER.$url;

        if (!$this->getAccess($objName, $accessType, $authorID)) {
            $this->Session->write('URL', $server.$_SERVER['REQUEST_URI']);
             $this->controller->redirect($url);
        }
    }

    /**
     * Create new object in the DB objects and GRAND all
     * @author vovich
     * @param int $objName
     * @return object ID
     */
    function __createNewObject($objName="")
    {
        $this->obj->cacheQueries = false;
        $objectID = $this->obj->field('id', array('name'=>$objName));

        if(empty($objectID)) {
            $tmp['name'] = $objName;
            $tmp['category_id'] = 1;
            $this->obj->create();
            $this->obj->save($tmp);
            $objID = $this->obj->getLastInsertID();

            if ($objID) {
                $sql = "SELECT id, defstats_id FROM groups";
                $groups = $this->model->query($sql);

                foreach ($groups as $group) {
                         $sql = "INSERT INTO access (object_id,status_id) VALUES(".$objID.",".$group['groups']['defstats_id'].")";
                         $this->model->query($sql);
                }

            }
            //Create ne cache
            $this->loadobjToCache();

            return $objID;
        }

    }
    /**
      * Create session loggedUser
      * and Update User profile
      * @author vovich
      * @param int  $userID
      * @param bool $rememberMe
      */
    function loggining($userID = null,$rememberMe = false)
    {
        if (!$this->model) {
            $this->__initModel();
        }

        $loggedSessionFields = array('User.id','firstname','middlename','lastname','lgn','email','last_logged','pre_last_logged','Timezone.value', 'avatar');
        if ($userID) {
            $conditions = array('User.id' => $userID);
            $userInfo   = $this->model->find('first',array('conditions' => $conditions, 'fields' => $loggedSessionFields));
            if (empty($userInfo)) {
                $this->log('error occured while Loggining: empty userInfo');
                return false;
            } else {

                $promocodes = $this->model->query("SELECT count(id) as promocodes FROM promocodes WHERE assign_user_id=".$userID);
                if(!isset($promocodes[0][0]['promocodes']) || !$promocodes[0][0]['promocodes']) {
                    $promocodes[0][0]['promocodes'] = 0;
                }
                $userInfo['User']['promocodes'] = $promocodes[0][0]['promocodes'];
                $userInfo['User']['timezone'] = $userInfo['Timezone']['value'];
                $this->Session->delete('loggedUser');
                $this->Session->write('loggedUser', $userInfo['User']);
                //Create statuses
                $userStatuses = $this->model->query("SELECT user_id, status_id FROM users_statuses WHERE user_id=".$userID);
                $this->Session->write('loggedUserStatuses', $userStatuses);

                /*Update User profile*/
                $userInfo['User']['pre_last_logged'] = $userInfo['User']['last_logged'] ;
                $userInfo['User']['last_logged']     = date('Y-m-d H:i:s');
                $userInfo['User']['last_logged_ip']  = $_SERVER['REMOTE_ADDR'];
                $this->model->save($userInfo, false, null, false);

                if ($rememberMe ==1) {
                    $this->Cookie->write('loggedUser', serialize($userInfo['User']), true, '65 Days');
                }

                return true;
            }

        }else{
            $this->log("error occured while Loggining: empty userID");
            return false;
        }

    }


    /**
      * return Access
      * @author vovich
      * @param varchar $objName
      * @param char    $accessType
      */
    function __returnAccess($objName = "",$accessType = "r")
    {

        if (!$this->model) {
            $this->__initModel();
        }

        /*Getting User ID*/
        if ($this->Session->check('loggedUser')) {
            $userSession = $this->Session->read('loggedUser');
            $userID = $userSession['id'];
        } else {
            $userID = VISITOR_USER;
        }

        /*Getting user statuses*/
        if ($this->Session->check('loggedUserStatuses')) {
            $userStatuses = $this->Session->read('loggedUserStatuses');
        } else {
            $userStatuses = $this->model->query("SELECT user_id, status_id FROM users_statuses WHERE user_id=".$userID);
            $this->Session->write('loggedUserStatuses', $userStatuses);
        }

        /*Check if Object is exist
        * 0 - deny;
        * 1 - allow only for author;
        * 2	- allow for ALL;
        */

        $objectID = $this->getObjIdByName($objName);

        if (!$objectID) {
            /*Create new object*/
            $objectID =    $this->__createNewObject($objName);
        }
        //Permissions
        $permissions = Cache::read('permissions');
        if (empty($permissions)) {
            $permissions = $this->loadobjToCache('permissions');
        }
        //Groups
        $groups = Cache::read('groups');
        if (empty($groups)) {
            $groups = $this->loadobjToCache('groups');
        }
        //Statuses
        $statuses = Cache::read('statuses');
        if (empty($statuses)) {
            $statuses = $this->loadobjToCache('statuses');
        }


        $isAccess = 0;

        foreach ($userStatuses as $userStat) {
            if (isset($permissions[$userStat['users_statuses']['status_id']][$objectID][$accessType])) {

                if (intval($permissions[$userStat['users_statuses']['status_id']][$objectID][$accessType])>$isAccess) {
                    $isAccess = intval($permissions[$userStat['users_statuses']['status_id']][$objectID][$accessType]);
                }

            } else {

                /*Getting group ID*/
                $def_status_id = $groups[$statuses[$userStat['users_statuses']['status_id']]];

                if (!isset($def_status_id)) {
                    $isAccess = 0;
                } else {
                    if (intval($permissions[$def_status_id][$objectID][$accessType])>$isAccess) {
                        $isAccess = intval($permissions[$def_status_id][$objectID][$accessType]);
                    }
                }

            }

        }/*EOF foreach*/

        /*	$sql = "SELECT max(ifnull(access.$accessType, def_access.$accessType)) as access
        FROM users_statuses AS users_statuses
        LEFT JOIN access as access ON users_statuses.status_id = access.status_id AND access.object_id = $objectID
        LEFT JOIN statuses as statuses ON users_statuses.status_id = statuses.id
        LEFT JOIN groups as groups ON statuses.group_id = groups.id
        LEFT JOIN access as def_access ON groups.defstats_id = def_access.status_id AND def_access.object_id = $objectID AND access.object_id is null
        WHERE users_statuses.user_id = ".$userID;

        $result = $this->model->query($sql);

        $isAccess = $result[0][0]['access'];*/

        return $isAccess;

    }

    /**
      * return smart  Access  'ALL','DENY','OWNER'
      * @author vovich
      * @param varchar $objName
      * @param char    $accessType
      */
    function returnAccess($objName = "",$accessType = "r")
    {
        $access = "DENY";
        $isAccess = $this->__returnAccess($objName, $accessType);
        if($isAccess==2) {
            $access = "ALL";
        }elseif($isAccess==1) {
            $access = "OWNER";
        }else{
            $access = "DENY";
        }

        return $access;
    }
    /**
      * return logged user ID
      * @author vovich
      */
    function getLoggedUserID()
    {
        if ($this->Session->check('loggedUser')) {
            $userSession = $this->Session->read('loggedUser');
            $userID = $userSession['id'];
        } else {
            $userID = VISITOR_USER;
        }
        return $userID;
    }
    /**
      * return logged user Information
      * @author vovich
      */
    function getLoggedUserInfo()
    {
        if ($this->Session->check('loggedUser')) {
            $userSession = $this->Session->read('loggedUser');
        } else {
            $userSession =null;
        }

        return $userSession;
    }

    /**
     *  Init model
     * @author vovich
     */
    function __initModel()
    {
        App::import('Model', 'User');
        App::import('Model', 'Obj');
        $this->model = ClassRegistry::init('User');
        $this->obj = ClassRegistry::init('Obj');
    }

    /**
      * Create hash objects and groups
      * @author vovich
      */
    function loadobjToCache($returnVar = null) 
    {

        if (!$this->obj) {
            $this->__initModel();
        }
        //Objects
        $obj = $this->obj->find('all', array('fields'=>array('name','id')));
        Cache::delete('obj');
        Cache::write('obj', $obj);

        //Groups
        $this->model->cacheQueries = false;
        $sql = "SELECT id,defstats_id FROM groups";
        $_groups = $this->model->query($sql);
        foreach ($_groups as $_g) {
            $groups[$_g['groups']['id']] =$_g['groups']['defstats_id'];
        }
        unset($_groups);
        Cache::delete('groups');
        Cache::write('groups', $groups);

        //Access
        $this->model->cacheQueries = false;
        $sql = "SELECT * FROM access ORDER BY status_id,object_id";
        $_permissions = $this->model->query($sql);
        $permissions = array();

        foreach ($_permissions as $_p) {
            $permissions[$_p['access']['status_id']][$_p['access']['object_id']]['c'] = $_p['access']['c'];
            $permissions[$_p['access']['status_id']][$_p['access']['object_id']]['u'] = $_p['access']['u'];
            $permissions[$_p['access']['status_id']][$_p['access']['object_id']]['r']  = $_p['access']['r'];
            $permissions[$_p['access']['status_id']][$_p['access']['object_id']]['d'] = $_p['access']['d'];
            $permissions[$_p['access']['status_id']][$_p['access']['object_id']]['l']  = $_p['access']['l'];
        }
        unset($_permissions);
        Cache::delete('permissions');
        Cache::write('permissions', $permissions);

        //Statuses
        $sql = "SELECT id, group_id FROM statuses";
        $_statuses = $this->model->query($sql);
        foreach ($_statuses as $_s) {
            $statuses[$_s['statuses']['id']] =$_s['statuses']['group_id'];
        }
        unset($_statuses);
        Cache::delete('statuses');
        Cache::write('statuses', $statuses);

        if (isset(${$returnVar}) && !empty(${$returnVar})) {
            return ${$returnVar};            
        }
    }

    /**
      * Get object by name (from cache)
      * @author vovich
      * @param string $name
      */
    function getObjIdByName($name = null) 
    {

        $obj = Cache::read('obj');
        if (empty($obj)) {
            $obj = $this->loadobjToCache('obj');
        }

        foreach ($obj as $o){
            if ($o['Obj']['name'] == $name) {
                return $o['Obj']['id']; 
            }
        }

        return false;
    }
    /**
* return all permission table from the cache
* @author vovich
* @return array $permissions['status_id'][$objectID][$accessType]
*/
    function getAllPermissons()
    {
        $permissions = Cache::read('permissions');
        if (empty($permissions)) {
            $permissions = $this->loadobjToCache('permissions');
        }

        return $permissions;
    }



}
?>
