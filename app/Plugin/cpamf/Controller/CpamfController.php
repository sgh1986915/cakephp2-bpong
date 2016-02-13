<?php
/**
 * Default controller for the amf plugin. Imports the amfphp vendor.
 *
 * @author Daniel Verner
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright (c) 2009 CarrotPlant Ltd.
 * @package cpamf
 * @subpackage controllers
 * @version $Id: cpamf_controller.php 97 2009-07-08 09:45:54Z daniel.verner $
 *
 */

define( "SERVICE_BROWSER_PATH", ROOT . DS . APP_DIR . DS . "plugins" . DS .
								"cpamf" . DS . "vendors" . DS . "amfphp" . DS .
								"browser" . DS  );

class CpamfController extends CpamfAppController {

	var $name = 'Cpamf';
	var $autoRender = false;
	var $layout = "blank";
	var $helpers = array( "Html" );

	function beforeFilter()
	{
        // If the auth component is loaded, we'll make sure to add cpamf to the exclude list
        if( isset( $this->Auth ) )
        {
                $this->Auth->allowedActions = array( 'index', 'gateway', 'browser' );
        }

		parent::beforeFilter();
	}

	function index() {

	}

	/**
	 * Gateway action serves the request from flash/flex
	 */
	function gateway()
	{
		App::import('Vendor', 'Cpamf.amfphp' . DS . 'cake_gateway.php');
	}

	/**
	 * Strips slashes from fileName so we can read file from the specified folder
	 * and check file exists.
	 *
	 * @param string $fileName
	 * @return mixed File name without slashes, or false if file not exists
	 */
	private function _checkFile( $fileName = false )
	{
		if( Configure::read( 'Cpamf.serviceBrowserEnabled' ) != 0 )
		{
			$fileName = str_replace( "/", "", $fileName );
			$fileName = str_replace( "\\", "", $fileName );

			if( file_exists( SERVICE_BROWSER_PATH . $fileName ) )
			{
				return $fileName;
			}
			else
			{
				return false;
			}
		}
	}

	/**
	 * Browser action loads the service browser (in this case controller browser)
	 * This works only if the Cpamf.serviceBrowserEnabled is set to 1
	 * else throws 404 error.
	 */
	function browser( $fileName = false )
	{
		if( Configure::read( 'Cpamf.serviceBrowserEnabled' ) == 0 )
		{
			$this->cakeError('error404');
		}

		if( $fileName == "index" || $fileName == "" )
		{
			$this->autoRender = true;

		}
		else
		{
			$this->autoRender = true;
			$this->view = 'Media';

			$fileName = $this->_checkFile( $fileName );

			if( $fileName === false )
			{
				$this->cakeError('error404');
			}

			$params = array(
				'id' => $fileName,
				'name' => pathinfo  ( $fileName, PATHINFO_FILENAME ),
				'download' => false,
				'extension' => pathinfo( $fileName, PATHINFO_EXTENSION ),
				'path' => SERVICE_BROWSER_PATH
		 	);

		 	$this->set($params);
		}
	}

}
?>