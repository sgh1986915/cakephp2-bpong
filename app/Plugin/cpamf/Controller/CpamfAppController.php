<?php
/**
 * Default controller for the cpamf plugin.
 * 
 * @author Daniel Verner
 * @license 
 * @copyright (c) 2009 carrotplant.com
 * @package flashservices
 * @subpackage 
 * @version $Id$
 *
 */

class CpamfAppController extends AppController {
    
    function beforeFilter()
    {
		Configure::write( 'debug', 0 );
		Configure::write( 'Cpamf.serviceBrowserEnabled', 0 );
    }

}
?>
