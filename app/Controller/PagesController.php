<?php
/* SVN FILE: $Id: pages_controller.php 7919 2011-12-10 03:52:48Z _skinny $ */
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *                                1785 E. Sahara Avenue, Suite 490-204
 *                                Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright    Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link         http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package      cake
 * @subpackage   cake.cake.libs.controller
 * @since        CakePHP(tm) v 0.2.9
 * @version      $Revision: 7919 $
 * @modifiedby   $LastChangedBy: _skinny $
 * @lastmodified $Date: 2011-12-10 05:52:48 +0200 (Сб, 10 дек 2011) $
 * @license      http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package    cake
 * @subpackage cake.cake.libs.controller
 */
class PagesController extends AppController
{
    /**
 * Controller name
 *
 * @var    string
 * @access public
 */
    var $name = 'Pages';
    /**
 * Default helper
 *
 * @var    array
 * @access public
 */
    var $helpers = array('Html', 'Forumlinks', 'Js' => array('Jquery'), 'Address');
    /**
 * This controller does not use a model
 *
 * @var    array
 * @access public
 */
    var $uses = array();

    function livestream() 
    {
        $url = 'https://www.facebook.com/BPONG?sk=app_165001306933021'; 
        return $this->redirect($url);
    }
    
    function beforeFilter()
    {
        parent::beforeFilter();
        $this->pageTitle = ucfirst(str_replace('_', ' ', $this->request->action));
    }
    function redirect_android_market() 
    {
        $url = 'https://market.android.com/details?id=com.bpong.scorekeeper';
        return $this->redirect($url);
    }
    /**
 * Displays a view
 *
 * @param  mixed What page to display
 * @access public
 */
    function display() 
    {


        $path = func_get_args();

        if (!count($path)) {
            return $this->redirect('/');
        }
        $count = count($path);
        $page = $subpage = $title = null;


        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        if (!empty($path[$count - 1])) {
            $title = Inflector::humanize($path[$count - 1]);
        }

        if ($page == "games") {
            $this->layout = false;
            Configure::write('debug', 0);
        }


        $this->set(compact('page', 'subpage', 'title'));
        $this->render(join('/', $path));


    }
    function mobileredirect($pageid) 
    {
        if ($pageid == 0) {
            return $this->redirect(MAIN_SERVER.'/store');
        }
        if ($pageid == 1) {
            $this->redirect(MAIN_SERVER.'/wsobp');
        }
        if ($pageid == 2) {
            $this->redirect(MAIN_SERVER.'/store');
        }
        if ($pageid == 3) {
            $this->redirect(MAIN_SERVER.'/store/category/beer-pong-tables');
        }        
        if ($pageid == 4) {
            $this->redirect(MAIN_SERVER.'/store/product/bpong-signature-series-hydro74-table-8ft');
        }    
    }
    function softwareWebDemo() 
    {
        return $this->redirect(MAIN_SERVER.'/webdemo/Main.html');
    }
    function tour()
    {
         $this->pageTitle = "The Tour page you requested does not longer available.";
         /*
         $show = 'showactive';
        $state = NULL;
        $tID = 0;

        if (!class_exists("Satellite")) {
        App::import("Model", "Satellite");
        }
        $Satellite = new Satellite();

        $tID = 71;

        if (!empty($this->request->data)) {
        if (!empty($this->request->data['SatFilterActive'])) { $show = 'showall'; }
        $state = $this->request->data['SatFilterState'];
        }

        $events = $Satellite->getSatellites($tID,$show,$state,0);
        $states = $Satellite->getStates($tID,$state,0);
        $this->set('results',$events);
        $this->set('states',$states);
        $this->set('show',$show);
        $this->set('currstate',$state);
        */

    }

