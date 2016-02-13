<?php

class OrganizationNewsController extends AppController
{

    var $name    = 'OrganizationNews';
    //var $uses = array('User');
    
    /**
     * Show organizations news
     * @author Oleg D.
     */
    function org_list($slug = null) 
    {
        if (empty($slug)) {
            $this->Session->setFlash('There is no Organization with such name.', 'flash_error');
            return $this->redirect('/');
        }

        $organization = $this->OrganizationNews->Organization->find('first', array('conditions' => array('Organization.slug' => $slug, 'Organization.is_deleted' => 0)));
        
        if (empty($organization)) {
            $this->Session->setFlash('There is no Organization with such name.', 'flash_error');
            return $this->redirect('/');
        }        
        
        $this->OrganizationNews->contain(array('Image'));

    
        $this->paginate = array(
         'conditions' => array('OrganizationNews.organization_id' => $organization['Organization']['id'], 'OrganizationNews.is_deleted' => 0), 
        'order' => array('OrganizationNews.id' => 'desc'),
         'contain' => array('Image', 'User')
        );
        $news = $this->paginate('OrganizationNews');
        
        
        $this->pageTitle = $organization['Organization']['name'] . ' :: ' . 'News';        
        $this->set('organizationsMenu',  1);    
        
        if ($this->Access->getAccess('OrganizationsNews', 'c', $this->OrganizationNews->Organization->OrganizationsUser->getManagers($organization['Organization']['id']))) {
            $canAdd = $canEdit = $canDelete = 1;            
        } else {
            $canAdd = $canEdit = $canDelete = 0;            
        }
    
        $this->set(compact('organization', 'news', 'canAdd', 'canEdit', 'canDelete'));        
    }
    
    /**
     * Add news
     * @author Oleg D.
     */
    function add($orgID) 
    {
        $this->Access->checkAccess('OrganizationsNews', 'c', $this->OrganizationNews->Organization->OrganizationsUser->getManagers($orgID));
        if (empty($orgID)) {
            $this->Session->setFlash('Access Error', 'flash_error');
            return $this->redirect('/');
        }
        $organization = $this->OrganizationNews->Organization->find('first', array('conditions' => array('Organization.id' => $orgID, 'Organization.is_deleted' => 0)));
        if (!empty($this->request->data)) {
            $this->OrganizationNews->create();
            $this->request->data['OrganizationNews']['user_id'] = $this->getUserID();
            $this->request->data['OrganizationNews']['organization_id'] = $orgID;
            if ($this->OrganizationNews->save($this->request->data)) {
                $this->Session->setFlash('News has been saved', 'flash_success');                
            }
            return $this->redirect('/o_news/' . $organization['Organization']['slug']);
        }
        $this->set('organizationsMenu',  1);
        $this->set(compact('organization', 'orgID'));
    }
    
    /**
     * Edit news
     * @author Oleg D.
     */
    function edit($id) 
    {
        if (empty($id)) {
            $this->Session->setFlash('Access Error', 'flash_error');
            return $this->redirect('/');
        }
        $this->OrganizationNews->contain('Image');
        $news = $this->OrganizationNews->find('first', array('conditions' => array('OrganizationNews.id' => $id)));
        $organization = $this->OrganizationNews->Organization->find('first', array('conditions' => array('Organization.id' => $news['OrganizationNews']['organization_id'], 'Organization.is_deleted' => 0)));
        
        $this->Access->checkAccess('OrganizationsNews', 'u', $this->OrganizationNews->Organization->OrganizationsUser->getManagers($organization['Organization']['id']));
        if (!empty($this->request->data)) {
            $this->request->data['OrganizationNews']['id'] = $id;
            if ($this->OrganizationNews->save($this->request->data)) {
                $this->Session->setFlash('News has been saved', 'flash_success');                
            }
            return $this->redirect('/o_news/' . $organization['Organization']['slug']);
        } else {
            $this->request->data = $news;
        }
        $this->set('organizationsMenu',  1);
        $this->set(compact('organization', 'id', 'news'));
    }
    
    /**
     * Delete news
     * @author Oleg D.
     */
    function delete($id) 
    {
        if (empty($id)) {
            $this->Session->setFlash('Access Error', 'flash_error');
            return $this->redirect('/');
        }        
        $this->OrganizationNews->contain('Image');
        $news = $this->OrganizationNews->find('first', array('conditions' => array('OrganizationNews.id' => $id)));
        $organization = $this->OrganizationNews->Organization->find('first', array('conditions' => array('Organization.id' => $news['OrganizationNews']['organization_id'], 'Organization.is_deleted' => 0)));
        
        $this->Access->checkAccess('OrganizationsNews', 'u', $this->OrganizationNews->Organization->OrganizationsUser->getManagers($organization['Organization']['id']));    
        
        if ($this->OrganizationNews->delete($id)) {
            $this->Session->setFlash('News has been deleted', 'flash_success');                
        }
        return $this->redirect('/o_news/' . $organization['Organization']['slug']);
    }
}
?>
