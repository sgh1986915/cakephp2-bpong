<?php
/**
 * Watermarks
 * http://jeka911.wordpress.com/2007/09/25/3_ways_to_add_watermark_to_image_in_php/
 */
class PongtablesController extends AppController
{
    var $name = 'Pongtables';

    var $uses = array( 'Pongtable', 'Country', 'Provincestate', 'Image' );

    var $paginate = array( 'order' => array('Pongtable.id' => 'asc'));

    var $helpers = array('Html', 'Form', 'Bbcode');

    function index() 
    {
        $this->Pongtable->recursive = 1;
        if ($this->Access->getAccess('pongtables', 'u') ) {
            $this->paginate['order'] = 'Pongtable.modified DESC';
            $this->set('tables', $this->paginate(array( "Pongtable.is_deleted <> 1" )));
        } else {
            $this->set('tables', $this->paginate(array("Pongtable.is_aprooved = 1", "Pongtable.is_deleted <> 1" )));
        }
        $this->set('userID', $this->Access->getLoggedUserID());
        //check ACCESS for Update and delete links
        $this->set('UpdatedTable', $this->Access->returnAccess('pongtables', 'u'));
        $this->set('DeletedTable', $this->Access->returnAccess('pongtables', 'd'));
        //Check access for the buttons
        $this->set('CreatedTable', $this->Access->getAccess('pongtables', 'c'));


    }//eof index


    function add() 
    {

        if (!empty($this->request->data) ) {

            $this->request->data ['Pongtable'] ['user_id'] = $this->Session->read('loggedUser.id');
            $this->request->data ['Pongtable'] ['title'] = trim(htmlentities(strip_tags($this->request->data ['Pongtable'] ['title'])));
            $this->request->data ['Pongtable'] ['analysis'] = trim(htmlentities(strip_tags($this->request->data ['Pongtable'] ['analysis'])));
            $this->request->data ['Pongtable'] ['description'] = trim(htmlentities(strip_tags($this->request->data ['Pongtable'] ['description'])));

            $this->Pongtable->create();

            if ($this->Pongtable->save($this->request->data) ) {

                $lastID = $this->Pongtable->getLastInsertID();

                $this->request->data ['Address'] ['model'] = "Pongtable";
                $this->request->data ['Address'] ['model_id'] = $lastID;
                $this->Pongtable->Address->save($this->request->data);
                $this->Session->setFlash('Pong table was saved.', 'flash_success');
                return $this->redirect(array('controller' => 'pongtables', 'action' => 'index'));
                exit();
            } else {
                $this->Session->setFlash('Please correct errors.', 'flash_success');
            }
        }
        
        /*pass to the view countries and states*/
        $countries_states = $this->Pongtable->Address->setCountryStates();
        $this->set('countries', $countries_states['countries']);
        $this->set('provincestates', $countries_states['states']);
        
        //$this->set('countries', $this->Country->find('list'));
        //$this->set('provincestates', $this->Provincestate->find('list'));

    }