    function nation() 
    {

        $this->pageTitle = 'BPONG Nation - Beer Pong Tournaments, Beer Pong Forums, Beer Pong Community | BPONG.COM.';

        if (!class_exists("Pongtable")) {
            App::import("Model", "Pongtable");
        }
        if (!class_exists("Image")) {
            App::import("Model", "Image");
        }
        if (!class_exists("Forumtopic")) {
            App::import("Model", "Forumtopic");
        }

        $objTable = new Pongtable();
        $this->set("pongtable", $objTable->getRandomTable());
        $objTopics = new Forumtopic();
        /*
        $objTopics->contain( array("User", "Lastpost" => array("User")) );
        $lastTopics = $objTopics->find("all", array('conditions' => array(
																		'Forumtopic.deleted IS NULL'
																		)
												  	, 'order' => 'Lastpost.id DESC'
													, 'limit' => 10
													, 'recursive' => 2));
        */
        $this->set("forumtopics", $objTopics->getLastTopicsForNationPage());

        /**
         * getting blog content
         */
        if (!class_exists("Blogpost")) {
            App::import("Model", "Blogpost");
        }

        $bpObject = new Blogpost();
        $post = $bpObject->find(
            'first', array(        'recursive' => 0,
                                                    'order' => 'Blogpost.created DESC',
                                                    'conditions' => array("Blogpost.is_deleted ='0'")
                                               )
        );

        $this->set('blogPost', $post);

    }

    /**
     * Displays terms for store
     * @author Edward
     */
    function storeTerms() 
    {
        $this->storecategoriesMenu();
    }

