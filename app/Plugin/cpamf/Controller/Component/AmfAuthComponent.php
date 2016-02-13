<?php
/**
 * Cpamf.AmfAuthComponent
 *
 * Handles redirect attempts for amf requests.
 * If authentication fails, the authcomponent makes a redirect, but with amf request,
 * redirects are not allowed (it causes channel disconnected error on the client side),
 * so we "convert" the redirect attempts to exceptions, which are handled by Cpamf plugin.
 * This way the flex client gets the correct fault message.
 *
 * @author Daniel Verner, Arnold Remete
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright (c) 2009 CarrotPlant Ltd.
 * @package cpamf
 * @subpackage controllers.components
 * @version $Id: amf_auth.php 96 2009-07-07 11:16:01Z daniel.verner $
 */

class AmfAuthComponent extends Component {

	function beforeRedirect(&$controller, $url, $status = null, $exit = true)
	{
		$message = "Error";

		if( isset( $controller->Auth ) && $controller->Auth->Session->check( "Message.auth" ) )
		{
			$message = $controller->Auth->Session->read( "Message.auth.message" );
		}
		else
		{
			$message = $controller->name . "Controller attempts to redirect to " . $url .
						( ($status != null) ? ", with status " . $status : "" );
		}
		throw new Exception( $message, $status );
		return false;
	}
}

?>