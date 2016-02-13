<?php 
class YoutubeHelper extends AppHelper {
    
    /**
     * Return video code by video ID
     * @author Oleg D.
     */
	function getVideoCode($id, $width = 425, $height = 344) {
		return '<object width="'.$width.'" wmode="opaque" height="'.$height.'"><param name="wmode" value="opaque"><param name="movie" value="http://www.youtube.com/v/'.$id.'?rel=0&fs=1&loop=0"></param></param><param name="allowFullScreen" value="true"><embed src="http://www.youtube.com/v/'.$id.'?rel=0&fs=1&loop=0" allowfullscreen="true" type="application/x-shockwave-flash" wmode="opaque" width="'.$width.'" height="'.$height.'"></embed></object>';
	}
	
    /**
     * Return video image by video ID
     * @author Oleg D.
     */
	function getVideoImage($id, $size = 'big') {
		if ($id) {
    		return 'http://img.youtube.com/vi/' . $id . '/1.jpg';
		} else {
			if ($size == 'big') {
				return "/img/video-120-90.png";
			} else {
				return "/img/video-75-57.png";				
			}
		}
	}
    /**
     * Fix youtube video code for dialog window 
     * @author Oleg D.
     */
	function fixVideoCode ($code) {
		$code = str_replace("</object>", '<param name="wmode" value="opaque"></object>', $code);
		$code = str_replace('<object', '<object wmode="opaque"', $code);
		$code = str_replace('<embed', '<embed wmode="opaque"', $code);
		$code = str_replace('transparent', 'opaque', $code);
		return $code;	
	}
}
?>