    function past_wsobps() 
    {

    }
    function last_cup() 
    {

    }
    /**
 * New contact page with sending emails
 * @author vovich
 * @param $actve - active tab
 * @return unknown_type
 */
    function contactnew($active =  null) 
    {
        $this->pageTitle = "Contact";
        $sendTo = "odikusar@shakuro.com";
        $accordionActive     = "false";
        $advertingActive      = "false";
        $merchandiseActive = "false";

        if (!empty($active)) {
            if ($active == "sponsorship") {
                               $accordionActive     = 0;
                               $advertingActive      = 0;
                               $merchandiseActive = 0;
            } elseif($active == "advertising" ) {
                              $accordionActive     = 0;
                           $advertingActive      = 1;
                           $merchandiseActive  = 0;
            } elseif($active == "preSale" ) {
                            $accordionActive    = 1;
                           $advertingActive      = 0;
                           $merchandiseActive  = 0;
            } elseif($active == "problemswithOrders" ) {
                           $accordionActive     = 1;
                           $advertingActive      = 0;
                           $merchandiseActive  = 1;
            } elseif($active == "cancelOrder" ) {
                           $accordionActive     = 1;
                           $advertingActive      = 0;
                           $merchandiseActive  = 2;
            } elseif($active == "dropShipProgram" ) {
                           $accordionActive     = 1;
                           $advertingActive      = 0;
                           $merchandiseActive  = 3;
            } elseif($active == "affiliateProgram" ) {
                           $accordionActive     = 1;
                           $advertingActive      = 0;
                           $merchandiseActive  = 4;
            } elseif($active == "retail" ) {
                            $accordionActive     = 1;
                           $advertingActive      = 0;
                           $merchandiseActive  = 5;

            } elseif ($active == "results") {
                            $accordionActive     = 2;
                           $advertingActive      = 0;
                           $merchandiseActive  = 0;
            } elseif($active == "tournaments" ) {
                            $accordionActive     = 3;
                           $advertingActive      = 0;
                           $merchandiseActive  = 0;

            } elseif($active == "signup" ) {
                            $accordionActive     = 4;
                           $advertingActive      = 0;
                           $merchandiseActive  = 0;

            } elseif($active == "publicRelations" ) {
                            $accordionActive     = 5;
                           $advertingActive      = 0;
                           $merchandiseActive  = 0;
            } elseif($active == "rules" ) {
                            $accordionActive     = 6;
                           $advertingActive      = 0;
                           $merchandiseActive  = 0;
             } elseif($active == "website" ) {
                            $accordionActive     = 7;
                           $advertingActive      = 0;
                           $merchandiseActive  = 0;
             } elseif($active == "other" ) {
                          $accordionActive     = 8;
                          $advertingActive      = 0;
                          $merchandiseActive  = 0;
             }

        }

        if (!empty($this->request->data)) {
            $type = "";
            foreach ($this->request->data as $key=>$value) {
                $type = $key;
            }

            $body     = $this->request->data[$key]['body'];
            $subject  = $this->request->data[$key]['subject'];

            if (empty($this->request->data[$key]['email']) || $this->request->data[$key]['email'] != $this->request->data[$key]['confirmEmail']) {
                $this->Session->setFlash('Please type a valid email address.', 'flash_error');
                $this->redirect("/");
            }

            switch ($key) {
            case "Sponsorship" :
                    $sendTo = "sponsorship@bpong.com";
                break;
            case "Advertising" :
                    $sendTo = "marketing@bpong.com";
                break;
            case "PreSale" :
                    $sendTo = "sales@bpong.com";
                break;
            case "ProblemswithOrders" :
                    $sendTo = "sales@bpong.com";
                    $body     = "Order Number:  " . $this->request->data[$key]['orderNumber'] . "<br>" . $this->request->data[$key]['body'];
                break;
            case "CancelOrder" :
                    $sendTo = "sales@bpong.com";
                    $body     = "Order Number:  " . $this->request->data[$key]['orderNumber'] . "<br>" . $this->request->data[$key]['body'];
                break;
            case "DropShipProgram" :
                    $sendTo = "dropship@bpong.com";
                break;
            case "AffiliateProgram" :
                    $sendTo = "affiliates@bpong.com";
                break;
            case "Retail" :
                    $sendTo = "retail@bpong.com";
                break;
            case "Results" :
                $sendTo = "tournaments@bpong.com";
                break;
            case "Tournaments" :
                    $sendTo = "tournaments@bpong.com";
                break;
            case "Signup" :
                    $sendTo = "registrations@bpong.com";
                break;
            case "PublicRelations" :
                    $sendTo = "pr@bpong.com";
                break;
            case "Website" :
                    $sendTo = "webmaster@bpong.com";
                break;
            case "Other" :
                    $sendTo = "misc@bpong.com";
                break;

            }

             App::import('Vendor', 'PHPMailer', array('file' => 'mailer.class.php'));
             $mail = new PHPMailer();
            // $sendTo = "vovich@shakuro.com";
             $mail->ContentType = "text/html";
             $mail->From            = $this->request->data[$key]['email'];
             $mail->FromName   = $this->request->data[$key]['email'];

             $mail->AddAddress($sendTo, $sendTo);
            /*
             // This is Temporary:
             // For right now, also send stuff to Skinny as we debug RT
             if ($key == "Results" || $key == "Tournaments" || $key == "Signup") {
             	$mail->AddAddress('skinny@bpong.com','skinny@bpong.com');
             }
             if ($key == "PreSale" || $key == "ProblemswithOrders" || $key == "CancelOrder") {
             	$mail->AddAddress('kerry@bpong.com','kerry@bpong.com');
             }
            */
             $mail->Subject         = $subject;
             $mail->Body            = $body;
            if (!$mail->Send()) {
                $this->Session->setFlash('Error', 'flash_error'); 
            }
            else {
                  $this->Session->setFlash('Email has been sent', 'flash_success'); 
            }

              $this->redirect("/");
        }
              $this->set(compact('active', 'accordionActive', 'advertingActive', 'merchandiseActive'));
    }
    /**
 *
 * @return unknown_type
 */
    function home() 
    {
        // Forum
        $forumtopics = [];
        //        $forumtopics = Cache::read('forumtopics', 'full_time');
        //        if (empty($forumtopics)) {
        //            $ObjForumtopic = ClassRegistry::init("Forumtopic");
        //            $forumtopics =  $ObjForumtopic->getLastTopicsForHomePage ();
        //            foreach ($forumtopics as $key => $forumtopic) {
        //    		    $slug_to_topic = $ObjForumtopic->generate_last_post_url_for_branch ($forumtopic ['Forumtopic'] ['slug'], $forumtopic ['Forumbranch'] ['lft'], $forumtopic ['Forumbranch'] ['rght'] );
        //    		    $forumtopics[$key]['Forumtopic']['slug_to_topic'] = $slug_to_topic;
        //            }
        //            Cache::write('forumtopics', $forumtopics, 'full_time');
        //        }
        $this->set("forumtopics", $forumtopics);
        // EOF Forum

        // NEW STUFF /////////////////////////////////////////////////////////////////////////////////////

        // Blog Posts
        $posts = Cache::read('last_blogposts', 'new_stuff');
        if (empty($posts)) {
            $BpObject = ClassRegistry::init("Blogpost");
            $BpObject->bindModel(array('hasMany' => array('Image' => array('className' => 'Image', 'className' => 'Image', 'conditions' => 'model="Blogpost" AND Image.is_deleted =0', 'order' => '', 'limit' => '1', 'foreignKey' => 'model_id', 'dependent' => false, 'exclusive' => false, 'finderQuery' => ''))));

            $lastPost = $BpObject->find('first', array('contain' => array('Image'), 'limit'   =>1,'order' => 'Blogpost.id DESC', 'conditions' => array('Blogpost.is_deleted' => 0)));
            $lastCount = $BpObject->find('count', array('conditions' => array('Blogpost.is_deleted' => 0, 'Blogpost.created >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)')));

            $posts['lastCount'] = $lastCount;
            $posts['lastPost'] = $lastPost;

            Cache::write('last_blogposts', $posts, 'new_stuff');
        }

        $this->set('blogPosts', $posts);
        // EOF Blog Posts
        // Last Images
        $lastImages = Cache::read('last_images', 'new_stuff');
        if (empty($lastImages)) {
            $ImageObj = ClassRegistry::init("Image");

            $lastImage = $ImageObj->find(
                'first', array('contain' => array('Album'), 'limit' => 1, 'order' => 'Image.modified DESC', 'conditions' => array('Image.is_deleted' => 0, 'Image.model' => 'Album', /**
                * 
                * 'Album.model <>' => 'StoreSlot' 
                */))
            );
            $lastCount = $ImageObj->find(
                'count', array('contain' => array('Album'), 'conditions' => array('Image.is_deleted' => 0, 'Image.model' => 'Album', 'Image.created >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)', /**
                * 
                * 'Album.model <>' => 'StoreSlot' 
                */))
            );

            $lastImages['lastCount'] = $lastCount;
            $lastImages['lastImage'] = $lastImage;

            Cache::write('last_images', $lastImages, 'new_stuff');
        }

        $this->set('lastImages', $lastImages);
        // EOF Last Images

        // Last Videos
        $lastVideos = Cache::read('last_videos', 'new_stuff');
        if (empty($lastVideos)) {

            $VideoObj = ClassRegistry::init("Video");

            $lastVideo = $VideoObj->find('first', array('limit' => 1, 'order' => 'Video.modified DESC', 'conditions' => array('Video.is_deleted' => 0, 'Video.model' => 'Album', 'Video.is_processed' => 1)));
            $lastCount = $VideoObj->find('count', array('conditions' => array('Video.is_deleted' => 0, 'Video.model' => 'Album', 'Video.is_processed' => 1, 'Video.created >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)')));

            $lastVideos['lastCount'] = $lastCount;
            $lastVideos['lastVideo'] = $lastVideo;

            Cache::write('last_videos', $lastVideos, 'new_stuff');
        }
        $this->set('lastVideos', $lastVideos);
        // EOF Last Videos

        // Last Links
        $lastLinks = Cache::read('last_links', 'new_stuff');
        if (empty($lastLinks)) {

            $LinkObj = ClassRegistry::init("Link");

            $lastCount = $LinkObj->find('count', array('conditions' => array('Link.is_deleted' => 0, 'Link.created >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)')));

            $lastLinks['lastCount'] = $lastCount;

            Cache::write('last_links', $lastLinks, 'new_stuff');
        }
        $this->set('lastLinks', $lastLinks);
        // EOF Last Links

        // Last Events
        $lastEvents = Cache::read('last_events', 'new_stuff');
        if (empty($lastEvents)) {
            $EventObj = ClassRegistry::init("Event");

            $lastCount = $EventObj->find('count', array('conditions' => array('Event.is_deleted' => 0, 'Event.created >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)')));

            $lastEvents['lastCount'] = $lastCount;

            Cache::write('last_events', $lastEvents, 'new_stuff');
        }
        $this->set('lastEvents', $lastEvents);
        // EOF Events

        // Last Event Results
        $lastEventResults = Cache::read('last_event_results', 'new_stuff');
        if (empty($lastEventResults)) {
            $EventObj = ClassRegistry::init("Event");
            $lastEventResults = $EventObj->find(
                'count', array('conditions' => array(
                'Event.is_deleted' => 0,
                'Event.end_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)',
                'Event.end_date <= NOW()',
                'Event.iscompleted'=>1))
            );


            Cache::write('last_event_results', $lastEventResults, 'new_stuff');
        }
        $this->set('lastEventResults', $lastEventResults);
        // EOF Event Results

        // EOF NEW STUFF /////////////////////////////////////////////////////////////////////////////////////


        // Slides
        $slides = Cache::read('slides', 'full_time');
        if (empty($slides)) {

            $ObjSlide = ClassRegistry::init("Slide");
             $ObjSlide->data = null;
             $slides = $ObjSlide->find('all', array('order' => 'ordering ASC', 'contain' => array('Image')));
            Cache::write('slides', $slides, 'full_time');
        }
        $this->set('slides', $slides);
        // EOF slidess

        // GOOGlE MAPS MARKERS FOR Satellites
        $satelliteMarkers = Cache::read('satelliteMarkers', 'satelliteMarkers');
        //       if (empty($satelliteMarkers)) {
        //           $ObjEventView = ClassRegistry::init('EventView');
        //           $satelliteConditions = array('EventView.is_deleted' => 0,'EventSatellite.relationship_type' => 'Satellite');
        //           $satelliteMarkers= $ObjEventView->getMapMarkers(date('Y-m-d'), $satelliteConditions, array());
        //           Cache::write('satelliteMarkers',$satelliteMarkers,'satelliteMarkers');
        //       }
        $this->set('satelliteMarkers', $satelliteMarkers);  
        // EOF GOOGlE MAPS MARKERS
       
        // GOOGlE MAPS MARKERS for venues
        $markers = Cache::read('markers', 'markers');
        //       if (empty($markers)) {
        //           $venueIDsWithSatellites = Set::extract($satelliteMarkers,'{n}.EventView.venue_id');
        //           $venueIDsWithSatellites = $this->custom_array_unique($venueIDsWithSatellites);
        //            $ObjVenueView = ClassRegistry::init("VenueView");
        //            $conditions = array(
        //                'VenueView.is_deleted' => 0,
        //                'VenueView.nbpltype <> ' => 'None',
        //                'VenueView.id NOT'=>$venueIDsWithSatellites
        //                );
        //
        //            $markers = $ObjVenueView->getMapMarkers();
        //
        //            Cache::write('markers', $markers, 'markers');
        //       }
        $this->set('markers', $markers);
    }
    /**
     * Customer services page
     * @author Oleg D.
     */
    function customer_services() 
    {
        $this->Access->checkAccess('CustomerShowServices', 'r');
        //$this->Access->checkAccess('CustomerShowOrders','r');
        $this->set('show_all_orders', $this->Access->getAccess('CustomerShowOrders', 'r'));
        $this->set('show_ipg_orders', $this->Access->getAccess('IPGShowOrders', 'r'));
        $this->set('show_sae_orders', $this->Access->getAccess('SAShowOrders', 'r'));
    }
    /**
     * Customer services page
     * @author Oleg D.
     */
    function reports() 
    {
        $this->Access->checkAccess('CustomerShowServices', 'r');
    }

