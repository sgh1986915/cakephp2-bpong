<?php
/* SVN FILE: $Id: languages_controller.php 557 2008-09-21 12:28:51Z ykharchenko $ */
/*
 * @version $Revision: 557 $
 * @modifiedby $LastChangedBy: ykharchenko $
 * @lastmodified $Date: 2008-09-21 15:28:51 +0300 (Вск, 21 Сен 2008) $
 */
class TeammatesController extends AppController
{
    var $name = 'Teammates';
    
    var $uses = array('Team','Teammate','User');

    /**
     * Search teammates ajax CALL
     * @author vovich
     */
    function findNewTeammates($byAjax = 0)
    {

        Configure::write('debug', 0);
        $this->layout = false;
        $teammates = array();
        if ($this->RequestHandler->isAjax() && ($this->request->data['Teammate']['email'] || $this->request->data['Teammate']['lgn'] || $this->request->data['Teammate']['last_name'])) {
            $teammatesInTeam = $this->Teammate->find('list', array('fields'=>array('user_id','user_id'),'conditions'=>array('team_id'=>$this->request->data['Team']['id'],'status'=>array('Creator','Accepted','Pending'))));
            /*Check if search is email or nickname*/
            if ($this->request->data['Teammate']['email']) {
                 $conditions['email'] = $this->request->data['Teammate']['email'];
            }
            if ($this->request->data['Teammate']['lgn']) {
                $conditions['lgn'] = $this->request->data['Teammate']['lgn'];
            }
            if ($this->request->data['Teammate']['last_name']) {
                $conditions['lastname'] = $this->request->data['Teammate']['last_name'];
            }                                                                         

            $this->Teammate->User->recursive = -1;
            $teammates  = $this->Teammate->User->find('all', array('conditions'=>$conditions));
             
            //  return $this->returnJSONResult(count($teammates));
            foreach ($teammates as $key=>$val ) {
                if (!empty($teammatesInTeam[$val['User']['id']])) {
                    unset($teammates[$key]);
                }
            }
            //       return $this->returnJSONResult($this->request->data['Team']['id']);

             $this->set('teamID', $this->request->data['Team']['id']);
            $this->set('teammates', $teammates);
            $this->set('byAjax', $byAjax);
        } else {
            exit ("Error not AJAX CALL");
        }
    }

