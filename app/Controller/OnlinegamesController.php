<?php
class OnlinegamesController extends AppController
{

    var $name = 'Onlinegames';
    var $helpers = array('Html', 'Form');

    function beforeFilter() 
    {
        parent::beforeFilter();
        $this->pageTitle = "Beer Pong Game | Play Beer Pong Online with BPONG Online Beer Pong Games | BPONG.COM";
        $this->set("meta_description", "Play the greatest drinking sport ever - beer pong - online. Play solo to perfect your skills, against an opponent online, or just to kill some time.");
    }

    /**
     * Show Index page of Games modul
     * @author Oled D.
     */
    function index() 
    {
        return $this->redirect('/');
    }
       /**
     * Show Single Player Games Window
     * @author Oled D.
     */
    function playgame() 
    {
        $this->layout=false;
        return $this->redirect('/');        


    }

}
?>
