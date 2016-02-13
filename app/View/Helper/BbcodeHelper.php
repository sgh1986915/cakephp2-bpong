<?php
App::import('Vendor', 'BbcodeLib', array('file' => 'bbcode' . DS . 'bbcode.lib.php'));
class BbcodeHelper extends AppHelper {
	
	function convert_bbcode($text) {
		$bb = new bbcode ( $text );
		// convert BBCode to HTML 
		return $bb->get_html ();
	}
}