    function wsobp_new() 
    {

    }

    function wsobp_pricing() 
    {

    }

    function wsobp_gallery() 
    {

    }
    function viewbrackets($eventID) 
    {
        $eventID = intval($eventID);
        $this->layout = false;
        $EventObject = ClassRegistry::init('Event');

        $event = $EventObject->find('first', array('conditions' => array('id' => $eventID)));
        $this->pageTitle = 'Results of Event: '.$event['Event']['name'];
        $this->set(compact('eventID', 'event'));
    }
    function viewprojector($eventID) 
    {
        $eventID = intval($eventID);
        $this->layout = false;
        $EventObject = ClassRegistry::init('Event');

        $event = $EventObject->find('first', array('conditions' => array('id' => $eventID)));
        $this->set(compact('eventID', 'event'));
    }
    
    function get_the_app() 
    {                
    }
    /**
        Send iphone app request (Coming soon for iOS)
     */
    function send_iphone_request() 
    {
        if (empty($this->request->data['Request']['email'])) {
            $this->Session->setFlash('Email address error!', 'flash_error');        
            return $this->redirect('/');            
        }
        
        $email = $this->request->data['Request']['email'];
        App::import('Vendor', 'PHPMailer', array('file' => 'mailer.class.php'));
        $mail = new PHPMailer();

        $mail->ContentType = "text/html";
        $mail->FromName   = $email;
        $subject = 'iOS App request';
        if (!LIVE_WEBSITE) {
            $subject = $subject . ' TESTING!!!';    
        }
        $body = "Email me when it's ready, my email: " . $email;
        $mail->AddAddress(MANAGER_EMAIL, MANAGER_EMAIL);
        // This is Temporary:
        // For right now, also send stuff to Skinny as we debug RT
        //if ($key == "Results" || $key == "Tournaments" || $key == "Signup") {
        //	$mail->AddAddress('skinny@bpong.com','skinny@bpong.com');
        //}
        $mail->Subject         = $subject;
        $mail->Body            = $body;
        if ($mail->Send()) {
            $this->Session->setFlash('Email has been sent', 'flash_success');         
        } else {
            $this->Session->setFlash('Error while sending', 'flash_error');             
        }
        
        return $this->redirect('/');
        exit();
    }
    /**
        Send bar support message
     */
    function send_bar_support() 
    {
        if (empty($this->request->data['Contact']['email'])) {
            $this->Session->setFlash('Email address error!', 'flash_error');        
            return $this->redirect('/');            
        }

        App::import('Vendor', 'PHPMailer', array('file' => 'mailer.class.php'));
        $mail = new PHPMailer();

        $mail->ContentType = "text/html";
        $mail->FromName   = $this->request->data['Contact']['first_name'];
        $subject = 'Bar Program Contact Form';
        if (!LIVE_WEBSITE) {
            $subject = $subject . ' TESTING!!!';    
        }
        $body.= '<b>First Name:</b> ' . $this->request->data['Contact']['first_name'] . '<br/>';
        $body.= '<b>Last Name:</b> ' . $this->request->data['Contact']['last_name'] . '<br/>';
        $body.= '<b>Email:</b> ' . $this->request->data['Contact']['email'] . '<br/>';
        $body.= '<b>Bar Name:</b> ' . $this->request->data['Contact']['bar_name'] . '<br/>';
        $body.= '<b>Bar Address:</b> ' . $this->request->data['Contact']['bar_adddress'] . '<br/>';
        $body.= '<b>Bar City/State:</b>' . $this->request->data['Contact']['bar_city_state'] . '<br/>';
        $body.= '<b>Bar Manager:</b> ' . $this->request->data['Contact']['bar_manager_name'] . '<br/>';
        $body.= '<b>Telephone:</b> ' . $this->request->data['Contact']['telephone'] . '<br/>';        
        $body.= '<b>Question:</b> ' . $this->request->data['Contact']['question'] . '<br/>';

        $mail->AddAddress(TOURNAMENTS_EMAIL, TOURNAMENTS_EMAIL);
        // This is Temporary:
        // For right now, also send stuff to Skinny as we debug RT
        //if ($key == "Results" || $key == "Tournaments" || $key == "Signup") {
        //	$mail->AddAddress('skinny@bpong.com','skinny@bpong.com');
        //}
        $mail->Subject         = $subject;
        $mail->Body            = $body;
        if ($mail->Send()) {
            $this->Session->setFlash('Email has been sent', 'flash_success');         
        } else {
            $this->Session->setFlash('Error while sending', 'flash_error');             
        }
        
        return $this->redirect('/');
        exit();
    }
        /**
        New Apply Bar
     */
    function send_apply_bar() 
    {
        if (empty($this->request->data['Contact']['email'])) {
            $this->Session->setFlash('Email address error!', 'flash_error');        
            $this->redirect('/');            
        }

        App::import('Vendor', 'PHPMailer', array('file' => 'mailer.class.php'));
        $mail = new PHPMailer();

        $mail->ContentType = "text/html";
        $mail->FromName   = $this->request->data['Contact']['your_name'];
        $subject = 'Apply to be an NBPL Bar Form';
        if (!LIVE_WEBSITE) {
            $subject = $subject . ' TESTING!!!';    
        }
        $body = '';
        $body.= '<b>Name:</b> ' . $this->request->data['Contact']['your_name'] . '<br/>'; $body.= '<b>Email:</b> ' . $this->request->data['Contact']['email'] . '<br/>';
        $body.= '<b>Bar Name:</b> ' . $this->request->data['Contact']['bar_name'] . '<br/>';
        $body.= '<b>Bar Address:</b> ' . $this->request->data['Contact']['bar_adddress'] . '<br/>';
        $body.= '<b>Bar City/State:</b>' . $this->request->data['Contact']['bar_city_state'] . '<br/>';
        $body.= '<b>Bar Manager:</b> ' . $this->request->data['Contact']['bar_manager_name'] . '<br/>';
        $body.= '<b>Telephone:</b> ' . $this->request->data['Contact']['telephone'] . '<br/>';        
        $body.= '<b>Why the bar is perfect for Beer Pong:</b> ' . $this->request->data['Contact']['question'] . '<br/>';

        $mail->AddAddress(TOURNAMENTS_EMAIL, TOURNAMENTS_EMAIL);
        // This is Temporary:
        // For right now, also send stuff to Skinny as we debug RT
        //if ($key == "Results" || $key == "Tournaments" || $key == "Signup") {
        //    $mail->AddAddress('skinny@bpong.com','skinny@bpong.com');
        //}
        $mail->Subject         = $subject;
        $mail->Body            = $body;
        if ($mail->Send()) {
            $this->Session->setFlash('Your application has been received.', 'flash_success');         
        } else {
            $this->Session->setFlash('Error while sending', 'flash_error');             
        }
        
        $this->redirect('/');
        exit();
    }
    