    function edit( $id ) 
    {

        //Security
        $this->Pongtable->id = $id;
        $this->Pongtable->contain();
        $pongtable = $this->Pongtable->read(array( 'user_id' ));
        $this->Access->checkAccess('pongtables', 'u', $pongtable ['Pongtable'] ['user_id']);
        $this->set('Deleted', $this->Access->getAccess('pongtables', 'd', $pongtable ['Pongtable'] ['user_id']));
        //end of security

        if (!empty($this->request->data) ) {
            // check for aprooving rights
            if (!$this->Access->getAccess('pongtables_aproove', 'u') && isset( $this->request->data ['Pongtable'] ['is_aprooved'])) {
                unset( $this->request->data ['Pongtable'] ['is_aprooved'] );
            }

            $this->request->data ['Pongtable'] ['title'] = trim(htmlentities(strip_tags($this->request->data ['Pongtable'] ['title'])));
            $this->request->data ['Pongtable'] ['analysis'] = trim(htmlentities(strip_tags($this->request->data ['Pongtable'] ['analysis'])));
            $this->request->data ['Pongtable'] ['description'] = trim(htmlentities(strip_tags($this->request->data ['Pongtable'] ['description'])));

            if((int)$this->request->data['Image']['new']['size'] == 0 ) {
                unset( $this->request->data['Image'] );
            }

            $this->Pongtable->id = $id;
            $this->Pongtable->save($this->request->data);

            $address_id = $this->Pongtable->Address->find('first', array ('conditions' => array ('model' => 'Pongtable', 'model_id' => $id)));

            $this->Pongtable->Address->id = $address_id['Address']['id'];
            $this->Pongtable->Address->save($this->request->data);

            return $this->redirect(array('controller' => 'pongtables', 'action' => 'index'));
            exit();

        }

        $this->request->data = $this->Pongtable->read(null, $id);
        $this->request->data ['Pongtable'] ['title'] = html_entity_decode($this->request->data ['Pongtable'] ['title']);
        $this->request->data ['Pongtable'] ['analysis'] = html_entity_decode($this->request->data ['Pongtable'] ['analysis']);
        $this->request->data ['Pongtable'] ['description'] = html_entity_decode($this->request->data ['Pongtable'] ['description']);

        /*pass to the view countries and states*/
        $countries_states = $this->Pongtable->Address->setCountryStates();
        $this->set('countries', $countries_states['countries']);
        $this->set('provincestates', $countries_states['states']);
        
        /*$this->set('countries', $this->Country->find('list'));
        $this->set('provincestates', $this->Provincestate->find('list'));*/

        //$userID = $this->Access->getLoggedUserID();
        $this->set('access_to_aproove', $this->Access->getAccess('pongtables_aproove', 'u'));

    }

    function delete( $id ) 
    {

        /*Check access*/
        $this->Pongtable->id = $id;
        $this->Pongtable->contain();
        $pongtable = $this->Pongtable->read(array ( 'user_id' ));
        $this->Access->checkAccess('pongtables', 'd', $pongtable ['Pongtable'] ['user_id']);
        /*EOF Check access*/

        $this->autoRender = false;
        $time_now = date("Y-m-d", time());
        $this->Pongtable->id = $id;
        $this->Pongtable->saveField('deleted', $time_now);
        $this->Pongtable->saveField("is_deleted", 1);

        $this->Image->updateAll(
            array(  'deleted' => "'".$time_now."'"
                                            , 'is_deleted' => 1), array ("Image.model = 'Pongtable' AND Image.model_id = $id AND Image.is_deleted <> 1" )
        );

        $this->Pongtable->Address->find('first', array ('conditions' => array ('model' => 'Pongtable', 'model_id' => $id)));
        $this->Pongtable->Address->id = $id;
        $this->Pongtable->Address->saveField('deleted', $time_now);
        $this->Pongtable->Address->saveField("is_deleted", 1);

        return $this->redirect(array('controller' => 'pongtables', 'action' => 'index'));
        exit();

    }
    /**
 * Ajax function to delete pongtable image
 *
 * @author    Povstyanoy
 * @param     imageID
 * @copyright All rights reserved
 */
    function deleteimage() 
    {
        Configure::write('debug', 0);

        $this->Image->id = $this->request->params['form']['imageID'];
        $model_id = $this->Image->read('model_id');
        /*Check access*/
        $this->Pongtable->id = $model_id['Image']['model_id'];
        $this->Pongtable->contain();
        $pongtable = $this->Pongtable->read(array ( 'user_id' ));
        $this->Access->checkAccess('pongtables', 'u', $pongtable ['Pongtable'] ['user_id']);
        /*EOF Check access*/

        if ($this->Access->getAccess('images', 'd', $userID) ) {
            $this->layout = false;
            $image = $this->Image->read(null, $this->request->params['form']['imageID']);

            if (!empty( $image ) ) {
                $this->Image->id = $this->request->params['form']['imageID'];
                $this->Image->saveField('deleted', date("Y-m-d", time()));
                $result = $this->Image->saveField('is_deleted', 1);
                if ($result) {
                    echo "1";
                    exit();
                }
            }
        }

        echo "0";
        exit();
    }

}

?>