    /**
     *  Assign user to the team
     *  @author vovich
     *  @param int    $teamID
     *  @param string $userName
     */
    function assign($teamID = null, $userName = null, $ajax = null) 
    {
        $this->layout = false;

        $userName = urldecode($userName);

        if (!empty($teamID) && !empty($userName)) {
            /*
            1) Get a list of all users currently on the team. Use this to check access. Note, that someone who is
            pending has the right to invite someone else (makes sense....)
            */
            $teammates = $this->Teammate->find('list', array('fields'=>array('user_id','user_id'),'conditions'=>array('team_id'=>$teamID,'Teammate.status'=>array('Creator','Accepted','Pending'))));
            $this->Access->checkAccess('Team', 'u', $teammates);
            /*
            2) Get the user that will be added
            */
            $this->Teammate->User->recursive = -1;
            $userInfo  = $this->Teammate->User->find('first', array('conditions'=>array('lgn'=>$userName)));
            if (empty($userInfo)) {
                if ($ajax) {
                    exit('Can not find such user.');
                }
                $this->Session->setFlash('Can not find such user.', 'flash_error');
                $this->redirect($_SERVER['HTTP_REFERER']);
            }
            /*
            3) Is the user already on the team as accepted or creator? If so, we're done
            */
            $this->Teammate->recursive = -1;
            if (isset($teammates[$userInfo['User']['id']])) {
                if ($ajax) {
                    exit('ok');
                }
                $this->Session->setFlash('Invitation has been sent to the user.', 'flash_success');
                $this->redirect($_SERVER['HTTP_REFERER']);
            }
            /*
            4) The user might exist as 'Deleted' or 'Declined'. Let's just get ride of those records (not really needed?)
            */
            $this->Teammate->deleteAll(array('team_id'=>$teamID,'user_id'=>$userInfo['User']['id']));
            
            /*
            5) Get the team, and check to see if there are already too many users
            */
            $this->Team->recursive = -1;
            $teamInfo = $this->Team->find('first', array('conditions'=>array('Team.id'=>$teamID)));   
            if (count($teammates)+1 > $teamInfo['Team']['people_in_team']) {
                if ($ajax) {
                    exit('Problem: Team is already full.');
                }
                $this->Session->setFlash('You can not assign this user because Team is already full.', 'flash_error');
                $this->redirect($_SERVER['HTTP_REFERER']);
            }
            /*
            6) Now, check to see if this exact team exists. 
            */
            $teammates[$userInfo['User']['id']] = $userInfo['User']['id']; //add the new player to the list of teammates
            $doesTeamAlreadyExist = $this->Team->doesMatchingTeamExistByPlayerIDs($teammates);                          
            /*
            7) Add a record to the Teammate DB. Note: this code is in this spot for a reason: The record needs
            to be in the database before the mergeteams function will work, but it cant go in until we've first checked
            if theres a duplicated. In other words, we see if a duplicate exists, and then make it a duplicate and then 
            delete. 
            */
            $teammate = array();
            $teammate['team_id']     = $teamID;
            $teammate['requester_id']= $this->getUserID();
            $teammate['user_id']     = $userInfo['User']['id'];
            $teammate['status']      = "Pending";   
            $this->Teammate->create();
            $this->Teammate->save($teammate);  
            
            if ($doesTeamAlreadyExist) {
                /**
                * Merge the old one into the new one, and return 
                */
                $result = $this->mergeTwoTeams($teamID, $doesTeamAlreadyExist['Team']['id']);
                if ($ajax) {
                    return $this->returnJSONResult(
                        array('duplicate'=>array(
                        'mergedid'=>$doesTeamAlreadyExist['Team']['id'],
                        'deletedteamname'=>$teamInfo['Team']['name']))
                    );
                }                
                $this->Session->setFlash('Duplicate team exists. Teams have been merged. Remember, you can use a different team name for each Event you play in.', 'flash_error');
                $this->redirect(
                    MAIN_SERVER.'/nation/beer-pong-teams/team-info/'.$doesTeamAlreadyExist['Team']['slug'].
                    '/'.$doesTeamAlreadyExist['Team']['id']
                ); 
            } 
            
            //Update team status to Pending
            $teamInfo['Team']['status'] = "Pending";
            $this->Teammate->Team->save($teamInfo);
            $this->User->updateTeamRatings($userInfo['User']['id']);
            $result = $this->sendMailMessage(
                'TeamInvitation', array(
                '{TEAMNAME}'      => $teamInfo['Team']['name'],
                       '{FNAME}'         => $userInfo['User']['firstname'],
                       '{LNAME}'         => $userInfo['User']['lastname'],
                       '{DESCRIPTION}'   => $teamInfo['Team']['description'],
                       '{VIEWTEAMLINK}'  => "<a href='" . MAIN_SERVER . "/nation/beer-pong-teams/team-info/{$teamInfo['Team']['slug']}/{$teamInfo['Team']['id']}'>Vew team</a>",
                       '{ACCEPTLINK}'    => "<a href='" . MAIN_SERVER . "/teams/accept/{$teamInfo['Team']['id']}/".urlencode($userInfo['User']['lgn'])."'>Accept</a>",
                       '{DECLINELINK}'   => "<a href='" . MAIN_SERVER . "/teams/decline/{$teamInfo['Team']['id']}/".urlencode($userInfo['User']['lgn'])."'>Decline</a>"
                              ),
                $userInfo['User']['email']
            );
            if ($ajax) {
                exit('ok');
            }
            $this->Session->setFlash('Invitation has been sent to the user.', 'flash_success');
            $this->redirect($_SERVER['HTTP_REFERER']);

        } else {
            if ($ajax) {
                exit('Error with input parameters.');
            }                                                      
            $this->Session->setFlash('Error with input parameters.', 'flash_error');
            $this->redirect($_SERVER['HTTP_REFERER']);
        }


    }


