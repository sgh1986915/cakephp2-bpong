<?php
class TournamentFeedsController extends AppController
{

    var $name = 'TournamentFeeds';
    var $uses = array('TournamentFeed');

    function beforeFilter()
    {
        $this->autoRender = false;
        return;
    }

    function status($event_id=null)
    {
        echo json_encode($this->TournamentFeed->getTournamentStatus($event_id));
    }

    function games($event_id=null)
    {
        echo json_encode($this->TournamentFeed->getTournamentGames($event_id));
    }

    function teams($event_id=null)
    {
        echo json_encode($this->TournamentFeed->getTournamentTeams($event_id));
    }
}