    /**
        Send bar recommend message
     */
    function send_bar_recommend() 
    {
        if (empty($this->request->data['Contact']['email'])) {
            $this->Session->setFlash('Email address error!', 'flash_error');        
            return $this->redirect('/');            
        }

        App::import('Vendor', 'PHPMailer', array('file' => 'mailer.class.php'));
        $mail = new PHPMailer();

        $mail->ContentType = "text/html";
        $mail->FromName   = $this->request->data['Contact']['your_name'];
        $subject = 'Recommend a Bar Form';
        if (!LIVE_WEBSITE) {
            $subject = $subject . ' TESTING!!!';    
        }
        $body = '';
        $body.= '<b>Name:</b> ' . $this->request->data['Contact']['your_name'] . '<br/>'; $body.= '<b>Email:</b> ' . $this->request->data['Contact']['email'] . '<br/>';
        $body.= '<b>Bar Name:</b> ' . $this->request->data['Contact']['bar_name'] . '<br/>';
        $body.= '<b>Bar Address:</b> ' . $this->request->data['Contact']['bar_adddress'] . '<br/>';
        $body.= '<b>Bar City/State:</b> '.$this->request->data['Contact']['bar_city_state'] . '<br/>';
        $body.= '<b>Bar Manager:</b> ' . $this->request->data['Contact']['bar_manager_name'] . '<br/>';
        $body.= '<b>Telephone:</b> ' . $this->request->data['Contact']['telephone'] . '<br/>';        
        $body.= '<b>Why the bar is perfect for Beer Pong:</b> ' . $this->request->data['Contact']['question'] . '<br/>';

        $mail->AddAddress(TOURNAMENTS_EMAIL, TOURNAMENTS_EMAIL);
        // This is Temporary:
        // For right now, also send stuff to Skinny as we debug RT
        //if ($key == "Results" || $key == "Tournaments" || $key == "Signup") {
        //	$mail->AddAddress('skinny@bpong.com','skinny@bpong.com');
        //}
        $mail->Subject         = $subject;
        $mail->Body            = $body;
        if ($mail->Send()) {
            $this->Session->setFlash('Email has been sent', 'flash_success');         
        } else {
            $this->Session->setFlash('Error while sending', 'flash_error');             
        }
        
        return $this->redirect('/');
        exit();
    }
    
    function bpong_redirect() 
    {
        header('Location: http://www.bpong.com' . $this->request->here);
        exit();
    }
}


?>
