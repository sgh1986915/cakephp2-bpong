<?php

class GroupsController extends AppController
{

    var $name    = 'Groups';
    var $helpers = array('Html', 'Form');
    var $uses = array('Group', 'Status','Accession');
    /**
     * Group model
     * @var $Group
     */
    var $Group;

    /**
     * Show all Groups
     * @author vovich
     */
    function index() 
    {
        $this->Access->checkAccess('UserGroup', 'u');
        $this->Group->recursive = 1;
        $groups = $this->paginate();
        $this->set('groups', $groups);
    }
    /**
     * add new Group
     * @author vovich
     */
    function add() 
    {
        $this->Access->checkAccess('UserGroup', 'c');
        if (!empty($this->request->data)) {
            /* Storing */
            $this->Group->create();

            if ($this->Group->save($this->request->data)) {
                $groupID  = $this->Group->getLastInsertID();
                $this->request->data['Status']['group_id'] = $groupID;
                if (empty($this->request->data['Status']['name'])) {
                    $this->request->data['Status']['name'] = "active";
                }
                if ($this->Group->Status->save($this->request->data['Status'], false)) {
                    $statusID = $this->Group->Status->getLastInsertID();
                    $this->Group->id = $groupID;
                    $this->Group->saveField('defstats_id', $statusID);

                    $acc = $this->Accession->find('all', array('conditions'=>array('Accession.status_id'=>$this->request->data['Status']['statuses'])));                    
                    /*Set access for default status*/                    
                    foreach ($acc as $obj) {
                             unset($obj['Accession']['id']);
                        $obj['Accession']['status_id'] = $statusID;
                             $this->Accession->create();
                             $this->Accession->save($obj);
                    }
                    $this->Access->loadobjToCache();
                }
                $this->redirect('/groups');
            } else {
                return $this->redirect('/groups');
            }
        } else {            
            $this->set("statuses", $this->Status->getStatusesLists());
            
        }

    }
    /**
     * Edit group
     * @author vovich
     * @param int $id
     */
    function edit($id = null) 
    {
        $this->Access->checkAccess('UserGroup', 'u');
        if (!$id && empty($this->request->data)) {
            //$this->Session->setFlash('Invalid Group');
            return $this->redirect('/groups/', null, true);
        }

        if (!empty($this->request->data)) {
            if ($this->Group->save($this->request->data)) {
                return $this->redirect('/groups');
            } else {
                $this->Session->setFlash('The Group could not be saved. Please, try again.', 'flash_error');
            }
        } else {
            $this->request->data = $this->Group->read(null, $id);

        }

    }
    /**
     * Delete statuses and groups
     * @author vovich
     * @param int $id
     */
    function delete($id = null) 
    {
        $this->Access->checkAccess('UserGroup', 'd');
        if (!$id) {
            $this->Session->setFlash('Invalid id for Group', 'flash_error');
            return $this->redirect('/groups');
        }
        if ($this->Group->del($id)) {
            $this->Status->deleteAll(array('group_id' => $id), false, true);
            return $this->redirect('/groups');
        }
    }

}
?>
