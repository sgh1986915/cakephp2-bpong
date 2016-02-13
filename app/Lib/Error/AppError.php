<?php
/**
 *Class for sending emails to the developer with notification error!
 *@author vovich
 */
class AppError extends ErrorHandler {

function __construct($method, $messages) {
   //Configure::write('debug', 1);

//      $message = 'Site '.env('SERVER_NAME').' has generated an error message'."<BR>";
//              $message .= "Error Type: $method<BR>";
//               foreach ($messages[0] as $key => $value) {
//                   $message .= str_pad($key, 10).": $value<BR>";
//               }
//               $message .= 'URL  : '.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."<BR>";
//               $message .= 'User Agent  : '.$_SERVER['HTTP_USER_AGENT']."<BR>";
//               $message .= 'Referrer  : '.env('HTTP_REFERER')."<BR>";
//               $message .= 'Remote add: '.env('REMOTE_ADDR')."<BR>";
//
//       App::import('Vendor', 'PHPMailer', array('file' => 'mailer.class.php'));
//  		 $mailer = new PHPMailer();
//  		 $mailer->From = 'error@localhost';
//  		 $mailer->AddAddress(DEVELOPER_EMAIL, DEVELOPER_EMAIL);
//  		 $mailer->Subject = "Error in Bpong application!";
//  		 $mailer->Body    = $message;
//           $mailer->Send();

         parent::__construct($method, $messages);

}


}
?>