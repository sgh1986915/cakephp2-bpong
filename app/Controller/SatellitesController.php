<?php
class SatellitesController extends AppController
{
    var $name = 'Satellites';
    var $components = array('Time');




    /**
    * Renders sattellites tied with current tournament
    * @author abel
    */
    function index() 
    {
        $this->pageTitle = "World Series of Beer Pong Satellite Tournaments";
        $show = 'showactive';
        $state = null;
        $tID = 0;

        if ($this->Session->check('Tournament')) {
            $userSession = $this->Session->read('Tournament');
            $tID = $userSession['id'];
        }

        if (!empty($this->request->data)) {
            if (!empty($this->request->data['SatFilterActive'])) { $show = 'showall'; 
            }
            $state = $this->request->data['SatFilterState'];
        }

        $events = $this->Satellite->getSatellites($tID, $show, $state);
        $states = $this->Satellite->getStates($tID, $state);
        $this->set('results', $events);
        $this->set('states', $states);
        $this->set('show', $show);
        $this->set('currstate', $state);
    }
}
?>