    /**
     * Delete User from the team and if team was completed then remove team
     * @author vovich
     * @param int    $teamID
     * @param string $userName
     */
    function delete($teamID = null,$userName = null) 
    {
        $userName = urldecode($userName);
        if (!$this->Session->check('loggedUser') || !$teamID) {
             $this->Session->setFlash('You are not logged', 'flash_error');
             $this->redirect(MAIN_SERVER);
        }
        $user = $this->Session->read('loggedUser');

        //Getting owners for checking access
        $teammates = $this->Teammate->find('list', array('fields'=>array('user_id','user_id'),'conditions'=>array('team_id'=>$teamID,'Teammate.status'=>array('Creator','Accepted'))));
        $this->Access->checkAccess('Team', 'd', $teammates);
        $this->Teammate->recursive = 0;

        $teamInfo = $this->Teammate->Team->find('first', array('conditions'=>array('Team.id'=>$teamID)));
        if (empty($teamInfo)) {
             $this->Session->setFlash('Can not find such team.', 'flash_error');
             $this->redirect(MAIN_SERVER);
        }
         //Confirm that the team does not have any games already
         $Game = ClassRegistry::init('Game');
         $Game->recursive = -1;
         $doesAGameExist = $Game->find(
             'first', array('conditions'=>array(
             'status <>'=>'Deleted',
             'OR'=>array(
                'team1_id'=>$teamID,
                'team2_id'=>$teamID)))
         );
         if ($doesAGameExist) {
             $this->Session->setFlash('You can not delete a team once it has played a game', 'flash_error');
             $this->redirect(array('controller'=>'teams','action'=>'myteams'));
            }

            $conditions = array('lgn'=>$userName);
            $userInfo  = $this->Teammate->User->find('first', array('conditions'=>$conditions));
            if (empty($userInfo)) {
                $this->Session->setFlash('Can not find such user.', 'flash_error');
                $this->redirect($_SERVER['HTTP_REFERER']);
            }
            $teammateInfo = $this->Teammate->find('first', array('conditions'=>array('team_id'=>$teamID,'user_id'=>$userInfo['User']['id'])));
            $teammateInfo['Teammate']['status'] = "Deleted";
            $this->Teammate->save($teammateInfo);

            if ($teamInfo['Team']['status'] == 'Completed' || $this->Teammate->find('count', array('conditions'=>array('team_id'=>$teamID,'Teammate.status'=>array('Creator','Accepted'))))==0) {
                //then remove team
                $teamInfo['Team']['status']       = 'Deleted';
                $teamInfo['Team']['is_deleted'] = 1;
                $teamInfo['Team']['deleted']     = date('Y-m-d H:i:s');
                $this->Teammate->Team->save($teamInfo);

                $TeamsObject = ClassRegistry::init('TeamsObject');
                $teamObjectInformations= $TeamsObject->find('all', array('conditions'=>array('team_id'=>$teamID)));
                if (!empty($teamObjectInformations)) {
                    foreach ($teamObjectInformations as $teamObjectInformation) {
                        $teamObjectInformation['TeamsObject']['status'] = 'Deleted';
                        $TeamsObject->save($teamObjectInformation);
                    }
                }

                //sending an email to the all users in team that completed
                $this->Teammate->recursive = 0;
                $usersInTeam = $this->Teammate->find('all', array('conditions'=>array('team_id'=>$teamID, 'Teammate.status'=>array('Creator','Accepted') )));

                foreach ($usersInTeam as $userInTeam) {
                    $result = $this->sendMailMessage(
                        'TeamHasBeenDeleted', array(
                        '{TEAMNAME}'      => $teamInfo['Team']['name'],
                         '{FNAME}'         => $userInTeam['User']['firstname'],
                         '{LNAME}'         => $userInTeam['User']['lastname'],
                         '{DESCRIPTION}'   => $teamInfo['Team']['description'],
                         '{REMOVER}'       => $user['lgn']
                          ),
                        $userInfo['User']['email']
                    );

                }
            }

            $result = $this->sendMailMessage(
                'TeammateRemoved', array(
                '{TEAM}'      => $teamInfo['Team']['name'],
                   '{FNAME}'         => $userInfo['User']['firstname'],
                   '{LNAME}'         => $userInfo['User']['lastname'],
                   '{DESCRIPTION}'   => $teamInfo['Team']['description']
                          ),
                $userInfo['User']['email']
            );
            $this->User->updateTeamRatings($userInfo['User']['id']);

            $this->Session->setFlash('The teammate has been deleted.', 'flash_success');
            //$this->redirect("/teams/view/".$teamID);
            return $this->redirect(array('controller'=>'teams','action'=>'myteams'));
    }

    function ajax_get_teammates($teamID = null) 
    {
        $this->layout = false;
        $teammates = $this->Teammate->find(
            'all', array(
            'conditions'=>array(
                'Teammate.team_id'=>$teamID,
                'Teammate.status'=>array('Pending','Accepted','Creator')),
            'contain'=>array('User'))
        );
        $this->set('teammates', $teammates);
    }
}

?>