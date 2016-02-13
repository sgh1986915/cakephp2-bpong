<?php
/* SVN FILE: $Id: error404.ctp 1647 2008-10-25 11:00:11Z vovich $ */
/**
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.cake.libs.view.templates.errors
 * @since			CakePHP(tm) v 0.10.0.1076
 * @version			$Revision: 1647 $
 * @modifiedby		$LastChangedBy: vovich $
 * @lastmodified	$Date: 2008-10-25 15:00:11 +0400 (Сб, 25 окт 2008) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
uses('cake_log');
CakeLog::write('404page', 'here: '.$this->request->here);
if (!empty($_SERVER['HTTP_REFERER'])) {
	CakeLog::write('404page', 'referer: '.$_SERVER['HTTP_REFERER']);
}
if (!empty($_GET)) {
	CakeLog::write('404page', 'GET: '.var_export($_GET, true));
}
if (!empty($_POST)) {
	CakeLog::write('404page', 'POST: '.var_export($_POST, true));
}
header("HTTP/1.1 404 Not Found");
?>
<div class="error404" style="color:black;">
    	<h3>The page you requested does not exist</h3>
		<p>The cause of this problem may be one or more of the following:</p>
		<div align="center" style="align:center;margin-left:320px;">
		<ul style="text-align:left;">
			<li style="list-style-type:square;">The document you requested was moved to a new URL.</li>
			<li style="list-style-type:square;">The document you requested was deleted.</li>
			<li style="list-style-type:square;">The URL you used was incorrectly coded or typed.</li>
		</ul>
		</div>
</div>