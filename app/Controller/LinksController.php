<?php
class LinksController extends AppController
{

    var $name = 'Links';

    /**
     * Add link from submissions page
     * @author Oleg D.
     */
    function submissionsAdd() 
    {
        $userID = $this->getUserID();
        if (!empty($this->request->data['Link'])) {
            $this->request->data['Link']['user_id'] = $userID;
            $this->Link->save($this->request->data['Link']);
            Cache::delete('last_links');
            $this->redirect('/submissions/finish/0/link');
        }
    }

    /**
     * Show All my links
     * @author Oleg D.
     */
    function listMy() 
    {
        $userID = $this->getUserID();
        if (!$userID) {
            exit;
        }
        $paginate['limit'] = 20;
        $paginate['conditions'] = array('Link.user_id' => $userID, 'Link.is_deleted' => 0);
        $paginate['order'] = array('id' => 'desc');

        $this->paginate = array('Link' => $paginate);
        $links = $this->paginate('Link');
        $this->set('links', $links);

    }
    /**
     * Show Link
       * @author Oleg D.
     */
    function show($id = null) 
    {
        $this->Link->changeViews($id);
        $userID = $this->getUserID();
        $this->Link->contain('User', 'Tag');
        $link = $this->Link->find('first', array('conditions' => array('Link.id' => $id)));

        if (empty($link['Link']['id']) || $link['Link']['is_deleted']) {
            $this->Session->setFlash('Access Error', 'flash_error');
            return $this->redirect('/');
        }

        $isAuthor = 0;
        if ($link['Link']['user_id'] == $userID) {
            $isAuthor = 1;
        }

        $allowUpdate = $this->Access->getAccess('Link', 'u', $link['Link']['user_id']);
        $allowDelete = $this->Access->getAccess('Link', 'd', $link['Link']['user_id']);

        // VOTES
        $linkVotes = $this->Link->Vote->getVotes('Link', $id, $this->getUserID());
        $this->set('linkVotes', $linkVotes);
        $this->set('canVoteSubmissions', $this->Access->returnAccess('Vote_Submissions', 'c'));
        // EOF VOTES

        // COMMENTS
        $this->set('comments', $this->Link->Comment->getCommentsTree('Link', $id));
        $this->set('commentVotes', $this->Link->Vote->getCommentVotes('Link', $id, $this->getUserID()));
        $this->set('canVoteComment', $this->Access->returnAccess('Vote_Comment', 'c'));
        $this->set('canComment', $this->Access->returnAccess('Comment_Blogpost', 'c'));
        $this->set('canDeleteComment', $this->Access->returnAccess('Comment', 'd'));
        // EOF COMMENTS

        $this->set('allowDelete', $allowDelete);
        $this->set('allowUpdate', $allowUpdate);
        $this->set('link', $link);
        $this->set('isAuthor', $isAuthor);

    }

    /**
     * Delete Link
     * @author Oleg D.
     */
    function delete($id = null)
    {
        $userID = $this->getUserID();

        $link = $this->Link->find('first', array('conditions' => array('Link.id' => $id)));
         $this->Access->checkAccess('Link', 'd', $link['Link']['user_id']);

        if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']) {
            $back_url = $_SERVER['HTTP_REFERER'];
        } else {
            $back_url = '/';
        }

        if ($this->Link->find('count', array('conditions' => array('id' => $id, 'is_deleted' => 0)))) {
            if ($this->Link->delete($id)) {
                Cache::delete('last_links');
                $this->Session->setFlash(__('Link has been deleted'), 'flash_success');
            }
        } else {
            $this->Session->setFlash(__('Access Error'), 'flash_error');
        }
        return $this->redirect('/');
        exit;
    }

    /**
     * Edit Link
     * @author Oleg D.
     */
    function edit($id) 
    {

        $link = $this->Link->find('first', array('conditions' => array('Link.id' => $id)));
         $this->Access->checkAccess('Link', 'u', $link['Link']['user_id']);

        $userID = $this->getUserID();
        if (!empty($this->request->data)) {
            $this->Link->save($this->request->data);
            Cache::delete('last_links');
            $this->redirect('/links/show/' . $id);
        }
        $this->request->data = $link;
        $this->set('id', $id);
    }
    /**
     * Show all images of user
     * @author Oleg D.
     */
    function users_all($userID) 
    {
        $model = 'Link';
        $isAuthor = 0;
        if ($userID == $this->getUserID()) {
            $isAuthor = 1;
        }
        $user = $this->Link->User->read(null, $userID);
        $limit = 10;

        $this->paginate = array(
            'limit' => $limit,
            'contain' => array('User', 'Album'),
            'order' => array($model . '.created' => 'DESC'),
            'conditions' => array($model . '.user_id' => $userID, $model . '.is_deleted' => 0)
        );

        $items = $this->paginate($model);
        $itemIDs = Set::combine($items, '{n}.' . $model . '.id', '{n}.' . $model . '.id');

        $this->Link->ModelsTag->contain(array('Tag'));
        $allTags = $this->Link->ModelsTag->find('all', array('conditions' => array('ModelsTag.model' => $model, 'ModelsTag.model_id' => $itemIDs)));
        $itemsTags = array();
        foreach ($allTags as $allTag) {
            $itemsTags[$allTag['ModelsTag']['model_id']][] = $allTag['Tag'];
        }
        $votes = $this->Link->Vote->getVotes($model, $itemIDs, $this->getUserID());

        $this->set('itemsTags', $itemsTags);
        $this->set('canVoteSubmissions', $this->Access->returnAccess('Vote_Submissions', 'c'));
        $this->set('votes', $votes);
        $this->set('model', $model);
        $this->set('limit', $limit);
        $this->set('items', $items);
        $this->set('user', $user);
        $this->set('isAuthor', $isAuthor);
    }

}
?>
