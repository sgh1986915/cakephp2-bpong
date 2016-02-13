<?php
class MailerComponent extends Component
{

    var $mailer;
    var $controller;

    /**
 * It is false by default.
 * Change it via EMAIL_HTML_FORMAT boolean constant in bootstrap.
 */
    var $html = null;
    /**
* Uses App.encoding param from core.php
*/
    var $charset =  null;
    /**
* Uses ADMIN_EMAIL from bootstrap.
*/
    var $adminEmail = null;
    /**
* Uses DOMAIN_NAME from bootstrap.
*/
    var $domainName = null;

    function startup(&$controller) 
    { 
        $this->controller = $controller; 

        App::import(
            'Vendor', 'PHPMailer', array(
            'file'   => 'class.phpmailer.php'
            )
        );

        if (!isset($this->controller->Mailtemplate)) {
            $this->controller->loadModel('Mailtemplate');
        }

        $this->html = defined('EMAIL_HTML_FORMAT') ? 
                      EMAIL_HTML_FORMAT : false;

        $charset = Configure::read('App.encoding');
        $this->charset = $charset ? strtolower($charset) : 'utf-8';
        unset($charset);

        $this->domainName = defined('DOMAIN_NAME') ? 
                            DOMAIN_NAME : $_SERVER['HTTP_HOST'];

        $this->adminEmail = $this->controller->Mailtemplate->adminEmail =
        defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'admin@' . $this->domainName;
    }//eof startup

    /**
 * Prepare data for sending
 * @param $name template name
 * @param $params
 * @return bool
 */
    function prepare($name = '', $params = array()) 
    {
        if (empty($name) || empty($params['to'])) {
            return false;
        }
        $template = $this->controller->Mailtemplate->getTemplate(
            $name, 
            isset($params['data']) ? $params['data'] : array()
        );
        $this->mailer = new PHPMailer();

        if (!empty($params['from'])) {
            $this->mailer->From = $params['from'];
        } elseif (!empty($template['from'])) {
            $this->mailer->From = $template['from'];
        } else {
            $this->mailer->From = $this->adminEmail;
        }

        $this->mailer->CharSet  = $this->charset;
        $this->mailer->FromName = $this->domainName;
        
        if (is_array($params['to'])) {
            foreach ($params['to'] as $addr) {
                $this->mailer->AddAddress($addr);
            }
        } else {
            $this->mailer->AddAddress($params['to']);
        }

        if (!empty($template['bcc'])) {
            $this->mailer->AddBCC($template['bcc'], $template['bcc']); 
        }
        
        $this->mailer->Subject = $template['subject'];
        $this->mailer->Body    = $template['body'];
        $this->mailer->isHTML($this->html);
        $this->mailer->IsSendmail();

        
        
        if (defined('LOG_EMAILS') && LOG_EMAILS) {
            $this->log($template, LOG_DEBUG);
        }

        return true;
    }//eof prepare
    /**
 * returns Body
 * @return unknown_type
 */
    function getBody()
    {
        return  $this->mailer->Body;
    }
    /**
 * returns Subject
 * @return unknown_type
 */
    function getSubject()
    {
        return  $this->mailer->Subject;
    }    
    /**
 * Sending
 * @return bool
 */
    function send() 
    {
        
        /*   $fp = fopen('email'.time().'.html',"w+");
        fwrite($fp,"Emailto: Subject:".$this->getSubject()."<BR> Body:".$this->getBody());
        fclose($fp);*/
        //echo "Emailto:".$emailto."<BR>Subject:".$template['subject']."<BR> Body:".$template['body'];
            
        $result = $this->mailer->Send();
        if (!$result) {
             $this->log('Can not send email with subj: "'.$this->getSubject().'" and body'.$this->getBody());   
        }
        return $result;
    }//eof send

}//class
?>
